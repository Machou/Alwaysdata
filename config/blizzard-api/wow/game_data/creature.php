<?php

namespace BlizzardApi\Wow\GameData;
use Error;

class Creature extends GenericDataEndpoint {
  public function index($options = []) {
    throw new Error("The Creature endpoint doesn't have an index method.");
  }

  /**
   * Returns an index of Creature Families
   * @param $options array Request options
   * @return mixed
   */
  public function families(array $options = [])
  {
      return $this->apiRequest("{$this->endpointUri('family')}/index", $this->defaultOptions($options));
  }

  /**
   * Returns a creature family by ID
   * @param $id int The ID of the creature family
   * @param $options array Request options
   * @return mixed
   */
    public function family(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('family')}/$id", $this->defaultOptions($options));
    }

  /**
   * Returns an index of Creature Types
   * @param $options array Request options
   * @return mixed
   */
    public function types(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('type')}/index", $this->defaultOptions($options));
    }

  /**
   * Returns a creature type by ID
   * @param $id int The ID of the creature type
   * @param $options array Request options
   * @return mixed
   */
    public function type(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('type')}/$id", $this->defaultOptions($options));
    }

  /**
   * Returns media for a creature display by ID
   * @param $id integer The ID of the creature display
   * @param $options array Request options
   * @return mixed
   */
    public function displayMedia(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/creature-display/$id", $this->defaultOptions($options));
    }

  /**
   * Returns a creature family media by ID
   * @param $id integer The ID of the creature family
   * @param $options array Request options
   * @return mixed
   */
    public function familyMedia(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/creature-family/$id", $this->defaultOptions($options));
    }

  /**
   * Performs a search of creatures
   * @param $search array Search parameters
   * @param $options array Request options
   * @return mixed
   */
    public function search(array $search = [], array $options = [])
    {
        return $this->apiSearchRequest("{$this->baseUrl('game_data')}/search/$this->endpoint", $search, $this->defaultOptions($options));
    }


  protected function endpointSetup() {
    $this->namespace = STATIC_NAMESPACE;
    $this->ttl = self::CACHE_TRIMESTER;
    $this->endpoint = 'creature';
  }
}