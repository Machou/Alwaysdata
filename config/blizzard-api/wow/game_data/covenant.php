<?php

namespace BlizzardApi\Wow\GameData;

class Covenant extends GenericDataEndpoint
{

    /**
     * Returns an index of Soulbinds
     * @param $options array Request options
     * @return mixed
     */
    public function soulbinds(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/soulbind/index", $this->defaultOptions($options));
    }

    /**
     * Returns a Soulbind by ID
     * @param $id int The ID of the soulbind
     * @param $options array Request options
     * @return mixed
     */
    public function soulbind(int $id, array $options = [])
    {
      return $this->apiRequest("{$this->endpointUri()}/soulbind/$id", $this->defaultOptions($options));
    }

    /**
     * Returns an index of conduits
     * @param $options array Request options
     * @return mixed
     */
    public function conduits(array $options = [])
    {
      return $this->apiRequest("{$this->endpointUri()}/conduit/index", $this->defaultOptions($options));
    }

    /**
     * Returns a conduit type by ID
     * @param $id int The ID of the conduit
     * @param $options array Request options
     * @return mixed
     */
    public function conduit(int $id, array $options = [])
    {
      return $this->apiRequest("{$this->endpointUri()}/conduit/$id", $this->defaultOptions($options));
    }

    /**
     * Returns media for a Covenant by ID
     * @param $id integer The ID of the Covenant
     * @param $options array Request options
     * @return mixed
     */
    public function displayMedia(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/covenant/$id", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'covenant';
    }
}