<?php 
session_start();
$redirect_page = 'login.php';
 if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
    $_SESSION['datapage'] = 'explore';
 }else{
    header('Location:'.$redirect_page);
 }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Explore | National Theatre</title>
    <?php include 'include/header.php'; ?>
    <link href="asset/css/pages/explore.css" rel="stylesheet" />
</head>

<body class="theme-teal">
    <!-- Page Loader -->
    <?php include 'include/loader.php'; ?>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Search Bar -->
    <?php include 'include/searchbar.php'; ?>
    <!-- #END# Search Bar -->
    <!-- Top Bar -->
    <?php include 'include/navbar2.php'; ?>
    <!-- #Top Bar -->
    <section>
        <!-- #END# Left Sidebar -->
        <?php include 'include/sidebar.php'; ?>
        <!-- Right Sidebar -->
        <!-- #END# Right Sidebar -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>EXPLORE EVENTS</h2>
            </div>

            <div class="row clearfix" id="eventContent">

                <script id="event_li" type="text/x-handlebars-template">
                    {{#events}}
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 event-item" data-index="{{cindex}}">
                        <div class="event card">
                            <div class="header event-card-head">
                                <img class="img-responsive" src="{{url}}" onerror="this.src='uploads/b.jpg';">
                                <!-- <button type="button" class="btn bg-pink waves-effect book-now" 
                                    data-action="bookEvent"
                                    data-trigger="focus" 
                                    data-container="body" 
                                    data-toggle="popover"
                                    data-placement="bottom" title="Popover Title" 
                                            data-content="{{buildUnit unit_list}}">Book Now</button> -->
                                <div class="ticket-detail-quant display-hide">
                                    <i class="material-icons data-close" style="cursor:pointer;" data-action="closePopup">close</i>
                                    <div class="text-center">
                                        <label for="" class="title"></label><br/>
                                        <label for="" class="price"></label>
                                        <div class="form-group align-center">
                                            <div class="input-group" style="width:50%;margin:5px auto;">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-primary btn-lg m-l-15 waves-effect" data-action="adjustQty" data-pt="sub">-</button>
                                                </span>
                                                <input type="text" class="form-control text-center qty-input" value="0" min="0" style="position: relative;top: 2px; padding:2px;">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-primary btn-lg m-l-15 waves-effect" data-action="adjustQty" data-pt="add">+</button>
                                                </span>                                       
                                            </div> 
                                        </div> 
                                    </div>
                                </div>
                                <div class="bg-teal ticket-detail" >
                                    <span class="data-count {{#if nocount}}display-hide{{/if}}">{{total_count}}</span>
                                    <br/>
                                    {{#if unit_list.length}}
                                    <ul class="menu">
                                        {{#unit_list}}
                                        <li data-index="{{@index}}">
                                            <a href="javascript:void(0);" class="waves-effect waves-block" data-action="viewTicket">
                                                <div class="menu-info">
                                                    <h4 class="pull-left name" data-badge-name="{{addUnderscore name}}">{{name}}<span class="badge badge-count {{#if nocount}}display-hide{{/if}}">{{total_count}}</span></h4>                                                    
                                                    <i class="pull-right material-icons">chevron_right</i>
                                                    <h4 class="pull-right price">{{{formatCurrency price}}}</h4>
                                                </div>
                                            </a>
                                        </li>
                                        {{/unit_list}}
                                    </ul>
                                    {{/if}}
                                </div>
                            </div>
                            <div class="body">
                                <h4 class="event-date">{{formatDate date}}</h4>
                                <h2 class="event-title">{{title}}</h2>
                                <p class="event-desc">{{desc}}</p>
                            </div>
                            <div class="footer">
                                <span class="child">{{formatTag tags}}</span>
                                <div class="child">
                                    <i class="material-icons" data-tooltip="tooltip" title="favourite">star_border</i>
                                    <i class="material-icons" data-tooltip="tooltip" title="like">favorite_border</i>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{/events}}
                </script>

            </div>

            <nav class="pg-con">
                <ul class="pagination">
                    <li class="disabled pagination-prev">
                        <a href="javascript:void(0);">
                            <i class="material-icons">chevron_left</i>
                        </a>
                    </li>
                    <li class="active pagination-items"><a href="javascript:void(0);">1</a></li>
                    <li class="pagination-next">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="material-icons">chevron_right</i>
                        </a>
                    </li>
                </ul>
                <p class="list-info"></p>
            </nav>

        </div>
    </section>

    <?php include 'include/footer.php'; ?>    
    <script src="asset/js/pages/explore.js"></script>

</body>

</html>