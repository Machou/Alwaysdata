<?php

namespace BlizzardApi\Cache;

interface CacheInterface
{
	/**
	 * @param $url string Complete URL used as cache key
	 * @param $data string Empty variable to return the data if cache was found and valid.
	 * @return boolean If cache key was found and data is still valid (below max ttl) returns true, false otherwise.
	 * Whenever the result is false, $data will be null.
	 */
	function retrieve($url, &$data);

	/**
	 * @param $url string Complete URL used as cache key
	 * @param $data string A valid JSON string to store.
	 * @param $ttl int Max time to live (in seconds).
	 * @return void
	 * @throws
	 */
	function store($url, $data, $ttl);
}