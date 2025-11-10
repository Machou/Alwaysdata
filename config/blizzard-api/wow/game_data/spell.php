<?php

namespace BlizzardApi\Wow\GameData;

use Error;

class Spell extends GenericDataEndpoint
{
    public function index($options = [])
    {
        throw new Error("Spell endpoint doesn't have a index method.");
    }

    /**
     * Performs a search of spells
     * @param $search array Search parameters
     * @param $options array Request options
     * @return mixed
     */
    public function search(array $search = [], array $options = [])
    {
        return $this->apiSearchRequest("{$this->baseUrl('game_data')}/search/$this->endpoint", $search, $this->defaultOptions($options));
    }

    /**
     * Returns media for a spell by ID
     * @param $id int The ID of the spell
     * @param $options array Request options
     * @return mixed
     */
    public function media(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/spell/$id", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'spell';
    }
}