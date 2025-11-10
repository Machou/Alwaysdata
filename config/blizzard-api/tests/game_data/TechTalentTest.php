<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class TechTalentTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testTechTalentTrees() {
    $data = self::$Wow->tech_talent()->techTalentTrees();
    $this->assertArrayKeyExists("talent_trees", $data);
  }

  /**
   * @throws ApiException
   */
  public function testTechTalentTree() {
    $data = self::$Wow->tech_talent()->techTalentTree(275);
    $this->assertArrayKeyExists("talents", $data);
  }

  /**
   * @throws ApiException
   */
  public function testTechTalents() {
    $data = self::$Wow->tech_talent()->index();
    $this->assertArrayKeyExists("talents", $data);
  }

  /**
   * @throws ApiException
   */
  public function testTechTalent() {
    $data = self::$Wow->tech_talent()->get(863);
    $this->assertEqual('Run Without Tiring', $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testMedia() {
    $data = self::$Wow->tech_talent()->displayMedia(863);
    $this->assertArrayKeyExists("assets", $data);
  }

}