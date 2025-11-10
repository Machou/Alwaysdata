<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class MythicKeystoneAffixTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testMedia() {
    $data = self::$Wow->mythic_keystone_affix()->media(1);
    $this->assertArrayKeyExists("assets", $data);
  }

  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->mythic_keystone_affix()->index();
    $this->assertArrayKeyExists("affixes", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = self::$Wow->mythic_keystone_affix()->get(2);
    $this->assertEqual('Skittish', $data->name->en_US);
  }
}