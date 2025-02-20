<?php
include_once('includes/config_mssql.php');
if(!empty($_SESSION['User'])) {
	$_SESSION['User'] = '';
	$_SESSION['CONN'] = '';
}
@header('location:'.HTTP_PATH);
?>
