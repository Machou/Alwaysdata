<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class PetTest extends ApiTest {
	/**
	 * @throws ApiException
	 */
	public function testIndex() {
		$data = self::$Wow->pet()->index();
		$this->assert(is_array($data->pets));
	}

	/**
	 * @throws ApiException
	 */
	public function testGet() {
		$data = self::$Wow->pet()->get(39);
		$this->assertEqual('Mechanical Squirrel', $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testMedia() {
		$data = self::$Wow->pet()->media(39);
		$this->assertArrayKeyExists("assets", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testAbilities() {
		$data = self::$Wow->pet()->abilities();
		$this->assertArrayKeyExists("abilities", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testAbility() {
		$data = self::$Wow->pet()->ability(110);
		$this->assertEqual('Bite', $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testAbilityMedia() {
		$data = self::$Wow->pet()->abilityMedia(110);
		$this->assertArrayKeyExists("assets", $data);
	}
}
