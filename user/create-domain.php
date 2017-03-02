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
			//SQL INSERT GOES HERE
			$query = "INSERT INTO domains (domain, admin_id) VALUES ('$domain', '$admin_id')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
		
			if($result){
				if (!file_exists('../domains/'.$domain.'')) {
    				mkdir('../domains/'.$domain.'', 0755, true);
				}

				$output = "success";
				die($output);
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