<?php
header('Content-Type: application/xml');

echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>https://hdv.li/</loc>
		<lastmod>'.date('c', filemtime('index.php')).'</lastmod>
		<changefreq>daily</changefreq>
		<priority>1.00</priority>
	</url>';

	// Routes

	$urls = [
		'addons.php'						=> 'addons',
		'mascottes.php'						=> 'mascottes',
		'jeton.php'							=> 'historique-des-prix-du-jeton-wow',
		'legal-cgu.php'						=> 'cgu',
		'legal-confidentialite.php'			=> 'politique-de-confidentialite',
		'profil_connexion.php'				=> 'connexion',
		'profil_inscription.php'			=> 'inscription',
		'profil_mot_de_passe_changer.php'	=> 'changer-mot-de-passe',
		'profil.php'						=> 'profil',
		'serveurs.php'						=> 'serveurs',
	];

	foreach($urls as $fichier => $u)
	{
		if(is_file($fichier) AND filesize($fichier) > 0)
		{
			echo '<url>
				<loc>https://hdv.li/'.$u.'</loc>
				<lastmod>'.date('c', filemtime($fichier)).'</lastmod>
				<priority>0.80</priority>
			</url>';
		}
	}

echo '</urlset>';