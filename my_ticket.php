<?php 
session_start();
$redirect_page = 'login.php';
 if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
    $_SESSION['datapage'] = 'my_ticket';
 }else{
    header('Location:'.$redirect_page);
 }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>My Ticket | National Theatre</title>
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
                <h2>MY TICKET</h2>
            </div>
            <div class="row clearfix eventContent">
                <script id="order_preview" type="text/x-handlebars-template">
                    <!-- Order Info -->
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="header">
                                <h2>TRANSACTION LIST</h2>
                            </div>
                            <div class="body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="text-left">Ref #</th>
                                                <th class="text-center">Number Of Ticket</th>
                                                <th class="text-right">Amount</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Date Purchased</th>
                                                <th class="text-right">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{#if order.length}}
                                            {{#order}}
                                            <tr data-index="{{@index}}">
                                                <td>{{{inc @index}}}</td>
                                                <td class="text-left">{{{txn_id}}}</td>
                                                <td class="text-center"><span class="label bg-green">{{tqty}}</span></td>
                                                <td class="text-right">{{{formatCurrency tamount}}}</td>
                                                <td class="text-center"><div class='text-center' style='color:#ffffff' ><span class='btn btn-xs bg-blue'>{{{status}}}</span><div></td>
                                                <td class="text-center">{{{formatDate date_created}}}</td>
                                                <td class="text-center">
                                                    <div class="text-center">
                                                        <button data-action="viewDetail" type="button" class="btn btn-primary btn-circle waves-effect waves-circle waves-float">
                                                            <i class="material-icons">view_list</i></button></div>
                                                </td>
                                            </tr>
                                            {{/order}}
                                            {{else}}
                                            <tr>
                                                <td class="text-center" colspan="7">No record.</td>
                                            </tr>
                                            {{/if}}
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- #END# order Info -->
                </script>  
            </div>
        </div>
    </section>

    <script id="ticket_detail" type="text/x-handlebars-template">
        <div class="table-responsive">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-left">Ticket</th>
                        <th class="text-left">Type</th>
                        <th class="text-center">Attendees</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    {{#if cart.length}}
                    {{#cart}}
                    <tr data-index="{{@index}}">
                        <td>{{{inc @index}}}</td>
                        <td class="text-left">{{{ename}}}</td>
                        <td class="text-left"><span class="label bg-orange">{{uname}}</span></td>
                        <td class="text-center"><span class="label bg-blue">{{multiply qty qty_per_unit}}</span></td>
                        <td class="text-center" width="10">{{qty}}</td>
                        <td class="text-right">{{{formatCurrency price}}}</td>
                        <td class="text-right">{{{formatCurrency total}}}</td>
                        <td class="text-center"><div class="text-center"><button type="button" class="btn bg-teal btn-circle waves-effect waves-circle waves-float" data-action="viewReceipt"><i class="material-icons">link</i></button></div></td>
                    </tr>
                    {{/cart}}
                    {{else}}
                    <tr>
                        <td class="text-center" colspan="7">No item in  your cart.</td>
                    </tr>
                    {{/if}}
            </table>
        </div>   
    </script> 
    

    <?php include 'include/footer.php'; ?>
    <script src="asset/js/pages/my_ticket.js"></script>

</body>

</html>