<?php

namespace BlizzardApi\Wow\GameData;

class TechTalent extends GenericDataEndpoint {

  /**
   * Returns an index of tech talent trees
   * @param array $options
   * @return mixed
   */
  public function techTalentTrees(array $options = []) {
    $this->endpoint = 'tech-talent-tree';
    return $this->apiRequest("{$this->endpointUri()}/index", $this->defaultOptions($options));
  }

  /**
   * Returns a tech talent tree by ID
   * @param $id int The ID of the tech talent tree
   * @param array $options
   * @return mixed
   */
  public function techTalentTree(int $id, array $options = []) {
    $this->endpoint = 'tech-talent-tree';
    return $this->apiRequest("{$this->endpointUri()}/$id", $this->defaultOptions($options));
  }

  /**
   * Returns media for a tech talent  by ID
   * @param $id integer The ID of the tech talent
   * @param $options array Request options
   * @return mixed
   */
  public function displayMedia(int $id, array $options = []) {
    return $this->apiRequest("{$this->baseUrl('media')}/tech-talent/$id", $this->defaultOptions($options));
  }

  protected function endpointSetup() {
    $this->namespace = STATIC_NAMESPACE;
    $this->ttl = self::CACHE_TRIMESTER;
    $this->endpoint = 'tech-talent';
  }
}