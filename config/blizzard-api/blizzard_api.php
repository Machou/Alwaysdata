<?php

namespace BlizzardApi;

function resolveClassPath($class_name): string
{
	$class_path = [];
	foreach(explode("\\", $class_name) as $part) {
		if($part === 'BlizzardApi') {
			continue;
		}
		$snake = preg_replace('/[A-Z]/', '_$0', $part);
		$snake = ltrim(mb_strtolower($snake), '_');
		$class_path[] = $snake;
	}
	return implode(DIRECTORY_SEPARATOR, $class_path).'.php';
}

function blizzardAPIAutoload($name)
{
	$filename = __DIR__ . DIRECTORY_SEPARATOR . resolveClassPath($name);
	if(file_exists($filename)) {
		require_once $filename;
	}
}

spl_autoload_register('BlizzardApi\blizzardAPIAutoload');

require_once __DIR__.'/config.php';
require_once __DIR__.'/request_handler.php';
require_once __DIR__.'/wow/wow.php';