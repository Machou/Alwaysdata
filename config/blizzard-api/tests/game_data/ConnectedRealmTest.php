<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class ConnectedRealmTest extends ApiTest {
	/**
	 * @throws ApiException
	 */
	public function testIndex() {
		$data = self::$Wow->connected_realms()->index();
		$this->assertArrayKeyExists("connected_realms", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testIndexClassic() {
		$data = self::$Wow->connected_realms()->index(['classic' => true]);
		$this->assertArrayKeyExists("connected_realms", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testGet() {
		$data = self::$Wow->connected_realms()->get(61);
		$this->assertEqual("Zul'jin", $data->realms[0]->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testGetClassic() {
		$data = self::$Wow->connected_realms()->get(4388, ['classic' => true]);
		$this->assertEqual("Westfall", $data->realms[0]->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testGetAuctions() {
		echo "Skipping Get Auctions";
		return true;
		// $data = self::$Wow->connected_realms()->getAuctions(61);
		// $this->assertEqual("https://us.api.blizzard.com/data/wow/connected-realm/47?namespace=dynamic-us", $data->connected_realm->href);
	}

	/**
	 * @throws ApiException
	 */
	public function testGetClassicAuctionHouseIndex() {
		$data = self::$Wow->connected_realms()->getClassicAuctionHouseIndex(4388, ['classic' => true]);
		$this->assertEqual("Alliance Auction House", $data->auctions[0]->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testGetClassicAuctions() {
		echo "Skipping Get Classic Auctions";
		return true;
		// $data = self::$Wow->connected_realms()->getClassicAuctions(4388, 2, ['classic' => true]);
		// $this->assertEqual("https://us.api.blizzard.com/data/wow/connected-realm/4388?namespace=dynamic-classic-us", $data->connected_realm->href);
	}
}