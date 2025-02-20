<?php
include_once('includes/config_mssql.php');
$loginpageJS_CSS_Source = 'http://'.$_SERVER['HTTP_HOST'].'/my_eruit/';
// if(IsLoggedIn()) {
	// @header('location:'.HTTP_PATH.'home');
// }
if($lang=='en') {
	$cssSrc='ltr';
} else {
	$cssSrc='rtl';
}


if(!empty($_POST)) {
	// $checkRecord = $db_MYSQL->db_select("users",array("*"),"WHERE Username='".sanitizepostdata($_POST['Username'])."' AND Password='".sanitizepostdata($_POST['Password'])."'");
	// if($checkRecord && count($checkRecord)>0) {
		// $_SESSION['User']['ID'] = $checkRecord[0]->UserId;
		// $_SESSION['User']['Username'] = $checkRecord[0]->Username;
        
        if($_POST['Username'] == 'admin' && $_POST['Password'] == 'revenger'){
            $_SESSION['User']['ID'] = 1;
            $_SESSION['User']['Username'] = `revenger`;
            
            @header('location:'.HTTP_PATH.'home');
        }

		// if($checkRecord[0]->ConnStr != "") {
		// 	connectToDatabase($checkRecord[0]->ConnStr);
		// }
	// } 
    else {
		addScriptForExec('$.fn.alertUser("Invalid Username or Password.");');	
	}
}
?>
<!DOCTYPE html>
<html lang="<?=$lang?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <base href="<?=HTTP_PATH?>">
    <title><?=PAGE_TITLE?> :: LOGIN</title>
    <link rel="stylesheet" href="<?=$loginpageJS_CSS_Source?>css/<?=$cssSrc?>.css">
	<script type="text/javascript" src="<?=$loginpageJS_CSS_Source?>js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="<?=$loginpageJS_CSS_Source?>js/noty/packaged/jquery.noty.packaged.js"></script>
    <script type="text/javascript" src="<?=$loginpageJS_CSS_Source?>js/formvalidator.js"></script>
</head>
<body>
    <div id="main">
        <div id="headerContainer">&nbsp;</div>     
        <div class="clear">&nbsp;</div>
        <div class="user_block">
       		<h3>LOGIN</h3>
            <div class="user_details">
                <form name="loginForm" class="formArea" id="loginFormID" action="<?=$CURRENT_URL?>" method="post">
                	<label for="username">Username</label>
                    <input name="Username" type="text" class="textbox required" value="">
                    <label for="password">Password</label>
                    <input name="Password" type="password" class="textbox required" value="">
                    <input type="submit" value="Login" name="saveBut" class="button">
                </form>
            </div>
       </div>
    </div>
    <?=executeScriptAfterPageLoad();?>
</body>
</html>