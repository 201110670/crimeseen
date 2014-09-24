<?php namespace Tests\Factory;

use Tests\TestCase;
use Champoyradoo\Chikka\Factory\MessengerFactory;
use Champoyradoo\Chikka\Messenger;
use Guzzle\Http\Client;

class MessengerFactoryTest extends TestCase
{
	public function testBuild()
	{
		/* arrange */
		$shortcode = "dummy_shortcode";
		$client_id = "dummy_client_id";
		$secret_key = "dummy_secret_key";
		/* act */
		$result = MessengerFactory::build($shortcode, $client_id, $secret_key);
		/* assert */
		$this->assertEquals(
			new Messenger(
				$shortcode,
				$client_id,
				$secret_key,
				new Client()
			),
			$result
		);
	}
}