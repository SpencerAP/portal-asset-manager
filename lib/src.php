<?php

function v4Post($apiKey, $endpoint, $data = null, $token = null)
{
	$data = $data ?? [];
	$baseUrl = PS_URL_BASE . 'psapi/v4.0/';
	$url = $baseUrl . $endpoint;
	$headers = [
		'Accept: application/json',
		'X-PS-Api-Key: ' . $apiKey,
	];

	return apiCall('POST', $url, $data, $headers, $token);
}

/**
 * Makes generic API calls with Curl.
 *
 * @param string $method GET || POST
 * @param string $url Full URL to call
 * @param array $data Associate array of form data for POST requests
 * @param array $headers List of request headers
 * @param string $token Bearer token
 *
 * @return array Decoded response data
 */
function apiCall($method, $url, $data, $headers, $token = null)
{
	if (!empty($token)) {
		$headers[] = 'Authorization: Bearer ' . $token;
	}

	$ch = curl_init();

	switch ($method) {
	case 'POST':
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		break;
	case 'GET':
	default:
		// curl defaults to GET
		break;
	}

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	$responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
	$curlError = curl_error($ch);

	if ($responseCode !== 200 || $curlError) {
		// there are many things we might potentially want to see
		// when debugging curl API errors
		$errMsg = sprintf(
			'CODE: %s; ERROR: %s; RETURN: %s; URL: %s',
			strval($responseCode),
			strval($curlError),
			strval($response),
			$url
		);
		throw new Exception($errMsg);
	}

	$response = $response ?? '{}';
	$response = json_decode($response, true);

	return $response;
}

/**
 * Initiates the authorization step of OAuth:
 *	- Validates user input
 *	- performs the redirect to the delegation form
 *
 * @return null
 */
function authorize()
{
	$params = [
		'response_type' => 'code',
		'client_id' => OAUTH_CLIENT_ID,
		'state' => 'pam',
		'redirect_uri' => SCRIPT_URI,
		'api_key' => V4_API_KEY,
	];
	$queryString = http_build_query($params);

	header('Location: ' . PS_URL_BASE . 'psapi/v4.0/oauth/authorize?' . $queryString);
}

function getBearerToken($code, $state)
{
	// exchange code for token
	$data = [
		'code' => $code,
		'grant_type' => 'authorization_code',
		'redirect_uri' => SCRIPT_URI,
		'client_id' => OAUTH_CLIENT_ID,
		'client_secret' => OAUTH_CLIENT_SECRET,
	];

	$response = v4Post(V4_API_KEY, 'oauth/token', $data);
	$accessToken = $response['access_token'];
	// $refreshToken = $response['refresh_token'];

	return $accessToken;
}
