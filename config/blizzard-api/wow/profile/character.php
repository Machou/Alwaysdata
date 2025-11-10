<?php

namespace BlizzardApi\Wow\Profile;

use BlizzardApi\Wow\Request;
use Error;

class Character extends Request
{
	public function index()
	{
		throw new Error('The Character endpoint does not have an index method.');
	}

	/**
	 * Return information about a wow character
	 * @param $realmName string Realm name
	 * @param $characterName string Character name
	 * @param $options array Request options
	 * @return mixed
	 */
	public function get(string $realmName, string $characterName, array $options = [])
	{
		return $this->character_request($realmName, $characterName, null, $options);
	}

	private function character_url($realmName, $characterName, $variant = null): string
	{
		$realmName = $this->createSlug($realmName);
		$characterName = mb_strtolower($this->createSlug($characterName));
		$url = "{$this->baseUrl('profile')}/character/$realmName/$characterName";
		if($variant) {
			$url .= "/$variant";
		}
		return $url;
	}

	private function character_request($realmName, $characterName, $variant = null, $options = [])
	{
		$url = $this->character_url($realmName, $characterName, $variant);
		return $this->apiRequest($url, array_merge($options, ['namespace' => PROFILE_NAMESPACE]));
	}

	public function achievements($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'achievements', $options);
	}

	public function achievementsStatistics($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'achievements/statistics', $options);
	}

	public function appearance($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'appearance', $options);
	}

	public function collections($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'collections', $options);
	}

	public function mountCollection($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'collections/mounts', $options);
	}

	public function petCollection($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'collections/pets', $options);
	}

	public function encounters($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'encounters', $options);
	}

	public function dungeons($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'encounters/dungeons', $options);
	}

	public function raids($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'encounters/raids', $options);
	}

	public function equipment($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'equipment', $options);
	}

	public function hunterPets($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'hunter-pets', $options);
	}

	public function media($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'character-media', $options);
	}

	public function keystoneProfile($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'mythic-keystone-profile', $options);
	}

	public function keystoneSeasonDetails($realmName, $characterName, $id, $options = [])
	{
		return $this->character_request($realmName, $characterName, "mythic-keystone-profile/season/$id", $options);
	}

	public function professions($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, "professions", $options);
	}

	public function profileStatus($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, "status", $options);
	}

	public function pvpBracket($realmName, $characterName, $bracket, $options = [])
	{
		if(preg_match('/^(2v2|3v3|rbg)$/', $bracket) === 0) {
			throw new Error('Invalid bracket.');
		}

		return $this->character_request($realmName, $characterName, "pvp-bracket/$bracket", $options);
	}

	public function pvpSummary($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'pvp-summary', $options);
	}

	public function quests($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, "quests", $options);
	}

	public function completedQuests($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, "quests/completed", $options);
	}

	public function reputations($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, "reputations", $options);
	}

	public function specializations($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'specializations', $options);
	}

	public function statistics($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'statistics', $options);
	}

	public function titles($realmName, $characterName, $options = [])
	{
		return $this->character_request($realmName, $characterName, 'titles', $options);
	}

	public function characterData($realmName, $characterName)
	{
		$urls = ['character' => $this->character_url($realmName, $characterName)];
		return $this->bulkExecute($urls, $responses, ['namespace' => PROFILE_NAMESPACE]);
	}
}