<?php
    require_once ('includes/managesessions.php'); 
    require_once ('includes/swdb_connect.php'); 
    require_once ('includes/utilityfunctions.php');

    if(!isset($_SESSION['username'])){
        header( "Location: login.php" ); 
    }
    if(($_SESSION['type'] & 16) < 1) {
        header( "Location: index.php" ); 
    }

    $style =[];
    $domain = $_SESSION['domain_id'];
    $query = "SELECT style_name, property_name, property_value FROM domain_styles ";
    $query .="WHERE domain_id ='$domain' ORDER BY id;"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
    
    if (!empty($result)) {
      foreach ($result as $row) {
        $styles[] = $row;
      }
    }

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
    <link rel="stylesheet" href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" >


    <!-- page styles -->
    <link rel="stylesheet" href="assets/css/home.css" />
    <link rel="stylesheet" href="assets/plugins/orakuploader/orakuploader.css">
    <link href="assets/plugins/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="domains/<?=$_SESSION['domain']?>/css/portal.css" />

    <!-- DataTables -->
    <link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="assets/css/datatableicons.css" rel="stylesheet" type="text/css" />

</head>

<body>
	<?php include('includes/topbar.php'); ?>


	<div class="container" id="body-container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">

                                    <div class="dropdown pull-right">
                                        <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                                            <i class="zmdi zmdi-more-vert"></i>
                                        </a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#">Action</a></li>
                                            <li><a href="#">Another action</a></li>
                                            <li><a href="#">Something else here</a></li>
                                            <li class="divider"></li>
                                            <li><a href="#">Separated link</a></li>
                                        </ul>
                                    </div>


                                    <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>User ID</th>
                                                <th>E-mail</th>
                                                <th>First name</th>
                                                <th>Last name</th>
                                                <th>a</th>
                                                <th>d</th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php


$utype = "16";
$domain = GetDomainFromDomainID($domain);
$aUsers = GetUserInfo($domain, $utype);

//echo "<pre>";
//print_r($aUsers);
//echo "</pre>";

// --------------------------------------------------------------------------
// Load our Template HTML
// --------------------------------------------------------------------------
$strApprovalsBody = file_get_contents('includes/template_view_approvals.html');



    foreach ($aUsers as $topkey => $topvalue) 
    {
      foreach ($topvalue as $key => $value) 
      {


        if ($key == 'id')
        {
          $id = $value;
        }

        if ($key == 'email')
        {
          $email = $value;
        }

        if ($key == 'lname')
        {
          $lname = $value;
        }

        if ($key == 'fname')
        {
          $fname = $value;
        }




        $accept   = sprintf ('<a href=approvals.php?id=%s&statusid=%s> %s </a>', $id, "1", "Accept");
        $decline  = sprintf ('<a href=approvals.php?id=%s&statusid=%s> %s </a>', $id, "0", "Decline");
      } // end foreach

      
      $strBuffer = sprintf($strApprovalsBody, $id, $email, $lname, $fname, $accept, $decline);
      echo $strBuffer;  

    }
  




?>




                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->
	</div>


    <?php include('../includes/footer.php'); ?>
    <!-- common functions -->
    <script src="assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    
    <!--color picker -->
    <script src="assets/plugins/colorpicker/js/bootstrap-colorpicker.js"></script>

    <!-- Uploader -->
    <script src="assets/plugins/orakuploader/orakuploader.js?ver=1.02"></script>   


        <!-- Datatables-->
        <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
        <script src="assets/plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="assets/plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="assets/plugins/datatables/jszip.min.js"></script>
        <script src="assets/plugins/datatables/pdfmake.min.js"></script>
        <script src="assets/plugins/datatables/vfs_fonts.js"></script>
        <script src="assets/plugins/datatables/buttons.html5.min.js"></script>
        <script src="assets/plugins/datatables/buttons.print.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="assets/plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.scroller.min.js"></script>
        <script src="assets/plugins/datatables/datatables.init.js"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                //$('#datatable').dataTable();
                
                $('#datatable-responsive').DataTable();
                
            } );
            //TableManageButtons.init();

        </script>

  <script>
  $('#error-alert').hide();

  $('#style-form').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
        type: 'post',
        url: 'assets/ajax/update-styles.php',
        data: $('#style-form').serialize(),
        success: function (data) {
          if(data == "error"){
            $('#error-alert').show();
          }
          if(data == "success"){
             location.reload();
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

    $('#body-background-picker').colorpicker();
    $('#topbar-background-picker').colorpicker();
    $('#topbar-link-picker').colorpicker();
    $('#body-text-picker').colorpicker();
  </script>
   
</body>
</html>