<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class MythicRaidLeaderboardTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = self::$Wow->mythic_raid_leaderboard()->get('uldir', 'horde');
    $this->assertArrayKeyExists("entries", $data);
  }
}