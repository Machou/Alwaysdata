<?php

namespace BlizzardApi\Wow\GameData;

use Error;

class Journal extends GenericDataEndpoint
{
    public function get($id, $options = [])
    {
        throw new Error('The Journal endpoint does not have a get method.');
    }

    public function index($options = [])
    {
        throw new Error('The Journal endpoint does not have an index method.');
    }

    /**
     * Returns an index of journal expansions
     * @param $options array Request options
     * @return mixed
     */
    public function expansions(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('expansion')}/index", $this->defaultOptions($options));
    }

    /**
     * Returns a journal expansion by ID
     * @param $id integer The ID of the journal expansion
     * @param $options array Request options
     * @return mixed
     */
    public function expansion(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('expansion')}/$id", $this->defaultOptions($options));
    }

    /**
     * Returns an index of journal encounters
     * @param $options array Request options
     * @return mixed
     */
    public function encounters(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('encounter')}/index", $this->defaultOptions($options));
    }

    /**
     * Returns a journal encounter by ID
     * @param $id integer The ID of the journal encounter
     * @param $options array Request options
     * @return mixed
     */
    public function encounter(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('encounter')}/$id", $this->defaultOptions($options));
    }

    /**
     * Performs a search of journal encounters
     * @param $search array Search parameters
     * @param $options array Request options
     * @return mixed
     */
    public function search(array $search = [], array $options = [])
    {
        return $this->apiSearchRequest("{$this->baseUrl('game_data')}/search/$this->endpoint-encounter", $search, $this->defaultOptions($options));
    }

    /**
     * Returns an index of journal instances
     * @param $options array Request options
     * @return mixed
     */
    public function instances(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('instance')}/index", $this->defaultOptions($options));
    }

    /**
     * Returns a journal instance
     * @param $id integer The ID of the journal instance
     * @param $options array Request options
     * @return mixed
     */
    public function instance(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('instance')}/$id", $this->defaultOptions($options));
    }

    /**
     * Returns media for journal instance by ID
     * @param $id int The ID of the journal instance
     * @param $options array Request options
     * @return mixed
     */
    public function media(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/journal-instance/$id", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'journal';
    }
}