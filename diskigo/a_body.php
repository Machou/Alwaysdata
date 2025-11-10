<?php
$languesArray = ['us', 'uk', 'de', 'es', 'fr', 'it', 'ca', 'au', 'in', 'se', 'ie'];
$locale = (!empty($_GET['locale']) AND mb_strlen($_GET['locale']) === 2 AND in_array($_GET['locale'] ?? '', $languesArray)) ? secuChars($_GET['locale']) : 'fr';

$marques = ['Acer', 'Avolusion', 'Crucial', 'HGST', 'Hitachi', 'Intel', 'Kingston', 'LaCie', 'Lexar', 'Patriot', 'Samsung', 'SanDisk', 'Seagate', 'Sony', 'Synology', 'Toshiba', 'Verbatim', 'Western Digital', 'WD_Black', 'XPG'];
$recherche = (!empty($_GET['recherche']) AND in_array($_GET['recherche'] ?? '', $marques)) ? secuChars($_GET['recherche']) : null;

# Routes

$pagesArray = [
	'/maj.php'				=> ['canonical'	=> 'https://www.diskigo.com/maj',			'desc' => 'MàJ - '],
	'/index.php'			=> ['canonical'	=> 'https://www.diskigo.com/',				'desc' => 'Marque : '.(!empty($_GET['recherche']) ? $recherche : null).' - Prix des disques dur, disques internes, disques externes, clés USB, etc. - '],
	'/securite-canary.php'	=> ['canonical'	=> 'https://www.diskigo.com/canary',		'desc' => 'Canary - '],
	'/securite-infos.php'	=> ['canonical'	=> 'https://www.diskigo.com/securite',		'desc' => 'Informations de Sécurité - '],
	'/securite-pgp.php'		=> ['canonical'	=> 'https://www.diskigo.com/pgp',			'desc' => 'Clé PGP - '],
];

$titleHead = !empty($pagesArray[$_SERVER['SCRIPT_NAME']]['desc'])						? $pagesArray[$_SERVER['SCRIPT_NAME']]['desc']			: null;
$lienCanonical = !empty($pagesArray[$_SERVER['SCRIPT_NAME']]['canonical'])				? $pagesArray[$_SERVER['SCRIPT_NAME']]['canonical']		: null;
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title><?= $titleHead; ?>Diskigo.com</title>

	<meta name="description" content="Comparaison de tous les disques dur interne, externe, SSD, clés USB, etc. sur les différentes versions d’Amazon, prix triés par Teraoctet et Gigaoctet.">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="index, follow">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://www.diskigo.com/">
	<meta property="og:title" content="Diskigo">
	<meta property="og:description" content="Comparatif de disques dur interne, externe, SSD, clés USB, etc. sur Amazon">
	<meta property="og:image" content="https://www.diskigo.com/logo.png">
	<meta property="og:image:type" content="image/png">

	<!-- X -->
	<meta property="twitter:card" content="summary">
	<meta property="twitter:url" content="https://www.diskigo.com/">
	<meta property="twitter:title" content="Diskigo">
	<meta property="twitter:description" content="Comparatif de disques dur interne, externe, SSD, clés USB, etc. sur Amazon">
	<meta property="twitter:image" content="https://www.diskigo.com/logo.png">

	<link rel="icon" type="image/ico" href="favicon.ico">
	<link rel="apple-touch-icon" sizes="192x192" href="/assets/img/apple-touch-icon-192x192.png">

	<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.8/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.25.0/dist/bootstrap-table.min.css">
	<link rel="stylesheet" href="https://unpkg.com/@fortawesome/fontawesome-free@7.1.0/css/all.min.css">
	<link rel="stylesheet" href="/assets/css/style.css?<?= filemtime('assets/css/style.css'); ?>">

	<script src="https://unpkg.com/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://unpkg.com/jquery@3.7.1/dist/jquery.min.js"></script>
	<script src="https://unpkg.com/bootstrap-table@1.25.0/dist/bootstrap-table.min.js"></script>
	<script src="https://unpkg.com/bootstrap-table@1.25.0/dist/locale/bootstrap-table-fr-FR.min.js"></script>

	<?php
	echo '<link rel="canonical" href="https://www.diskigo.com/'.($locale.(!empty($recherche) ? '/'.$recherche : null)).'">';
	?>
</head>

<body>
