<?php

namespace BlizzardApi\Wow\GameData;

class AzeriteEssence extends GenericDataEndpoint
{
    public function media($id, $options = [])
    {
        /**
         * Returns media for an azerite essence by ID
         * @param $id int The ID of the azerite essence
         * @param $options array Request options
         * @return mixed
         */
        return $this->apiRequest("{$this->baseUrl('media')}/azerite-essence/$id", $this->defaultOptions($options));
    }

    /**
     * Performs a search of azerite essences
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
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'azerite-essence';
    }
}