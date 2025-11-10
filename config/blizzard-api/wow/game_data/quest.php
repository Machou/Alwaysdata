<?php

namespace BlizzardApi\Wow\GameData;

class Quest extends GenericDataEndpoint
{
    /**
     * Returns an index of quest categories (such as quests for a specific class, profession, or storyline)
     * @param array $options
     * @return mixed
     */
    public function categories(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/category/index", $this->defaultOptions($options));
    }

    /**
     * Returns a quest category by ID
     * @param $id integer The ID of the quest category
     * @param array $options
     * @return mixed
     */
    public function category(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/category/$id", $this->defaultOptions($options));
    }

    /**
     * Returns an index of quest areas
     * @param array $options
     * @return mixed
     */
    public function areas(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/area/index", $this->defaultOptions($options));
    }

    /**
     * Returns a quest area by ID
     * @param $id integer The ID of the quest area
     * @param array $options
     * @return mixed
     */
    public function area(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/area/$id", $this->defaultOptions($options));
    }

    /**
     * Returns an index of quest types (such as PvP quests, raid quests, or account quests)
     * @param array $options
     * @return mixed
     */
    public function types(array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/type/index", $this->defaultOptions($options));
    }

    /**
     * Returns a quest type by ID
     * @param $id integer The ID of the quest type
     * @param array $options
     * @return mixed
     */
    public function type(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->endpointUri()}/type/$id", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_WEEK;
        $this->endpoint = 'quest';
    }
}