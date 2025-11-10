<?php

namespace BlizzardApi\Wow\GameData;

class Talent extends GenericDataEndpoint {

  /**
   * Returns an index of PvP talents
   * @param array $options
   * @return mixed
   */
  public function pvpIndex(array $options = [])
  {
      $this->endpoint = 'pvp-talent';
      return $this->apiRequest("{$this->endpointUri()}/index", $this->defaultOptions($options));
  }

  /**
   * Returns a PvP talent by ID
   * @param $id int The ID of the PvP talent
   * @param array $options
   * @return mixed
   */
    public function pvp(int $id, array $options = [])
    {
        $this->endpoint = 'pvp-talent';
        return $this->apiRequest("{$this->endpointUri()}/$id", $this->defaultOptions($options));
    }

  protected function endpointSetup() {
    $this->namespace = STATIC_NAMESPACE;
    $this->ttl = self::CACHE_TRIMESTER;
    $this->endpoint = 'talent';
  }
}