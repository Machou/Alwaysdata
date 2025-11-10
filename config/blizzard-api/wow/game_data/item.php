<?php

namespace BlizzardApi\Wow\GameData;

use Error;

class Item extends GenericDataEndpoint
{
    public function index($options = [])
    {
        throw new Error('The Item endpoint does not have an index method.');
    }

    /**
     * Returns an index of item classes
     * @param $options array Request options
     * @return mixed
     */
    public function classes(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('class')}/index", $this->defaultOptions($options));
    }

    /**
     * Returns an item class by ID
     * @param $id int The ID of the item class
     * @param $options array Request options
     * @return mixed
     */
    public function class(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('class')}/$id", $this->defaultOptions($options));
    }

    /**
     * Returns an index of item sets
     * @param $options array Request options
     * @return mixed
     */
    public function sets(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('set')}/index", $this->defaultOptions($options));
    }

    /**
     * Returns an item set by ID
     * @param $id int The ID of the item set
     * @param $options array Request options
     * @return mixed
     */
    public function set(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('set')}/$id", $this->defaultOptions($options));
    }

    /**
     * Returns an item subclass by ID
     * @param $id int The ID of the item class
     * @param $subClassID int The ID of the item subclass
     * @param $options array Request options
     * @return mixed
     */
    public function subClass(int $id, int $subClassID, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('class')}/$id/item-subclass/$subClassID", $this->defaultOptions($options));
    }

    /**
     * Returns media for an Item by ID
     * @param $id int The ID of the item
     * @param $options array Request options
     * @return mixed
     */
    public function media(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/$this->endpoint/$id", $this->defaultOptions($options));
    }

    /**
     * Performs a search of items
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
        $this->endpoint = 'item';
    }
}