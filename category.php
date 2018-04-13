<?php 
session_start();
$redirect_page = 'login.php';
 if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
    $_SESSION['datapage'] = 'category';
 }else{
    header('Location:'.$redirect_page);
 }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Category | National Theatre</title>
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
                <h2>CATEGORY SETUP</h2>
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
                                    <form class="form-view other-page" id="category">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="role">Category Code<span class="important-field">*</span></label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" placeholder="Category code" id="code">
                                                    </div>
                                                </div>                                        
                                            </div>
                                            <div class="col-md-6">
                                                <label for="role">Category Name<span class="important-field">*</span></label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" placeholder="Category Name" id="name">
                                                    </div>
                                                </div>                                        
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="level">Category Type<span class="important-field">*</span></label>
                                                <div class="form-group">
                                                <select class="form-control show-tick" title="Select Category Type" id="cat_drop">
                                                        <option value="" disabled>Select Category Type</option>
                                                </select>  
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
    <script src="asset/js/pages/category.js"></script>

</body>

</html>
