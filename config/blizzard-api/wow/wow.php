<?php

namespace BlizzardApi\Wow;

use BlizzardApi\ApiException;
use BlizzardApi\Cache\CacheInterface;

require_once __DIR__.'/request.php';
require_once __DIR__.'/game_data/generic_endpoint.php';

class Wow
{
	private $defaultRegion;
	private ?CacheInterface $cache;

	public function __construct($region = 'eu', ?CacheInterface $cache = null)
	{
		$this->defaultRegion = $region;
		$this->cache = $cache;
	}

	# GAME DATA ENDPOINTS

	/**
	 * @return GameData\Achievement
	 * @throws ApiException
	 */
	public function achievement(): GameData\Achievement
	{
		return new GameData\Achievement($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Auctions
	 * @throws ApiException
	 */
	public function auctions(): GameData\Auctions
	{
		return new GameData\Auctions($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\AzeriteEssence
	 * @throws ApiException
	 */
	public function azerite_essence(): GameData\AzeriteEssence
	{
		return new GameData\AzeriteEssence($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\ConnectedRealms
	 * @throws ApiException
	 */
	public function connected_realms(): GameData\ConnectedRealms
	{
		return new GameData\ConnectedRealms($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Covenant
	 * @throws ApiException
	 */
	public function covenant(): GameData\Covenant
	{
		return new GameData\Covenant($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Creature
	 * @throws ApiException
	 */
	public function creature(): GameData\Creature
	{
		return new GameData\Creature($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\GuildCrest
	 * @throws ApiException
	 */
	public function guild_crest(): GameData\GuildCrest
	{
		return new GameData\GuildCrest($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Item
	 * @throws ApiException
	 */
	public function item(): GameData\Item
	{
		return new GameData\Item($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Journal
	 * @throws ApiException
	 */
	public function journal(): GameData\Journal
	{
		return new GameData\Journal($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Media
	 * @throws ApiException
	 */
	public function media(): GameData\Media
	{
		return new GameData\Media($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\ModifiedCrafting
	 * @throws ApiException
	 */
	public function modified_crafting(): GameData\ModifiedCrafting
	{
		return new GameData\ModifiedCrafting($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Mount
	 * @throws ApiException
	 */
	public function mount(): GameData\Mount
	{
		return new GameData\Mount($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\MythicKeystoneAffix
	 * @throws ApiException
	 */
	public function mythic_keystone_affix(): GameData\MythicKeystoneAffix
	{
		return new GameData\MythicKeystoneAffix($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\MythicKeystoneDungeon
	 * @throws ApiException
	 */
	public function mythic_keystone_dungeon(): GameData\MythicKeystoneDungeon
	{
		return new GameData\MythicKeystoneDungeon($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\MythicKeystoneLeaderboard
	 * @throws ApiException
	 */
	public function mythic_keystone_leaderboard(): GameData\MythicKeystoneLeaderboard
	{
		return new GameData\MythicKeystoneLeaderboard($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\MythicRaidLeaderboard
	 * @throws ApiException
	 */
	public function mythic_raid_leaderboard(): GameData\MythicRaidLeaderboard
	{
		return new GameData\MythicRaidLeaderboard($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Pet
	 * @throws ApiException
	 */
	public function pet(): GameData\Pet
	{
		return new GameData\Pet($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\PlayableClass
	 * @throws ApiException
	 */
	public function playable_class(): GameData\PlayableClass
	{
		return new GameData\PlayableClass($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\PlayableRace
	 * @throws ApiException
	 */
	public function playable_race(): GameData\PlayableRace
	{
		return new GameData\PlayableRace($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\PlayableSpecialization
	 * @throws ApiException
	 */
	public function playable_specialization(): GameData\PlayableSpecialization
	{
		return new GameData\PlayableSpecialization($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\PowerType
	 * @throws ApiException
	 */
	public function power_type(): GameData\PowerType
	{
		return new GameData\PowerType($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Profession
	 * @throws ApiException
	 */
	public function profession(): GameData\Profession
	{
		return new GameData\Profession($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\PvpSeason
	 * @throws ApiException
	 */
	public function pvp_season(): GameData\PvpSeason
	{
		return new GameData\PvpSeason($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\PvpTier
	 * @throws ApiException
	 */
	public function pvp_tier(): GameData\PvpTier
	{
		return new GameData\PvpTier($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Quest
	 * @throws ApiException
	 */
	public function quest(): GameData\Quest
	{
		return new GameData\Quest($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Realm
	 * @throws ApiException
	 */
	public function realm(): GameData\Realm
	{
		return new GameData\Realm($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Region
	 * @throws ApiException
	 */
	public function region(): GameData\Region
	{
		return new GameData\Region($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Reputations
	 * @throws ApiException
	 */
	public function reputations(): GameData\Reputations
	{
		return new GameData\Reputations($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Spell
	 * @throws ApiException
	 */
	public function spell(): GameData\Spell
	{
		return new GameData\Spell($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Talent
	 * @throws ApiException
	 */
	public function talent(): GameData\Talent
	{
		return new GameData\Talent($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\TechTalent
	 * @throws ApiException
	 */
	public function tech_talent(): GameData\TechTalent
	{
		return new GameData\TechTalent($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\Title
	 * @throws ApiException
	 */
	public function title(): GameData\Title
	{
		return new GameData\Title($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return GameData\WowToken
	 * @throws ApiException
	 */
	public function wow_token(): GameData\WowToken
	{
		return new GameData\WowToken($this->defaultRegion, null, $this->cache);
	}

	# Profile endpoints

	/**
	 * @return Profile\Character
	 * @throws ApiException
	 */
	public function character(): Profile\Character
	{
		return new Profile\Character($this->defaultRegion, null, $this->cache);
	}

	/**
	 * @return Profile\Guild
	 * @throws ApiException
	 */
	public function guild(): Profile\Guild
	{
		return new Profile\Guild($this->defaultRegion, null, $this->cache);
	}
}
