<?php
	ob_start();
	ini_set('mssql.charset','UTF-8');
	session_start();
	error_reporting(E_ALL^E_NOTICE);
   	define("HTTP_PATH", "https://".$_SERVER['HTTP_HOST']."/3d_floor/");
	define("PHYSICAL_PATH", $_SERVER['DOCUMENT_ROOT'].'/3d_floor/');
	define("PAGE_TITLE", "Eruit 3D Floor Planner");
	global $CURRENT_URL,$SCRIPT_PENDING_FOR_EXECUTION,$lang;
	$SCRIPT_PENDING_FOR_EXECUTION = array();

	$v = explode('/', str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']));
	// $lang = ($lang!='')?$lang:'en';
	// $lang=$v[3];
	$page=$v[2];
	
	$page = ($page!='')?$page:'home';

	$mainMenyArr = array('events'=>'Events','interests'=>'Interest','meetings'=>'Meetings','tasks'=>'Tasks');


	foreach($_GET as $k=>$v) {
		if($v!='') {
			${'_GET_'.$k} = $v;
		} else {
			${'_GET_'.$k} = '';
		}
	}
	foreach($_POST as $k=>$v) {
		if($v!='') {
			${'_POST_'.$k} = $v;
		} else {
			${'_POST_'.$k} = '';
		}
	}
	if(!empty($_SESSION['CONN'])) {
		define("DB_HOST",$_SESSION['CONN']['HOST'].':1433');
		define("DB_USER",$_SESSION['CONN']['USER']);
		define("DB_PASSWORD",$_SESSION['CONN']['PASSWORD']);
		define("DB_NAME",$_SESSION['CONN']['DB_NAME']);
		define("DB_CHARSET","utf8");
		include_once(__DIR__."/dbClass_mssql.php");
		// $db = new dbClass_mssql();

	} else {

	// echo var_dump($v[2] == '')

		// $dbhost = $_SERVER['RDS_HOSTNAME'];
		// $dbport = $_SERVER['RDS_PORT'];
		// $dbname = 'eruitco_web';
		// $username = $_SERVER['RDS_USERNAME'];
		// $password = $_SERVER['RDS_PASSWORD'];


		// define("DB_HOST",$dbhost);
		// define("DB_USER",$username);
		// define("DB_PASSWORD",$password);
		// define("DB_NAME",$dbname);
		// define("DB_CHARSET","utf8");

		define("DB_HOST","localhost");
		define("DB_USER","root");
		define("DB_PASSWORD","");
		define("DB_NAME","floor");
		// define("DB_HOST","localhost");
		// define("DB_USER","eruitco_dbuser");
		// define("DB_PASSWORD","db_eruit_2013");
		// define("DB_NAME","eruitco_web");

		define("DB_CHARSET","utf8");
		include_once(__DIR__."/dbClass.php");
		$db_MYSQL = new dbClass();
	}

	$CURRENT_URL = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

	include_once(__DIR__."/functions.php");
?>
