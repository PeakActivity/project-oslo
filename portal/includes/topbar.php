<div class="container-fluid my-2" id="header">
  <div class="container">
    <div class="row">
      <div class="col-6">
        <?php 
          //require_once ('swdb_connect.php'); 

          if(isset($_SESSION['domain'])) {
            $domain_id = $_SESSION['domain']; 
          } else if($login_domain) {
            $domain_id = $login_domain;
          }             

          $query = "SELECT property_name, property_value FROM domain_styles t1 ";
          $query .= "JOIN domains t2 on t1.domain_id = t2.id ";
          $query .= "WHERE t2.domain = '$domain_id' AND t1.style_name = 'logo-image';";
          $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
          $numrows = mysqli_num_rows($result);
          if($numrows == 1) {
            $row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
          ?>
            <a href="/"><img src="domains/<?= $domain_id ?>/images/tn/<?= $row['property_value'] ?>" border="0" height="36" /></a>
          <?php } else { ?>
            <a href="/"><img src="domains/<?= $domain_id ?>/images/tn/logo.png" border="0" height="36" /></a>
          <?php } ?>
      </div>
      <div class="col-6 text-right">
        <?php if(isset($_SESSION['username'])){ ?>
          <span class="align-middle"><a href="#"><?= $_SESSION['username']?> <i class="fa fa-user"></i></a></span>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid bg-primary py-0">
  <div class="container">
    <div class="row">
      <div class="col-6">

        <nav class="navbar navbar-toggleable-md navbar-inverse bg-primary top-nav-bar">
          <button class="navbar-toggler navbar-toggler-left" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <ul class="nav nav-pills hidden-md-down">
            <?php if(isset($_SESSION['type'])) { ?>
              <?php if(($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0) { ?>
              <li class="nav-item">
                <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/manage-portal.php' || $_SERVER['PHP_SELF'] == '/manage-users.php') { echo('active'); } ?>" href="manage-portal.php">Manage Portal <span class="sr-only">(current)</span></a>
              </li>
              <?php } ?>
              <?php if(($_SESSION['type'] & 8) > 0 || ($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0) { ?>
              <li class="nav-item">
                <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/create-postcard.php') { echo('active'); } ?>" href="create-postcard.php">Create Postcard <span class="sr-only">(current)</span></a>
              </li>
              <?php } ?>
            <?php } ?>
          </ul>
          
        </nav>
        </div>
        <div class="col-6">
        <nav class="navbar navbar-inverse bg-primary top-nav-bar pull-right">
          <ul class="navbar-nav">
            <li class="nav-item">
              <?php if(!isset($_SESSION['username'])){ ?>
                <a class="nav-link" href="login.php">Log in</a>
              <?php } else { ?>
                <a class="nav-link" href="../user/logout.php">Log out</a>
              <?php } ?>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <nav class="navbar navbar-inverse bg-primary top-nav-bar">
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav">
          <?php if(isset($_SESSION['type'])) { ?>
            <?php if(($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0) { ?>
            <li class="nav-item">
              <a class="nav-link active" href="manage-portal.php">Manage Portal <span class="sr-only">(current)</span></a>
            </li>
            <?php } ?>
            <?php if(($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0){ ?>
            <li class="nav-item">
              <a class="nav-link" href="manage-users.php">Manage Users</a>
            </li>
            <?php } ?>
          <?php } ?>
        </ul>
      </div>
    </nav>
  </div>
</div>
<?php if($_SERVER['PHP_SELF'] == '/manage-portal.php' || $_SERVER['PHP_SELF'] == '/manage-users.php') { ?>
<div class="container-fluid hidden-md-down" id="manage_submenu">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <ul class="nav nav-pills">
          <?php if(isset($_SESSION['type'])) { ?>
            <?php if(($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0){ ?>
            <li class="nav-item">
              <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/manage-portal.php') { echo('active'); } ?>" href="manage-portal.php">Manage Styles <span class="sr-only">(current)</span></a>
            </li>
            <?php } ?>
            <?php if(($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0) { ?>
            <li class="nav-item">
              <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/manage-users.php') { echo('active'); } ?>" href="manage-users.php">Manage Users <span class="sr-only">(current)</span></a>
            </li>
            <?php } ?>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php } ?>