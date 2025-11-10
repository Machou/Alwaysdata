<?php

namespace BlizzardApi\Wow\GameData;

class Title extends GenericDataEndpoint
{
    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'title';
    }
}