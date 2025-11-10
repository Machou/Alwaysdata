<?php

namespace BlizzardApi\Wow\GameData;

class PlayableSpecialization extends GenericDataEndpoint
{
    /**
     * Returns media for a playable specialization by ID
     * @param $id integer The ID of the playable specialization
     * @param $options array Request options
     * @return mixed
     */
    public function media(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/playable-specialization/$id", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'playable-specialization';
    }
}