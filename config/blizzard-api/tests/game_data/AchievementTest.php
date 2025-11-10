<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class AchievementTest extends ApiTest {
	/**
	 * @throws ApiException
	 */
	public function testCategories() {
		$data = self::$Wow->achievement()->categories();
		$this->assert(is_array($data->categories));
	}

	/**
	 * @throws ApiException
	 */
	public function testCategory() {
		$data = self::$Wow->achievement()->category(81);
		$this->assertEqual('Feats of Strength', $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testIndex() {
		$data = self::$Wow->achievement()->index();
		$this->assert(is_array($data->achievements));
	}

	/**
	 * @throws ApiException
	 */
	public function testGet() {
		$data = self::$Wow->achievement()->get(6);
		$this->assertEqual('Level 10', $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testMedia() {
		$data = self::$Wow->achievement()->media(6);
		$this->assertArrayKeyExists("assets", $data);
	}
}