<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class TitleTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = $this::$Wow->title()->index();
    $this->assertArrayKeyExists("titles", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = $this::$Wow->title()->get(419);
    $this->assertEqual("the Faceless One", $data->name->en_US);
  }
}