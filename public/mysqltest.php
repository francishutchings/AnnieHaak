<?php
/* Modify with your specifics */
$server = "mysqlcluster7";    
$user = "kellstan_frank";
$pass = "123InYouGo!";
$db = "edith";
$con = mysql_connect($server, $user, $pass) or die(mysql_error());
mysql_select_db($db, $con) or die(mysql_error());
echo "<h3>Connection was successful!</h3>";
?>