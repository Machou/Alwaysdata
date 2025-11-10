<?php
use IPv4\SubnetCalculator;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

function clean(?string $str): ?string							{ return !empty($str)														? trim(strip_tags(emoji(espace($str))))							: null; }
function secu(?int $nb): ?int									{ return (!empty($nb) AND filter_var($nb, FILTER_VALIDATE_INT) !== false)	? (int) $nb														: null; }
function secuChars(?string $str): ?string						{ return !empty($str)														? (string) trim(htmlspecialchars($str, ENT_QUOTES, 'UTF-8'))	: null; }
function idAleatoire(?int $taille = null): string				{ return !empty($taille) ? substr(chr(rand(97, 122)).uniqid('', true), 0, $taille) : chr(rand(97, 122)).uniqid(); }
function redirection(string $url, int $duree = 2000): void		{ echo '<script>setTimeout(function() { window.top.location="'.$url.'" }, '.$duree.');</script>'; }

function setFlash(string $type, string $message, ?string $css = null): void
{
	$_SESSION['flash'] = ($css === null) ? alerte($type, $message) : alerte($type, $message, $css);
}

function getFlash(): ?string
{
	if(!empty($_SESSION['flash']))
	{
		$msg = $_SESSION['flash'];

		unset($_SESSION['flash']);

		return $msg;
	}

	return null;
}

function retirerVirguelEtPoint(?string $chaine): string
{
	if(!empty($chaine))
	{
		$dernierCaractere = substr($chaine, -1);

		if($dernierCaractere === '.' OR $dernierCaractere === ',')
			return (string) substr($chaine, 0, -1);
	}

	return (string) $chaine;
}

function pays(string $pays): string
{
	if($pays == 'us')		return 'com';
	elseif($pays == 'uk')	return 'co.uk';
	elseif($pays == 'de')	return 'de';
	elseif($pays == 'es')	return 'es';
	elseif($pays == 'fr')	return 'fr';
	elseif($pays == 'it')	return 'it';
	elseif($pays == 'ca')	return 'ca';
	elseif($pays == 'au')	return 'com.au';
	elseif($pays == 'in')	return 'in';
	elseif($pays == 'ie')	return 'ie';
	elseif($pays == 'se')	return 'se';
	else					return 'fr';
}

function espace(?string $str): ?string
{
	if(!empty($str))
	{
		$str = preg_replace('/<\s*br\s*\/?>/i',		'', $str);
		$str = preg_replace('/\s+/',				'', $str);
		$str = str_replace('   ',					'', $str);

		return (string) trim($str);
	}

	return null;
}

function emoji(?string $str): ?string
{
	return !empty($str) ? (string) preg_replace('/([\x{0001F000}-\x{0001FAFF}])/mu', '', $str) : null;
}

function p($data): void
{
	echo '<div style="z-index: 9999;" class="container position-relative p-0">
		<div style="font-family: \'Source Code Pro\', \'Ubuntu Mono\', monospace;" class="bg-light border border-black d-block fs-6 mb-4 px-3 py-2 overflow-y-auto rounded text-start">
			<pre class="my-1">';
				print_r($data);
			echo '</pre>
		</div>
	</div>';
}

function slug(string $str, string $delimiteur = '-'): string
{
	$str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$str = mb_strtolower($str, 'UTF-8');
	$str = str_ireplace(
		["'", '’', '‘', '‛', '`', 'ʼ', 'ʹ', 'ʽ', 'ʾ', 'ʿ', "ˈ", 'ˊ', 'ˋ', '´', '′'],
		$delimiteur,
		$str
	);
	$str = preg_replace('/[^a-z0-9]+/i', $delimiteur, $str);
	$str = str_replace('-039-', '-', $str);
	$str = trim($str, $delimiteur);

	return (string) $str;
}

function aide(?string $str, ?string $css = null): string
{
	return !empty($str) ? '<sup class="fw-bold curseur'.$css.'" data-bs-toggle="tooltip" data-bs-title="'.$str.'">[ ? ]</sup>' : null;
}

function btnCopie(string $txt, ?string $css = null): string
{
	$txt = str_replace('"', '&quot;', $txt);
	$css = empty($css) ? 'btn btn-dark btn-copie text-dark bg-transparent border-0 p-0 ms-2' : $css;

	return '<button class="'.$css.'" data-type="dns" data-bs-toggle="tooltip" data-bs-title="Copier vers le presse-papiers" data-clipboard-text="'.$txt.'"><i class="fa-regular fa-clipboard"></i></button>';
}

function lien(string $lien): string
{
	$lienParse		= parse_url($lien);
	$scheme			= !empty($lienParse['scheme'])		? $lienParse['scheme'] : null;
	$host			= !empty($lienParse['host'])		? $lienParse['host'] : null;
	$port			= !empty($lienParse['port'])		? $lienParse['port'] : null;
	$user			= !empty($lienParse['user'])		? $lienParse['user'] : null;
	$pass			= !empty($lienParse['pass'])		? $lienParse['pass'] : null;
	$path			= !empty($lienParse['path'])		? $lienParse['path'] : null;
	$query			= !empty($lienParse['query'])		? '?'.$lienParse['query'] : null;
	$fragment		= !empty($lienParse['fragment'])	? '#'.$lienParse['fragment'] : null;

	return '<span class="text-decoration-underline link-underline-primary link-offset-2 text-primary curseur ms-lg-1" data-bs-toggle="tooltip" data-bs-title="Schéma de l’URL">'.$scheme.'://</span>'.
	'<span class="text-decoration-underline link-underline-success link-offset-2 text-success curseur" data-bs-toggle="tooltip" data-bs-title="Hôte de l’URL">'.$host.'</span>'.
	(!empty($path) ? '<span class="text-decoration-underline link-underline-dark link-offset-2 text-dark curseur" data-bs-toggle="tooltip" data-bs-title="Chemin de l’URL">'.$path.'</span>' : null).
	(!empty($query) ? '<span class="text-decoration-underline link-underline-danger link-offset-2 text-danger curseur" data-bs-toggle="tooltip" data-bs-title="Requête de l’URL">'.$query.'</span>' : null).
	(!empty($fragment) ? '<span class="text-decoration-underline link-underline-warning link-offset-2 text-warning curseur" data-bs-toggle="tooltip" data-bs-title="Fragment de l’URL">'.$fragment.'</span>' : null);
}

function istrtr(string $str, array $remplacements): string
{
	foreach($remplacements as $cle => $val) {
		$str = preg_replace('/'.preg_quote($cle, '/').'/i', $val, $str);
	}

	return $str;
}

function s($str): ?string
{
	if(!empty($str))
	{
		if(is_array($str))							return (count($str) > 1) ? 's' : null;
		elseif(!isset($str) || $str === '')			return null;
		elseif(is_numeric($str) AND (int) $str > 1)	return 's';
		elseif(str_contains($str, ','))				return (substr_count($str, ',') > 1) ? 's' : null;
	}

	return null;
}

function cloudflare(string $zone): bool
{
	$ch = curl_init('https://api.cloudflare.com/client/v4/zones/'.$zone.'/purge_cache');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['purge_everything' => true]));
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'X-Auth-Email: tortuegeniale@tuta.io',
		'X-Auth-Key: 07549dcf4df83380aa35896ad7d507200d74a',
		'Content-Type: application/json; charset=utf-8'
	]);

	$response = curl_exec($ch);
	curl_close($ch);

	if(!empty($response))
	{
		$response = json_decode($response);

		if($response->success === true)
			return true;
	}

	return false;
}

function nettoyerImdb(): bool
{
	$repertoireCache = '/home/blok/divers/composer/vendor/imdbphp/imdbphp/cache';

	if(is_dir($repertoireCache))
	{
		$fichiersImdb = glob($repertoireCache.'/*');

		foreach($fichiersImdb as $fichierCache)
		{
			if(is_file($fichierCache))
				unlink($fichierCache);
		}

		return true;
	}

	else
		return false;
}

function nettoyerLogs(string $fichier): bool
{
	$f = [
		'erreurs' => '/home/blok/www/apache-errors.log',
		'mail' => '/home/blok/www/mail.log'
	];

	if(!empty($fichier) AND file_exists($f[$fichier]) AND is_file($f[$fichier]))
	{
		$handle = fopen($f[$fichier], 'w');
		if($handle) {
			fwrite($handle, '');
			fclose($handle);
		}
	}

	return true;
}

function nettoyage($cache = false): string
{
	$thisip = cloudflare('bc457c2e2105c145c2de34f17aa04bfb');
	$diskigo = cloudflare('206c17f7b8dce0a5ac25b5048f193db5');
	$hdvli = cloudflare('e4bcafe81b1bfa221aba3be6e8b11709');

	$thisipCf = $thisip ? 'success' : 'danger';
	$diskigoCf = $diskigo ? 'success' : 'danger';
	$hdvlioCf = $hdvli ? 'success' : 'danger';

	$thisipMsg = '<div style="border-left: '.($thisip ? 'rgba(25,135,84, 1)' : 'rgba(220,53,69, 1)').' 1rem solid !important;" class="col-12 col-lg-4 bg-'.$thisipCf.'-subtle border border-start-0 border-'.$thisipCf.' rounded mx-auto mb-3 p-3 text-center text-'.$thisipCf.' fw-bold">Cache Cloudflare <span class="text-decoration-underline">ThisIP.pw</span></div>';
	$diskigoMsg = '<div style="border-left: '.($diskigo ? 'rgba(25,135,84, 1)' : 'rgba(220,53,69, 1)').' 1rem solid !important;" class="col-12 col-lg-4 bg-'.$diskigoCf.'-subtle border border-start-0 border-'.$diskigoCf.' rounded mx-auto mb-3 p-3 text-center text-'.$diskigoCf.' fw-bold">Cache Cloudflare <span class="text-decoration-underline">Diskigo.com</span></div>';
	$hdvliMsg = '<div style="border-left: '.($hdvli ? 'rgba(25,135,84, 1)' : 'rgba(220,53,69, 1)').' 1rem solid !important;" class="col-12 col-lg-4 bg-'.$hdvlioCf.'-subtle border border-start-0 border-'.$hdvlioCf.' rounded mx-auto mb-3 p-3 text-center text-'.$hdvlioCf.' fw-bold">Cache Cloudflare <span class="text-decoration-underline">HdV.Li</span></div>';

	nettoyerLogs('erreurs');
	nettoyerLogs('mail');

	if(nettoyerImdb() === true)
		$imdbMsg = '<div style="border-left: rgba(25,135,84, 1) 1rem solid !important;" class="col-12 col-lg-4 bg-success-subtle border border-start-0 border-success rounded mx-auto mb-'.(!isset($_GET['complet']) ? '5' : '0').' p-3 text-center text-success fw-bold">Cache <span class="text-decoration-underline">IMDb</span></div>';

	$sortie[] = '<div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
		<h3 class="mb-0">[ <a href="?uncache" class="'.((isset($_GET['uncache']) AND !isset($_GET['complet'])) ? 'link-offset-3 text-decoration-underline' : null).' text-success fw-bold">Cloudflare</a> ]</h3>
		<h3 class="mb-0">[ <a href="?uncache&complet" class="'.(isset($_GET['complet']) ? 'link-offset-3 text-decoration-underline ' : null).'text-success fw-bold">Nettoyage complet</a> ]</h3>
	</div>'.$thisipMsg.$diskigoMsg.$hdvliMsg.$imdbMsg;

	if($cache)
	{
		$dossiers = ['assets/cache/dom/*', 'assets/cache/ip/*', 'assets/cache/kaotic/*', 'assets/cache/tmdb/*'];

		$fichiersA = [];
		$i = 0;
		foreach($dossiers as $dossier)
		{
			$fichiers = glob($_SERVER['DOCUMENT_ROOT'].$dossier);
			foreach($fichiers as $fichier)
			{
				if(is_file($fichier))
				{
					$fichierA = strtr($fichier, ['/home/blok/www/thisip/assets/cache/' => '', 'dom' => '<span class="fw-bold">dom</span>', 'ip' => '<span class="fw-bold">ip</span>', 'kaotic' => '<span class="fw-bold">kaotic</span>', 'tmdb' => '<span class="fw-bold">tmdb</span>',]);

					if(preg_match('/\/ip\//is', $fichier) AND filemtime($fichier) < strtotime('-6 months'))
					{
						$fichiersA[] = '<div class="row text-success">
							<div class="col-3 text-end" title="Taille du fichier">'.taille(filesize($fichier)).'</div>
							<div class="col-9 text-start text-truncate" title="Nom du fichier"><span class="me-1 curseur" data-bs-toggle="tooltip" data-bs-title="'.temps(filemtime($fichier)).'"><i class="fa-regular fa-clock"></i></span> <span>'.$fichierA.'</span></div>
						</div>';

						$i++;
					}

					elseif(!preg_match('/\/ip\//is', $fichier))
					{
						$fichiersA[] = '<div class="row text-success">
							<div class="col-3 text-end" title="Taille du fichier">'.taille(filesize($fichier)).'</div>
							<div class="col-9 text-start text-truncate" title="Nom du fichier"><span class="me-1 curseur" data-bs-toggle="tooltip" data-bs-title="'.temps(filemtime($fichier)).'"><i class="fa-regular fa-clock"></i></span> <span>'.$fichierA.'</span></div>
						</div>';

						$i++;
					}

					if(preg_match('/\/ip\//is', $fichier) AND filemtime($fichier) < strtotime('- 6 months'))	unlink($fichier);
					elseif(!preg_match('/\/ip\//is', $fichier))													unlink($fichier);
				}
			}
		}

		$sortie[] = ($i > 0 ? '<div style="border-left: rgba(25,135,84, 1) 1rem solid !important;" class="accordion accordion-flush col-12 col-lg-8 bg-success-subtle border border-start-0 border-success rounded mx-auto my-5 p-4" id="accordionFlush">
			<div class="accordion-item bg-success-subtle">
				<div class="accordion-button collapsed bg-success-subtle p-0 m-0" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse">
					<span class="d-block w-100 text-center fw-bold fs-4 text-success">'.($i.' fichier'.s($i).' supprimé'.s($i)).'</span>
				</div>
				<div class="accordion-collapse collapse mt-3" data-bs-parent="#accordionFlush" id="flush-collapse">'.implode($fichiersA).'</div>
			</div>
		</div>' : alerte('danger', 'Aucun fichier à supprimer'));
	}

	return (string) implode($sortie);
}

function alerte(string $type, string $message, ?string $class = 'col-12 col-lg-8 my-5'): string
{
	$type = in_array($type ?? '', ['danger', 'success', 'info']) ? $type : 'danger';

	$typeIcone = [
		'success' => 'circle-check',
		'danger' => 'circle-exclamation',
		'info' => 'circle-info'
	];

	$typeCouleurs = [
		'success' => 'rgba(25,135,84, 1)',
		'danger' => 'rgba(220,53,69, 1)',
		'info' => 'rgba(15,199,240, 1)'
	];

	return (string) '<div class="mx-auto '.$class.'">
		<div style="border-left: '.$typeCouleurs[$type].' 1rem solid !important;" class="alert alert-'.$type.' alert-dismissible fade show mb-0" role="alert">
			<div class="d-flex align-items-center">
				<i class="fa-solid fa-'.$typeIcone[$type].' fs-1 me-3 ms-auto"></i>
				<p class="me-auto mb-0">'.$message.'</p>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
			</div>
		</div>
	</div>';
}

function imageDistante(string $url): bool
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 4);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_exec($ch);

	$http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: '';
	curl_close($ch);

	if($http >= 200 && $http < 300 && str_starts_with($mime, 'image/'))
		return true;

	return false;
}

function telecharger(string $url, string $chemin, $header = [false]): void
{
	$fp = fopen($chemin, 'wb');
	if(!$fp) {
		throw new Exception('Impossible d’ouvrir le fichier en écriture');
	}

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_exec($ch);

	if(curl_errno($ch))
	{
		curl_close($ch);
		fclose($fp);

		throw new Exception('Erreur cURL : '.curl_error($ch));
	}

	curl_close($ch);
	fclose($fp);
}

function get(string $url, string $req = 'GET', ?array $headers = ['headers' => ['Accept-Language' => 'fr', 'Cache-Control' => 'no-cache']]): ?string
{
	try {
		$client = HttpClient::create();
		$response = $client->request($req, $url, $headers);

		if($response->getStatusCode() === 200) {
			return (string) $response->getContent();
		}

		return null;
	} catch(\Exception $e) {
		echo alerte('danger', 'Erreur lors de la récupération de <span class="fw-bold">'.$url.'</span> : '.$e->getMessage());
		// throw new Exception('Erreur DNS : '.$e->getMessage());

		return null;
	}
}

function getYgg(string $url): ?string
{
	$cookie = '/home/blok/www/thisip/projets/tmdb/cookies.txt';
	$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0';
	$pageConnexion = 'https://www.yggtorrent.top/auth/login';
	$pageFormulaire = 'https://www.yggtorrent.top/auth/process_login';

	// 1. Préparer le cookie
	$dir = dirname($cookie);
	if(!is_dir($dir))
	{
		if(!mkdir($dir, 0750, true) AND !is_dir($dir)) {
			return 'Erreur : impossible de créer le répertoire des cookies';
		}
	}

	if(!file_exists($cookie) AND false === @touch($cookie)) {
		return 'Erreur : impossible de créer le fichier cookies';
	}

	@chmod($cookie, 0600);

	// cURL
	$mk = function(string $u) use ($cookie, $ua)
	{
		$ch = curl_init($u);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_FOLLOWLOCATION	=> true,
			CURLOPT_MAXREDIRS 		=> 10,
			CURLOPT_CONNECTTIMEOUT	=> 15,
			CURLOPT_TIMEOUT			=> 30,
			CURLOPT_COOKIEJAR		=> $cookie,
			CURLOPT_COOKIEFILE		=> $cookie,
			CURLOPT_USERAGENT		=> $ua,
			CURLOPT_ENCODING		=> '',
			CURLOPT_HEADER			=> false,
		]);

		return $ch;
	};

	// 2. GET login page (cookies + potentiels tokens)
	$ch = $mk($pageConnexion);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
		'Accept-Language: fr-FR',
	]);
	$connexionHtml = curl_exec($ch);
	if($connexionHtml === false)
	{
		$err = curl_error($ch);
		curl_close($ch);
		return 'Erreur GET /auth/login : '.$err;
	}

	$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($http >= 400) {
		return 'Erreur GET /auth/login : HTTP '.$http;
	}

	// 3. Récupère tous les <input type="hidden"> (tokens, etc.)
	$hidden = [];
	if(preg_match_all('/<input\s+[^>]*type=["\']hidden["\'][^>]*>/i', (string)$connexionHtml, $m)) {
		foreach ($m[0] as $input) {
			if(preg_match('/name=["\']([^"\']+)["\']/i', $input, $mn) &&
				preg_match('/value=["\']([^"\']*)["\']/i', $input, $mv)) {
				$hidden[$mn[1]] = $mv[1];
			}
		}
	}

	// 4. POST Connexion (cred + hidden fields + headers cohérents)
	$champsFormulaire = array_merge($hidden, ['id' => 'Poulok', 'pass' => 'yggisgood']);

	$ch = $mk($pageFormulaire);
	curl_setopt_array($ch, [
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query($champsFormulaire),
	]);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
		'Accept-Language: fr-FR',
		'Content-Type: application/x-www-form-urlencoded',
		'Origin: https://www.yggtorrent.top/',
		'Referer: '.$pageConnexion,
		'Cache-Control: no-cache',
		'Pragma: no-cache',
	]);

	$formulaireReponse = curl_exec($ch);
	if($formulaireReponse === false) {
		$err = curl_error($ch);
		curl_close($ch);
		return 'Erreur POST /auth/process_login : '.$err;
	}

	$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($http >= 400) {
		return 'Erreur POST /auth/process_login : HTTP '.$http;
	}

	// 5. Vérif minimale : le cookie a-t-il été écrit ?
	if(!file_exists($cookie) || filesize($cookie) === 0) {
		return 'Erreur : fichier cookie vide après login (login probablement refusé).';
	}

	// 6. GET de la ressource cible avec la même session
	$ch = $mk($url);
	$reponse = curl_exec($ch);
	if($reponse === false) {
		$err = curl_error($ch);
		curl_close($ch);

		return 'Erreur GET ressource : '.$err;
	}
	$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if($http >= 400) {
		return 'Erreur GET ressource : HTTP '.$http;
	}

	// 7. Détection basique Cloudflare
	if(preg_match('/Just a moment|cf-browser-verification|cf-chl/i', $reponse)) {
		return null;
	}

	return $reponse;
}

// function getYgg(string $url): ?string
// {
// 	$cookie = '/home/blok/www/thisip/projets/tmdb/cookies.txt';

// 	// Étape 1 : Connexion
// 	$connexion = curl_init('https://www.yggtorrent.top/auth/process_login');
// 	curl_setopt($connexion, CURLOPT_POST, true);
// 	curl_setopt($connexion, CURLOPT_POSTFIELDS, http_build_query(['id' => 'Poulok', 'pass' => 'yggisgood'])); // Binouchette Yggisgood!!!123
// 	curl_setopt($connexion, CURLOPT_RETURNTRANSFER, true);
// 	curl_setopt($connexion, CURLOPT_FOLLOWLOCATION, true);
// 	curl_setopt($connexion, CURLOPT_COOKIEJAR, $cookie);
// 	curl_setopt($connexion, CURLOPT_COOKIEFILE, $cookie);
// 	curl_setopt($connexion, CURLOPT_USERAGENT, getRandomUserAgent());
// 	curl_setopt($connexion, CURLOPT_HTTPHEADER, [
// 		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
// 		'Accept-Language: fr-FR',
// 		'Referer: https://www.yggtorrent.top/auth/login',
// 		'Priority: u=1',
// 		'Pragma: no-cache',
// 		'Cache-Control: no-cache',
// 		'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0',
// 	]);

// 	$loginResponse = curl_exec($connexion);

// 	if(curl_errno($connexion)) {
// 		curl_close($connexion);
// 		return 'Erreur de connexion (login) : '.curl_error($connexion);
// 	}
// 	curl_close($connexion);

// 	if(!file_exists($cookie) OR filesize($cookie) == 0)
// 		return 'Erreur : fichier cookie vide ou inexistant après login.';

// 	// Étape 2 : Accès avec cookies
// 	$ch = curl_init($url);
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// 	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
// 	curl_setopt($ch, CURLOPT_USERAGENT, getRandomUserAgent());

// 	$response = curl_exec($ch);

// 	if(curl_errno($ch)) {
// 		curl_close($ch);
// 		return 'Erreur de connexion (accès URL) : '.curl_error($ch);
// 	}
// 	curl_close($ch);

// 	// Vérification Cloudflare ou autre blocage
// 	if(preg_match('/Just a moment/i', $response))
// 		return null;

// 	curl_close($ch);

// 	return $response;
// }

function cache(string $fichier, string $donnees)
{
	$handle = fopen($fichier, 'w+');
	fwrite($handle, $donnees);
	fclose($handle);

	if(!is_file($fichier) AND !empty($fichier))
	{
		echo '<p class="text-danger">Le fichier <strong>'.$fichier.'</strong> n’existe pas ou est vide</p>';

		return false;
	}

	return true;
}

function stdToArray($obj): array
{
	$tableau = $obj;

	foreach($tableau as $cle => &$champ)
	{
		if(is_object($champ))
			$champ = stdToArray($champ);
	}

	return (array) $tableau;
}

function uniqueMultidimArray(array $array, $cleUnique): array
{
	$uniqueArray = [];
	$nouvelleValeur = [];

	foreach($array as $cle => $valeur)
	{
		if(isset($valeur[$cleUnique]) AND !in_array($valeur[$cleUnique], $nouvelleValeur))
		{
			$uniqueArray[$cle] = $valeur;
			$nouvelleValeur[] = $valeur[$cleUnique];
		}
	}

	return (array) $uniqueArray;
}

function minsEnHrs(int $temps, string $format = '%2dh %02d'): ?string
{
	if($temps < 1)
		return null;

	$heures = intdiv($temps, 60);
	$minutes = $temps % 60;

	return sprintf($format, $heures, $minutes);
}

function dateFormat(int|string $dateTime, ?string $pattern = null): string
{
	$dateTime = is_string($dateTime) ? strtotime($dateTime) : $dateTime;

	$date = (new DateTime())->setTimestamp($dateTime);

	if($pattern === 'DATE_ATOM')		$p = "yyyy-MM-dd'T'HH:mm:ssXXX";
	elseif($pattern === 'DATE_RFC7231')	$p = "EEE, dd MMM yyyy HH:mm:ss 'GMT'";
	elseif($pattern === 'c')			$p = 'd MMMM yyyy à HH:mm:ss';
	elseif($pattern === 'm')			$p = 'MMMM yyyy';
	elseif($pattern === 's')			$p = 'dd/MM/Y';
	elseif(!empty($pattern))			$p = $pattern;
	else								$p = 'd MMMM yyyy';

	$formatter = new IntlDateFormatter(
		'fr_FR',						// ?string $locale
		IntlDateFormatter::FULL,		// int $dateType = IntlDateFormatter::FULL
		IntlDateFormatter::FULL,		// int $timeType = IntlDateFormatter::FULL
		'Europe/Paris',					// IntlTimeZone|DateTimeZone|null|string $timezone = null
		IntlDateFormatter::GREGORIAN,	// IntlCalendar|int|null $calendar = null
		$p								// ?string $pattern = null - https://unicode-org.github.io/icu/userguide/format_parse/datetime/
	);

	return (string) $formatter->format($date);
}

function dateInfos(?string $date): array
{
	if(!empty($date))
	{
		try {
			$dateTime = new DateTime($date);

			return [
				'date' => $dateTime->format('Y-m-d'),
				'timestamp' => $dateTime->getTimestamp()
			];
		} catch (Exception $e) { }
	}

	return [
		'date' => null,
		'timestamp' => null
	];
}

function temps(int $temps): string
{
	$maintenant = new DateTime();
	$date = (new DateTime())->setTimestamp($temps);

	if($date > $maintenant) {
		$diff = $maintenant->diff($date);
		$prefixe = 'dans ';
	}

	else {
		$diff = $date->diff($maintenant);
		$prefixe = 'il y a ';
	}

	if($diff->y > 0)			return $prefixe.$diff->y.' an'.($diff->y > 1 ? 's' : '');
	elseif($diff->m > 0)		return $prefixe.$diff->m.' mois';

	elseif($diff->d >= 7) {
		$semaines = floor($diff->d / 7);
		return $prefixe.$semaines.' semaine'.($semaines > 1 ? 's' : '');
	}

	elseif($diff->d > 0)		return $prefixe.$diff->d.' jour'.($diff->d > 1 ? 's' : '');
	else						return 'aujourd’hui';
}

function taille(int $tailleOctets, int $precision = 2): ?string
{
	if($tailleOctets < 0)
		return null;

	if($tailleOctets === 0)
		return '0 B';

	$unites = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb'];
	$index = 0;
	$taille = (float) $tailleOctets;

	while($taille >= 1024 AND $index < count($unites) - 1) {
		$taille /= 1024;
		$index++;
	}

	return round($taille, $precision).' '.$unites[$index];
}

function tailleDossier(string $cheminDossier): int
{
	$taille = 0;

	if(!is_dir($cheminDossier)) {
		throw new InvalidArgumentException('Le chemin n’est pas un dossier');
	}

	$directoryIterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($cheminDossier, RecursiveDirectoryIterator::SKIP_DOTS)
	);

	foreach($directoryIterator as $file) {
		$taille += $file->getSize();
	}

	return (int) $taille;
}

function cdn(string $ip): bool|string
{
	if(!isIPv4($ip))
		return false;

	$ipsCDNs = [
		'cloudflare' =>	// https://www.cloudflare.com/fr-fr/ips/
			['173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22', '141.101.64.0/18', '108.162.192.0/18', '190.93.240.0/20', '188.114.96.0/20', '197.234.240.0/22', '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13', '104.24.0.0/14', '172.64.0.0/13', '131.0.72.0/22'],

		'imperva' => // https://docs.imperva.com/bundle/z-kb-articles-km/page/c85245b7.html
			['199.83.128.0/21', '198.143.32.0/19', '149.126.72.0/21', '103.28.248.0/22', '45.64.64.0/22', '185.11.124.0/22', '192.230.64.0/18', '107.154.0.0/16', '45.60.0.0/16', '45.223.0.0/16', '131.125.128.0/17'],

		'fastly' => // https://developer.fastly.com/reference/api/utils/public-ip-list/
			['23.235.32.0/20', '43.249.72.0/22', '103.244.50.0/24', '103.245.222.0/23', '103.245.224.0/24', '104.156.80.0/20', '151.101.0.0/16', '157.52.64.0/18', '172.111.64.0/18', '185.31.16.0/22', '199.27.72.0/21', '199.232.0.0/16'],

		'cloudfront' => // https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/LocationsOfEdgeServers.html
			['120.52.22.96/27', '205.251.249.0/24', '180.163.57.128/26', '204.246.168.0/22', '111.13.171.128/26', '18.160.0.0/15', '205.251.252.0/23', '54.192.0.0/16', '204.246.173.0/24', '54.230.200.0/21', '120.253.240.192/26', '116.129.226.128/26', '130.176.0.0/17', '108.156.0.0/14', '99.86.0.0/16', '205.251.200.0/21', '13.32.0.0/15', '120.253.245.128/26', '13.224.0.0/14', '70.132.0.0/18', '15.158.0.0/16', '111.13.171.192/26', '13.249.0.0/16', '18.238.0.0/15', '18.244.0.0/15', '205.251.208.0/20', '65.9.128.0/18', '130.176.128.0/18', '58.254.138.0/25', '54.230.208.0/20', '3.160.0.0/14', '116.129.226.0/25', '52.222.128.0/17', '18.164.0.0/15', '111.13.185.32/27', '64.252.128.0/18', '205.251.254.0/24', '54.230.224.0/19', '71.152.0.0/17', '216.137.32.0/19', '204.246.172.0/24', '18.172.0.0/15', '120.52.39.128/27', '118.193.97.64/26', '18.154.0.0/15', '54.240.128.0/18', '205.251.250.0/23', '180.163.57.0/25', '52.46.0.0/18', '52.82.128.0/19', '54.230.0.0/17', '54.230.128.0/18', '54.239.128.0/18', '130.176.224.0/20', '36.103.232.128/26', '52.84.0.0/15', '143.204.0.0/16', '144.220.0.0/16', '120.52.153.192/26', '119.147.182.0/25', '120.232.236.0/25', '111.13.185.64/27', '3.164.0.0/18', '54.182.0.0/16', '58.254.138.128/26', '120.253.245.192/27', '54.239.192.0/19', '18.68.0.0/16', '18.64.0.0/14', '120.52.12.64/26', '99.84.0.0/16', '130.176.192.0/19', '52.124.128.0/17', '204.246.164.0/22', '13.35.0.0/16', '204.246.174.0/23', '3.172.0.0/18', '36.103.232.0/25', '119.147.182.128/26', '118.193.97.128/25', '120.232.236.128/26', '204.246.176.0/20', '65.8.0.0/16', '65.9.0.0/17', '108.138.0.0/15', '120.253.241.160/27', '64.252.64.0/18'],

		'cloudfront_region' =>
			['13.113.196.64/26', '13.113.203.0/24', '52.199.127.192/26', '13.124.199.0/24', '3.35.130.128/25', '52.78.247.128/26', '13.233.177.192/26', '15.207.13.128/25', '15.207.213.128/25', '52.66.194.128/26', '13.228.69.0/24', '52.220.191.0/26', '13.210.67.128/26', '13.54.63.128/26', '43.218.56.128/26', '43.218.56.192/26', '43.218.56.64/26', '43.218.71.0/26', '99.79.169.0/24', '18.192.142.0/23', '35.158.136.0/24', '52.57.254.0/24', '13.48.32.0/24', '18.200.212.0/23', '52.212.248.0/26', '3.10.17.128/25', '3.11.53.0/24', '52.56.127.0/25', '15.188.184.0/24', '52.47.139.0/24', '3.29.40.128/26', '3.29.40.192/26', '3.29.40.64/26', '3.29.57.0/26', '18.229.220.192/26', '54.233.255.128/26', '3.231.2.0/25', '3.234.232.224/27', '3.236.169.192/26', '3.236.48.0/23', '34.195.252.0/24', '34.226.14.0/24', '13.59.250.0/26', '18.216.170.128/25', '3.128.93.0/24', '3.134.215.0/24', '52.15.127.128/26', '3.101.158.0/23', '52.52.191.128/26', '34.216.51.0/25', '34.223.12.224/27', '34.223.80.192/26', '35.162.63.192/26', '35.167.191.128/26', '44.227.178.0/24', '44.234.108.128/25', '44.234.90.252/30'],

		'fly.io' => // https://ipinfo.io/AS40509
			['109.105.216.0/23', '109.105.218.0/23', '109.105.220.0/23', '109.105.222.0/23', '137.66.0.0/23', '137.66.10.0/23', '137.66.12.0/23', '137.66.14.0/23', '137.66.16.0/23', '137.66.18.0/23'],
	];

	foreach($ipsCDNs as $cdn => $plages)
	{
		foreach($plages as $plage)
		{
			[$ipDebut, $masque] = explode('/', $plage);
			$calc = new IPv4\SubnetCalculator($ipDebut, $masque);

			if($calc->isIPAddressInSubnet($ip))
				return $cdn;
		}
	}

	return false;
}

function nettoyageRelease(string $release): string
{
		if(!empty($release))
		{
			preg_match('/(.*)\b(19[0-9]{2}|20[0-1][0-9]|202[0-5]|2160(p|i)?|1080(p|i)?|720(p|i)?)\b(.*)/isU', $release, $m);

			$release = !empty($m[0]) ? $m[0] : $release;
		}

		if(preg_match('/\((.*?)\)\s*$/', $release, $matches)) {
			$release = $matches[1];
		}

		else
		{
			$release = preg_replace('/('.implode('|', [
				'\b(?:19|20)\d{2}\b', // Année
				'\bS\d{1,2}E\d{2}\b', // Saisons & Épisodes
				'1080p', '2160p.MA', '2160p', '720p', '1080i', '2160i', '720i', 'SCREENER', ' TS ', ' TC ',
				'WEBRip', 'WEB-DL', 'BluRay', 'HDLight', 'REMUX', 'HDR10plus', 'HDR', 'WEB', 'DV',
				'x264', 'x265', 'H264', 'H265', 'AVC', 'AV1',
				'5 1', '7 1', 'DD5\.1', 'DTS(?:-HD)?', 'DTS-HDMA', 'FLAC', 'EAC3', 'E-AC3', 'AC3', 'MP3', 'Atmos',
				'TRUEFRENCH', 'FRENCH', 'MULTi', 'VF2', 'VFF', 'VOF', 'VFi', 'VFQ', 'CUSTOM', 'FULL',
				'DDP5\.1', '10Bit', '1\.0', '2\.0', '5\.1', '7\.1',
				'DDP5\.1', '10Bit', '1\.0', '2\.0', '5\.1', '7\.1',
				'-[A-Za-z0-9]+', // Team
			]).')/i', '', $release);

			$release = str_replace('.', ' ', $release);
			$release = preg_replace('/\s+/', ' ', $release);
		}

		return secuChars($release);
}

function parserRelease(string $release, ?int $time): string
{
	$rls		= !empty($release) ? str_replace('.', ' ', trim(strip_tags(emoji(($release)))))		: null;
	$rls		= preg_replace(['/5 1/', '/7 1/', '/2025.1080p/'], ['5.1', '7.1', '2025 1080p'], $rls);
	$cssTime	= (!empty($time) AND ((time() - (60 * 60 * 24 * 2)) < $time)) ? 'fw-bold '			: null;
	$regexTF	= 'VFF|VF2|VOF|VFi|TRUEFRENCH';

	// https://emojipedia.org/fr/drapeaux
	if(preg_match('/VFQ/is', $rls) OR (preg_match('/ MULTi| FRENCH /is', $rls) AND !empty($pays) AND !preg_match('/France/is', strip_tags($pays))) AND !preg_match('/'.$regexTF.'/is', $rls))
																													$pays = '🇨🇦';
	elseif(preg_match('/MULTi.*?('.$regexTF.')/is', $rls) OR (preg_match('/MULTi|FRENCH|'.$regexTF.'/is', $rls)))	$pays = '🇫🇷';
	elseif((preg_match('/MULTi/is', $rls)) AND !preg_match('/'.$regexTF.'/is', $rls))								$pays = '<img src="/assets/img/drapeau-multi.png" style="height: 10px; width: 12px;" alt="MULTi FR / CA / EN" title="MULTi FR / CA / EN">';
	elseif(preg_match('/SUBFRENCH|VOSTFR|VOST/is', $rls))															$pays = '<img src="/assets/img/drapeau-vostfr.png" style="height: 10px; width: 12px;" alt="VOSTFR" title="VOSTFR">';
	else																											$pays = '🇺🇸';

	$rlsArray = [
		"#039;" => '’', "'" => '’',

		'DD5 1' => 'DD5.1',

		'X264' => 'x264', 'H264' => 'h264',
		'X265' => 'x265', 'H265' => 'h265',

		'&amp;' => 'and', '&' => 'and',
		'dvdrip' => 'DVDRip', 'webrip' => 'WEBRip', 'webdl' => 'WEBRip', 'web-dl' => 'WEBRip', ' web ' => ' WEBRip ',
		'bluray' => 'BluRay', 'multi' => 'MULTi',
	];

	$rls = str_ireplace(array_keys($rlsArray), array_values($rlsArray), $rls);

	$rls = preg_match('/ nf /is', $rls)		? '<span class="me-1">'.logoNetflixMini(13, 0, 'vertical-align: baseline').'</span>'.$rls							: $rls;
	$rls = preg_match('/ amzn /is', $rls)	? '<i class="fa-brands fa-amazon me-1" style="color: rgba(25,147,247, 1);" title="Logo Amazon Prime"></i>'.$rls	: $rls;
	$rls = preg_match('/ dsnp /is', $rls)	? '<span class="me-1">'.logoDisneyPlus(20).'</span>'.$rls															: $rls;

	return (string) '<span class="me-2">'.$pays.'</span><span class="'.$cssTime.'">'.$rls.'</span>';
}

function isYoutubeId(string $id): bool
{
	return preg_match('/^[a-zA-Z0-9_-]{11}$/', $id) === 1;
}

function isVimeoId(string $id): bool
{
	if(!preg_match('/^[1-9][0-9]{7,11}$/', $id)) {
		return false;
	}

	$ch = curl_init('https://vimeo.com/api/v2/video/'.$id.'.json');
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	return $httpCode === 200;
}

function isSha1(string $hash): bool
{
	return (bool) (!empty($hash) AND ctype_xdigit($hash) AND mb_strlen($hash) === 40) ? true : false;
}

function isYear(string $annee): ?int
{
	return (!empty($annee) AND mb_strlen($annee) === 4) ? (int) $annee : null;
}

function isInFile(string $fichier, string $ip): bool
{
	if(!isIPv4($ip))
		return false;

	if(!is_file($fichier) OR !is_readable($fichier))
		return false;

	$handle = fopen($fichier, 'r');
	if(!$handle)
		return false;

	while(($ligne = fgets($handle)) !== false)
	{
		if(trim($ligne) === $ip)
		{
			fclose($handle);

			return true;
		}
	}

	fclose($handle);

	return false;
}

function isIPv4(string $ip): bool
{
	return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
}

function isIPv6(string $ip): bool
{
	return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
}

function getCertificatSSL(string $domaine): ?array
{
	$streamContext = stream_context_create([
		'ssl' => [
			'capture_peer_cert' => true,
			'verify_peer' => false,
			'verify_peer_name' => false,
			'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT,
		]
	]);

	$client = @stream_socket_client('ssl://'.$domaine.':443', $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $streamContext);

	if($client === false) {
		throw new Exception('Impossible de se connecter à <strong>'.$domaine.'</strong> : '.$errstr.' ('.$errno.')');
	}

	$contexte = stream_context_get_params($client);
	$certificat = openssl_x509_parse($contexte['options']['ssl']['peer_certificate']);

	return $certificat;
}

function getRemoteAddr(): string
{
	$ip = !empty($_GET['ip'])							? trim($_GET['ip'])							: null;
	$ipP = !empty($_POST['chercher_ip'])				? trim($_POST['chercher_ip'])				: null;
	$ipG = !empty($_GET['chercher_ip'])					? trim($_GET['chercher_ip'])				: null;
	$ipCf = !empty($_SERVER['HTTP_CF_CONNECTING_IP'])	? trim($_SERVER['HTTP_CF_CONNECTING_IP'])	: null;
	$ipRA = !empty($_SERVER['REMOTE_ADDR'])				? trim($_SERVER['REMOTE_ADDR'])				: null;


	if(!empty($ip) AND (isIPv4($ip) OR isIPv6($ip)))			return (string) secuChars($ip);
	elseif(!empty($ipP) AND (isIPv4($ipP) OR isIPv6($ipP)))		return (string) secuChars($ipP);
	elseif(!empty($ipG) AND (isIPv4($ipG) OR isIPv6($ipG)))		return (string) secuChars($ipG);
	elseif(!empty($ipCf) AND (isIPv4($ipCf) OR isIPv6($ipCf)))	return (string) secuChars($ipCf);
	elseif(!empty($ipRA) AND (isIPv4($ipRA) OR isIPv6($ipRA)))	return (string) secuChars($ipRA);
	else														return (string) '127.0.0.1';
}

function getHttpUserAgent(): ?string
{
	return !empty($_SERVER['HTTP_USER_AGENT']) ? (string) trim(secuChars($_SERVER['HTTP_USER_AGENT'])) : null;
}

function getHttpAcceptLanguage(): ?string
{
	return !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? (string) trim(mb_strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2))) : null;
}

function getExt(string $f): string
{
	return (string) pathinfo($f, PATHINFO_EXTENSION);
}

function getRandomUserAgent(): string
{
	$userAgents = [

		// Divers
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', // Firefox User Agents
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:122.0) Gecko/20100101 Firefox/122.0', // Firefox User Agents
		'Mozilla/5.0 (X11; Linux x86_64; rv:121.0) Gecko/20100101 Firefox/121.0', // Firefox User Agents
		'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.7624.1198 Mobile Safari/537.36', // Chrome 53 on Android (Marshmallow) LG Nexus 5
		'Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.6362.1125 Mobile Safari/537.36', // Chrome 51 on Android (Lollipop) Samsung SM-G900P
		'Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.7483.1921 Mobile Safari/537.36', // Chrome 54 on Android (Oreo) Google Pixel 2
		'Mozilla/5.0 (Linux; Android 14; J5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.6783.69 Mobile Safari/537.36', // Chrome 133 on Android 14
		'Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.7090.1114 Mobile Safari/537.36', // Chrome 43 on Android (Oreo) Google Pixel 2
		'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.8530.1964 Mobile Safari/537.36', // Chrome 46 on iOS 11 Apple iPhone
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3303.67 Safari/537.36 Edge/18.18362', // Edge 44 on Windows 10
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.20.124 Safari/537.36 Edg/91.0.85.59', // Edge 91 on Windows 10
		'Mozilla/5.0 (Linux; Android 9; CPH2083 Build/PPR1.180610.011; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36 EdgA/124.0.2478.64', // Edge 124 on Android (Pie)
		'Mozilla/5.0 (Linux; Android 15.0.0; SM-S931W) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.3124.105 Mobile Safari/537.36 EdgA/134.0.3124.105', // Edge 134 on Android 15 Samsung SM-S931W
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.79 (KHTML, like Gecko) Version/14.1.20 Safari/605.1.23', // Safari 14.1 on macOS
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6) AppleWebKit/537.36 (KHTML, like Gecko) Version/10.1.80 Safari/618.1.15', // Safari 10.1 on Mac OS X (Snow Leopard)
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:136.0) Gecko/20100101 Firefox/136.0/byA6CjeQHYiZ0Se', // Firefox 136 on Windows 10
		'Mozilla/5.0 (Windows NT 10.0; WOW64; x64; rv:134.0) Gecko/20100101 Firefox/134.0/nTnjbOREbT-86', // Firefox 134 on Windows 10
		'Mozilla/5.0 (Linux; Android 12; BRAVE) AppleWebKit/537.36 (KHTML like Gecko) Chrome/117.0.5877.0 Mobile Safari/537.36', // Brave on Android 12
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0 Brave Browser/107.1.45.123 Safari/537.36', // Brave on macOS (Mojave)
		'Mozilla/5.0 (Linux; U; Android 8.1.0; SM-G610M Build/M1AJQ; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/138.0.7204.63 Mobile Safari/537.36 OPR/11.2.2254.68178', // Opera 11.2 on Android (Oreo) Samsung SM-G610M
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', // Chrome on Windows
		'Mozilla/5.0 (Windows NT 11.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', // Chrome on Windows
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36', // Chrome on macOS
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_2_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', // Chrome on macOS
		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', // Chrome on Linux
		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36', // Chrome on Linux

		// Windows

		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0', // Edge 124 sur Windows 10 / 11
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.114 Safari/537.36 Edg/103.0.1264.62', // Edge 103 sur Windows 10 / 11
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.81 Safari/537.36 Edg/104.0.1293.47', // Edge 104 sur Windows 10 / 11
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:130.0) Gecko/20100101 Firefox/130.0', // Firefox 130 sur Windows 10 / 11
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0', // Firefox 120 sur Windows 10 / 11
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36 Edg/125.0.0.0', // Chrome 125 sur Windows 10 / 11
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.3', // Chrome 124 sur Windows 10 / 11
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 OPR/109.0.0.0', // Opera 109 sur Windows 10 / 11

		// Linux

		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36', // Chrome 124 sur Linux
		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36', // Chrome 123 sur Linux
		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', // Chrome 120 sur Linux
		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36', // Chrome 44 sur Linux
		'Mozilla/5.0 (X11; Linux i686; rv:125.0) Gecko/20100101 Firefox/125.0', // Firefox 125 sur Linux
		'Mozilla/5.0 (X11; Linux x86_64; rv:115.0) Gecko/20100101 Firefox/115.0', // Firefox 115 sur Linux
		'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:24.0) Gecko/20100101 Firefox/24.0', // Firefox 24 sur Ubuntu Linux
		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 OPR/109.0.0.0', // Opera 109 sur Linux

		// MacOS

		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 YaBrowser/24.1.0.0 Safari/537.36', // Yandex Browser Generic sur MacOS
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 YaBrowser/20.1.0.0 Safari/537.36', // Yandex Browser Generic sur MacOS
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', // Chrome 122 sur MacOS (Catalina)
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36', // Chrome 104 sur MacOS (Catalina)
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36', // Chrome 103 sur MacOS (Catalina)
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6.1 Safari/605.1.15', // Safari 15.6.1 sur MacOS (Catalina)
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6 Safari/605.1.15', // Safari 15.6 sur MacOS (Catalina)
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.5 Safari/605.1.15', // Safari 15.5 sur MacOS (Catalina)
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.4 Safari/605.1.15', // Safari 15.4 sur MacOS (Catalina)
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.2 Safari/605.1.15', // Safari 14.1 sur MacOS (Catalina)
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/605.1.33 (KHTML, like Gecko) Version/11.1 Safari/605.1.33', // Safari 11.1 sur MacOS (Catalina)
	];

	return (string) $userAgents[mt_rand(0, count($userAgents) - 1)];
}

function dns()
{
	// https://fr.wikipedia.org/wiki/Liste_des_enregistrements_DNS
	return [
	// 0							1					2										3						4												5
	['Enregistrement',				'Code IANA',		'RFC',									'Statut',				'Signification',								'Fonction'],
	['A',							'1',				'RFC 1035',								'actif',				'Address IPv4',									'Renvoie une ou plusieurs adresses IPv4 pour un nom d’hôte donné.'],
	['AAAA',						'28',				'RFC 3596',								'actif',				'Adresse IPv6',									'Renvoie une ou plusieurs adresses IPv6 pour un nom de domaine donné.'],
	['AAAA',						'28',				'RFC 3596',								'actif',				'Adresse IPv6',									'Renvoie une ou plusieurs adresses IPv6 pour un nom de domaine donné.'],
	['AAAA',						'28',				'RFC 3596',								'actif',				'Adresse IPv6',									'Renvoie une ou plusieurs adresses IPv6 pour un nom de domaine donné.'],
	['NS',							'2',				'RFC 1035',								'actif',				'Name Server',									'Délègue la gestion d’une zone à un serveur de nom faisant autorité'],
	['AFSDB',						'18',				'RFC 1183',								'actif',				'',												''],
	['AXFR',						'252',				'RFC 1035 et RFC 5936',					'actif',				'',												'Transfert de zone'],
	['Md',							'3',				'RFC 1035',								'OBSOLÈTE',				'',												''],
	['MF',							'4',				'RFC 1035',								'OBSOLÈTE',				'',												''],
	['CNAME',						'5',				'RFC 1035',								'actif',				'Canonical NAME',								'Permet de réaliser un alias (un raccourci) d’un hôte vers un autre.'],
	['SOA',							'6',				'RFC 1035',								'actif',				'Start Of Authority',							'Définit le serveur maitre du domaine.'],
	['MB',							'7',				'RFC 1035',								'EXPÉRIMENTAl',			'',												''],
	['MG',							'8',				'RFC 1035',								'EXPÉRIMENTAl',			'',												''],
	['MR',							'9',				'RFC 1035',								'EXPÉRIMENTAl',			'',												''],
	['NULl',						'10',				'RFC 1035',								'EXPÉRIMENTAl',			'',												''],
	['WKS',							'11',				'RFC 1035',								'actif',				'Well Known Service',							''],
	['PTR',							'12',				'RFC 1035',								'actif',				'Pointer',										'Réalise l’inverse de l’enregistrement A ou AAAA : donne un nom de host (FQDN) pour une adresse IP.'],
	['HINFO',						'13',				'RFC 1035',								'actif',				'Host information',								'Permet de spécifier le type de CPU (processeur) et le système d’exploitation de l’hôte concerné.'],
	['MINFO',						'14',				'RFC 1035',								'actif',				'Mail Information',								'Indique l’adresse mail du responsable du domaine mail ainsi que l’adresse à contacter en cas d’erreur.'],
	['MX',							'15',				'RFC 1035',								'actif',				'MX record',									'Définit le nom du Serveur de messagerie électronique|serveur de courrier du domaine'],
	['TXT',							'16',				'RFC 1035',								'actif',				'Texte',										'Permet d’enregistrer du texte non-structuré.'],
	['RP',							'17',				'RFC 1183',								'actif',				'Responsible Person',							'Définit une personne responsable du serveur (il y a toujours un champ TXT lorsqu’il y a un champ RP).'],
	['X25',							'19',				'RFC 1183',								'actif',				'',												''],
	['ISDN',						'20',				'RFC 1183',								'actif',				'',												''],
	['RT',							'21',				'RFC 1183',								'actif',				'',												''],
	['NSAP',						'22',				'RFC 1706',								'actif',				'',												''],
	['NSAP-PTR',					'23',				'RFC 1348',								'actif',				'',												''],
	['SIG',							'24',				'RFC 4034, RFC 3755 et RFC 2535',		'actif',				'DNSSEC',										''],
	['KEY',							'25',				'RFC 4034, RFC 3755 et RFC 2535',		'actif',				'DNSSEC',										''],
	['PX',							'26',				'RFC 2163',								'actif',				'',												''],
	['GPOS',						'27',				'RFC 1712',								'actif',				'',												''],
	['LOC',							'29',				'RFC 1876',								'actif',				'Localisation géographique',					''],
	['NXT',							'30',				'RFC 3755 et RFC 2535',					'OBSOLÈTE',				'',												''],
	['EId',							'31',				'Patton',								'actif',				'',												''],
	['NIMLOC',						'32',				'Patton',								'actif',				'',												''],
	['SRV',							'33',				'RFC 2782',								'actif',				'SRV record',									'Permet de définir un serveur spécifique pour une application, notamment pour la répartition de charge.'],
	['ATMA',						'34',				'ATMDOC',								'actif',				'',												''],
	['NAPTR',						'35',				'RFC 2915, RFC 2168 et RFC 3403',		'actif',				'',												''],
	['KX',							'36',				'RFC 2230',								'actif',				'',												''],
	['CERT',						'37',				'RFC 4398',								'actif',				'',												''],
	['A6',							'38',				'RFC 6563, RFC 3226 et RFC 2874',		'HISTORIQUE',			'Adresse IPv6',									''],
	['DNAME',						'39',				'RFC 2672',								'actif',				'Delegation Name',								'Alias pour un nom et tous ses sous-noms'],
	['SINK',						'40',				'Eastlake',								'actif',				'',												''],
	['OPT',							'41',				'RFC 2671',								'actif',				'EDNS',											'Extensions DNS comme l’augmentation de la taille des paquets.'],
	['APl',							'42',				'RFC 3123',								'actif',				'',												''],
	['DS',							'43',				'RFC 4034 et RFC 3658',					'actif',				'DNSSEC',										''],
	['SSHFP',						'44',				'RFC 4255',								'actif',				'',												''],
	['IPSECKEY',					'45',				'RFC 4025',								'actif',				'',												''],
	['RRSIG',						'46',				'RFC 4034 et RFC 3755',					'actif',				'DNSSEC',										'Resource Record Signature contient la signature numérique d’un RRset.'],
	['NSEC',						'47',				'RFC 4034 et RFC 3755',					'actif',				'DNSSEC',										''],
	['DNSKEY',						'48',				'RFC 4034 et RFC 3755',					'actif',				'DNSSEC',										'Contient la clé publique utilisée pour signer un RRSIG'],
	['DHCId',						'49',				'RFC 4701',								'actif',				'',												''],
	['NSEC3',						'50',				'RFC 5155',								'actif',				'DNSSEC',										''],
	['NSEC3PARAM',					'51',				'RFC 5155',								'actif',				'DNSSEC',										''],
	['TLSA',						'52',				'RFC 6698',								'actif',				'TLSA certificate association',					'Un enregistrement pour l’authentification d’entités nommées basée sur DNS (DANE).'],
	['HIP',							'55',				'RFC 5205',								'actif',				'Host Identity Protocol',						''],
	['NINFO',						'56',				'Reid',									'actif',				'',												''],
	['RKEY',						'57',				'Reid',									'actif',				'',												''],
	['TALINK',						'58',				'Wijngaards',							'actif',				'',												''],
	['CDS',							'59',				'RFC 7344',								'actif',				'Délégation Signer du domaine enfant',			''],
	['SPF',							'99',				'RFC 7208',								'OBSOLÈTE',				'',												'Le code 99 est obsolète. Le SPF doit être entré dans le champ TXT. Voir RFC7208, section 3.1'],
	['UINFO',						'100',				'IANA-Reserved',						'actif',				'',												''],
	['UId',							'101',				'IANA-Reserved',						'actif',				'',												''],
	['GId',							'102',				'IANA-Reserved',						'actif',				'',												''],
	['UNSPEC',						'103',				'IANA-Reserved',						'actif',				'',												''],
	['TKEY',						'249',				'RFC 2930',								'actif',				'',												''],
	['TSIG',						'250',				'RFC 2845',								'actif',				'',												''],
	['IXFR',						'251',				'RFC 1995',								'actif',				'',												''],
	['MAILB',						'253',				'RFC 1035',								'OBSOLÈTE',				'',												''],
	['MAILA',						'254',				'RFC 1035',								'OBSOLÈTE',				'',												''],
	['*',							'255',				'RFC 1035',								'actif',				'',												''],
	['CAA',							'257',				'RFC 6844',								'actif',				'DNS Certification Authority Authorization',	'Permet d’indiquer aux autorités de certification quelles sont celles autorisées à certifier le domaine.'],
	['TA',							'32768',			'Weiler',								'actif',				'',												''],
	['DLV',							'32769',			'RFC 4431',								'actif',				'DNSSEC Lookaside Validation',					''],
	['',							'',			'',							'',					'',					''],

	];
}

function modalTexte(array|string $str, string $txt): string
{
	return '<div class="modal fade" id="modal'.ucfirst(slug($txt)).'" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<p class="mb-0 modal-title" id="titre'.ucfirst(slug($txt)).'">Détails supplémentaires à propos de <span class="fw-bold">'.$txt.'</span></p>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
				</div>

				<div class="modal-body">
					<pre>'.print_r($str, true).'</pre>
				</div>

				<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button></div>
			</div>
		</div>
	</div>

	<p class="mb-0 text-center" data-bs-toggle="tooltip" data-bs-title="Afficher le '.$txt.' sous forme de texte brute"><a href="#" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modal'.ucfirst(slug($txt)).'">Afficher le '.$txt.' texte brute</a></p>';
}

function isoEmoji(string $codePays): string
{
	$codePays = ($codePays === 'en') ? 'GB' : $codePays;
	$codePays = ($codePays === 'uk') ? 'GB' : $codePays;

	static $codesValides = [
		'AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'AO', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AW', 'AX', 'AZ',
		'BA', 'BB', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BL', 'BM', 'BN', 'BO', 'BQ', 'BR', 'BS', 'BT', 'BV', 'BW', 'BY', 'BZ',
		'CA', 'CC', 'CD', 'CF', 'CG', 'CH', 'CI', 'CK', 'CL', 'CM', 'CN', 'CO', 'CR', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
		'DE', 'DJ', 'DK', 'DM', 'DO', 'DZ',
		'EC', 'EE', 'EG', 'EH', 'ER', 'ES', 'ET',
		'FI', 'FJ', 'FM', 'FO', 'FR',
		'GA', 'GB', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GL', 'GM', 'GN', 'GP', 'GQ', 'GR', 'GT', 'GU', 'GW', 'GY',
		'HK', 'HM', 'HN', 'HR', 'HT', 'HU',
		'ID', 'IE', 'IL', 'IM', 'IN', 'IO', 'IQ', 'IR', 'IS', 'IT',
		'JE', 'JM', 'JO', 'JP',
		'KE', 'KG', 'KH', 'KI', 'KM', 'KN', 'KP', 'KR', 'KW', 'KY', 'KZ',
		'LA', 'LB', 'LC', 'LI', 'LK', 'LR', 'LS', 'LT', 'LU', 'LV', 'LY',
		'MA', 'MC', 'MD', 'ME', 'MF', 'MG', 'MH', 'MK', 'ML', 'MM', 'MN', 'MO', 'MP', 'MQ', 'MR', 'MS', 'MT', 'MU', 'MV', 'MW', 'MX', 'MY', 'MZ',
		'NA', 'NC', 'NE', 'NF', 'NG', 'NI', 'NL', 'NO', 'NP', 'NR', 'NU', 'NZ',
		'OM',
		'PA', 'PE', 'PF', 'PG', 'PH', 'PK', 'PL', 'PM', 'PN', 'PR', 'PT', 'PW', 'PY',
		'QA',
		'RE', 'RO', 'RS', 'RU', 'RW',
		'SA', 'SB', 'SC', 'SD', 'SE', 'SG', 'SH', 'SI', 'SJ', 'SK', 'SL', 'SM', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SX', 'SY', 'SZ',
		'TC', 'TD', 'TF', 'TG', 'TH', 'TJ', 'TK', 'TL', 'TM', 'TN', 'TO', 'TR', 'TT', 'TV', 'TZ',
		'UA', 'UG', 'UM', 'US', 'UY', 'UZ',
		'VA', 'VC', 'VE', 'VG', 'VI', 'VN', 'VU',
		'WF', 'WS',
		'YE', 'YT', 'ZA', 'ZM', 'ZW',
	];

	$codePays = strtoupper($codePays);

	if(!in_array($codePays, $codesValides, true)) {
		return '🏁';
	}

	$emoji = [];
	for($i = 0; $i < strlen($codePays); $i++) {
		$codePoint = 0x1F1E6 + (ord($codePays[$i]) - ord('A'));
		$emoji[] = mb_convert_encoding('&#'.$codePoint.';', 'UTF-8', 'HTML-ENTITIES');
	}

	return implode($emoji);
}