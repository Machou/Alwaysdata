<?php

namespace BlizzardApi\Wow\GameData;

use Error;

class WowToken extends GenericDataEndpoint
{
    public function get($id, $options = [])
    {
        throw new Error('The WowToken endpoint does not have a get method.');
    }

    protected function endpointSetup()
    {
        $this->namespace = DYNAMIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'token';
    }
}