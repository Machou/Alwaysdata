<?php

namespace BlizzardApi\Wow\GameData;

class PvpTier extends GenericDataEndpoint
{
    /**
     * Returns media for a PvP tier by ID
     * @param $id integer The ID of the PvP tier
     * @param $options array Request options
     * @return mixed
     */
    public function media(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/pvp-tier/$id", $this->defaultOptions($options));
    }


    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'pvp-tier';
    }

}