<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Super-simple, minimum abstraction Futuready Product API v1 wrapper
 * 
 * Requires curl (I know, right?)
 * This probably has more comments than code.
 * 
 * @author Agung Trilaksono Suwarto Putra <agungtrilaksonosp@gmail.com>
 * @version 1.0
 */
class Cometchat_library
{
	private $api_endpoint = URL_COMETCHAT_API;
	private $verify_ssl   = false;
	/**
	 * Create a new instance
	 */
	function __construct()
	{
		$this->ci =& get_instance();
	}
	/**
	 * Call an API method. Every request needs the API key, so that is added automatically -- you don't need to pass it in.
	 * @param  string $method The API method to call, e.g. 'lists/list'
	 * @param  array  $args   An array of arguments to pass to the method. Will be json-encoded for you.
	 * @return array          Associative array of json decoded API response.
	 */
	public function call($args=array(), $timeout = 600)
	{
		return $this->makeRequest($args, $timeout);
	}
	/**
	 * Performs the underlying HTTP request. Not very exciting
	 * @param  string $method The API method to be called
	 * @param  array  $args   Assoc array of parameters to be passed
	 * @return array          Assoc array of decoded response
	 */
	private function makeRequest($args=array(), $timeout = 600)
	{      
		$url = $this->api_endpoint;
		// print_r($url);exit;
		if (function_exists('curl_init') && function_exists('curl_setopt')){
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');       
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
		    $response = curl_exec($ch);

		    curl_close($ch);
		} else {
		    $response    = file_get_contents($url, null, stream_context_create(array(
			'http' => array(
			    'protocol_version' => 1.1,
			    'user_agent'       => 'PHP-MCAPI/2.0',
			    'method'           => 'POST',
			    'header'           => "Content-type: application/json\r\n".
						  "Connection: close\r\n" .
						  "Content-length: " . http_build_query($args) . "\r\n",
			    'content'          => http_build_query($args),
			),
		    )));
		}
        return json_decode(trim($response), TRUE);

	}
}
