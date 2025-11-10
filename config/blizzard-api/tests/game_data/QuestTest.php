<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class QuestTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->quest()->index();
    $this->assertArrayKeyExists("categories", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = self::$Wow->quest()->get(2);
    $this->assertEqual("Sharptalon's Claw", $data->title->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testCategories() {
    $data = self::$Wow->quest()->categories();
    $this->assertArrayKeyExists("categories", $data);
  }

  /**
   * @throws ApiException
   */
  public function testCategory() {
    $data = self::$Wow->quest()->category(370);
    $this->assertEqual("Brewfest", $data->category->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testAreas() {
    $data = self::$Wow->quest()->areas();
    $this->assertArrayKeyExists("areas", $data);
  }

  /**
   * @throws ApiException
   */
  public function testArea() {
    $data = self::$Wow->quest()->area(14);
    $this->assertEqual("Durotar", $data->area->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testTypes() {
    $data = self::$Wow->quest()->types();
    $this->assertArrayKeyExists("types", $data);
  }

  /**
   * @throws ApiException
   */
  public function testType() {
    $data = self::$Wow->quest()->type(62);
    $this->assertEqual("Raid", $data->type->en_US);
  }
}