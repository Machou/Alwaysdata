<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class PowerTypeTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->power_type()->index();
    $this->assertArrayKeyExists("power_types", $data);
  }

  /**
   * @throws ApiException
   */
  public function testIndexClassic() {
    $data = self::$Wow->power_type()->index(['classic' => true]);
    $this->assertArrayKeyExists("power_types", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = self::$Wow->power_type()->get(0);
    $this->assertEqual('Mana', $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testGetClassic() {
    $data = self::$Wow->power_type()->get(0, ['classic' => true]);
    $this->assertEqual('Mana', $data->name->en_US);
  }
}