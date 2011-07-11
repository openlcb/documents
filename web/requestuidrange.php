<?php require_once('access.php');?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Request OpenLCB Unique ID Range</title>
    
    <STYLE TYPE="text/css">
    </STYLE>

</head>
<body>
<IMG SRC="logo-ajs-dph.png" NAME="graphics1" ALIGN=RIGHT WIDTH=195 HEIGHT=80 BORDER=0>
<h1>Request OpenLCB Unique ID Range</h1>  

This page allows you to request a range of 256 OpenLCB Unique IDs for your own use.
<P>
For more information on OpenLCB, please see the <a href="../documents/index.html">documentation page</a>.
For more information on OpenLCB unique ID assignment, please see the current draft
<a href="../specs/drafts/GenUniqueIdS.pdf">specification</a> and 
<a href="../specs/drafts/GenUniqueIdTN.pdf">technical note</a>.

<?php 
// parse out arguments
//parse_str($_SERVER["QUERY_STRING"], $args);
$args = $_POST;

$p_first_name = $args["fn"];
$p_last_name = $args["ln"];
$p_email = $args["em"];
$p_organization = $args["or"];

// check for necessary user ID fields
if ($p_first_name == "" || $p_last_name == "" || $p_email == "") {
    // missing something, present ID form
    echo '<form method="post" action="requestuidrange.php">';
    echo 'Your info (* fields required):<br>';
    echo 'First Name: <input  name="fn" value="'.$p_first_name.'"/>*';
    echo 'Last Name:  <input  name="ln" value="'.$p_last_name.'"/>*<br>';
    echo 'Organization: <input  name="or" value="'.$p_organization.'"/><br>';
    echo 'Email Address: <input  name="em" value="'.$p_email.'"/>*<br>';
    echo '<button type="submit">Next</button>';
    echo '</form>';
    echo '</body></html>';
    return;
}

// open DB
global $opts;
mysql_connect($opts['hn'],$opts['un'],$opts['pw']);
@mysql_select_db($opts['db']) or die( "Unable to select database");

// see if person already present
$query = "SELECT * FROM Person
            WHERE person_email = '".$p_email."'
        ;";
$result=mysql_query($query);

if (mysql_numrows($result) == 0) {
    // insert this person
    $query = "INSERT INTO Person
                (person_first_name, person_last_name, person_email, person_organization, person_request_IP_address)
                VALUES 
                ('".$p_first_name."', '".$p_last_name."', '".$p_email."', '".$p_organization."', '".$_SERVER['REMOTE_ADDR']."')
            ;";
    $result=mysql_query($query);    
    $id = mysql_insert_id();   // inserted ID
} else if (mysql_numrows($result) == 1) {
    // person already exists
    $id = mysql_result($result,0,"person_id");
} else {
    // can't cope
    echo "Sorry, have multiple records for this email, can't cope. Please contact openlcb@pacbell.net for help";
}

// now, set if type request present
// (only 256 for now)
$size = $args["sz"];
if ($size == "") {
    echo '<form method="post" action="requestuidrange.php">';
    echo 'Requested allocations:<br>';
    echo '<input type="radio" name="sz" group="size" value="1" checked="yes">256 values<br>';
    echo 'Comment: <textarea  name="cm"></textarea><br>';

    echo '<input  type="hidden" name="fn" value="'.$p_first_name.'"/>';
    echo '<input  type="hidden" name="ln" value="'.$p_last_name.'"/>';
    echo '<input  type="hidden" name="or" value="'.$p_organization.'"/>';
    echo '<input  type="hidden" name="em" value="'.$p_email.'"/>';
    echo '<button type="submit">Next</button>';
    echo '</form>';
    echo '(Larger requests are handled offline, please contact openlcb@pacbell.net directly)';
    echo '</body></html>';
    return;
}

// got all needed info, make the request
$b0 = 5;
$b1 = 1;
$b2 = 1;
$b3 = 1;

$query = "SELECT MAX(uniqueid_byte4_value) FROM UniqueIDs
            WHERE uniqueid_byte0_value = '".$b0."'
            AND   uniqueid_byte1_value = '".$b1."'
            AND   uniqueid_byte2_value = '".$b2."'
            AND   uniqueid_byte3_value = '".$b3."'
        ;";
$result=mysql_query($query);

$next = 1+mysql_result($result,0,0);

// insert
$query = "INSERT INTO UniqueIDs
            (person_id, uniqueid_byte0_value, uniqueid_byte1_value, uniqueid_byte2_value, 
             uniqueid_byte3_value, uniqueid_byte4_value, uniqueid_user_comment, uniqueid_byte5_value, 
             uniqueid_byte5_mask)
            VALUES 
            ('".$id."', '".$b0."', '".$b1."', '".$b2."',
             '".$b3."', '".$next."','".$args["cm"]."', '0',
             '255')
        ;";
$result=mysql_query($query);
if (! $result) {
    echo "Could not commit to database, contact openlcb@pacbell.net for help. Error (" . mysql_errno() . ") " . mysql_error();
    return;
}

// inform user

$range = $b0.' . '.$b1.' . '.$b2.' . '.$b3.' . '.$next.' . '.' (0-255)';

echo 'Your assigned range is:<br>';
echo $range;

// and send email notification
define(EMAIL_FROM, "openlcb@pacbell.net");

$subject = "OpenLCB unique ID range assignment";
$body = "You were assigned an OpenLCB unique ID range of: \n".$range."\n\nThe OpenLCB Group\n";

include('Mail.php');

$recipients = array( $p_email ); # Can be one or more emails

$headers = array (
    'From' => EMAIL_FROM,
    'To' => join(', ', $recipients),
    'CC' => EMAIL_FROM,
    'Subject' => $subject,
);

$mail_object =& Mail::factory('smtp',
    array(
        'host' => 'prwebmail',
        'auth' => true,
        'username' => 'openlcb',
        'password' => $opts['email'],
        #'debug' => true, # uncomment to enable debugging
    ));

$ok = $mail_object->send($recipients, $headers, $body);

if ($ok) {
    echo "<p>Confirmation email sent</P>";
} else {
    echo "<p>Unable to send confirmation email</P>";
}

?>

</body></html>
