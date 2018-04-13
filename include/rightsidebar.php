<!-- Right Sidebar -->
<aside id="rightsidebar" class="right-sidebar">
    <ul class="nav nav-tabs tab-nav-right" role="tablist">
        <li role="presentation" class="active" style="width:100%!important;"><a href="#profile" data-toggle="tab">PROFILE</a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade active in" id="profile">
            <div class="demo-settings">
                <form id="profile_update" method="POST">
                    <ul class="setting-list">
                        <li>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="username" id="uname" name="uname" required autofocus value="<?php if(isset($_SESSION['user'])) echo $_SESSION['user']['uname']; ?>">
                                </div>                            
                            </div> 
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="first name" id="fname" name="fname" required value="<?php if(isset($_SESSION['user'])) echo $_SESSION['user']['fname']; ?>">
                                </div>                            
                            </div> 
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="last name" id="lname" name="lname" required value="<?php if(isset($_SESSION['user'])) echo $_SESSION['user']['lname']; ?>">
                                </div>                            
                            </div> 
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="email" class="form-control" placeholder="email" id="em" name="em" required value="<?php if(isset($_SESSION['user'])) echo $_SESSION['user']['email']; ?>">
                                </div>                            
                            </div> 
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="phone" id="ph" name="ph" required value="<?php if(isset($_SESSION['user'])) echo $_SESSION['user']['phone']; ?>">
                                </div>                            
                            </div> 
                        </li>   
                        <li>
                            <div class="form-group">
                                <div class="form-line">
                                    <script>
                                        document.write('<input type="password" class="form-control" name="pass" placeholder="password" id="pass" required value="'+ atob(JSON.parse(sessionStorage.getItem("user_info")).pword)+'">');
                                    </script>                                    
                                </div>                            
                            </div>
                            <div class="switch">
                                <label><input type="checkbox" id="showPassword"><span class="lever"></span></label>
                            </div>
                        </li>
                    </ul>
                    <div class="col-xs-12">
                        <button class="btn btn-block bg-pink waves-effect" type="submit" id="adminLogin">UPDATE PROFILE</button>
                    </div>  
                </form>
            </div>
        </div>
    </div>
</aside>
<!-- #END# Right Sidebar -->