<?php

namespace BlizzardApi\Wow\GameData;

class Mount extends GenericDataEndpoint
{
    /**
     * Performs a search of mounts
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
        $this->endpoint = 'mount';
    }
}