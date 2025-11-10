<?php

namespace BlizzardApi\Wow\GameData;

class PlayableClass extends GenericDataEndpoint
{
    /**
     * Returns media for a playable class by ID
     * @param $id integer The ID of the playable class
     * @param $options array Request options
     * @return mixed
     */
    public function media(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/playable-class/$id", $this->defaultOptions($options));
    }

    /**
     * Returns the PVP Talent slots for a playable class by ID
     * @param $id integer The ID of the playable class
     * @param $options array Request options
     * @return mixed
     */
    public function pvpTalentSlots(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/$id/pvp-talent-slots", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'playable-class';
    }
}