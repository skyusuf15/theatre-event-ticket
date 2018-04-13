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
                <li><a href="javascript:void(0);" class="js-search" data-close="true"><i class="material-icons">search</i></a></li>         
                <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">
                            <i class="material-icons">shopping_basket</i>
                            <span class="label-count">0</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">MY CART</li>
                            <li class="body" id="cart_body">
                                <script id="cart_list" type="text/x-handlebars-template">
                                    <ul class="menu" style="width:300px;">
                                        {{#if cart.length}}
                                        {{#cart}}
                                        <li>
                                            <a href="javascript:void(0);" class="waves-effect waves-block">
                                                <div class="icon-circle bg-light-green" style="background-color:{{getColor ename}}!important;">
                                                    <span class="round-total">{{qty}}</span>
                                                </div>
                                                <div class="menu-info">
                                                    <h4>{{ename}}</h4>
                                                    <p>
                                                        <i class="material-icons">access_time</i> {{uname}}
                                                    </p>
                                                </div>
                                                <div class="menu-info pull-right">
                                                    <h4>{{{formatCurrency total}}}</h4>
                                                    <p>
                                                    {{{formatCurrency price}}}
                                                    </p>
                                                </div>
                                            </a>
                                        </li>
                                        {{/cart}}
                                        {{else}}
                                        <li>
                                            <div class="text-center" style="margin: 30% auto;">
                                                <p>Cart is empty</p>
                                                <i class="material-icons">shopping_cart</i>
                                            </div>
                                        </li>
                                        {{/if}}
                                    </ul>
                                </script>
                            </li>                           
                            <li class="footer">
                                <button type="button" class="btn bg-pink btn-block waves-effect display-hide checkout" onclick="window.location.href='cart.php';">CHECKOUT</button>
                                <a href="javascript:void(0);" class="waves-effect waves-block sub-total">No items in your cart.</a>
                            </li>
                        </ul>
                    </li>    
            </ul>
        </div>
    </div>
</nav>