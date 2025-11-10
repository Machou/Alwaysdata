<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class PvpTierTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testMedia() {
    $data = self::$Wow->pvp_tier()->media(1);
    $this->assertArrayKeyExists("assets", $data);
  }

  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->pvp_tier()->index();
    $this->assertArrayKeyExists("tiers", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = self::$Wow->pvp_tier()->get(1);
    $this->assertEqual('Unranked', $data->name->en_US);
  }
}