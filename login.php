<?php 
session_start();
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
    header('Location:'.$_SESSION['datapage'].'.php');
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Sign In | National Theatre</title>
    <!-- Favicon-->
    <link rel="icon" href="../../favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="asset/material/font.css" rel="stylesheet" type="text/css">
    <link href="asset/material/icon.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="asset/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="asset/plugins/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="asset/plugins/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="asset/css/style.css" rel="stylesheet">
    <link href="asset/css/pages/login.css" rel="stylesheet">

</head>

<body class="login-page">
    <div class="login-box">
        <div class="logo">
            <a href="index.php">National Theatre</a>
            <small>Ticket Management System</small>
        </div>
        <div class="card">
            <div class="body">
                <form id="sign_in" method="POST">
                    <div class="msg">Sign in to start your session</div>

                    <div class="alert bg-pink alert-dismissible hidden msg" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <span></span>
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="username" placeholder="Username" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="row">
                        <!-- <div class="col-xs-8 p-t-5">
                            <input type="checkbox" name="rememberme" id="rememberme" class="filled-in chk-col-pink">
                            <label for="rememberme">Remember Me</label>
                        </div> -->
                        <div class="col-xs-12">
                            <button class="btn btn-block bg-pink waves-effect" type="submit" id="adminLogin">SIGN IN</button>
                        </div>
                    </div>
                    <div class="row m-t-15 m-b--20">
                        <div class="col-xs-6">
                            <a href="signup.php">Register Now!</a>
                        </div>
                        <div class="col-xs-6 align-right">
                            <a href="forget_password.php">Forgot Password?</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="asset/plugins/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="asset/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="asset/plugins/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="asset/plugins/jquery.validate.js"></script>

    <!-- handlebars -->
    <script src="asset/plugins/handlebars-v4.0.11.js"></script>
    <script src="asset/plugins/underscore.js"></script>

    <!-- Custom Js --> 
    <script src="asset/js/master.js"></script>
    <script src="asset/js/pages/login.js"></script>

</body>

</html>