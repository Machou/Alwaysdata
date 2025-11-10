<?php

namespace BlizzardApi\Wow\GameData;

class Auctions extends GenericDataEndpoint
{

	/**
	 * Returns Returns all active auctions for a connected realm
	 * @param $options array Request options
	 * @return mixed
	 */
	public function index($options = [])
	{
		return $this->apiRequest("{$this->endpointUri()}/commodities", $this->defaultOptions($options));
	}

	protected function endpointSetup($options = [])
	{
		$this->namespace = DYNAMIC_NAMESPACE;
		$this->ttl = self::CACHE_TRIMESTER;
		$this->endpoint = 'auctions';
	}
}