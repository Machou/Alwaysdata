<?php

namespace BlizzardApi\Wow\GameData;

class ConnectedRealms extends GenericDataEndpoint {
    /**
     * Performs a search of connected realms
     * @param $search array Search parameters
     * @param $options array Request options
     * @return mixed
     */
    public function search(array $search = [], array $options = [])
    {
        return $this->apiSearchRequest("{$this->baseUrl('game_data')}/search/$this->endpoint", $search, $this->defaultOptions($options));
    }

    /**
     * Returns all non commodity auctions on a connected realm by ID
     * @param int $id the ID of the connected realm
     * @param array $options
     * @return mixed
     */
    public function getAuctions(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/$id/auctions", $this->defaultOptions($options));
    }

    /**
     * Returns an index of classic auction houses for a connected realm
     * @param int $connectedRealmId the ID of the connected realm
     * @param array $options
     * @return mixed
     */
    public function getClassicAuctionHouseIndex(int $connectedRealmId, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/$connectedRealmId/auctions/index", $this->defaultOptions($options));
    }

    /**
     * Returns all classic auctions for a specific auction house on a connected realm
     * @param int $connectedRealmId the ID of the connected realm
     * @param int $auctionHouseId int the ID of the auction house
     * @param array $options
     * @return mixed
     */
    public function getClassicAuctions(int $connectedRealmId, int $auctionHouseId, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/$connectedRealmId/auctions/$auctionHouseId", $this->defaultOptions($options));
    }


    protected function endpointSetup()
    {
        $this->namespace = DYNAMIC_NAMESPACE;
        $this->ttl = self::CACHE_MONTH;
        $this->endpoint = 'connected-realm';
    }
}