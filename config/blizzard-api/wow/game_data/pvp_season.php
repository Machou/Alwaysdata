<?php

namespace BlizzardApi\Wow\GameData;

use Error;

class PvpSeason extends GenericDataEndpoint
{
	/**
	 * Returns an index of PVP Leaderboards for a PVP Season
	 * @param $id integer The ID of the PvP season
	 * @param $options array Request options
	 * @return mixed
	 */
	public function leaderboards(int $id, array $options = [])
	{
		return $this->apiRequest("{$this->endpointUri()}/$id/pvp-leaderboard/index", $this->defaultOptions($options));
	}

	/**
	 * Returns a PvP leaderboard of a specific PvP bracket for a PvP season
	 * @param $id integer The ID of the PvP season
	 * @param $bracket string The PvP bracket type
	 * @return mixed
	 */
	const PVP_BRACKETS = ['2v2', '3v3', 'rbg'];

	public function leaderboard($id, $bracket, $options = [])
	{
		if(!in_array($bracket, self::PVP_BRACKETS)) {
			throw new Error('Invalid PVP Bracket');
		} else {
			return $this->apiRequest("{$this->endpointUri()}/$id/pvp-leaderboard/$bracket", $this->defaultOptions($options));
		}
	}

	/**
	 * Returns an index of PVP rewards for a PvP season.
	 * @param $id integer The ID of the PvP season
	 * @param $options array Request options
	 * @return mixed
	 */
	public function rewards(int $id, array $options = [])
	{
		return $this->apiRequest("{$this->endpointUri()}/$id/pvp-reward/index", $this->defaultOptions($options));
	}

	protected function endpointSetup()
	{
		$this->namespace = DYNAMIC_NAMESPACE;
		$this->ttl = self::CACHE_WEEK;
		$this->endpoint = 'pvp-season';
	}
}