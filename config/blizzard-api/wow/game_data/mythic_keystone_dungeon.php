<?php

namespace BlizzardApi\Wow\GameData;

use Error;

class MythicKeystoneDungeon extends GenericDataEndpoint
{
    public function get($id, $options = [])
    {
        throw new Error('The MythicKeystoneDungeon endpoint does not have a get method.');
    }

    /**
     * Returns an index of Mythic Keystone dungeons
     * @param $options array Request options
     * @return mixed
     */
    public function dungeons(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/dungeon/index", $this->defaultOptions($options));
    }

    /**
     * Returns a Mythic Keystone dungeon by ID
     * @param $id integer The ID of the dungeon
     * @param $options array Request options
     * @return mixed
     */
    public function dungeon(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/dungeon/$id", $this->defaultOptions($options));
    }

    /**
     * Returns an index of Mythic Keystone periods
     * @param $options array Request options
     * @return mixed
     */
    public function periods(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/period/index", $this->defaultOptions($options));
    }

    /**
     * Returns a Mythic Keystone period by ID
     * @param $id integer The ID of the Mythic Keystone season period
     * @param $options array Request options
     * @return mixed
     */
    public function period(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/period/$id", $this->defaultOptions($options));
    }

    /**
     * Returns an index of Mythic Keystone seasons
     * @param $options array Request options
     * @return mixed
     */
    public function seasons(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/season/index", $this->defaultOptions($options));
    }

    /**
     * Returns a Mythic Keystone season by ID
     * @param $id integer The ID of the Mythic Keystone season
     * @param $options array Request options
     * @return mixed
     */
    public function season(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/season/$id", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = DYNAMIC_NAMESPACE;
        $this->ttl = self::CACHE_WEEK;
        $this->endpoint = 'mythic-keystone';
    }
}