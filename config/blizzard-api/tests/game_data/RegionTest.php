<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class RegionTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->region()->index();
    $this->assertArrayKeyExists("regions", $data);
  }

  /**
   * @throws ApiException
   */
  public function testIndexClassic() {
    $data = self::$Wow->region()->index(['classic' => true]);
    $this->assertArrayKeyExists("regions", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = self::$Wow->region()->get(1);
    $this->assertEqual("North America", $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testGetClassic() {
    $data = self::$Wow->region()->get(41, ['classic' => true]);
    $this->assertEqual("Classic North America", $data->name->en_US);
  }
}