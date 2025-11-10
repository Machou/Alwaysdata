<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class MythicKeystoneDungeonTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testDungeons() {
    $data = self::$Wow->mythic_keystone_dungeon()->dungeons();
    $this->assertArrayKeyExists("dungeons", $data);
  }

  /**
   * @throws ApiException
   */
  public function testDungeon() {
    $data = self::$Wow->mythic_keystone_dungeon()->dungeon(391);
    $this->assertArrayKeyExists("keystone_upgrades", $data);
  }

  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->mythic_keystone_dungeon()->index();
    $this->assertArrayKeyExists("seasons", $data);
  }

  /**
   * @throws ApiException
   */
  public function testPeriods() {
    $data = self::$Wow->mythic_keystone_dungeon()->periods();
    $this->assertArrayKeyExists("periods", $data);
  }

  /**
   * @throws ApiException
   */
  public function testPeriod() {
    $data = self::$Wow->mythic_keystone_dungeon()->period(641);
    $this->assertArrayKeyExists("start_timestamp", $data);
  }

  /**
   * @throws ApiException
   */
  public function testSeasons() {
    $data = self::$Wow->mythic_keystone_dungeon()->seasons();
    $this->assertArrayKeyExists("seasons", $data);
  }

  /**
   * @throws ApiException
   */
  public function testSeason() {
    $data = self::$Wow->mythic_keystone_dungeon()->season(1);
    $this->assertArrayKeyExists("periods", $data);
  }
}