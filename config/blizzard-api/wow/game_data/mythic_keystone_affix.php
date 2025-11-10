<?php

namespace BlizzardApi\Wow\GameData;

class MythicKeystoneAffix extends GenericDataEndpoint
{
    /**
     * Returns media for a mythic keystone affix by ID
     * @param $id int The ID of the mythic keystone affix
     * @param $options array Request options
     * @return mixed
     */
    public function media(int $id, array $options = [])
    {
        return $this->apiRequest("{$this->baseUrl('media')}/keystone-affix/$id", $this->defaultOptions($options));
    }

    protected function endpointSetup()
    {
        $this->namespace = STATIC_NAMESPACE;
        $this->ttl = self::CACHE_TRIMESTER;
        $this->endpoint = 'keystone-affix';
    }
}