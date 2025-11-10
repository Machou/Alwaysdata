<?php

namespace BlizzardApi\Wow\GameData;

class Pet extends GenericDataEndpoint
{
    /**
     * Returns media for a battle pet by ID
     * @param $id integer The ID of the pet
     * @param $options array Request options
     * @return mixed
     */
    public function media(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/pet/$id", $this->defaultOptions($options));
    }

    /**
     * Returns an index of pet abilities
     * @param $options array Request options
     * @return mixed
     */
    public function abilities(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('ability')}/index", $this->defaultOptions($options));
    }

    /**
     * Returns a pet ability by ID
     * @param $id integer The ID of the pet ability
     * @param $options array Request options
     * @return mixed
     */
    public function ability(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri('ability')}/$id", $this->defaultOptions($options));
    }

    /**
     * Returns media for a pet ability by ID
     * @param $id integer The ID of the pet ability
     * @param $options array Request options
     * @return mixed
     */
    public function abilityMedia(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/pet-ability/$id", $this->defaultOptions($options));
    }


    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'pet';
    }
}