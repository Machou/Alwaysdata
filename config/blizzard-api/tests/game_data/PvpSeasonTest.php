<?php

namespace BlizzardApi\Test;
class PvpSeasonTest extends ApiTest {
  /**
   * @throws /ApiException
   */
  public function testSeasons() {
    $data = self::$Wow->pvp_season()->index();
    $this->assertArrayKeyExists("seasons", $data);
  }

  /**
   * @throws /ApiException
   */
  public function testSeason() {
    $data = self::$Wow->pvp_season()->get(33);
    $this->assertArrayKeyExists("leaderboards", $data);
  }

  /**
   * @throws /ApiException
   */
  public function testLeaderboards() {
    $data = self::$Wow->pvp_season()->leaderboards(33);
    $this->assertArrayKeyExists("leaderboards", $data);
  }

  /**
   * @throws /ApiException
   */
  public function testLeaderboard() {
    $data = self::$Wow->pvp_season()->leaderboard(33, '3v3');
    $this->assertArrayKeyExists("entries", $data);
  }

  /**
   * @throws /ApiException
   */
  public function testRewards() {
    $data = self::$Wow->pvp_season()->rewards(33);
    $this->assertArrayKeyExists("rewards", $data);
  }
}