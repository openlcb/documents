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

<p>
<ul>
<li>You must provide a personal name. You may also provide a company name.  
In our <a href="viewuid.php">listing of assigned ranges</a>, we will publish the company name, if provided.
If there is no company name, we will publish the personal name.  

<li>We will not publish your email address.

<li>You may provide a URL, which we will publish if provided.

<li>You may provide a comment, which we will publish if provided.  
You can use this for company contact information, for example, including an email address.

<li>If you check the &quot;Add to OpenLCB email list&quot; box, 
we will add your email address to a 
<a href="https://sourceforge.net/mailarchive/forum.php?forum_name=openlcb-announcements">mailing list</a>
for occasional updates regarding OpenLCB standards & documentation, policy changes, etc.  
We strongly recommend that you subscribe so that you'll hear about these things in a timely manner.  
The traffic on that list will be low, generally less than one email a month.
</ul>

<hr>
<?php 
// parse out arguments
//parse_str($_SERVER["QUERY_STRING"], $args);
$args = $_POST;

$p_first_name = $args["fn"];
$p_last_name = $args["ln"];
$p_email = $args["em"];
$p_organization = $args["or"];
$p_url = $args["ur"];
$p_comment = $args["cm"];
$p_subscribe = ($args["ms"] != '')?"y":"n";
$p_subscribe_checked = " checked ";  // always check box by default

// check for necessary user ID fields
if ($p_first_name == "" || $p_last_name == "" || $p_email == "") {
    // missing something, present ID form
    echo '<form method="post" action="requestuidrange.php">';
    echo 'First, enter your info. (* fields required)<p>';
    echo 'First Name: <input  name="fn" value="'.$p_first_name.'"/>* ';
    echo 'Last Name:  <input  name="ln" value="'.$p_last_name.'"/>*<p>';
    echo 'Organization: <input  name="or" value="'.$p_organization.'"/><p>';
    echo 'Email Address: <input  name="em" value="'.$p_email.'"/>*<p>';
    echo '<input type="checkbox" name="ms" value="y" '.$p_subscribe_checked.'>Add to OpenLCB email list<p>';
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
                (person_first_name, person_last_name, person_email, person_organization, person_request_IP_address, person_subscribe)
                VALUES 
                ('".$p_first_name."', '".$p_last_name."', '".$p_email."', '".$p_organization."', 
                 '".$_SERVER['REMOTE_ADDR']."', '".$p_subscribe."')
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
    echo 'Last, enter the request information.<br>';
    echo '<form method="post" action="requestuidrange.php">';
    echo 'Requested allocations:<br>';
    echo '<input type="radio" name="sz" group="size" value="1" checked="yes">256 values<p>';
    echo 'Comment: <textarea  name="cm"></textarea><p>';
    echo 'URL: <textarea  name="ur"></textarea><p>';

    echo '<input  type="hidden" name="fn" value="'.$p_first_name.'"/>';
    echo '<input  type="hidden" name="ln" value="'.$p_last_name.'"/>';
    echo '<input  type="hidden" name="or" value="'.$p_organization.'"/>';
    echo '<input  type="hidden" name="em" value="'.$p_email.'"/>';
    echo '<button type="submit">Submit Request</button>';
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
             uniqueid_byte3_value, uniqueid_byte4_value, uniqueid_byte5_value, 
             uniqueid_byte5_mask, uniqueid_user_comment, uniqueid_url)
            VALUES 
            ('".$id."', '".$b0."', '".$b1."', '".$b2."',
             '".$b3."', '".$next."', '0',
             '255','".$args["cm"]."','".$args["ur"]."')
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
