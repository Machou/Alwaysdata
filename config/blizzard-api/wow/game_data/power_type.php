<?php

namespace BlizzardApi\Wow\GameData;

class PowerType extends GenericDataEndpoint
{
    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'power-type';
    }
}