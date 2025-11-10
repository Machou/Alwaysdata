<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class ProfessionTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->profession()->index();
    $this->assertArrayKeyExists("professions", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = self::$Wow->profession()->get(165);
    $this->assertEqual("Leatherworking", $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testMedia() {
    $data = self::$Wow->profession()->media(165);
    $this->assertArrayKeyExists("assets", $data);
  }

  /**
   * @throws ApiException
   */
  public function testSkillTier() {
    $data = self::$Wow->profession()->skillTier(165, 2525);
    $this->assertEqual("Kul Tiran Leatherworking / Zandalari Leatherworking", $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testRecipe() {
    $data = self::$Wow->profession()->recipe(1631);
    $this->assertEqual("Rough Sharpening Stone", $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testRecipeMedia() {
    $data = self::$Wow->profession()->recipeMedia(1631);
    $this->assertArrayKeyExists("assets", $data);
  }
}