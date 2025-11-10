<?php

namespace BlizzardApi\Wow\GameData;

class Region extends GenericDataEndpoint
{

    protected function endpointSetup()
    {
        $this->namespace = DYNAMIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'region';
    }
}