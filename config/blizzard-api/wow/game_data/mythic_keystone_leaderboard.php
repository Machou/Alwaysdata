<?php

namespace BlizzardApi\Wow\GameData;

use BlizzardApi\Wow\Request;

class MythicKeystoneLeaderboard extends Request
{
    /**
     * Returns an index of Mythic Keystone Leaderboard dungeon instances for a connected realm
     * @param $id int The ID of the connected realm
     * @param $options array Request options
     * @return mixed
     * @throws
     */
    public function index(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('game_data')}/connected-realm/$id/mythic-leaderboard/index", array_merge(['namespace' => DYNAMIC_NAMESPACE, 'ttl' => self::CACHE_DAY], $options));
    }

    /**
     * Returns a weekly Mythic Keystone Leaderboard by period
     * @param $connectedRealm int The ID of the connected realm
     * @param $dungeon int The ID of the dungeon
     * @param $period int The unique identifier for the leaderboard period
     * @param $options array Request options
     * @return mixed
     * @throws
     */
    public function get(int $connectedRealm, int $dungeon, int $period, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('game_data')}/connected-realm/$connectedRealm/mythic-leaderboard/$dungeon/period/$period", array_merge(['namespace' => DYNAMIC_NAMESPACE, 'ttl' => self::CACHE_DAY], $options));
    }
}