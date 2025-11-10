<?php
require_once '../../config/config.php';

$titleHead = isset($_GET['uncache']) ? 'Nettoyage du cache - ' : null;
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title><?= $titleHead; ?>Les Projets</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<link rel="icon" type="image/png" href="/favicon.png">
	<link rel="stylesheet" href="/assets/css/vendors.css?<?= filemtime('../assets/css/vendors.css'); ?>">

	<style>
	body			{ background-color: rgba(17,21,28, 1); color: rgba(214,104,83, 1) !important; font-family: Arial, Verdana, Tahoma, sans-serif; font-size: .95rem !important; }
	a				{ color: rgba(214,104,83, 1); text-decoration: none; }
	a:hover			{ opacity: .75; }
	.curseur		{ cursor: pointer; }
	h1 a			{ text-decoration: underline; text-underline-offset: .375rem !important; }
	</style>

	<script src="/assets/js/vendors.js?<?= filemtime('../assets/js/vendors.js'); ?>"></script>
</head>

<body>
<div class="container">
	<h1 class="my-5 text-center"><a href="/projets/">Les Projets</a></h1>

	<?php
	echo isset($_GET['uncache']) ? nettoyage((isset($_GET['complet']) ? true : false)) : null;

	echo '<div class="row">
		<div class="col-12 col-lg-4 mx-auto">';

		if($handle = opendir('.'))
		{
			while(false !== ($fichiers = readdir($handle)))
			{
				if(!in_array($fichiers, ['.', '..', '.htaccess', 'index.php', '_footer.php']))
				{
					if(is_dir($fichiers))
					{
						$dirs[] = '<div class="row mb-2 p-0">
							<div class="col-6" title="'.$fichiers.'"><i class="fa-regular fa-folder me-2"></i> <a href="'.$fichiers.'/" class="text-decoration-underline">'.$fichiers.'</a></div>
							<div class="col-2"><span class="badge rounded-pill bg-primary curseur" title="Taille du dossier">'.taille(tailleDossier($fichiers)).'</span></div>
							<div class="col-4"><span class="badge rounded-pill bg-primary curseur" title="Le '.dateFormat(filemtime($fichiers), 'c').'"><time datetime="'.date(DATE_ATOM, filemtime($fichiers)).'">'.temps(filemtime($fichiers)).'</time></span></div>
						</div>';
					}

					else
					{
						$fichiersLiens = ($fichiers !== 'adminer.php') ? str_ireplace('.php', '', $fichiers) : $fichiers;

						$fichiersArray[] = '<div class="row mb-2 p-0">
							<div class="col-6" title="'.$fichiers.'"><i class="fa-regular fa-file me-2"></i> <a href="'.$fichiersLiens.'">'.$fichiers.'</a></div>
							<div class="col-2"><span class="badge rounded-pill bg-primary curseur" title="Taille du fichier">'.taille(filesize($fichiers)).'</span></div>
							<div class="col-4"><span class="badge rounded-pill bg-primary curseur" title="Le '.dateFormat(filemtime($fichiers), 'c').'"><time datetime="'.date(DATE_ATOM, filemtime($fichiers)).'">'.temps(filemtime($fichiers)).'</time></span></div>
						</div>';
					}
				}
			}

			closedir($handle);
		}

		if(!empty($dirs)) {
			sort($dirs);

			foreach($dirs as $dir)
				echo $dir;
		}

		if(!empty($fichiersArray)) {
			sort($fichiersArray);

			foreach($fichiersArray as $f)
				echo $f;
		}

		echo '</div>
	</div>
</div>';

require_once $_SERVER['DOCUMENT_ROOT'].'projets/_footer.php';