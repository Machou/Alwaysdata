<?php

namespace BlizzardApi;

define('REGION_US', 'us');
define('REGION_EU', 'eu');
define('REGION_KO', 'ko');
define('REGION_TW', 'tw');

define('STATIC_NAMESPACE', 'static');
define('DYNAMIC_NAMESPACE', 'dynamic');
define('PROFILE_NAMESPACE', 'profile');

use BlizzardApi\Cache\CacheInterface;
use Exception;
use stdClass;

class ApiException extends Exception { }

class Request
{
	const CACHE_MINUTE = 60;
	const CACHE_HOUR = 60 * self::CACHE_MINUTE;
	const CACHE_DAY = 24 * self::CACHE_HOUR;
	const CACHE_WEEK = self::CACHE_DAY * 7;
	const CACHE_MONTH = self::CACHE_DAY * 30;
	const CACHE_TRIMESTER = self::CACHE_MONTH * 3;

	const BASE_URLS = [
		'game_data' => 'https://%s.api.blizzard.com/data/%s',
		'community' => 'https://%s.api.blizzard.com/%s',
		'profile' => 'https://%s.api.blizzard.com/profile/%s',
		'media' => 'https://%s.api.blizzard.com/data/%s/media',
		'oauth/auth' => 'https://%s.battle.net/oauth/authorize',
		'oauth/token' => 'https://%s.battle.net/oauth/token'
	];

	/**
	 * @var $accessToken string Cached access token.
	 */
	public $accessToken;

	/**
	 * @var $region string API region
	 */
	protected $region;

	/**
	 * @var $game string Game name
	 */
	protected $game;

	/**
	 * @var $cache CacheInterface
	 */
	protected $cache;

	/**
	 * Creates an interface for calling API Endpoints
	 * @param $region string One of the supported API regions: REGION_US, REGION_EU, REGION_KO or REGION_TW
	 * @param null $accessToken Allow to specify an access_token for the requests, useful for specifying a token obtained
	 * using authorization_code flow or a custom cache.
	 * @param CacheInterface|null $cache CacheInterface Cache interface
	 * @throws ApiException In case a token cannot be obtained.
	 */
	public function __construct($region, $accessToken = null, ?CacheInterface $cache = null)
	{
		$this->region = $region;
		$this->cache = $cache;

		if($accessToken) {
			# Using an externally created token
			$this->accessToken = $accessToken;
		} elseif(Config::$storeAccessTokenInSession && isset($_SESSION[Config::$accessTokenSessionKey])) {
			# Checking for a token in the session
			$this->accessToken = $_SESSION[Config::$accessTokenSessionKey];
		} else {
		# Creating a new client_credentials token
			$this->accessToken = $this->createAccessToken();
		}
	}

	/**
	 * Used to create a new access token using the OAuth2
	 * Api client and secret must be configured using the BlizzardAPi\Config class.
	 *
	 * If the argument $code is provided the authorization code flow will be used:
	 * @see https://develop.battle.net/documentation/guides/using-oauth/authorization-code-flow
	 *
	 * If no $code argument is provided an access token will be created using the client credentials flow
	 * @see https://develop.battle.net/documentation/guides/using-oauth/client-credentials-flow
	 *
	 * @param string|null $code An optional authorization code
	 * @return string
	 * @throws ApiException
	 */
	public function createAccessToken($code = null) {
		$ch = curl_init('https://oauth.battle.net/token');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, Config::$apiKey.':'.Config::$apiSecret);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
		$reponse = curl_exec($ch);

		if(curl_errno($ch))
		{
			curl_close($ch);

			throw new Exception('Erreur cURL : '.curl_error($ch));
		}

		curl_close($ch);

		return !empty($reponse) ? json_decode($reponse)->access_token : null;
	}

	/**
	 * @param $scope string API scope to apply the base URL
	 * @return mixed Base URL to call endpoints
	 */
	protected function baseUrl($scope) {
		return sprintf(self::BASE_URLS[$scope], $this->region, $this->game);
	}

	/**
	 * Creates a valid url for authorizing a user using BNet OAuth2 provider for the current region.
	 * @see https://develop.battle.net/documentation/guides/using-oauth/authorization-code-flow
	 *
	 * @param string $scope The desired OAuth2 scope.
	 * @return string The url for the login button.
	 */
	public function authorizationURL($scope = 'wow.profile') {
		$queryString = http_build_query([
			'auth_flow' => 'auth_code',
			'client_id' => Config::$apiKey,
			'scope' => $scope,
			'response_type' => 'code',
			'redirect_uri' => Config::$redirectURI
		]);
		return sprintf("%s?%s", $this->baseUrl('oauth/auth'), $queryString);
	}

	protected function prepareURL($url, $options) {
		$queryString = $this->extractQueryString($options);
		if($queryString) {
			$url .= "?$queryString";
		}
		return $url;
	}

	/**
	 * @param $url string The endpoint url
	 * @param array $options array An array containing options for a single request
	 * @return mixed
	 */
	protected function apiRequest($url, $options = []) {
		$url = $this->prepareURL($url, $options);
		if($this->cache && $this->cache->retrieve($url, $data)) {
			return json_decode($data);
		}
		$data = $this->execute($url, $responseCode);
		if($this->cache && $responseCode === 200) {
			$ttl = isset($options['ttl']) ? $options['ttl'] : 86400;
			$this->cache->store($url, $data, $ttl);
		}
		return json_decode($data);
	}

	/**
	 * @param $url string The endpoint url
	 * @param array $search array An array containing search parameters for a single request
	 * @param array $options array An array containing options for a single request
	 * @return mixed
	 */
	protected function apiSearchRequest($url, $search = [], $options = []) {
		$url = $this->prepareURL($url, $options);
		$url .= "&" . implode("", $search);
		if($this->cache && $this->cache->retrieve($url, $data)) {
			return json_decode($data);
		}
		return $this->genericApiRequest($url, $responseCode, $options, $data);
	}

	/**
	 * @param $options array An array containing options for a single request
	 * @return string The query string params for this request
	 */
	private function extractQueryString(&$options) {
		$defaultOptions = ['ttl' => 86400, 'region' => $this->region, $accessToken = $this->accessToken, 'namespace' => 'static', 'classic' => false];
		$queryString = array_diff_key($options, $defaultOptions);
		if(array_key_exists('namespace', $options)) {
			$queryString['namespace'] = $this->endpointNamespace($options);
		}
		$options = array_intersect_key($options, $defaultOptions);
		return http_build_query($queryString);
	}

	/**
	 * @param $options array An array containing options for a single request
	 * @return string The appropriate namespace for the endpoint and region
	 */
	protected function endpointNamespace($options) {
		$namespace = $options['namespace'];
		switch ($namespace) {
		case 'static':
		case 'dynamic':
			return isset($options['classic']) && $options['classic'] ? "$namespace-classic-$this->region" : "$namespace-$this->region";
		case 'profile':
			return "$namespace-$this->region";
		default:
			return '';
		}
	}

	/**
	 * @param $url string API endpoint full url with querystring params
	 * @param $responseStatus int Response status
	 * @return mixed stdClass JSON object response
	 */
	public function execute($url, &$responseStatus)
	{
		try {
			$curl_handle = curl_init();
			try {
				curl_setopt($curl_handle, CURLOPT_URL, $url);
				curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl_handle, CURLOPT_HTTPHEADER, ["Authorization: Bearer $this->accessToken"]);
				$response = curl_exec($curl_handle);
				$responseStatus = (int)curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
			} catch (Exception $e) {
				$responseStatus = 0;
				$response = null;
			} finally {
				curl_close($curl_handle);
			}
		} finally {
			return $response;
		}
	}

	/**
	 * @param $urls array An array of multiple complete urls
	 * @param $responseStatuses array An array of multiple response statuses
	 * @param $options array An array containing options for a single request
	 * @return array|stdClass|null An array of JSON responses or null in case of failure
	 */
	public function bulkExecute($urls, &$responseStatuses, $options = [])
	{
		try {
			$curl_multi_handle = curl_multi_init();
			try {
				$curl_handles = [];
				$responses = new stdClass();
				foreach($urls as $key => $url) {
					$curl_handle = curl_init();
					curl_setopt($curl_handle, CURLOPT_URL, $this->prepareURL($url, $options));
					curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl_handle, CURLOPT_HTTPHEADER, ["Authorization: Bearer $this->accessToken"]);

					curl_multi_add_handle($curl_multi_handle, $curl_handle);
					$curl_handles[$key] = $curl_handle;
				}

				$running = true;
				do {
					$status = curl_multi_exec($curl_multi_handle, $running);
					if($running) {
						curl_multi_select($curl_multi_handle);
					}
				} while ($running && $status == CURLM_OK);
			} catch (Exception $e) {
				$responseStatuses = null;
				$responses = null;
			} finally {
				foreach($curl_handles as $key => $handle) {
				$responseStatuses[] = curl_getinfo($handle, CURLINFO_HTTP_CODE);
				$responses->$key = json_decode(curl_multi_getcontent($handle));
				curl_close($handle);
				}
			}
		} finally {
			curl_multi_close($curl_multi_handle);
			return $responses;
		}
	}

	protected function createSlug($name)
	{
		$name = mb_strtolower($name);
		$name = str_replace(' ', '-', $name);
		$name = str_replace('\'', '', $name);
		return rawurlencode($name);
	}

	/**
	 * @param $url string The endpoint url
	 * @param array $options array An array containing options for a single request
	 * @param $responseCode int response status
	 * @param $data
	 * @return mixed
	 */
	protected function genericApiRequest(string $url, &$responseCode, array $options, &$data)
	{
		$data = $this->execute($url, $responseCode);
		if($this->cache && $responseCode === 200) {
			$ttl = isset($options['ttl']) ? $options['ttl'] : 86400;
			$this->cache->store($url, $data, $ttl);
		}

		return json_decode($data);
	}
}