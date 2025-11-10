<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class WoWTokenTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = $this::$Wow->wow_token()->index();
    $this->assertArrayKeyExists("price", $data);
  }
}