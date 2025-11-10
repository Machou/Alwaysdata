<?php

namespace BlizzardApi\Test;

require_once "blizzard_api/blizzard_api.php";
require_once "blizzard_api/cache/redis_cache.php";

use BlizzardApi;
use BlizzardApi\Wow\Wow;
use RedisException;

// Configuration
BlizzardApi\Config::$apiKey = $_SERVER['BNET_APPLICATION_ID'];
BlizzardApi\Config::$apiSecret = $_SERVER['BNET_APPLICATION_SECRET'];

function loadTestFiles($dir) {
	$files = glob($dir.'/*.php');
	foreach($files as $file) {
		require $file;
	}
}

loadTestFiles(__DIR__.'/community');
loadTestFiles(__DIR__.'/game_data');
loadTestFiles(__DIR__.'/profile');

class ApiTest
{
	/**
	 * @var Wow $Wow
	 */
	protected static $Wow;

	protected static $tests = 0;
	protected static $assertions = 0;
	protected static $failedAssertions = 0;

	/**
	 * @throws RedisException
	 */
	public static function init()
	{
		$cache = null;
		if($_SERVER['USE_REDIS']) {
			$redisConnection = new \Redis();
			$redisConnection->connect($_SERVER['REDIS_HOST'], $_SERVER['REDIS_PORT']);

			$cache = new BlizzardApi\Cache\RedisCache($redisConnection);
		}
		self::$Wow = new BlizzardApi\Wow\Wow('us', $cache);
	}

	public static function getStatistics() {
		return [self::$assertions, self::$tests, self::$failedAssertions];
	}

	public function run($methodName) {
		$className = get_class($this);
		$testMethods = preg_grep("/^test.*$methodName.*/", get_class_methods($className));
		foreach($testMethods as $method) {
			self::$tests += 1;
			printf("Testing %s->%s\n", $className, $method);
			$this->$method();
			usleep(500000);
		}
	}

	protected function assert($value) {
		self::$assertions += 1;
		if(!$value) {
		$this->printBacktrace("Expected true, got false");
		}
	}

	protected function printBacktrace($reason) {
		self::$failedAssertions += 1;
		$stack = debug_backtrace()[1];
		printf("%s at %s:%d \n", $reason, $stack['file'], $stack['line']);
	}

	protected function assertEqual($expected, $actual) {
		self::$assertions += 1;
		if($expected !== $actual) {
		$printableActual = print_r($actual, true);
		$this->printBacktrace("Expected $expected, got $printableActual");
		}
	}

	protected function assertGreaterThanOrEqual($expected, $actual) {
		self::$assertions += 1;
		if($expected > $actual) {
		$printableActual = print_r($actual, true);
		$this->printBacktrace("Expected $expected, got $printableActual");
		}
	}

	protected function assertArrayKeyExists($key, $array) {
		self::$assertions += 1;
		if(!array_key_exists($key, $array)) {
		$this->printBacktrace("Array does not have the key '$key'");
		}
	}
}

try {
	ApiTest::init();
} catch (RedisException $e) { }

$methodPattern = $argv[1] ?? $methodPattern = '.*';

$testClasses = preg_grep('/^BlizzardApi\\\\Test\\\\.*/', get_declared_classes());

foreach($testClasses as $testClass) {
	usleep(750000);

	if($testClass === 'BlizzardApi\\Test\\ApiTest') {
		continue;
	}

	$obj = new $testClass();
	$obj->run($methodPattern);
}

[$assertions, $tests, $failedAssertions] = ApiTest::getStatistics();

vprintf("Executed %d assertions in %d tests, %d assertions failed.\n", [$assertions, $tests, $failedAssertions]);

if($failedAssertions > 0) {
	exit(1);
}
