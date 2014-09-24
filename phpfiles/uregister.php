<?php
/** save codes by loading database module */
include_once("config.php");

/** these headers allow sending data from mobile */
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");

/** variables for different data received */
$fname     = ucwords($_GET["fullname"]);
$email     = $_GET["email"];
$username  = $_GET["username"];
$password  = $_GET["pass"];


/** sql statements go here */
$sql = "INSERT INTO users (username,password,name,email)
values('" . $username . "','" . $password . "','" . $fname . "','" . $email . "')";
mysql_query($sql);


/** return a callback to the mobile app */
echo $_GET['callback'] . "(" . json_encode(array(
    "fname" => $fname
)) . ");";

?>