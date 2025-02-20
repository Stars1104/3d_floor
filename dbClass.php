<?php

function generatePaginationBlock($baseLink,$totalPage,$currentPage=1,$GET_METHOD_PAGE_COUNT = true) {
	$paginationHTML = "<div class=\"paginateList\"><ul>"."\r\r";
	$next_page = $currentPage+1;
	$prev_page = $currentPage-1;
	if($GET_METHOD_PAGE_COUNT) {
		if($currentPage!=1) {
			$paginationHTML .= '<li><a href="'.$baseLink.'?page='.$prev_page.'">&laquo; Previous </a></li>'."\r\r";
		} else {
			$paginationHTML .= '<li class="disabled"><span>&laquo; Previous </span></li>'."\r\r";
		}
	
		for($i=1;$i<=$totalPage;$i++) {
			$classAttr = ($currentPage==$i)?' class="active"':'';
			$paginationHTML .= '<li'.$classAttr.'><a href="'.$baseLink.'?page='.$i.'">'.$i.'</a></li>'."\r\r";
		}
		
		if($currentPage<$totalPage) {
			$paginationHTML .= '<li><a href="'.$baseLink.'?page='.$next_page.'"> Next &raquo;</a></li>'."\r\r";
		} else {
			$paginationHTML .= '<li class="disabled"><span> Next &raquo;</span></li>'."\r\r";
		}
	}
	else {
		if($currentPage!=1) {
			$paginationHTML .= '<li><a href="'.$baseLink.$prev_page.'">&laquo; Previous </a></li>'."\r\r";
		} else {
			$paginationHTML .= '<li class="disabled"><span>&laquo; Previous </span></li>'."\r\r";
		}
	
		for($i=1;$i<=$totalPage;$i++) {
			$classAttr = ($currentPage==$i)?' class="active"':'';
			$paginationHTML .= '<li'.$classAttr.'><a href="'.$baseLink.$i.'">'.$i.'</a></li>'."\r\r";
		}
		
		if($currentPage<$totalPage) {
			$paginationHTML .= '<li><a href="'.$baseLink.$next_page.'"> Next &raquo;</a></li>'."\r\r";
		} else {
			$paginationHTML .= '<li class="disabled"><span> Next &raquo;</span></li>'."\r\r";
		}
	}
	$paginationHTML .= "</ul></div>"."\r\r";
	return $paginationHTML;
}

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
		$this->dbh = @mysqli_connect($this->dbhost,$this->dbuser,$this->dbpassword);
		if(!$this->dbh) {
			$this->throwException("Cannot conect to database");
		}
		else {	
			$this->setCharset();
			$this->selectDb($this->dbname,$this->dbh);
		}
		return;
	}
	
	
	////////*************////////
////////	Set CharSet		 ////////
	////////*************////////
	
	
	protected function setCharset($charset = null) {
		if(is_null($charset))
		  $charset = $this->charset;
		@mysqli_query($this->dbh, "set names '".$charset."'");
		@mysqli_query($this->dbh, "set character set ".$charset);	
		@mysqli_set_charset($this->dbh,$charset);	
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
		if(!@mysqli_select_db($db_h,$db_name)) {
			$this->throwException(@mysqli_error($this->dbh));
		}
	}
	
	////////*************////////
////////	Common Exception  ////////
	////////*************////////
	
	public function throwException($message,$null_records=false,$errormsg='') {
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
			$insert_st = @mysqli_query($this->dbh,$ins_sql);
			
			if(!$insert_st) {
				$this->throwException(@mysqli_error($this->dbh));
			}
			else
				$insert_id = mysqli_insert_id($this->dbh);
			
			if($insert_id == "") {
				$insert_id = true;
			}
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
			
			$update_st = @mysqli_query($this->dbh,$upd_sql);
			
			if(!$update_st) {
			  $this->throwException(@mysqli_error($this->dbh));
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
			
			$select_res = @mysqli_query($this->dbh,$select_sql);
			$select_rec = array();
			if($select_res) {
				if(mysqli_num_rows($select_res)>0) {
					while($each_rec = mysqli_fetch_object($select_res)) {
						$select_rec[] = $each_rec;
					}
					mysqli_free_result($select_res);
				}
				else
				  return 0;
			}
			else {
			   $this->throwException(@mysqli_error($this->dbh));
			}
			return $select_rec;
		}
	}


	////////////////////////////////////////////////////////////////////////////////
	////////			Data Selection with pagination	 			////////////////
	////////			Access :: Public							////////////////
	////////		Attributes :: SQL (WITHOUT LIMIT), per page		////////////////
	///////////////////////////////////////////////////////////////////////////////
	
	/*
	RETURN VALUES :: 
		-1 : Invalid Page,
		-2 : Sql limit parsing error,
		-3 : No sql passed,
		-4 : Query couldn't execute,
		
	RETURNS array('PAGINATION_BLOCK'=>'','PAGINATED_RESULT'=>'') on success
	*/
	
	
	public function db_paginate($SQL,$per_page,$current_page,$baseLink='',$GET_METHOD_PAGE_COUNT = true) {
		$baseLink = ($baseLink!='')?($baseLink):'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$baseLink = explode('?',$baseLink);
		$baseLink = $baseLink[0];
		$select_rec = array();
		$LIMIT_rec = array();
		if($this->dbh) {
			if($SQL) {
				$select_res = @mysqli_query($this->dbh,$SQL);
				if($select_res) {
					if(mysqli_num_rows($select_res)>0) {
						$totalRec = mysqli_num_rows($select_res);
						$totalPages = ceil($totalRec/$per_page);
						mysqli_free_result($select_res);
						if($current_page<=$totalPages) {
							$start = ($current_page==1)?0:(($current_page-1)*$per_page);
							$SQL .= " LIMIT ".$start.",".$per_page;
							$select_Limited_res = @mysqli_query($this->dbh,$SQL);
							if($select_Limited_res) {
								if(mysqli_num_rows($select_Limited_res)>0) {
									while($each_lmted_rec = mysqli_fetch_object($select_Limited_res)) {
										$LIMIT_rec[] = $each_lmted_rec;
									}
									if($GET_METHOD_PAGE_COUNT) {
										$PaginationBlock = generatePaginationBlock($baseLink,$totalPages,$current_page);
									} else {
										$PaginationBlock = generatePaginationBlock($baseLink,$totalPages,$current_page,false);
									}
									mysqli_free_result($select_Limited_res);
									return array('PAGINATION_BLOCK'=>$PaginationBlock,'PAGINATED_RESULT'=>$LIMIT_rec);
								}
								else
					 				return array();
									
							} else {
								return -2;
							}
						} else {
							return -1;
						}
					}
					else
					  return array();
				}
				else {
				   return -4;
				}
			} else {
				return -3;
			}
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
			$del_result = @mysqli_query($this->dbh,$del_sql);
			if($del_result) {
				return true;
			}
			else {
			   $this->throwException(@mysqli_error($this->dbh));
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
			if(mysqli_num_rows($result)>0) {
				$records = array();
				while($eachRec = mysqli_fetch_object($result)) {
					$records[] = $eachRec;
				}
				return $records;
			}
			else {
			   return array();
			}
		}
	}
	
	
	////////*************/////////////////
////////	DIRECT SQL			////////////////
//////  	Access :: Public			////////////////
////// 		Attributes :: RESULT		////////////////
	////////*************////////////////
	
	
	public function db_query($sql) {
		$sql = trim($sql);
		$selectQuery = false;

		if(strrpos($sql,"SELECT") !== false && (strrpos($sql,"SELECT") == 0 || strrpos($sql,"select") == 0)) {
			$selectQuery = true;
		}
		
		if($this->dbh) {
			if(!@mysqli_query($this->dbh,$sql)) {
				return @mysqli_error($this->dbh);
			}
			else {
				if($selectQuery) {
					$resp = @mysqli_query($this->dbh,$sql);
					return $this->get_records($resp);
				}
				else {
					return true;
				}
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
			$result = @mysqli_query($this->dbh,$sql);
			if($result) {
				return $this->get_records($result);
			}
			else {
			   $this->throwException(@mysqli_error($this->dbh));
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
			$delete_st = @mysqli_query($this->dbh,$sql);
			if($delete_st) {
				return true;
			}
			else {
			   $this->throwException(@mysqli_error($this->dbh));
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
			$update_st = @mysqli_query($sql,$this->dbh);
			if($update_st) {
				return $update_st;
			}
			else {
			   $this->throwException(@mysqli_error($this->dbh));
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
			$type_res = @mysqli_query($this->dbh,$type_q);
			if(isset($type_res) && mysqli_num_rows($type_res)) {
				while($each = mysqli_fetch_array($type_res)) {
					$table_column_types[$each['COLUMN_NAME']] = $each['DATA_TYPE'];
				}
				mysqli_free_result($type_res);
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
			$del_result = @mysqli_query($this->dbh,$del_sql);
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
		@mysqli_close($this->dbh);
	}
	
}
?>