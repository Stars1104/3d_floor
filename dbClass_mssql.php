<?php
/*
 * created by pritam
 * 30 june 2012
 */

class dbClass_mssql {

	private $dbh;

	private $dbhost = DB_HOST_MSSQL;
	private $dbuser = DB_USER_MSSQL;
	private $dbpassword = DB_PASSWORD_MSSQL;
	private $dbname = DB_NAME_MSSQL;
	private $charset = DB_CHARSET_MSSQL;
	private $debug = true;
	private $table_name;

	////////*************////////
////////	Constructor		 ////////
	////////*************////////

	function __construct($settings=null) {
		if(!empty($settings)){
			$settings = @json_decode(@json_encode($settings, true));
			$this->dbhost = $settings->DB_HOST_MSSQL;
			$this->dbuser = $settings->DB_USER_MSSQL;
			$this->dbpassword = $settings->DB_PASSWORD_MSSQL;
			$this->dbname = $settings->DB_NAME_MSSQL;
			$this->charset = $settings->DB_CHARSET_MSSQL;
		}

		$this->db_connect();
	}


	////////*************////////
////////	Connect		 ////////////////
	////////*************////////

	protected function db_connect() {

		if(function_exists('sqlsrv_connect')) {
			$con = sqlsrv_connect('172.31.40.227', [
				"Database" => $this->dbname, "Uid" => $this->dbuser, "PWD" => $this->dbpassword, 'TrustServerCertificate' => true, "CharacterSet" => "UTF-8"
			]);

			if(!$con) {
				print_r( sqlsrv_errors(), true);
			}

			return $con;
		}
	}

	////////*************////////
////////	Set CharSet		 ////////
	////////*************////////


	protected function setCharset($charset = null) {
		if(is_null($charset)) {
		  $charset = $this->charset;
		}
	}


	////////*************////////
////////	Select Database	  ////////
	////////*************////////


	protected function selectDb($db_name = null,$db_h = null) {
		if(is_null($db_h)) {
			$db_h = $this->dbh;
		}
		if(is_null($db_name)) {
			$db_name = $this->dbname;
		}
		// if(!@mssql_select_db($db_name,$db_h)) {
		// 	$this->throwException('<h4 align="center" class="dbError">Cannot Select Dababase</h4>');
		// }
	}

	////////*************////////
////////	Common Exception  ////////
	////////*************////////

	protected function throwException($message,$null_records=false,$errormsg='') {
		if($null_records)
		   print($message);
		else
		   print($message.$errormsg);
		exit;
		die;
	}


	////////*************///////////////////////////////////////////////////////////////////////////////
////////	Data Insert	 																		 ////////////////
//////  	Access :: Public																	 ////////////////
////// 		Attributes :: Table name, Data Array in this format $dataArr[column_name1] = value1  ////////////////
	////////*************///////////////////////////////////////////////////////////////////////////////


	public function db_insert($table_name,$dataArr) {

		if($this->dbh) {

			$fieldsArr = $valuesArr = array();
			$this->table_name = $table_name;
			foreach($dataArr as $eachField=>$eachValue) {
				$fieldsArr[]=$eachField;
				$valuesArr[]=$this->sanitizepostdata($eachValue);
			}
			$fields = implode(',',$fieldsArr);
			$values = implode("','",$valuesArr);

			$field_types = $this->table_field_type($this->table_name,$fieldsArr);
			$value_types = $this->setValueFormats($field_types);
			$value_formats = implode(",",$value_types);

			$ins_sql = vsprintf("insert into ".$this->table_name." (".$fields.") values (".$value_formats.") ",$valuesArr);
			$insert_st = @mssql_query($ins_sql,$this->dbh);

			if(!$insert_st) {
			  $this->throwException('<h4 align="center" class="dbError">Error in Insert Query. Please Check all the array keys that is passed on db_insert() function</h4>');
			}
			else
			  $insert_id = mssql_insert_id();

			return @$insert_id;
		}
	}


	////////*************/////////////////////////////////////////////////////////////////////////////////////////////
////////	Data Update	 																						  ////////////////
//////  	Access :: Public																	 				  ////////////////
////// 		Attributes :: Table name, Data Array in this format $dataArr[column_name1] = value1, Where Condition  ////////////////
	////////*************/////////////////////////////////////////////////////////////////////////////////////////////


	public function db_update($table_name,$dataArr,$whereClause=null) {

		if($this->dbh && !is_null($whereClause)) {

			$fieldsArr = $valuesArr = array();
			$this->table_name = $table_name;
			foreach($dataArr as $eachField=>$eachValue) {
				$fieldsArr[]=$eachField;
				$valuesArr[]=$this->sanitizepostdata($eachValue);
			}

			$field_types = $this->table_field_type($this->table_name,$fieldsArr);
			$value_types = $this->setValueFormats($field_types);
			$updateFrag = '';
			foreach($value_types as $each_field=>$each_value_type) {
				$updateFrag .= $each_field.'='.$each_value_type.',';
			}
			$updateFrag = rtrim($updateFrag, ",");
			$upd_sql = vsprintf("update ".$this->table_name." set ".$updateFrag." ".$whereClause,$valuesArr);

			$update_st = @mssql_query($upd_sql,$this->dbh);

			if(!$update_st) {
			  $this->throwException('<h4 align="center" class="dbError">Error in Update Query. Please Check all the array keys that is passed on db_update() function</h4>');
			}

			return true;
		}
	}



	////////*************////////////////////////////////////////////////////////////////////////
////////	Data Selection	 																	 ////////////////
//////  	Access :: Public																	 ////////////////
////// 		Attributes :: Table name, Field Array $fieldArr[column_name1] , Where Condition  	 ////////////////
	////////*************////////////////////////////////////////////////////////////////////////


	public function db_select($table_name,$fieldArr=array("*"),$whereClause = '',$orderClause = '')
	{

		if($this->dbh) {

			if($table_name)
			   $this->table_name = $table_name;

			$fields = implode(",",$fieldArr);
			$select_sql = "select ".$fields." from ".$this->table_name." ".$whereClause." ".$orderClause." ";

			$select_res = @mssql_query($select_sql,$this->dbh);
			$select_rec = array();
			if($select_res) {
				if(mssql_num_rows($select_res)>0) {
					while($each_rec = mssql_fetch_object($select_res)) {
						$select_rec[] = $each_rec;
					}
					mssql_free_result($select_res);
				}
				else
				  return 0;
			}
			else {
			   $this->throwException('<h4 align="center" class="dbError">'.mssql_get_last_message().'</h4>');
			}
			return $select_rec;
		}
	}


	////////*************//////////////////////////////////////////
////////	DELETE SQL										////////////////
//////  	Access :: Public								////////////////
////// 		Attributes :: TABLE, WHERE CLAUSE 				////////////////
	////////*************//////////////////////////////////////////


	public function db_delete($table_name,$whereClause = '') {

		if($this->dbh) {
			$del_sql = "delete from ".$table_name." ".$whereClause;
			$del_result = @mssql_query($del_sql,$this->dbh);
			if($del_result) {
				return true;
			}
			else {
			   $this->throwException('<h4 align="center" class="dbError">Error in Delete Query Condition</h4>');
			   return false;
			}
		}
	}


	////////*************/////////////////
////////	GET RESULT IN OBJECT		////////////////
//////  	Access :: Public			////////////////
////// 		Attributes :: RESULT		////////////////
	////////*************////////////////

	public function get_records($result) {
        if(sqlsrv_has_rows($result)) {
            $records = array();
            while($eachRec = sqlsrv_fetch_object($result)) {
                $records[] = $eachRec;
            }
            return $records;
        }
        else {
            return array();
        }
	}

	////////*************/////////////////
////////	DIRECT SELECT SQL			////////////////
//////  	Access :: Public			////////////////
////// 		Attributes :: RESULT		////////////////
	////////*************////////////////

	public function db_select_direct($sql) {

		$con = $this->db_connect();

	    if ($con) {
	        $result = sqlsrv_query($con, $sql);


	        if ($result === false) {
			    die(print_r(sqlsrv_errors(), true)); 
			}

			return $this->get_records($result);

	        if ($result !== false) {
	            return $this->get_records($result);
	        } else {
	            $errors = sqlsrv_errors();
	            if ($errors) {
	                $message = '<h4 class="dbError">Error in Query (' . $errors[0]['message'] . ')</h4>';
	            } else {
	                $message = '<h4 class="dbError">Error in Query</h4>';
	            }
	            $this->throwException($message);
	            return false;
	        }
	    }
	}

	public function db_select_direct_params($sql, $params) {

	    $con = $this->db_connect();

	    if ($con) {
	        $stmt = sqlsrv_prepare($con, $sql, $params);

	        if ($stmt === false) {
	            die(print_r(sqlsrv_errors(), true));
	        }

	        $result = sqlsrv_execute($stmt);
	        if ($result === false) {
	            die(print_r(sqlsrv_errors(), true));
	        }

	        return $this->get_records_params($stmt);
	    }
	}

	public function get_records_params($stmt) {

	    $records = array();
	    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
	        $records[] = $row;
	    }
	    return $records;
	}

	////////*************//////////////////////
////////	DIRECT DELETE SQL			////////////////
//////  	Access :: Public			////////////////
////// 		Attributes :: SQL  			////////////////
	////////*************/////////////////////


	public function db_direct_delete($sql) {

		// if($this->dbh) {
		$con = $this->db_connect();
			// $delete_st = @mssql_query($sql,$this->dbh);
			 $delete_st=sqlsrv_query($con, $sql);

			if($delete_st) {
				return true;
			}
			else {
			   $this->throwException('<h4 align="center" class="dbError">'.'delete query error'.'</h4>');
			   return false;
			}
		// }
	}

	////////*************//////////////////////
////////	DIRECT DELETE SQL			////////////////
//////  	Access :: Public			////////////////
////// 		Attributes :: SQL  			////////////////
	////////*************/////////////////////


	public function db_direct_update($sql) {

		$con = $this->db_connect();

		if ($con) {
		    $stmt = sqlsrv_query($con, $sql);

		    if ($stmt === false) {
			    die(print_r(sqlsrv_errors(), true));
			} else {
			    return $stmt;
			}
		}
	}

	public function db_direct_update_params($sql, $params) {

	    $con = $this->db_connect();
	    if($con) {
	        $stmt = sqlsrv_prepare($con, $sql, $params);
	        if ($stmt === false) {
	            die(print_r(sqlsrv_errors(), true));
	        }
	        $result = sqlsrv_execute($stmt);
	        if ($result === false) {
	            die(print_r(sqlsrv_errors(), true));
	        }
	        return $result;
	    }
	}

	////////*************//////////////////////
////////	DIRECT Insert SQL			////////////////
//////  	Access :: Public			////////////////
////// 		Attributes :: SQL  			////////////////
	////////*************/////////////////////
	
	public function db_direct_insert($sql) {
		$con = $this->db_connect();
		if($con) {

			$stmt = sqlsrv_query($con, $sql);

			if ($stmt === false) {
			    die(print_r(sqlsrv_errors(), true));
			} else {
			    return $stmt;
			}
		}
	}

	public function db_direct_insert_params($sql, $params) {
	    $con = $this->db_connect();
	    if($con) {
	        $stmt = sqlsrv_prepare($con, $sql, $params);
	        if ($stmt === false) {
	            die(print_r(sqlsrv_errors(), true));
	        }
	        $result = sqlsrv_execute($stmt);
	        if ($result === false) {
	            die(print_r(sqlsrv_errors(), true));
	        }
	        return $result;
	    }
	}

	////////*************///////////////////////////////
////////	Determine Table Column Types			  ////////
//////  	Access :: Protected						  ////////
////// 		Attributes :: Table name, Needed Fields   ////////
	////////*************///////////////////////////////


	protected function table_field_type($table_name,$fieldsArr) {
		$table_column_types = array();
		foreach($fieldsArr as $eachFieldName) {
			$table_column_types[$eachFieldName] = "varchar";
		}
  	   return @$table_column_types;
	}


	////////*************//////////////////////////////////////////////
////////	Calculate Table fields Data Types based on field type	 ////////
//////  	Access :: Protected										 ////////
//////  	Attributes :: Field Data types array 					 ////////
	////////*************//////////////////////////////////////////////



	protected function setValueFormats($field_data_type_arr) {
		$value_types = array();
		foreach($field_data_type_arr as $each_field_name=>$each_field_type) {
			if(in_array($each_field_type,array("enum","varchar","text","date","datetime","char"))) {
				$value_types[$each_field_name] = "'%s'";
			}
			elseif($each_field_type=="int") {
				$value_types[$each_field_name] = "'%d'";
			}
			elseif($each_field_type=="float") {
				$value_types[$each_field_name] = "'%f'";
			}
		}
	   return @$value_types;
	}



	////////*************///////////////////////////////////////////////////////////////////////////////
////////	Data Insert	SQL PRINT 																 ////////////////
//////  	Access :: Public																	 ////////////////
////// 		Attributes :: Table name, Data Array in this format $dataArr[column_name1] = value1  ////////////////
	////////*************///////////////////////////////////////////////////////////////////////////////


	public function db_insert_sql($table_name,$dataArr) {

		if($this->dbh) {

			$fieldsArr = $valuesArr = array();
			$this->table_name = $table_name;
			foreach($dataArr as $eachField=>$eachValue) {
				$fieldsArr[]=$eachField;
				$valuesArr[]=$this->sanitizepostdata($eachValue);
			}
			$fields = implode(',',$fieldsArr);
			$values = implode("','",$valuesArr);

			$field_types = $this->table_field_type($this->table_name,$fieldsArr);
			$value_types = $this->setValueFormats($field_types);
			$value_formats = implode(",",$value_types);

			$ins_sql = vsprintf("insert into ".$this->table_name." (".$fields.") values (".$value_formats.") ",$valuesArr);

			return @$ins_sql;
		}
	}




	////////*************/////////////////////////////////////////////////////////////////////////////////////////////
////////	Data Update SQL	 																		 			  ////////////////
//////  	Access :: Public																	 				  ////////////////
////// 		Attributes :: Table name, Data Array in this format $dataArr[column_name1] = value1, Where Condition  ////////////////
	////////*************/////////////////////////////////////////////////////////////////////////////////////////////


	public function db_update_sql($table_name,$dataArr,$whereClause=null) {

		if($this->dbh && !is_null($whereClause)) {

			$fieldsArr = $valuesArr = array();
			$this->table_name = $table_name;
			foreach($dataArr as $eachField=>$eachValue) {
				$fieldsArr[]=$eachField;
				$valuesArr[]=$this->sanitizepostdata($eachValue);
			}

			$field_types = $this->table_field_type($this->table_name,$fieldsArr);
			$value_types = $this->setValueFormats($field_types);
			$updateFrag = '';
			foreach($value_types as $each_field=>$each_value_type) {
				$updateFrag .= $each_field.'='.$each_value_type.',';
			}
			$updateFrag = rtrim($updateFrag, ",");
			$upd_sql = vsprintf("update ".$this->table_name." set ".$updateFrag." ".$whereClause,$valuesArr);

			return $upd_sql;
		}
	}



	////////*************///////////////////////////////////////////////////////////////////////////////
////////	Data Selection	SQL 																 ////////////////
//////  	Access :: Public																	 ////////////////
////// 		Attributes :: Table name, Field Array $fieldArr[column_name1] , Where Condition  	 ////////////////
	////////*************///////////////////////////////////////////////////////////////////////////////


	public function db_select_sql($table_name,$fieldArr=array("*"),$whereClause = '',$orderClause = '',$limitClause = '')
	{

		if($this->dbh) {

			if($table_name)
			   $this->table_name = $table_name;

			$fields = implode(',',$fieldArr);
			$select_sql = "select ".$fields." from ".$this->table_name." ".$whereClause." ".$orderClause." ".$limitClause;

			return $select_sql;
		}
	}


	////////*************//////////////////////////////////////////
////////	DELETE SQL Print								////////////////
//////  	Access :: Public								////////////////
////// 		Attributes :: TABLE, WHERE CLAUSE 				////////////////
	////////*************//////////////////////////////////////////


	public function db_delete_sql($table_name,$whereClause = '') {

		if($this->dbh) {
			$del_sql = "delete from ".$table_name." ".$whereClause;
			$del_result = @mssql_query($del_sql,$this->dbh);
			return $del_result;
		}
	}


	////////*************/////////////////////////////////////////////////////
////////	Data Sanitization [ At the time of insertion and selection ]	 ////////
//////  	Access :: Protected												 ////////
//////  	Attributes :: Data 												 ////////
	////////*************/////////////////////////////////////////////////////

	protected function sanitizepostdata($data) {
		return trim(addslashes($data));
	}

	protected function sanitizedbdata($data) {
		return trim(stripslashes($data));
	}


	////////*************////////
////////	Destructor		 ////////
	////////*************////////


	function __destruct() {
		// @mssql_close($this->dbh);
	}

}
?>
