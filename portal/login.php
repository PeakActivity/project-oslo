
<?php
    require_once ('../includes/managesessions.php');
    require_once ('../includes/utilityfunctions.php'); 
    require_once ('../includes/swdb_connect.php'); 

    if(isset($_SESSION['domain'])){
        header( "Location: index.php" ); 
    }
    $login_domain = extract_subdomains($_SERVER['HTTP_HOST']);
    $query = "SELECT domain FROM domains ";
    $query .="WHERE domain = '$login_domain';"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
    $numrows = mysqli_num_rows($result);
    
    if ($numrows < 1) {
        header( "Location: http://www.project-oslo.com" );
    }
?>

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <title>Log in to your portal</title>

    <!-- domo arigato mr roboto.. load this font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" >


    <!-- page styles -->
    <link rel="stylesheet" href="assets/css/home.css" />
    <?php if(isset($_SESSION['domain'])) { ?>
        <link rel="stylesheet" href="domains/<?=$_SESSION['domain']?>/css/portal.css" /> 
    <?php } else if($login_domain){ ?>
        <link rel="stylesheet" href="domains/<?= $login_domain ?>/css/portal.css" /> 
    <?php } ?>

</head>

<body>

    <?php include('includes/topbar.php'); ?>
    <div class="container" id="body-container">
    </div>
    <!-- Register User Modal -->
    <div class="modal fade" id="register-modal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">New User Registration</h5>
            <button type="button" class="close" data-dismiss="modal" id="close-register" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form name="user_register_form" id="user_register_form" role="form" action="../user/register.php" method="post">
          <div class="modal-body">
             <div class="row">
                <div class='col-6'>
                    <div class="form-group">
                       <label for="register_first">First Name</label>
                        <input type="text" class="form-control" id="register_first" name="register_first" required="true" >
                    </div>
                </div>
                <div class='col-6'>
                    <div class="form-group">
                       <label for="register_last">Last Name</label>
                        <input type="text" class="form-control" id="register_last" name="register_last" required="true" >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-12'>
                    <div class="alert alert-danger" role="alert" id="exists-alert">
                      <strong>A conflict exists</strong><br/> Someone has registered this email address. <a href="#" class="login-link">Click here to log in</a>, or if you have forgotten your password, <a href="#" class="recover-link">click here to recover it</a>.
                    </div>
                    <div class="form-group">
                       <label for="register_email">Email address</label>
                        <input type="email" class="form-control" id="register_email" name="register_email" required="true" >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-12'>
                    <div class="alert alert-danger" role="alert" id="password-alert">
                      <strong>Password problem</strong><br/> Either your password was not 8 characters, or the fields did not match. Please fix them and try submitting again.
                    </div>
                    <div class="form-group">
                       <label for="register_password">Password</label>
                        <input type="password" class="form-control" id="register_password" name="register_password" required="true" >
                       <small class="form-text text-muted">Password must be at least 8 characters.</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-12'>
                    <div class="form-group">
                       <label for="register_password_repeat">Repeat Password</label>
                        <input type="password" class="form-control" id="register_password_repeat" name="register_password_repeat" required="true">
                       <small class="form-text text-muted">Password fields must match.</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <a class="login-link" href="#">Return to Login</a>
                </div>
                <div class="col-6" style="text-align: right;">
                    <a class="recover-link" href="#">Forgot Password?</a>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <div style="width:100%; text-align:center;">
                <input type="hidden" id="register_type" name="register_type" value="8">
                <button type="submit" class="btn btn-primary" style="cursor: pointer;">Sign Up</button>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>

<!-- Login Modal -->
    <div class="modal fade" id="login-modal" data-backdrop="static" keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Log in to Project Oslo</h5>
          </div>
          <form name="login_form" id="login_form" role="form" action="../user/login.php" method="post">
          <div class="modal-body">
            <div class="row">
                <div class='col-12'>
                    <div class="alert alert-danger" role="alert" id="login-alert">
                      <strong>There was a problem logging in</strong><br/> Either your password or email address was incorrect. <a href="#" class="register-link">Click here to register as a new user</a>, or if you think you have forgotten your password, <a href="#" class="recover-link">click here to recover it</a>.
                    </div>
                    <div class="alert alert-danger" role="alert" id="unverified-alert">
                      <strong>There was a problem logging in</strong><br/> You must verify your email address before you can log in. We have just re-sent the email just in case you did not receive it before.</a>.
                    </div>
                    <div class="alert alert-danger" role="alert" id="unapproved-user-alert">
                      <strong>There was a problem logging in</strong><br/> The Portal Administrator for <?php echo($_SERVER['HTTP_HOST']) ?> has yet to approve your registration. You will receive an email as soon as it has been approved.</a>.
                    </div>
                    <div class="alert alert-danger" role="alert" id="unapproved-admin-alert">
                      <strong>There was a problem logging in</strong><br/>The Project Oslo Platform administrator has yet to approve your Portal Admin registration. You will receive an email as soon as it has been approved.</a>.
                    </div>
                    <div class="form-group">
                       <label for="login_email">Email address</label>
                        <input type="email" class="form-control" id="login_email" name="login_email" required="true" >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-12'>
                    <div class="form-group">
                       <label for="login_password">Password</label>
                        <input type="password" class="form-control" id="login_password" name="login_password" required="true" >
                       <small class="form-text text-muted">Password must be at least 8 characters.</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <a class="register-link" href="#">Register New User</a>
                </div>
                <div class="col-6" style="text-align: right;">
                    <a class="recover-link" href="#">Forgot Password?</a>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <div style="width:100%; text-align:center;">
                <button type="submit" class="btn btn-primary" style="cursor: pointer;">Log in</button>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>

<!-- Recover Modal -->
    <div class="modal fade" id="recover-modal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Recover your password</h5>
            <button type="button" class="close" id="close-recover" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form name="recover_form" id="recover_form" role="form" action="../user/recover.php" method="post">
          <div class="modal-body">
            <div class="row">
                <div class='col-12'>
                    <div class="alert alert-danger" role="alert" id="recover-alert">
                      <strong>This email does not exist in our system.</strong><a href="#" class="register-link">Click here to register as a new user</a>.
                    </div>
                    <div class="form-group">
                       <label for="recover_email">Email address</label>
                        <input type="email" class="form-control" id="recover_email" name="recover_email" required="true" >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <a class="register-link" href="#">Register new user</a>
                </div>
                <div class="col-6" style="text-align: right;">
                    <a class="login-link" href="#">Back to login</a>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <div style="width:100%; text-align:center;">
                <button type="submit" class="btn btn-primary" style="cursor: pointer;">Recover password</button>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>


    <!-- Register Success Modal -->
    <div class="modal fade" id="success-modal"  data-backdrop="static" keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Thank you for registering with Project Oslo</h5>
          </div>
          <div class="modal-body">
            <p>An email is being sent to the address you provided with a verification link.</p>
            <p>Click this link to verify your email address and begin using Project Oslo.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Regcover Success Modal -->
    <div class="modal fade" id="recover-success-modal"  data-backdrop="static" keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Your password has been reset</h5>
          </div>
          <div class="modal-body">
            <p>An email is being sent to the address you provided with a temporary password.</p>
            <p>Log in to your account using the link in the email, or by <a class="login-link" href="#">clicking here.</a></p>
          </div>
        </div>
      </div>
    </div>


    
    <?php include('includes/footer.php'); ?>
    <!-- common functions -->
    <script src="../assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
   
   <script> 
    $('#exists-alert').hide();
    $('#password-alert').hide();
    $('#login-alert').hide();
    $('#recover-alert').hide();
    $('#unapproved-user-alert').hide();
    $('#unapproved-admin-alert').hide();
    $('#unverified-alert').hide();

    var activeModal = $('#login-modal');
    activeModal.modal('show');

    $('#user_register_form').on('submit', function (e) {
      e.preventDefault();
      
      $.ajax({
        type: 'post',
        url: 'user/register.php',
        data: $('#user_register_form').serialize(),
        success: function (data) {
          if(data == "exists"){
            $('#exists-alert').show();
          }
          if(data == "pass"){
            $('#password-alert').show();
          }
          if(data == "dberr"){
            $('#page-modal').modal('hide');
            $('#dberr-modal').modal('show');
          }
          if(data == "mailerr"){
            $('#page-modal').modal('hide');
            $('#mailerr-modal').modal('show');
          }

          if(data == "success"){
            $('#register-modal').modal('hide');
            $('#success-modal').modal('show');
          }

        },
        error: function (data) {
               var r = jQuery.parseJSON(data.responseText);
               alert("Message: " + r.Message);
               alert("StackTrace: " + r.StackTrace);
               alert("ExceptionType: " + r.ExceptionType);
        }
      });

    });

    

    $('#login_form').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
        type: 'post',
        url: 'user/login.php',
        data: $('#login_form').serialize(),
        success: function (data) {
          if(data == "error"){
            $('#login-alert').show();
          }
          if(data == "unverified"){
            $('#unverified-alert').show();
          }
          if(data == "unapproved-user"){
            $('#unapproved-user-alert').show();
          }
          if(data == "unapproved-admin"){
            $('#unapproved-admin-alert').show();
          }
          if(data == "success"){
             var url = "index.php";
             $(location).attr('href',url);
          }
        },
        error: function (data) {
               var r = data.responseText;
               alert("Message: " + r.Message);
               alert("StackTrace: " + r.StackTrace);
               alert("ExceptionType: " + r.ExceptionType);
        }
      });

    });

    $('#recover_form').on('submit', function (e) {
      e.preventDefault();
      
      $.ajax({
        type: 'post',
        url: 'user/recover.php',
        data: $('#recover_form').serialize(),
        success: function (data) {
          if(data == "error"){
            $('#recover-alert').show();
          }
          if(data == "success"){
            $('#recover-modal').modal('hide');
            $('#recover-success-modal').modal('show');
          }
        },
        error: function (data) {
               var r = jQuery.parseJSON(data.responseText);
               alert("Message: " + r.Message);
               alert("StackTrace: " + r.StackTrace);
               alert("ExceptionType: " + r.ExceptionType);
        }
      });

    });

    $('.login-link').click(function(event){
        event.preventDefault();
        activeModal.modal('hide');
        $('#login-modal').modal('show');
        activeModal = $('#login-modal');
    });

    $('.recover-link').click(function(event){
        event.preventDefault();
        activeModal.modal('hide');
        $('#recover-modal').modal('show');
        activeModal = $('#recover-modal');
    });

    $('.register-link').click(function(event){
        event.preventDefault();
        activeModal.modal('hide');
        $('#register-modal').modal('show');
        activeModal = $('#register-modal');
    });

    $('#close-recover').click(function(event){
        $('#login-modal').modal('show');
        activeModal = $('#login-modal');
    });

    $('#close-register').click(function(event){
        $('#login-modal').modal('show');
        activeModal = $('#login-modal');
    });
   </script>
</body>
</html>