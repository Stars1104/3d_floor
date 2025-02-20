<?php


    // $dbhost = $_SERVER['RDS_HOSTNAME'];
    // $dbport = $_SERVER['RDS_PORT'];
    // $dbname = 'eruitco_web';
    // $username = $_SERVER['RDS_USERNAME'];
    // $password = $_SERVER['RDS_PASSWORD'];

    $dbhost = 'localhost';
    $dbport = 3306;
    $dbname = 'floor';
    $username = 'root';
    $password = '';


  	ini_set('upload_max_filesize','128M');
  	define("IP_ADDR",$dbhost);
  	define("USER_NAME",$username);
  	define("USER_PASS",$password);
  	define("DB_NAME",$dbname);

?>
