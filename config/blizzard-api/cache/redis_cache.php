<?php

namespace BlizzardApi\Cache;

use Redis;

class RedisCache implements CacheInterface
{
	/**
	 * Redis connection
	 * @var Redis $connection
	 */
	protected $connection;

	/**
	 * RedisCache is used to cache API requests using Redis
	 * @param Redis|null $connection A Redis connection.
	 */
	public function __construct(Redis $connection) {
		$this->connection = $connection;
	}

	public function retrieve($url, &$data) {
		$data = $this->connection->get($url);
		return $data !== false;
	}

	public function store($url, $data, $ttl) {
		return $this->connection->setex($url, $ttl, $data);
	}
}