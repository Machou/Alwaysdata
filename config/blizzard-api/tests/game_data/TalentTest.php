<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class TalentTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = $this::$Wow->talent()->index();
    $this->assertArrayKeyExists("talents", $data);
  }

  /**
   * @throws ApiException
   */
  public function testGet() {
    $data = $this::$Wow->talent()->get(106520);
    $this->assertEqual("Eye of the Tiger", $data->spell->name->en_US);
  }

  /**
   * @throws ApiException
   */
  public function testPvpIndex() {
    $data = $this::$Wow->talent()->pvpIndex();
    $this->assertArrayKeyExists("pvp_talents", $data);
  }

  /**
   * @throws ApiException
   */
  public function testPvp() {
    $data = $this::$Wow->talent()->pvp(166);
    $this->assertEqual("Barbarian", $data->spell->name->en_US);
  }
}