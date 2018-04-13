<?php 
session_start();
$redirect_page = 'login.php';
 if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
    $_SESSION['datapage'] = 'cart';
 }else{
    header('Location:'.$redirect_page);
 }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Cart | National Theatre</title>
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
                <h2>CART</h2>
            </div>
            <div class="row clearfix eventContent">
                <script id="cart_preview" type="text/x-handlebars-template">
                    <!-- Order Info -->
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="header">
                                <h2>ORDER PREVIEW</h2>
                            </div>
                            <div class="body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="text-left">Ticket</th>
                                                <th class="text-left">Type</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-right">Price</th>
                                                <th class="text-right">Total</th>
                                                <th class="text-right">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" rowspan="2"></th>
                                                <th class="text-right" colspan="2">Sub Total:</th>
                                                <th class="text-right" colspan="2">&nbsp;</th>
                                                <th class="text-right">&nbsp;</th>
                                            </tr>
                                            <tr>
                                                <th class="text-right" colspan="2">Grand Total:</th>
                                                <th class="text-right" colspan="2">{{{getTotal cart}}}</th>
                                                <th class="text-right">&nbsp;</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            {{#if cart.length}}
                                            {{#cart}}
                                            <tr data-index="{{@index}}">
                                                <td>{{{inc @index}}}</td>
                                                <td class="text-left">{{{ename}}}</td>
                                                <td class="text-left"><span class="label bg-green">{{uname}}</span></td>
                                                <td class="text-center" width="10">
                                                    <input  type="number" min="1" class="form-control qty-input" value="{{qty}}">
                                                </td>
                                                <td class="text-right">{{{formatCurrency price}}}</td>
                                                <td class="text-right">{{{formatCurrency total}}}</td>
                                                <td class="text-center"><div class="text-center"><button data-action="delTicket" type="button" class="btn btn-danger btn-circle waves-effect waves-circle waves-float" data-action="delete"><i class="material-icons">delete</i></button></div></td>
                                            </tr>
                                            {{/cart}}
                                            {{else}}
                                            <tr>
                                                <td class="text-center" colspan="7">No item in  your cart.</td>
                                            </tr>
                                            {{/if}}
                                    </table>
                                </div>
                                <div class='row'>
                                    <div class='col-md-3 align-right'>
                                        <button class='form-control btn bg-pink submit-button' id="proceedPayment" data-toggle="modal" data-target="#paymentModal">Proceed To Payment »</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- #END# order Info -->
                </script>  
            </div>
        </div>
    </section>

    <!-- payment detail -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="header text-center">
                            <h2>PAYMENT DETAIL</h2>
                        </div>
                        <div class="body">
                            <form id="payment_form" method="post">
                                <div class='row'>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" class="form-control" id="card_name" required name="card_name">
                                                <label class="form-label">Name On Card</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" class="form-control" id="card_number" required name="card_number">
                                                <label class="form-label">Card Number</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="password" class="form-control" id="pin" required name="pin">
                                                <label class="form-label">PIN</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" class="form-control" id="cvc" required name="cvc">
                                                <label class="form-label">CVC</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" class="form-control" id="month" required name="month">
                                                <label class="form-label">MM</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" class="form-control" id="year" required name="year">
                                                <label class="form-label">YYYY</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>                              
                                <div class='row'>
                                    <div class='col-md-12'>
                                        <div class='form-control total text-center btn-info'>
                                        Total:
                                        <span class='amount'>{{{getTotal cart}}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-md-12 form-group'>
                                        <button class='form-control btn btn-primary submit-button' type='submit'>Pay »</button>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-md-12 error form-group hide'>
                                        <div class='alert-danger alert'>
                                        Please correct the errors and try again.
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- #END# payment detail -->

    <?php include 'include/footer.php'; ?>
    <script src="asset/js/pages/cart.js"></script>

</body>

</html>