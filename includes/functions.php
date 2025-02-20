<?php
/*
 * created by pritam
 * January 14, 2014
 */

	function sanitizepostdata($data) {
		return trim(addslashes($data));
	}
	
	function sanitizedbdata($data) {
		return trim(stripslashes($data));
	}

	function connectToDatabase($connStr) {
		global $lang;
		$connElems = array();
		$connStrPart = explode(';',$connStr);
		foreach($connStrPart as $eachPart) {
			if(trim($eachPart)!='') {
				$eachPartArr = explode('=',trim($eachPart));
				// $connElems[] = $eachPartArr[1];
			}
		}
		/*if(!filter_var($connElems[0], FILTER_VALIDATE_IP)) {
			addScriptForExec('$.fn.alertUser("Invalid Server ('.sanitizepostdata($connElems[0]).')..");');
			return;
		}*/

		$testConn = @mssql_connect($connElems[0].':1433',$connElems[2],$connElems[3]);
		//$testConn
		if(true) {
			//!@mssql_select_db($connElems[1],$testConn)
			if(false) {
				$res = mssql_query("SELECT name FROM master..sysdatabases");
				if($res) {
					$allRec = '';
					while($rec=mssql_fetch_object($res)) {
						$allRec .= $rec->name.',';
					}
				}
				addScriptForExec('$.fn.alertUser("Cannot Selected requested database. These are the databases on server that are accessible over this port :: '.$allRec.'");');

			} else {
				$_SESSION['CONN']['HOST'] = $connElems[0];
				$_SESSION['CONN']['USER'] = $connElems[2];
				$_SESSION['CONN']['PASSWORD'] = $connElems[3];
				$_SESSION['CONN']['DB_NAME'] = $connElems[1];
				@header('location:'.HTTP_PATH.'home');
				addScriptForExec('window.location="'.HTTP_PATH.'home";');
			}
		} else {
			addScriptForExec('$.fn.alertUser("Cannot Connect to host.");');
		}
	}
	
	function IsLoggedIn() {
		global $CURRENT_URL;
		if(!empty($_SESSION['CONN']) && !empty($_SESSION['User'])) {
			return true;
		} else {
			return false;
		}
	}
	//Used in templates footer part
	function executeScriptAfterPageLoad() {
		global $SCRIPT_PENDING_FOR_EXECUTION;
		$allCode = '';
		if($SCRIPT_PENDING_FOR_EXECUTION && count($SCRIPT_PENDING_FOR_EXECUTION)) {
			foreach($SCRIPT_PENDING_FOR_EXECUTION as $eachCode) {
				$allCode .= $eachCode."\n";
			}
		}
		if($allCode!='') {
			echo "<script>$(function(){\n".$allCode."});</script>\r\n";
		}
	}
		
	function addScriptForExec($code) {
		global $SCRIPT_PENDING_FOR_EXECUTION;
		$SCRIPT_PENDING_FOR_EXECUTION[] = $code;
	}
	
	function getHebDate($db,$params) {
		$return = array();
		$str = 'hebdate';
		$HebDate = $db->db_select_direct("SELECT hebdate FROM t_hebdate WHERE grig_date='".$params['date']."'");
		if($HebDate && count($HebDate)>0) {
			$return['error'] = 0;
			$return['message'] = $HebDate[0]->hebdate;
		} else {
			$return['error'] = 0;
			$return['message'] = $str;
		}
		return $return;
	}
	
	function getAllHebDatesAtATime($db,$params) {
		$return = array();
		$AllDateRecs = array();
		$year = $params['year'];
		$month = $params['month'];
		$month = str_pad($month,2,"0",STR_PAD_LEFT);
		$first_day = 1;
		$last_day = date('t',strtotime($date));
		$AllDates = '';
		for($i=$first_day;$i<=$last_day;$i++) {
			$day = str_pad($i,2,"0",STR_PAD_LEFT);
			$cDate = $year.'-'.$month.'-'.$day;
			$AllDates .= "'".$cDate."',";
		}
		$AllDates = rtrim($AllDates,',');
		$str = '';
		//$HebDates = $db->db_select_direct("SELECT grig_date,hebdate FROM t_hebdate WHERE grig_date IN (".$AllDates.")");
		$HebDates = $db->db_select_direct("SELECT grig_date,hebdate FROM t_hebdate WHERE MONTH(grig_date)='".$month."' AND YEAR(grig_date)='".$year."'");
		if($HebDates && count($HebDates)>0) {
			$return['error'] = 0;
			$arr = array();			
			foreach($HebDates as $eachDate) {
				$output = preg_replace('!\s+!',' ',$eachDate->hebdate);
				$temp = explode(' ',$output);
				$temp1 = $temp[0].' '.$temp[1];
				$arr[$eachDate->grig_date] = $temp1;
			}
			$return['message'] = $arr;
		} else {
			$return['error'] = 1;
			$return['message'] = $str;
		}
		/*echo '<!DOCTYPE><html lang="he"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';
		print_r($arr);
		echo '</body></html>';
		die('');*/
		return $return;
	}
?>