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
    <title>Sign Up | National Theatre</title>
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

    <!-- sweetalert Select Css -->
    <link href="asset/plugins/sweetalert.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="asset/css/style.css" rel="stylesheet">
    <link href="asset/css/pages/signup.css" rel="stylesheet">

</head>

<body class="register-page">
    <div class="login-box">
        <div class="logo">
            <a href="index.php">National Theatre</a>
            <small>Ticket Management System</small>
        </div>
        <div class="card">
            <div class="body">
                <form id="sign_up" method="POST">
                    <div class="msg">Create your account to enjoy our awesome platform</div>

                    <div class="alert bg-pink alert-dismissible hidden msg" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <span></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">person</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="firstname" placeholder="Fist Name" required autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">person</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="lastname" placeholder="Last Name" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">person</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="username" placeholder="User Name" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">person</i>
                                </span>
                                <input name="gender" type="radio" class="with-gap" id="male" value="M" required>
                                <label for="male">Male</label>
                                <input name="gender" type="radio" id="female" class="with-gap"  value="F" required>
                                <label for="female">Female</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">lock</i>
                                </span>
                                <div class="form-line">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">lock</i>
                                </span>
                                <div class="form-line">
                                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">email</i>
                                </span>
                                <div class="form-line">
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">call</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="phone" placeholder="Phone" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <button class="btn btn-block bg-pink waves-effect" type="submit" style="width:100px;">REGISTER</button>
                        </div>
                    </div>
                    <div class="row m-t-15 m-b--20">
                        <div class="col-xs-12 align-center">
                            <a href="login.php">Already have an account?</a>
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

    <!-- Sweetalert Plugin Js -->
    <script src="asset/plugins/sweetalert.min.js"></script>

    <!-- handlebars -->
    <script src="asset/plugins/handlebars-v4.0.11.js"></script>
    <script src="asset/plugins/underscore.js"></script>

    <!-- Custom Js --> 
    <script src="asset/js/master.js"></script>
    <script src="asset/js/pages/signup.js"></script>

</body>

</html>