<?php

namespace BlizzardApi\Test;

use BlizzardApi\ApiException;

class ItemTest extends ApiTest {
	/**
	 * @throws ApiException
	 */
	public function testGet() {
		$data = self::$Wow->item()->get(35000);
		$this->assertEqual("Brutal Gladiator's Dragonhide Legguards", $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testGetClassic() {
		$data = self::$Wow->item()->get(25, ['classic' => true]);
		$this->assertEqual("Worn Shortsword", $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testSets() {
		$data = self::$Wow->item()->sets();
		$this->assertArrayKeyExists("item_sets", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testSet() {
		$data = self::$Wow->item()->set(1060);
		$this->assertEqual(76749, $data->items[0]->id);
	}

	/**
	 * @throws ApiException
	 */
	public function testClasses() {
		$data = self::$Wow->item()->classes();
		$this->assertArrayKeyExists("item_classes", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testClassesClassic() {
		$data = self::$Wow->item()->classes(['classic' => true]);
		$this->assertArrayKeyExists("item_classes", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testItemClass() {
		$data = self::$Wow->item()->class(1);
		$this->assertEqual('Container', $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testClassClassic() {
		$data = self::$Wow->item()->class(1, ['classic' => true]);
		$this->assertEqual('Container', $data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testSubclass() {
		$data = self::$Wow->item()->subClass(1, 1);
		$this->assertEqual('Soul Bag', $data->display_name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testSubclassClassic() {
		$data = self::$Wow->item()->subClass(1, 1, ['classic' => true]);
		$this->assertEqual('Soul Bag', $data->display_name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testMedia() {
		$data = self::$Wow->item()->media(35000);
		$this->assertArrayKeyExists("assets", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testMediaClassic() {
		$data = self::$Wow->item()->media(25, ['classic' => true]);
		$this->assertArrayKeyExists("assets", $data);
	}

	/**
	 * @throws ApiException
	 */
	public function testSearch() {
		$data = self::$Wow->item()->search(['search' => 'id=35000']);
		$this->assertEqual("Brutal Gladiator's Dragonhide Legguards", $data->results[0]->data->name->en_US);
	}

	/**
	 * @throws ApiException
	 */
	public function testSearchClassic() {
		$data = self::$Wow->item()->search(['search' => 'id=25'], ['classic' => true]);
		$this->assertEqual("Worn Shortsword", $data->results[0]->data->name->en_US);
	}
}