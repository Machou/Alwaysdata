<?php

namespace BlizzardApi\Wow\GameData;

use Error;

class Reputations extends GenericDataEndpoint
{
    public function get($id, $options = [])
    {
        throw new Error('The Reputations endpoint does not have a get method.');
    }

    public function index($options = [])
    {
        throw new Error('The Reputations endpoint does not have an index method.');
    }

    /**
     * Returns an index of reputation factions
     * @param $options array Request options
     * @return mixed
     */
    public function factions(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('faction')}/index", $this->defaultOptions($options));
    }

    /**
     * Returns a single reputation faction by ID
     * @param $id integer The ID of the reputation faction
     * @param $options array Request options
     * @return mixed
     */
    public function faction(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('faction')}/$id", $this->defaultOptions($options));
    }

    /**
     * Returns an index of reputation tiers
     * @param $options array Request options
     * @return mixed
     */
    public function tiersIndex(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('tiers')}/index", $this->defaultOptions($options));
    }

    /**
     * Returns a single set of reputation tiers by ID
     * @param $id integer The ID of the set of reputation tiers
     * @param $options array Request options
     * @return mixed
     */
    public function tiers(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('tiers')}/$id", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'reputation';
    }
}