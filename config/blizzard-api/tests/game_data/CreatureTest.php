<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class CreatureTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = self::$Wow->creature()->get(42722);
    $this->assertEqual('Young Mastiff', $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testGetClassic() {
    $data = self::$Wow->creature()->get(107, ['classic' => true]);
    $this->assertEqual('Raptor', $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testFamilies() {
    $data = self::$Wow->creature()->families();
    $this->assertArrayKeyExists("creature_families", $data);
  }

  /**
   * @throws ApiException
   */
  public function testFamiliesClassic() {
    $data = self::$Wow->creature()->families(['classic' => true]);
    $this->assertArrayKeyExists("creature_families", $data);
  }

  /**
   * @throws ApiException
   */
  public function testFamily() {
    $data = self::$Wow->creature()->family(1);
    $this->assertEqual('Wolf', $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testFamilyClassic() {
    $data = self::$Wow->creature()->family(1, ['classic' => true]);
    $this->assertEqual('Wolf', $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testFamilyMedia() {
    $data = self::$Wow->creature()->familyMedia(1);
    $this->assertArrayKeyExists("assets", $data);
  }

  /**
   * @throws ApiException
   */
  public function testFamilyMediaClassic() {
    $data = self::$Wow->creature()->familyMedia(1, ['classic' => true]);
    $this->assertArrayKeyExists("assets", $data);
  }

  /**
   * @throws ApiException
   */
  public function testTypes() {
    $data = self::$Wow->creature()->types();
    $this->assertArrayKeyExists("creature_types", $data);
  }

  /**
   * @throws ApiException
   */
  public function testTypesClassic() {
    $data = self::$Wow->creature()->types(['classic' => true]);
    $this->assertArrayKeyExists("creature_types", $data);
  }

  /**
   * @throws ApiException
   */
  public function testType() {
    $data = self::$Wow->creature()->type(1);
    $this->assertEqual('Beast', $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testTypeClassic() {
    $data = self::$Wow->creature()->type(1, ['classic' => true]);
    $this->assertEqual('Beast', $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testDisplayMedia() {
    $data = self::$Wow->creature()->displayMedia(30221);
    $this->assertArrayKeyExists("assets", $data);
  }

  /**
   * @throws ApiException
   */
  public function testDisplayMediaClassic() {
    $data = self::$Wow->creature()->displayMedia(180, ['classic' => true]);
    $this->assertArrayKeyExists("assets", $data);
  }

  /**
   * @throws ApiException
   */
  public function testSearch() {
    $data = self::$Wow->creature()->search(['search' => 'data.id=40624', '&locale=en_US', '&orderby=id', '&_page=1']);
    $this->assertEqual('Celestial Dragon', $data->results[0]->data->name->en_US);
  }

}