<?php

namespace BlizzardApi;

class Config
{
	const VERSION = '1.0.1';

	public static $apiKey;
	public static $apiSecret;
	public static $redirectURI;

	public static $storeAccessTokenInSession = true;
	public static $accessTokenSessionKey = 'blizzard_api_access_token';
}