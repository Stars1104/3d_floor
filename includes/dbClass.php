<?php
/*
 * created by pritam
 * 30 june 2012
 */

class dbClass {

	private $dbh;
	
	private $dbhost = DB_HOST;
	private $dbuser = DB_USER;
	private $dbpassword = DB_PASSWORD;
	private $dbname = DB_NAME;
	private $charset = DB_CHARSET;
	private $debug = true;
	private $table_name;
	
	
	////////*************////////
////////	Constructor		 ////////
	////////*************////////
	
	function __construct() {
		$this->db_connect();
	}
	
	
	////////*************////////
////////	Connect		 ////////////////
	////////*************////////
	protected function db_connect() {
		try {
			$this->dbh = new mysqli($this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname);
			if ($this->dbh->connect_error) {
				throw new Exception($this->dbh->connect_error);
			} else {
				$this->setCharset();
			}
		} catch (Exception $e) {
			$this->throwException('<h3 class="dbError">Database connection failed: ' . $e->getMessage() . '</h3>');
		}
	}
	
	////////*************////////
////////	Set CharSet		 ////////
	////////*************////////
	
	
	protected function setCharset($charset = null) {
		if(is_null($charset))
		  $charset = $this->charset;
		$this -> dbh -> query("set names '".$charset."'");
		$this -> dbh -> query("set character set ".$charset);	
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
		if(!@mysql_select_db($db_name,$db_h)) {
			$this->throwException('<h3 class="dbError">'.mysql_error($this->dbh).'</h3>');
		}
	}
	
	////////*************////////
////////	Common Exception  ////////
	////////*************////////
	
	protected function throwException($message,$null_records=false,$errormsg='') {
		if($null_records)
		   print($message);
		else
		   die($message.$errormsg);
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
			$insert_st = @mysql_query($ins_sql,$this->dbh);
			
			if(!$insert_st) {
			  $this->throwException('<h3 class="dbError">'.mysql_error($this->dbh).'</h3>');
			}
			else
			  $insert_id = mysql_insert_id();
			  
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
			
			$update_st = @mysql_query($upd_sql,$this->dbh);
			
			if(!$update_st) {
			  $this->throwException('<h3 class="dbError">'.mysql_error($this->dbh).'</h3>');
			}

			return true;
		}
	}
	
	

	////////*************////////////////////////////////////////////////////////////////////////
////////	Data Selection	 																	 ////////////////
//////  	Access :: Public																	 ////////////////
////// 		Attributes :: Table name, Field Array $fieldArr[column_name1] , Where Condition  	 ////////////////
	////////*************////////////////////////////////////////////////////////////////////////
	
	
	public function db_select($table_name,$fieldArr=array("*"),$whereClause = '',$orderClause = '',$limitClause = '')
	{

		if($this->dbh) {
			if($table_name)
			   $this->table_name = $table_name;
			   
			$fields = implode(',',$fieldArr);
			$select_sql = "select ".$fields." from ".$this->table_name." ".$whereClause." ".$orderClause." ".$limitClause;
			
			$select_res = $this->dbh->query($select_sql);
			$select_rec = array();
			if($select_res) {
				if($select_res->num_rows > 0) {
					while($each_rec = $select_res->fetch_object()) {
						$select_rec[] = $each_rec;
					}
					$select_res->free();
				}
				else
				  return 0;
			}
			else {
			   $this->throwException('<h3 class="dbError">'.$this->dbh->error.'</h3>');
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
			$del_result = @mysql_query($del_sql,$this->dbh);
			if($del_result) {
				return true;
			}
			else {
			   $this->throwException('<h3 class="dbError">'.mysql_error($this->dbh).'</h3>');
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
		
		if($this->dbh) {	
			if(mysql_num_rows($result)>0) {
				$records = array();
				while($eachRec = mysql_fetch_object($result)) {
					$records[] = $eachRec;
				}
				return $records;
			}
			else {
			   $this->throwException('<h3 class="dbError">'.mysql_error($this->dbh).'</h3>',null);
			   return false;
			}
		}
	}
	
	

	////////*************/////////////////
////////	DIRECT SELECT SQL			////////////////
//////  	Access :: Public			////////////////
////// 		Attributes :: RESULT		////////////////
	////////*************////////////////
	
	
	public function db_select_direct($sql) {
		
		if($this->dbh) {	
			$result = @mysql_query($sql,$this->dbh);
			if($result) {
				return $this->get_records($result);
			}
			else {
			   $this->throwException('<h3 class="dbError">'.mysql_error($this->dbh).'</h3>');
			   return false;
			}
		}
	}
	
	
	
	////////*************//////////////////////
////////	DIRECT DELETE SQL			////////////////
//////  	Access :: Public			////////////////
////// 		Attributes :: SQL  			////////////////
	////////*************/////////////////////
	
	
	public function db_direct_delete($sql) {
		
		if($this->dbh) {			
			$delete_st = @mysql_query($sql,$this->dbh);
			if($delete_st) {
				return true;
			}
			else {
			   $this->throwException('<h3 class="dbError">'.mysql_error($this->dbh).'</h3>');
			   return false;
			}
		}
	}

	
	
	////////*************//////////////////////
////////	DIRECT DELETE SQL			////////////////
//////  	Access :: Public			////////////////
////// 		Attributes :: SQL  			////////////////
	////////*************/////////////////////
	
	
	public function db_direct_update($sql) {
		
		if($this->dbh) {			
			$update_st = @mysql_query($sql,$this->dbh);
			if($update_st) {
				return $update_st;
			}
			else {
			   $this->throwException('<h3 class="dbError">'.mysql_error($this->dbh).'</h3>');
			   return false;
			}
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
			/*$type_q = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND COLUMN_NAME = '$eachFieldName'";
			$type_res = @mysql_query($type_q);
			if(isset($type_res) && mysql_num_rows($type_res)) {
				while($each = mysql_fetch_array($type_res)) {
					$table_column_types[$each['COLUMN_NAME']] = $each['DATA_TYPE'];
				}
				mysql_free_result($type_res);
			}*/
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
			$del_result = @mysql_query($del_sql,$this->dbh);
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
		$this->dbh->close();
	}
	
}
?>