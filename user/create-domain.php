<?php 

// ---------------------------------------------------------------------------------
//  FILENAME:      create-domain.php
//
//  DESCRIPTION:   Domain Creation Script 
//
//  NOTES:         This source script is used to create subdomains for the current
//				   website.
//
//  COPYRIGHTS:    Copyright (c) Watermark 2016
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    02/27/17  RAM     Created this file
// ---------------------------------------------------------------------------------
 
// --------------------------------------------------------------------------    	  	
// Connect to the database. Include utility functions
// --------------------------------------------------------------------------  
require_once ('../includes/swdb_connect.php'); 
require_once ('../includes/utilityfunctions.php');
require_once ('createsubdomain.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//check if its an ajax request, exit if not
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

	    $output = 'notajax';
	    die($output);
	}

	// --------------------------------------------------------------------------    	  	
	// Store the local variables.
	// --------------------------------------------------------------------------  
	$domain = 	filter_var(trim($_POST["domain"]), FILTER_SANITIZE_STRING);
	$admin_id = 	filter_var(trim($_POST["admin_id"]), FILTER_SANITIZE_STRING);

	
	if($_POST["domain"] && $_POST["admin_id"]){
		// --------------------------------------------------------------------------  
		// Look the domain up in the database.
		// --------------------------------------------------------------------------  
		$query = "SELECT * FROM domains WHERE domain = '$domain';"; 
		$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
		$numrows = mysqli_num_rows($result);

		// --------------------------------------------------------------------------  
		// If the number of rows is 1 that means that this domain was found in our 
		// database. Redirect them back to verify with an error
		// --------------------------------------------------------------------------  	

		if($numrows == 1)
		{
			$output = 'exists';
		    die($output);
		}

		// --------------------------------------------------------------------------  
		// If nothing was found, add the domain
		// --------------------------------------------------------------------------  
		else
		{
			CreateSubdomain($domain, "");

			//SQL INSERT GOES HERE
			$query = "INSERT INTO domains (domain, admin_id) VALUES ('$domain', '$admin_id')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
			$last_id = $GLOBALS["___mysqli_ston"]->insert_id;
			

			$query = "INSERT INTO domain_styles (domain_id, style_name, property_name, property_value) VALUES ('$last_id', 'body', 'background-color', '#FFFFFF')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
			
			$query = "INSERT INTO domain_styles (domain_id, style_name, property_name, property_value) VALUES ('$last_id', 'body', 'color', '#333333')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
			
			$query = "INSERT INTO domain_styles (domain_id, style_name, property_name, property_value) VALUES ('$last_id', '.bg-primary', 'background-color', '#0275d8')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
			
			$query = "INSERT INTO domain_styles (domain_id, style_name, property_name, property_value) VALUES ('$last_id', '.navbar-inverse .navbar-nav .nav-link', 'color', 'rgba (255,255,255, .7)')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
			$last_id = $GLOBALS["___mysqli_ston"]->insert_id;
		
			if($last_id > 0){
				if (!file_exists('../portal/domains/'.$domain.'')) {
    				mkdir('../portal/domains/'.$domain.'', 0755, true);
    				mkdir('../portal/domains/'.$domain.'/css', 0755, true);
    				mkdir('../portal/domains/'.$domain.'/images', 0755, true);
				}

				$raw_css = file_get_contents('../portal/assets/templates/css_template.css');
				$css_file = sprintf($raw_css, '#FFFFFF', '#333333', '#0275d8', 'rgba (255,255,255, .7)');

				$domain_name = $_SESSION['domain'];
				$css_written = file_put_contents('../portal/domains/'.$domain.'/css/portal.css', $css_file, FILE_USE_INCLUDE_PATH);

				echo("success");
				die();
			} else {
				//----------------------------------------------------------------------------
				// The insert failed
				//----------------------------------------------------------------------------
				$output = 'error';
			    die($output);
			}
		}
	}
}

?>