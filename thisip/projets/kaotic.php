<?php
require_once '../../config/config.php';

function getKaoticVideoNom(string $titre): string
{
	return (string) $_SERVER['DOCUMENT_ROOT'].'assets/cache/kaotic/'.$titre.'.mp4';
}

function getKaoticDerniereVideo()
{
	$fichiers = array_diff(scandir($_SERVER['DOCUMENT_ROOT'].'assets/cache/kaotic/'), ['..', '.']);

	foreach($fichiers as $fichier)
	{
		$f = filemtime($_SERVER['DOCUMENT_ROOT'].'assets/cache/kaotic/'.$fichier);

		if(time() > $f)
			return (string) $f;
	}
}

function getFicheKaotic(string $lien): ?array
{
	$get = get($lien, 'GET', [
		'headers' => [
			'Accept'			=> 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
			'Accept-Language'	=> 'fr-FR,en-US;q=0.9,en;q=0.8',
			'Cache-Control'		=> 'no-cache',
			'Referer'			=> 'https://kaotic.com/',
			'User-Agent'		=> getRandomUserAgent(),
		]
	]);

	if(empty($get))
		return null;

	$res = [];

	libxml_use_internal_errors(true);
	$doc = new DOMDocument();
	$doc->loadHTML('<?xml encoding="utf-8" ?>' . $get);
	libxml_clear_errors();

	foreach ($doc->getElementsByTagName('meta') as $m) {
		$tag = $m->getAttribute('name') ?: $m->getAttribute('property');

		if($tag === '')
			continue;

		if(in_array($tag, ['description', 'keywords'], true) OR str_starts_with($tag, 'og:'))
		{
			$content = trim($m->getAttribute('content'));

			if($content !== '')
				$res[$tag] = $content;
		}
	}

	if(preg_match('/Submitted\s+on\s+(\d{2}\/\d{2}\/\d{4})/i', $get, $m)) {
		$res['date'] = $m[1];
	}

	if(!empty($res['og:video:url']))	$res['video'] = $res['og:video:url'];
	elseif(!empty($res['og:video']))	$res['video'] = $res['og:video'];

	return $res ?: null;
}

$categories = ['accident' => 'Accident', 'fight' => 'Combat', 'funny' => 'Fun', 'war' => 'Guerre', 'justice' => 'Justice','protests' => 'Manifs', 'medical' => 'Médical', 'reposts' => 'Reposts', 'shooting' => 'Tirs', 'robbery' => 'Vols', 'wtf' => 'WTF'];

$catUrl = (!empty($_GET['categorie']) AND array_key_exists($_GET['categorie'], $categories))	? clean(secuChars($_GET['categorie']))	: null;
$search = !empty($_POST['search'])																? clean(secuChars($_POST['search']))	: null;
$idFiche = !empty($_POST['idFiche'])															? clean(secuChars($_POST['idFiche']))	: null;

if(!empty($_POST['action']) AND $_POST['action'] == 'dl')
{
	header('Content-Type: application/json; charset=utf-8');

	if(empty(getKaoticDerniereVideo()) OR getKaoticDerniereVideo() < (time() - (10)))
	{
		if(!empty($idFiche))
		{
			$getFicheTelechargement = getFicheKaotic('https://www.kaotic.com/video/'.$idFiche);

			if(!empty($getFicheTelechargement))
			{
				$lienVideoTelechargement = !empty($getFicheTelechargement['og:video:url']) ? trim($getFicheTelechargement['og:video:url']) : null;

				if(!empty($lienVideoTelechargement) AND filter_var($lienVideoTelechargement, FILTER_VALIDATE_URL) AND preg_match('/(https:\/\/|kaotic|mp4)/is', $lienVideoTelechargement))
				{
					if(!is_file(getKaoticVideoNom($idFiche)))
					{
						$fileContents = file_get_contents($lienVideoTelechargement);
						if($fileContents !== false)
						{
							if(file_put_contents(getKaoticVideoNom($idFiche), $fileContents))
								echo json_encode(['status' => true, 'message' => 'La vidéo <span class="fw-bold">'.$idFiche.'.mp4</span> a été téléchargée']);

							else
								echo json_encode(['status' => false, 'message' => 'Impossible de sauvegarder la vidéo']);
						}

						else
							echo json_encode(['status' => false, 'message' => 'Erreur lors du téléchargement de la vidéo']);
					}

					else
						echo json_encode(['status' => false, 'message' => 'Erreur, la vidéo est déjà présente']);
				}

				else
					echo json_encode(['status' => false, 'message' => 'Le lien de la vidéo est incorrect']);
			}

			else
				echo json_encode(['status' => false, 'message' => 'Impossible de récupérer la fiche Kaotic']);
		}

		else
			echo json_encode(['status' => false, 'message' => 'Erreur de formulaire']);
	}

	else
		echo json_encode(['status' => false, 'message' => 'Attendez quelques secodnes avant de retélécharger une vidéo']);
}

elseif(!empty($_GET['fichier']))
{
	$fichier = realpath($_SERVER['DOCUMENT_ROOT'].'/assets/cache/kaotic/'.secuChars($_GET['fichier']).'.mp4');

	if(is_file($fichier) AND filesize($fichier) > 0 AND getExt($fichier) == 'mp4')
	{
		if(!empty($_GET['action']) AND $_GET['action'] == 'telecharger')
		{
			header('Cache-Control: must-revalidate');
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename='.basename($fichier));
			header('Content-Type: '.mime_content_type($fichier));
			header('Expires: 0');
			header('Pragma: public');

			ob_clean();
			flush();

			readfile($fichier);

			header('Location: kaotic');
			exit;
		}

		elseif(!empty($_GET['action']) AND $_GET['action'] == 'supprimer')
		{
			if(!empty($fichier))
			{
				unlink($fichier);

				header('Location: kaotic?msg=videoSupprimmee');
			}

			else
				header('Location: kaotic?msg=videoInconnue');
		}

		else
			header('Location: kaotic?msg=actionInconnue');
	}
}

else
{
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title><?= ((!empty($_GET['categorie']) AND $catUrl == $_GET['categorie']) ? 'Catégorie : '.$categories[$_GET['categorie']].' - ' : null); ?>Kaotic</title>

	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="none, noarchive">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<?= isset($_GET['supprimer']) ? '<meta http-equiv="refresh" content="5; url=https://thisip.pw/projets/kaotic'.(!empty($catUrl) ? '?categorie=' : null).'">' : null; ?>

	<link rel="icon" type="image/png" href="/favicon.png">
	<link rel="stylesheet" href="/assets/css/vendors.css?<?= filemtime('../assets/css/vendors.css'); ?>">

	<style>
	body					{ background-color: #dfe1eb; }
	img						{ height: 165px; max-width: 350px; width: 100%; }
	video, .vignette		{ border-radius: .25rem !important; height: 165px; width: 100%; }
	.link-title:hover		{ background-color: #ccc; border-radius: .25rem; text-decoration: none !important; }
	.badge-liste a			{ text-decoration: none; padding: .5rem; margin-bottom: .25rem; }
	.badge-liste a:hover	{ color: white; opacity: .75; }
	.badge-liste > .bg-info { margin-right: .25rem; }
	.badge-liste > .bg-info:last-child { margin-right: 0; }

	.border.rounded.mx-auto.p-2.text-center.h-100 > div:first-child	{ height: 165px; }
	.curseur														{ cursor: pointer; }

	@media (min-width: 992px) {
		.w-lg-50	{ width: 50% !important; }
		.w-lg-25	{ width: 25% !important; }

		.badge-liste a {
			font-size: 1.1rem;
			line-height: 1.2;
			margin-bottom: 0;
		}
	}
	</style>

	<script src="/assets/js/vendors.js?<?= filemtime('../assets/js/vendors.js'); ?>"></script>
</head>

<body>
<div class="container p-lg-0">
	<div class="row bg-light rounded-bottom mb-4 py-3" id="header">
		<div class="col-12 col-lg-3 mb-3 mb-lg-0">
			<form action="kaotic" method="post" id="searchKaotic">
				<div class="input-group">
					<input type="text" name="search" <?= !empty($_POST['search']) ? 'value="'.secuChars($_POST['search']).'"' : null; ?> class="form-control curseur" minlength="2" maxlength="50" placeholder="Mot clé…" required>
					<button class="btn btn-outline-secondary" type="submit" form="searchKaotic"><i class="fa-solid fa-magnifying-glass"></i></button>
				</div>
			</form>
		</div>
		<div class="col-12 col-lg-9 badge-liste text-center">
			<a href="kaotic" class="badge bg-success" title="Accueil"><i class="fa-solid fa-house"></i></a>
			<a href="https://kaotic.com/" class="badge bg-danger" <?= $onclick; ?> title="Kaotic.com"><i class="fa-solid fa-k"></i></a>
			<a href="?dernieres" class="badge bg-dark<?= (isset($_GET['dernieres']) ? ' opacity-50' : null); ?>">Dernières</a>

			<?php
			foreach($categories as $categorie => $categorieReal)
				echo '<a href="?categorie='.$categorie.'" class="badge bg-info'.($catUrl == $categorie ? ' opacity-50' : null).'">'.$categorieReal.'</a>';
			?>
		</div>
	</div>

	<div class="my-4 text-center" id="reponse"></div>
	<div style="display: none;" class="my-4 text-center" id="chargement">
		<img src="/assets/img/chargement.svg" style="height: 75px;" class="d-flex mx-auto" alt="Chargement…" title="Chargement…">
		<p class="bg-success-subtle w-100 w-lg-25 shadow-sm mx-auto mb-0 p-3 rounded text-center fw-bold">Téléchargement de la vidéo…</p>
	</div>
	<?php
	$msg = [
		'videoSupprimmee' => alerte('success', 'La vidéo a été supprimée'),
		'erreurIdFiche' => alerte('danger', 'Erreur nom sur l’ID fiche'),
		'videoInconnue' => alerte('danger', 'Vidéo inconnue'),
		'actionInconnue' => alerte('danger', 'Action non reconnue'),
	];

	echo !empty($_GET['msg']) ? $msg[$_GET['msg']] : null;

	echo '<div class="row bg-light rounded" id="form-container">';

		if(isset($_GET['dernieres']))	$urlKaotic = 'https://kaotic.com/recent/';
		elseif(!empty($catUrl))			$urlKaotic = 'https://kaotic.com/category/'.$catUrl;
		elseif(!empty($search))			$urlKaotic = 'https://kaotic.com/mediaSearch/?search='.$search;
		else							$urlKaotic = 'https://kaotic.com/recent/';

		$getKaotic = get($urlKaotic);

		if(!empty($getKaotic))
		{
			preg_match_all('/<div class="col-xs-6 col-sm-6 col-md-3 hard-4">(.*)<div class="video">(.*)<div class="video-image">(.*)<a href="(.*)" title="(.*)"><img src="(?P<fiche_image>.*)" alt="(.*)" onerror="(.*)"><\/a>(.*)<\/div>(.*)<h2 class="video-title"><a href="https:\/\/kaotic\.com\/video\/(?P<fiche_id>.*)" title="(?P<nom_video>.*)">(.*)<\/a><\/h2>(.*)<div class="info info_new">(.*)<span class="author_cat">(.*)By: <a href="https:\/\/kaotic\.com\/user\/(.*)\/" title="(.*)" class="user-profile">(.*)<\/a>(.*)<\/span>(.*)<a class="comm-count" href="https:\/\/kaotic.com\/video\/(.*)"><span class="comments">(?P<nb_commentaires>.*)<\/span><\/a>(.*)<a class="views-count" href="https:\/\/kaotic\.com\/video\/(.*)"><span class="views">(.*)<\/span><\/a>(.*)<\/div>(.*)<\/div>/isU', $getKaotic, $sKaotic);

			if(!empty($sKaotic['fiche_id']))
			{
				foreach($sKaotic['fiche_id'] as $cle => $fiche_id)
				{
					if(!empty($sKaotic['fiche_image'][$cle]))
					{
						$img = str_ireplace('https://cdn2.kaotic.com/thumbs/', '', $sKaotic['fiche_image'][$cle]);

						$mois = explode('/', $img)[0];
						$annee = explode('/', $img)[1];

						$imgAnnee = (!empty($mois) AND mb_strlen($mois) === 4) ? (int) $mois : null;
						$imgMois = (!empty($annee) AND mb_strlen($annee) === 2) ? (int) $annee : null;
					}

					$imgVideo		= !empty($sKaotic['fiche_image'][$cle])		? secuChars($sKaotic['fiche_image'][$cle])	: 'https://picsum.photos/300/165';
					$idFiche		= !empty($fiche_id)							? secuChars($fiche_id)		: null;
					$titreVideo		= !empty($sKaotic['nom_video'][$cle])		? secuChars($sKaotic['nom_video'][$cle])	: 'Titre inconnu';
					$titreVideo		= str_replace('"', '', $titreVideo);
					$titreVideo		= secuChars($titreVideo);
					$timestamp		= (!empty($imgAnnee) AND !empty($imgMois))	? strtotime($imgAnnee.'-'.$imgMois)			: null;
					$dateVideo		= (!empty($timestamp) AND is_int($timestamp) AND $timestamp >= 0 AND $timestamp <= 2147483647) ? dateFormat($timestamp, 'MMMM Y') : null;
					$lienFicheVideo	= !empty($idFiche)							? 'https://kaotic.com/video/'.$idFiche		: null;

					if(!empty($idFiche))
						{
						echo '<div class="col-12 col-lg-3 mx-auto my-2">
							<form id="form'.$idFiche.'">
								<div class="border rounded mx-auto p-2 text-center h-100">
									<div class="mb-2">'.(!is_file(getKaoticVideoNom($idFiche)) ? '<a href="'.$imgVideo.'" data-fancybox="gallerie"><img src="'.$imgVideo.'" class="vignette" alt="'.$titreVideo.'"></a>' : '<video controls poster="'.$imgVideo.'" preload="auto"><source src="/assets/cache/kaotic/'.$idFiche.'.mp4" type="video/mp4">Votre navigateur ne prends pas en charge les vidéos.</video>').'</div>
									<div class="mb-2 text-truncate"><a href="'.$lienFicheVideo.'" class="link-title text-decoration-none" title="Accéder à la vidéo" '.$onclick.'>'.$titreVideo.'</a></div>
									<p class="mb-2 text-truncate">'.(!empty($timestamp) ? '<time datetime="'.date(DATE_ATOM, $timestamp).'">'.$dateVideo.'</time>' : 'date inconnue').'</p>';

									if(is_file(getKaoticVideoNom($idFiche)))
									{
										echo '<div class="d-flex">
											<a href="?action=telecharger&fichier='.$idFiche.'" class="btn btn-success p-2 w-100 me-2" title="Télécharger la vidéo '.$titreVideo.'">Télécharger la vidéo</a>
											<a href="?action=supprimer&fichier='.$idFiche.'" class="btn btn-danger" onclick="return confirm(\'Es-tu sûr de vouloir supprimer ce fichier ?\')"><i class="fa-solid fa-trash-can"></i></a>
										</div>';
									}

									else
									{
										echo'<button type="submit" name="telecharger" class="btn btn-primary p-2 w-100 validate-button" data-form-id="form'.$idFiche.'" form="form'.$idFiche.'">Télécharger la vidéo sur le serveur</button>
										<input type="hidden" name="action" value="dl">
										<input type="hidden" name="idFiche" value="'.$idFiche.'">';
									}

								echo '</div>
							</form>
						</div>';
					}
				}
			}
		}

		else
			echo alerte('danger', 'Erreur de chargement du site distant');

	echo '</div>

	<p class="my-4 text-center fw-bold">Kaotic (c)</p>
</div>';
?>
<script>
document.querySelector('#form-container').addEventListener('click', function(event) {
	if(event.target.classList.contains('validate-button')) {
		let button = event.target.closest('.validate-button');
		let formId = button.getAttribute('data-form-id');
		let form = document.querySelector('#' + formId);

		if(form.checkValidity()) {
			document.querySelector('#chargement').style.display = 'block';
			document.querySelectorAll('button').forEach(button => button.disabled = true);

			let submitEvent = new Event('submit', {
				bubbles: true,
				cancelable: true
			});

			form.dispatchEvent(submitEvent);
		}
	}
});

document.querySelectorAll('form').forEach(function(form) {
	if(form.id !== 'searchKaotic') {
		form.addEventListener('submit', function(event) {
			event.preventDefault();

			let formData = new FormData(this);
			let submitButton = this.querySelector('button[type="submit"]');
			let formId = this.id;
			let originalButtonText = submitButton.innerHTML;

			submitButton.innerHTML = 'Chargement en cours…';
			submitButton.disabled = true;

			scrollToTop();

			fetch('kaotic.php', {
					method: 'POST',
					body: formData
				})
				.then((response) => {
					if(!response.ok) {
						throw new Error(`Erreur HTTP, status = ${response.status}`);
					}

					return response.json();
				})
				.then(data => {
					let responseDiv = document.querySelector('#reponse');
					if(data.status) {
						responseDiv.innerHTML = `<p class="bg-success-subtle w-100 w-lg-50 shadow-sm mx-auto mb-0 p-3 rounded text-center fw-bold">${data.message}</p>`;

						setTimeout(() => {
							responseDiv.innerHTML = '';
							window.location.href = `kaotic#${formId}`;
							setTimeout(() => {
								window.location.reload(true);
							}, 100);
						}, 3000);
					} else {
						responseDiv.innerHTML = `<p class="bg-danger-subtle w-100 w-lg-50 shadow-sm mx-auto mb-0 p-3 rounded text-center fw-bold">${data.message}</p>`;
					}

					submitButton.innerHTML = originalButtonText;
					submitButton.disabled = false;

					document.querySelector('#chargement').style.display = 'none';
					document.querySelectorAll('button').forEach(button => button.disabled = false);
				})
				.catch(error => {
					submitButton.innerHTML = originalButtonText;
					submitButton.disabled = false;

					document.querySelector('#chargement').style.display = 'none';
					document.querySelectorAll('button').forEach(button => button.disabled = false);

					let responseDiv = document.querySelector('#reponse');
					responseDiv.innerHTML = '<p class="bg-danger-subtle w-100 w-lg-50 shadow-sm mx-auto mb-0 p-3 rounded text-center fw-bold">Une erreur est survenue</p>';
				});
		});
	}
});
</script>
<button id="remonterPage">↑ Remonter la page ↑</button>
<script src="/assets/js/scripts.js?<?= filemtime('../assets/js/scripts.js'); ?>"></script>
</body>
</html>
<?php
}