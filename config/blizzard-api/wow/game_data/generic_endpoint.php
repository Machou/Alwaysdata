<?php

namespace BlizzardApi\Wow\GameData;

use BlizzardApi\Cache\CacheInterface;
use BlizzardApi\Wow\Request;

abstract class GenericDataEndpoint extends Request
{
    /**
     * @var string Endpoint URI
     */
    protected $endpoint;

    /**
     * @var string Endpoint namespace
     */
    protected $namespace;

    /**
     * @var int Cache duration
     */
    protected $ttl;

    public function __construct($region, $accessToken = null, ?CacheInterface $cache = null)
    {
        parent::__construct($region, $accessToken, $cache);
        $this->endpointSetup();
    }

    protected abstract function endpointSetup();

    public function index($options = [])
    {
        $url = sprintf('%s/index', $this->endpointUri());
        return $this->apiRequest($url, $this->defaultOptions($options));
    }

    protected function endpointUri($variant = null): string
    {
        if($variant) {
            return sprintf('%s/%s-%s', $this->baseUrl('game_data'), $this->endpoint, $variant);
        } else {
            return sprintf('%s/%s', $this->baseUrl('game_data'), $this->endpoint);
        }
    }

    protected function defaultOptions($options = []): array
    {
        return array_merge(['namespace' => $this->namespace, 'ttl' => $this->ttl], $options);
    }

    public function get($id, $options = [])
    {
        $url = sprintf('%s/%d', $this->endpointUri(), $id);
        return $this->apiRequest($url, $this->defaultOptions($options));
    }
}