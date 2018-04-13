<?php 
session_start();
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
    header('Location:'.$_SESSION['datapage'].'.php');
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>National Theatre - TMS</title>
    <!-- BOOTSTRAP CORE STYLE CSS -->
    <link rel="icon" href="asset/images/favicon.ico">
    <link href="asset/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME CSS -->
    <link href="asset/font-awesome.min.css" rel="stylesheet" />
     <!-- FLEXSLIDER CSS -->
    <link href="asset/css/flexslider.css" rel="stylesheet" />
    <!-- CUSTOM STYLE CSS -->
    <link href="asset/css/pages/index.css" rel="stylesheet" />    
</head>
<body >
   
    <div class="navbar navbar-inverse navbar-fixed-top " id="menu">
    <?php require_once('auth/dal/db_connection.php'); //initialize db once?>
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><img class="logo-custom" src="asset/images/logos.png" alt=""  /> National Theatre</a>
            </div>
            <div class="navbar-collapse collapse move-me">
                <ul class="nav navbar-nav navbar-right">
                    <li ><a href="#home">HOME</a></li>
                    <li><a href="#features-sec">FEATURES</a></li>
                    <li><a href="signup.php">CREATE ACCOUNT</a></li>
                    <li><a href="login.php">LOGIN</a></li>
                </ul>
            </div>           
        </div>
    </div>
      <!--NAVBAR SECTION END-->
       <div class="home-sec" id="home" >
           <div class="overlay">
                <div class="container">
                <div class="row text-center " >
            
                    <div class="col-lg-12  col-md-12 col-sm-12">
                
                    <div class="flexslider set-flexi" id="main-section" >
                        <ul class="slides">
                            <!-- Slider 01 -->
                            <li>
                                <h3>National Theatre</h3>
                                <h1>TICKET ORDERING SYSTEM</h1>
                                <a data-fetch="ticket-login" class="btn btn-success btn-lg action-btn">
                                    GET STARTED
                                </a>
                            </li>
                            <!-- End Slider 01 -->
                            <!-- Slider 02 -->
                            <li>
                                <h3>National Theatre</h3>
                                <h1>THE UNIQUE TICKET SYSTEM</h1>
                                <a data-fetch="ticket-login" class="btn btn-danger btn-lg action-btn">
                                    GET STARTED
                                </a>
                            </li>
                            <!-- End Slider 02 -->
                            <!-- Slider 03 -->
                            <li>
                                <h3>National Theatre</h3>
                                <h1>EASY APPROACH</h1>
                                <a data-fetch="ticket-login" class="btn btn-info btn-lg action-btn">
                                    GET STARTED
                                </a>
                            </li>
                            <!-- End Slider 03 -->
                        </ul>
                    </div>

                </div>
                    
                </div>
                </div>
            </div>
           
       </div>
       <!--HOME SECTION END-->   
    <div  class="tag-line" >
         <div class="container">
           <div class="row  text-center" >           
               <div class="col-lg-12  col-md-12 col-sm-12">               
                    <h2 data-scroll-reveal="enter from the bottom after 0.1s" ><i class="fa fa-circle-o-notch"></i> WELCOME TO THE National Theatre <i class="fa fa-circle-o-notch"></i> </h2>
                </div>
            </div>
        </div>        
    </div>
    <!--HOME SECTION TAG LINE END-->   
         <div id="features-sec" class="container set-pad" >
             <div class="row text-center">
                 <div class="col-lg-8 col-lg-offset-2 col-md-8 col-sm-8 col-md-offset-2 col-sm-offset-2">
                     <h1 data-scroll-reveal="enter from the bottom after 0.2s"  class="header-line">FEATURES </h1>
                     <p data-scroll-reveal="enter from the bottom after 0.3s" >
                        Easy way for customer to order ticket for event, drama, stage play and movies in the Theatre so as to reduce the issue of queue and time wastage.
                         </p>
                 </div>
             </div>
             <!--/.HEADER LINE END-->


           <div class="row" >
           
               
                 <div class="col-lg-4  col-md-4 col-sm-4" data-scroll-reveal="enter from the bottom after 0.4s">
                     <div class="about-div">
                     <i class="fa fa-paper-plane-o fa-4x icon-round-border" ></i>
                   <h3 >ORDER TICKET</h3>
                 <hr />
                       <hr />
                   <p >
                       Explore several tickets for events in the Theatre. The system also support buying more than one ticket per events. 
                       
                   </p>
               <a href="#" class="btn btn-getstarted btn-set"  >GET STARTED</a>
                </div>
                   </div>
                   <div class="col-lg-4  col-md-4 col-sm-4" data-scroll-reveal="enter from the bottom after 0.5s">
                     <div class="about-div">
                     <i class="fa fa-bolt fa-4x icon-round-border" ></i>
                   <h3 >TRACK TICKET</h3>
                 <hr />
                       <hr />
                   <p >
                       Easily track your purchase history by checking list of all your previous and recent tickets.
                       
                   </p>
                         <a href="#" class="btn btn-getstarted btn-set">GET STARTED</a>
                </div>
                   </div>
                 <div class="col-lg-4  col-md-4 col-sm-4" data-scroll-reveal="enter from the bottom after 0.6s">
                     <div class="about-div">
                     <i class="fa fa-magic fa-4x icon-round-border" ></i>
                   <h3 >VALIDATE TICKET</h3>
                 <hr />
                       <hr />
                   <p >
                       Ticketer at the event easily validate your ticket so as to gain a pass for individual event you purchased it tickets.
                   </p>
                         <a href="#" class="btn btn-getstarted btn-set">GET STARTED</a>
                </div>
                   </div>
                 
                 
               </div>
             </div>
   
     <div class="container">
             <div class="row set-row-pad"  >
    <div class="col-lg-4 col-md-4 col-sm-4   col-lg-offset-1 col-md-offset-1 col-sm-offset-1 " data-scroll-reveal="enter from the bottom after 0.4s">

                    <h2 ><strong>Our Location </strong></h2>
        <hr />
                    <div>
                        <h5>National Theatre P.M.B 250,</h5>
                        <h5>Iganmu Area,</h5>
                        <h5>Costain, Lagos State</h5>
                        <h5><strong>Call:</strong> 08093570289, 08179336487 </h5>
                        <h5><strong>Email: </strong>info@theatre.ticket.com.ng</h5>
                    </div>


                </div>
                 <div class="col-lg-4 col-md-4 col-sm-4   col-lg-offset-1 col-md-offset-1 col-sm-offset-1" data-scroll-reveal="enter from the bottom after 0.4s">

                    <h2 ><strong>Social Conectivity </strong></h2>
        <hr />
                    <div >
                        <a href="#">  <img src="asset/images/Social/facebook.png" alt="" /> </a>
                     <a href="#"> <img src="asset/images/Social/google-plus.png" alt="" /></a>
                     <a href="#"> <img src="asset/images/Social/twitter.png" alt="" /></a>
                    </div>
                    </div>


                </div>
                 </div>
     <!-- CONTACT SECTION END-->
    <div id="footer">
          &copy <script>document.write(new Date().getFullYear())</script> theatre.ticket.com.ng | All Rights Reserved |  <a href="#" style="color: #fff" target="_blank">
              Design by : AhmzyJazzy </a>
    </div>
     <!-- FOOTER SECTION END-->
   
    <!--  Jquery Core Script -->
    <script src="asset/plugins/jquery.min.js"></script>
    <!--  Core Bootstrap Script -->
    <script src="asset/plugins/bootstrap/js/bootstrap.js"></script>
    <!--  Flexslider Scripts --> 
         <script src="asset/plugins/jquery.flexslider.js"></script>
     <!--  Scrolling Reveal Script -->
    <script src="asset/plugins/scrollReveal.js"></script>
    <!--  Scroll Scripts --> 
    <script src="asset/plugins/jquery.easing.min.js"></script>
    <!--  Custom Scripts --> 
         <script src="asset/js/pages/index.js"></script>
</body>
</html>