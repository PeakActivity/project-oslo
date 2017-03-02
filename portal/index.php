<?php
    require_once ('../includes/managesessions.php'); 

    if(!isset($_SESSION['username'])){
        header( "Location: login.php" ); 
    }
    session_start();
?>

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <title>Welcome to Project Oslo</title>

    <!-- domo arigato mr roboto.. load this font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">


    <!-- page styles -->
    <link rel="stylesheet" href="../assets/css/home.css" />

</head>

<body style="padding-top:56px;">
	<?php include('../includes/topbar.php'); ?>
	<div class="container-fluid style="background-color: rgba(255,255,255, .7);">
		<div class="row">
            <nav class="col-sm-3 col-md-2 hidden-xs-down bg-faded sidebar">
              <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                  <a class="nav-link active" href="#">Portal Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Nav item</a>
                </li>
                <?php if(($_SESSION['usertype'] & 16) > 0) { ?>
                <li class="nav-item">
                  <a class="nav-link" href="user-manage.php">Manage Users</a>
                </li>
                <?php } ?>
                <li class="nav-item">
                  <a class="nav-link" href="#">Nav item again</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">One more nav</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Another nav item</a>
                </li>
              </ul>

              <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                  <a class="nav-link" href="#">Nav item again</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">One more nav</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Another nav item</a>
                </li>
              </ul>
            </nav>
			<main class="col-xs-12 col-sm-9 col-md-10">
              <h1>Welcome to your Oslo Portal, <?= $_SESSION['username'] ?></h1>

              <section class="row text-center placeholders">
                <div class="col-6 col-sm-3 placeholder">
                  <img src="data:image/gif;base64,R0lGODlhAQABAIABAAJ12AAAACwAAAAAAQABAAACAkQBADs=" class="img-fluid rounded-circle" alt="Generic placeholder thumbnail" height="200" width="200">
                  <h4>Label</h4>
                  <div class="text-muted">Something else</div>
                </div>
                <div class="col-6 col-sm-3 placeholder">
                  <img src="data:image/gif;base64,R0lGODlhAQABAIABAADcgwAAACwAAAAAAQABAAACAkQBADs=" class="img-fluid rounded-circle" alt="Generic placeholder thumbnail" height="200" width="200">
                  <h4>Label</h4>
                  <span class="text-muted">Something else</span>
                </div>
                <div class="col-6 col-sm-3 placeholder">
                  <img src="data:image/gif;base64,R0lGODlhAQABAIABAAJ12AAAACwAAAAAAQABAAACAkQBADs=" class="img-fluid rounded-circle" alt="Generic placeholder thumbnail" height="200" width="200">
                  <h4>Label</h4>
                  <span class="text-muted">Something else</span>
                </div>
                <div class="col-6 col-sm-3 placeholder">
                  <img src="data:image/gif;base64,R0lGODlhAQABAIABAADcgwAAACwAAAAAAQABAAACAkQBADs=" class="img-fluid rounded-circle" alt="Generic placeholder thumbnail" height="200" width="200">
                  <h4>Label</h4>
                  <span class="text-muted">Something else</span>
                </div>
              </section>

              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>

            </main>
		</div>
	</div>


    <?php include('../includes/footer.php'); ?>
    <!-- common functions -->
    <script src="assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
   
</body>
</html>