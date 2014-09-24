<?php namespace Champoyradoo\Chikka\Factory;

use Champoyradoo\Chikka\Messenger;
use Guzzle\Http\Client;

class MessengerFactory
{
	public static function build($shortcode, $client_id, $secret_key)
	{
		return new Messenger(
			$shortcode,
			$client_id,
			$secret_key,
			new Client()
		);
	}
}