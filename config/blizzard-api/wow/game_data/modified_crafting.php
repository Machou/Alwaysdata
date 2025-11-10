<?php

namespace BlizzardApi\Wow\GameData;

use Error;

class ModifiedCrafting extends GenericDataEndpoint
{
    public function get($id, $options = [])
    {
        throw new Error('The Modified Crafting does not have a get method.');
    }

    /**
     * Returns an index of modified crafting categories
     * @param $options array Request options
     * @return mixed
     */
    public function categories(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/category/index", $this->defaultOptions($options));
    }

    /**
     * Returns a modified crafting category by ID
     * @param $id integer The ID of the Crafting Category
     * @param $options array Request options
     * @return mixed
     */
    public function category(int $id, array $options = [])
    {
      return $this->apiRequest("{$this->endpointUri()}/category/$id", $this->defaultOptions($options));
    }

    /**
     * Returns an index of modified crafting reagent slot types
     * @param $options array Request options
     * @return mixed
     */
    public function reagentSlotTypes(array $options = [])
    {
      return $this->apiRequest("{$this->endpointUri()}/reagent-slot-type/index", $this->defaultOptions($options));
    }

    /**
     * Returns a modified crafting reagent slot type by ID
     * @param $id integer The ID of the modified crafting reagent slot type
     * @param $options array Request options
     * @return mixed
     */
    public function reagentSlotType(int $id, array $options = [])
    {
      return $this->apiRequest("{$this->endpointUri()}/reagent-slot-type/$id", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'modified-crafting';
    }
}