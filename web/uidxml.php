<?php require_once('access.php');?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		"http://www.w3.org/TR/html4/loose.dtd">


<?php 

// open DB
global $opts;
mysql_connect($opts['hn'],$opts['un'],$opts['pw']);
@mysql_select_db($opts['db']) or die( "Unable to select database. Error (" . mysql_errno() . ") " . mysql_error());


function value($result, $j, $index) {
    if (255 == mysql_result($result,$j,"uniqueid_byte".$index."_mask")) return "*";
    else return mysql_result($result,$j,"uniqueid_byte".$index."_value");
}

$query = "SELECT * FROM UniqueIDs JOIN Person USING (person_id)
        ORDER BY uniqueid_byte0_value, uniqueid_byte1_value, uniqueid_byte2_value, uniqueid_byte3_value, uniqueid_byte4_value, uniqueid_byte5_value
        ;";
$result=mysql_query($query);

echo "<uidranges>\n";
echo "<!-- '*' means that any values are accepted in that byte, forming the range.-->\n";

for ($j = 0; $j < mysql_numrows($result); $j++) {
    echo '<uidrange>';
    echo '<byte0>'.value($result,$j,"0").'</byte0>';
    echo '<byte1>'.value($result,$j,"1").'</byte1>';
    echo '<byte2>'.value($result,$j,"2").'</byte2>';
    echo '<byte3>'.value($result,$j,"3").'</byte3>';
    echo '<byte4>'.value($result,$j,"4").'</byte4>';
    echo '<byte5>'.value($result,$j,"5").'</byte5>';
    if (mysql_result($result,$j,"person_organization") != '') {
        echo '<organization>'.mysql_result($result,$j,"person_organization").'</organization>';
    } else {
        echo '<firstname>'.mysql_result($result,$j,"person_first_name").'</firstname><lastname>'.mysql_result($result,$j,"person_last_name").'</lastname>';
    }
    echo '<url>'.mysql_result($result,$j,"uniqueid_url").'</url>';
    echo '<comment>'.mysql_result($result,$j,"uniqueid_user_comment").'</comment>';
    echo "</uidrange>\n";
}

echo "</uidranges>\n";

?>


