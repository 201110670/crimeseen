<?php
namespace Champoyradoo\Chikka;

use Guzzle\Http\Exception\ClientErrorResponseException;

class Messenger
{
	protected $credentials = array();
	protected $http_client;
	protected $request_url = "https://post.chikka.com/smsapi/request";
	protected $message_type = "SEND";
	protected $mobile_number;
	protected $message;
	protected $errros = array();
	
	public function __construct($shortcode, $client_id, $secret_key, $http_client)
	{
		$this->credentials["shortcode"] = $shortcode;
		$this->credentials["client_id"] = $client_id;
		$this->credentials["secret_key"] = $secret_key;
		$this->http_client = $http_client;
	}
	
	public function getCredentials()
	{
		return $this->credentials;
	}
	
	public function getRequestUrl()
	{
		return $this->request_url;
	}
	
	public function getMessageType()
	{
		return $this->message_type;
	}
	
	public function mobileNumber($mobile_number)
	{
		$this->mobile_number = trim($mobile_number);
		
		return $this;
	}
	
	public function getMobileNumber()
	{
		return $this->mobile_number;
	}
	
	public function message($message)
	{
		$this->message = trim($message);
		
		return $this;
	}
	
	public function getMessage()
	{
		return $this->message;
	}
	
	public function send($message_id = null)
	{
		if (! $message_id) {
			$this->addError("empty_message_id", "Message ID is empty.");
			
			return false;
		}
	
		if (! $this->mobile_number) {
			$this->addError("empty_mobile_number", "Mobile number is empty.");
		}
		
		if (! $this->message) {
			$this->addError("empty_message", "Message is empty.");
		}
		
		if ($this->hasErrors()) {
			return false;
		}
		
		$this->mobile_number = $this->formatMobileNumber($this->mobile_number);
		
		if (! $this->isValidMobileNumber($this->mobile_number)) {
			$this->addError("invalid_mobile_number", "Mobile number is invalid.");
			
			return false;
		}
		
		try {
			$response = $this->http_client->post(
				$this->request_url,
				array(),
				array(
					"message_type"  => $this->message_type,
					"mobile_number" => $this->mobile_number,
					"shortcode"     => $this->credentials["shortcode"],
					"message_id"    => $message_id,
					"message"       => $this->message,
					"client_id"     => $this->credentials["client_id"],
					"secret_key"    => $this->credentials["secret_key"]
				)
			)->send();
		} catch (ClientErrorResponseException $e) {
			$response = $e->getResponse();
		}
		
		$json_response = $response->json();

		if (! is_array($json_response) || empty($json_response["status"])) {
			$this->addError(
				"invalid_json_response",
				"The response from {$this->request_url} is not a valid JSON."
			);
			
			return false;
		}
		
		if ($json_response["status"] != "200") {
			$description = (isset($json_response["description"])) 
				? " {$json_response["description"]}" 
				: "";
		
			$this->addError(
				"chikka_server_error",
				"Chikka Server Error: {$json_response["status"]} {$json_response["message"]}{$description}"
			);
			
			return false;
		}
	
		return true;
	}
	
	protected function addError($key, $value)
	{
		$this->errors[$key] = $value;
	}
	
	protected function hasErrors()
	{
		return (! empty($this->errors));
	}
	
	public function getErrors()
	{
		return $this->errors;
	}
	
	public function isValidMobileNumber($mobile_number)
	{
		return (boolean) preg_match(
			"/^09[0-9]{9}$|^63[0-9]{10}$/",
			$mobile_number
		);
	}
	
	public function formatMobileNumber($mobile_number)
	{
		return str_replace(
			array(" ", "-", "+"),
			"",
			$mobile_number
		);
	}
}