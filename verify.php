<?php
	require_once ('includes/utilityfunctions.php');
	require_once ('includes/swdb_connect.php'); 
	$validated = false;
	$approved - false;
	// --------------------------------------------------------------------------  
	// Check the GUID in the database
	// --------------------------------------------------------------------------  
	$guid = $_GET['guid'];
	$query = "SELECT * FROM users WHERE validationGUID = '$guid';"; 
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	$numrows = mysqli_num_rows($result);

	// --------------------------------------------------------------------------  
	// If the number of rows is 1 that means that this guid was found, therefore
	// validated.
	// --------------------------------------------------------------------------  	

	if($numrows == 1)
	{
		$row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
		$domain = $row['domain'];	
		$adminid = $row['id'];
		$type = $row['type'];
		//if type says the email is not validated, remove the bitmask for unvalidated email (1)
		if (($type & 1) > 0){
			//update type to reflect validated email
			$type = $type - 1;
			//if type says the user is not admin approved
			if(($type & 2) > 0){
				//check to see if manual verification of portals is turned on
				$validate = checkManualValidate();
				//if manual verification of portals is turned off, remove the bitmask for "not admin approved" (2)
				if(!$validate) {
					//we're only auto-validating portal admins for now.. portal users must always be approved by portal admins (for now) 
					if(($type & 16) > 0){
						$type = $type - 2;
					}
				}
			}
			$query = "UPDATE users SET type = '$type' WHERE validationGUID = '$guid';"; 
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
		}
		if(($type & 2) == 0){
			$approved = true;
		}
		$validated = true;
	}


?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <title><?php if($validated){ ?>
    			Oslo - Thank you for validating your email
    		<?php } else { ?>
    			Oslo - There was a problem validating your email
    		<?php } ?>
    		</title>

    <!-- domo arigato mr roboto.. load this font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">


    <!-- page styles -->
    <link rel="stylesheet" href="assets/css/home.css" />

</head>

<body>
	<?php include('includes/topbar.php'); ?>
	<!-- Register Admin Modal -->
	<?php if($validated) { ?>
		<?php if($approved) { 
			session_unset();
			session_destroy();
			?>
		<div class="modal fade" id="page-modal" data-backdrop="static" keyboard="false">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Creating Your Oslo Portal</h5>
		      </div>
			  <div class="modal-body">
	             <div class="row">
	            	<div class='col-12'>
					  	<div class="alert alert-danger" role="alert" id="create-alert">
						  <strong>There was a problem creating your domain</strong><br/> Please click on the link in your validation email again, or contact <a href="mailto:customersupport@project-oslo.com">customer support</a>.
						</div>
					  	<div class="alert alert-danger" role="alert" id="exists-alert">
						  <strong>There was a problem creating your domain</strong><br/> This domain already exists. <a href="http://<?=$domain?>.project-oslo.com">Click here to log in to <?=$domain?>.project-oslo.com</a>.
						</div>
	            		<p>Thank you for validating your email address.</p>
	            		<p>One moment. We are now creating your Oslo portal for <?= $domain ?>.project-oslo.com</p>
	            		
					  	<div class="alert alert-success" role="alert" id="success-alert">
						  <strong>Your Oslo portal has been created.</strong><br/>Note that sometimes it can take a few minutes for the new domain name to take hold. If you receive an error message trying to reach your portal, wait a few minutes and try again.
						</div>
	                </div>
	            </div>
	          </div>  
		      <div class="modal-footer">
		      	<div style="width:100%; text-align:center;">
		        	<a class="btn btn-primary disabled" id="login-link" href="http://<?= $domain ?>.project-oslo.com/login.php">Log in to <?= $domain ?>.project-oslo.com</a>
		        </div>
		      </div>
		    </div>
		  </div>
		</div>
		<?php } else { ?>
		<div class="modal fade" id="page-modal" data-backdrop="static" keyboard="false">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Awaiting Adminstrator Approval</h5>
		      </div>
			  <div class="modal-body">
	             <div class="row">
	            	<div class='col-12'>
	            		<p>Thank you for validating your email address.</p>
	            		<p>Your account is awaiting administrator approval. You will receive an email as soon as you are approved.</p>	            		
	                </div>
	            </div>
	          </div>  
		    </div>
		  </div>
		</div>
		<?php } ?>
	<?php } else {?>
		<div class="modal fade" id="page-modal">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Validation Error</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
			  <div class="modal-body">
	            <p>There was an error when trying to validate your email address. You can <a href="index.php">register</a> again, or email <a href="mailto:customersupport@project-oslo.com">customer support</a> and inform them of this error.</p>
	          </div>
	          <div class="modal-footer">
		      	<div style="width:100%; text-align:center;">
		        	<a class="btn btn-primary" href="mailto:customersupport@project-oslo.com">Contact Customer Support</a>
		        </div>
		      </div>
	        </div>
	      </div>
	    </div>
	<?php } ?>

	<form id="create-domain" name="create-domain" action="user/create-domain.php" method="post">
		<input type="hidden" id="domain" name="domain" value="<?= $domain ?>"/>
		<input type="hidden" id="admin_id" name="admin_id" value="<?= $adminid ?>"/>
	</form>

    <?php include('includes/footer.php'); ?>
    <!-- common functions -->
    <script src="assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
   
   <script> 
   $(document).ready(function(){
		$('#page-modal').modal('show');
		$('#create-alert').hide();
		$('#exists-alert').hide();
		$('#success-alert').hide();
		<?php if ($validated == true && $approved == true) { ?>
		$('#create-domain').submit();
		<?php } ?>
   });

   $('#create-domain').on('submit', function (e) {
      e.preventDefault();
      
      $.ajax({
        type: 'post',
        url: 'portal/user/create-domain.php',
        data: $('#create-domain').serialize(),
        success: function (data) {
          if(data == "error"){
          	$('#create-alert').show();
          }
          if(data == "exists"){
          	$('#exists-alert').show();
          }
          
          if(data == "success"){
			$('#success-alert').show();
			$('#login-link').removeClass("disabled");
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
   </script>
</body>
</html>