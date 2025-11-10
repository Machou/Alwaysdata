<?php

namespace BlizzardApi\Wow\GameData;

use Error;

class Media extends GenericDataEndpoint
{
    public function get($id, $options = [])
    {
        throw new Error('The Media endpoint does not have a get method.');
    }

    public function index($options = [])
    {
        throw new Error('The Media endpoint does not have an index method.');
    }

    /**
     * Performs a search of all types of media documents
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
        $this->endpoint = 'media';
    }
}