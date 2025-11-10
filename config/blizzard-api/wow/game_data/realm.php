<?php

namespace BlizzardApi\Wow\GameData;

class Realm extends GenericDataEndpoint
{
    /**
     * Returns a single realm by slug or ID
     * @param $realm mixed slug of the realm or realm ID
     * @param $options array Request options
     * @return mixed
     */
    public function get($realm, $options = [])
    {
        if(!is_numeric($realm)) {
            $realmBySlug = $this->createSlug($realm);
            return $this->apiRequest("{$this->baseUrl('game_data')}/realm/$realmBySlug", $this->defaultOptions($options));
        } else {
            return $this->apiRequest("{$this->baseUrl('game_data')}/realm/$realm", $this->defaultOptions($options));
        }
    }

    /**
     * Performs a search of realms
     * @param $search array Search parameters
     * @param $options array Request options
     * @return mixed
     */
    public function search(array $search = [], array $options = [])
    {
        return $this->apiSearchRequest("{$this->baseUrl('game_data')}/search/$this->endpoint", $search, $this->defaultOptions($options));
    }


    protected function endpointSetup()
    {
        $this->namespace = DYNAMIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'realm';
    }
}