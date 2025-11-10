<?php
header('Content-Type: application/xml');

echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>https://www.diskigo.com/</loc>
		<lastmod>'.date('c', filemtime('index.php')).'</lastmod>
		<changefreq>daily</changefreq>
		<priority>1.00</priority>
	</url>';

	// Routes

	$langs = ['us', 'uk', 'de', 'es', 'fr', 'it', 'ca', 'au', 'in', 'se'];

	foreach($langs as $lang)
	{
		if(is_file('donnees/'.$lang.'.txt') AND filesize('donnees/'.$lang.'.txt') > 0)
		{
			echo '<url>
				<loc>https://www.diskigo.com/'.$lang.'</loc>
				<lastmod>'.date('c', filemtime('donnees/'.$lang.'.txt')).'</lastmod>
				<priority>0.80</priority>
			</url>';
		}
	}

echo '</urlset>';