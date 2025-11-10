<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class PlayableSpecializationTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testMedia() {
    $data = self::$Wow->playable_specialization()->media(262);
    $this->assertArrayKeyExists("assets", $data);
  }

  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->playable_specialization()->index();
    $this->assertArrayKeyExists("character_specializations", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = self::$Wow->playable_specialization()->get(262);
    $this->assertEqual('Elemental', $data->name->en_US);
  }
}