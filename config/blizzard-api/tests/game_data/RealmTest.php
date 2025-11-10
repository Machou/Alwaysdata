<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class RealmTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->realm()->index();
    $this->assertArrayKeyExists("realms", $data);
  }

  /**
   * @throws ApiException
   */
  public function testIndexClassic() {
    $data = self::$Wow->realm()->index(['classic' => true]);
    $this->assertArrayKeyExists("realms", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGetString() {
    $data = self::$Wow->realm()->get("zuljin");
    $this->assertEqual("Zul'jin", $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testGetID() {
    $data = self::$Wow->realm()->get(61);
    $this->assertEqual("Zul'jin", $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testGetClassicString() {
    $data = self::$Wow->realm()->get("mankrik", ['classic' => true]);
    $this->assertEqual("Mankrik", $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testGetClassicID() {
    $data = self::$Wow->realm()->get(4384, ['classic' => true]);
    $this->assertEqual("Mankrik", $data->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testSearch() {
    $data = self::$Wow->realm()->search(['search' => 'slug=zuljin']);
    $this->assertEqual("zuljin", $data->results[0]->data->slug);
  }

  /**
   * @throws ApiException
   */
  public function testSearchClassic() {
    $data = self::$Wow->realm()->search(['search' => 'slug=mankrik'], ['classic' => true]);
    $this->assertEqual("mankrik", $data->results[0]->data->slug);
  }
}