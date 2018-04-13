<?php 
session_start();
$redirect_page = 'login.php';
 if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
    $_SESSION['datapage'] = 'ticket';
 }else{
    header('Location:'.$redirect_page);
 }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Ticket | National Theatre</title>
    <?php include 'include/header.php'; ?>
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
    <?php include 'include/navbar.php'; ?>
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
                <h2>TICKET SETUP</h2>
            </div>

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="body">
                            <!-- Nav tabs -->
                            <?php include "include/navtabs.php"; ?>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane fade active in" id="formview">
                                    <br/>
                                    <form class="form-view other-page" id="ticket">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="level">Event<span class="important-field">*</span></label>
                                                <div class="form-group">
                                                    <select class="form-control show-tick" title="Select Event" id="event_drop">
                                                        <option value="" disabled>Select Event</option>
                                                    </select>  
                                                </div>                                      
                                            </div>
                                            <div class="col-md-6">
                                                <label for="role">Ticket Code<span class="important-field">*</span></label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" placeholder="Ticket Code" id="code">
                                                    </div>
                                                </div>                                        
                                            </div>                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="role">Ticket Name<span class="important-field">*</span></label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" placeholder="Ticket Name" id="name">
                                                    </div>
                                                </div>                                        
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email_address">Active</label>
                                                <div class="form-group">
                                                    <div class="demo-radio-button">
                                                        <input name="active" type="radio" class="with-gap" id="active_yes" value="1" >
                                                        <label for="active_yes">Yes</label>
                                                        <input name="active" type="radio" id="active_no" class="with-gap" checked  value="0">
                                                        <label for="active_no">No</label>
                                                    </div>                                            
                                                </div>                                  
                                            </div>
                                        </div>
                                        <b>Unit List <span class="badge count">0</span></b>
                                        <hr/>
                                        <div class="row">
                                            <div class="body" style="padding-top:0px;">
                                                <table class="table" id="unitTb">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>Unit</th>
                                                            <th class="text-right">Price</th>
                                                            <th class="text-right">Discount</th>
                                                            <th class="text-right">Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>#</td>
                                                            <td>
                                                                <div class="form-group mb-0">
                                                                <select class="form-control show-tick ms" title="Select Unit" id="unit_drop" data-main="setQuantity">
                                                                    <option value="" disabled>Select Unit</option>
                                                                </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group mb-0">
                                                                    <div class="form-line">
                                                                        <input type="number" min="0" class="form-control text-right" placeholder="0.0" id="unit_price">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group mb-0">
                                                                    <div class="form-line">
                                                                        <input type="number" min="0" class="form-control text-right" placeholder="0" id="unit_discount">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group mb-0">
                                                                    <div class="form-line">
                                                                        <input type="number" min="1" class="form-control text-right" placeholder="1" id="unit_quantity" disabled>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-line text-center">
                                                                    <button class="btn bg-blue btn-sm waves-effect" type="button" data-action="addUnit">Add Line</button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <script id="unit_tb" type="text/x-handlebars-template">
                                                            {{#unit_list}}
                                                            <tr data-index="{{@index}}">
                                                                <td>{{inc @index}}</td>
                                                                <td>{{unit_name}}</td>
                                                                <td class="text-right">{{price}}</td>
                                                                <td class="text-right">{{discount}}</td>
                                                                <td class="text-right">{{quantity}}</td>
                                                                <td>
                                                                    <div class="form-line text-center">
                                                                        <i class="material-icons" style="color:#2196F3;cursor:pointer;" data-action="editUnit">edit</i></button>
                                                                        <i class="material-icons" style="color:red;cursor:pointer;" data-action="delUnit">delete</i>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            {{/unit_list}}
                                                        </script>
                                                    </tbody>
                                                </table>
                                            </div>                                         
                                        </div>
                                        <br>
                                        <?php include "include/button.php"; ?>
                                    </form>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="recordview">
                                    <br/>
                                    <?php include "include/datatable.php"; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <?php include 'include/footer.php'; ?>
    <script src="asset/js/pages/ticket.js"></script>

</body>

</html>