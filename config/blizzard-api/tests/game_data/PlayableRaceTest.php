<?php

namespace BlizzardApi\Test;
use BlizzardApi\ApiException;

class PlayableRaceTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->playable_race()->index();
    $this->assertArrayKeyExists("races", $data);
  }

  /**
   * @throws ApiException
   */
  public function testIndexClassic() {
    $data = self::$Wow->playable_race()->index(['classic' => true]);
    $this->assertArrayKeyExists("races", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = self::$Wow->playable_race()->get(2);
    $this->assertEqual('Orc', $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testGetClassic() {
    $data = self::$Wow->playable_race()->get(2, ['classic' => true]);
    $this->assertEqual('Orc', $data->name->en_US);
  }
}