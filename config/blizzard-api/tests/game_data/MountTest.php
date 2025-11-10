<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class MountTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->mount()->index();
    $this->assertArrayKeyExists("mounts", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = self::$Wow->mount()->get(7);
    $this->assertEqual('Gray Wolf', $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testSearch() {
    $data = self::$Wow->mount()->search(['search' => 'id=335']);
    $this->assertEqual("Magic Rooster", $data->results[0]->data->name->en_US);
  }
}