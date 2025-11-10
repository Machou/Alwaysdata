<?php

namespace BlizzardApi\Wow\GameData;

class Profession extends GenericDataEndpoint
{
    /**
     * Returns media for a profession by ID
     * @param $id integer The ID of the profession
     * @param $options array Request options
     * @return mixed
     */
    public function media(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/profession/$id", $this->defaultOptions($options));
    }

    /**
     * Returns a skill tier for a profession by ID
     * @param $id integer The ID of the profession
     * @param $skillTierID integer The ID of the skill tier
     * @param $options array Request options
     * @return mixed
     */
    public function skillTier(int $id, int $skillTierID, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/$id/skill-tier/$skillTierID", $this->defaultOptions($options));
    }

    /**
     * Returns a recipe by ID
     * @param $id integer The ID of the recipe
     * @param $options array Request options
     * @return mixed
     */
    public function recipe(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('game_data')}/recipe/$id", $this->defaultOptions($options));
    }

    /**
     * Returns media for a recipe by ID
     * @param $id integer The ID of the recipe
     * @param $options array Request options
     * @return mixed
     */
    public function recipeMedia(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/recipe/$id", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'profession';
    }
}