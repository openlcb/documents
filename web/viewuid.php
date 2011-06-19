<?php require_once('access.php');?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>View OpenLCB Unique ID Ranges</title>
    
    <STYLE TYPE="text/css">
    </STYLE>

</head>
<body>
<IMG SRC="logo-ajs-dph.png" NAME="graphics1" ALIGN=RIGHT WIDTH=195 HEIGHT=80 BORDER=0>
<h1>View OpenLCB Unique ID Ranges</h1>  

<?php 

// open DB
global $opts;
mysql_connect($opts['hn'],$opts['un'],$opts['pw']);
@mysql_select_db($opts['db']) or die( "Unable to select database");


function value($result, $j, $index) {
    if (255 == mysql_result($result,$j,"uniqueid_byte".$index."_mask")) return "*";
    else return mysql_result($result,$j,"uniqueid_byte".$index."_value");
}

$query = "SELECT * FROM UniqueIDs
        ORDER BY uniqueid_byte0_value, uniqueid_byte1_value, uniqueid_byte2_value, uniqueid_byte3_value, uniqueid_byte4_value, uniqueid_byte5_value
        ;";
$result=mysql_query($query);

echo '<table border="1">';
echo "'*' means that any values are accepted in that byte, forming the range.<p>";

for ($j = 0; $j < mysql_numrows($result); $j++) {
    echo '<tr>';
    echo '<td WIDTH="20" ALIGN="CENTER">'.value($result,$j,"0").'</td>';
    echo '<td WIDTH="20" ALIGN="CENTER">'.value($result,$j,"1").'</td>';
    echo '<td WIDTH="20" ALIGN="CENTER">'.value($result,$j,"2").'</td>';
    echo '<td WIDTH="20" ALIGN="CENTER">'.value($result,$j,"3").'</td>';
    echo '<td WIDTH="20" ALIGN="CENTER">'.value($result,$j,"4").'</td>';
    echo '<td WIDTH="20" ALIGN="CENTER">'.value($result,$j,"5").'</td>';
    echo '<td>'.mysql_result($result,$j,"uniqueid_user_comment").'</td>';
    echo '</tr>';
}

echo '</table>';

?>

</body></html>
