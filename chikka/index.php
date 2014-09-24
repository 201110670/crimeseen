<?php
error_reporting(E_ALL ^ E_DEPRECATED);
$con=mysql_connect("localhost","root","");
	mysql_select_db("aaa");
include "vendor/autoload.php";

use Champoyradoo\Chikka\Factory\MessengerFactory;

$is_post = ($_SERVER["REQUEST_METHOD"] == "GET");



if ($is_post) {
	$messenger = MessengerFactory::build(
		"2929088888",
		"6ec9c69f01b3e4dceaef421c4165333c16379e0a7b63ab5a15cb405bb57316ff",
		"16dc740ecb04243f40117cf6e7294977b6c3771c4563dfbaada625d42b3f5636"
	);
	
		$is_send = $messenger->mobileNumber($_GET['number'])
		->message($_GET["message"])
		->send(uniqid());
}	



$comma_sep = implode(",",$contact);

