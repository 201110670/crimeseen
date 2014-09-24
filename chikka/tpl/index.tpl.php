<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Chikka</title>
	<style type="text/css">
		.success {
			color: green;
		}
		.error {
			color: red;
		}
	</style>
</head>
<body>
	<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>">
		<h1>Chikka Minute!</h1>
		<?php if ($is_post) : ?>
			<?php if ($is_send) : ?>
			<p class="success">Message successfully sent to 
			<?php echo $comma_sep; ?>
			</p>
			<?php else: ?>
				<?php foreach ($messenger->getErrors() as $key => $value) : ?>
				<p class="error"><?php echo $value; ?></p>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endif; ?>
		<p><input type="text" name="mobile_number" value="" placeholder="enter mobile number"> </p>
		<p><textarea name="message" placeholder="type here"></textarea></p>
		<p><button type ="submit" name='send'>Send</button></p>
	</form>
</body>

$mysql_host = "mysql16.000webhost.com";
$mysql_database = "a7002300_crime";
$mysql_user = "a7002300_crime";
$mysql_password = "lastrollo123";

crimeseen.uphero.com