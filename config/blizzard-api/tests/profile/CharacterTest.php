<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

global $characterName;
global $realmName;
global $pvpName;
global $pvpRealm;
global $hunterName;
global $hunterRealm;
$characterName = "Mythickmonk";
$realmName = "Zul'jin";
$pvpName = "Cloudluti";
$pvpRealm = "Illidan";
$hunterName = "Feralpewpew";
$hunterRealm = "Zul'jin";

class CharacterTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testGet() {
    $character = $GLOBALS['characterName'];
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->get("$realm", "$character");
    $this->assertEqual(mb_strtolower($character), mb_strtolower($data->name));
  }

  /**
   * @throws ApiException
   */
  public function testAchievements() {
    $character = $GLOBALS['characterName'];
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->achievements("$realm", "$character");
    $this->assert(is_array($data->achievements));
  }

  /**
   * @throws ApiException
   */
  public function testAchievementsStatistics() {
    $character = $GLOBALS['characterName'];
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->achievementsStatistics("$realm", "$character");
    $this->assertEqual(mb_strtolower($character), mb_strtolower($data->character->name));
  }

  /**
   * @throws ApiException
   */
  public function testAppearance() {
    $character = $GLOBALS['characterName'];
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->appearance("$realm", "$character");
    $this->assertEqual("$character", $data->character->name);
  }

  /**
   * @throws ApiException
   */
  public function testCollections() {
    $characterName = $GLOBALS['characterName'];
    $realmName = $GLOBALS['realmName'];
    $realm = $this->createSlug($realmName);
    $character = mb_strtolower($this->createSlug($characterName));
    $data = self::$Wow->character()->collections("$realm", "$character");
    $this->assertEqual("https://us.api.blizzard.com/profile/wow/character/$realm/$character/collections?namespace=profile-us", $data->_links->self->href);
  }

  /**
   * @param $name
   * @return string
   */
  protected function createSlug($name) {
    $name = mb_strtolower($name);
    $name = str_replace(' ', '-', $name);
    $name = str_replace('\'', '', $name);
    return rawurlencode($name);
  }

  /**
   * @throws ApiException
   */
  public function testMountCollection() {
    $character = $GLOBALS['characterName'];
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->mountCollection("$realm", "$character");
    $this->assert(is_array($data->mounts));
  }

  /**
   * @throws ApiException
   */
  public function testPetCollection() {
    $character = $GLOBALS['hunterName'];
    $realm = $GLOBALS['hunterRealm'];
    $data = self::$Wow->character()->petCollection("$realm", "$character");
    $this->assert(is_array($data->pets));
  }

  /**
   * @throws ApiException
   */
  public function testEncounters() {
    $character = $GLOBALS['characterName'];
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->encounters("$realm", "$character");
    $this->assertEqual("$character", $data->character->name);
  }

  /**
   * @throws ApiException
   */
  public function testDungeons() {
    $character = $GLOBALS['characterName'];
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->dungeons("$realm", "$character");
    $this->assert(is_array($data->expansions));
  }

  /**
   * @throws ApiException
   */
  public function testRaids() {
    $character = $GLOBALS['characterName'];
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->raids("$realm", "$character");
    $this->assert(is_array($data->expansions));
  }

  /**
   * @throws ApiException
   */
  public function testEquipment() {
    $character = $GLOBALS['characterName'];
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->equipment("$realm", "$character");
    $this->assert(is_array($data->equipped_items));
  }

  /**
   * @throws ApiException
   */
  public function testHunterPets() {
    $character = $GLOBALS['hunterName'];
    $realm = $GLOBALS['hunterRealm'];
    $data = self::$Wow->character()->hunterPets("$realm", "$character");
    $this->assert(is_array($data->hunter_pets));
  }

  /**
   * @throws ApiException
   */
  public function testMedia() {
    $character = $GLOBALS['characterName'];
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->media("$realm", "$character");
    $this->assertEqual("$character", $data->character->name);
  }

  /**
   * @throws ApiException
   */
  public function testKeystoneProfile() {
    $character = mb_strtolower($GLOBALS['characterName']);
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->keystoneProfile("$realm", "$character");
    $this->assertEqual($character, mb_strtolower($data->character->name));
  }

  /**
   * @throws ApiException
   */
  public function testKeystoneSeasonDetails() {
    $character = mb_strtolower($GLOBALS['characterName']);
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->keystoneSeasonDetails("$realm", "$character", 4);
    $this->assertEqual($character, mb_strtolower($data->character->name));
  }

  /**
   * @throws ApiException
   */
  public function testProfessions() {
    $characterName = $GLOBALS['characterName'];
    $realmName = $GLOBALS['realmName'];
    $realm = $this->createSlug($realmName);
    $character = mb_strtolower($this->createSlug($characterName));
    $data = self::$Wow->character()->professions("$realm", "$character");
    $this->assertEqual("https://us.api.blizzard.com/profile/wow/character/$realm/$character/professions?namespace=profile-us", $data->_links->self->href);
  }

  /**
   * @throws ApiException
   */
  public function testProfileStatus() {
    $characterName = $GLOBALS['characterName'];
    $realmName = $GLOBALS['realmName'];
    $realm = $this->createSlug($realmName);
    $character = mb_strtolower($this->createSlug($characterName));
    $data = self::$Wow->character()->profileStatus("$realm", "$character");
    $this->assertEqual("https://us.api.blizzard.com/profile/wow/character/$realm/$character/status?namespace=profile-us", $data->_links->self->href);
  }

  /**
   * @throws ApiException
   */
  public function testPvpBracket() {
    $character = mb_strtolower($GLOBALS['pvpName']);
    $realm = $GLOBALS['pvpRealm'];
    $data = self::$Wow->character()->pvpBracket("$realm", "$character", '3v3');
    $this->assertEqual($character, mb_strtolower($data->character->name));
  }

  /**
   * @throws ApiException
   */
  public function testPvpSummary() {
    $character = $GLOBALS['pvpName'];
    $realm = $GLOBALS['pvpRealm'];
    $data = self::$Wow->character()->pvpSummary("$realm", "$character");
    $this->assert(is_array($data->brackets));
  }

  /**
   * @throws ApiException
   */
  public function testQuests() {
    $character = mb_strtolower($GLOBALS['characterName']);
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->quests("$realm", "$character");
    $this->assertEqual($character, mb_strtolower($data->character->name));
  }

  /**
   * @throws ApiException
   */
  public function testCompletedQuests() {
    $character = mb_strtolower($GLOBALS['characterName']);
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->completedQuests("$realm", "$character");
    $this->assertEqual($character, mb_strtolower($data->character->name));
  }

  /**
   * @throws ApiException
   */
  public function testReputations() {
    $character = mb_strtolower($GLOBALS['characterName']);
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->reputations("$realm", "$character");
    $this->assert(is_array($data->reputations));
  }

  /**
   * @throws ApiException
   */
  public function testSpecializations() {
    $character = mb_strtolower($GLOBALS['characterName']);
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->specializations("$realm", "$character");
    $this->assert(is_array($data->specializations));
  }

  /**
   * @throws ApiException
   */
  public function testStatistics() {
    $character = mb_strtolower($GLOBALS['characterName']);
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->statistics("$realm", "$character");
    $this->assertEqual($character, mb_strtolower($data->character->name));
  }

  /**
   * @throws ApiException
   */
  public function testTitles() {
    $character = mb_strtolower($GLOBALS['characterName']);
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->titles("$realm", "$character");
    $this->assertEqual($character, mb_strtolower($data->character->name));
  }

  /**
   * @throws ApiException
   */
  public function testCharacterData() {
    $character = mb_strtolower($GLOBALS['characterName']);
    $realm = $GLOBALS['realmName'];
    $data = self::$Wow->character()->characterData("$realm", "$character");
    $this->assertEqual($character, mb_strtolower($data->character->name));
  }
}