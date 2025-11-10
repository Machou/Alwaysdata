<?php

namespace BlizzardApi\Wow\GameData;

use Error;

class GuildCrest extends GenericDataEndpoint
{
    public function get($id, $options = [])
    {
        throw new Error('The GuildCrest endpoint does not have a get method.');
    }

    /**
     * Returns media for a guild crest border by ID
     * @param $id int The ID of the guild crest border
     * @param $options array Request options
     * @return mixed
     */
    public function borderMedia(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/$this->endpoint/border/$id", $this->defaultOptions($options));
    }

    /**
     * Returns media for a guild crest emblem by ID
     * @param $id int The ID of the guild crest emblem
     * @param $options array Request options
     * @return mixed
     */
    public function emblemMedia(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/$this->endpoint/emblem/$id", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'guild-crest';
    }
}