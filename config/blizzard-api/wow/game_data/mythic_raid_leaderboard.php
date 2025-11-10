<?php

namespace BlizzardApi\Wow\GameData;

use BlizzardApi\Wow\Request;
use Error;

class MythicRaidLeaderboard extends Request
{
    public function index()
    {
        throw new Error('The MythicRaidLeaderboard endpoint does not have an index method.');
    }

    /**
     * Returns the leaderboard for a given raid and faction
     * @param $raid string The raid for a leaderboard
     * @param $faction string Player faction (`alliance` or `horde`)
     * @param $options array Request options
     * @return mixed
     */
    public function get(string $raid, string $faction, array $options = [])
    {
        $raidBySlug = $this->createSlug($raid);
        $factionBySlug = $this->createSlug($faction);
        return $this->apiRequest("{$this->baseUrl('game_data')}/leaderboard/hall-of-fame/$raidBySlug/$factionBySlug", array_merge(['namespace' => DYNAMIC_NAMESPACE, 'ttl' => self::CACHE_DAY], $options));
    }
}