<?php
use Symfony\Component\HttpClient\HttpClient;

function setMagnet(string $hash, ?string $tracker = null): ?string
{
	if(isSha1($hash))
	{
		$trackers = [];

		$newTrackon = get('https://newtrackon.com/api/all?include_ipv6_only_trackers=false');

		if(!empty($newTrackon))
		{
			$trackers = explode("\n", $newTrackon);
			$trackers = array_filter($trackers);
			sort($trackers);

			// Binouchette : d7w7YabaUGVln0VK9fCPKYOyAzF114l0
			// Poulok : VCqTzvtG2DeC8GQjXKVCR6Y795Limxe8

			if($tracker == 'ygg')		$tks[] = 'http://connect.maxp2p.org:8080/d7w7YabaUGVln0VK9fCPKYOyAzF114l0/announce';
			elseif($tracker == 'abn')	$tks[] = 'http://tracker.abn.lol:2710/vav11pxb3pimeic8p8v0s0udekzz34az/announce';

			$i = 0;
			foreach($trackers as $tk) {
				$tks[] = $tk;

				$i++;
				if($i >= 19) break;
			}
		}

		else
			$tks = ['udp://tracker.opentrackr.org:1337/announce', 'http://openbittorrent.com:80/announce'];

		return 'magnet:?xt=urn:btih:'.mb_strtolower($hash).'&tr='.implode('&tr=', $tks);
	}

	return
		null;
}

function bandeAnnonce(array|null $videos): ?string
{
	if(empty($videos)) {
		return null;
	}

	$youtube_fr = null;
	$youtube_vostfr = null;
	$youtube = null;
	$vimeo = null;

	foreach($videos as $item)
	{
		if($item->type !== 'Trailer' OR !preg_match('/vimeo|youtube/is', $item->site)) {
			continue;
		}

		if(preg_match('/youtube/i', $item->site) && isYoutubeId($item->key))
		{
			if($item->iso_639_1 === 'fr' && !preg_match('/vost(fr)?/i', $item->name)) {
				$youtube_fr ??= '<a href="https://www.youtube.com/embed/'.$item->key.'" data-fancybox="gallerie">'.logoYouTube(21).'</a>';
			}

			elseif($item->iso_639_1 === 'fr' && preg_match('/vost(fr)?/i', $item->name)) {
				$youtube_vostfr ??= '<a href="https://www.youtube.com/embed/'.$item->key.'" data-fancybox="gallerie">'.logoYouTube(21).'</a>';
			}

			else {
				$youtube ??= '<a href="https://www.youtube.com/embed/'.$item->key.'" data-fancybox="gallerie">'.logoYouTube(21).'</a>';
			}
		}

		if(preg_match('/vimeo/i', $item->site) && isVimeoId($item->key)) {
			$vimeo ??= '<a href="https://vimeo.com/'.$item->key.'" data-fancybox="gallerie">'.logoVimeo(21).'</a>';
		}
	}

	return $youtube_fr
		?? $youtube_vostfr
		?? $youtube
		?? $vimeo
		?? null;
}

function crew(?array $crews, string $categorie, string $type): ?string
{
	if(isset($crews) AND !empty($crews))
	{
		$i = 0;
		$equipesArray = [];

		if($categorie === 'réalisateur')	$filtreMetier = ['Director', 'Co-Director'];
		elseif($categorie === 'scénariste')	$filtreMetier = ['Writer', 'Screenplay', 'Story'];

		foreach($crews as $k => $crew)
		{
			if($type === 'film') {
				if(in_array($crew->job, $filtreMetier)) {
					$equipesArray[] = '<span class="badge bg-primary" title="Secteur : '.jobsFr($crew->department).'"><a href="?person_id='.$crew->id.'&merge&crew&'.$type.'" class="link-light">'.$crew->name.'</a></span>';

					$i++;
				}
			}

			elseif($categorie === 'createur' AND !empty($crew->id)) {
				$equipesArray[] = '<span class="badge bg-primary" title="Créateur de la série : '.ucwords(jobsFr($crew->name)).'"><a href="?person_id='.$crew->id.'&merge&crew&'.$type.'" class="link-light">'.$crew->name.'</a></span>';
			}

			if($i > 4) break;
		}

		$equipesArray = array_unique($equipesArray);

		return !empty($equipesArray) ? implode(' ', $equipesArray) : $categorie.' inconnu';
	}

	return null;
}

function cast(?array $casts, string $type): ?string
{
	if(isset($casts) AND !empty($casts))
	{
		$i = 0;
		$acteursArray = [];

		foreach($casts as $k => $cast)
		{
			if($type === 'film') {
				$acteursArray[] = '<span class="badge bg-primary" title="'.(!empty($cast->character) ? 'Nom dans le film : '.$cast->character : 'Nom dans le film inconnu').'"><a href="?person_id='.$cast->id.'&merge&cast&'.$type.'" class="link-light">'.$cast->name.'</a></span>';
			}

			elseif($type === 'serie') {
				$acteursArray[] = '<span class="badge bg-primary" title="'.(!empty($cast->roles[0]->character) ? 'Nom dans la série : '.$cast->roles[0]->character : 'Nom dans la série inconnu').'"><a href="?person_id='.$cast->id.'&merge&cast&'.$type.'" class="link-light">'.$cast->name.'</a></span>';
			}

			$i++;
			if($i > 4) break;
		}

		$acteursArray = array_unique($acteursArray);

		return !empty($acteursArray) ? implode(' ', $acteursArray) : 'acteur inconnu';
	}

	return null;
}

function genres(?array $genres): ?string
{
	$type = in_array($_GET['type'] ?? '', ['film', 'serie']) ? secuChars($_GET['type']).'s' : 'films';

	if(isset($genres) AND !empty($genres))
	{
		$i = 0;
		$genresArray = [];

		foreach($genres as $k => $g)
		{
			$name = genresFr($g->name);

			$genresArray[] = '<span class="badge bg-success" title="Genre : '.$name.'"><a href="/projets/tmdb/'.$type.'/genre/'.secu($g->id).'-'.slug($g->name).'" class="link-light">'.$name.'</a></span>';

			$i++;
			if($i > 2) break;
		}

		$genresArray = array_unique($genresArray);

		$i > 1 ? sort($genresArray) : null;

		return !empty($genresArray) ? implode(' ', $genresArray) : 'genre inconnu';
	}

	return null;
}

function countries(PDO $pdo, ?array $paysOrigine): ?string
{
	$type = in_array($_GET['type'] ?? '', ['film', 'serie']) ? secuChars($_GET['type']).'s' : 'films';

	if(isset($paysOrigine) AND !empty($paysOrigine) AND count($paysOrigine) > 0)
	{
		$i = 0;
		$paysArray = [];
		foreach($paysOrigine as $pays)
		{
			try {
				$stmt = $pdo->prepare('SELECT name, translations, emoji FROM countries WHERE iso2 = :pays');
				$stmt->execute(['pays' => (string) strtoupper($pays)]);
				$paysSql = $stmt->fetch();
			} catch (\PDOException $e) {
				return null;
			}

			if(!empty($paysSql))
			{
				$translations = json_decode($paysSql['translations']);

				if(!empty($translations->fr))
					$paysArray[] = '<span class="badge bg-info" title="Pays d’origine : '.$translations->fr.'"><a href="/projets/tmdb/'.$type.'/origine/'.mb_strtolower($pays).'" class="link-light">'.$paysSql['emoji'].' '.$translations->fr.'</a></span>';

				$i++;
				if($i > 4) break;
			}
		}

		return !empty($paysArray) ? implode(' ', $paysArray) : 'origine inconnue';
	}

	return null;
}

function images(?array $images): ?array
{
	if(isset($images) AND !empty($images))
	{
		$imagesArray = [];

		foreach($images as $cle => $v)
		{
			$imagesArray[] = !empty($v->file_path) ? trim('https://image.tmdb.org/t/p/original'.$v->file_path) : null;

			if(!empty($imagesArray[199])) break;
		}

		$imgs = array_filter($imagesArray);

		return !empty($imgs) ? $imgs : null;
	}

	return null;
}

function recommandations(?array $recommandations): ?array
{
	$type = in_array($_GET['type'] ?? '', ['film', 'serie']) ? secuChars($_GET['type']) : 'film';

	if(isset($recommandations) AND !empty($recommandations))
	{
		$recommandationsArray = [];

		foreach($recommandations as $cle => $s)
		{
			$id		= !empty($s->id) ? $s->id : 0;
			$titre	= ($type === 'film') ? (!empty($s->title) ? $s->title : 'titre du film inconnu') : (!empty($s->name) ? $s->name : 'titre de la série inconnu');
			$bp		= !empty($s->backdrop_path) ? 'https://image.tmdb.org/t/p/original'.$s->backdrop_path : 'https://dummyimage.com/1280x720/cccccc/555555.png?text='.urlencode($titre);

			$recommandationsArray[] = $id.'|'.$bp.'|'.$titre;

			if(isset($recommandationsArray[9])) break;
		}

		return $recommandationsArray;
	}

	return null;
}

function similaires(?array $similaires): ?array
{
	$type = in_array($_GET['type'] ?? '', ['film', 'serie']) ? secuChars($_GET['type']) : 'film';

	if(isset($similaires) AND !empty($similaires))
	{
		$similairesArray = [];

		foreach($similaires as $cle => $s)
		{
			$id		= !empty($s->id) ? $s->id : 0;
			$titre	= ($type === 'film') ? (!empty($s->title) ? $s->title : 'titre du film inconnu') : (!empty($s->name) ? $s->name : 'titre de la série inconnu');
			$bp		= !empty($s->backdrop_path) ? 'https://image.tmdb.org/t/p/original'.$s->backdrop_path : 'https://dummyimage.com/1280x720/cccccc/555555.png?text='.urlencode($titre);

			$similairesArray[] = $id.'|'.$bp.'|'.$titre;

			if(isset($similairesArray[9])) break;
		}

		return $similairesArray;
	}

	return null;
}

function genresFr(string $genre): string
{
	return trim(strtr($genre, [
		'Science-Fiction' => 'Science-fiction',
		'Action & Adventure' => 'Action et aventure',
		'Kids' => 'Enfants',
		'News' => 'Actualités',
		'Reality' => 'Réalité',
		'Science-Fiction & Fantastique' => 'Science-fiction et fantastique',
		'Talk' => 'Émission-débat',
		'War & Politics' => 'Guerre et politique'
	]));
}

function statusFr(string $status, string $type = 'film'): string
{
	return ucfirst(trim(strtr(mb_strtolower($status), [
		'returning series' => 'retour de série',
		'post production' => 'post-production',
		'in production' => 'en production',
		'canceled' => 'annulé'.($type === 'serie' ? 'e' : null),
		'released' => 'sortie',
		'planned' => 'planifié'.($type === 'serie' ? 'e' : null),
		'rumored' => 'rumeur',
		'pilot' => 'pilote',
		'ended' => 'terminée',
	])));
}

function charsFr(string $char, int $gender = 2): string
{
	return trim(strtr(mb_strtolower($char), [
		'sign language translator' => 'traducteur en langue des signes',
		'various characters' => 'personnages divers',
		'(archive footage)' => '(images d’archives)',
		'director #1 - ny' => 'directeur #1 - NY',
		'interviewee' => 'interviewé',
		'uncredited' => 'non crédité'.($gender == 1 ? 'e' : null),
		'filmmaker' => 'cinéaste',
		'narrator' => 'narration',
		'himself' => 'lui-même',
		'guest' => 'invité',
		'voice' => 'voix',
		'host' => 'hôte',
		'self' => 'soi-même',
	]));
}

function jobsFr(string $job, int $gender = 2): string
{
	// https://developer.themoviedb.org/reference/configuration-jobs

	return trim(strtr(mb_strtolower($job), [
		'second unit director of photography' => ($gender == 1 ? 'directrice' : 'directeur').' photographie de la seconde unité',
		'visual effects production assistant' => 'assistant'.($gender == 1 ? 'e' : null).' production des effets visuels',
		'additional director of photography' => ($gender == 1 ? 'directrice' : 'directeur').' photographie additionnel'.($gender == 1 ? 'le' : null),
		'supervising sound effects editor' => 'superviseur du montage des effets sonores',
		'second assistant art director' => 'deuxième '.($gender == 1 ? 'directrice' : 'directeur').' artistique adjoint',
		'additional script supervisor' => 'superviseur de scénario supplémentaire',
		'production office assistant' => 'assistant'.($gender == 1 ? 'e' : null).' bureau de production',
		'art department coordinator' => ($gender == 1 ? 'coordinatrice' : 'coordinateur').' du département artistique',
		'special effects supervisor' => 'superviseur effets spéciaux',
		'visual effects coordinator' => ($gender == 1 ? 'coordinatrice' : 'coordinateur').' effets visuels',
		'visual effects supervisor' => 'superviseur des effets visuels',
		'executive music producer' => ($gender == 1 ? 'productrice' : 'producteur').' exécutif de la musique',
		'sound re-recording mixer' => 'mixage de réenregistrement du son',
		'supervising sound editor' => 'superviseur du montage sonore',
		'director of photography' => ($gender == 1 ? 'directrice' : 'directeur').' photographie',
		'direction of operations' => 'direction des opérations',
		'original music composer' => 'compositeur de musique originale',
		'art department manager' => 'responsable du département artistique',
		'assistant art director' => ($gender == 1 ? 'directrice' : 'directeur').' artistique adjoint',
		'sound design assistant' => 'assistant'.($gender == 1 ? 'e' : null).' conception sonore',
		'special guest director' => ($gender == 1 ? 'directrice' : 'directeur').' des invités spéciaux',
		'co-executive producer' => 'co-'.($gender == 1 ? 'productrice' : 'producteur').' exécutif',
		'digital storyboarding' => 'scénarimage numérique',
		'animation supervisor' => 'supervision de l’animation',
		'hair department head' => 'chef du département coiffure',
		'supervising animator' => 'chef de l’animation',
		'additional writing' => 'rédaction supplémentaire',
		'compositing artist' => 'artiste compositeur',
		'rotoscoping artist' => 'artiste rotoscopie',
		'associate producer' => ($gender == 1 ? 'productrice associée' : 'producteur associé'),
		'executive producer' => ($gender == 1 ? 'productrice' : 'producteur').' exécutif',
		'costume & make-up' => 'costumes et maquillage',
		'production artist' => 'artiste production',
		'production design' => 'conception de la production',
		'stunt coordinator' => ($gender == 1 ? 'coordinatrice' : 'coordinateur').' cascade',
		'supervising sound' => 'supervision du son',
		'assistant camera' => 'assistant'.($gender == 1 ? 'e' : null).' caméra',
		'story supervisor' => 'supervision de l’histoire',
		'camera operator' => 'opérateur caméra',
		'special effects' => 'effets spéciaux',
		'cinematography' => 'cinématographie',
		'costume design' => 'conception de costumes',
		'music arranger' => 'arrangement musical',
		'sound designer' => 'conception sonore',
		'sound engineer' => 'ingénieur du son',
		'visual effects' => 'effets visuels',
		'adr & dubbing' => 'doublage',
		'art direction' => 'direction artistique',
		'data wrangler' => 'gestionnaire de données',
		'hair designer' => 'coiffure',
		'makeup artist' => 'maquillage',
		'prop designer' => 'conception d’accessoires',
		'series writer' => ($gender == 1 ? 'rédactrice' : 'rédacteur').' de la série',
		'fix animator' => 'aide à l’animation',
		'foley artist' => 'Bruiteur',
		'orchestrator' => 'orchestrateur',
		'stunt driver' => 'cascadeur voiture',
		'stunt double' => 'doubleur cascade',
		'co-producer' => 'co-production',
		'hairdresser' => 'coiffure',
		'hairstylist' => 'coiffure',
		'characters' => 'personnages',
		'compositor' => ($gender == 1 ? 'compositrice' : 'compositeur'),
		'screenplay' => 'scénario',
		'storyboard' => 'scénarimage',
		'vfx artist' => 'artiste effets visuels',
		'directing' => 'direction',
		'presenter' => 'présentation',
		'conductor' => 'chef d’orchestre',
		'director' => 'direction',
		'lighting' => 'éclairage',
		'modeling' => 'modélisation',
		'producer' => 'production',
		'sculptor' => 'sculpture',
		'creator' => 'créateur',
		'editing' => 'édition',
		'himself' => 'soi-même',
		'writing' => 'écriture',
		'acting' => ($gender == 1 ? 'actrice' : 'acteur'),
		'camera' => 'caméra',
		'editor' => 'éditeur',
		'script' => 'scénario',
		'stunts' => 'cascadeur',
		'thanks' => 'remerciements',
		'writer' => 'écriture',
		'cameo' => 'caméo',
		'music' => 'musique',
		'story' => 'histoire',
		'stunt' => 'cascadeur',
		'sound' => 'son',
		'cast' => 'acteur',
		'crew' => 'équipe',
		'"a"' => '',
	]));
}

function paysFr(string $m): string
{
	return trim(strtr($m, [
		'Algeria' => 'Algérie', 'Argentina' => 'Argentine', 'Armenia' => 'Arménie', 'Australia' => 'Australie', 'Australasia' => 'Australasie', 'Austria' => 'Autriche', 'Azerbaijan' => 'Azerbaïdjan',
		'Belarus' => 'Biélorussie', 'Belgium' => 'Belgique', 'Brazil' => 'Brésil', 'Bulgaria' => 'Bulgarie',
		'Cambodia' => 'Cambodge', 'Chile' => 'Chili', 'China' => 'Chine', 'Colombia' => 'Colombie', 'Cyprus' => 'Chypre', 'Czech Republic' => 'République tchèque', 'Czechoslovakia' => 'Tchécoslovaquie',
		'Denmark' => 'Danemark', 'Dominican Republic' => 'République dominicaine',
		'East Germany' => 'République démocratique allemande', 'East Timor' => 'Timor-Leste', 'England' => 'Britannique', 'Egypt' => 'Égypte', 'Estonia' => 'Estonie',
		'Finland' => 'Finlande',
		'Germany' => 'Allemagne', 'Georgia' => 'Géorgie', 'Greece' => 'Grèce',
		'Hungary' => 'Hongrie', 'Hong-Kong' => 'Hong Kong', 'Hong-kong' => 'Hong Kong', 'Hong kong' => 'Hong Kong',
		'Iceland' => 'Islande', 'India' => 'Inde', 'Indonesia' => 'Indonésie', 'Ireland' => 'Irlande', 'Israel' => 'Israël', 'Italy' => 'Italie',
		'Japan' => 'Japon',
		'Kuwait' => 'Koweït',
		'Latvia' => 'Lettonie', 'Lithuania' => 'Lituanie',
		'Malaysia' => 'Malaisie', 'Malta' => 'Malte', 'Mauritius' => 'Maurice', 'Mexico' => 'Mexique', 'Micronesia' => 'Micronésie', 'Mongolia' => 'Mongolie', 'Morocco' => 'Maroc',
		'Netherlands' => 'Pays-Bas', 'New Zealand' => 'Nouvelle-Zélande', 'Northern Ireland' => 'Irlande du Nord', 'Norway' => 'Norvège',
		'Peru' => 'Pérou', 'Poland' => 'Pologne', 'Portugal' => 'Portugal', 'Puerto Rico' => 'Porto Rico',
		'Republic of North Macedonia' => 'Macédoine du Nord', 'Romania' => 'Roumanie', 'Russia' => 'Russie',
		'Saudi Arabia' => 'Arabie saoudite', 'Senegal' => 'Sénégal', 'Serbia and Montenegro' => 'Serbie-et-Monténégro', 'Serbia' => 'Serbie', 'Singapore' => 'Singapour', 'Slovakia' => 'Slovaquie', 'South Africa' => 'Afrique du Sud', 'South Korea' => 'Corée du Sud', 'Soviet Union' => 'Union des républiques socialistes soviétiques', 'Spain' => 'Espagne', 'Sweden' => 'Suède', 'Switzerland' => 'Suisse',
		'Taiwan' => 'Taïwan', 'Thailand' => 'Thaïlande', 'The Democratic Republic Of Congo' => 'République démocratique du Congo', 'Tunisia' => 'Tunisie', 'Turkey' => 'Turquie',
		'UK' => 'Britannique', 'United Arab Emirates' => 'Émirats arabes unis', 'United Kingdom' => 'Britannique', 'United States' => 'États-Unis', 'United States of America' => 'États-Unis', 'USA' => 'États-Unis',
		'West Germany' => 'Allemagne',
		'Yugoslavia' => 'Yougoslavie'
	]));
}

function logoBitTorrent(?int $h = 20): string							{ return '<img src="/assets/img/logo-bittorrent.svg"		style="height: '.$h.'px;"					alt="BitTorrent"			title="BitTorrent">'; }
function logoDisneyPlus(?int $h = 20): string							{ return '<img src="/assets/img/logo-disney-plus.svg"		style="height: '.$h.'px;"					alt="Disney+"				title="DisneyPlus.com">'; }
function logoGoogle(?int $h = 20): string								{ return '<img src="/assets/img/logo-google.svg"			style="height: '.$h.'px;"					alt="Google Traduction"		title="Google Traduction">'; }
function logoIMDb(?int $h = 20, ?string $style = null): string			{ return '<img src="/assets/img/logo-imdb.svg"				style="height: '.$h.'px; '.$style.'"		alt="IMDb"					title="IMDb.com">'; }
function logoNetflix(?int $h = 20): string								{ return '<img src="/assets/img/logo-netflix.svg"			style="height: '.$h.'px;"					alt="Netflix"				title="Netflix.com">'; }
function logoNetflixMini(?int $h = 20, ?string $style = null): string	{ return '<img src="/assets/img/logo-netflix-mini.svg"		style="height: '.$h.'px; '.$style.'"		alt="Netflix"				title="Netflix.com">'; }
function logoRadarr(?int $h = 20): string								{ return '<img src="/assets/img/logo-radarr.svg"			style="height: '.$h.'px;"					alt="Radarr"				title="Radarr">'; }
function logoTMDB(?int $h = 20): string									{ return '<img src="/assets/img/logo-tmdb.svg"				style="height: '.$h.'px;"					alt="TMDB"					title="TMDB.com">'; }
function logoTMDBMini(?int $h = 20, ?int $w = null): string				{ return '<img src="/assets/img/logo-tmdb-mini.svg"			style="height: '.$h.'px; width: '.$w.'px;"	alt="TMDB"					title="TMDB.com">'; }
function logoVimeo(?int $h = 20): string								{ return '<img src="/assets/img/logo-vimeo.svg"				style="height: '.$h.'px;"					alt="Vimeo"					title="Vimeo.com">'; }
function logoW3CLove(): string											{ return '<img src="/assets/img/logo-w3c-ilove.png"			style="height: 15px;"						alt="W3C"					title="W3C.org">'; }
function logoYouTube(?int $h = 20): string								{ return '<img src="/assets/img/logo-youtube.svg"			style="height: '.$h.'px;"					alt="YouTube"				title="YouTube.com">'; }