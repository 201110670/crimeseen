## Chikka Wrappers

OOP Wrappers for Chikka's API (https://api.chikka.com/)

### Messenger

Text people in the Philippines for free!

#### Sample Usage
```
<?php
include "vendor/autoload.php";

use Champoyradoo\Chikka\Factory\MessengerFactory;

$messenger = MessengerFactory::build(
	{YOUR_SHORTCODE},
	{YOUR_CLIENT_ID},
	{YOUR_SECRET_KEY}
);

$is_send = $messenger
	->mobileNumber({MOBILE_NUMBER})
	->message({MESSAGE})
	->send({MESSAGE_ID});
	
if ($is_send) {
	echo "SENT!";
} else {
	print_r($messenger->getErrors());
}
```
#### Other notes
Messenger automatically formats mobile numbers before send.

#####Accepted Mobile Number Formats:
* 09.. (No Spaces)
* 0906 234 ... (Space Delimiters)
* 0906-234 ... (Dash Delimiters)
* +63... (No Spaces)
* 63... (No Plus)
* +63 906... (Space Delimiters)
* +63-906... (Dash Delimiters)

Also, send() validates an empty message id, an empty mobile number, an empty message or an invalid mobile number.

For more functions, see **src/Champoyradoo/Chikka/Messenger.php**
