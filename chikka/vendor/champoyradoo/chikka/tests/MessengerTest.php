<?php namespace Tests;

use Champoyradoo\Chikka\Messenger;
use Guzzle\Http\Client;
use Mockery;
use Guzzle\Http\Message\Response;

class MessengerTest extends TestCase
{
	protected $shortcode;
	protected $client_id;
	protected $secret_key;
	protected $http_client_mock;
	protected $messenger;
	
	public function setUp()
	{
		$this->shortcode = "dummy_shortcode";
		$this->client_id = "dummy_client_id";
		$this->secret_key = "dummy_secret_key";
		$this->http_client_mock = Mockery::mock(new Client());
		$this->messenger = new Messenger(
			$this->shortcode,
			$this->client_id,
			$this->secret_key,
			$this->http_client_mock
		);
	}
	
	public function tearDown()
	{
		unset($this->messenger);
		
		parent::tearDown();
	}
	
	public function testGetCredentials()
	{
		/* act */
		$result = $this->messenger->getCredentials();
		/* assert */
		$this->assertEquals(
			array(
				"shortcode" => $this->shortcode,
				"client_id" => $this->client_id,
				"secret_key" => $this->secret_key
			),
			$result
		);
	}
	
	public function testGetUrl()
	{
		/* act */
		$result = $this->messenger->getRequestUrl();
		/* assert */
		$this->assertEquals(
			"https://post.chikka.com/smsapi/request",
			$result
		);
	}
	
	public function testGetMessageType()
	{
		/* act */
		$result = $this->messenger->getMessageType();
		/* assert */
		$this->assertEquals(
			"SEND",
			$result
		);
	}
	
	public function testMobileNumber()
	{
		/* arrange */
		$mobile_number = "   dummy_mobile_number   ";
		/* act */
		$result = $this->messenger->mobileNumber($mobile_number);
		/* assert */
		$this->assertEquals(
			$this->messenger,
			$result
		);
		$this->assertEquals(
			"dummy_mobile_number",
			$this->messenger->getMobileNumber()
		);
	}
	
	public function testMessage()
	{
		/* arrange */
		$message = "  dummy_message ";
		/* act */
		$result = $this->messenger->message($message);
		/* assert */
		$this->assertEquals(
			$this->messenger,
			$result
		);
		$this->assertEquals(
			"dummy_message",
			$this->messenger->getMessage()
		);
	}
	
	public function testSendReturnsFalseEmptyMessageId()
	{
		/* act */
		$result = $this->messenger->send();
		/* assert */
		$this->assertFalse($result);
		
		$this->assertEquals(
			"Message ID is empty.",
			$this->messenger->getErrors()["empty_message_id"]
		);
	}
	
	public function testSendReturnsFalseEmptyFields()
	{
		/* arrange */
		$this->messenger->mobileNumber("  ");
		
		$message_id = uniqid();
		/* act */
		$result = $this->messenger->send($message_id);
		/* assert */
		$this->assertFalse($result);
		
		$errors = $this->messenger->getErrors();
		
		$this->assertEquals(
			"Mobile number is empty.",
			$errors["empty_mobile_number"]
		);
		$this->assertEquals(
			"Message is empty.",
			$errors["empty_message"]
		);
	}
	
	public function testIsValidMobileNumber()
	{
		/* assert */
		$this->assertFalse(
			$this->messenger->isValidMobileNumber("asdasd23243")
		);
		$this->assertFalse(
			$this->messenger->isValidMobileNumber("23390623352")
		);
		$this->assertFalse(
			$this->messenger->isValidMobileNumber("090623350772")
		);
		$this->assertTrue(
			$this->messenger->isValidMobileNumber("09062335077")
		);
		$this->assertFalse(
			$this->messenger->isValidMobileNumber("+63906233507")
		);
		$this->assertFalse(
			$this->messenger->isValidMobileNumber("+6390623350770")
		);
		$this->assertTrue(
			$this->messenger->isValidMobileNumber("639062335077")
		);
	}
	
	public function testFormatMobileNumber()
	{
		/* assert */
		$expected_result = "09062335077";
		
		$this->assertEquals(
			$expected_result,
			$this->messenger->formatMobileNumber("0906 233  5077")
		);
		$this->assertEquals(
			$expected_result,
			$this->messenger->formatMobileNumber("0906-233 - 5077")
		);
		$this->assertEquals(
			"639062335077",
			$this->messenger->formatMobileNumber("+639062335077")
		);
	}
	
	public function testSendFormatsMobileNumber()
	{
		/* arrange */
		$this->messenger
			->mobileNumber("0906 233 5077")
			->message("dummy_message");
			
		$message_id = uniqid();
		/* act */
		$result = $this->messenger->send($message_id);
		/* assert */
		$this->assertEquals(
			"09062335077",
			$this->messenger->getMobileNumber()
		);
	}
	
	public function testSendReturnsFalseInvalidMobileNumber()
	{
		/* arrange */
		$this->messenger
			->mobileNumber("invalid_mobile_number")
			->message("dummy_message");
			
		$message_id = uniqid();
		/* act */
		$result = $this->messenger->send($message_id);
		/* assert */
		$this->assertFalse($result);
		$this->assertEquals(
			"Mobile number is invalid.",
			$this->messenger->getErrors()["invalid_mobile_number"]
		);
	}
	
	public function testSendReturnsFalseInvalidResponseJsonString()
	{
		/* arrange */
		$mobile_number = "09062335077";
		$message = "dummy_message";
		
		$this->messenger
			->mobileNumber($mobile_number)
			->message($message);
		
		$message_id = uniqid();
		$credentials = $this->messenger->getCredentials();
		// Mock Guzzle\Http\Message\Response
		$response_mock = Mockery::mock('Guzzle\Http\Message\Response');
		
		$response_mock
			->shouldReceive("json")
			->once()
			->andReturn("invalid_json");
		// Mock Guzzle\Http\Message\EntityEnclosingRequest
		$entity_enclosing_request_mock = Mockery::mock('Guzzle\Http\Message\EntityEnclosingRequest');
		
		$entity_enclosing_request_mock
			->shouldReceive("send")
			->once()
			->andReturn($response_mock);
		// $this->http_client_mock->post()
		$this->http_client_mock
			->shouldReceive("post")
			->with(
				$this->messenger->getRequestUrl(),
				array(),
				array(
					"message_type"  => $this->messenger->getMessageType(),
					"mobile_number" => $mobile_number,
					"shortcode"     => $credentials["shortcode"],
					"message_id"    => $message_id,
					"message"       => $message,
					"client_id"     => $credentials["client_id"],
					"secret_key"    => $credentials["secret_key"]
				)
			)
			->once()
			->andReturn($entity_enclosing_request_mock);
		/* act */
		$result = $this->messenger->send($message_id);
		/* assert */
		$this->assertFalse($result);
		$this->assertEquals(
			"The response from {$this->messenger->getRequestUrl()} is not a valid JSON.",
			$this->messenger->getErrors()["invalid_json_response"]
		);
	}
	
	public function testSendReturnsFalseInvalidResponseJsonArray()
	{
		/* arrange */
		$mobile_number = "09062335077";
		$message = "dummy_message";
		
		$this->messenger
			->mobileNumber($mobile_number)
			->message($message);
		
		$message_id = uniqid();
		$credentials = $this->messenger->getCredentials();
		// Mock Guzzle\Http\Message\Response
		$response_mock = Mockery::mock('Guzzle\Http\Message\Response');
		
		$response_mock
			->shouldReceive("json")
			->once()
			->andReturn(array("key" => "value"));
		// Mock Guzzle\Http\Message\EntityEnclosingRequest
		$entity_enclosing_request_mock = Mockery::mock('Guzzle\Http\Message\EntityEnclosingRequest');
		
		$entity_enclosing_request_mock
			->shouldReceive("send")
			->once()
			->andReturn($response_mock);
		// $this->http_client_mock->post()
		$this->http_client_mock
			->shouldReceive("post")
			->with(
				$this->messenger->getRequestUrl(),
				array(),
				array(
					"message_type"  => $this->messenger->getMessageType(),
					"mobile_number" => $mobile_number,
					"shortcode"     => $credentials["shortcode"],
					"message_id"    => $message_id,
					"message"       => $message,
					"client_id"     => $credentials["client_id"],
					"secret_key"    => $credentials["secret_key"]
				)
			)
			->once()
			->andReturn($entity_enclosing_request_mock);
		/* act */
		$result = $this->messenger->send($message_id);
		/* assert */
		$this->assertFalse($result);
		$this->assertEquals(
			"The response from {$this->messenger->getRequestUrl()} is not a valid JSON.",
			$this->messenger->getErrors()["invalid_json_response"]
		);
	}
	
	public function testSendReturnsFalseInvalidChikkaServerError()
	{
		/* arrange */
		$mobile_number = "09062335077";
		$message = "dummy_message";
		
		$this->messenger
			->mobileNumber($mobile_number)
			->message($message);
		
		$message_id = uniqid();
		$credentials = $this->messenger->getCredentials();
		// Mock Guzzle\Http\Message\Response
		$response_mock = Mockery::mock('Guzzle\Http\Message\Response');
		$response_array = array(
			"status"  => "400",
			"message" => "BAD REQUEST"
		);
		
		$response_mock
			->shouldReceive("json")
			->once()
			->andReturn($response_array);
		// Mock Guzzle\Http\Message\EntityEnclosingRequest
		$entity_enclosing_request_mock = Mockery::mock('Guzzle\Http\Message\EntityEnclosingRequest');
		
		$entity_enclosing_request_mock
			->shouldReceive("send")
			->once()
			->andReturn($response_mock);
		// $this->http_client_mock->post()
		$this->http_client_mock
			->shouldReceive("post")
			->with(
				$this->messenger->getRequestUrl(),
				array(),
				array(
					"message_type"  => $this->messenger->getMessageType(),
					"mobile_number" => $mobile_number,
					"shortcode"     => $credentials["shortcode"],
					"message_id"    => $message_id,
					"message"       => $message,
					"client_id"     => $credentials["client_id"],
					"secret_key"    => $credentials["secret_key"]
				)
			)
			->once()
			->andReturn($entity_enclosing_request_mock);
		/* act */
		$result = $this->messenger->send($message_id);
		/* assert */
		$this->assertFalse($result);
		$this->assertEquals(
			"Chikka Server Error: {$response_array["status"]} {$response_array["message"]}",
			$this->messenger->getErrors()["chikka_server_error"]
		);
	}
	
	public function testSendReturnsFalseInvalidChikkaServerErrorWithDescription()
	{
		/* arrange */
		$mobile_number = "09062335077";
		$message = "dummy_message";
		
		$this->messenger
			->mobileNumber($mobile_number)
			->message($message);
		
		$message_id = uniqid();
		$credentials = $this->messenger->getCredentials();
		// Mock Guzzle\Http\Message\Response
		$response_mock = Mockery::mock('Guzzle\Http\Message\Response');
		$response_array = array(
			"status"  => "400",
			"message" => "BAD REQUEST",
			"description" => "Exceeded Daily Broadcast Limit"
		);
		
		$response_mock
			->shouldReceive("json")
			->once()
			->andReturn($response_array);
		// Mock Guzzle\Http\Message\EntityEnclosingRequest
		$entity_enclosing_request_mock = Mockery::mock('Guzzle\Http\Message\EntityEnclosingRequest');
		
		$entity_enclosing_request_mock
			->shouldReceive("send")
			->once()
			->andReturn($response_mock);
		// $this->http_client_mock->post()
		$this->http_client_mock
			->shouldReceive("post")
			->with(
				$this->messenger->getRequestUrl(),
				array(),
				array(
					"message_type"  => $this->messenger->getMessageType(),
					"mobile_number" => $mobile_number,
					"shortcode"     => $credentials["shortcode"],
					"message_id"    => $message_id,
					"message"       => $message,
					"client_id"     => $credentials["client_id"],
					"secret_key"    => $credentials["secret_key"]
				)
			)
			->once()
			->andReturn($entity_enclosing_request_mock);
		/* act */
		$result = $this->messenger->send($message_id);
		/* assert */
		$this->assertFalse($result);
		$this->assertEquals(
			"Chikka Server Error: {$response_array["status"]} {$response_array["message"]} {$response_array["description"]}",
			$this->messenger->getErrors()["chikka_server_error"]
		);
	}
	
	public function testSendReturnsTrue()
	{
		/* arrange */
		$mobile_number = "09062335077";
		$message = "dummy_message";
		
		$this->messenger
			->mobileNumber($mobile_number)
			->message($message);
		
		$message_id = uniqid();
		$credentials = $this->messenger->getCredentials();
		// Mock Guzzle\Http\Message\Response
		$response_mock = Mockery::mock('Guzzle\Http\Message\Response');
		$response_array = array(
			"status"  => "200",
			"message" => "ACCEPTED"
		);
		
		$response_mock
			->shouldReceive("json")
			->once()
			->andReturn($response_array);
		// Mock Guzzle\Http\Message\EntityEnclosingRequest
		$entity_enclosing_request_mock = Mockery::mock('Guzzle\Http\Message\EntityEnclosingRequest');
		
		$entity_enclosing_request_mock
			->shouldReceive("send")
			->once()
			->andReturn($response_mock);
		// $this->http_client_mock->post()
		$this->http_client_mock
			->shouldReceive("post")
			->with(
				$this->messenger->getRequestUrl(),
				array(),
				array(
					"message_type"  => $this->messenger->getMessageType(),
					"mobile_number" => $mobile_number,
					"shortcode"     => $credentials["shortcode"],
					"message_id"    => $message_id,
					"message"       => $message,
					"client_id"     => $credentials["client_id"],
					"secret_key"    => $credentials["secret_key"]
				)
			)
			->once()
			->andReturn($entity_enclosing_request_mock);
		/* act */
		$result = $this->messenger->send($message_id);
		/* assert */
		$this->assertTrue($result);
	}
}