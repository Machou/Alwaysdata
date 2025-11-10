<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class CovenantTest extends ApiTest {
	/**
	 * @throws ApiException
	 */
	public function testCovenants() {
		$data = self::$Wow->covenant()->index();
		$this->assertArrayKeyExists("covenants", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testCovenant() {
		$data = self::$Wow->covenant()->get(1);
		$this->assertEqual('Kyrian', $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testSoulbinds() {
		$data = self::$Wow->covenant()->soulbinds();
		$this->assertArrayKeyExists("soulbinds", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testSoulbind() {
		$data = self::$Wow->covenant()->soulbind(1);
		$this->assertEqual('Niya', $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testConduits() {
		$data = self::$Wow->covenant()->conduits();
		$this->assertArrayKeyExists("conduits", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testConduit() {
		$data = self::$Wow->covenant()->conduit(19);
		$this->assertEqual('Calculated Strikes', $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testMedia() {
		$data = self::$Wow->covenant()->displayMedia(1);
		$this->assertArrayKeyExists("assets", $data);
	}
}