<?php 

// ---------------------------------------------------------------------------------
//  FILENAME:      add_to_cart.php
//
//  DESCRIPTION:   Add Product to shopping cart in $_SESSION  
//                 
//
//  COPYRIGHTS:    Copyright (c) Watermark 2017
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    03/25/17  RAM     Created this file
// ---------------------------------------------------------------------------------
 
// --------------------------------------------------------------------------    	  	
// Connect to the database.
// --------------------------------------------------------------------------  

require_once ('../../includes/managesessions.php'); 
require_once ('../../includes/swdb_connect.php'); 
require_once ('../../includes/utilityfunctions.php'); 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//check if its an ajax request, exit if not
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

	    echo('error');
	    die();
	}
	// --------------------------------------------------------------------------    	  	
	// filter input and store in local variables.
	// --------------------------------------------------------------------------  
	$local_vars = array();
	
	foreach($_POST as $key => $value) {
		$local_vars[$key] = filter_var(trim($value), FILTER_SANITIZE_STRING);
	}

	array_splice($_SESSION['cart_contents'], $local_vars['cart_index'], 1);

	echo("success");
	die();
	 
}

?>
