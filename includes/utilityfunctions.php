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

function UnifyInvoice ($strName, $invid1, $invid2)
{
    $rc = false;

    $query = "INSERT INTO invoice_unify (invoice_name, invoice1, invoice2) VALUES ('$strName', '$invid1', '$invid2')";        
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------              
    // Everything ran ok, so update the Success array to tell the user that 
    // this record was added successfully.
    // --------------------------------------------------------------------------              
    if ($result) 
    {
        $rc = true;
    }
   
    return $rc;
}


// --------------------------------------------------------------------------           
// Relink Sub Account to Group
// --------------------------------------------------------------------------           
function RelinkSubAccountToGroup($subaccountid, $newgroupid)
{
    $rc = 0;

    // --------------------------------------------------------------------------           
    // Update the SubAccount table with the new Group.
    // --------------------------------------------------------------------------    
    $query = "UPDATE customer_sub_account SET cdgid='$newgroupid' WHERE id='$subaccountid'";                                  
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 1) 
    {   
        $rc = true;
    }

    return $rc;
} // End of Function


// --------------------------------------------------------------------------           
// Search through Customer Table
// --------------------------------------------------------------------------
function SearchCustomers($search)
{
    $rc = 0;
    $aCustomerSearch = array();

    $query = "SELECT id, description, address1, city, state, zip, phone, accounttype, biller FROM customer_hierarchy_node WHERE 
                            description             LIKE '$search' OR
                            address1                LIKE '$search' OR
                            address2                LIKE '$search' OR
                            city                    LIKE '$search' OR
                            state                   LIKE '$search' OR
                            zip                     LIKE '$search' OR
                            country                 LIKE '$search' OR
                            phone                   LIKE '$search' OR
                            accounttype             LIKE '$search' OR
                            biller                  LIKE '$search' OR
                            foreignaccounttype      LIKE '$search' OR 
                            foreignaccountbiller    LIKE '$search'"; 

    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through each row of data and push it into our Master array.
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    { 
        array_push($aCustomerSearch, $row);        
    }

    // --------------------------------------------------------------------------                 
    // Check that we have valid data returned. Return the array if we do.. 
    // otherwise return 0.
    // --------------------------------------------------------------------------                 
    if (is_array($aCustomerSearch) && !empty($aCustomerSearch))
    {
        return $aCustomerSearch;        
    }

    return $rc;
} // End of Function

// --------------------------------------------------------------------------           
// Given a customer ID, return the customer name 
// --------------------------------------------------------------------------
function GetCustomer($custid)
{
    $rc = 0;

    $query = "SELECT description FROM customer_hierarchy_node WHERE id='$custid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_num_rows($result) == 1) 
    {   
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC); 
        $rc = $row['description'];                    
    }    

    return $rc;
} // End of Function

// --------------------------------------------------------------------------           
// Given an Invoice ID, return the Bundle Aggregator ID
// --------------------------------------------------------------------------
function GetBundleAggregatorIDFromInvoiceID($invid)
{
    $rc = 0;

    $query = "SELECT bundleaggregatorid FROM customer_invoice WHERE id='$invid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_num_rows($result) == 1) 
    {   
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC); 
        $rc = $row['bundleaggregatorid'];                    
    }    

    return $rc;
} // End of Function

// --------------------------------------------------------------------------           
// Given a bundle aggregator ID, return the customer name 
// --------------------------------------------------------------------------
function GetCustomerLegalNameFromAggregatorID($aggid)
{
    $rc = 0;

    $query = "SELECT customerlegalname FROM customer_bundle_aggregator_node WHERE id='$aggid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_num_rows($result) == 1) 
    {   
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC); 
        $rc = $row['customerlegalname'];                    
    }    

    return $rc;
} // End of Function

// --------------------------------------------------------------------------           
// Given a customer ID, return the Bundle Aggregator Descriptive String
// --------------------------------------------------------------------------
function GetBundleAggregators($custid)
{
    $rc = 0;
    $aBundles = array();

    $query = "SELECT id, description, pcshomernum FROM customer_bundle_aggregator_node WHERE hierarchy_id='$custid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the row of data
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $bundlename = $row['description'];
        $bundlenum = $row['pcshomernum'];
        $bundleid =  $row['id'];

        $userstring = sprintf("%s#%s - %s", $bundlenum, $bundleid, $bundlename);        
        array_push($aBundles, $userstring);        
    }

    return $aBundles;
} // End of Function

// --------------------------------------------------------------------------           
// Given a bundle aggregator ID, return the Invoice Descriptive String
// --------------------------------------------------------------------------
function GetInvoices($bundleid)
{
    $rc = 0;
    $aInvoices = array();

    $query = "SELECT id, description, ponumber FROM customer_invoice WHERE bundleaggregatorid='$bundleid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $invname = $row['description'];
        $invnum = $row['ponumber'];
        $invid =  $row['id'];

        $userstring = sprintf("%s#%s - %s", $invnum, $invid, $invname);        
        array_push($aInvoices, $userstring);        
    }

    return $aInvoices;
} // End of Function


function GetInvoiceDescriptiveStringFromID($invid)
{
    $rc = 0;

    $query = "SELECT id, description, ponumber FROM customer_invoice WHERE id='$invid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $invname = $row['description'];
        $invnum = $row['ponumber'];
        $invid =  $row['id'];

        $userstring = sprintf("%s#%s - %s", $invnum, $invid, $invname);        
        return $userstring;        
    }

    return $rc;
}



// --------------------------------------------------------------------------           
//
// --------------------------------------------------------------------------
function IsInvoiceUnified($invid)
{
    $rc = 0;
    $aUInvoices = array();



    $query = "SELECT invoice_name, invoice1, invoice2 FROM invoice_unify WHERE invoice1='$invid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the result and store in Array
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $invname    = $row['invoice_name'];
        $invoice1   = $row['invoice1'];
        $invoice2   = $row['invoice2'];

        if ($invoice1 == $invid)
        {
            array_push($aUInvoices, $invname);
            array_push($aUInvoices, $invoice1);
            array_push($aUInvoices, $invoice2);
            return $aUInvoices;
        }

    }

    return $rc;
} // End of Function



// --------------------------------------------------------------------------           
// Given a bundle aggregator ID, return the Invoice Descriptive String
// --------------------------------------------------------------------------
function GetInvoiceFull($invid)
{
    $rc = 0;
    $aInvoices = array();


    // id, bundleaggregatorid, startdate, enddate, CFMstartdate, MCN, sequence, description, relinkdate, billingtype, monthlychargeperiod, billfrequency, ponumber, creditmemovat,
    // mcs, singledomestic, rollupoption, rollupdetails, GSAFinancialNumber, billingname, billingname2, billingname3, delivery, suppresspaper, printzero, address1, address2,
    // postalcode, city, county, state, country, vattaxcode, vatreason, vatmask, vatregistration, taxoffice, invoicetaxtype, perceptionrate, regionaltaxcountry, 
    // regionaltaxprovince, regionalexemptid, regionalexemptstart, regionalexemptend

    $query = "SELECT * FROM customer_invoice WHERE id='$invid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        array_push($aInvoices, $row);        
    }

    return $aInvoices;
} // End of Function

// --------------------------------------------------------------------------           
// Given a invoice ID, return the Invoice Details Array
// --------------------------------------------------------------------------
function GetInvoiceData($invid)
{
    $rc = 0;

    $query = "SELECT billfrequency, ponumber, billingname, billingname2, address1, address2, postalcode, city, state FROM customer_invoice WHERE id='$invid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through and store in array
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $billfrequency  = $row['billfrequency'];     
        $ponumber       = $row['ponumber'];    
        $billingname    = $row['billingname'];    
        $billingname2   = $row['billingname2'];
        $address1       = $row['address1'];    
        $address2       = $row['address2']; 
        $zipcode        = $row['postalcode']; 
        $city           = $row['city']; 
        $state          = $row['state']; 
    }

    $aInvoiceData = array(
                    'billfrequency'      => $billfrequency,
                    'ponumber'           => $ponumber,
                    'billingname'        => $billingname,
                    'billingname2'       => $billingname2,
                    'address1'           => $address1,
                    'address2'           => $address2,
                    'city'               => $city,
                    'state'              => $state,
                    'zipcode'            => $zipcode);

    return $aInvoiceData;
} // End of Function

// --------------------------------------------------------------------------           
// Given a invoice ID, return the Customer Data Group
// --------------------------------------------------------------------------
function GetCDGS($invid)
{
    $rc = 0;
    $aGroups = array();

    $query = "SELECT id, description, startdate, enddate FROM customer_cdg WHERE invoiceid='$invid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $cdgname = $row['description'];        
        $cdgid =  $row['id'];
        $cdgbegin =  $row['startdate'];
        $cdgend =  $row['enddate'];


        $userstring = sprintf("%s#%s - [%s - %s]", $cdgname, $cdgid, $cdgbegin, $cdgend);        
        array_push($aGroups, $userstring);        
    }

    return $aGroups;
} // End of Function

// --------------------------------------------------------------------------           
// Given a group ID, return the Sub Account Descriptive String
// --------------------------------------------------------------------------
function GetSubAccounts($groupid)
{
    $rc = 0;
    $aSubAccounts = array();

    $query = "SELECT id, originatingcode, servicecenter FROM customer_sub_account WHERE cdgid='$groupid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {               
        $subid =  $row['id'];
        $subcode =  $row['originatingcode'];
        $subcenter =  $row['servicecenter'];

        $userstring = sprintf("%s#%s - %s", $subcode, $subid, $subcenter);        
        array_push($aSubAccounts, $userstring);        
    }

    return $aSubAccounts;
} // End of Function

// --------------------------------------------------------------------------           
// Given a sub account ID, return the charge ID 
// --------------------------------------------------------------------------
function GetChargeIDFromSubAccount($subid)
{
    $rc = 0;
    $aChargeIDs = array();
    
    $query = "SELECT chargeid FROM node_charges WHERE subaccountid='$subid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {               
        $chargeid =  $row['chargeid'];
        array_push($aChargeIDs, $chargeid);        
    }

    return $aChargeIDs;
} // End of Function

// --------------------------------------------------------------------------           
// Given a charge ID, return the actual charges array
// --------------------------------------------------------------------------
function GetChargesFromChargeID($chargeid)
{
    $rc = 0;
    $aCharges = array();
    
    $query = "SELECT * FROM charges WHERE id='$chargeid'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {               
        array_push($aCharges, $row);        
    }

    return $aCharges;
} // End of Function

// --------------------------------------------------------------------------           
// Given a string in the form 
//          NNNNNNNNNNN#nn - DESCRIPTION
//
// This function will find and return "nn"
// --------------------------------------------------------------------------
function GetNumFromString($strInput)
{
    $strPosBegin = InString($strInput, "#");
    $strPosEnd = InString($strInput, "-");

    $strLength = $strPosEnd - $strPosBegin;
    $number = substr ($strInput, $strPosBegin+1, $strLength-1);
    $number = trim($number);
    return $number;
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
// Return the number of clauses based on clause type
// --------------------------------------------------------------------------
function GetNumClauses($type, $pid)
{
    $type = strtolower($type);   

    $query = "SELECT count(*) FROM clauses WHERE id_project=$pid AND clause_type='$type'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
    
    $count = Getmysqli_result($result, 0, 0);
    return $count;
} // End of Function

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
// Used by the Reset Password functionality, this takes a user/email and 
// password and resets it in the users table. Returns true on success and 
// false if unable to set the password.
// --------------------------------------------------------------------------
function UpdateUserPassword($user, $password)
{
    $rc = false;

    $passwordmd5 = sha1($password);
    $query = "UPDATE users SET password='$passwordmd5' WHERE user='$user'";
                    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); // Run the query.
        
    if ((mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 1)  || (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 0))
    { 
        $rc = true;
    } 

    return $rc;
}

// --------------------------------------------------------------------------           
// Given a userid, extract the user's name and username (email) from the 
// database and return it in the form of a username string which matches the
// (lname, fname - email) format.
// --------------------------------------------------------------------------  
function GetUserNameStringFromID($userid)
{
    $username = 0;

    $query = "SELECT lname, fname, user FROM users WHERE id=$userid"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_num_rows($result) == 1) 
    {               
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC); 
        $lname = $row['lname'];
        $fname = $row['fname'];
        $user = $row['user'];

        $username = sprintf("%s, %s - %s", $lname, $fname, $user);
    }    

    return $username;
} // End of Function

// --------------------------------------------------------------------------
// Given the user/email it checks the users table and returns the lastname 
// and firstname associated with that user.. in the form "John Doe"
// --------------------------------------------------------------------------
function GetUserFullname($user)
{
    $username = 0;

    $query = "SELECT lname, fname FROM users WHERE user='$user'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_num_rows($result) == 1) 
    {               
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC); 
        $lname = $row['lname'];
        $fname = $row['fname'];        

        $username = sprintf("%s %s", $fname, $lname);
    }    

    return $username;
} // End of Function

// --------------------------------------------------------------------------           
// Given a fully formed username string (lname, fname - email), return the
// userid of the user.
// --------------------------------------------------------------------------           
function GetIDFromUserName($username)
{
    $id = 0;

    // --------------------------------------------------------------------------
    // If the username is "Unassign" then we return 0
    // --------------------------------------------------------------------------
    if ($username === STRMSG_UNASSIGN_USER)
    {
        return $id;
    }

    // --------------------------------------------------------------------------
    // The incoming username string should be in the form "lname, fname - email"
    // But, the string can also contain just something like "Select a User" etc.
    // So.. make sure our string contains a "dash" character.  If there is no 
    // dash character, then return 0.
    // --------------------------------------------------------------------------
    $pos = strpos($username, "-");

    if ($pos === false) 
    {
        return $id;
    }

    // --------------------------------------------------------------------------
    // Explode on the "Dash" character giving us two strings (using list).
    // tempname contains the "lname, fname" part while $user contains the email
    // address which will be unique.. use it for our query
    // --------------------------------------------------------------------------
    list($tempname, $user) = explode('-', $username);
    $user = trim ($user);

    $query = "SELECT id FROM users WHERE user='$user'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_num_rows($result) == 1) 
    {               
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC); 
        $id = $row['id'];
    }    

    return $id;
} // End of Function

// --------------------------------------------------------------------------           
// Given a fully formed company name, return its company identifier.
// --------------------------------------------------------------------------           
function GetCompanyIDFromCompanyName($name)
{
    $id = 0;

    $query = "SELECT id FROM companies WHERE name='$name'";  
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_num_rows($result) == 1) 
    {               
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC); 
        $id = $row['id'];
    }    

    return $id;
} // End of Function

// --------------------------------------------------------------------------           
// Given a fully formed company identifier, return the company name.
// --------------------------------------------------------------------------  
function GetCompanyNameFromID($id)
{
    $query = "SELECT name FROM companies WHERE id=$id";  
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_num_rows($result) == 1) 
    {               
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC); 
        $name = $row['name'];
    }    

    return $name;
} // End of Function

// --------------------------------------------------------------------------           
// Get Count of Business Days from a date (filter weekends and holidays)
// --------------------------------------------------------------------------  
function GetXBusinessDays($date, $backwards = true, $numdays)
{
    $holidays       = array(); // todo: get_holidays(date("Y", strtotime($date)));
    $working_days   = array();

    do
    {
        $direction = $backwards ? 'last' : 'next';
        $date = date("Y-m-d", strtotime("$direction weekday", strtotime($date)));
        if (!in_array($date, $holidays))
        {
            $working_days[] = $date;
        }
    }
    while (count($working_days) < $numdays);

    return $working_days;
}

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
// Checks session variable to see if a given user in an Admin.
// --------------------------------------------------------------------------
function IsUserAdmin ()  
{
    // --------------------------------------------------------------------------              
    // User is Admin
    // --------------------------------------------------------------------------  
    if ($_SESSION['usertype'] == 1)
    {
        return true;
    }

    // --------------------------------------------------------------------------     
    // User is Not an Admin
    // -------------------------------------------------------------------------- 
    else if ($_SESSION['usertype'] == 0)
    {
        return false;
    }

    return false; 
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
// Email Address Validation (Simple)
// --------------------------------------------------------------------------
function IsEmailAddressValid ($strEmail) 
{
    $lastDot = strrpos ($strEmail, '.');
    $atsign  = strrpos ($strEmail, '@');
    $length  = strlen ($strEmail);

    // --------------------------------------------------------------------------          
    // Make sure we have the following (a non zero length string, one dot, 
    // one atsign, there are at least 3 chars after the dot). 
    // --------------------------------------------------------------------------
    $rc = ($lastDot === false || $atsign === false || $length === false || $lastDot - $atsign < 3 || $length - $lastDot < 3);        
    return !($rc);
}

// --------------------------------------------------------------------------              
// Email Address Validation (Better Validation Check)
// --------------------------------------------------------------------------
function IsEmailAddressValidRegExp ($strEmail) 
{
    $rc = false;

    if (ereg("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)\b", $strEmail)) 
    {
        // --------------------------------------------------------------------------              
        // Email is valid.
        // --------------------------------------------------------------------------              
        $rc = true;        
    }

    else 
    {
        // --------------------------------------------------------------------------              
        // Email is invalid.
        // --------------------------------------------------------------------------              
        $rc = false;        
    }
    
    return $rc;
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
// Returns the date by taking a start date and adding number of days to it.
// --------------------------------------------------------------------------
function GetDateFromAddingDays ($mydate_start, $numdays)
{
    // -------------------------------------------------------------------------- 
    // DO NOT simply copy the object.. it will modify the date.. use clone()
    // -------------------------------------------------------------------------- 
    $date_temp = clone $mydate_start;

    // -------------------------------------------------------------------------- 
    // Create our Date Interval object
    // -------------------------------------------------------------------------- 
    $strNumDays = sprintf("P%sD", $numdays);
    $di = new DateInterval($strNumDays);

    // -------------------------------------------------------------------------- 
    // Add our interval to our start date and format so we can return to caller
    // -------------------------------------------------------------------------- 
    $date_temp->add($di);
    $rc = $date_temp->format('Y-m-d');
    
    // -------------------------------------------------------------------------- 
    // Clean up and return our date back to caller
    // -------------------------------------------------------------------------- 
    $di = null;
    $mydate_start = null;
    unset ($di);
    unset ($date_temp);
    return $rc;
}

// --------------------------------------------------------------------------
// Returns the difference between dates.
// --------------------------------------------------------------------------
function GetDiffBetweenDates ($str_interval, $dt_menor, $dt_maior, $relative=false)
{
    if (is_string( $dt_menor)) $dt_menor = date_create( $dt_menor);
    if (is_string( $dt_maior)) $dt_maior = date_create( $dt_maior);

    $diff = date_diff( $dt_menor, $dt_maior, ! $relative);
       
    switch ($str_interval)
    {
        case "y": 
             $total = $diff->y + $diff->m / 12 + $diff->d / 365.25; 
             break;
        
        case "m":
             $total= $diff->y * 12 + $diff->m + $diff->d/30 + $diff->h / 24;
             break;

        case "d":
             $total = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h/24 + $diff->i / 60;
             break;
           
        case "h": 
             $total = ($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h + $diff->i/60;
             break;
        
        case "i": 
             $total = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s/60;
             break;
        
        case "s": 
             $total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i)*60 + $diff->s;
             break;
    } // end switch( $str_interval)
    
    if ($diff->invert)
    {
        return -1 * $total;
    }

    else
    {
        return $total;
    }
}

// --------------------------------------------------------------------------           
// Use constants to define the Message Box Type.
// --------------------------------------------------------------------------              
DEFINE ('MB_INFO', 1);
DEFINE ('MB_WARNING', 2);
DEFINE ('MB_ERROR', 3);

// --------------------------------------------------------------------------
// Display a Message to the User.
// --------------------------------------------------------------------------
function DisplayMessages($mbtype, $mbtitlebar, $mbtext)
{
    switch ($mbtype)
    {
        case MB_INFO: 
             $alert = "alert-info"; 
             break;

        case MB_WARNING: 
             $alert = ""; 
             break;

        case MB_ERROR: 
             $alert = "alert-danger"; 
             break;
    }

    // --------------------------------------------------------------------------
    // Format the Message Buffer Div container, copy it into a session var.
    // --------------------------------------------------------------------------
    $strBuffer = sprintf('<div class="alert %s fade in">                                
                                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                                    <strong>%s</strong><br>
                                    %s                                      
                                </div>', $alert, $mbtitlebar, $mbtext);

    $_SESSION['lastmessage'] = $strBuffer;
    
    // --------------------------------------------------------------------------
    // Redirect to the error page which will read the session var.
    // --------------------------------------------------------------------------
    RedirectToPage("error.php");
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

?>