<?php 
session_start();
$redirect_page = 'login.php';
 if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
    $_SESSION['datapage'] = 'role';
 }else{
    header('Location:'.$redirect_page);
 }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Role | National Theatre</title>
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
                <h2>ROLE SETUP</h2>
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
                                    <form class="form-view other-page" id="role">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="role">Role</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" placeholder="Role" id="role_name">
                                                    </div>
                                                </div>
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
                                            <div class="col-md-7">
                                                <label for="email_address">Page Access</label>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <select id="role_drop" lass="form-control show-tick" data-width="100%" data-live-search="true" show-menu-arrow title="Please select pages role can have access to" multiple>
                                                            <option value="" disabled>-- Please select pages role can have access to --</option>
                                                        </select> 
                                                        <span class="input-group-btn">
                                                        <button type="button" class="btn btn-primary btn-lg m-l-15 waves-effect" id="addPages" data-action="addPages">Add Pages</button>
                                                        </span>                                       
                                                    </div> 
                                                </div>                                        
                                                <div id="accessTb" class="body table-responsive" style="max-height:300px;">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Page</th>
                                                                <th>Module</th>
                                                                <th>Default page</th>
                                                                <th class="text-center"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <script id="page_access_tb" type="text/x-handlebars-template">
                                                                {{#pages}}
                                                                <tr data-index="{{@index}}">
                                                                    <th scope="row">{{inc @index}}</th>
                                                                    <td>{{custom_name}}</td>
                                                                    <td>{{module_custom_name}}</td>
                                                                    <td><input name="default_page_id" type="radio" class="with-gap" id="active_yes_{{@index}}" value="{{page_id}}" ><label for="active_yes_{{@index}}"></label></td>
                                                                    <td class="text-center delete" data-action="delAccess" style="color:#F44336;cursor:pointer;"><i class="material-icons">delete</i></td>
                                                                </tr> 
                                                                {{/pages}}
                                                            </script>
                                                            <tr>
                                                                <td colspan="7" class="text-center">No page access configure.</td>
                                                            </tr>                                                     
                                                        </tbody>
                                                    </table>
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
    <script src="asset/js/pages/role.js"></script>

</body>

</html>
