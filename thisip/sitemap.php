<?php
header('Content-Type: application/xml');

echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>https://thisip.pw/</loc>
		<lastmod>'.date('c', filemtime('index.php')).'</lastmod>
		<changefreq>daily</changefreq>
		<priority>1.00</priority>
	</url>';

	// Routes

	$urls = [
		'.well-known/security.txt'	=> '.well-known/security.txt',
		'.well-known/pgp.txt'		=> '.well-known/pgp.txt',
		'adresse-ip.php'			=> 'adresse-ip',
		'analyse-web.php'			=> 'analyse-web',
		'changements.php'			=> 'changements',
		'cidr.php'					=> 'calculer-ip-sous-reseau',
		'courriel.php'				=> 'reputation-courriel',
		'dns.php'					=> 'dns',
		'exif.php'					=> 'exif',
		'legal-a-propos.php'		=> 'a-propos',
		'legal-cgu.php'				=> 'cgu',
		'legal-confidentialite.php'	=> 'politique-de-confidentialite',
		'robots.txt'				=> 'robots.txt',
		'rss.php'					=> 'rss',
		'securite-canary.php'		=> 'canary',
		'securite-infos.php'		=> 'securite',
		'securite-pgp.php'			=> 'pgp',
		'xkcd.php'					=> 'xkcd',
	];

	foreach($urls as $fichier => $u)
	{
		if(is_file($fichier) AND filesize($fichier) > 0)
		{
			echo '<url>
				<loc>https://thisip.pw/'.$u.'</loc>
				<lastmod>'.date('c', filemtime($fichier)).'</lastmod>
				<priority>0.80</priority>
			</url>';
		}
	}

echo '</urlset>';