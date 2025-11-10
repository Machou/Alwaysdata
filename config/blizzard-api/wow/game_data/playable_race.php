<?php

namespace BlizzardApi\Wow\GameData;

class PlayableRace extends GenericDataEndpoint
{
    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'playable-race';
    }
}