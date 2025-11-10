<?php
require_once '../../config/config.php';

use Symfony\Component\Yaml\Yaml;

echo '<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title>Trackers</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<link rel="icon" href="/assets/img/favicon-wow.png">
	<link rel="stylesheet" href="/assets/css/vendors.css?'.filemtime('../assets/css/vendors.css').'">

	<script src="/assets/js/vendors.js?'.filemtime('../assets/js/vendors.js').'"></script>
</head>

<body>
<div class="container">
	<h1 class="my-5 text-center"><a href="trackers.php" class="link-offset-2">Informations Trackers</a></h1>';

	$repertoireDefinitions = '/home/blok/www/thisip/projets/Definitions';


// https://vault.bitwarden.com/#/reports/breach-report


	if(!is_dir($repertoireDefinitions)) {
		echo alerte('danger', 'Le dossier des définitions est introuvable : <span class="fw-bold">'.$repertoireDefinitions.'</span>');
		exit;
	}

	$fichiers = glob($repertoireDefinitions.'/*.yml');

	if(empty($fichiers)) {
		echo alerte('danger', 'Aucun fichier <span class="fw-bold">.yml</span> trouvé dans <span class="fw-bold">'.$repertoireDefinitions.'</span>');
		exit;
	}

	foreach($fichiers as $file)
	{
		echo '<div class="row mb-4">
			<div class="col-12 text-center fw-bold fs-5">'.str_replace('.yml', '', basename($file)).'</div>
		</div>';

		try {
			$data = Yaml::parseFile($file);

			$name = $data['name'] ?? 'n/a';
			$description = $data['description'] ?? 'n/a';
			$language = $data['language'] ?? 'n/a';
			$type = $data['type'] ?? 'n/a';

			$liens = $data['links'] ?? [];
			$legacyLiens = $data['legacylinks'] ?? [];

			echo '<div class="row">
				<div class="col-3">Nom : '.$name.'</div>
				<div class="col-3">Description : '.$description.'</div>
				<div class="col-3">Langue : '.$language.'</div>
				<div class="col-3">Type : '.$type.'</div>
			</div>

			<div class="row">
				<div class="col-3">Liens</div>
				<div class="col-9">';

					foreach($liens as $lien)
					{
						$lienC = trim(strtr(mb_strtolower($lien), [
							'https://' => '',
							'http://' => '',
							'www5.' => '',
							'www4.' => '',
							'www3.' => '',
							'www2.' => '',
							'www1.' => '',
						]));

						echo '<p><a href="'.$lien.'" '.$onclick.'">'.$lienC.'</a></p>';
					}

				echo '</div>
			</div>

			<div class="row">
				<div class="col-3">Liens Legacy</div>
				<div class="col-9">';

					foreach($legacyLiens as $lien)
					{
						$lienC = trim(strtr(mb_strtolower($lien), [
							'https://' => '',
							'http://' => '',
							'www5.' => '',
							'www4.' => '',
							'www3.' => '',
							'www2.' => '',
							'www1.' => '',
						]));

						echo '<p><a href="'.$lien.'" '.$onclick.'">'.$lienC.'</a></p>';
					}

				echo '</div>
			</div>';

			// echo str_repeat('-', 40).'<br>';

		} catch (\Exception $e) {
			echo 'Erreur de lecture YAML dans '.$file.' : '.$e->getMessage();
		}
	}

echo '</div>';

require_once $_SERVER['DOCUMENT_ROOT'].'projets/_footer.php';