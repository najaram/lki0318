<?php

namespace LkiFeedApi;

class LkiFeedApi
{
	/**
	 * The client id
	 * 
	 * @var string
	 */
	protected $clientId;

	/**
	 * The client secret key
	 * 
	 * @var string
	 */
	protected $clientSecret;

	/**
	 * Redirect uri
	 * 
	 * @var string
	 */
	protected $redirectUri;

	/**
	 * State
	 * 
	 * @var string
	 */
	protected $state;

	/**
	 * Authentication url
	 * 
	 * @var string
	 */
	protected $authUrl = 'https://www.linkedin.com/oauth/v2';

	/**
	 * Stores access token
	 * 
	 * @var array
	 */
	protected $data = [];

	/**
	 * Constructor
	 * 
	 * @param string $clientId
	 * @param string $clientSecret
	 */
	public function __construct($clientId, $clientSecret, $redirectUri)
	{
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->redirectUri = $redirectUri;
	}

	public function echoAuthLink()
	{
		$this->state = $this->generateString();
		$url = $this->authUrl.'/authorization?response_type=code&client_id='.$this->clientId.'&redirect_uri='.$this->redirectUri.'&state='.$this->state;

		echo '<a href="'.$url.'">Request Access</a>';
	}

	/**
	 * Get the access token
	 * 
	 * @param  string $code 
	 * @return array $data
	 */
	public function getToken($code)
	{

		$body = [
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => $this->redirectUri,
			'client_id' => $this->clientId,
			'client_secret' => $this->clientSecret,
		];

		$url = $this->authUrl.'/accessToken';
		$accessToken = wp_remote_post($url, [
			'method' => 'POST',
			'body' => $body
		]);

		$this->data = json_decode($accessToken['body'], true);

		if (is_wp_error($this->data)) {
			throw new \Exception($this->data->get_error_message());
		}

		return $this->data;

	}

	public function getProfileInfo($url, $token)
	{
		$args = [
			'headers' => [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $token['access_token']
			]
		];

		$response = wp_remote_get($url, $args);

		if (is_wp_error($response)) {
			throw new \Exception($response->get_error_message());
		}

		$data = wp_remote_retrieve_body($response);
		$data = json_decode($data, true);

		return $data;
	}

	/**
	 * Generates a string used for state
	 * 
	 * @return string
	 */
	protected function generateString()
	{
		return base64_encode(uniqid());
	}
}