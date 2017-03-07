<div class="navmenu navmenu-default navmenu-fixed-left offcanvas">
</div>

<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a class="navmenu-brand" href="http://<?= $_SERVER['HTTP_HOST'] ?>">
        <img id="sidebar-logo" src="images/platform/Oslo_Logo-header-black.png" height="36" alt="Oslo" />
    </a>
    <ul class="nav navmenu-nav">
        <li class="hidden-md-up register-link"><a href="#">Register</a>
        </li>
        <li class="hidden-md-up"><hr></li>
        <li><a href="#">My Dashboard</a>
        </li>
        <li><a href="#">New Order</a>
        </li>
        <li><a href="#">Open Orders</a>
        </li>
        <li><a href="#">Past Orders</a>
        </li>
        <li><a href="#">Account Settings</a>
        </li>
        <!--<li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">English<b class="caret"></b></a>
              <ul class="dropdown-menu navmenu-nav">
                <li class="active"><a href="#">English</a></li>
                <li><a href="#">Spanish</a></li>
              </ul>
            </li>-->
    </ul>
</div>

<!-- Document Wrapper -->
<div id="main">
    <div id="header">
        <div id="top-navbar" class="navbar navbar-default fixed-top">
            <div class="container-fluid">
                <button class="btn default navbar-toggler navbar-toggler-left" type="button" data-toggle="offcanvas" data-target=".navmenu" data-canvas="body" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
                </button>
                <a href="index.php">
                    <img class="hidden-sm-down" id="top-navbar-logo" src="images/platform/Oslo_Logo-header.png" alt=""  />
                    <img class="hidden-md-up" id="top-navbar-logo" src="images/platform/Oslo_Logo-header-black.png" alt=""  />
                </a>

                <div class="right hidden-sm hidden-xs">
                    <a href="#" class="button button-border button-rounded button-large button-dark noleftmargin register-link">Register</a>
                </div>
            </div>
        </div>
    </div>
