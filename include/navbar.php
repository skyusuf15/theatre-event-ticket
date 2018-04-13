<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
            <a href="javascript:void(0);" class="bars"></a>
            <a class="navbar-brand" href="<?php if(isset($_SESSION['user']) && $_SESSION['logged_in'] == true){ echo $_SESSION['user']['default_page']; }?>.php">National Theatre - TMS</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button"><i class="material-icons">account_circle</i>
                    <span class="log-label">Hello, <?php if(isset($_SESSION['user']) && $_SESSION['logged_in'] == true){ echo $_SESSION['user']['role']; }?></span></a>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:void(0);" class="js-right-sidebar" data-close="true"><i class="material-icons">person</i>Profile</a></li>
                        <li role="seperator" class="divider"></li>
                        <li><a href="javascript:void(0);" class="logout"><i class="material-icons">input</i>Sign Out</a></li>
                    </ul>
                </li>              
            </ul>
        </div>
    </div>
</nav>