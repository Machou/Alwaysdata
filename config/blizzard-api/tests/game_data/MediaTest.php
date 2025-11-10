<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class MediaTest extends ApiTest {
  /**
   * @throws ApiException
   */
  public function testSearch() {
    $data = self::$Wow->media()->search(['search' => 'data.id=25', '&locale=en_US', '&tags=item', '&orderby=id', '&_page=1']);
    $this->assertEqual(25, $data->results[0]->data->id);
  }
}
