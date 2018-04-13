<aside id="leftsidebar" class="sidebar">
    <!-- User Info -->
    <div class="user-info">
        <div class="image">
            <img src="asset/images/user.png" width="48" height="48" alt="User" />
        </div>
        <div class="info-container">
            <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></div>
            <div class="email"></div>
            <div class="btn-group user-helper-dropdown">
                <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                <ul class="dropdown-menu pull-right">
                    <li><a href="javascript:void(0);"><i class="material-icons">person</i>Profile</a></li>
                    <li role="seperator" class="divider"></li>
                    <li><a href="javascript:void(0);"><i class="material-icons">lock</i>Lock Screen</a></li>
                    <li><a href="javascript:void(0);"><i class="material-icons">input</i>Sign Out</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- #User Info -->
    <!-- Menu SIDEBAR -->
    <div class="menu">
        <ul class="list">
            <li class="header">MAIN NAVIGATION</li>
            <li class="active">
                <a href="index.html">
                    <i class="material-icons">home</i>
                    <span>Home</span>
                </a>
            </li>
            <li>
                <a href="pages/typography.html">
                    <i class="material-icons">text_fields</i>
                    <span>Validate Ticket</span>
                </a>
            </li>
            <li>
                <a href="pages/helper-classes.html">
                    <i class="material-icons">layers</i>
                    <span>Sell Ticket</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">widgets</i>
                    <span>Access Control</span>
                </a>
                <ul class="ml-menu">
                    <li>
                        <a href="javascript:void(0);">Role</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);">User</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);">Approval</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);">Notification</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">swap_calls</i>
                    <span>Event Setup</span>
                </a>
                <ul class="ml-menu">
                    <li>
                        <a href="javascript:void(0);">Event Hall</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);">Event Category/Genre</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);">Create Event</a>
                    </li>
                    <!-- <li>
                        <a href="javascript:void(0);">Event Tag</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);">Schedule Event</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);">Create Ticket</a>
                    </li>  -->
                </ul>
            </li>
            <li>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">pie_chart</i>
                    <span>Report</span>
                </a>
                <ul class="ml-menu">
                    <li>
                        <a href="javascript:void(0);">Sales Reciepts</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);">Customer History</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <!-- #Menu -->
    <!-- Footer -->
    <div class="legal">
        <div class="copyright">
            &copy; 2016 - 2017 <a href="javascript:void(0);">National Theatre - TMS</a>.
        </div>
        <!-- <div class="version">
            <b>Version: </b> 1.0.5
        </div> -->
    </div>
    <!-- #Footer -->
</aside>
<?php include "rightsidebar.php"; ?>

<script id="dynamic-menu" type="text/x-handlebars-template">
    <!-- User Info -->
    <div class="user-info">
        <div class="image">
            <img src="asset/images/user.png" width="48" height="48" alt="User" />
        </div>
        <div class="info-container">
            <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{fullname}}</div>
            <div class="email">{{email}}</div>
            <div class="btn-group user-helper-dropdown">
                <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                <ul class="dropdown-menu pull-right">
                    <li><a href="javascript:void(0);" class="js-right-sidebar" data-close="true"><i class="material-icons">person</i>Profile</a></li>
                    <li role="seperator" class="divider"></li>
                    <!-- <li><a href="javascript:void(0);"><i class="material-icons">lock</i>Lock Screen</a></li> -->
                    <li><a href="javascript:void(0);" class="logout"><i class="material-icons">input</i>Sign Out</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- #User Info -->
    <!-- Menu SIDEBAR -->
    <div class="menu">
        <ul class="list">
            <li class="header">MAIN NAVIGATION</li>         
            {{#modules}}
            {{#if has_module}}
            <li class="{{checkActive pages}}">
                <a href="javascript:void(0);" class="menu-toggle {{checkToggle pages}} waves-effect waves-block">
                    <i class="material-icons">{{module_icon}}</i>
                    <span>{{module}}</span>
                </a>
                <ul class="ml-menu" style="display:{{checkDisplay pages}}">
                    {{#pages}}
                    <li class="{{page_class}}">
                        <a href="{{page_name}}.php">{{custom_name}}</a>
                    </li>
                    {{/pages}}
                </ul>
            </li>
            {{else}}
            {{#pages}}
            <li class="{{page_class}}">
                <a href="{{page_name}}.php">
                    <i class="material-icons">{{page_icon}}</i>
                    <span>{{custom_name}}</span>
                </a>
            </li>
            {{/pages}}
            {{/if}}
            {{/modules}}
        </ul>
    </div>
    <!-- #Menu -->
    <!-- Footer -->
    <div class="legal">
        <div class="copyright">
            &copy; 2018 <a href="<?php if(isset($_SESSION['user']) && $_SESSION['logged_in'] == true){ echo $_SESSION['user']['default_page']; }?>.php">National Theatre - TMS</a>.
        </div>
        <div class="version">
            <b>Version: </b> 1.0.0
        </div>
    </div>
    <!-- #Footer -->
    </script>