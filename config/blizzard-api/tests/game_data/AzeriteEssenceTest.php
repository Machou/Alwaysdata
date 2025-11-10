<?php

namespace BlizzardApi\Test;
use BlizzardApi\ApiException;

class AzeriteEssenceTest extends ApiTest
{
	/**
	 * @throws ApiException
	 */
	public function testIndex() {
		$data = self::$Wow->azerite_essence()->index();
		$this->assert(is_array($data->azerite_essences));
	}

	/**
	 * @throws ApiException
	 */
	public function testGet() {
		$data = self::$Wow->azerite_essence()->get(2);
		$this->assertEqual("Azeroth's Undying Gift", $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testMedia() {
		$data = self::$Wow->azerite_essence()->media(2);
		$this->assertArrayKeyExists("assets", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testSearch() {
		$data = self::$Wow->azerite_essence()->search(['search' => 'allowed_specializations.name.en_US=Arcane', '&orderby=name', '&_page=1', '&_pageSize=1']);
		$this->assertEqual("Arcane", $data->results[0]->data->allowed_specializations[0]->name->en_US);
	}
}