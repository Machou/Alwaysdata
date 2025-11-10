<?php
require_once '../../../config/bdd.php';
require_once '../../../config/config.php';

$cacheT				= '-7 days';
// $cacheT				= '-2 seconds';
$cacheTPopulaires	= '-12 hours';
// $cacheTPopulaires	= '-2 seconds';
$titreRecherche		= !empty($_GET['titre'])				? (string) $_GET['titre']					: null;
$titreRecherche		= !empty($_GET['titreRelease'])			? nettoyageRelease($_GET['titreRelease'])	: $titreRecherche;
$id					= !empty($_GET['id'])					? secu($_GET['id'])							: null;
$person_id			= !empty($_GET['person_id'])			? secu($_GET['person_id'])					: null;
$anneeRecherche		= !empty($_GET['anneeRecherche'])		? (int) isYear($_GET['anneeRecherche'])		: null;
$genre_id			= !empty($_GET['genre_id'])				? secu($_GET['genre_id'])					: null;
$motcle_id			= !empty($_GET['motcle_id'])			? secu($_GET['motcle_id'])					: null;
$origine			= !empty($_GET['origine'])				? secuChars(strtoupper($_GET['origine']))	: null;
$page				= isset($_GET['page']) ? (($_GET['page'] >= 1 AND $_GET['page'] <= 1000) ? (int) $_GET['page'] : 1) : 1;
$type				= (!empty($_GET['type']) AND in_array($_GET['type'], ['film', 'films', 'serie', 'series'])) ? secuChars($_GET['type']) : null;

$tmdb = new TMDB();

// Fiche du film ou de la série

if(!empty($type) AND !empty($id) AND !isset($_GET['saison']) AND !isset($_GET['fullCast']) AND !isset($_GET['populaires']) AND !isset($_GET['exclus']) AND !isset($_GET['hash']) AND !isset($_GET['seedbox']) AND !isset($_GET['note-le']))
{
	if($type === 'film')
	{
		$t					= $tmdb->getMovie($id);
		$titre				= !empty($t->title) ? trim($t->title) : null;

		$t_external			= $tmdb->getMovieExternalIds($id);
		$t_credits			= $tmdb->getMovieCredits($id);
		$t_videos			= $tmdb->getMovieVideos($id);
		$t_keywords			= $tmdb->getMovieKeywords($id);
		$t_similaires		= $tmdb->getMovieSimilar($id);
		$t_recommandations	= $tmdb->getMovieRecommendations($id);
		$t_imgs				= $tmdb->getMovieImages($id, 'fr,en,null');

		if(isset($_GET['maj']))
		{
			if(is_file($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/fiche_film_'.$id.'.cache'))				unlink($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/fiche_film_'.$id.'.cache');
			if(is_file($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/images_film_'.$id.'.cache'))			unlink($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/images_film_'.$id.'.cache');
			if(is_file($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/recommandations_film_'.$id.'.cache'))	unlink($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/recommandations_film_'.$id.'.cache');
			if(is_file($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/similaires_film_'.$id.'.cache'))		unlink($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/similaires_film_'.$id.'.cache');

			nettoyerImdb();
		}
	}

	elseif($type === 'serie')
	{
		$t					= $tmdb->getTv($id);
		$titre				= !empty($t->name) ? trim($t->name) : null;

		$t_external			= $tmdb->getTvExternalIds($id);
		$t_credits			= $tmdb->getTvAggregateCredits($id);
		$t_videos			= $tmdb->getTvVideos($id);
		$t_keywords			= $tmdb->getTvKeywords($id);
		$t_similaires		= $tmdb->getTvSimilar($id);
		$t_recommandations	= $tmdb->getTvRecommendations($id);
		$t_imgs				= $tmdb->getTvImages($id, 'fr,en,null');

		if(isset($_GET['maj']))
		{
			if(is_file($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/fiche_serie_'.$id.'.cache'))			unlink($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/fiche_serie_'.$id.'.cache');
			if(is_file($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/images_serie_'.$id.'.cache'))			unlink($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/images_serie_'.$id.'.cache');
			if(is_file($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/recommandations_serie_'.$id.'.cache'))	unlink($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/recommandations_serie_'.$id.'.cache');
			if(is_file($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/similaires_serie_'.$id.'.cache'))		unlink($_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/similaires_serie_'.$id.'.cache');

			nettoyerImdb();
		}
	}

	$id = !empty($t->id) ? (int) $t->id : null;
	$titreEntete = !empty($titre) ? (string) trim($titre).' - ' : null;
}

// Genre, origine ou mot clé d’un film ou d’une série

elseif(!empty($type) AND (!empty($genre_id) OR !empty($motcle_id) OR !empty($origine)) AND !isset($_GET['saison']) AND !isset($_GET['fullCast']) AND !isset($_GET['populaires']) AND !isset($_GET['exclus']) AND !isset($_GET['hash']) AND !isset($_GET['seedbox']) AND !isset($_GET['note-le']))
{
	if(!empty($genre_id))
	{
		if($type === 'films')		$genre_nnom = !empty($genre_id) ? $tmdb->getMovieGenres('fr') : null;
		elseif($type === 'series')	$genre_nnom = !empty($genre_id) ? $tmdb->getTvGenres('fr') : null;

		foreach($genre_nnom->genres as $g)
		{
			if($g->id === $genre_id) {
				$g_id = !empty($g->id) ? (int) $g->id : null;
				$g_nom = !empty($g->name) ? (string) genresFr(secuChars($g->name)) : null;
			}
		}

		$titreEntete = !empty($g_nom) ? 'Genre : '.$g_nom.' - '.(!empty($anneeRecherche) ? 'Année : '.$anneeRecherche.' - ' : null).($page > 1 ? 'Page : '.$page.' - ' : null) : null;
	}

	elseif(!empty($motcle_id))
	{
		$motcle_nomF = !empty($motcle_id) ? $tmdb->getKeywordDetails($motcle_id) : null;
		$motcle_nom = !empty($motcle_nomF) ? secuChars($motcle_nomF->name) : null;

		$titreEntete = !empty($motcle_nom) ? 'Mot clé : '.ucfirst($motcle_nom).' - '.(!empty($anneeRecherche) ? 'Année : '.$anneeRecherche.' - ' : null).($page > 1 ? 'Page : '.$page.' - ' : null) : null;
	}

	elseif(!empty($origine))
	{
		$liste_pays = $tmdb->getConfigurationCountries();

		foreach($liste_pays as $pays)
		{
			if($pays->iso_3166_1 === $origine) {
				$p_iso = !empty($pays->iso_3166_1) ? secuChars($pays->iso_3166_1) : null;
				$p_iso_nom = !empty($pays->native_name) ? secuChars(paysFr($pays->native_name)) : null;
			}
		}

		$titreEntete = !empty($p_iso_nom) ? 'Origine : '.$p_iso_nom.' - '.(!empty($anneeRecherche) ? 'Année : '.$anneeRecherche.' - ' : null).($page > 1 ? 'Page : '.$page.' - ' : null) : null;
	}

	else
		$titreEntete = null;
}

// Fiche d’un artiste

elseif(empty($type) AND !empty($person_id))
{
	$p = $tmdb->getPerson($person_id);
	$p_external = $tmdb->getPersonExternalIds($person_id);
	$p_credits = $tmdb->getPersonCombinedCredits($person_id);

	$person_id = !empty($p->id) ? (int) $p->id : null;
	$titreEntete = !empty($p->name) ? (string) trim($p->name).' - ' : null;
}

// Distribution des rôles et équipe technique

elseif(!empty($type) AND !empty($id) AND isset($_GET['fullCast']))
{
	if($type === 'film')
	{
		$t = $tmdb->getMovie($id);
		$t_credits = $tmdb->getMovieCredits($id);

		$titre = !empty($t->title) ? (string) trim($t->title) : null;
		$titreEntete = 'Rôles et équipe technique de '.$titre.' - ';
	}

	elseif($type === 'serie')
	{
		$saison = (int) (!empty($_GET['saison']) AND $_GET['saison'] > 0) ? secu($_GET['saison']) : null;
		$episode = (int) (!empty($_GET['episode']) AND $_GET['episode'] > 0) ? secu($_GET['episode']) : null;

		$t = $tmdb->getTv($id);
		$t_saisons = $tmdb->getTvSeasons($id, $saison);

		$titre = !empty($t->name) ? (string) trim($t->name) : null;

		if(!empty($saison))
		{
			if($saison > $t->number_of_seasons) {
				header('Location: /projets/tmdb/?type=serie&id='.$id.'&fullCast');
				die();
			}

			if(empty($episode)) {
				$t_credits = $tmdb->getTvSeasonsAggregateCredits($id, $saison);

				$titreEntete = 'Rôles et équipe technique de '.$titre.' - Saison '.$saison.' - ';
			}

			elseif(!empty($episode)) {
				function getEpisode($episodes, $episode_number) {
					foreach($episodes as $episode) {
						if($episode->episode_number == $episode_number) {
							return $episode;
						}
					}

					return null;
				}

				if(empty(getEpisode($t_saisons->episodes, $episode))) {
					header('Location: /projets/tmdb/?type=serie&id='.$id.'&fullCast');
					die();
				}

				$t_credits = $tmdb->getTvEpisodesCredits($id, $saison, $episode);

				$titreEntete = 'Rôles et équipe technique de '.$titre.' - Saison '.$saison.' Épisode '.$episode.' - ';
			}
		}

		else
		{
			$t_credits = $tmdb->getTvAggregateCredits($id);

			$titreEntete = 'Rôles et équipe technique de '.$titre.' - ';
		}
	}

	$id = !empty($t->id) ? (int) $t->id : null;
}

// Les Saisons & Épisodes

elseif(!empty($type) AND !empty($id) AND isset($_GET['saison']) AND !isset($_GET['fullCast']) AND !isset($_GET['populaires']) AND !isset($_GET['exclus']) AND !isset($_GET['hash']) AND !isset($_GET['seedbox']) AND !isset($_GET['note-le']))
{
	$t = $tmdb->getTv($id);

	$id = !empty($t->id) ? (int) $t->id : null;
	$titre = !empty($t->name) ? (string) trim($t->name) : null;
	$titreEntete = !empty($titre) ? 'Les saisons et épisodes de '.$titre.' - ' : null;
}

elseif(isset($_GET['exclus']))		$titreEntete = 'Exclusivités - '.(isset($_GET['ygg']) ? 'YGG - ' : null).(isset($_GET['1337x']) ? '1337x - ' : null); // Exclusivités
elseif(isset($_GET['genres']))		$titreEntete = 'Les Genres - '; // Les Genres
elseif(isset($_GET['origines']))	$titreEntete = 'Les Pays - '; // Les Pays
elseif(isset($_GET['hash']))		$titreEntete = 'Hash 2 Magnet - '; // Hash 2 Magnet
elseif(isset($_GET['seedbox']))		$titreEntete = 'Seedbox - '; // Seedbox
elseif(isset($_GET['note-le']))		$titreEntete = 'Note le ! - '.(!empty($_POST['titre']) ? 'Recherche : '.secuChars($_POST['titre']).' - ' : null); // Note le !

elseif(isset($_GET['films']))
{
	if(isset($_GET['populaires']))		$titreEntete = 'Films populaires - '.(!empty($page) ? 'Page : '.$page.' - ' : null);
	elseif(isset($_GET['tendances']))	$titreEntete = 'Films tendances - '.(!empty($page) ? 'Page : '.$page.' - ' : null);
	elseif(isset($_GET['notes']))		$titreEntete = 'Films les mieux notés - '.(!empty($page) ? 'Page : '.$page.' - ' : null);
}

elseif(isset($_GET['series']))
{
	if(isset($_GET['populaires']))		$titreEntete = 'Séries populaires - '.(!empty($page) ? 'Page : '.$page.' - ' : null);
	elseif(isset($_GET['tendances']))	$titreEntete = 'Séries tendances - '.(!empty($page) ? 'Page : '.$page.' - ' : null);
	elseif(isset($_GET['notes']))		$titreEntete = 'Séries les mieux notées - '.(!empty($page) ? 'Page : '.$page.' - ' : null);
}

elseif(!empty($titreRecherche))		$titreEntete = 'Résultat pour : '.secuChars($titreRecherche).(!empty($anneeRecherche) ? ' - Année : '.$anneeRecherche.' - ' : null).' - '.(!empty($page) ? 'Page : '.$page.' - ' : null); // Recherche

echo '<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title>'.(!empty($titreEntete) ? $titreEntete : null).'βяαιη vιδéo</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<link rel="icon" type="image/png" href="/favicon.png">
	<link rel="stylesheet" href="/assets/css/vendors.css?'.filemtime('../../assets/css/vendors.css').'">
	<link rel="stylesheet" href="/assets/css/brain-video.css?'.filemtime('../../assets/css/brain-video.css').'">

	<script src="/assets/js/vendors.js?'.filemtime('../../assets/js/vendors.js').'"></script>
</head>

<body>
<header class="mb-4">
	<div class="container">
		<nav class="navbar navbar-expand-lg rounded-bottom">
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Afficher / masquer la navigation"><span class="navbar-toggler-icon"></span></button>

			<a href="/projets/tmdb/" class="navbar-brand ms-3">βяαιη vιδéo</a>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-3 mb-lg-0">
					<li class="nav-item"><a href="/projets/tmdb/films/populaires" class="nav-link'.(isset($_GET['populaires']) ? ' text-decoration-underline' : null).'"><i class="fa-solid fa-video"></i> Les Populaires</a></li>
					<li class="nav-item"><a href="/projets/tmdb/exclus" class="nav-link'.(isset($_GET['exclus']) ? ' text-decoration-underline' : null).'"><i class="fa-solid fa-hashtag"></i> Exclusivités</a></li>
					<li class="nav-item"><a href="/projets/tmdb/hash" class="nav-link'.(isset($_GET['hash']) ? ' text-decoration-underline' : null).'"><i class="fa-solid fa-magnet"></i> Magnet</a></li>
					<li class="nav-item"><a href="/projets/tmdb/seedbox" class="nav-link'.(isset($_GET['seedbox']) ? ' text-decoration-underline' : null).'"><i class="fa-regular fa-folder-open"></i> Seedbox</a></li>
					<li class="nav-item"><a href="/projets/tmdb/note-le" class="nav-link'.(isset($_GET['note-le']) ? ' text-decoration-underline' : null).'"><i class="fa-regular fa-star"></i> Note le !</a></li>
				</ul>

				<form action="/projets/tmdb/" method="get" id="formSearch">
					<div class="row">
						<div class="col-11 col-lg-7 mb-2 mb-lg-0 mx-auto">
							<div class="input-group">
								<input type="text" name="titre"'.(!empty($titreRecherche) ? ' value="'.secuChars($titreRecherche).'"' : null).' class="form-control" placeholder="Je cherche…" autocomplete="off">
								<button type="submit" class="btn btn-outline-brain" form="formSearch"><i class="fas fa-search"></i></button>
							</div>
						</div>

						<div class="col-11 col-lg-5 mx-auto text-center">
							<div class="form-check form-control-lg form-check-inline m-0 fs-6">
								<input '.$onchange.' class="form-check-input curseur" name="formFilm" value="film" type="checkbox" id="filmsRecherche" '.((empty($_GET['formFilm']) AND empty($_GET['formSerie']) OR (!empty($_GET['formFilm']))) ? 'checked' : null).'>
								<label '.$onchange.' class="form-check-label curseur" for="filmsRecherche">Films</label>
							</div>
							<div class="form-check form-control-lg form-check-inline m-0 fs-6">
								<input '.$onchange.' class="form-check-input curseur" name="formSerie" value="serie" type="checkbox" id="seriesRecherche" '.((empty($_GET['formFilm']) AND empty($_GET['formSerie']) OR (!empty($_GET['formSerie']))) ? 'checked' : null).'>
								<label '.$onchange.' class="form-check-label curseur" for="seriesRecherche">Séries</label>
							</div>
						</div>
					</div>
				</form>
			</div>
		</nav>
	</div>
</header>

<main>
	<div class="container">';

	if(!empty($type) AND !empty($id) AND !isset($_GET['saison']) AND !isset($_GET['fullCast']) AND !isset($_GET['populaires']) AND !isset($_GET['exclus']) AND !isset($_GET['hash']) AND !isset($_GET['seedbox']) AND !isset($_GET['note-le']))
	{
		if($type === 'film') {
			$titreOriginal		= (string) !empty($t->original_title)		? secuChars($t->original_title)					: $titre;
			$dateInfos			= dateInfos($t->release_date);
			$timestamp			= !empty($dateInfos['timestamp'])			? $dateInfos['timestamp']						: null;
			$date				= !empty($dateInfos['date'])				? $dateInfos['date']							: null;
		}

		elseif($type === 'serie') {
			$titreOriginal		= (string) !empty($t->original_name)		? secuChars($t->original_name)					: $titre;
			$nombreDeSaisons	= !empty($t->number_of_seasons)				? (int) $t->number_of_seasons					: 'aucune saison disponible';
			$dateInfos			= dateInfos($t->first_air_date);
			$timestamp			= !empty($dateInfos['timestamp'])			? $dateInfos['timestamp']						: null;
			$date				= !empty($dateInfos['date'])				? $dateInfos['date']							: null;
		}

		$typeHtml				= ($type === 'film')						? 'du film'										: 'de la série';
		$idIMDb					= !empty($t_external->imdb_id)				? (string) $t_external->imdb_id					: null;
		$backdropPath			= $tmdb->getImageUrl($t->backdrop_path,	TMDB::IMAGE_BACKDROP,	'w1280',	$titre);
		$posterPath				= $tmdb->getImageUrl($t->poster_path,	TMDB::IMAGE_POSTER,		'w185', 	$titre);
		$posterPathLink			= $tmdb->getImageUrl($t->poster_path,	TMDB::IMAGE_POSTER,		'original', $titre);
		$realisateurs			= (string) !empty($t_credits->crew)			? crew($t_credits->crew, 'réalisateur', $type)	: 'réalisateur inconnu';
		$scenaristes			= (string) !empty($t_credits->crew)			? crew($t_credits->crew, 'scénariste', $type)	: 'scénariste inconnu';
		$createurs				= (string) !empty($t->created_by[0]->name)	? crew($t->created_by, 'createur', 'serie')		: 'créateur inconnu';
		$acteurs				= (string) !empty($t_credits->cast)			? cast($t_credits->cast, $type)					: 'acteur inconnu';
		$genres					= (string) !empty($t->genres)				? genres($t->genres)							: 'genre inconnu';
		$paysOrigine			= (string) !empty($t->origin_country)		? countries($pdo, $t->origin_country)			: 'origine inconnue';
		$duree					= (string) !empty($t->runtime)				? minsEnHrs(secu($t->runtime)).'m'				: 'durée inconnue';
		$status					= !empty($t->status)						? (string) statusFr($t->status, $type)			: null;
		$tagline				= !empty($t->tagline)						? secuChars($t->tagline)						: null;
		$siteOfficiel			= !empty($t->homepage)						? secuChars($t->homepage)						: null;
		$synopsis				= (string) !empty($t->overview)				? nl2br(secuChars($t->overview), false)			: 'Le synopsis '.$typeHtml.' <strong>'.$titre.'</strong> est inconnu';
		$bandeAnnonce			= bandeAnnonce($t_videos, $type);

		$cacheFiche = $_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/fiche_'.$type.'_'.$id.'.cache';
		if(!file_exists($cacheFiche) OR (filemtime($cacheFiche) < strtotime($cacheT)))
		{
			try {
				$imdb = new IMDb($t_external->imdb_id);
				$note = !empty($imdb->note()) ? (string) $imdb->note() : null;
			} catch (Exception $e) {
				$note = null;
			}

			$donneesFiche = '<div>
				<div style="background: no-repeat center/100% url(\''.$backdropPath.'\');" class="backdrop mb-4" id="backdrop"><a href="?type='.$type.'&id='.$id.'"><div class="bg-backdrop">'.$titre.'</div></a></div>
				<div class="container mb-5" id="informations">
					<div class="row mb-4">
						<div class="		col-lg-2 d-none d-lg-block p-0"><a href="'.$posterPathLink.'" data-fancybox="gallerie"><img src="'.$posterPath.'" class="img-fluid img-fiche" alt="Poster de '.$titre.'" title="Poster de '.$titre.'"></a></div>
						<div class="col-10	col-lg-8 ps-0 ps-lg-4">
							<ul class="list-group liste-informations">
								'.($titreOriginal !== $titre ? '<li title="'.($type === 'film' ? 'Titre original du film' : 'Nom originale de la série').'"><div>'.($type === 'film' ? 'Titre' : 'Nom').' original</div><div>'.$titreOriginal.'</div></li>' : null).'
								<li title="Date de sortie '.$typeHtml.'"												><div>Date de sortie</div><div>'.(!empty($date) ? '<time datetime="'.date(DATE_ATOM, $timestamp).'">'.dateFormat($date).'</time>, <span style="font-size: .75rem;" title="Quand ?">'.temps($timestamp).'</span>' : 'inconnue').'</div></li>
								'.($type === 'film' ? '<li title="Réalisateur'.s($t_credits->crew).' '.$typeHtml.'"		><div>Réalisation</div><div>'.$realisateurs.'</div></li>' : null).'
								'.($type === 'film' ? '<li title="Scénariste'.s($t_credits->crew).' '.$typeHtml.'"		><div>Scénario</div><div>'.$scenaristes.'</div></li>' : null).'
								'.($type === 'serie' ? '<li title="Créateur '.$typeHtml.'"								><div>Création</div><div>'.$createurs.'</div></li>' : null).'
								<li title="Acteur'.s($t_credits->cast).' '.$typeHtml.'"									><div><a href="?type='.$type.'&id='.$id.'&fullCast" class="text-decoration-underline" title="Afficher le casting complet">Acteur'.s($t_credits->cast).'</a></div><div>'.$acteurs.'</div></li>
								<li title="Genre'.s($t->genres).' '.$typeHtml.'"										><div><a href="/projets/tmdb/genres" class="text-decoration-underline" title="Afficher la liste de tous les Genres">Genre'.s($t->genres).'</a></div><div>'.$genres.'</div></li>
								<li title="Pays d’origine '.$typeHtml.'"												><div><a href="/projets/tmdb/'.$type.'s/origines" class="text-decoration-underline" title="Afficher la liste de tous les Pays">Origine'.s($t->origin_country).'</a></div><div>'.$paysOrigine.'</div></li>
								'.($type === 'film' ? '<li title="Durée '.$typeHtml.'"									><div>Durée</div><div>'.$duree.'</div></li>' : null).'
								'.($type === 'serie' ? '<li title="Saisons et épisodes"									><div><a href="?type=serie&id='.$id.'&saison" class="text-decoration-underline" title="Distribution des rôles et équipe technique">Détails saison'.s($nombreDeSaisons).'</a></div><div>'.$nombreDeSaisons.' saison'.s($nombreDeSaisons).' et '.number_format($t->number_of_episodes).' épisode'.s($t->number_of_episodes).'</div></li>' : null).'
								'.(!empty($note) ? '<li title="Note IMDb"												><div>Note</div><div><span class="badge bg-warning text-dark me-3">'.$note.'</span><span class="badge bg-warning text-dark">'.$imdb->nbVotes().'</span></div></li>' : null).'
								'.((!empty($status) OR !empty($siteOfficiel)) ? '<li title="Informations '.$typeHtml.'" class="mb-2">
									<div>Informations</div>
									<div>
										'.(!empty($status)			? '<span						class="me-2 badge bg-primary"			title="Statut '.$typeHtml.'"					>'.$status.'</span>'									: null).'
										'.(!empty($siteOfficiel)	? '<a href="'.$siteOfficiel.'"	class="badge bg-primary link-light"		title="Site officiel"			'.$onclick.'	><i class="fa-solid fa-link"></i> Site officiel</a>'	: null).'
									</div>
								</li>' : null).'
								<li title="Informations complémentaires '.$typeHtml.'">
									<div>Fiches</div>
									<div>';
										$donneesFiche .= (!empty($idIMDb)	? '<a href="https://www.imdb.com/title/'.$idIMDb.'/"												class="me-2 me-lg-3 svg-lien"	title="Fiche IMDb" '.$onclick.'>'.logoIMDb(21).'</a>'						: null);
										$donneesFiche .= (!empty($type)		? '<a href="https://www.themoviedb.org/'.($type == 'serie' ? 'tv' : 'movie').'/'.$id.'"				class="me-2 me-lg-3 svg-lien"	title="Fiche TMDB" '.$onclick.'>'.logoTMDBMini(20, 75).'</a>'				: null);
										$donneesFiche .= (!empty($type)		? '<a href="https://translate.google.com/?sl=fr&tl=en&text='.urlencode($synopsis).'&op=translate"	class="me-2 me-lg-3 svg-lien"	title="Traduire sur Google Traduction" '.$onclick.'>'.logoGoogle(21).'</a>'	: null);
										$donneesFiche .= (!empty($type)		? '<a href="https://s19.easy-tk.biz/rutorrent/"														class="me-2 me-lg-3 svg-lien"	title="Ma Seedbox" '.$onclick.'>'.logoBitTorrent(21).'</a>'					: null);
										$donneesFiche .= (!empty($type)		? '<a href="http://192.168.0.48:7878/add/new"														class="svg-lien"				title="Ajouter un film sur Radarr" '.$onclick.'>'.logoRadarr(21).'</a>'		: null);
									$donneesFiche .= '</div>
								</li>
								'.(!empty($bandeAnnonce) ? '<li class="mb-0" title="Bande-annonce de '.$titre.'"><div>Bande-annonce</div><div>'.$bandeAnnonce.'</div></li>' : null).'
							</ul>
						</div>
						<div class="col-2 text-end">
							<a href="?type='.$type.'&id='.$id.(isset($_GET['ygg']) ? '&ygg' : null).'&maj" style="color: rgba(214,104,83, 1);" class="align-top btn p-0 me-0 me-lg-2 mb-2 mb-lg-0" data-bs-toggle="tooltip" data-bs-title="Mettre à jour la fiche de '.$titre.'"><i class="fa-solid fa-rotate fs-2"></i></a>
							<button style="color: rgba(214,104,83, 1);" class="btn btn-copie p-0" data-type="fiche-media" data-bs-toggle="tooltip" data-bs-title="Copier le titre : '.$titre.'" data-clipboard-text="'.$titre.'"><i class="fa-regular fa-clipboard fs-2"></i></button>
						</div>
					</div>

					<div class="row mb-4" id="synopsis">
						<p class="fs-4 fw-bold mb-2 liner">Synopsis</p>
						'.(!empty($tagline) ? '<p class="text-center d-none">“ <span class="fst-italic">'.$tagline.'</span> ”</p>' : null).'
						<div class="synopsis ps-3 border-c">'.$synopsis.'</div>
					</div>

					<div class="d-flex justify-content-center flex-wrap gap-2" id="motsCles">';

							$motsCles = ($type === 'film') ? $t_keywords->keywords : $t_keywords->results;

							usort($motsCles, function($a, $b) {
								return strcasecmp($a->name, $b->name);
							});

							foreach($motsCles as $c => $v)
							{
								$idMotCle = secu($v->id);
								$motCle = secuChars($v->name);

								$donneesFiche .= '<a href="/projets/tmdb/'.$type.'s/motcle/'.$idMotCle.'-'.slug($motCle).'" title="Mot clé : '.$motCle.'">'.$motCle.'</a>';
							}

					$donneesFiche .= '</div>
				</div>
			</div>';

			if(!empty($donneesFiche))
			{
				echo $donneesFiche;

				cache($cacheFiche, $donneesFiche);
			}
		}

		else
			echo (file_exists($cacheFiche) AND filesize($cacheFiche) > 0) ? file_get_contents($cacheFiche) : null;

		// YGG

		$qualitesArray = [
			'Q2160px265'	=> '&option_qualite[]=2&option_qualite[]=9&option_qualite[]=13&option_qualite[]=18&option_qualite[]=22',
			'Q2160p'		=> '&option_qualite[]=2&option_qualite[]=9&option_qualite[]=13&option_qualite[]=18&option_qualite[]=22',
			'Q1080px265'	=> '&option_qualite[]=8&option_qualite[]=12&option_qualite[]=17&option_qualite[]=21',
			'Q1080p'		=> '&option_qualite[]=8&option_qualite[]=12&option_qualite[]=17&option_qualite[]=21',
			'Q720p'			=> '&option_qualite[]=10&option_qualite[]=14&option_qualite[]=19&option_qualite[]=23',
			'QBDRip'		=> '&option_qualite[]=1',
			'QDVDRip'		=> '&option_qualite[]=7',
			'QWEBRip'		=> '&option_qualite[]=16&option_qualite[]=20',
			'QHDLight'		=> '',
		];

		$languesArray = [
			'VFF'		=> '&option_langue:multiple[]=2',
			'CA'		=> '&option_langue:multiple[]=5',
			'MULTi'		=> '&option_langue:multiple[]=4',
			'VOSTFR'	=> '&option_langue:multiple[]=8',
			'VO'		=> '&option_langue:multiple[]=1',
		];

		$sort				= in_array($_GET['sort'] ?? '', ['name', 'publish_date', 'size', 'seed', 'leech', 'completed']) ? secuChars($_GET['sort']) : 'seed';
		$sortby				= (!empty($_GET['sortby']) AND $_GET['sortby'] == 'desc') ? 'asc' : 'desc';

		$filtresSaisons		= (!empty($_GET['filtresSaisons']) AND $_GET['filtresSaisons'] >= 1 AND $_GET['filtresSaisons'] <= $nombreDeSaisons)	? '&filtresSaisons='.secu($_GET['filtresSaisons'])			: null;
		$filtresQualites	= (!empty($_GET['filtresQualites']) AND array_key_exists($_GET['filtresQualites'], $qualitesArray))						? '&filtresQualites='.secuChars($_GET['filtresQualites'])	: null;
		$filtresLangues		= (!empty($_GET['filtresLangues']) AND array_key_exists($_GET['filtresLangues'], $languesArray))						? '&filtresLangues='.secuChars($_GET['filtresLangues'])		: null;
		$filtresDates		= (!empty($_GET['filtresDates']) AND in_array($_GET['filtresDates'], ['today', 'week', 'month', 'year']))				? '&filtresDates='.secuChars($_GET['filtresDates'])			: null;
		$filtresTailles		= (!empty($_GET['filtresTailles']) AND in_array($_GET['filtresTailles'], [1, 3, 5, 10, 25, 50, 100]))					? '&filtresTailles='.secu($_GET['filtresTailles'])			: null;

		$tu					= isset($_GET['titre'])				? '&titre'				: null;
		$yf					= isset($_GET['anneTmdb'])			? '&anneTmdb'			: null;
		$si					= isset($_GET['serieIntegrale'])	? '&serieIntegrale'		: null;
		$tt					= isset($_GET['topTeams'])			? '&topTeams'			: null;

		if($type === 'serie' AND preg_match('/Animation/is', $genres))	$sub_category = 2179; // Film/Vidéo > Animation Série
		elseif(preg_match('/Animation/is', $genres))					$sub_category = 2178; // Film/Vidéo > Animation
		elseif(preg_match('/Documentaire/is', $genres))					$sub_category = 2181; // Film/Vidéo > Documentaire
		elseif($type === 'serie')										$sub_category = 2184; // Film/Vidéo > Série
		else															$sub_category = 2183; // Film/Vidéo > Film

		$titre				= isset($_GET['titre']) ? $titre : $titreOriginal;
		$titre				= strtr($titre, ['American Dad!' => 'American Dad', '!' => '', '/' => '', 'œ' => 'oe', "'" => '', '&#039;' => ' ']);

		$yggHttps			= 'https://www.yggtorrent.top';
		$yggTrackerUrl		= $yggHttps.'/engine/search/?'.http_build_query([
			'name'			=> $titre.((isset($_GET['anneTmdb']) AND !empty($date)) ? ' '.explode('-', $date)[0] : null),
			'sort'			=> $sort,
			'order'			=> $sortby,
			'do'			=> 'search',
			'category'		=> 2145,
			'sub_category'	=> $sub_category
		]).
		((!empty($_GET['filtresQualites']) AND array_key_exists($_GET['filtresQualites'], $qualitesArray)) ? $qualitesArray[$_GET['filtresQualites']] : null).
		((!empty($_GET['filtresLangues']) AND array_key_exists($_GET['filtresLangues'], $languesArray)) ? $languesArray[$_GET['filtresLangues']] : null);

		$abnTrackerUrl = 'https://abn.lol/Torrent?'.http_build_query([
			'SelectedCats' => ($type === 'film' ? '2' : '1'),
			'SortOn' => 'Created',
			'SortOrder' => 'desc',
			'Search' => $titre.((isset($_GET['anneTmdb']) AND !empty($date)) ? ' '.explode('-', $date)[0] : null)
		]);

		$filtresUrl = $sort.$filtresSaisons.$filtresQualites.$filtresLangues.$filtresDates.$filtresTailles.$tu.$yf.$si.$tt;

		echo '<div id="yggtorrent">
			<div class="d-flex justify-content-lg-start justify-content-center flex-wrap gap-2 gap-lg-3 mb-4" id="yggLiens">
				<div class="d-inline-block dropdown dropdown-hover-all">
					<div class="btn-group">
						<button type="button" style="min-width: 140px; padding-right: 1rem;" class="btn btn-light btn-sm dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">Filtres avancés</button>
						<div class="dropdown-menu dropdown-menu-start">';

							if($type === 'serie')
							{
								echo '<div class="dropdown dropend">
									<a href="#" class="dropdown-item '.(!empty($_GET['filtresSaisons']) ? 'active' : null).' dropdown-toggle" id="dropdown-layoutsSaisons" data-bs-toggle="dropdown" aria-expanded="false">Par saisons</a>
									<div class="dropdown-menu" aria-labelledby="dropdown-layoutsSaisons">';

										for($iS = 1; $iS <= $nombreDeSaisons; $iS++)
											echo '<a href="?type='.$type.'&id='.$id.'&ygg&filtresSaisons='.$iS.'&sort='.$sort.$filtresLangues.$filtresQualites.$filtresDates.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresSaisons']) AND $_GET['filtresSaisons'] == $iS) ? 'active' : null).' " >Saison '.$iS.'</a>';

									echo '</div>
								</div>';
							}

							echo '<div class="dropdown dropend">
								<a href="#" class="dropdown-item '.(!empty($_GET['filtresQualites']) ? 'active' : null).' dropdown-toggle" id="dropdown-layoutsQualites" data-bs-toggle="dropdown" aria-expanded="false">Par qualités</a>
								<div class="dropdown-menu" aria-labelledby="dropdown-layoutsQualites">';

									foreach($qualitesArray as $k => $v)
										echo '<a href="?type='.$type.'&id='.$id.'&ygg&filtresQualites='.$k.'&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresDates.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresQualites']) AND $_GET['filtresQualites'] == $k) ? 'active' : null).'">'.ltrim(str_replace('1080px265', '1080p x265', str_replace('2160px265', '2160p x265', $k)), $k[0]).'</a>';

									echo '<a href="?type='.$type.'&id='.$id.'&ygg&sort='.$sort.$titre.$yf.$tt.$filtresLangues.$filtresDates.$filtresTailles.'#ygg" class="dropdown-item">Toutes les qualités</a>
								</div>
							</div>

							<div class="dropdown dropend">
								<a href="#" class="dropdown-item '.((!empty($_GET['filtresLangues']) AND ($_GET['filtresLangues'] == 'VFF' OR $_GET['filtresLangues'] == 'CA' OR $_GET['filtresLangues'] == 'MULTi' OR $_GET['filtresLangues'] == 'VOSTFR' OR $_GET['filtresLangues'] == 'VO')) ? 'active' : null).' dropdown-toggle" id="dropdown-layoutsLangues" data-bs-toggle="dropdown" aria-expanded="false">Par langues</a>
									<div class="dropdown-menu" aria-labelledby="dropdown-layoutsLangues">
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresLangues=VFF&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresDates.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresLangues']) AND $_GET['filtresLangues'] === 'VFF')		? 'active' : null).'">VFF 🇫🇷</a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresLangues=CA&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresDates.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresLangues']) AND $_GET['filtresLangues'] === 'CA')			? 'active' : null).'">CA 🇨🇦</a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresLangues=MULTi&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresDates.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresLangues']) AND $_GET['filtresLangues'] === 'MULTi')	? 'active' : null).'">MULTi <img src="https://i.ibb.co/41kg36P/MULTi.jpg" style="height: 9px; width: 13px;" alt="MULTi FR / EN" title="MULTi FR / EN"></a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresLangues=VOSTFR&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresDates.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresLangues']) AND $_GET['filtresLangues'] === 'VOSTFR')	? 'active' : null).'">VOSTFR <img src="https://i.ibb.co/tQw2tgb/VOSTFR.jpg" style="height: 9px; width: 13px;" alt="VOSTFR" title="VOSTFR"></a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresLangues=VO&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresDates.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresLangues']) AND $_GET['filtresLangues'] === 'VO')			? 'active' : null).'">VO 🇺🇸</a>
								</div>
							</div>

							<div class="dropdown dropend">
								<a href="#" class="dropdown-item '.((!empty($_GET['filtresDates']) AND ($_GET['filtresDates'] == 'today' OR $_GET['filtresDates'] == 'week' OR $_GET['filtresDates'] == 'month' OR $_GET['filtresDates'] == 'year')) ? 'active' : null).' dropdown-toggle" id="dropdown-layoutsDates" data-bs-toggle="dropdown" aria-expanded="false">Par dates</a>
								<div class="dropdown-menu" aria-labelledby="dropdown-layoutsDates">
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresDates=today&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresDates']) AND $_GET['filtresDates'] === 'today')	? 'active' : null).'"><i class="fa-solid fa-calendar-day"></i> Aujourd’hui</a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresDates=week&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresDates']) AND $_GET['filtresDates'] === 'week')		? 'active' : null).'"><i class="fa-solid fa-calendar-week"></i> Cette semaine</a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresDates=month&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresDates']) AND $_GET['filtresDates'] === 'month')	? 'active' : null).'"><i class="fa-solid fa-calendar-days"></i> Ce mois</a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresDates=year&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresDates']) AND $_GET['filtresDates'] === 'year')		? 'active' : null).'"><i class="fa-solid fa-calendar"></i> Cette année</a>
								</div>
							</div>

							<div class="dropdown dropend">
								<a href="#" class="dropdown-item '.(((!empty($_GET['filtresTailles']) AND ($_GET['filtresTailles'] === 1 OR $_GET['filtresTailles'] == 3 OR $_GET['filtresTailles'] == 5 OR $_GET['filtresTailles'] === 10 OR $_GET['filtresTailles'] == 25 OR $_GET['filtresTailles'] == 50 OR $_GET['filtresTailles'] === 100))) ? 'active' : null).' dropdown-toggle" id="dropdown-layoutsTailles" data-bs-toggle="dropdown" aria-expanded="false">Par tailles</a>

								<div class="dropdown-menu" aria-labelledby="dropdown-layoutsTailles">
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresTailles=1&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresDates.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresTailles']) AND $_GET['filtresTailles'] === '1')		? 'active' : null).'"><i class="fa-solid fa-hard-drive"></i> &lt; 1 Go</a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresTailles=3&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresDates.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresTailles']) AND $_GET['filtresTailles'] === '3')		? 'active' : null).'"><i class="fa-solid fa-hard-drive"></i> &lt; 3 Go</a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresTailles=5&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresDates.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresTailles']) AND $_GET['filtresTailles'] === '5')		? 'active' : null).'"><i class="fa-solid fa-hard-drive"></i> &lt; 5 Go</a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresTailles=10&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresDates.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresTailles']) AND $_GET['filtresTailles'] === '10')		? 'active' : null).'"><i class="fa-solid fa-hard-drive"></i> &lt; 10 Go</a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresTailles=25&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresDates.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresTailles']) AND $_GET['filtresTailles'] === '25')		? 'active' : null).'"><i class="fa-solid fa-hard-drive"></i> &lt; 25 Go</a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresTailles=50&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresDates.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresTailles']) AND $_GET['filtresTailles'] === '50')		? 'active' : null).'"><i class="fa-solid fa-hard-drive"></i> &lt; 50 Go</a>
									<a href="?type='.$type.'&id='.$id.'&ygg&filtresTailles=100&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresDates.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.((!empty($_GET['filtresTailles']) AND $_GET['filtresTailles'] === '100')	? 'active' : null).'"><i class="fa-solid fa-hard-drive"></i> &lt; 100 Go</a>
								</div>
							</div>

							<a class="dropdown-item disabled" aria-disabled="true"><hr class="my-1"></a>
							<a href="?type='.$type.'&id='.$id.'&ygg'.(!isset($_GET['titre'])									? '&titre' : null).'&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresDates.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.(isset($_GET['titre'])			? 'active' : null).'" title="Afficher les torrents par Titre">Par titre</a>
							<a href="?type='.$type.'&id='.$id.'&ygg'.(!isset($_GET['anneTmdb'])									? '&anneTmdb' : null).'&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresDates.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.(isset($_GET['anneTmdb'])		? 'active' : null).'" title="Afficher les torrents avec l’année de sortie (sur TMDB)">Par année</a>
							'.($type === 'serie' ? '<a href="?type='.$type.'&id='.$id.'&ygg'.(!isset($_GET['serieIntegrale'])	? '&serieIntegrale' : null).'&sort='.$sort.$filtresLangues.$filtresQualites.$filtresDates.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.(isset($_GET['serieIntegrale'])			? 'active' : null).'" title="Afficher les Saisons intégrales uniquement">Saisons intégrales</a>' : null).'
							<a href="?type='.$type.'&id='.$id.'&ygg'.(!isset($_GET['topTeams'])									? '&topTeams' : null).'&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresQualites.$filtresDates.$filtresTailles.$titre.$yf.$si.$tt.'#ygg" class="dropdown-item '.(isset($_GET['topTeams'])		? 'active' : null).'" title="Afficher les torrents des Tops Teams uniquement">Top Teams</a>

							<a class="dropdown-item disabled" aria-disabled="true"><hr class="my-1"></a>

							<a href="'.$abnTrackerUrl.'" class="dropdown-item" '.$onclick.'>ABN</a>
							<a href="'.$yggTrackerUrl.'" class="dropdown-item" '.$onclick.'>YGG</a>

							<a class="dropdown-item disabled" aria-disabled="true"><hr class="my-1"></a>

							<a href="?type='.$type.'&id='.$id.'&ygg#ygg" class="dropdown-item bg-danger text-white">Réinitialiser</a>
						</div>
					</div>
				</div>
				<a href="'.$yggTrackerUrl.'" style="" class="badge badge-ygg py-2" '.$onclick.'>YggTorrent</a>
				<a href="'.$abnTrackerUrl.'" class="badge badge-abn py-2" '.$onclick.'>ABN</a>
				<a href="https://s19.easy-tk.biz/rutorrent/" class="badge badge-seedboxio py-2" '.$onclick.'>Seedbox</a>
			</div>

			<div class="mb-5" id="ygg">
				<a data-bs-toggle="collapse" href="#collapse" role="button" aria-expanded="false" aria-controls="collapse"><h3 class="mb-0 liner">ygg.to</h3></a>';

				echo (!empty($erreur) ? $erreur : null).'

				<div class="collapse'.(isset($_GET['ygg']) ? ' show' : null).'" id="collapse">';

					if(isset($_GET['ygg']))
					{
						if(!empty($_GET['filtresSaisons']) OR !empty($_GET['filtresQualites']) OR !empty($_GET['filtresLangues']) OR !empty($_GET['filtresDates']) OR !empty($_GET['filtresTailles']) OR isset($_GET['titre']) OR isset($_GET['anneTmdb']) OR isset($_GET['serieIntegrale']) OR isset($_GET['topTeams']))
						{
							echo '<div class="row border-bottom">
								<div class="col-12 my-3 text-start">
									<a href="?type='.$type.'&id='.$id.'&ygg#ygg" class="badge text-bg-danger p-2" title="Réinitialiser tous les filtres">Réinitialiser</a>

									'.(!empty($_GET['filtresSaisons']) ? '<a href="?type='.$type.'&id='.$id.'&ygg&sort='.$sort.$filtresQualites.$filtresLangues.$filtresDates.$filtresTailles.$tu.$yf.$si.$tt.'#ygg" title="Supprimer le filtre Par saisons"><span class="badge text-bg-brain p-2">Par saisons <i class="fa-regular fa-circle-xmark"></i></span></a>' : null).'
									'.(!empty($_GET['filtresQualites']) ? '<a href="?type='.$type.'&id='.$id.'&ygg&sort='.$sort.$filtresSaisons.$filtresLangues.$filtresDates.$filtresTailles.$tu.$yf.$si.$tt.'#ygg" title="Supprimer le filtre Par qualités"><span class="badge text-bg-brain p-2">Par qualité <i class="fa-regular fa-circle-xmark"></i></span></a>' : null).'
									'.(!empty($_GET['filtresLangues']) ? '<a href="?type='.$type.'&id='.$id.'&ygg&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresDates.$filtresTailles.$tu.$yf.$si.$tt.'#ygg" title="Supprimer le filtre Par langues"><span class="badge text-bg-brain p-2">Par langue <i class="fa-regular fa-circle-xmark"></i></span></a>' : null).'
									'.(!empty($_GET['filtresDates']) ? '<a href="?type='.$type.'&id='.$id.'&ygg&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresLangues.$filtresTailles.$tu.$yf.$si.$tt.'#ygg" title="Supprimer le filtre Par dates"><span class="badge text-bg-brain p-2">Par date <i class="fa-regular fa-circle-xmark"></i></span></a>' : null).'
									'.(!empty($_GET['filtresTailles']) ? '<a href="?type='.$type.'&id='.$id.'&ygg&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresLangues.$tu.$yf.$si.$tt.'#ygg" title="Supprimer le filtre Par tailles"><span class="badge text-bg-brain p-2">Par taille <i class="fa-regular fa-circle-xmark"></i></span></a>' : null).'

									'.(isset($_GET['titre']) ? '<a href="?type='.$type.'&id='.$id.'&ygg&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresLangues.$filtresDates.$filtresTailles.$yf.$si.$tt.'#ygg" title="Supprimer le filtre Titre"><span class="badge text-bg-brain p-2">Titre <i class="fa-regular fa-circle-xmark"></i></span></a>' : null).'
									'.(isset($_GET['anneTmdb']) ? '<a href="?type='.$type.'&id='.$id.'&ygg&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresLangues.$filtresDates.$filtresTailles.$tu.$si.$tt.'#ygg" title="Supprimer le filtre Année TMDB"><span class="badge text-bg-brain p-2">Année TMDB <i class="fa-regular fa-circle-xmark"></i></span></a>' : null).'
									'.(isset($_GET['serieIntegrale']) ? '<a href="?type='.$type.'&id='.$id.'&ygg&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresLangues.$filtresDates.$filtresTailles.$tu.$yf.$tt.'#ygg" title="Supprimer le filtre Saisons intégrales"><span class="badge text-bg-brain p-2">Saisons intégrales <i class="fa-regular fa-circle-xmark"></i></span></a>' : null).'
									'.(isset($_GET['topTeams']) ? '<a href="?type='.$type.'&id='.$id.'&ygg&sort='.$sort.$filtresSaisons.$filtresQualites.$filtresLangues.$filtresDates.$filtresTailles.$tu.$yf.$si.'#ygg" title="Supprimer le filtre Top Teams"><span class="badge text-bg-brain p-2">Top Teams <i class="fa-regular fa-circle-xmark"></i></span></a>' : null).'
								</div>
							</div>';
						}

						$reponseYgg = getYgg($yggTrackerUrl);

						if(!empty($reponseYgg))
						{
							echo '<div class="row align-items-center align-content-start text-center mt-4 mt-lg-0 pb-3" id="trierYgg">
								<div class="		col-lg-1 d-none d-lg-block">
								</div>
								<div class="col-3	col-lg-6">
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=name&sortby=desc&'.$filtresUrl.'#ygg"			title="Z à A"><i class="fas fa-lg fa-angle-double-up'.(($sort == 'name' AND $sortby == 'asc')									? ' opacity-50' : null).'"></i></a>
									<div>Release</div>
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=name&sortby=asc&'.$filtresUrl.'#ygg"			title="A à Z"><i class="fas fa-lg fa-angle-double-down'.(($sort == 'name' AND $sortby == 'desc')								? ' opacity-50' : null).'"></i></a>
								</div>
								<div class="col-2	col-lg-1">
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=publish_date&sortby=desc&'.$filtresUrl.'#ygg"	title="+ récent au + ancien"><i class="fas fa-lg fa-angle-double-up'.(($sort == 'publish_date' AND $sortby == 'asc')			? ' opacity-50' : null).'"></i></a>
									<div>Date</div>
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=publish_date&sortby=asc&'.$filtresUrl.'#ygg"	title="+ ancien au + récent"><i class="fas fa-lg fa-angle-double-down'.(($sort == 'publish_date' AND $sortby == 'desc')			? ' opacity-50' : null).'"></i></a>
								</div>
								<div class="col-2	col-lg-1">
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=size&sortby=desc&'.$filtresUrl.'#ygg"			title="+ lourd au + léger"><i class="fas fa-lg fa-angle-double-up'.(($sort == 'size' AND $sortby == 'asc')						? ' opacity-50' : null).'"></i></a>
									<div>Taille</div>
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=size&sortby=asc&'.$filtresUrl.'#ygg"			title="+ léger au + lourd"><i class="fas fa-lg fa-angle-double-down'.(($sort == 'size' AND $sortby == 'desc')					? ' opacity-50' : null).'"></i></a>
								</div>
								<div class="col-2	col-lg-1 text-success">
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=seed&sortby=desc&'.$filtresUrl.'#ygg"			title="+ de sources à - de sources"><i class="fas fa-lg fa-angle-double-up'.(($sort == 'seed' AND $sortby == 'asc')				? ' opacity-50' : null).'"></i></a>
									<div>Sources</div>
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=seed&sortby=asc&'.$filtresUrl.'#ygg"			title="- de sources à + de sources"><i class="fas fa-lg fa-angle-double-down'.(($sort == 'seed' AND $sortby == 'desc')			? ' opacity-50' : null).'"></i></a>
								</div>
								<div class="col-3	col-lg-1 text-danger">
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=leech&sortby=desc&'.$filtresUrl.'#ygg"			title="+ de clients à - de clients"><i class="fas fa-lg fa-angle-double-up'.(($sort == 'leech' AND $sortby == 'asc')			? ' opacity-50' : null).'"></i></a>
									<div>Clients</div>
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=leech&sortby=asc&'.$filtresUrl.'#ygg"			title="- de clients à + de clients"><i class="fas fa-lg fa-angle-double-down'.(($sort == 'leech' AND $sortby == 'desc')			? ' opacity-50' : null).'"></i></a>
								</div>
								<div class="		col-lg-1 d-none d-lg-block text-dark-emphasis">
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=completed&sortby=desc&'.$filtresUrl.'#ygg"		title="+ de complétés à - de complétés"><i class="fas fa-lg fa-angle-double-up'.(($sort == 'completed' AND $sortby == 'asc')	? ' opacity-50' : null).'"></i></a>
									<div>Complets</div>
									<a href="?type='.$type.'&id='.$id.'&ygg&sort=completed&sortby=asc&'.$filtresUrl.'#ygg"		title="- de complétés à + de complétés"><i class="fas fa-lg fa-angle-double-down'.(($sort == 'completed' AND $sortby == 'desc')	? ' opacity-50' : null).'"></i></a>
								</div>
							</div>';

							preg_match('/(?P<resultats>[0-9 ]+) résultats trouvés/', $reponseYgg, $resultats);
							$nbTorrents = !empty($resultats['resultats']) ? secu(str_replace(' ', '', $resultats['resultats'])) : null;

							if(empty($nbTorrents))
							{
								if(preg_match('/Votre compte est(.*)<u>désactivé<\/u>/is', $reponseYgg))	goto erreurYggCompteDesactive;
								elseif(preg_match('/Aucun résultat !/is', $reponseYgg))						goto erreurYggAucunResultat;

								unset($_GET['filtresSaisons']);
								unset($_GET['filtresQualites']);
								unset($_GET['filtresLangues']);
								unset($_GET['filtresDates']);
								unset($_GET['filtresTailles']);
							}

							elseif(!empty($nbTorrents))
							{
								preg_match_all('/<td style="text-align: left;"><a id="torrent_name" href="(?P<lien>.*)">(?P<release>.*)<\/td>(.*)<td><a target="(?P<id>.*)" id="get_nfo">(.*)<div class="hidden">(?P<timestamp>.*)<\/div><span class="ico_clock-o">(.*)<\/td>(.*)<td>(?P<taille>.*)<\/td>(.*)<td>(?P<completes>.*)<\/td>(.*)<td>(?P<seeders>.*)<\/td>(.*)<td>(?P<leechers>.*)<\/td>(.*)<\/tr>/isU',
								$reponseYgg,
								$m);

								$yggResultats = [];

								$c = ($nbTorrents > 50) ? 49 : ($nbTorrents - 1);

								if(!empty($m['id']))
								{
									foreach($m['id'] as $cle => $id)
									{
										$yggId			= $id;
										$yggUrl			= !empty($m['lien'][$cle])		? clean($m['lien'][$cle])		: null;
										$yggTimestamp	= !empty($m['timestamp'][$cle])	? clean($m['timestamp'][$cle])	: '0';
										$yggTaille		= !empty($m['taille'][$cle])	? clean($m['taille'][$cle])		: '0';
										$yggTaille		= strtr($yggTaille, ['Ko' => ' Ko', 'Mo' => ' Mo', 'Go' => ' Go', 'To' => ' To']);
										$yggSources		= !empty($m['seeders'][$cle])	? $m['seeders'][$cle]			: '0';
										$yggClients		= !empty($m['leechers'][$cle])	? $m['leechers'][$cle]			: '0';
										$yggComplete	= !empty($m['completes'][$cle])	? $m['completes'][$cle]			: '0';
										$yggRls			= !empty($m['release'][$cle])	? parserRelease($m['release'][$cle], $yggTimestamp, $paysOrigine) : null;

										$yggResultats[] = [
											'<div class="row gx-0 g-lg-0 py-3 border-bottom text-center">
												<div class="order-2 order-lg-1	col-3	col-lg-1">
													<a href="'.$yggHttps.'/engine/download_torrent?id='.$yggId.'" class="me-2" title="Telecharger le torrent"><i class="fas fa-download fa-xl"></i></a>
													<a href="/projets/tmdb/hash?tracker=ygg&hashPost='.$yggUrl.'" title="Afficher le magnet"><i class="fa-solid fa-magnet fa-xl"></i></a>
												</div>
												<div class="order-1 order-lg-2	col-12	col-lg-6 mb-3 mb-lg-0 text-start"									title="Nom de la release"><a href="'.$yggUrl.'" '.$onclick.'>'.$yggRls.'</a></div>
												<div class="order-3				col-3	col-lg-1"															title="Date d’ajout">'.strtr(temps($yggTimestamp), ['il y a' => '', 'semaines' => 'sems.']).'</div>
												<div class="order-4				col-2	col-lg-1"															title="Taille du torrent">'.$yggTaille.'</div>
												<div class="order-5				col-2	col-lg-1 text-success"												title="Sources du torrent">'.$yggSources.'</div>
												<div class="					col-2	col-lg-1 text-danger"							style="order: 6;"	title="Clients du torrent">'.$yggClients.'</div>
												<div class="							col-lg-1 d-none d-lg-block text-dark-emphasis"	style="order: 7;"	title="Téléchargement complétés">'.$yggComplete.'</div>
											</div>',
											$yggRls,
											$yggTimestamp,
											$yggTaille,
										];
									}

									if(empty($yggResultats))
										goto erreurYgg;
								}

								else
									goto erreurYgg;

								foreach($yggResultats as $cle => $valeur)
								{
									$yggResultClean = strip_tags($yggResultats[$cle][1]);

									// Par saisons

									if(!empty($yggResultats[$cle]) AND !empty($_GET['filtresSaisons']) AND $_GET['filtresSaisons'] >= 1 AND $_GET['filtresSaisons'] <= 15) {
										$filtresSaisons = secu($_GET['filtresSaisons']);

										if($filtresSaisons >= 1 AND $filtresSaisons <= 9)	$filtresSaisonsUrl = 'S0'.$filtresSaisons;
										elseif($filtresSaisons >= 10)						$filtresSaisonsUrl = 'S'.$filtresSaisons;
										else												$filtresSaisonsUrl = 'S01';

										if(!empty($_GET['filtresSaisons']) AND !preg_match('/ '.$filtresSaisonsUrl.' /is', $yggResultClean))
											unset($yggResultats[$cle]);
									}

									// Par qualités

									if(!empty($yggResultats[$cle]) AND !empty($_GET['filtresQualites']) AND array_key_exists($_GET['filtresQualites'], $qualitesArray)) {
										if($_GET['filtresQualites'] == 'Q2160px265') {
											if(!preg_match('/265/is', $yggResultClean))
												unset($yggResultats[$cle]);
										}
									}

									if(!empty($yggResultats[$cle]) AND !empty($_GET['filtresQualites']) AND array_key_exists($_GET['filtresQualites'], $qualitesArray)) {
										if($_GET['filtresQualites'] == 'Q1080px265') {
											if(!preg_match('/265/is', $yggResultClean))
												unset($yggResultats[$cle]);
										}
									}

									if(!empty($yggResultats[$cle]) AND !empty($_GET['filtresQualites']) AND array_key_exists($_GET['filtresQualites'], $qualitesArray)) {
										if($_GET['filtresQualites'] == 'QHDLight') {
											if(!preg_match('/light/is', $yggResultClean))
												unset($yggResultats[$cle]);
										}
									}

									// Par dates

									if(!empty($yggResultats[$cle]) AND !empty($_GET['filtresDates']) AND in_array($_GET['filtresDates'], ['today', 'week', 'month', 'year'])) {
										if($_GET['filtresDates'] == 'today' AND $yggResultats[$cle][2] < (time() - (60 * 60 * 48)))				unset($yggResultats[$cle]);
										elseif($_GET['filtresDates'] == 'week' AND $yggResultats[$cle][2] < (time() - (60 * 60 * 24 * 7)))		unset($yggResultats[$cle]);
										elseif($_GET['filtresDates'] == 'month' AND $yggResultats[$cle][2] < (time() - (60 * 60 * 24 * 31)))	unset($yggResultats[$cle]);
										elseif($_GET['filtresDates'] == 'year' AND $yggResultats[$cle][2] <div (time() - (60 * 60 * 24 * 365)))	unset($yggResultats[$cle]);
									}

									// Par tailles

									if((!empty($yggResultats[$cle]) AND !empty($_GET['filtresTailles']) AND in_array($_GET['filtresTailles'], [1, 3, 5, 5, 10, 25, 50, 100]))) {
										$t = $yggResultats[$cle][3];
										$tailles = trim(strtr($yggResultats[$cle][3], [' Ko' => '', ' Mo' => '', ' Go' => '', ' To' => '']));

										$tailleKo = preg_match('/ Ko/is', $t) ? ($tailles * 1024) : 0;
										$tailleMo = preg_match('/ Mo/is', $t) ? ($tailles * 1024 * 1024) : 0;
										$tailleGo = preg_match('/ Go/is', $t) ? ($tailles * 1024 * 1024 * 1024) : 0;
										$tailleTo = preg_match('/ To/is', $t) ? ($tailles * 1024 * 1024 * 1024 * 1024) : 0;

										if($tailleKo > 0)		$taille = round($tailleKo);
										elseif($tailleMo > 0)	$taille = round($tailleMo);
										elseif($tailleGo > 0)	$taille = round($tailleGo);
										elseif($tailleTo > 0)	$taille = round($tailleTo);
										else					$taille = null;

										// https://www.generationcyb.net/convertisseur-octet-ko-mo-go-to/

										if($_GET['filtresTailles'] === 1 AND $taille > 1073741824)			unset($yggResultats[$cle]);
										elseif($_GET['filtresTailles'] === 3 AND $taille > 3221225472)		unset($yggResultats[$cle]);
										elseif($_GET['filtresTailles'] === 5 AND $taille > 5368709120)		unset($yggResultats[$cle]);
										elseif($_GET['filtresTailles'] === 10 AND $taille > 10737418240)		unset($yggResultats[$cle]);
										elseif($_GET['filtresTailles'] === 25 AND $taille > 26843545600)		unset($yggResultats[$cle]);
										elseif($_GET['filtresTailles'] === 50 AND $taille > 53687091200)		unset($yggResultats[$cle]);
										elseif($_GET['filtresTailles'] === 100 AND $taille > 107374182400)	unset($yggResultats[$cle]);
									}

									// Saisons intégrales

									if(!empty($yggResultats[$cle]) AND $_GET['type'] === 'serie' AND isset($_GET['serieIntegrale'])) {
										if(!preg_match('/complete?|int(e|é)gral|S01 | S02 | S03 | S04 | S05 | S06 | S07 | S08 | S09 | S10 | S11 | S12 | S13 | S14 | S15 | S16 | S17 | S18 | S19 | S20 /is', $yggResultClean))
											unset($yggResultats[$cle]);
									}

									// Top Teams

									if(!empty($yggResultats[$cle]) AND isset($_GET['topTeams'])) {
										$topTeams = 'ALLDAYiN|AMB3R|AvALoN|BONBON|BTT|BULiTT|COLL3CTiF|DELiCiOUS|DEMS|Elcrackito|FCK|FERVEX|FLOP|FRATERNiTY|FTMVHD|FW|GHT|gismo65|HiggsBoson|JiHEFF|KFL|LiBERTAD|LiHDL|mHDgz|MULTiViSiON|ONLYMOViE|OZEF|PiCKLES|PopHD|QTZ|R3MiX|ROMKENT|SAKADOX|SceneGuardians|SERQPH|Slay3R|TFA|XSHD|Winks|ZEKEY|ZiT';
										if(!preg_match('/'.$topTeams.'/is', $yggResultClean))
											unset($yggResultats[$cle]);
									}
								}

								array_filter($yggResultats);

								$nbResultats = count($yggResultats);

								if($nbResultats == 0)
									goto erreurYggAucunResultat;

								elseif($nbResultats > 0) {
									foreach($yggResultats as $r)
										echo $r[0];
								}

								else
									goto erreurYgg;
							}

							elseif(empty($nbTorrents))
							{
								erreurYggCompteDesactive:

								echo alerte('danger', 'Le compte <span class="fw-bold">Poulok</span> est désactivé');
							}

							elseif(empty($resultats['resultats'][0]))
							{
								erreurYggAucunResultat:

								echo alerte('danger', 'La recherche '.$typeHtml.' <a href="'.$yggTrackerUrl.'" '.$onclick.' class="fw-bold">'.$titre.'</a> ne contient aucun résultat');
							}

							elseif(empty($resultats))
							{
								erreurYgg:

								echo alerte('danger', 'La recherche '.$typeHtml.' <a href="'.$yggTrackerUrl.'" '.$onclick.' class="fw-bold">'.$titre.'</a> a rencontrée une erreur');
							}
						}

						else
							echo alerte('danger', 'Erreur de chargement du site distant', 'mt-5');
					}

					else
						echo '<p class="text-center mb-0 mt-4"><a href="/projets/tmdb/?type='.$type.'&id='.$id.'&ygg#ygg" class="btn btn-outline-brain">Afficher les torrents</a></p>';

					echo !empty($erreur) ? $erreur : null.'
				</div>
			</div>
		</div>';

		// VidSrc

		// if($type === 'film')
		// {
		// 	echo '<div class="mb-5" id="streaming">
		// 		<a data-bs-toggle="collapse" href="#collapseStreaming" role="button" aria-expanded="false" aria-controls="collapseStreaming"><h3 class="mb-4 liner">Streaming</h3></a>

		// 		<div class="collapse" id="collapseStreaming">
		// 			<div style="height: 450px;">
		// 				<iframe src="https://vidsrc.me/embed/movie?imdb='.$idIMDb.'" style="width: 100%; height: 100%;" frameborder="0" referrerpolicy="origin" allowfullscreen></iframe>
		// 			</div>
		// 		</div>
		// 	</div>';
		// }

		// Images

		$cacheImages = $_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/images_'.$type.'_'.$id.'.cache';
		if(!file_exists($cacheImages) OR (filemtime($cacheImages) < strtotime($cacheT)))
		{
			$imgs = images($t_imgs->backdrops);
			$cB = !empty($imgs) ? count($imgs) : 0;
			if($cB > 0)
			{
				$donneesImages[] = '<div class="mb-5" id="images">
					<a data-bs-toggle="collapse" href="#collapseImages" role="button" aria-expanded="false" aria-controls="collapseImages"><h3 class="mb-4 liner">Images '.$typeHtml.'</h3></a>

					<div class="collapse" id="collapseImages">
						<div class="images-liste">
							<a href="'.$posterPathLink.'" data-fancybox="gallerie"><img src="https://dummyimage.com/256x144/cccccc/555555.png?text='.urlencode('Poster '.$typeHtml).'" class="img-fluid rounded" alt="Poster de '.$titre.'" title="Poster de '.$titre.'" loading="lazy"></a>';

							$i = 1;
							foreach($imgs as $k => $url_img)
							{
								$alt = 'Image n°'.$i.' de '.$titre;

								$donneesImages[] = '<a href="'.$url_img.'" data-fancybox="gallerie"><img src="'.str_replace('original', 'w185', $url_img).'" class="img-fluid rounded" alt="'.$alt.'" title="'.$alt.'" loading="lazy"></a>';

								$i++;
							}

						$donneesImages[] = '</div>
					</div>
				</div>';

				if(!empty($donneesImages))
				{
					echo implode($donneesImages);

					cache($cacheImages, implode($donneesImages));
				}
			}
		}

		else
			echo (file_exists($cacheImages) AND filesize($cacheImages) > 0) ? file_get_contents($cacheImages) : null;

		// Recommandations

		if(!empty($t_recommandations->total_results) AND $t_recommandations->total_results > 0 AND !empty($t_recommandations->results))
		{
			$cacheRecos = $_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/recommandations_'.$type.'_'.$id.'.cache';
			if(!file_exists($cacheRecos) OR (filemtime($cacheRecos) < strtotime($cacheT)))
			{
				$donneesRecos[] = '<div class="mb-5" id="recommandations">
					<a data-bs-toggle="collapse" href="#collapseRecs" role="button" aria-expanded="false" aria-controls="collapseRecs"><h3 class="mb-4 liner">'.($type === 'film' ? 'Films Recommandés' : 'Séries Recommandées').'</h3></a>

					<div class="collapse" id="collapseRecs">
						<div class="carousel slide" id="myCarouselRec" data-bs-ride="carousel">

							<div class="carousel-indicators">';

							$recs = recommandations($t_recommandations->results);
							$nbRecomm = count($recs);

							for($i = 0; $i < $nbRecomm; $i++)
								$donneesRecos[] = '<button type="button" data-bs-target="#myCarouselRec" data-bs-slide-to="'.$i.'"'.($i === 0 ? ' class="active" aria-current="true"' : null).' aria-label="Slide '.($i + 1).'"></button>';

							$donneesRecos[] = '</div>

							<div class="carousel-inner">';

							for($i = 0; $i < $nbRecomm; $i++)
							{
								$recId = explode('|', $recs[$i])[0];
								$recPoster_w300 = str_replace('original', 'w300', explode('|', $recs[$i])[1]);
								$recPoster_w780 = str_replace('original', 'w780', explode('|', $recs[$i])[1]);
								$recPoster_w1280 = str_replace('original', 'w1280', explode('|', $recs[$i])[1]);
								$recNom = explode('|', $recs[$i])[2];

								$donneesRecos[] = '<div class="carousel-item'.($i < 1 ? ' active' : null).'">
									<picture>
										<source srcset="'.$recPoster_w300.'" media="(max-width: 500px)">
										<source srcset="'.$recPoster_w780.'" media="(max-width: 780px)">
										<source srcset="'.$recPoster_w1280.'" media="(max-width: 1280px)">
										<source srcset="'.explode('|', $recs[$i])[1].'">
										<img src="'.explode('|', $recs[$i])[1].'" class="d-block w-100 rounded" alt="Poster de '.$recNom.'" loading="lazy">
									</picture>

									<div class="carousel-caption"><a href="?type='.$type.'&id='.$recId.'"><div class="bg-backdrop-recommandes-similaires">'.$recNom.'</div></a></div>
								</div>';
							}

							$donneesRecos[] = '</div>

							<button class="carousel-control-prev btn-success" type="button" data-bs-target="#myCarouselRec" data-bs-slide="prev" title="Slide précédent">
								<span class="carousel-control-prev-icon" aria-hidden="true"></span>
								<span class="visually-hidden">Précédent</span>
							</button>
							<button class="carousel-control-next btn-success" type="button" data-bs-target="#myCarouselRec" data-bs-slide="next" title="Slide suivant">
								<span class="visually-hidden">Suivant</span>
								<span class="carousel-control-next-icon" aria-hidden="true"></span>
							</button>
						</div>
					</div>
				</div>';

				if(!empty($donneesRecos))
				{
					echo implode($donneesRecos);

					cache($cacheRecos, implode($donneesRecos));
				}
			}

			else
				echo (file_exists($cacheRecos) AND filesize($cacheRecos) > 0) ? file_get_contents($cacheRecos) : null;
		}

		// Similaires

		if(!empty($t_similaires->total_results) AND $t_similaires->total_results > 0 AND !empty($t_similaires->results))
		{
			$cacheSimilaires = $_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/similaires_'.$type.'_'.$id.'.cache';
			if(!file_exists($cacheSimilaires) OR (filemtime($cacheSimilaires) < strtotime($cacheT)))
			{
				$donneesSimilaires[] = '<div id="similaires">
					<a data-bs-toggle="collapse" href="#collapseSims" role="button" aria-expanded="false" aria-controls="collapseSims"><h3 class="mb-4 liner">'.($type === 'film' ? 'Films' : 'Séries').' Similaires</h3></a>

					<div class="collapse" id="collapseSims">
						<div class="carousel slide" id="myCarousel" data-bs-ride="carousel">

							<div class="carousel-indicators">';

							$sims = similaires($t_similaires->results);
							$cS = count($sims);

							for($i = 0; $i < $cS; $i++)
								$donneesSimilaires[] = '<button type="button" data-bs-target="#myCarousel" data-bs-slide-to="'.$i.'" '.($i < 1 ? 'class="active" aria-current="true"' : null).' aria-label="Slide '.$i.'"></button>';

							$donneesSimilaires[] = '</div>

							<div class="carousel-inner">';

							for($i = 0; $i < $cS; $i++)
							{
								$simId = explode('|', $sims[$i])[0];
								$simPoster_w300 = str_replace('original', 'w300', explode('|', $sims[$i])[1]);
								$simPoster_w780 = str_replace('original', 'w780', explode('|', $sims[$i])[1]);
								$simPoster_w1280 = str_replace('original', 'w1280', explode('|', $sims[$i])[1]);
								$simNom = explode('|', $sims[$i])[2];

								$donneesSimilaires[] = '<div class="carousel-item '.($i < 1 ? 'active' : null).'">
									<picture>
										<source srcset="'.$simPoster_w300.'" media="(max-width: 500px)">
										<source srcset="'.$simPoster_w780.'" media="(max-width: 780px)">
										<source srcset="'.$simPoster_w1280.'" media="(max-width: 1280px)">
										<source srcset="'.explode('|', $sims[$i])[1].'">
										<img src="'.explode('|', $sims[$i])[1].'" class="d-block w-100 rounded" alt="Poster de '.$simNom.'" loading="lazy">
									</picture>

									<div class="carousel-caption"><a href="?type='.$type.'&id='.$simId.'"><div class="bg-backdrop-recommandes-similaires">'.$simNom.'</div></a></div>
								</div>';
							}

							$donneesSimilaires[] = '</div>

							<button class="carousel-control-prev btn-success" type="button" data-bs-target="#myCarousel" data-bs-slide="prev" title="Slide précédent">
								<span class="carousel-control-prev-icon" aria-hidden="true"></span>
								<span class="visually-hidden">Précédent</span>
							</button>
							<button class="carousel-control-next btn-success" type="button" data-bs-target="#myCarousel" data-bs-slide="next" title="Slide suivant">
								<span class="visually-hidden">Suivant</span>
								<span class="carousel-control-next-icon" aria-hidden="true"></span>
							</button>
						</div>
					</div>
				</div>';

				if(!empty($donneesSimilaires))
				{
					echo implode($donneesSimilaires);

					cache($cacheSimilaires, implode($donneesSimilaires));
				}
			}

			else
				echo (file_exists($cacheSimilaires) AND filesize($cacheSimilaires) > 0) ? file_get_contents($cacheSimilaires) : null;
		}
	}

	// Fiche d’un artiste

	elseif(empty($type) AND !empty($person_id))
	{
		$nom		= !empty($p->name) ? trim($p->name) : 'nom inconnu';
		$img		= $tmdb->getImageUrl($p->profile_path, TMDB::IMAGE_PROFILE, 'h632', $nom);
		$imgLien	= $tmdb->getImageUrl($p->profile_path, TMDB::IMAGE_PROFILE, 'original', $nom);
		$acteur		= ($p->gender === 1) ? 'Actrice' : 'Acteur';
		$sexe		= ($p->gender === 1) ? 'e' : null;

		echo '<h1 class="liner"><a href="?person_id='.$person_id.'&merge">'.$nom.'</a></h1>

		<div class="row">
			<div class="col-12 col-lg-2" id="informations-artiste">
				<div class="mb-4 text-center"><a href="'.$imgLien.'" data-fancybox="gallerie"><img src="'.$img.'" class="img-fluid img-fiche-artiste" alt="Photo de '.$nom.'" title="Photo de '.$nom.'"></a></div>
				<div class="mb-2 text-center gap-3" id="social">
					'.($p_external->facebook_id		? '<a href="https://www.facebook.com/'.$p_external->facebook_id.'"			title="Profil Facebook"		'.$onclick.'><i class="fab fa-facebook-square"></i></a>'	: null).'
					'.($p_external->twitter_id		? '<a href="https://x.com/'.$p_external->twitter_id.'"						title="Profil X"			'.$onclick.'><i class="fa-brands fa-x-twitter"></i></a>'	: null).'
					'.($p_external->instagram_id	? '<a href="https://instagram.com/'.$p_external->instagram_id.'/"			title="Profil Instagram"	'.$onclick.'><i class="fa-brands fa-instagram"></i></a>'	: null).'
					'.($p_external->tiktok_id		? '<a href="https://www.tiktok.com/'.$p_external->tiktok_id.'"				title="Profil TikTok"		'.$onclick.'><i class="fa-brands fa-tiktok"></i></a>'		: null).'
					'.($p_external->wikidata_id		? '<a href="https://www.wikidata.org/wiki/'.$p_external->wikidata_id.'"		title="Fiche Wikipédia"		'.$onclick.'><i class="fa-brands fa-wikipedia-w"></i></a>'	: null).'
					'.($p_external->youtube_id		? '<a href="https://youtube.com/'.$p_external->youtube_id.'"				title="Chaîne YouTube"		'.$onclick.'><i class="fa-brands fa-youtube"></i></a>'		: null).'
					'.($p->homepage					? '<a href="'.$p->homepage.'"						class=" text-white"		title="Site officiel"		'.$onclick.'><i class="fas fa-link"></i></a>'				: null).'
					'.($p_external->imdb_id			? '<a href="https://www.imdb.com/name/'.$p_external->imdb_id.'/"			title="Fiche IMDb"			'.$onclick.'><i class="fa-brands fa-imdb"></i></a>'			: null).'
				</div>
				<div class="mb-4 text-center">'.($p->id ? '<a href="https://www.themoviedb.org/person/'.$p->id.'" '.$onclick.'>'.logoTMDB(20).'</a>' : null).'</div>
				<div class="mb-4">
					'.($p->birthday					? '<p class="mb-2"><i class="fas fa-birthday-cake"></i> né'.$sexe.' le '.IntlDateFormatter::formatObject(IntlCalendar::fromDateTime($p->birthday, 'fr_FR'), 'd MMMM y', 'fr_FR').(!$p->deathday ? ' (<span class="fst-italic">'.(date_diff(date_create($p->birthday), date_create('today'))->y).' ans</span>)' : null).'</p>'	: null).'
					'.($p->place_of_birth			? '<p class="mb-2" title="Afficher sur Google Maps"><a href="https://www.google.com/maps/search/?q='.urlencode($p->place_of_birth).'" class="text-decoration-underline" data-fancybox="gallerie" data-preload="true" data-width="1600" data-height="1066"><i class="fa-solid fa-location-dot"></i> '.$p->place_of_birth.'</a></p>'															: null).'
					'.($p->deathday					? '<p class="mb-0" title="Décédé'.$sexe.' '.temps(strtotime($p->deathday)).'"><i class="fa-solid fa-cross"></i> décédé'.$sexe.' le '.IntlDateFormatter::formatObject(IntlCalendar::fromDateTime($p->deathday, 'fr_FR'), 'd MMMM y', 'fr_FR').' à l’âge de '.(date_diff(date_create($p->birthday), date_create(($p->deathday ? $p->deathday : 'today')))->y).' ans</p>'					: null).'
				</div>';

				if(!empty($p->also_known_as))
				{
					echo '<div class="d-none d-lg-inline-block text-start">
						<p class="fs-5 fw-bold mb-1" title="Différent(s) alias de l’acteur">Alias</p>';

						foreach($p->also_known_as as $alias)
							echo '<p style="font-size: .7rem;" class="mb-0 alias">'.$alias.'</p>';

					echo '</div>';
				}

			echo '</div>

			<div class="col-12 col-lg-10">
				<div class="mb-5" id="biographie">
					<p class="fs-4 fw-bold liner">Biographie</p>
					<div class="ps-3 border-c">';

						if(empty($p->biography)) {
							echo 'La biographie de <span class="fw-bold">'.$nom.'</span> est inconnue';
						}

						elseif(mb_strlen($p->biography) < 500) {
							echo nl2br($p->biography, false);
						}

						else {
							echo nl2br(substr($p->biography, 0, 500)).'<span class="pds">…</span>

							<span style="display: block; max-height: 0; overflow: hidden; transition: max-height .75s ease;" id="texte-suite">
								'.nl2br(substr($p->biography, 500), false).'
							</span>

							<p class="text-end m-0"><span class="lire-la-suite curseur" id="btnLireLaSuite">Lire la suite</span></p>';
						}

					echo '</div>
				</div>';

				$credits = [];
				foreach($p_credits->cast as $c)
				{
					$credits[] = (array) $c;
				}

				$unique = [];
				foreach($credits as $item)
				{
					$name = $item['name'] ?? null;
					if ($name !== null) {
						$unique[$name] = $item;
					}
				}

				$credits = array_values($unique);

				usort($credits, function($a, $b) {
					return $b['popularity'] <=> $a['popularity'];
				});

				$connuPour = array_slice($credits, 0, 10);
				if(!empty($connuPour))
				{
					echo '<div class="container mb-5" id="connuPour">
						<p class="fs-4 fw-bold liner">Connu pour</p>

						<div class="row flex-nowrap overflow-auto gap-3">';

						foreach($connuPour as $c => $v)
						{
							$typeFiche	= ($v['media_type'] === 'movie')	? 'film'				: 'serie';
							$titre		= ($v['media_type'] === 'movie')	? $v['title']			: $v['name'];
							$id			= !empty($v['id'])					? (int) $v['id']		: null;
							$posterUrl	= $tmdb->getImageUrl($v['poster_path'], TMDB::IMAGE_POSTER, 'w185', $titre);
							$date		= ($v['media_type'] === 'movie')	? strtotime($v['release_date'])	: strtotime($v['first_air_date']);

							echo'<div class="cadre-films-series-connupour">
								<a href="/projets/tmdb/?type='.$typeFiche.'&id='.$id.'"><img src="'.$posterUrl.'" class="img-fluid" alt="Poster de '.$titre.'" title="Poster de '.$titre.'"></a>
								<p class="my-3 px-1 text-center text-truncate fw-bold"><a href="/projets/tmdb/?type='.$typeFiche.'&id='.$id.'">'.$titre.'</a></p>
								<p class="mb-0 text-dark-emphasis text-center text-truncate" title="Date de sortie">'.(is_numeric($date) ? '<time datetime="'.date(DATE_ATOM, $date).'">'.dateFormat($date).'</time>' : 'date inconnue').'</p>
							</div>';
						}

						echo '</div>
					</div>';
				}

				echo '<div>';

					$cast = (!isset($_GET['cast']) AND !empty($p_credits->crew)) ? $p_credits->crew : [];
					$crew =	(!isset($_GET['crew']) AND !empty($p_credits->cast)) ? $p_credits->cast : [];

					if(!isset($_GET['cast']) AND !empty($p_credits->crew))		$cast = $p_credits->crew;
					elseif(!isset($_GET['crew']) AND !empty($p_credits->cast))	$crew = $p_credits->cast;
					elseif(isset($_GET['crew']) AND isset($_GET['cast'])) {
						$cast = $p_credits->crew;
						$crew = $p_credits->cast;
					} else {
						$cast = [];
						$crew = [];
					}

					$mergeUrl	= isset($_GET['merge'])			? '&merge'	: null;
					$castUrl	= isset($_GET['cast'])			? '&cast'	: null;
					$crewUrl	= isset($_GET['crew'])			? '&crew'	: null;
					$moviesUrl	= isset($_GET['movie'])			? '&movie'	: null;
					$serieUrl	= isset($_GET['serie'])			? '&serie'	: null;
					$fA			= (!empty($_GET['filtreAnnee']) AND mb_strlen($_GET['filtreAnnee']) === 4) ? (int) $_GET['filtreAnnee'] : null;
					$fAUrl		= !empty($fA)					? '&filtreAnnee='.$fA : null;

					$castArray = stdToArray($cast);
					$crewArray = stdToArray($crew);

					$castArray = (isset($_GET['movie']) AND !isset($_GET['serie'])) ? uniqueMultidimArray($castArray, 'title')	: $castArray;
					$crewArray = (isset($_GET['serie']) AND !isset($_GET['movie'])) ? uniqueMultidimArray($crewArray, 'name')	: $crewArray;

					$r = array_merge_recursive($castArray, $crewArray);

					usort($r, function($a, $b) {
						$ageA = isset($a['release_date']) ? $a['release_date'] : PHP_INT_MAX;
						$ageB = isset($b['release_date']) ? $b['release_date'] : PHP_INT_MAX;
						return $ageA <=> $ageB;
					});

					foreach($r as $item) {
						if(!empty($item['release_date'])) {
							$pA[] = explode('-', $item['release_date'])[0];
						}
						if(!empty($item['first_air_date'])) {
							$pA[] = explode('-', $item['first_air_date'])[0];
						}
					}

					if(!empty($pA[0]))
					{
						rsort($pA);
						$pA = array_unique($pA);
					}

					echo '<div class="container mb-5" id="filtres">
						<div class="row justify-content-center">
							<div class="col-auto">
								<div class="dropdown dropdown-hover">
									<button type="button" style="min-width: 150px;" class="btn btn-outline-brain dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">Filtres</button>
									<ul class="dropdown-menu">
										<li><a href="?person_id='.$person_id.(!isset($_GET['merge'])			? '&merge' : null).$castUrl.$crewUrl.$moviesUrl.$serieUrl.$fAUrl.'#biographie" class="dropdown-item '.(isset($_GET['merge'])	? 'active' : null).'" title="Fusionner les titres similaires">Fusionner<span class="float-end">'.(isset($_GET['merge']) ? '<i class="fa-solid fa-check"></i>' : null).'</span></a></li>
										<li><a class="dropdown-item disabled" aria-disabled="true"><hr class="my-1"></a></li>
										<li><a href="?person_id='.$person_id.$mergeUrl.(!isset($_GET['cast'])	? '&cast' : null).$crewUrl.$moviesUrl.$serieUrl.$fAUrl.'#biographie" class="dropdown-item '.(isset($_GET['cast'])				? 'active' : null).'" title="Filtrer par acteur">Acteur<span class="float-end">'.(isset($_GET['cast']) ? '<i class="fa-solid fa-check"></i>' : null).'</span></a></li>
										<li><a href="?person_id='.$person_id.$mergeUrl.(!isset($_GET['crew'])	? '&crew' : null).$castUrl.$moviesUrl.$serieUrl.$fAUrl.'#biographie" class="dropdown-item '.(isset($_GET['crew'])				? 'active' : null).'" title="Filtrer par réalisateur / équipe de production">Création<span class="float-end">'.(isset($_GET['crew']) ? '<i class="fa-solid fa-check"></i>' : null).'</span></a></li>
										<li><a class="dropdown-item disabled" aria-disabled="true"><hr class="my-1"></a></li>
										<li><a href="?person_id='.$person_id.$mergeUrl.(!isset($_GET['movie'])	? '&movie' : null).$castUrl.$crewUrl.$serieUrl.$fAUrl.'#biographie" class="dropdown-item '.(isset($_GET['movie'])				? 'active' : null).'"><i class="fa-solid fa-film me-1"></i> Les Films<span class="float-end">'.(isset($_GET['movie']) ? '<i class="fa-solid fa-check"></i>' : null).'</span></a></li>
										<li><a href="?person_id='.$person_id.$mergeUrl.(!isset($_GET['serie'])	? '&serie' : null).$castUrl.$crewUrl.$moviesUrl.$fAUrl.'#biographie" class="dropdown-item '.(isset($_GET['serie'])				? 'active' : null).'"><i class="fa-solid fa-tv me-1"></i> Les Séries<span class="float-end">'.(isset($_GET['serie']) ? '<i class="fa-solid fa-check"></i>' : null).'</span></a></li>
									</ul>
								</div>
							</div>
							<div class="col-auto">
								<div class="dropdown dropdown-hover">
									<button type="button" style="min-width: 150px;" class="btn btn-outline-brain dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">'.(!empty($fA) ? ($fA === 5000 ? 'En projet' : $fA) : 'Par année').'</button>
									<ul class="dropdown-menu scroll">
										<li><a href="?person_id='.$person_id.$mergeUrl.$castUrl.$crewUrl.$moviesUrl.$serieUrl.'#enprojet" class="dropdown-item'.(empty($fA) ? ' active' : null).'">Toutes les années'.(empty($fA) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>
										<li><a href="?person_id='.$person_id.$mergeUrl.$castUrl.$crewUrl.$moviesUrl.$serieUrl.'&filtreAnnee=5000#enprojet" class="dropdown-item'.((!empty($fA) AND $fA == 5000) ? ' active' : null).'">En projet<span class="float-end">'.((!empty($fA) AND $fA == 5000) ? '<i class="fa-solid fa-check"></i>' : null).'</span></a></li>
										<li><a class="dropdown-item disabled" aria-disabled="true"><hr class="my-1"></a></li>';

										for($a = 2025; $a > 1900; $a--)
											echo '<li><a href="?person_id='.$person_id.$mergeUrl.$castUrl.$crewUrl.$moviesUrl.$serieUrl.'&filtreAnnee='.$a.'#'.$a.'" class="dropdown-item'.((!empty($fA) AND $fA == $a) ? ' active' : null).'">'.$a.'<span class="float-end">'.((!empty($fA) AND $fA === $a) ? '<i class="fa-solid fa-check"></i>' : null).'</span></a></li>';

									echo '</ul>
								</div>
							</div>
							<div class="col-auto">
								<a href="?person_id='.$person_id.'" class="btn btn-danger ms-0 	ms-lg-2" title="Réinitialiser les filtres"><i class="fas fa-times fa-1x"></i></a>
							</div>
						</div>
					</div>';

					if(!empty($r))
					{
						if(isset($_GET['merge']))
							$r = uniqueMultidimArray($r, 'id');

						$videos = [];
						foreach($r as $result => $v)
						{
							if((!empty($_GET['departement']) AND !empty($v['department']) AND mb_strtolower($_GET['departement']) == mb_strtolower($v['department'])) OR (empty($_GET['departement'])))
							{
								if(isset($_GET['movie']))
								{
									if(isset($v['original_title']) AND !empty($v['release_date']))		$videos[explode('-', $v['release_date'])[0]][] = $result;
									elseif(isset($v['original_title']) AND empty($v['release_date']))	$videos[5000][] = $result;
								}

								if(isset($_GET['serie']))
								{
									if(isset($v['first_air_date']) AND !empty($v['first_air_date']))	$videos[explode('-', $v['first_air_date'])[0]][] = $result;
									elseif(isset($v['first_air_date']) AND empty($v['first_air_date']))	$videos[5000][] = $result;
								}

								if(!isset($_GET['movie']) AND !isset($_GET['serie']))
								{
									// Films
									if(isset($v['original_title']) AND !empty($v['release_date']))		$videos[explode('-', $v['release_date'])[0]][] = $result;
									elseif(isset($v['original_title']) AND empty($v['release_date']))	$videos[5000][] = $result;

									// Séries
									if(isset($v['first_air_date']) AND !empty($v['first_air_date']))	$videos[explode('-', $v['first_air_date'])[0]][] = $result;
									elseif(isset($v['first_air_date']) AND empty($v['first_air_date']))	$videos[5000][] = $result;
								}
							}
						}

						if(!empty($videos))
						{
							krsort($videos, SORT_NUMERIC);

							if(!empty($fA) AND mb_strlen($fA) === 4)
							{
								if(!empty($videos[$fA]))
								{
									$vA[$fA] = $videos[$fA];
									unset($videos);
								}

								else
									$erreurVideo = true;
							}

							else
								$vA = $videos;

							if(!empty($vA))
							{
								$cvA = count($vA);
								$i = 0;
								foreach($vA as $cle => $annees)
								{
									echo '<div'.($i < ($cvA - 1) ? ' class="mb-5"' : null).' id="'.($cle === 5000 ? 'enprojet' : $cle).'">
										<h3 class="liner mb-3 mt-0"><a href="#'.($cle === 5000 ? 'enprojet' : $cle).'" class="ancre"><i class="fa-solid fa-link ms-3 me-2"></i></a>'.($cle === 5000 ? 'En projet' : $cle).'</h3>';

										foreach($annees as $id)
										{
											$idTmdb			= !empty($r[$id]['id'])						? $r[$id]['id']								: null;
											$synopsis		= !empty($r[$id]['overview'])				? $r[$id]['overview']						: null;
											$media			= !empty($r[$id]['media_type'])				? $r[$id]['media_type']						: null;
											$voteAvg		= !empty($r[$id]['vote_average'])			? $r[$id]['vote_average']					: 0;
											$dpt			= !empty($r[$id]['department'])				? $r[$id]['department']						: null;
											$char			= !empty($r[$id]['character'])				? $r[$id]['character']						: null;
											$char			= !empty($char)								? charsFr($char)							: $char;
											$job			= !empty($r[$id]['job'])					? $r[$id]['job']							: null;
											$job			= !empty($job)								? jobsFr($job)								: $job;
											$typeHtml		= !empty($r[$id]['first_air_date'])			? 'série'									: 'film';

											$synoC			= !empty($synopsis)							? mb_strlen(strip_tags($synopsis))			: 0;

											if(!empty($synoC) AND $synoC <= 350)				$synoHtml = trim(strip_tags(str_replace("'", '’', str_replace('"', '', $synopsis))));
											elseif(!empty($synoC) AND $synoC > 350)				$synoHtml = trim(substr(strip_tags(str_replace("'", '’', str_replace('"', '', $synopsis))), 0, 350)).'…';
											else												$synoHtml = 'Synopsis inconnu';

											if(!empty($r[$id]['title']))	$titre = secuChars($r[$id]['title']);
											elseif(!empty($r[$id]['name']))	$titre = secuChars($r[$id]['name']);
											else							$titre = 'inconnu';

											$posterPath		= $tmdb->getImageUrl($r[$id]['poster_path'], TMDB::IMAGE_POSTER, 'original', $titre);

											if(!empty($r[$id]['release_date']))					$release_date = $r[$id]['release_date'];
											elseif(!empty($r[$id]['first_air_date']))			$release_date = $r[$id]['first_air_date'];
											else												$release_date = null;

											$voteAvg = round($voteAvg, 1);

											if($voteAvg >= 8.1 AND $voteAvg <= 10)				$clrVote = 'style="color: rgba(25,135,84, 1);"';
											elseif($voteAvg >= 6.1 AND $voteAvg <= 8)			$clrVote = 'style="color: rgba(140,164,46, 1);"';
											elseif($voteAvg >= 4.1 AND $voteAvg <= 6)			$clrVote = 'style="color: rgba(255,193,7, 1);"';
											elseif($voteAvg >= 2.1 AND $voteAvg <= 4)			$clrVote = 'style="color: rgba(253,126,20, 1);"';
											elseif($voteAvg >= 0 AND $voteAvg <= 2)				$clrVote = 'style="color: rgba(220,53,69, 1);"';
											if(empty($voteAvg))									$clrVote = 'style="color: rgba(214,104,83, 1);"';

											if(!empty($voteAvg))								$v = $voteAvg.' / 10';
											else												$v = '—';

											if(!empty($r[$id]['release_date']))					$y = explode('-', $r[$id]['release_date'])[0];
											elseif(!empty($r[$id]['first_air_date']))			$y = explode('-', $r[$id]['first_air_date'])[0];
											else												$y = '—';

											echo '<div class="row gx-0 py-2 details-artiste">
												<div class="col-12 col-lg-9 mb-2 mb-lg-0 ps-2 text-start clearfix">
													<a href="'.$posterPath.'" data-fancybox="gallerie"><img src="'.$posterPath.'" style="border: rgba(214,104,83, 1) 1px solid; height: 90px; width: 65px;" class="float-start me-4 rounded" alt="Poster de '.$titre.'"></a>
													<p>
														'.(!empty($job) ? '* ' : null).'<a href="?type='.($media === 'tv' ? 'serie' : 'film').'&id='.$idTmdb.'" class="text-decoration-underline fw-bold" data-bs-toggle="tooltip" data-bs-title="'.$synoHtml.'">'.$titre.'</a>
														'.(!empty($char) ? ' incarne <span class="fst-italic fw-bold">'.ucwords($char).'</span>' : null).'
														'.(!empty($job) ? ' - <span class="fw-semibold" title="Fonction de l’artiste">'.$job.'</span>' : null).'
														'.(empty($release_date) ? '<br><br><span class="badge bg-secondary">en production</span>' : null).'
													</p>
												</div>
												<div class="col-4 col-lg-1 my-auto text-center" '.$clrVote.' title="Note moyenne : '.($v == '—' ? 'inconnue' : $v).'">'.$v.'</div>
												<div class="col-4 col-lg-1 my-auto text-center" title="Produit en '.($y == '—' ? 'inconnue' : $y).'">'.$y.'</div>
												<div class="col-4 col-lg-1 my-auto text-center" title="Type : '.$typeHtml.'">'.$typeHtml.'</div>
											</div>';
										}

									echo '</div>';

									$i++;
								}
							}
						}

						else
							$erreurVideo = true;
					}

					echo (isset($_GET['movie']) AND !isset($_GET['serie']) AND empty($videos)) ? alerte('danger', 'Aucun film trouvé pour <span class="fw-bold">'.$nom.'</span>') : null;
					echo (!isset($_GET['movie']) AND isset($_GET['serie']) AND empty($videos)) ? alerte('danger', 'Aucune série trouvée pour <span class="fw-bold">'.$nom.'</span>') : null;

					if(isset($erreurVideo) AND $erreurVideo === true)
					{
						if(!empty($fA) AND $fA !== 5000)		$msgAnnee = 'Aucun film / série n’été trouvé pour l’année <span class="fw-bold">'.$fA.'</span>';
						elseif(!empty($fA) AND $fA == 5000)		$msgAnnee = 'Aucun film / série <span class="fw-bold">en projet</span> n’été trouvé';
						else									$msgAnnee = 'La filmographie de <span class="fw-bold">'.$nom.'</span> est inconnue';

						echo alerte('danger', $msgAnnee);
					}

				echo '</div>
			</div>
		</div>';
	}

	// Distribution des rôles et équipe technique

	elseif(!empty($type) AND !empty($id) AND isset($_GET['fullCast']))
	{
		$castArray = array_merge((isset($t_credits->cast) ? $t_credits->cast : []), (isset($t_credits->guest_stars) ? $t_credits->guest_stars : []));
		if(!empty($castArray)) {
			$castArray = stdToArray($castArray);
			$castArray = uniqueMultidimArray($castArray, 'original_name');
			$countCast = count($castArray);
		}

		else {
			$countCast = !empty($castArray) ? count($castArray) : 0;
		}

		if(isset($t_credits->crew) AND !empty($t_credits->crew)) {
			$crewArray = stdToArray($t_credits->crew);
			$crewArray = uniqueMultidimArray($crewArray, 'original_name');
			$countCrew = !empty($crewArray) ? count($crewArray) : 0;
		}

		else {
			$crewArray = [];
			$countCrew = !empty($crewArray) ? count($crewArray) : 0;
		}

		function roles(array $get, string $castOrCrew): string
		{
			global $tmdb;

			if(!empty($get))
			{
				foreach($get as $casts => $cast)
				{
					$idArtiste		= (int) $cast['id'];
					$nomOriginal	= (string) !empty($cast['original_name'])			? $cast['original_name']											: 'Nom inconnu';
					$sexe			= (int) !empty($cast['gender'])						? $cast['gender']													: 2;
					$lienImg		= $tmdb->getImageUrl($cast['profile_path'], TMDB::IMAGE_PROFILE, 'w185', $nomOriginal);
					$lienImgOrig	= $tmdb->getImageUrl($cast['profile_path'], TMDB::IMAGE_PROFILE, 'original', $nomOriginal);

					if($castOrCrew === 'cast')
					{
						$nbEpisodes	= (int) !empty($cast['roles'][0]->episode_count)	? $cast['roles'][0]->episode_count									: 0;

						$domaine	= (string) !empty($cast['known_for_department'])	? jobsFr($cast['known_for_department'], $sexe)						: 'domaine inconnu';

						if(!empty($cast['character']))									$role = ucwords(jobsFr($cast['character'], $sexe));
						elseif(!empty($cast['roles'][0]->character))					$role = ucwords(jobsFr($cast['roles'][0]->character, $sexe));
						else															$role = 'rôle inconnu';
					}

					elseif($castOrCrew === 'crew')
					{
						$nbEpisodes	= (int) !empty($cast['jobs'][0]->episode_count)		? $cast['jobs'][0]->episode_count									: 0;

						$domaine	= (string) !empty($cast['known_for_department'])	? jobsFr($cast['known_for_department'], $sexe)						: 'domaine inconnu';
						$secteur	= (string) !empty($cast['department'])				? jobsFr($cast['department'], $sexe)								: 'secteur inconnu';

						if(!empty($cast['job']))										$job = ucwords(jobsFr($cast['job'], $sexe));
						elseif(!empty($cast['jobs'][0]->job))							$job = ucwords(jobsFr($cast['jobs'][0]->job, $sexe));
						else															$job = 'poste inconnu';
					}

					$roles[] = '<div class="col-6 col-md-4 col-lg-2 mb-3" title="'.$nomOriginal.'">
						<div class="border border-bv rounded h-100">
							<div class="mb-2"><a href="'.$lienImgOrig.'" data-fancybox="gallerie"><img src="'.$lienImg.'" class="img-fluid img-profil-distribution" alt="'.$nomOriginal.'" title="Poster de '.$nomOriginal.'"></a></div>
							<div class="px-2 text-center">
								<p class="mb-2 fw-bold text-truncate"><a href="/projets/tmdb/?person_id='.$idArtiste.'&merge" title="Nom : '.$nomOriginal.'">'.$nomOriginal.'</a></p>
								'.($castOrCrew === 'cast' ? '<p class="mb-2 text-secondary text-truncate" title="Nom du rôle : '.$role.'">'.$role.'</p>' : null).'
								'.(($castOrCrew === 'cast' AND $nbEpisodes > 0) ? '<p class="mb-2 text-truncate" title="'.$nomOriginal.' a joué(e) dans '.$nbEpisodes.' épisode'.s($nbEpisodes).'">'.$nbEpisodes.' épisode'.s($nbEpisodes).'</p>' : null).'
								'.(($castOrCrew === 'crew' AND $nbEpisodes > 0) ? '<p class="mb-2 text-truncate" title="'.$nomOriginal.' a travailler dans '.$nbEpisodes.' épisode'.s($nbEpisodes).'">'.$nbEpisodes.' épisode'.s($nbEpisodes).'</p>' : null).'
								'.(($castOrCrew === 'crew' AND $domaine !== $job) ? '<p class="mb-2 text-light-emphasis text-truncate" title="Intitulé du poste : '.$job.'">'.$job.'</p>' : null).'
							</div>
						</div>
					</div>';
				}

				return implode($roles);
			}

			return alerte('danger', 'Équipe technique inconnue', null);
		}

		if(!empty($saison) AND empty($episode))			$cacheDistribution = $_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/distribution_'.$type.'_'.$id.'_saison_'.$saison.'.cache';
		elseif(!empty($saison) AND !empty($episode))	$cacheDistribution = $_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/distribution_'.$type.'_'.$id.'_saison_'.$saison.'_episode_'.$episode.'.cache';
		else											$cacheDistribution = $_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/distribution_'.$type.'_'.$id.'.cache';

		if(!file_exists($cacheDistribution) OR (filemtime($cacheDistribution) < strtotime($cacheT)))
		{
			$donneesDistribution[] = '<h1 class="liner"><a href="?type='.$type.'&id='.$id.'">'.$titre.'</a></h1>';

			if($type === 'serie')
			{
				$donneesDistribution[] = '<div class="d-flex liner mb-4">
					<h2 class="fs-3'.(!empty($saison) ? ' liner' : null).'"><a href="?type=serie&id='.$id.'&fullCast" data-bs-toggle="tooltip" data-bs-title="Les Saisons et épisodes de '.$titre.'"><span class="d-block d-lg-none">Saisons et éps.</span><span class="d-none d-lg-block">Les saisons et épisodes</span></a></h2>
					'.(!empty($saison) ? '<h2 class="fs-3'.(!empty($episode) ? ' liner' : null).'"><a href="?type=serie&id='.$id.'&fullCast&saison='.$saison.'" data-bs-toggle="tooltip" data-bs-title="Saison '.$saison.' de '.$titre.'"><span class="d-block d-lg-none">S'.$saison.'</span><span class="d-none d-lg-block">Saison '.$saison.'</span></a></h2>' : null).'
					'.((!empty($saison) AND !empty($episode)) ? '<h2 class="fs-3"><a href="?type=serie&id='.$id.'&fullCast&saison='.$saison.'&episode='.$episode.'" data-bs-toggle="tooltip" data-bs-title="Épisode '.$episode.' de '.$titre.'"><span class="d-block d-lg-none">E'.$episode.'</span><span class="d-none d-lg-block">Épisode '.$episode.'</span></a></h2>' : null).'
				</div>';

				if(!empty($saison) AND empty($episode)) {
					$lienTMDBSaisonEp = 'https://www.themoviedb.org/tv/'.$id.'/season/'.$saison;
					$externalIdsEpisode = $tmdb->getTvSeasonsExternalIds($id, $saison);
				}

				elseif(!empty($saison) AND !empty($episode)) {
					$lienTMDBSaisonEp = 'https://www.themoviedb.org/tv/'.$id.'-'.slug($titre).'/season/'.$saison.'/episode/'.$episode;
					$externalIdsEpisode = $tmdb->getTvEpisodesExternalIds($id, $saison, $episode);
				}

				else {
					$lienTMDBSaisonEp = 'https://www.themoviedb.org/tv/'.$id.'-'.slug($titre).'/seasons';
					$externalIdsEpisode = $tmdb->getTvExternalIds($id);
				}

				$lienIMDbSaisonEpisode = !empty($externalIdsEpisode->imdb_id) ? 'https://www.imdb.com/title/'.$externalIdsEpisode->imdb_id.'/' : null;

				$donneesDistribution[] = '<div class="row mb-4">
					<div class="col-auto ms-auto d-flex flex-wrap justify-content-center">';

						if($t->number_of_seasons > 1)
						{
							$donneesDistribution[] = '<div class="dropdown dropdown-hover">
								<button type="button" style="min-width: 160px;" class="btn btn-outline-brain dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">Saison '.$saison.'</button>
								<ul class="dropdown-menu scroll">';

									foreach($t->seasons as $cleSaison => $valSaison)
									{
										if($valSaison->name !== 'Épisodes spéciaux')
										{
											$sNum = (int) $valSaison->season_number;

											$donneesDistribution[] = '<li><a href="?type=serie&id='.$id.'&fullCast&saison='.$sNum.'" class="dropdown-item'.((!empty($saison) AND $sNum === $saison) ? ' active' : null).'">'.$sNum.((!empty($saison) AND $sNum === $saison) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>';
										}
									}

								$donneesDistribution[] = '</ul>
							</div>';
						}

						else
						{
							$donneesDistribution[] = '<button type="button" style="min-width: 160px;" class="btn btn-outline-brain dropdown-toggle d-flex justify-content-between align-items-center" disabled>Saison 1</button>';
						}

					$donneesDistribution[] = '</div>

					<div class="col-auto d-flex flex-wrap justify-content-center">';

						if(!empty($t_saisons->episodes))
						{
							$donneesDistribution[] = '<div class="dropdown dropdown-hover">
								<button type="button" style="min-width: 160px;" class="btn btn-outline-brain dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">Épisode '.$episode.'</button>
								<ul class="dropdown-menu scroll">';

									foreach($t_saisons->episodes as $cleEp => $valEp)
									{
										$sNum = (int) $valEp->season_number;
										$epNum = (int) $valEp->episode_number;

										$donneesDistribution[] = '<li><a href="?type=serie&id='.$id.'&fullCast&saison='.$sNum.'&episode='.$epNum.'" class="dropdown-item'.((!empty($episode) AND $epNum === $episode) ? ' active' : null).'">'.$epNum.((!empty($episode) AND $epNum === $episode) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>';
									}

								$donneesDistribution[] = '</ul>
							</div>';
						}

						else
						{
							$donneesDistribution[] = '<button type="button" style="min-width: 160px;" class="btn btn-outline-brain dropdown-toggle d-flex justify-content-between align-items-center" disabled>Épisodes</button>';
						}

					$donneesDistribution[] = '</div>

					<div class="col-auto me-auto">
						<a href="?type=serie&id='.$id.'&fullCast" class="btn btn-danger" title="Réinitialiser les filtres"><i class="fas fa-times"></i></a>
					</div>
				</div>

				<div class="col-12 d-flex flex-wrap justify-content-center gap-3 mb-4">
					'.(!empty($lienIMDbSaisonEpisode) ? '<a href="'.$lienIMDbSaisonEpisode.'"	class="svg-lien"	title="Fiche IMDb" '.$onclick.'>'.logoIMDb(23).'</a>' : '<span class="me-2" title="Fiche IMDBb désactivée">'.logoIMDb(23, 'filter: grayscale(100%);').'</span>').'
					<a href="'.$lienTMDBSaisonEp.'"												class="svg-lien"	title="Fiche TMDB" '.$onclick.'>'.logoTMDBMini(20, 75).'</a>
				</div>';
			}

			$donneesDistribution[] = '<div class="mx-auto">
				<div class="mb-4 text-center" id="cast">
					<div class="d-inline-block me-5"><a href="#cast" style="border-bottom: 1px dashed;" class="fs-3 me-2" title="Distribution des rôles '.($type === 'film' ? 'du film' : 'de la série').'">Distribution des rôles</a> <span class="fs-5 opacity-75" title="Nombre d’acteur">'.$countCast.'</span></div>
					<div class="d-inline-block"><a href="#crew" class="fs-3 me-2" title="Équipe technique '.($type === 'film' ? 'du film' : 'de la série').'">Équipe technique</a> <span class="fs-5 opacity-75" title="Nombre de personne dans l’équipe technique">'.$countCrew.'</span></div>
				</div>

				<div class="row mx-auto">';

					// Distribution des rôles
					$donneesDistribution[] = roles($castArray, 'cast');

				$donneesDistribution[] = '</div>

				<div class="mb-4 mt-5 text-center gap-3" id="crew">
					<div class="d-inline-block me-5"><a href="#cast" class="fs-3 me-3" title="Distribution des rôles '.($type === 'film' ? 'du film' : 'de la série').'">Distribution des rôles</a> <span class="fs-5 opacity-75" title="Nombre d’acteur">'.$countCast.'</span></div>
					<div class="d-inline-block"><a href="#crew" style="border-bottom: 1px dashed;" class="fs-3 me-3" title="Équipe technique '.($type === 'film' ? 'du film' : 'de la série').'">Équipe technique</a> <span class="fs-5 opacity-75" title="Nombre de personne dans l’équipe technique">'.$countCrew.'</span></div>
				</div>

				<div class="row mx-auto">';

					// Équipe technique
					$donneesDistribution[] = roles($crewArray, 'crew');

				$donneesDistribution[] = '</div>
			</div>';

			if(!empty($donneesDistribution))
			{
				echo implode($donneesDistribution);

				cache($cacheDistribution, implode($donneesDistribution));
			}
		}

		else
			echo (file_exists($cacheDistribution) AND filesize($cacheDistribution) > 0) ? file_get_contents($cacheDistribution) : null;
	}

	// Les Saisons & Épisodes

	elseif(!empty($type) AND !empty($id) AND isset($_GET['saison']) AND !isset($_GET['fullCast']) AND !isset($_GET['populaires']) AND !isset($_GET['exclus']) AND !isset($_GET['hash']) AND !isset($_GET['seedbox']) AND !isset($_GET['note-le']))
	{
		$cacheEpisodes = $_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/episodes_'.$type.'_'.$id.'.cache';
		if(!file_exists($cacheEpisodes) OR (filemtime($cacheEpisodes) < strtotime($cacheT)))
		{
			$backdropPathEpisode = $tmdb->getImageUrl($t->backdrop_path, TMDB::IMAGE_BACKDROP, 'w1280', $titre);

			$donneesEpisode[] = '<div class="d-flex backdrop mb-5" style="background: no-repeat center/100% url(\''.$backdropPathEpisode.'\');"><a href="?type='.$type.'&id='.$id.'" class="row mx-auto"><div class="bg-backdrop">'.$titre.'</div></a></div>
				<h2 class="fs-3 liner mb-5"><a href="?type='.$type.'&id='.$id.'&saison">Détails des saisons et épisodes</a></h2>

				<div id="episodes">';

				$nombreTotalSaisons		= (int)(!empty($t->number_of_seasons) AND is_numeric($t->number_of_seasons))	? $t->number_of_seasons : 0;
				$nombreTotalEpisodes	= (int)(!empty($t->number_of_episodes) AND is_numeric($t->number_of_episodes))	? $t->number_of_episodes : 0;

				if($nombreTotalEpisodes >= 1)
				{
					foreach($t->seasons as $saisons => $s)
					{
						$episodes = $tmdb->getTvSeasons($id, $s->season_number);

						$nomSaison			= (!empty($s->name) AND $s->season_number == 0 AND $s->name == 'Épisodes spéciaux') ? 'Épisodes spéciaux' : 'Saison '.$s->season_number;
						$descSaison			= !empty($episodes->overview) ? '<span class="fw-bold me-2" data-bs-toggle="tooltip" data-bs-title="'.str_replace('"', '', nl2br($episodes->overview, false)).'"><i class="fa-solid fa-circle-info"></i></span>' : null;
						$nombreEpsSaison	= $s->episode_count;
						$numeroSaison		= $s->season_number;
						$iSaisonCollapse	= 'collapseS'.$numeroSaison;
						$iSaisonHead		= 'headingS'.$numeroSaison;

						if($nombreEpsSaison > 0)
						{
							$donneesEpisode[] = '<div class="accordion '.($numeroSaison == $nombreTotalSaisons ? 'mb-0' : 'mb-3').'" id="accordionSaison'.$numeroSaison.'">
								<div class="accordion-item">
									<h2 class="accordion-header" id="'.$iSaisonHead.'">
										<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#'.$iSaisonCollapse.'" aria-expanded="false" aria-controls="'.$iSaisonCollapse.'">
											'.$descSaison.'
											<span>'.$nomSaison.'</span>
											<span class="mx-2 mx-lg-3">—</span>
											<span>'.$nombreEpsSaison.' épisode'.s($nombreEpsSaison).'</span>
										</button>
									</h2>
									<div class="accordion-collapse collapse" id="'.$iSaisonCollapse.'" aria-labelledby="'.$iSaisonHead.'" data-bs-parent="#accordionSaison'.$numeroSaison.'">
										<div class="accordion-body">
											<div class="accordion" id="accordion'.$numeroSaison.'">
												'.($numeroSaison > 0 ? '<div class="accordion-item accordion-item mb-2 p-3 border border-bv rounded">
													<h2 class="accordion-header text-center"><a href="?type=serie&id='.$id.'&fullCast&saison='.$numeroSaison.'" data-bs-toggle="tooltip" data-bs-title="Afficher tous les rôles et l’équipe technique de la saison '.$numeroSaison.' de '.$titre.'">Distribution des rôles et équipe technique de la saison '.$numeroSaison.'</a></h2>
												</div>' : null);

												foreach($episodes->episodes as $eps_infos => $v)
												{
													$showIdEpisode		= (int) $v->show_id;
													$idEpisode			= (int) $v->id;
													$numeroEpSaison		= (int) $v->season_number;
													$numeroEpisode		= (int) $v->episode_number;
													$dateInfos			= dateInfos($v->air_date);
													$timestamp			= !empty($dateInfos['timestamp'])	? $dateInfos['timestamp']							: null;
													$date				= !empty($dateInfos['date'])		? $dateInfos['date']								: null;
													$titreEpisode		= (string) !empty($v->name)			? $v->name											: 'nom de l’épisode inconnu';
													$imageEpisode		= $tmdb->getImageUrl($v->still_path, TMDB::IMAGE_BACKDROP, 'w1280', $titreEpisode);
													$synopsisEpisode	= (string) !empty($v->overview)		? $v->overview										: 'description de l’épisode inconnue';
													$codeProdEpisode	= !empty($v->production_code)		? (string) $v->production_code						: null;
													$dureeEpisode		= !empty($v->runtime)				? (string) minsEnHrs($v->runtime)					: null;
													$voteMoyen			= !empty($v->vote_average)			? (string) round($v->vote_average, 1).' / 10'		: null;
													$voteTotal			= (string) !empty($v->vote_count)	? $v->vote_count.' vote'.s($v->vote_count)			: 'n/a';
													$iEpisodeCollapse	= 'collapseId'.$idEpisode;
													$iEpisodeHead		= 'headingId'.$idEpisode;
													$bgCSS				= 'episode-'.$idEpisode;

													$donneesEpisode[] = '<style>.'.$bgCSS.':before { background-image: url(\''.$imageEpisode.'\'); background-size: cover; background-position: center; background-repeat: no-repeat; }</style>

													<div class="accordion-item '.($numeroEpisode == $nombreEpsSaison ? 'mb-0' : 'mb-2').'">
														<h2 class="accordion-header" id="'.$iEpisodeHead.'">
															<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#'.$iEpisodeCollapse.'" aria-expanded="false" aria-controls="'.$iEpisodeCollapse.'">
																<span class="numero-episode" title="Numéro de l’épisode">Épisode '.$numeroEpisode.'</span>
																<span class="mx-2">—</span>
																<span class="text-dark-emphasis text-truncate" title="Nom de l’épisode">'.$titreEpisode.'</span>
															</button>
														</h2>

														<div class="accordion-collapse collapse" id="'.$iEpisodeCollapse.'" aria-labelledby="'.$iEpisodeHead.'" data-bs-parent="#accordion'.$numeroEpSaison.'">
															<div class="accordion-body p-4 border border-bv border-top-0 rounded-bottom episode '.$bgCSS.'">
																<div class="infos mb-4">
																	<span class="text-white-50" title="Date de sortie de l’épisode">'.(!empty($timestamp) ? '<time datetime="'.date(DATE_ATOM, $timestamp).'">'.dateFormat($timestamp).'</time>' : 'Date de sortie de l’épisode inconnue').'</span>
																	'.(!empty($runtimeEpisode) ? '<span class="ms-4 text-white" title="Durée de l’épisode">Durée : '.$dureeEpisode.'min</span>' : null).'
																</div>

																<p class="mb-4 text-white" title="Description de l’épisode">'.$synopsisEpisode.'</p>

																<div class="infos text-center text-lg-start">
																	<a href="'.$imageEpisode.'" class="me-2 me-lg-3 text-white" data-fancybox="gallerie" title="Capture d’écran de l’épisode"><i class="fa-regular fa-image fa-2xl align-middle"></i></a>
																	<a href="https://www.themoviedb.org/tv/'.$id.'-'.slug($titre).'/season/'.$numeroEpSaison.'/episode/'.$numeroEpisode.'" class="me-2 me-lg-3"><span class="d-inline-block d-lg-none">'.logoTMDBMini(19).'</span><span class="d-none d-lg-inline-block">'.logoTMDB(19).'</span></a>
																	'.(!empty($voteMoyen) ? '<span class="badge bg-warning text-dark me-2 me-lg-3" title="Note de l’épisode">'.$voteMoyen.'</span>' : null).'
																	<span class="badge bg-warning text-dark me-2 me-lg-3" title="Nombre de vote de l’épisode">'.$voteTotal.'</span>
																	'.($numeroSaison > 0 ? '<a href="?type=serie&id='.$id.'&fullCast&saison='.$numeroSaison.'&episode='.$numeroEpisode.'" class="link-light badge bg-brain" title="Afficher tous les rôles et l’équipe technique pour l’épisode '.$numeroEpisode.' de la saison '.$numeroSaison.'"><span class="d-inline-block d-lg-none">Détails</span><span class="d-none d-lg-inline-block">Distribution des rôles et équipe technique de l’épisode '.$numeroEpisode.' de la saison '.$numeroEpSaison.'</span></a>' : null).'
																</div>
															</div>
														</div>
													</div>';
												}

											$donneesEpisode[] = '</div>
										</div>
									</div>
								</div>
							</div>';
						}
					}
				}

				else
					$donneesEpisode[] = alerte('danger', 'Aucune saison');

				$donneesEpisode[] = '<div class="mt-3 text-end"><button class="btn btn-outline-danger btn-sm" id="fermerTousAccordion">Fermer tous les accordéons</button></div>
			</div>';


			if(!empty($donneesEpisode))
			{
				function z(?string $str): ?string
				{
					if(!empty($str))
					{
						$str = str_ireplace('		',	'', $str);
						$str = str_ireplace("\t\t",		'', $str);
						$str = str_ireplace("\t",		'', $str);

						return (string) trim($str);
					}

					return null;
				}

				$donneesEpisode = array_map('z', $donneesEpisode);

				echo implode($donneesEpisode);

				cache($cacheEpisodes, implode($donneesEpisode));
			}
		}

		else
			echo (file_exists($cacheEpisodes) AND filesize($cacheEpisodes) > 0) ? file_get_contents($cacheEpisodes) : null;
	}

	// Exclusivités

	elseif(isset($_GET['exclus']))
	{
		echo '<h1 class="my-5"><a href="/projets/tmdb/exclus'.(isset($_GET['ygg']) ? '/ygg' : '/1337x').'">Exclusivités</a> '.(isset($_GET['ygg']) ? '<a href="https://www.yggtorrent.top/" class="text-decoration-underline link-offset-2" '.$onclick.'>YGG Torrent</a>' : '<a href="https://1337x.to/" class="text-decoration-underline link-offset-2" '.$onclick.'>1337x</a>').'</h1>

		<div class="row">
			<div class="col-6 text-center text-lg-end">
				<a href="/projets/tmdb/exclus/1337x" class="btn btn'.(!isset($_GET['1337x']) ? '-outline' : null).'-info"'.(isset($_GET['1337x']) ? ' id="service" data-service="1337x"' : null).'>1337x</a>
				<a href="/projets/tmdb/exclus/ygg" class="btn btn'.(isset($_GET['1337x']) ? '-outline' : null).'-info"'.(!isset($_GET['1337x']) ? ' id="service" data-service="ygg"' : null).'>YggTorrent</a>
			</div>
			<div class="col-6 text-center text-lg-start">
				<a href="https://1337x.to/" class="btn btn-outline-danger" '.$onclick.' title="Lien vers 1337x.to">1337x</a>
				<a href="https://www.yggtorrent.top/torrents/exclus" class="btn btn-outline-danger" '.$onclick.' title="Lien vers YggTorrent">YggTorrent</a>
			</div>';

			$get = isset($_GET['1337x']) ? get('https://1337x.to/popular-movies-week') : getYgg('https://www.yggtorrent.top/torrents/exclus');
			if(isset($_GET['1337x']))
			{
				if(!empty($get))
				{
					$get = strtr($get, ['mkv' => '', '...' => '', '+' => '', ' — ' => '', '[' => ' ', ']' => ' ', '(' => ' ', ')' => ' ', '_' => ' ', '  ' => '']);

					preg_match_all('/<tr>(.*)<td class="coll-1 name"><a href="(.*)" class="icon"><i class="flaticon-(.*)"><\/i><\/a><a href="\/torrent\/(?P<id>.*)\/(.*)\/">(?P<release>[^"]*?)<\/a>(?:<span class="comments"><i class="flaticon-message"><\/i>(.*)<\/span>)?<\/td>(.*)<td class="coll-2 seeds">(?P<seeders>.*)<\/td>(.*)<td class="coll-3 leeches">(?P<leechers>.*)<\/td>(.*)<td class="coll-date">(?P<timestamp>.*)<\/td>(.*)<td class="coll-4 size(.*)">(?P<size>.*)<span class="seeds">(.*)<\/span><\/td>(.*)<td class="coll-5(.*)"><a href="\/user\/(?P<uploader>.*)\/">(?P<name_uploader>.*)<\/a><\/td>/isU',
					$get,
					$m);

					for($i = 0; $i <= 50; $i++)
					{
						$exclusId			= !empty($m['id'][$i])						? secu($m['id'][$i])															: null;
						$exclusTime			= (int) !empty($m['timestamp'][$i])			? dateInfos(str_replace("'", '', $m['timestamp'][$i]))['timestamp']					: 0;
						$exclusTaille		= !empty($m['size'][$i])					? (string) clean($m['size'][$i])													: null;
						$exclusTaille		= !empty($exclusTaille)						? (string) istrtr($exclusTaille, ['tb' => ' To', 'gb' => ' Go', 'mb' => ' Mo'])		: 'n/a';
						$exclusSources		= !empty($m['seeders'][$i])					? secu($m['seeders'][$i])														: 'n/a';
						$exclusClients		= !empty($m['leechers'][$i])				? secu($m['leechers'][$i])													: 'n/a';
						$exclusComplets		= (string) 'n/a';
						$exclusSlugRls		= (string) !empty($m['release'][$i])		? slug($m['release'][$i])															: 'release';
						$exclusRls			= (string) !empty($m['release'][$i])		? parserRelease($m['release'][$i], $exclusTime)										: 'Release';
						$exclusRls			= (string) str_replace('<img src="/assets/img/drapeau-multi.png" style="height: 10px; width: 12px;" alt="MULTi FR / CA / EN" title="MULTi FR / CA / EN">', '🇺🇸', $exclusRls);
						$exclusRls			= (string) str_replace('<img src="/assets/img/drapeau-vostfr.png" style="height: 10px; width: 12px;" alt="VOSTFR" title="VOSTFR">', '🇺🇸', $exclusRls);
						$rls				= (string) !empty($m['release'][$i])		? secuChars($m['release'][$i])														: null;

						if(!empty($exclusId) AND !empty($m['release'][$i]))
						{
							$idHide = idAleatoire();

							$donnees[] = '<div class="row py-3 border-bottom text-center curseur exclusivites" id="'.$idHide.'">
								<div class="order-1		col-2	col-lg-1">'.(!empty($rls) ? '<a href="/projets/tmdb/?titreRelease='.urlencode($rls).'&formFilm=film&formSerie=serie&page=1" title="Chercher '.$rls.'" onclick="hide(\'#'.$idHide.'\');"><i class="fa-solid fa-magnifying-glass fa-xl"></i></a>' : null).'</div>
								<div class="order-first order-lg-2	col-12	col-lg-6 mb-3 mb-lg-0 text-start text-truncate"	title="Nom de la release"><a href="https://1337x.to/torrent/'.$exclusId.'/'.$exclusSlugRls.'/" onclick="hide(\'#'.$idHide.'\');" target="_blank">'.$exclusRls.'</a></div>
								<div class="order-2		col-3	col-lg-1"													title="Ajouté le '.dateFormat($exclusTime, 'c').'"><time datetime="'.dateFormat($exclusTime, 'DATE_ATOM').'">'.strtr(temps($exclusTime), ['heure' => 'hrs.', 'semaines' => 'sems.']).'</time></div>
								<div class="order-3		col-3	col-lg-1"													title="Taille du torrent">'.$exclusTaille.'</div>
								<div class="order-4		col-2	col-lg-1 text-success"										title="Sources du torrent">'.$exclusSources.'</div>
								<div class="order-5		col-2	col-lg-1 text-danger"										title="Clients du torrent">'.$exclusClients.'</div>
								<div class="order-last			col-lg-1 d-none d-lg-inline-block text-dark-emphasis"		title="Complétés">'.$exclusComplets.'</div>
							</div>';
						}
					}

					$donnees = '<div class="mt-5">'.implode($donnees).'</div>';
				}

				else
					$donnees = alerte('danger', 'Erreur de chargement du site distant');
			}

			else
			{
				if(!empty($get))
				{
					preg_match_all('/<td style="text-align: left;"><a(.*)href="(?P<lien>.*)">(?P<release>.*)<\/td>(.*)<td><a target="(?P<id>.*)" id="get_nfo"><img(.*)src="https:\/\/www\.ygg\.re\/assets\/img\/nfo\.gif"><\/td>(.*)<td>(?P<comments>.*)<span class="ico_comment"><\/span>(.*)<\/td>(.*)<td>(.*)<div class="hidden">(?P<timestamp>.*)<\/div><span class="ico_clock-o"><\/span>(.*)<\/td>(.*)<td>(?P<size>.*)<\/td>(.*)<td>(?P<complete>.*)<\/td>(.*)<td>(?P<seeders>.*)<\/td>(.*)<td>(?P<leechers>.*)<\/td>/isU',
					$get, $m);

					for($i = 0; $i <= 24; $i++)
					{
						$exclusId		= !empty($m['id'][$i])					? (int) $m['id'][$i]									: null;
						$exclusTime		= !empty($m['timestamp'][$i])			? (int) (secu($m['timestamp'][$i]) + 1000)				: 0;
						$exclusTime		= (int) !empty($m['timestamp'][$i])		? (dateInfos($m['timestamp'][$i])['timestamp'] + 1000)	: 0;
						$exclusTaille	= (string) !empty($m['size'][$i])		? clean($m['size'][$i])									: 'n/a';
						$exclusSources	= !empty($m['seeders'][$i])				? secu($m['seeders'][$i])							: 'n/a';
						$exclusClients	= !empty($m['leechers'][$i])			? secu($m['leechers'][$i])						: 'n/a';
						$exclusComplets	= !empty($m['complete'][$i])			? secu($m['complete'][$i])						: 'n/a';
						$exclusRls		= (string) !empty($m['release'][$i])	? parserRelease($m['release'][$i], $exclusTime)			: 'Release';

						if(!empty($exclusId) AND !empty($exclusRls))
						{
							preg_match($regexNomFilm, $m['release'][$i], $regexRechercheFilm);
							$titreFilm = strtr($regexRechercheFilm[1], $arrayClean);

							$idHide = idAleatoire();

							$donnees[] = '<div class="row py-3 border-bottom text-center curseur exclusivites" id="'.$idHide.'">
								<div class="order-1 				col-2 col-lg-1"><a href="https://www.yggtorrent.top/engine/download_torrent?id='.$exclusId.'" title="Télécharger le torrent" onclick="hide(\'#'.$idHide.'\');"><i class="fas fa-download fa-xl me-2"></i></a>'.(!empty($titreFilm) ? '<a href="/projets/tmdb/?title='.urlencode($titreFilm).'&formFilm=film&formSerie=serie&page=1" title="Chercher '.$titreFilm.'" onclick="hide(\'#'.$idHide.'\');"><i class="fa-solid fa-magnifying-glass fa-xl"></i></a>' : null).'</div>
								<div class="order-first order-lg-2	col-12 col-lg-6 mb-3 mb-lg-0 text-start"	title="Nom de la release"><a href="https://www.yggtorrent.top/torrent/filmvid%C3%A9o/film/'.$exclusId.'-'.slug($exclusRls).'" '.$onclick.'>'.$exclusRls.'</a></div>
								<div class="order-2					col-2 col-lg-1"								title="Ajouté le '.dateFormat($exclusTime, 'c').'">'.strtr(temps($exclusTime), ['il y a' => '', 'semaines' => 'sems.']).'</div>
								<div class="order-3					col-2 col-lg-1"								title="Taille du torrent">'.$exclusTaille.'</div>
								<div class="order-4					col-2 col-lg-1 text-success"				title="Sources du torrent">'.$exclusSources.'</div>
								<div class="order-5					col-2 col-lg-1 text-danger"					title="Clients du torrent">'.$exclusClients.'</div>
								<div class="order-last				col-2 col-lg-1 text-dark-emphasis"			title="Complétés">'.$exclusComplets.'</div>
							</div>';
						}

						else
							goto erreurAucuneExclus;
					}

					$donnees = '<div class="mt-5">'.implode($donnees).'</div>';
				}

				else
				{
					erreurAucuneExclus:

					$donnees = alerte('danger', 'Erreur de chargement du site distant');
				}
			}

			echo trim($donnees).'
		</div>';
	}

	// Les Genres

	elseif(isset($_GET['genres']) AND empty($_GET['genres']))
	{
		echo '<h1 class="mb-4 mt-5 liner"><a href="/projets/tmdb/genres"><i class="fa-solid fa-film"></i> Les Genres des Films</a></h1>

		<div class="d-flex flex-wrap gap-3 fs-4">';

			foreach($tmdb->getMovieGenres()->genres as $g)
			{
				$genresFilmsArray[] = '<a href="/projets/tmdb/films/genre/'.$g->id.'-'.slug($g->name).'">'.genresFr($g->name).'</a>';
			}

			echo implode(' - ', $genresFilmsArray);

		echo '</div>

		<h1 class="mb-4 mt-5 liner"><a href="/projets/tmdb/genres"><i class="fa-solid fa-tv"></i> Les Genres des Séries</a></h1>

		<div class="d-flex flex-wrap gap-3 fs-4">';

			foreach($tmdb->getTvGenres()->genres as $g)
			{
				$genresSeriesArray[] = '<a href="/projets/tmdb/series/genre/'.$g->id.'-'.slug($g->name).'">'.genresFr($g->name).'</a>';
			}

			echo implode(' - ', $genresSeriesArray);

		echo '</div>';
	}

	// Les Pays

	elseif(isset($_GET['origines']) AND empty($_GET['origines']))
	{
		echo '<h1 class="my-5 liner"><a href="/projets/tmdb/films/origines"><i class="fa-solid fa-earth-americas"></i> Les Pays</a></h1>

		<div class="d-flex flex-wrap gap-3 fs-6">';

			foreach($tmdb->getConfigurationCountries() as $pays)
			{
				$paysArray[] = '<a href="/projets/tmdb/'.$type.'/origine/'.strtolower($pays->iso_3166_1).'">'.isoEmoji($pays->iso_3166_1).' '.paysFr($pays->native_name).'</a>';
			}

			echo implode(' - ', $paysArray);

			echo '<div class="text-center mt-5">
				<a href="https://upload.wikimedia.org/wikipedia/commons/3/3d/Flag-map_of_the_world_%282017%29.png" data-fancybox="gallerie">
					<img src="https://upload.wikimedia.org/wikipedia/commons/3/3d/Flag-map_of_the_world_%282017%29.png" class="img-fluid rounded" alt="Carte des drapeaux" title="Carte des drapeaux">
				</a>
			</div>
		</div>';
	}

	// Hash 2 Magnet

	elseif(isset($_GET['hash']))
	{
		if(isset($_POST['hashPost']) OR isset($_POST['url']))
		{
			if(isset($_POST['tracker']) AND !empty($_POST['tracker']) AND in_array($_POST['tracker'] ?? '', ['abn', 'ygg']))
			{
				if(!empty($_POST['hashPost']))
				{
					if(preg_match('/http/is', $_POST['hashPost']) AND preg_match('/ygg/is', parse_url($_POST['hashPost'])['host']))
					{
						$yggGet = getYgg($_POST['hashPost']);

						if(!empty($yggGet))
						{
							preg_match_all('/<h1>(?P<yggRls>.*)<\/h1>(.*)<td>Seeders<\/td>(.*)<td><strong class="green">(?P<yggSeeders>.*)<\/strong><\/td>(.*)<td id="subcat_item" class="adv_search_option"(.*)style="">Leechers<\/td>(.*)<td id="subcat_item" style="">(.*)<strong class="red">(?P<yggLeechers>.*)<\/strong>(.*)<\/td>(.*)<td id="subcat_item" class="adv_search_option"(.*)style="">Complétés<\/td>(.*)<td id="subcat_item" style="">(.*)<strong>(?P<yggCompletes>.*)<\/strong>(.*)<td>Info Hash<\/td>(.*)<td>(?P<yggHash>([0-9-a-f]{40}))<\/td>/isU',
							$yggGet,
							$m);

							$rlsGet			= !empty($m['yggRls'][0])												? trim(str_replace('Exclusivité', '', str_replace('.', ' ', clean($m['yggRls'][0]))))	: null;
							$seedersGet		= intval((!empty($m['yggSeeders'][0]) AND $m['yggSeeders'][0] > 0)		? clean(str_replace(' ', '', $m['yggSeeders'][0]))										: 0);
							$leechersGet	= intval((!empty($m['yggLeechers'][0]) AND $m['yggLeechers'][0] > 0)	? clean(str_replace(' ', '', $m['yggLeechers'][0]))									: 0);
							$completesGet	= intval((!empty($m['yggCompletes'][0]) AND $m['yggCompletes'][0] > 0)	? clean(str_replace(' ', '', $m['yggCompletes'][0]))									: 0);
							$hashGet		= !empty($m['yggHash'][0])												? clean($m['yggHash'][0])																: null;

							if(!empty($rlsGet) AND (isset($seedersGet) AND $seedersGet !== '') AND (isset($leechersGet) AND $leechersGet !== '') AND (isset($completesGet) AND $completesGet !== '') AND !empty($hashGet))
							{
								$hash = '<style>
								.card			{ border-color: rgba(214,104,83, 1); }
								.card-header	{ background-color: rgba(214,104,83, 1); }
								h1				{ color: rgba(51,51,51, 1); }
								</style>

								<div class="row mt-5">
									<div class="col-12 col-lg-6 mx-auto">
										<div class="card text-center">
											<div class="card-header"><p class="fs-6 my-2"><a href="'.secuChars(mb_strtolower($_POST['hashPost'])).'" style="color: rgba(51,51,51, 1);" '.$onclick.'>'.$rlsGet.'</a></p></div>
											<div class="card-body">
												<p class="card-text">
													<span class="badge text-bg-success fs-6 px-2 py-1" title="Sources du torrent">'.$seedersGet.'</span>
													<span class="badge text-bg-danger fs-6 px-2 py-1" title="Clients du torrent">'.$leechersGet.'</span>
													<span class="badge text-bg-dark fs-6 px-2 py-1" title="Torrents complétés">'.$completesGet.'</span>
												</p>
												<p class="card-text fs-6" title="Hash du Torrent"><kbd class="p-2">'.secuChars(mb_strtolower($hashGet)).'</kbd></p>
											</div>
										</div>
									</div>
								</div>';
							}
						}

						else
							$hash = alerte('danger', 'Erreur de chargement du site distant');
					}

					else
					{
						if(isSha1($_POST['hashPost']))	$hashGet = secuChars($_POST['hashPost']);
						else							$hash = alerte('danger', 'Hash sha1 du magnet est incorrect');
					}
				}

				else
					$hash = alerte('danger', 'Le champs est vide');
			}

			else
				$hash = alerte('danger', 'Le champs du tracker est incorrect');
		}

		echo '<h1 class="mb-5"><a href="/projets/tmdb/hash">Hash 2 Magnet</a></h1>

		<form method="post" id="magnet-form">
			<div class="row">
				<div class="col-12 col-lg-6 mx-auto">
					<div class="mb-2">
						<input type="radio" class="btn-check" name="tracker" value="ygg" id="option-ygg" '.(((!empty($_POST['tracker']) AND $_POST['tracker'] == 'ygg') OR empty($_POST['tracker'])) ? ' checked' : null).'>
						<label class="btn btn-outline-brain" for="option-ygg">YGG</label>

						<input type="radio" class="btn-check" name="tracker" value="abn" id="option-abn" '.((!empty($_POST['tracker']) AND $_POST['tracker'] == 'abn') ? ' checked' : null).'>
						<label class="btn btn-outline-brain" for="option-abn">ABN</label>
					</div>

					<div class="input-group">
						<input type="text" name="hashPost" class="form-control form-control-lg" '.(!empty($_POST['hashPost']) ? 'value="'.secuChars(mb_strtolower($_POST['hashPost'])).'"' : null).' placeholder="Hash" id="inputHash" required>
						<input type="submit" value="Valider" class="btn btn-outline-brain" form="magnet-form">
					</div>
				</div>
			</div>
		</form>

		'.((!empty($hashGet) AND isSha1($hashGet)) ? '<div class="row text-center g-0">
			<div class="col-12 col-lg-6 mt-5 mx-auto">
				<button style="width: 250px;" class="btn btn-outline-success btn-copie fs-5 p-3" data-type="magnet" data-bs-toggle="tooltip" data-bs-title="Copier le magnet" data-clipboard-text="'.setMagnet($hashGet, (in_array($_POST['tracker'], ['abn', 'ygg']) ? secuChars($_POST['tracker']) : 'ygg')).'"><i class="fa-solid fa-magnet fa-1x me-2"></i> Copier le magnet</button>
			</div>

			<div class="col-12 col-lg-8 mt-5 mx-auto">
				<a href="https://s19.easy-tk.biz/rutorrent/" style="width: 250px;" class="btn btn-outline-brain fs-5 p-3" '.$onclick.'>Seedbox</a>
			</div>
		</div>' : null);

		echo !empty($hash) ? $hash : null;
	}

	// Seedbox

	elseif(isset($_GET['seedbox']))
	{
		if(isset($_GET['titreAZ']))		$nomCache = 'titreaz';
		elseif(isset($_GET['titreZA']))	$nomCache = 'titreza';
		else							$nomCache = 'date';

		$ftpConnexion = ftp_connect('s19.easy-tk.biz', 3339);
		if(!$ftpConnexion) {
			die('Impossible de se connecter au serveur FTP');
		}

		if(!ftp_login($ftpConnexion, 'easy206', 'hjLrjC6uShHdXqX')) {
			ftp_close($ftpConnexion);
			die('Échec de l’authentification FTP');
		}

		ftp_pasv($ftpConnexion, true);

		if(!empty($_POST['titre']))
		{
			$fichier = '/Films/'.trim($_POST['titre']);

			if(!ftp_login($ftpConnexion, 'easy206', 'hjLrjC6uShHdXqX')) {
				ftp_close($ftpConnexion);
				die('Échec de l’authentification FTP');
			}

			ftp_pasv($ftpConnexion, true);

			if(ftp_size($ftpConnexion, $fichier) != -1)
				echo ftp_delete($ftpConnexion, $fichier) ? alerte('success', 'Le fichier <span class="fw-bold">'.$fichier.'</span> a été supprimé') : alerte('danger', 'Impossible de supprimer <span class="fw-bold">'.$fichier.'</span>');

			else
				echo alerte('danger', 'Le fichier <span class="fw-bold">'.$fichier.'</span> n’existe pas');
		}

		$listeFichiers = ftp_nlist($ftpConnexion, '/Films');
		if($listeFichiers === false)
			$listeFichiers = [];

		$liste = [];
		foreach($listeFichiers as $e)
		{
			$base = basename($e);

			if($base === '.' || $base === '..') { continue; }

			$remote = $e;
			if(strpos($e, '/') === false)
				$remote = rtrim($path, '/') . '/' . $e;

			$t = ftp_mdtm($ftpConnexion, $remote);

			if($t === -1)
				$t = 0;

			$liste[] = ['name' => $base, 'remote' => $remote, 'mtime' => $t];
		}

		if(isset($_GET['titreAZ']) OR isset($_GET['titreZA']))
		{
			$parAZ = $liste;

			usort($parAZ, function(array $a, array $b) {
				return strnatcasecmp($a['name'], $b['name']);
			});

			$listeFichiers = array_column($parAZ, 'name');

			if(isset($_GET['titreZA']))
			{
				$parZA = array_reverse($parAZ);
				$listeFichiers = array_column($parZA, 'name');
			}
		}

		else
		{
			$parDate = $liste;
			usort($parDate, function(array $a, array $b) {
				return $b['mtime'] <=> $a['mtime'];
			});
			$listeFichiers = array_column($parDate, 'name');
		}

		ftp_close($ftpConnexion);

		if(!empty($listeFichiers))
		{
			foreach($listeFichiers as $fichier)
			{
				if(preg_match('/\b(19[0-9][0-9]|20[0-1][0-9]|202[0-5])\b/is', $fichier))
				{
					preg_match('/(.*)\b(19[0-9]{2}|20[0-1][0-9]|202[0-5])\b(.*)/is', $fichier, $m);
					$titre = str_replace('/files/Films/', '', trim($m[1]));
					$id = idAleatoire();

					$fichiersFtp[] = '<div class="cadre-seedbox" id="'.$id.'">
						<a href="/projets/tmdb/?titre='.urlencode($titre).'&formFilm=film" class="border border-bv rounded p-2" onclick="hide(\'#'.$id.'\');">'.$titre.'</a>

						<form action="/projets/tmdb/seedbox" method="post" class="" onsubmit="return confirm(\'Es-tu sûr de vouloir supprimer ce fichier ?\');">
							<input type="hidden" name="titre" value="'.$fichier.'">
							<button type="submit" class="btn btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
						</form>
					</div>';
				}
			}
		}

		else
			echo alerte('danger', 'La liste des fichiers ne peut être récupée');


		echo '<h1 class="mb-5"><a href="/projets/tmdb/seedbox">Seedbox</a></h1>

		<div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
			<a href="?date" class="btn btn-'.((!isset($_GET['titreAZ']) AND !isset($_GET['titreZA'])) ? 'outline-' : null).'brain">Date</a>
			<a href="?titreAZ" class="btn btn-'.(isset($_GET['titreAZ']) ? 'outline-' : null).'brain">Titre (A - Z)</a>
			<a href="?titreZA" class="btn btn-'.(isset($_GET['titreZA']) ? 'outline-' : null).'brain">Titre (Z - A)</a>
		</div>

		<div class="d-flex flex-wrap gap-2">';

			echo !empty($fichiersFtp) ? implode($fichiersFtp) : null;

		echo '</div>';
	}

	// Note le !

	elseif(isset($_GET['note-le']))
	{
		$titre = !empty($_POST['titre']) ? secuChars($_POST['titre']) : null;

		echo '<h1 class="mb-5"><a href="/projets/tmdb/note-le">Note le !</a></h1>

		<form method="post" id="note-le-form">
			<div class="row">
				<div class="col-12 col-lg-6 mx-auto">
					<div class="input-group">
						<input type="text" name="titre"'.(!empty($titre) ? ' value="'.secuChars($titre).'" ' : null).' class="form-control form-control-lg" placeholder="Je cherche…" autocomplete="off" '.(empty($titre) ? 'autofocus' : null).' required '.$onclickSelect.'>
						<input type="submit" value="Valider" class="btn btn-outline-brain" form="note-le-form">
					</div>
				</div>
			</div>
		</form>';

		if(isset($_POST['titre']))
		{
			echo '<div class="text-center col-12 col-lg-6 mx-auto mt-5">
				<div id="lien-note-le-imdb"><a href="https://www.imdb.com/find/?q='.$titre.'"						class="d-block rounded mb-3 p-3 fs-3"	onclick="hide(\'#lien-note-le-imdb\')"			target="_blank"	>IMDb</a></div>
				<div id="lien-note-le-allocine"><a href="https://www.allocine.fr/rechercher/?q='.$titre.'"			class="d-block rounded mb-3 p-3 fs-3"	onclick="hide(\'#lien-note-le-allocine\')"		target="_blank	">AlloCiné</a></div>
				<div id="lien-note-le-betaseries"><a href="https://www.betaseries.com/films/texte-'.$titre.'"		class="d-block rounded mb-3 p-3 fs-3"	onclick="hide(\'#lien-note-le-betaseries\')"	target="_blank"	>BetaSeries</a></div>
				<div id="lien-note-le-criticker"><a href="https://www.criticker.com/?search='.$titre.'&type=films"	class="d-block rounded mb-3 p-3 fs-3"	onclick="hide(\'#lien-note-le-criticker\')"		target="_blank"	>Criticker</a></div>
				<div id="lien-note-le-senscritique"><a href="https://www.senscritique.com/search?query='.$titre.'"	class="d-block rounded mb-3 p-3 fs-3"	onclick="hide(\'#lien-note-le-senscritique\')"	target="_blank"	>SensCritique</a></div>
				<div id="lien-note-le-trakttv"><a href="https://trakt.tv/search?query='.$titre.'"					class="d-block rounded mb-3 p-3 fs-3"	onclick="hide(\'#lien-note-le-trakttv\')"		target="_blank">Trakt.tv</a></div>
				<div id="lien-note-le-tvtime"><a href="https://app.tvtime.com/explore/search?q='.$titre.'"			class="d-block rounded mb-3 p-3 fs-3"	onclick="hide(\'#lien-note-le-tvtime\')"		>TV Time</a></div>
			</div>';
		}
	}

	// Populaires

	elseif((isset($_GET['films']) OR isset($_GET['series'])) AND (isset($_GET['populaires']) OR isset($_GET['tendances']) OR isset($_GET['notes'])))
	{
		$btnFilmsPopulaires = (isset($_GET['films']) AND isset($_GET['populaires'])) ? ' Populaires' : null;
		$btnFilmsTendances = (isset($_GET['films']) AND isset($_GET['tendances'])) ? ' Tendances' : null;
		$btnFilmsNotes = (isset($_GET['films']) AND isset($_GET['notes'])) ? ' Mieux Notés' : null;

		$btnSeriesPopulaires = (isset($_GET['series']) AND isset($_GET['populaires'])) ? ' Populaires' : null;
		$btnSeriesTendances = (isset($_GET['series']) AND isset($_GET['tendances'])) ? ' Tendances' : null;
		$btnSeriesNotes = (isset($_GET['series']) AND isset($_GET['notes'])) ? ' Mieux Notées' : null;

		$type = isset($_GET['films']) ? 'films' : 'series';

		if(isset($_GET['populaires']))		$typeCategorie = 'populaires';
		elseif(isset($_GET['tendances']))	$typeCategorie = 'tendances';
		elseif(isset($_GET['notes']))		$typeCategorie = 'notes';
		else								$typeCategorie = 'populaires';

		$tendancesDate = isset($_GET['tendances']) ? (isset($_GET['day']) ? 'day' : 'week') : null;

		$url = '/projets/tmdb/'.$type.'/'.$typeCategorie;

		$dropdown = '<div class="container mt-5">
			<div class="row justify-content-center">
				<div class="col-auto">
					<div class="dropdown dropdown-hover">
						<button type="button" style="min-width: 180px;" class="btn btn-'.(isset($_GET['series']) ? 'outline-' : null).'brain dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">Films'.$btnFilmsPopulaires.$btnFilmsTendances.$btnFilmsNotes.'</button>
						<ul class="dropdown-menu">
							<li><a href="/projets/tmdb/films/populaires" class="dropdown-item'.((isset($_GET['films']) AND isset($_GET['populaires'])) ? ' active' : null).'">Populaires'.((isset($_GET['films']) AND isset($_GET['populaires'])) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>
							<li><a href="/projets/tmdb/films/tendances" class="dropdown-item'.((isset($_GET['films']) AND isset($_GET['tendances'])) ? ' active' : null).'">Tendances'.((isset($_GET['films']) AND isset($_GET['tendances'])) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>
							<li><a href="/projets/tmdb/films/notes" class="dropdown-item'.((isset($_GET['films']) AND isset($_GET['notes'])) ? ' active' : null).'">Mieux notés'.((isset($_GET['films']) AND isset($_GET['notes'])) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>
						</ul>
					</div>
				</div>

				<div class="col-auto">
					<div class="dropdown dropdown-hover">
						<button type="button" style="min-width: 180px;" class="btn btn-'.(isset($_GET['films']) ? 'outline-' : null).'brain dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">Séries'.$btnSeriesPopulaires.$btnSeriesTendances.$btnSeriesNotes.'</button>
						<ul class="dropdown-menu">
							<li><a href="/projets/tmdb/series/populaires" class="dropdown-item'.((isset($_GET['series']) AND isset($_GET['populaires'])) ? ' active' : null).'">Populaires'.((isset($_GET['series']) AND isset($_GET['populaires'])) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>
							<li><a href="/projets/tmdb/series/tendances" class="dropdown-item'.((isset($_GET['series']) AND isset($_GET['tendances'])) ? ' active' : null).'">Tendances'.((isset($_GET['series']) AND isset($_GET['tendances'])) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>
							<li><a href="/projets/tmdb/series/notes" class="dropdown-item'.((isset($_GET['series']) AND isset($_GET['notes'])) ? ' active' : null).'">Mieux notées'.((isset($_GET['series']) AND isset($_GET['notes'])) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>';

		if(isset($_GET['tendances']) OR isset($_GET['notes']))
		{
			$dropdown .= '<div class="container mt-4">
				<div class="row justify-content-center">';

					if(isset($_GET['tendances']))
					{
						$dropdown .= '<div class="col-auto">
							<a href="'.$url.'?day" class="btn btn-'.($tendancesDate === 'week' ? 'outline-' : null).'brain">Du jour</a> <a href="'.$url.'?week" class="btn btn-'.($tendancesDate === 'day' ? 'outline-' : null).'brain">De la semaine</a>
						</div>';
					}

					elseif(isset($_GET['notes']))
					{
						$dropdown .= '<div class="col-auto">
							<div class="dropdown">
								<button type="button" style="min-width: 180px;" class="btn btn-outline-brain dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">'.(!empty($anneeRecherche) ? $anneeRecherche : 'Année de sortie').'</button>
								<ul class="dropdown-menu scroll">
									<li><a href="'.$url.'" class="dropdown-item'.(empty($anneeRecherche) ? ' active' : null).'">Toutes les années'.(empty($anneeRecherche) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>
									<li><a class="dropdown-item disabled" aria-disabled="true"><hr class="my-1"></a></li>';

									for($a = 2025; $a > 1900; $a--)
									{
										$dropdown .= '<li><a href="'.$url.'?anneeRecherche='.$a.'" class="dropdown-item '.($a === $anneeRecherche ? 'active' : null).'">'.$a.((!empty($a) AND $a === $anneeRecherche) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>';
									}

								$dropdown .= '</ul>
							</div>
						</div>
						<div class="col-auto">
							<a href="'.$url.'" class="btn btn-danger ms-0 ms-lg-2" title="Réinitialiser les filtres"><i class="fas fa-times fa-1x"></i></a>
						</div>';
					}

				$dropdown .= '</div>
			</div>';
		}

		function tmdbPopulairesTendancesNotes(object $tmdb, string $type, int $page, string $tempsCache, string $typeCategorie, ?string $tendancesDate = null, ?int $anneeRecherche = null)
		{
			if($type === 'film')
			{
				if($typeCategorie === 'populaires')		{ $typeFiche = 'film'; $tmddRecherche = $tmdb->getMoviePopular(page: $page); }
				elseif($typeCategorie === 'tendances')	{ $typeFiche = 'film'; $tmddRecherche = $tmdb->getTrendingMovie($tendancesDate); }
				elseif($typeCategorie === 'notes')		{ $typeFiche = 'film'; $tmddRecherche = $tmdb->getMovieDiscover(certification_country: 'FR', language: 'fr-FR', page: $page, primary_release_year: $anneeRecherche, sort_by: 'vote_count.desc', with_runtimegte: 60, with_runtimelte: 400); }
			}

			elseif($type === 'serie')
			{
				if($typeCategorie === 'populaires')		{ $typeFiche = 'serie'; $tmddRecherche = $tmdb->getTvPopular(page: $page); }
				elseif($typeCategorie === 'tendances')	{ $typeFiche = 'serie'; $tmddRecherche = $tmdb->getTrendingTv($tendancesDate); }
				elseif($typeCategorie === 'notes')		{ $typeFiche = 'serie'; $tmddRecherche = $tmdb->getTvDiscover(first_air_date_year: $anneeRecherche, language: 'fr-FR', page: $page, sort_by: 'vote_count.desc', vote_averagegte: 0, vote_averagelte: 10, vote_countgte: 200, watch_region: 'FR', with_runtimegte: 40, with_runtimelte: 400); }
			}

			$cache = $_SERVER['DOCUMENT_ROOT'].'assets/cache/tmdb/'.$type.'s_'.$typeCategorie.'_page_'.$page.(isset($_GET['tendances']) ? (isset($_GET['day']) ? '_day' : '_week') : null).(!empty($anneeRecherche) ? '_annee_'.$anneeRecherche : null).'.cache';
			if(!file_exists($cache) OR (filemtime($cache) < strtotime($tempsCache)))
			{
				$donnees[] = '<div class="container mt-5 films-series-populaires">
					<div class="row gap-3">';

					if(!empty($tmddRecherche->results))
					{
						foreach($tmddRecherche->results as $c => $v)
						{
							if($type === 'film')
							{
								$titre	= !empty($v->title)				? secuChars($v->title)			: 'titre inconnu';
								$date	= !empty($v->release_date)		? strtotime($v->release_date)	: null;
							}

							elseif($type === 'serie')
							{
								$titre	= !empty($v->name)				? secuChars($v->name)			: 'nom inconnu';
								$date	= !empty($v->first_air_date)	? strtotime($v->first_air_date)	: null;
							}

							$id			= !empty($v->id)				? secu($v->id)					: null;
							$posterUrl	= $tmdb->getImageUrl($v->poster_path, TMDB::IMAGE_POSTER, 'w185', $titre);

							$donnees[] = '<div class="cadre-films-series">
								<div>
									<a href="/projets/tmdb/?type='.$typeFiche.'&id='.$id.'">
										<img src="'.$posterUrl.'" class="img-fluid" alt="Poster de '.$titre.'" title="Poster de '.$titre.'">
									</a>
									<p class="my-3 px-1 text-center text-truncate fw-bold"><a href="/projets/tmdb/?type='.$typeFiche.'&id='.$id.'">'.$titre.'</a></p>
									<p class="mb-0 text-dark-emphasis text-center text-truncate" title="Date de sortie">'.(is_numeric($date) ? '<time datetime="'.date(DATE_ATOM, $date).'">'.dateFormat($date).'</time>' : 'date inconnue').'</p>
								</div>
							</div>';
						}
					}

					else
						$donnees[] = alerte('danger', 'Aucun résultat', 'mx-auto col-12 col-lg-8');

					$donnees[] = '</div>
				</div>';

				$pagination = new Paginator(1000, 20, $page, '?page=(:num)'.(isset($_GET['tendances']) ? '&'.$tendancesDate : null).((isset($_GET['notes']) AND !empty($anneeRecherche)) ? '&anneeRecherche='.$anneeRecherche : null));
				$donnees[] = '<nav class="my-5" id="pagination" aria-label="Pagination">'.$pagination.'</nav>';

				if(!empty($donnees))
				{
					echo implode($donnees);

					cache($cache, implode($donnees));
				}
			}

			else
				echo (file_exists($cache) AND filesize($cache) > 0) ? file_get_contents($cache) : null;
		}

		if(isset($_GET['films']))
		{
			if(isset($_GET['populaires']))		{ echo '<h1 class="mb-0 mt-5"><a href="/projets/tmdb/films/populaires">Films Populaires</a></h1>'; echo $dropdown; echo tmdbPopulairesTendancesNotes($tmdb, 'film', $page, $cacheTPopulaires, 'populaires'); }
			elseif(isset($_GET['tendances']))	{ echo '<h1 class="mb-0 mt-5"><a href="/projets/tmdb/films/tendances">Films Tendances</a></h1>'; echo $dropdown; echo tmdbPopulairesTendancesNotes($tmdb, 'film', $page, $cacheTPopulaires, 'tendances', $tendancesDate); }
			elseif(isset($_GET['notes']))		{ echo '<h1 class="mb-0 mt-5"><a href="/projets/tmdb/films/notes">Films mieux notés</a></h1>'; echo $dropdown; echo tmdbPopulairesTendancesNotes($tmdb, 'film', $page, $cacheTPopulaires, 'notes', 	null, $anneeRecherche); }
		}

		elseif(isset($_GET['series']))
		{
			if(isset($_GET['populaires']))		{ echo '<h1 class="mb-0 mt-5"><a href="/projets/tmdb/series/populaires">Séries Populaires</a></h1>'; echo $dropdown; echo tmdbPopulairesTendancesNotes($tmdb, 'serie', $page, $cacheTPopulaires, 'populaires'); }
			elseif(isset($_GET['tendances']))	{ echo '<h1 class="mb-0 mt-5"><a href="/projets/tmdb/series/tendances">Séries Tendances</a></h1>'; echo $dropdown; echo tmdbPopulairesTendancesNotes($tmdb, 'serie', $page, $cacheTPopulaires, 'tendances', $tendancesDate); }
			elseif(isset($_GET['notes']))		{ echo '<h1 class="mb-0 mt-5"><a href="/projets/tmdb/series/notes">Séries mieux notées</a></h1>'; echo $dropdown; echo tmdbPopulairesTendancesNotes($tmdb, 'serie', $page, $cacheTPopulaires, 'notes', null, $anneeRecherche); }
		}
	}

	// Recherche

	elseif(!empty($titreRecherche) AND (!empty($_GET['formFilm']) OR !empty($_GET['formSerie'])) OR (!empty($g_nom) OR !empty($motcle_nom) OR !empty($p_iso)))
	{
		// Filtres Recherche

		$formTitle = !empty($titreRecherche) ? '?titre='.secuChars(urlencode($titreRecherche)) : null;
		$formFilm = !empty($_GET['formFilm']) ? '&formFilm=film' : null;
		$formSerie = !empty($_GET['formSerie']) ? '&formSerie=serie' : null;
		$genreUrl = !empty($g_nom) ? $type.'/genre/'.$g_id.'-'.slug(strtolower($g_nom)) : null;
		$motCleUrl = !empty($motcle_id) ? $type.'/motcle/'.$motcle_id.'-'.slug(strtolower($motcle_nom)) : null;
		$origineUrl = !empty($p_iso) ? $type.'/origine/'.strtolower($p_iso) : null;

		$urlAnnee = '/projets/tmdb/'.$genreUrl.$motCleUrl.$origineUrl.$formTitle.$formFilm.$formSerie;

		echo '<form method="get">
			<div class="container mb-3 mt-4">
				<div class="row justify-content-center">
					'.(!empty($titreRecherche) ? '<input type="hidden" name="titre" value="'.$titreRecherche.'">' : null).'
					<input type="hidden" name="formFilm" value="film">

					<div class="col-auto">
						<div class="dropdown">
							<button type="button" style="min-width: 180px;" class="btn btn-outline-brain dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">'.(!empty($anneeRecherche) ? $anneeRecherche : 'Année de sortie').'</button>
							<ul class="dropdown-menu scroll">
								<li><a href="'.$urlAnnee.'" class="dropdown-item'.(empty($anneeRecherche) ? ' active' : null).'">Toutes les années'.(empty($anneeRecherche) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>
								<li><a class="dropdown-item disabled" aria-disabled="true"><hr class="my-1"></a></li>';

								for($a = 2025; $a > 1900; $a--)
								{
									echo '<li><a href="'.$urlAnnee.((empty($formFilm) AND empty($formSerie) AND empty($titreRecherche)) ? '?' : '&').'anneeRecherche='.$a.'" class="dropdown-item '.($a === $anneeRecherche ? 'active' : null).'">'.$a.((!empty($a) AND $a === $anneeRecherche) ? '<span class="float-end"><i class="fa-solid fa-check"></i></span>' : null).'</a></li>';
								}

							echo '</ul>
						</div>
					</div>
					<div class="col-auto">
						<a href="'.$formTitle.$formFilm.$formSerie.'" class="btn btn-danger ms-0 ms-lg-2" title="Réinitialiser les filtres"><i class="fas fa-times fa-1x"></i></a>
					</div>
				</div>
			</div>

			<input type="submit" class="d-none">
		</form>';

		// Films : Genres, Mots clés et Origines : getMovieDiscover()

		if($type === 'films' AND (!empty($genre_id) OR !empty($motcle_id) OR !empty($origine)))
		{
			$release_dategte = !empty($_GET['release_dategte']) ? $_GET['release_dategte'] : null;
			$release_datelte = !empty($_GET['release_dategte']) ? $_GET['release_dategte'] : null;

			$filmsFiltres = $tmdb->getMovieDiscover(
				language: 'fr-FR',
				page: $page,
				primary_release_year: $anneeRecherche,
				release_dategte: $release_dategte,
				release_datelte: $release_datelte,
				with_genres: $genre_id,
				with_keywords: $motcle_id,
				with_origin_country: $origine
			);

			$filmsResultats = !empty($filmsFiltres->results) ? $filmsFiltres->results : null;
			$filmsTotalPages = !empty($filmsFiltres->total_pages) ? $filmsFiltres->total_pages : null;
			$filmsResultatsTotaux = !empty($filmsFiltres->total_results) ? $filmsFiltres->total_results : null;

			$films = $filmsFiltres;
		}

		// Recherche Films : searchMovie()

		elseif(!empty($_GET['formFilm']) AND !empty($titreRecherche) AND empty($genre_id) AND empty($motcle_id) AND empty($origine))
		{
			$films = $tmdb->searchMovie(
				query: $titreRecherche,
				page: $page,
				year: $anneeRecherche,
			);

			$filmsResultats = !empty($films->results) ? $films->results : null;
			$filmsTotalPages = !empty($films->total_pages) ? $films->total_pages : null;
			$filmsResultatsTotaux = !empty($films->total_results) ? $films->total_results : null;
		}

		else
		{
			$filmsResultats = (object) ['results' => false];
			$filmsTotalPages = 0;
			$filmsResultatsTotaux = 0;
		}

		// Séries : Genres, Mots clés et Origines : getTvDiscover()

		if($type === 'series' AND (!empty($genre_id) OR !empty($motcle_id) OR !empty($origine)))
		{
			$first_air_dategte = !empty($_GET['first_air_datelte']) ? $_GET['first_air_datelte'] : null;
			$first_air_datelte = !empty($_GET['first_air_datelte']) ? $_GET['first_air_datelte'] : null;

			$seriesFiltres = $tmdb->getTvDiscover(
				language: 'fr-FR',
				page: $page,
				first_air_date_year: $anneeRecherche,
				first_air_dategte: $first_air_dategte,
				first_air_datelte: $first_air_datelte,
				with_genres: $genre_id,
				with_keywords: $motcle_id,
				with_origin_country: $origine
			);

			$seriesResultats = !empty($seriesFiltres->results) ? $seriesFiltres->results : null;
			$seriesTotalPages = !empty($series->total_pages) ? $seriesFiltres->total_pages : null;
			$seriesResultatsTotaux = !empty($seriesFiltres->total_results) ? $seriesFiltres->total_results : null;

			$series = $seriesFiltres;
		}

		// Recherche Séries : searchTv()

		elseif(!empty($_GET['formSerie']) AND !empty($titreRecherche) AND empty($genre_id) AND empty($motcle_id) AND empty($origine))
		{
			$series = $tmdb->searchTv(
				$titreRecherche,
				page: $page,
				year: $anneeRecherche
			);

			$seriesResultats = !empty($series->results) ? $series->results : null;
			$seriesTotalPages = !empty($series->total_pages) ? $series->total_pages : null;
			$seriesResultatsTotaux = !empty($series->total_results) ? $series->total_results : null;
		}

		else
		{
			$seriesResultats = (object) ['results' => false];
			$seriesTotalPages = 0;
			$seriesResultatsTotaux = 0;
		}

		$r = array_merge_recursive(['movie' => $filmsResultats], ['serie' => $seriesResultats]);

		// Recherche Artistes : searchPerson()

		if((!empty($_GET['formFilm']) OR !empty($_GET['formSerie'])) AND !empty($titreRecherche))
		{
			$artistes = $tmdb->searchPerson(
				$titreRecherche,
				page: $page
			);

			$artistesResultats = !empty($artistes->results) ? $artistes->results : null;
			$artistesTotalPages = !empty($artistes->total_pages) ? $artistes->total_pages : null;
			$artistesResultatsTotaux = !empty($artistes->total_results) ? $artistes->total_results : null;
		}

		else
		{
			$artistesResultats = (object) ['results' => false];
			$artistesTotalPages = 0;
			$artistesResultatsTotaux = 0;
		}

		// Recherche Films

		if(isset($filmsResultatsTotaux) AND $filmsResultatsTotaux > 0 AND isset($filmsResultats) AND !empty($filmsResultats))
		{
			if($filmsResultatsTotaux === 1 AND $seriesResultatsTotaux == 0 AND $artistesResultatsTotaux == 0)
			{
				echo alerte('success', 'Un film trouvé pour <strong>'.secuChars($filmsResultats[0]->title).'</strong>, redirection…');

				redirection('/projets/tmdb/?type=film&id='.secu($filmsResultats[0]->id), 1);
				exit;
			}

			echo '<div class="mb-5" id="films">
				<div class="d-flex ps-3 liner border-c'.((!empty($g_nom) OR !empty($motcle_nom) OR !empty($p_iso_nom)) ? ' mb-4' : null).'"><h2 class="fs-2">Films</h2></div>'.

				(!empty($g_nom) ? '<div class="d-flex ps-3 liner mb-4 border-c"><h2 class="fs-3">Genre : '.$g_nom.'</h2></div>' : null).
				(!empty($motcle_nom) ? '<div class="d-flex ps-3 liner mb-4 border-c"><h2 class="fs-3">Mot clé : '.ucfirst($motcle_nom).'</h2></div>' : null).
				(!empty($p_iso_nom) ? '<div class="d-flex ps-3 liner mb-4 border-c"><h2 class="fs-3">Origine : '.$p_iso_nom.'</h2></div>' : null);

				if($r['movie'] AND $filmsResultatsTotaux > 0)
				{
					foreach($r['movie'] as $km => $vm)
					{
						$id				= (int) $vm->id;
						$titre			= !empty($vm->title)				? secuChars($vm->title)		: 'titre du film inconnu';
						$dateInfos		= dateInfos($vm->release_date);
						$timestamp		= !empty($dateInfos['timestamp'])	? $dateInfos['timestamp']	: null;
						$date			= !empty($dateInfos['date'])		? $dateInfos['date']		: null;
						$synopsis		= !empty($vm->overview)				? secuChars($vm->overview)	: 'synopsis inconnu';
						$poster			= $tmdb->getImageUrl($vm->poster_path, TMDB::IMAGE_POSTER, 'w185', $vm->title);
						$posterLien		= $tmdb->getImageUrl($vm->poster_path, TMDB::IMAGE_POSTER, 'original', $vm->title);

						echo '<div class="d-flex border border-bv rounded mt-3 recherche">
							<a href="/projets/tmdb/?type=film&id='.$id.'" class="d-flex">
								<div><img src="'.$poster.'" class="rounded-start img-recherche-film-serie" alt="Poster de '.$titre.'" title="Poster de '.$titre.'" loading="lazy"></div>
								<div class="p-3">
									<h2>'.$titre.'</h2>
									<p class="opacity-50">'.(!empty($date) ? '<time datetime="'.date(DATE_ATOM, $timestamp).'">'.dateFormat($date).'</time>' : 'date de sortie inconnue').'</p>
									<div class="recherche-synopsis">'.$synopsis.'</div>
								</div>
							</a>
						</div>';
					}
				}

			echo '</div>';

			$pageTotalFilms = ($filmsResultatsTotaux < 10000) ? $filmsResultatsTotaux : 10000;
			$paginationFilms = new Paginator($pageTotalFilms, 20, $page, '?page=(:num)'.(!empty($titreRecherche) ? '&titre='.secuChars($titreRecherche) : null).$formFilm.$formSerie.'#films');

			echo ($filmsResultatsTotaux > 20) ? '<nav class="mb-5" id="pagination" aria-label="Pagination">'.$paginationFilms.'</nav>' : null;
		}

		// Recherche Séries

		if(isset($seriesResultatsTotaux) AND $seriesResultatsTotaux > 0 AND isset($seriesResultats) AND !empty($seriesResultats))
		{
			if($filmsResultatsTotaux == 0 AND $seriesResultatsTotaux === 1 AND $artistesResultatsTotaux == 0)
			{
				echo alerte('success', 'Une série trouvée pour <strong>'.secuChars($seriesResultats[0]->name).'</strong>, redirection…');

				redirection('/projets/tmdb/?type=serie&id='.secu($seriesResultats[0]->id), 1);
				exit;
			}

			echo '<div'.(!empty($artistesResultats) ? ' class="mb-5"' : null).' id="series">
				<div class="d-flex ps-3 liner border-c'.((!empty($g_nom) OR !empty($motcle_nom) OR !empty($p_iso_nom)) ? ' mb-4' : null).'"><h2 class="fs-2">Séries</h2></div>'.

				(!empty($g_nom) ? '<div class="d-flex ps-3 liner mb-4 border-c"><h2 class="fs-3">Genre : '.$g_nom.'</h2></div>' : null).
				(!empty($motcle_nom) ? '<div class="d-flex ps-3 liner mb-4 border-c"><h2 class="fs-3">Mot clé : '.ucfirst($motcle_nom).'</h2></div>' : null).
				(!empty($p_iso_nom) ? '<div class="d-flex ps-3 liner mb-4 border-c"><h2 class="fs-3">Origine : '.$p_iso_nom.'</h2></div>' : null);

				if($r['serie'] AND $seriesResultatsTotaux > 0)
				{
					foreach($r['serie'] as $kt => $vt)
					{
						$id				= (int) $vt->id;
						$titre			= !empty($vt->name)					? secuChars($vt->name)		: 'nom de la série inconnue';
						$dateInfos		= dateInfos($vt->first_air_date);
						$timestamp		= !empty($dateInfos['timestamp'])	? $dateInfos['timestamp']	: null;
						$date			= !empty($dateInfos['date'])		? $dateInfos['date']		: null;
						$synopsis		= !empty($vt->overview)				? secuChars($vt->overview)	: 'synopsis inconnu';
						$poster			= $tmdb->getImageUrl($vt->poster_path, TMDB::IMAGE_POSTER, 'w185', $vt->name);
						$posterLien		= $tmdb->getImageUrl($vt->poster_path, TMDB::IMAGE_POSTER, 'original', $vt->name);

						echo '<div class="col-12 d-flex border border-bv rounded mt-3 recherche">
							<a href="/projets/tmdb/?type=serie&id='.$id.'" class="d-flex">
								<div><img src="'.$poster.'" class="rounded-start img-recherche-film-serie" alt="Poster de '.$titre.'" title="Poster de '.$titre.'" loading="lazy"></div>
								<div class="p-3">
									<h2>'.$titre.'</h2>
									<p class="opacity-50">'.(!empty($date) ? '<time datetime="'.date(DATE_ATOM, $timestamp).'">'.dateFormat($date).'</time>' : 'date de sortie inconnue').'</p>
									<div class="recherche-synopsis">'.$synopsis.'</div>
								</div>
							</a>
						</div>';
					}
				}

			echo '</div>';

			$pageTotalSeries = ($seriesResultatsTotaux < 10000) ? $seriesResultatsTotaux : 10000;
			$paginationSeries = new Paginator($pageTotalSeries, 50, $page, '?page=(:num)'.(!empty($titreRecherche) ? '&titre='.secuChars($titreRecherche) : null).$formFilm.$formSerie.'#series');

			echo ($seriesResultatsTotaux > 20) ? '<nav class="mb-5" id="paginationSeries" aria-label="paginationSeries">'.$paginationSeries.'</nav>' : null;
		}

		// Recherche Artistes

		if(isset($artistesResultatsTotaux) AND $artistesResultatsTotaux > 0 AND isset($artistesResultats) AND !empty($artistesResultats))
		{
			if($filmsResultatsTotaux == 0 AND $seriesResultatsTotaux == 0 AND $artistesResultatsTotaux === 1)
			{
				echo alerte('success', 'Un artiste trouvé pour <strong>'.secuChars($artistesResultats[0]->name).'</strong>, redirection…');

				redirection('/projets/tmdb/?person_id='.secu($artistesResultats[0]->id).'&merge', 1);
				exit;
			}

			echo '<div class="mb-5 '.((!empty($donneesFilms) OR !empty($donneesSeries)) ? 'mt-5' : null).'" id="artistes">
				<div class="liner mb-4 ps-3 fs-2 border-c">Artistes</div>';

				if(isset($artistesResultatsTotaux) AND $artistesResultatsTotaux > 0 AND !empty($artistesResultats))
				{
					foreach($artistesResultats as $vp)
					{
						$idArtiste			= (int) $vp->id;
						$nomArtiste			= !empty($vp->name)					? secuChars($vp->name)					: 'nom / prénom de l’artiste inconnu';
						$sexeArtiste		= !empty($vp->gender)				? (string) ($vp->gender == '2' ? 'homme' : 'femme')	: 'genre de l’artiste inconnu';
						$connuPour			= !empty($vp->known_for_department)	? (string) $vp->known_for_department	: 'catégorie de travail inconnue';
						$ppArtiste			= $tmdb->getImageUrl($vp->profile_path, TMDB::IMAGE_POSTER, 'w90_and_h90_face', $nomArtiste);
						$posterLienArtiste	= $tmdb->getImageUrl($vp->profile_path, TMDB::IMAGE_POSTER, 'original', $nomArtiste);

						echo '<div class="d-flex border border-bv rounded mt-3 recherche">
							<a href="/projets/tmdb/?person_id='.$idArtiste.'&merge" class="d-flex">
								<div class="m-2"><img src="'.$ppArtiste.'" class="rounded img-recherche-artiste" alt="Poster de '.$nomArtiste.'" title="Poster de '.$nomArtiste.'"></div>
								<div class="p-3">
									<h2>'.$nomArtiste.'</h2>
									<p class="text-dark-emphasis mb-0">'.$sexeArtiste.' - <em>'.jobsFr($connuPour).'</em></p>
								</div>
							</a>
						</div>';
					}
				}

			echo '</div>';

			$paginationArtistes = new Paginator($artistesResultatsTotaux, 20, $page, '?page=(:num)'.(!empty($titreRecherche) ? '&titre='.secuChars($titreRecherche) : null).$formFilm.$formSerie.'#artistes');

			echo ($artistesResultatsTotaux > 20) ? '<nav id="paginationArtistes" aria-label="PaginationArtistes">'.$paginationArtistes.'</nav>' : null;
		}

		if($filmsResultatsTotaux == 0 AND $seriesResultatsTotaux == 0 AND $artistesResultatsTotaux == 0 AND !empty($titreRecherche))
			echo alerte('danger', 'Aucun résultat pour <span class="fw-bold">'.secuChars($titreRecherche).'</span>.');
	}

	// Accueil

	else
	{
		echo (!empty($type) AND !empty($_GET['id']) AND empty($id))													? alerte('danger', 'La fiche est introuvable') : null;
		echo (!empty($_GET['person_id']) AND empty($person_id))														? alerte('danger', 'La fiche de l’artiste est introuvable') : null;
		echo (!empty($_GET['genre_id']) AND empty($g_id))															? alerte('danger', 'Le genre est inconnu') : null;
		echo (!empty($_GET['origine']) AND empty($p_iso))															? alerte('danger', 'Le pays d’origine est inconnu') : null;
		echo (isset($_GET['titre']) AND empty($titreRecherche))														? alerte('danger', 'Le champ de recherche ne être vide') : null;
		echo (isset($recherche_spec_nb) AND $recherche_spec_nb === 0 AND !empty($genre_id) AND !empty($g_nom))		? alerte('danger', 'Aucun'.($type === 'serie' ? 'e série' : ' film').' pour le genre <span class="fw-bold">'.(!empty($g_nom) ? $g_nom : 'inconnu').'</span>') : null;
		echo (isset($recherche_spec_nb) AND $recherche_spec_nb === 0 AND !empty($motcle_id))						? alerte('danger', 'Aucun'.($type === 'serie' ? 'e série' : ' film').' pour le mot clé <span class="fw-bold">'.(!empty($motcle_nom->name) ? ucfirst($motcle_nom->name) : 'inconnu').'</span>') : null;
		echo (isset($recherche_spec_nb) AND $recherche_spec_nb === 0 AND !empty($origine) AND !empty($p_iso_nom))	? alerte('danger', 'Aucun'.($type === 'serie' ? 'e série' : ' film').' pour le pays <span class="fw-bold">'.(!empty($p_iso_nom) ? $p_iso_nom : 'inconnu').'</span>') : null;

		echo '<h1 class="my-5"><a href="/projets/tmdb/" style="border-bottom: 1px dashed; text-underline-offset: .375em !important;">βяαιη vιδéo</a></h1>

		<form action="/projets/tmdb/" method="get" id="formSearchAcceuil">
			<div class="input-group">
				<div class="col-12 col-lg-9">
					<div class="input-group">
						<input type="text" name="titre"'.(!empty($titreRecherche) ? ' value="'.secuChars($titreRecherche).'"' : null).' class="form-control form-control-lg" placeholder="Je cherche…" autocomplete="off"'.((!isset($_GET['ygg']) AND empty($_GET['formFilm']) AND empty($_GET['formSerie'])) ? ' autofocus' : null).' required>
						<button type="submit" class="btn btn-brain btn-lg" form="formSearchAcceuil"><i class="fas fa-search"></i><span class="ms-2 d-none d-sm-inline-block">Chercher</span></button>
					</div>
				</div>
				<div class="col-12 col-lg-3 text-center">
					<div class="form-check form-control-lg form-check-inline ms-3 curseur">
						<input '.$onchange.' class="form-check-input curseur" type="checkbox" name="formFilm" value="film" id="filmsRechercheAcceuil" '.((empty($_GET['formMovie']) AND empty($_GET['formSerie']) OR (!empty($_GET['formMovie']))) ? 'checked' : null).'>
						<label class="form-check-label curseur" for="filmsRechercheAcceuil">Films</label>
					</div>
					<div class="form-check form-control-lg form-check-inline curseur">
						<input '.$onchange.' class="form-check-input curseur" type="checkbox" name="formSerie" value="serie" id="seriesRechercheAcceuil" '.((empty($_GET['formMovie']) AND empty($_GET['formSerie']) OR (!empty($_GET['formSerie']))) ? 'checked' : null).'>
						<label class="form-check-label curseur" for="seriesRechercheAcceuil">Séries</label>
					</div>
				</div>
			</div>
		</form>';
	}

	echo '</div>
</main>';

require_once $_SERVER['DOCUMENT_ROOT'].'projets/_footer.php';