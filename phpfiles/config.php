<?php
error_reporting(E_ALL ^ E_DEPRECATED);
	$host = "localhost";
	$user = "root";
	$password = "";
	$db = "phplocator";
	$database="phplocator"; 
	$con = mysql_connect($host,$user,$password) or die(mysql_error());
	mysql_select_db($db);
?>