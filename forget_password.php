
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Forgot Password | National Theatre</title>
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
    <link href="asset/css/pages/fp.css" rel="stylesheet">
</head>

<body class="fp-page">
    <div class="fp-box">
        <div class="logo">
            <a href="index.php">National Theatre</a>
            <small>Ticket Management System</small>
        </div>
        <div class="card">
            <div class="body">
                <form id="forget_password" method="POST">
                    <div class="msg">
                        Enter your email address that you used to register to reset your password.
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="email" placeholder="Email" required autofocus>
                        </div>
                    </div>

                    <button class="btn btn-block btn-lg bg-pink waves-effect" type="submit">VERIFY EMAIL</button>

                    <div class="row m-t-20 m-b--5 align-center">
                        <a href="login.php">Sign In</a>
                    </div>
                </form>

                <form id="reset_password" method="POST" class="display-hide">
                    <div class="msg">
                        Enter your new password to reactivate it.
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Password" id="password" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                        </div>
                    </div>
                    <button class="btn btn-block btn-lg bg-pink waves-effect" type="submit">RESET MY PASSWORD</button>

                    <div class="row m-t-20 m-b--5 align-center">
                        <a href="login.php">Sign In</a>
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
    <script src="asset/js/pages/forget_password.js"></script>
</body>

</html>