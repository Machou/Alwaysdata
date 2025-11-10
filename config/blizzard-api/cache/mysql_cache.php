	<?php

	namespace BlizzardApi\Cache;

class MySQLCache implements CacheInterface
{
	const CACHE_INSERT_STMT = <<<SQL
	INSERT INTO `blizzard_api_cache` (
	`cache_key`,
	`data`,
	`ttl`
	) VALUES (
	:key,
	:data,
	:ttl
	) ON DUPLICATE KEY UPDATE `data`=VALUES(`data`), `ttl`=VALUES(`ttl`);
	SQL;

	const CACHE_SELECT_STMT = <<<SQL
	SELECT `data`
	FROM `blizzard_api_cache`
	WHERE DATE_ADD(updated_at, INTERVAL ttl SECOND) >= now()
	AND `cache_key` = :key;
	SQL;

	/**
	 * MySQL connection
	 * @var PDO $connection
	 */
	protected $connection;

	/**
	 * MySQLCache is used to cache API requests using MySQL
	 * @param PDO|null $connection If you already have a MySQL connection you can use instead of creating a new one.
	 */
	public function __construct(?PDO $connection = null) {
		$this->connection = $connection;
	}

	public function retrieve($url, &$data) {
		$cache_key = md5($url);
		$stmt = $this->connection->prepare(self::CACHE_SELECT_STMT);
		if($stmt->execute(['key' => $cache_key]) && $stmt->rowCount() > 0) {
		$data = $stmt->fetch(PDO::FETCH_NUM)[0];
		return true;
		}
		$data = null;
		return false;
	}

	public function store($url, $data, $ttl) {
		$cache_key = md5($url);
		$stmt = $this->connection->prepare(self::CACHE_INSERT_STMT);
		return $stmt->execute(['key' => $cache_key, 'data' => $data, 'ttl' => $ttl]);
	}

	/**
	 * Create the cache table, it is intended to be called just once when installing the cache.
	 * @param $recreate boolean If true will force recreate the table. ALL DATA WILL BE LOST.
	 * @return boolean False if any error occurred.
	 */
	public function setupCache($recreate = false) {
		try {
		$script = '';

		if($recreate) {
			$script .= <<<'SQL'
	DROP TABLE IF EXISTS `blizzard_api_cache`;
	SQL;
		}
		$script .= <<<'SQL'
	CREATE TABLE `blizzard_api_cache` (
	`cache_key` CHAR(32) NOT NULL COMMENT 'Cache key. MD5 hash of the full URL.',
	`data` LONGTEXT NOT NULL COMMENT 'API response',
	`updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last updated time',
	`ttl` INT UNSIGNED NOT NULL DEFAULT 86400 COMMENT 'Time to live in seconds. Defaults to 24h.',
	PRIMARY KEY (`cache_key`));
	SQL;
		return $this->connection->exec($script) !== false;
		} catch (Exception $e) {
		return false;
		}
	}
}