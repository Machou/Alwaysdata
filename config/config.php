<?php
declare(strict_types=1);

ini_set('display_errors', 1);
error_reporting(E_ALL);
setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR', 'fr');
date_default_timezone_set('Europe/Paris');

function microtime_float()
{
	list($usec, $sec) = explode(' ', microtime());

	return ((float) $usec + (float) $sec);
}

$time_start = microtime_float();

require_once 'cookie.php';

require_once __DIR__.'/../../divers/composer/vendor/autoload.php';

require_once 'fonctions.php';
require_once 'class.csrf.php';
require_once 'class.pagination.php';
require_once 'class.grab.php';
require_once 'class.imdb.php';
require_once 'class.tmdb.php';
require_once 'class.tmdb.parser.php';

$onchange		= 'onchange="this.form.submit();"';
$onclick		= 'onclick="window.open(this.href); return false;"';
$onclickSelect	= 'onclick="this.select()"';