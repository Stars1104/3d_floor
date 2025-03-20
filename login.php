<?php
include_once('includes/config_mssql.php');


$loginpageJS_CSS_Source = 'http://' . $_SERVER['HTTP_HOST'] . '/my_eruit/';
if (IsLoggedIn()) {
    header('Location: ' . HTTP_PATH . 'home');
    exit;
}

$cssSrc = ($lang == 'en') ? 'ltr' : 'rtl';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Username'], $_POST['Password'])) {
    if ($_POST['Username'] === 'admin' && $_POST['Password'] === 'eruit') {
        $_SESSION['User'] = [
            'ID' => 1,
            'Username' => 'revenger'
        ];
        header('Location: index.php');
        exit;
    } else {
        addScriptForExec('$.fn.alertUser("Invalid Username or Password.");');
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <base href="<?= HTTP_PATH ?>">
    <title><?= PAGE_TITLE ?> :: LOGIN</title>
    <!-- <link rel="stylesheet" href="<?= $loginpageJS_CSS_Source ?>css/<?= $cssSrc ?>.css">
    <link rel="stylesheet" href="./style/login.css">
    <script type="text/javascript" src="<?= $loginpageJS_CSS_Source ?>js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="<?= $loginpageJS_CSS_Source ?>js/noty/packaged/jquery.noty.packaged.js"></script>
    <script type="text/javascript" src="<?= $loginpageJS_CSS_Source ?>js/formvalidator.js"></script> -->
</head>

<style>
    body {
        margin: 0
    }

    .main {
        width: 100%;
        height: 899px;
        background-color: #0e1013;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .header {
        width: 100%;
        height: 53px;
        background: #32353C;
        position: fixed;
        top: 0;
        display: flex;
        align-items: center;
    }

    .header>a img {
        width: 74px;
        height: 26.31px;
        margin-left: 29px;
    }

    .container {
        width: 100%;
        height: 900px;
        position: absolute;
        top: 53px;
        background-color: #0e1013;
    }

    .main-bg {
        position: absolute;
        top: 0;
        width: 100%;
        opacity: 0.6;
    }

    .login_bg_1 {
        position: absolute;
        left: 0;
        top: 0;
        width: 447px;
        height: 595px;
        transform: rotateY(180deg);
        opacity: 0.3;
    }

    .login_bg_2 {
        position: absolute;
        right: 0;
        bottom: 0;
        width: 447px;
        height: 595px;
        opacity: 0.3;
    }

    .user_block {
        width: 939px;
        height: 643px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1;
        background-image: url('./img2/logo3.png');
        gap: 15px;
        position: relative;
    }

    .login-header {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 20px;
    }

    .welcome {
        font-size: 40px !important;
        font-family: 'system-ui';
        color: #dddddd !important;
    }

    .login-header>span {
        font-size: 20px;
        line-height: 22.67px;
        color: #505050;
        font-family: 'system-ui';
        font-weight: 700;
    }

    .formArea {
        width: 772px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .username {
        color: #4290F2;
        font-size: 20px;
        font-weight: 600;
        line-height: 22.67px;
        font-family: system-ui;
    }

    .formArea>input {
        width: 100%;
        background-color: #32353C;
        height: 58px;
        outline: none;
        border: none;
        font-size: 20px;
        color: #dddddd;
        padding: 0 15px;
        margin-top: 8px;
    }

    .password {
        color: #4290F2;
        font-size: 20px;
        font-weight: 600;
        line-height: 22.67px;
        font-family: system-ui;
        margin-top: 15px;
    }

    .formArea>div {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 15px;
        margin-top: 15px;
    }

    .formArea>div input[type=checkbox] {
        width: 30px;
        height: 30px;
        accent-color: #32353C;
    }

    .formArea>div label {
        color: #dddddd;
        font-size: 20px;
        font-weight: 600;
        line-height: 22.67px;
        font-family: system-ui;
    }

    .formArea>input[type=submit] {
        width: 392px;
        height: 70px;
        background-color: #2385FF;
        margin-top: 15px;
        font-family: system-ui;
        font-size: 20px;
        font-weight: 600;
        cursor: pointer;
    }

    .user_block>a {
        width: 772px;
        color: #2385FF;
        font-size: 20px;
        font-weight: 600;
        line-height: 22.67px;
        font-family: system-ui;
        text-align: start;
        margin-top: 30px;
    }

    .close {
        position: absolute;
        right: 35px;
        top: 30px;
    }

    .rose {
        position: absolute;
        bottom: 80px;
        right: 100px;
    }
</style>

<body>
    <div id="main" class="main">
        <header class="header">
            <a class="logo_style" href="<?= 'http://' . $_SERVER['HTTP_HOST'] ?>">
                <img src="./img2/logo.png">
            </a>
        </header>
        <div class="container">
            <img src="./img2/background.jpg" class="main-bg" alt="">
            <img src="./img2/logo1.png" alt="" class="login_bg_1">
            <img src="./img2/logo1.png" alt="" class="login_bg_2">
        </div>
        <div id="headerContainer">&nbsp;</div>
        <div class="clear">&nbsp;</div>
        <div class="user_block">
            <div class="login-header">
                <span class="welcome">Welcome!</span>
                <span>Sign up for Eruit</span>
            </div>
            <div class="user_details">
                <form name="loginForm" class="formArea" id="loginFormID" method="post">
                    <label for="username" class="username">USERNAME</label>
                    <input name="Username" type="text" class="textbox required" value="">
                    <label for="password" class="password">PASSWORD</label>
                    <input name="Password" type="password" class="textbox required" value="">
                    <div>
                        <input type="checkbox">
                        <label for="remember">REMEMBER ME</label>
                    </div>
                    <input type="submit" value="SIGN IN" name="saveBut" class="button">
                </form>
            </div>
            <a href="">HELP I CAN'T SIGN IN</a>
            <img src="./img2/new/close.png" class="close" alt="">
            <img src="./img2/logo2.png" class="rose" alt="">
        </div>
    </div>
    <?= executeScriptAfterPageLoad(); ?>
</body>

</html>