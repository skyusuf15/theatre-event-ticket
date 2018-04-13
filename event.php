
<?php 
session_start();
$redirect_page = 'login.php';
 if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
    $_SESSION['datapage'] = 'event';
 }else{
    header('Location:'.$redirect_page);
 }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Event | National Theatre</title>
    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="asset/plugins/bootstrap-material-datetimepicker.css" rel="stylesheet" />
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
                <h2>EVENT/MOVIE SETUP</h2>
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
                                    <form class="form-view other-page" id="event">
                                        <div class="row">                                            
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="role">Event/Movie Code<span class="important-field">*</span></label>
                                                        <div class="form-group">
                                                            <div class="form-line">
                                                                <input type="text" class="form-control" placeholder="Event/Movie Code" id="code">
                                                            </div>
                                                        </div>                                        
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="role">Event/Movie Name<span class="important-field">*</span></label>
                                                        <div class="form-group">
                                                            <div class="form-line">
                                                                <input type="text" class="form-control" placeholder="Event/Movie Name" id="name">
                                                            </div>
                                                        </div>                                        
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="role">Description</label>
                                                        <div class="form-group">
                                                            <div class="form-line">
                                                                <textarea rows="1" class="form-control no-resize auto-growth" placeholder="Description..." id="desc"></textarea>
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
                                            </div>   
                                            <div class="col-md-3 text-center">
                                                <img class="img-responsive thumbnail" id="imgUpload" src="asset/images/no-image.png"> 
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn bg-blue waves-effect btn-upload">Choose File
                                                        <input type="file" name="file" />
                                                    </button>
                                                    <button type="button" class="btn bg-teal waves-effect display-hide file-exist">Change</button>
                                                    <button type="button" class="btn bg-red waves-effect display-hide file-exist">Remove</button>
                                                </div>  
                                            </div>                                         
                                        </div>
                                        <b>Configuration</b>
                                        <hr/>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="level">Hall<span class="important-field">*</span></label>
                                                <div class="form-group">
                                                <select class="form-control show-tick" title="Select Event/Movie Hall" id="hall_drop">
                                                        <option value="" disabled>Select Event/Movie Hall</option>
                                                </select>  
                                                </div>                                      
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email_address">Use Hall Capacity?</label>
                                                <div class="form-group">
                                                    <div class="demo-radio-button">
                                                        <input name="use_cap" type="radio" class="with-gap" id="cap_yes" value="1" >
                                                        <label for="cap_yes">Yes</label>
                                                        <input name="use_cap" type="radio" id="cap_no" class="with-gap" checked  value="0">
                                                        <label for="cap_no">No</label>
                                                    </div>                                            
                                                </div>                                  
                                            </div>                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="level">Date<span class="important-field">*</span></label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="datepicker form-control" placeholder="Please choose date..." id="date">
                                                    </div>
                                                </div>                                      
                                            </div>
                                            <div class="col-md-6">
                                                <label for="level">Time<span class="important-field">*</span></label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="timepicker form-control" placeholder="Please choose time..." id="time">
                                                    </div>
                                                </div>                                      
                                            </div>
                                        </div>
                                        <b>Event Genres</b>
                                        <hr/>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="level">Tag</label>
                                                <div class="form-group">
                                                <select class="form-control show-tick" title="Select Event/Movie Genres" data-live-search="true" multiple id="tag_drop">
                                                        <option value="" disabled>Select Event/Movie Genres</option>
                                                </select>  
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
    <!-- Autosize Plugin Js -->
    <script src="asset/plugins/autosize.js"></script>
    <!-- Moment Plugin Js -->
    <script src="asset/plugins/moment.js"></script>
    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="asset/plugins/bootstrap-material-datetimepicker.js"></script>
    <script>
        
        //Textare auto growth
        autosize($('textarea.auto-growth'));

        $('.datepicker').bootstrapMaterialDatePicker({
            format: 'dddd DD MMMM YYYY',
            clearButton: true,
            weekStart: 1,
            time: false
        });

        $('.timepicker').bootstrapMaterialDatePicker({
            format: 'HH:mm',
            clearButton: true,
            date: false
        });

    </script>
    <script src="asset/js/pages/event.js"></script>

</body>

</html>
