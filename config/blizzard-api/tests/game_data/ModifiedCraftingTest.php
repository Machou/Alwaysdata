<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class ModifiedCraftingTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testModifiedCrafting() {
    $data = self::$Wow->modified_crafting()->index();
    $this->assertArrayKeyExists("slot_types", $data);
  }

  /**
   * @throws ApiException
   */
  public function testCategories() {
    $data = self::$Wow->modified_crafting()->categories();
    $this->assertArrayKeyExists("categories", $data);
  }

  /**
   * @throws ApiException
   */
  public function testCategory() {
    $data = self::$Wow->modified_crafting()->category(1);
    $this->assertEqual('Specify Haste', $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testReagentSlotTypes() {
    $data = self::$Wow->modified_crafting()->reagentSlotTypes();
    $this->assertArrayKeyExists("slot_types", $data);
  }

  /**
   * @throws ApiException
   */
  public function testReagentSlotType() {
    $data = self::$Wow->modified_crafting()->reagentSlotType(16);
    $this->assertEqual('Modify Item Level - Major', $data->description->en_US);
  }

}