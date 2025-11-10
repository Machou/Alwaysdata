<?php
require_once '../config/bdd.php';
require_once '../config/config.php';

if(!empty($_GET['chercher_ip']) AND (isIPv4($_GET['chercher_ip']) OR isIPv4($_GET['chercher_ip'])))
	header('Location: https://thisip.pw/ip/'.trim($_GET['chercher_ip']));

$ip = (!empty($_GET['ip']) AND (isIPv4($_GET['ip']) OR isIPv4($_GET['ip']))) ? secuChars($_GET['ip']) : getRemoteAddr();

# Routes

$pagesArray = [
	'/adresse-ip.php'				=> ['canonical'	=> '/adresse-ip',					'desc' => 'Adresse IP - '],
	'/analyse-web.php'				=> ['canonical'	=> '/analyse-web',					'desc' => 'Analyse Web - '],
	'/changements.php'				=> ['canonical'	=> '/changements',					'desc' => 'Changements - '],
	'/cidr.php'						=> ['canonical'	=> '/calculer-ip-sous-reseau',		'desc' => 'Calculer le sous-réseau - '],
	'/courriel.php'					=> ['canonical'	=> '/reputation-courriel',			'desc' => 'Vérifier la réputation d’un courriel - '],
	'/dns.php'						=> ['canonical'	=> '/dns',							'desc' => 'Qu’est-ce qu’un DNS et comment en changer - '],
	'/exif.php'						=> ['canonical'	=> '/exif',							'desc' => 'Retirer les données EXIF d’une image - '],
	'/index.php'					=> ['canonical'	=> '/',								'desc' => ''],
	'/legal-a-propos.php'			=> ['canonical'	=> '/a-propos',						'desc' => 'À Propos - '],
	'/legal-cgu.php'				=> ['canonical'	=> '/cgu',							'desc' => 'Conditions générales d’utilisation - '],
	'/legal-confidentialite.php'	=> ['canonical'	=> '/politique-de-confidentialite',	'desc' => 'Politique de confidentialité - '],
	'/rss.php'						=> ['canonical'	=> '/rss',							'desc' => 'RSS Finder'.((!empty($_POST['urlRss']) AND filter_var($_POST['urlRss'], FILTER_VALIDATE_URL)) ? ' - '.mb_strtolower(clean($_POST['urlRss'])) : null).' - '],
	'/securite-canary.php'			=> ['canonical'	=> '/canary',						'desc' => 'Cannary - '],
	'/securite-infos.php'			=> ['canonical'	=> '/securite',						'desc' => 'Informations de Sécurité - '],
	'/securite-pgp.php'				=> ['canonical'	=> '/pgp',							'desc' => 'Clé PGP - '],
	'/xkcd.php'						=> ['canonical' => '/xkcd',							'desc' => 'xkcd : Webcomics par Randall Munroe - ']
];

if($_SERVER['SCRIPT_NAME'] === '/analyse-web.php' AND !empty($_GET['uniqid']))
{
	$uniqid = (!empty($_GET['uniqid']) AND mb_strlen($_GET['uniqid']) === 15) ? trim(htmlspecialchars($_GET['uniqid'], ENT_QUOTES, 'UTF-8')) : null;

	$stmt = $pdo->prepare('SELECT * FROM whois WHERE uniqid = :uniqid');
	$stmt->execute(['uniqid' => (string) $uniqid]);
	$resSelect = $stmt->fetch();
}

$titleHead = (!empty($_GET['ip']) AND isIPv4($_GET['ip']))												? secuChars($_GET['ip']).' IPv4 - '									: null;
$titleHead = (!empty($_GET['ip']) AND isIPv6($_GET['ip']))												? secuChars($_GET['ip']).' IPv6 - '									: $titleHead;
$titleHead = !empty($pagesArray[$_SERVER['SCRIPT_NAME']]['desc'])										? $pagesArray[$_SERVER['SCRIPT_NAME']]['desc']						: $titleHead;
$titleHead = ($_SERVER['SCRIPT_NAME'] === '/analyse-web.php' AND !empty($resSelect['url_formulaire']))	? 'Analyse Web de '.secuChars($resSelect['url_formulaire']).' - '	: $titleHead;
$lienCanonical = !empty($pagesArray[$_SERVER['SCRIPT_NAME']]['canonical'])								? $pagesArray[$_SERVER['SCRIPT_NAME']]['canonical']					: null;
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title><?= $titleHead; ?>ThisIP.pw</title>

	<meta name="description" content="ThisIP est un outil complet pour la gestion des adresses IP, incluant des fonctionnalités d'affichage d'informations, de calcul de sous-réseaux, d'analyse de noms de domaine et de vérification de courriels, avec une touche ludique grâce à des dessins humoristiques de xkcd.">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="index, follow">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?= $lienCanonical; ?>">
	<meta property="og:title" content="<?= $titleHead; ?>ThisIP.pw">
	<meta property="og:description" content="API de géolocalisation par adresse IP">
	<meta property="og:image" content="https://thisip.pw/logo.png">
	<meta property="og:image:type" content="image/png">

	<!-- X (Twitter) -->
	<meta property="twitter:card" content="summary">
	<meta property="twitter:url" content="<?= $lienCanonical; ?>">
	<meta property="twitter:title" content="<?= $titleHead; ?>ThisIP.pw">
	<meta property="twitter:description" content="API de géolocalisation par adresse IP">
	<meta property="twitter:image" content="https://thisip.pw/logo.png">

	<link rel="icon" type="image/png" href="/favicon.png">
	<link rel="apple-touch-icon" sizes="192x192" href="/assets/img/apple-touch-icon-192x192.png">

	<link rel="stylesheet" href="/assets/css/vendors.css?<?= filemtime('assets/css/vendors.css'); ?>">
	<link rel="stylesheet" href="/assets/css/thisip.css?<?= filemtime('assets/css/thisip.css'); ?>">
	<?php
	// https://app.unpkg.com/@fingerprintjs/fingerprintjs@latest
	echo ($_SERVER['SCRIPT_NAME'] == '/index.php') ? '<script src="/assets/js/fp.js?'.filemtime('assets/js/fp.js').'"></script>'."\n" : null;

	if($_SERVER['SCRIPT_NAME'] == '/analyse-web.php')
	{
		// https://unpkg.com/browse/@highlightjs/cdn-assets@latest

		echo '<link rel="stylesheet" href="https://unpkg.com/@highlightjs/cdn-assets@11.11.1/styles/default.min.css">
		<script src="https://unpkg.com/@highlightjs/cdn-assets@11.11.1/highlight.min.js"></script>
		<script src="https://unpkg.com/@highlightjs/cdn-assets@11.11.1/languages/go.min.js"></script>'."\n";

		// https://unpkg.com/browse/js-beautify@latest/js/lib/

		echo '<script src="https://unpkg.com/js-beautify@1.15.4/js/lib/beautifier.min.js"></script>
		<script src="https://unpkg.com/js-beautify@1.15.4/js/lib/beautify-css.js"></script>
		<script src="https://unpkg.com/js-beautify@1.15.4/js/lib/beautify-html.js"></script>'."\n";
	}
	?>
	<script src="/assets/js/vendors.js?<?= filemtime('assets/js/vendors.js'); ?>"></script>

	<?= !empty($lienCanonical) ? '<link rel="canonical" href="https://thisip.pw'.$lienCanonical.'">'."\n" : null; ?>
</head>

<body>
<div class="container">
	<nav class="navbar navbar-expand-lg bg-light border border-2 border-top-0 rounded-bottom" id="navbar">
		<div class="container-fluid">
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Afficher / masquer la navigation"><span class="navbar-toggler-icon"></span></button>
			<a href="https://thisip.pw/" class="navbar-brand me-3"><i style="color: rgba(0,31,63, 1);" class="fa-solid fa-house"></i> ThisIP.pw</a>
			<div class="collapse navbar-collapse text-center" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mt-3 mt-lg-0">
					<li class="nav-item"><a href="/analyse-web" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/analyse-web.php' ? ' active' : null); ?>"><i class="fa-solid fa-expand"></i> Analyse Web</a></li>
					<li class="nav-item"><a href="/adresse-ip" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/adresse-ip.php' ? ' active' : null); ?>"><i class="fa-solid fa-circle-nodes"></i> Adresse IP</a></li>
					<li class="nav-item"><a href="/dns" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/dns.php' ? ' active' : null); ?>"><i class="fa-solid fa-robot"></i> DNS</a></li>
					<li class="nav-item"><a href="/calculer-ip-sous-reseau" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/cidr.php' ? ' active' : null); ?>"><i class="fa-solid fa-network-wired"></i> Sous-réseau</a></li>
					<li class="nav-item"><a href="/reputation-courriel" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/courriel.php' ? ' active' : null); ?>"><i class="fa-solid fa-at"></i> Réputation Courriel</a></li>
					<li class="nav-item"><a href="/rss" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/rss.php' ? ' active' : null); ?>"><i class="fa-solid fa-rss"></i> RSS Finder</a></li>
					<li class="nav-item"><a href="/exif" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/exif.php' ? ' active' : null); ?>"><i class="fa-regular fa-image"></i> EXIF</a></li>
					<li class="nav-item"><a href="/xkcd" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/xkcd.php' ? ' active' : null); ?>"><i class="fa-regular fa-comment-dots"></i> xkcd</a></li>
				</ul>
			</div>
		</div>
	</nav>
