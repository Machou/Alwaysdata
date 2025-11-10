<?php

namespace BlizzardApi\Wow;

class Request extends \BlizzardApi\Request
{
    public function __construct($region, $accessToken = null, $cache = null)
    {
        parent::__construct($region, $accessToken, $cache);

        $this->game = 'wow';
    }
}
