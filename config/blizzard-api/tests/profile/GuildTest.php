<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

global $guildName;
global $guildRealm;

$guildName = 'Just for Alts';
$guildRealm = "Zul'jin";

class GuildTest extends ApiTest {
	/**
	 * @throws ApiException
	 */
	public function testGet() {
		$guild = mb_strtolower($GLOBALS['guildName']);
		$realm = $GLOBALS['guildRealm'];
		$data = self::$Wow->guild()->get("$realm", "$guild");
		$this->assertEqual("$guild", mb_strtolower($data->name));
	}

	/**
	 * @throws ApiException
	 */
	public function testActivity() {
		$guild = mb_strtolower($GLOBALS['guildName']);
		$realm = $GLOBALS['guildRealm'];
		$data = self::$Wow->guild()->activity("$realm", "$guild");
		$this->assertEqual("$guild", mb_strtolower($data->guild->name));
	}

	/**
	 * @throws ApiException
	 */
	public function testAchievements() {
		$guild = mb_strtolower($GLOBALS['guildName']);
		$realm = $GLOBALS['guildRealm'];
		$data = self::$Wow->guild()->achievements("$realm", "$guild");
		$this->assertEqual("$guild", mb_strtolower($data->guild->name));
	}

	/**
	 * @throws ApiException
	 */
	public function testRoster() {
		$guild = mb_strtolower($GLOBALS['guildName']);
		$realm = $GLOBALS['guildRealm'];
		$data = self::$Wow->guild()->roster("$realm", "$guild");
		$this->assertEqual("$guild", mb_strtolower($data->guild->name));
	}
}