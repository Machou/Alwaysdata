<?php
session_start();

require_once 'a_body.php';

function getRSSLocation(string $url): ?array
{
	$html = get($url);

	$fluxRss = [];

	if(!empty($html))
	{
		$base = parse_url($url);
		$domaine = $base['scheme'].'://'.$base['host'];

		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($html);
		libxml_clear_errors();

		$xpath = new DOMXPath($dom);

		$hrefs = $xpath->query('//a[@aria-label="AASyndication" or contains(@href, "/syndication/rss/news/0")]');

		foreach($hrefs as $href)
		{
			$fluxRss[] = $domaine.$href->getAttribute('href');
		}

		$liens = $dom->getElementsByTagName('link');

		foreach($liens as $lien)
		{
			$rel = $lien->getAttribute('rel');
			$type = $lien->getAttribute('type');

			if($rel === 'alternate' || $type === 'application/rss+xml' || $type === 'application/atom+xml')
			{
				$href = $lien->getAttribute('href');
				$e = ((!preg_match('/http/i', $href) AND !empty($href)) ? $domaine : null).$href;
				$fluxRss[] = trim($e);
				// $fluxRss[] = str_replace('//', '/', $e);

				// if(!is_null($rssUrl))
				// {
				// 	libxml_use_internal_errors(true);

				// 	$rss = get($domaine);
				// 	// $rss = get($rssUrl);

				// 	if($rss !== false AND !empty($rss))
				// 	{
				// 		$rss = simplexml_load_string($rss);
				// 		if($rss !== false)
				// 			$fluxRss[] = $rssUrl;

				// 		libxml_clear_errors();
				// 	}
				// }
			}
		}
	}

	return !empty($fluxRss) ? $fluxRss : null;
}

$urlRss = (!empty($_POST['urlRss']) AND filter_var($_POST['urlRss'], FILTER_VALIDATE_URL)) ? mb_strtolower(clean($_POST['urlRss'])) : null;

if(!empty($urlRss))
{
	if(CSRF::verifier($_POST['jetonCSRF'], 'formRSS'))
	{
		$liensRss = getRSSLocation($urlRss);

		if(!empty($liensRss))
		{
			$liensRss = array_unique($liensRss);
			$i = 1;
			$nbFlux = count($liensRss);

			foreach($liensRss as $lienRss)
			{
				$rssHtml[] = '<div class="row'.($i !== $nbFlux ? ' mb-3' : null).'">
					<div class="col-12 col-lg-6 mx-auto mb-3">
						<div class="input-group">
							'.($nbFlux > 1 ? '<span class="input-group-text">#'.$i.'</span>' : null).'
							<input type="text" value="'.secuChars($lienRss).'" class="form-control form-control-lg curseur">
							<button class="btn btn-outline-success btn-copie" data-type="rss" data-bs-toggle="tooltip" data-bs-title="Copier vers le presse-papiers" data-clipboard-text="'.secuChars($lienRss).'"><i class="fa-regular fa-clipboard"></i></button>
						</div>
					</div>
				</div>';

				$i++;
			}
		}

		else
			$erreur = alerte('danger', 'Aucun flux RSS trouvé');
	}

	else
		$erreur = alerte('danger', 'Jeton CSRF incorrect');
}

echo '<div class="border rounded">
	<h1 class="mb-5 text-center"><a href="/rss"><i class="fa-solid fa-rss"></i> RSS Finder</a></h1>

	<form method="post" id="rssForm">
		<div class="row">
			<div class="col-12 col-lg-5 mx-auto">
				<div class="input-group input-group-thisip">
					<span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
					<input type="text" name="urlRss" '.(!empty($urlRss) ? 'value="'.$urlRss.'"' : null).' class="form-control form-control-lg" placeholder="https://www.google.com/flux.rss" '.(!empty($_POST['urlRss']) ? 'autofocus' : null).' required>
					<input type="hidden" name="jetonCSRF" value="'.CSRF::generer('formRSS').'">
					<button type="submit" class="btn btn-primary" form="rssForm">Valider</button>
				</div>
			</div>
		</div>
	</form>

	<div class="mt-5 p-3 text-primary-emphasis bg-primary-subtle border border-primary-subtle rounded-3">
		<div class="d-flex align-items-center justify-content-center">
			<i class="fa-solid fa-circle-info fs-1 me-3"></i>
			<span>Vous cherchez un <strong>flux RSS</strong> sur un blog ou un site ? Copiez l’adresse du site dans formulaire ci-dessus pour afficher les flux RSS du site distant.</span>
		</div>
	</div>';

	echo !empty($erreur) ? $erreur : null;

	echo !empty($rssHtml) ? '<div class="mt-5"><h3 class="text-center mb-4">Liste des flux RSS</h3>'.implode($rssHtml).'</div>' : null;

echo '</div>';

require_once 'a_footer.php';