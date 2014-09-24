<?php
/** save codes by loading database module */
include_once("config.php");

/** these headers allow sending data from mobile */
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");

/** variables for different data received */
$username = $_GET["username"];

$usernamecode = "";

/**checks if username already exists */
$sql    = "SELECT email FROM users WHERE username = '" . $username . "'";
$select = mysql_query($sql);
if (mysql_num_rows($select) > 0) {
    $usernamecode = "exists";
} else {
    $usernamecode = "notexists";
}
/** return a callback to the mobile app */
echo $_GET['callback'] . "(" . json_encode(array(
    "username" => $username,
    "uexists" => $usernamecode
)) . ");";
?>