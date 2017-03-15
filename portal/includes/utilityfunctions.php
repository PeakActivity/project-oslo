<?php

// ---------------------------------------------------------------------------------
//  FILENAME:      utilityfunctions.php
//
//  DESCRIPTION:   Functions used throughout the application
//
//  NOTES:         This source script contains various "utility" type functions 
//                 used by the application.
//                 
//  COPYRIGHTS:    Copyright (c) Watermark Digital 2016
//                 All Rights Reserved                            
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    01/30/16  UJS     Created this file
// ---------------------------------------------------------------------------------

// --------------------------------------------------------------------------           
// Global Defines
// --------------------------------------------------------------------------           
DEFINE ('STRMSG_UNASSIGN_USER', 'UNASSIGN USER FROM RR');


/**
    * Function: ArrayDisplay
    * 
    * NOTE: This function is used to dump the contents of an array to the display. The 
    * function works in conjunction with the DEBUG flag which should be defined at the 
    * top of the file. If DEBUG is defined.. it will print the contents of the array.
    *
    * @param array $aData - the array to display
    * @param string $strTitleText - any text you want to display (like array name)
    * @return void
*/
function ArrayDisplay($aData, $strTitleText)
{
    if (DEBUG)
    {
        echo "-----------------------------";
        echo $strTitleText;
        echo "-----------------------------";
        echo "<pre>";
        print_r($aData);
        echo "</pre>";
    }
}


/**
    * Function: GetDomainFromDomainID
    * 
    * NOTE: This function is used to retrieve the domain from the domain id 
    *
    * @param string $domainid - the domain identifier 
    * @return int $userid - the userid that matches the avatar 
*/
function GetDomainFromDomainID($domainid)
{    
    $query = "SELECT domain FROM domains WHERE id='$domainid'";    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
    $row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
    $domain = $row['domain'];
    return $domain;
}



function GetUserInfo($domain)
{
    $rc = 0;
    $aUsers = array();


    $query = "SELECT id, email, fname, lname, type FROM users WHERE domain='$domain' "; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aUser = array(
                    'id'                    => $row['id'],
                    'email'                 => $row['email'],
                    'lname'             => $row['lname'],
                    'fname'              => $row['fname'],
                    'type'              => $row['type']                   
                    );      
        array_push($aUsers, $aUser);       
    }

    return $aUsers;
} // End of Function

// --------------------------------------------------------------------------           
// Send debug information to browser console
// --------------------------------------------------------------------------
function debug_to_console($data) 
{
    if (is_array($data))
    {
        $output = "<script>console.log('Debug Objects: " . implode(',', $data) . "');</script>";
    }
    
    else
    {
        $output = "<script>console.log('Debug Objects: " . $data . "');</script>";
    }

    echo $output;
}

// --------------------------------------------------------------------------           
// Implementation of mysql_result for mysqli
// --------------------------------------------------------------------------
function Getmysqli_result($res, $row, $field=0) 
{ 
    $res->data_seek($row); 
    $datarow = $res->fetch_array(); 
    return $datarow[$field]; 
} 


// --------------------------------------------------------------------------           
// Checks if needle is within input string.. return -1 on error, otherwise
// returns the location of the needle/substring.
// --------------------------------------------------------------------------
function InString($haystack, $needle) 
{ 
    $pos=strpos($haystack, $needle); 
    if ($pos !== false) 
    { 
        return $pos; 
    } 
    
    else 
    { 
        return -1; 
    } 
} 


// --------------------------------------------------------------------------           
// Performs a string length (but strips whitespace)
// --------------------------------------------------------------------------           
function GetLengthAndTrim($strInput)
{
    $strInput   = trim($strInput);
    $iLength    = strlen($strInput); 
    return $iLength;
}

// --------------------------------------------------------------------------           
// The name field in the companies table should be a unique value.
// Returns id if it does and 0 if the company does not exist.
// -------------------------------------------------------------------------- 
function DoesCompanyExist($company)
{
    $rc = 0;

    $query = "SELECT id FROM companies WHERE name='$company'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_num_rows($result) == 1) 
    {   
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC); 
        $rc = $row['id'];                    
    }    

    return $rc;
} // End of Function

// --------------------------------------------------------------------------           
// The user field in the users table contains the email address for a given 
// user.  Since this is a unique value, check to see if the user previously
// exists.  Returns userid if it does and 0 if the user does not exist.
// -------------------------------------------------------------------------- 
function DoesUserExist($user)
{
    $rc = 0;

    $query = "SELECT id FROM users WHERE user='$user'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_num_rows($result) == 1) 
    {   
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC); 
        $rc = $row['id'];                    
    }    

    return $rc;
} // End of Function


// --------------------------------------------------------------------------  
// Escape data using htmlentities
// --------------------------------------------------------------------------  
function escape_data_html ($data) 
{
    // --------------------------------------------------------------------------           
    // Check if Magic Quotes are enabled.
    // --------------------------------------------------------------------------           
    if (ini_get('magic_quotes_gpc')) 
    {
        $data = stripslashes($data);
    }

    // --------------------------------------------------------------------------           
    // Now deal with idiots who attempt to perform a cross-site scripting 
    // attack by filtering the string.
    // --------------------------------------------------------------------------           
    $data = htmlentities ($data);

    // --------------------------------------------------------------------------           
    // Return the filtered data string back to the caller.
    // --------------------------------------------------------------------------           
    return $data;

} // End of function.


// --------------------------------------------------------------------------  
// Performs a location redirect 
// --------------------------------------------------------------------------  
function RedirectToPage($strPage)
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

    // --------------------------------------------------------------------------                  
    // Check for a trailing slash.
    // --------------------------------------------------------------------------      
    if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\') ) 
    {
        $url = substr ($url, 0, -1); // Chop off the slash.
    }
    
    // --------------------------------------------------------------------------              
    // Add the page.
    // --------------------------------------------------------------------------  
    $url .= '/';
    $url .= $strPage;

    // --------------------------------------------------------------------------  
    // Now redirect.                
    // --------------------------------------------------------------------------  
    header("Location: $url");
    exit();    
}

// --------------------------------------------------------------------------              
// String Concatenate... concats a string to the length specified and adds
// ellipses at the cutoff point.
// --------------------------------------------------------------------------              
function StringConcat ($strPassed, $iMaxLength)  
{ 
    if (strlen($strPassed) > $iMaxLength)  
    { 
        $strPassed = substr($strPassed, 0, $iMaxLength); 
        $strPassed .= "..."; 
    } 
    return $strPassed; 
} 

// --------------------------------------------------------------------------              
// US Phone number validation.. checks NNN-NNN-NNNN where N is a number
// -------------------------------------------------------------------------- 
function IsPhoneNumberValid ($strPhoneNum) 
{
    $rc = false;

    if (ereg("^[0-9]{3}-[0-9]{3}-[0-9]{4}$", $strPhoneNum)) 
    {
        // --------------------------------------------------------------------------              
        // Phone number is valid.
        // --------------------------------------------------------------------------              
        $rc = true;
    }

    return $rc;
}

// --------------------------------------------------------------------------              
// US 5 Digit Zip code validation
// --------------------------------------------------------------------------
function IsZipcodeValid ($strZipcode) 
{
    $length  = strlen ($strZipcode);

    $numeric = is_numeric($strZipcode);

    // --------------------------------------------------------------------------          
    // Make sure we have the following (a non zero length string that is exactly 
    // five characters long, and consists of all numeric characters. 
    // --------------------------------------------------------------------------
    $rc = ($length === false || $length < 5 || $numeric === false);        
    return !($rc);
}


// --------------------------------------------------------------------------              
// Normalizes a Name (capitalizes Words)
// --------------------------------------------------------------------------
function NormalizeName($name) 
{
    $name = strtolower($name);
    $normalized = array();

    foreach (preg_split('/([^a-z])/', $name, NULL, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) as $word) 
    {
        if (preg_match('/^(mc)(.*)$/', $word, $matches)) 
        {
            $word = $matches[1] . ucfirst($matches[2]);
        }

        $normalized[] = ucfirst($word);
    }

    return implode('', $normalized);
}


// --------------------------------------------------------------------------
// Beautifully simple function to push a key and value pair into an 
// associative array.
// --------------------------------------------------------------------------
function array_push_assoc($array, $key, $value)
{
    $array[$key] = $value;
    return $array;
}

// --------------------------------------------------------------------------
// Checks a number to see if it is even. Returns true if even, false if odd.
// --------------------------------------------------------------------------
function IsNumberEven($number)
{
    if ($number % 2 == 0) 
    {
        return true;
    }
    
    else
    {
        return false;
    }
}

//--------------------------------------------------------------------------
// Create a GUID 
//--------------------------------------------------------------------------
function getGUID(){
    mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12);
    return $uuid;
}

//--------------------------------------------------------------------------
// Extract the domain from a URL
//--------------------------------------------------------------------------
function extract_domain($domain)
{
    if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches))
    {
        return $matches['domain'];
    } else {
        return $domain;
    }
}


//--------------------------------------------------------------------------
// Extract the subdomain from a domain
//--------------------------------------------------------------------------
function extract_subdomains($domain)
{
    $subdomains = $domain;
    $domain = extract_domain($subdomains);

    $subdomains = rtrim(strstr($subdomains, $domain, true), '.');

    return $subdomains;
}


//--------------------------------------------------------------------------
// Check if manual approval is turned on for portals
//--------------------------------------------------------------------------
function checkManualValidate() 
{
    $query = "SELECT * FROM platform_prefs WHERE pref = 'validate_portal';"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
    $numrows = mysqli_num_rows($result);    
    $validate = 0;
    if($numrows == 1)
    {
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
        $validate = $row["value"];
    }
    //if manual verification of portals is turned off, remove the bitmask for "not admin approved" (2)
    if($validate < 1) {
        return false;
    } else {
        return true;
    }
}

function function_update_styles($domain_id, $style_name, $property_name, $property_value) {
    return "UPDATE domain_styles SET property_value = '$property_value' WHERE property_name = '$property_name' AND style_name = '$style_name' AND domain_id = '$domain_id';"; 
}

?>