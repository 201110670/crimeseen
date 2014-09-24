<?php
/** save codes by loading database module */
include_once("config.php");

/** these headers allow sending data from mobile */
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");

/** variables for different data received */
$name     = ucwords($_GET["username"]);
$address  = $_GET["street"];
$lat  = $_GET["lat"];
$lng     = $_GET["lng"];
$type  = $_GET["crimetype"];
$description  = $_GET["description"];
$remarks  = $_GET["remarks"];
$datetime  = $_GET["datetime"];

/** sql statements go here */
$sql = "INSERT INTO markers(name,address,lat,lng,type,description,remarks,datetime)
values('" . $name . "','" . $address . "','" . $lat . "','" . $lng . "','" . $type . "','" . $description . "','" . $remarks . "','" . $datetime . "')";
mysql_query($sql);

/** return a callback to the mobile app */
echo $_GET['callback'] . "(" . json_encode(array(
    "fname" => $fname
)) . ");";

?>