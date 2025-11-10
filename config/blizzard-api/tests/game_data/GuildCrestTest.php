<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class GuildCrestTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testIndex() {
    $data = self::$Wow->guild_crest()->index();
    $this->assertArrayKeyExists("emblems", $data);
    $this->assertArrayKeyExists("borders", $data);
  }

  /**
   * @throws ApiException
   */
  public function testIndexClassic() {
    $data = self::$Wow->guild_crest()->index(['classic' => true]);
    $this->assertArrayKeyExists("emblems", $data);
    $this->assertArrayKeyExists("borders", $data);
  }

  /**
   * @throws ApiException
   */
  public function testBordersMedia() {
    $data = self::$Wow->guild_crest()->borderMedia(0);
    $this->assertArrayKeyExists("assets", $data);
  }

  /**
   * @throws ApiException
   */
  public function testBorderMediaClassic() {
    $data = self::$Wow->guild_crest()->borderMedia(0, ['classic' => true]);
    $this->assertArrayKeyExists("assets", $data);
  }

  /**
   * @throws ApiException
   */
  public function testEmblemMedia() {
    $data = self::$Wow->guild_crest()->emblemMedia(0);
    $this->assertArrayKeyExists("assets", $data);
  }

  /**
   * @throws ApiException
   */
  public function testEmblemMediaClassic() {
    $data = self::$Wow->guild_crest()->emblemMedia(0, ['classic' => true]);
    $this->assertArrayKeyExists("assets", $data);
  }
}