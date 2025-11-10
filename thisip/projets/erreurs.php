<?php
require_once '../../config/config.php';

$fichier = 'apache-errors.log';
$fichierCourriel = 'mail.log';

$f = '/home/blok/www/'.$fichier;
$fCourriel = '/home/blok/www/'.$fichierCourriel;

if(isset($_GET['nettoyer']) AND isset($_GET['erreurs']))	nettoyerLogs('erreurs') ? header('Location: erreurs') : null;
if(isset($_GET['nettoyer']) AND isset($_GET['mail']))		nettoyerLogs('mail') ? header('Location: erreurs?mail') : null;
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title><?= (!isset($_GET['mail']) ? $fichier : $fichierCourriel); ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<link rel="icon" type="image/png" href="/favicon.png">
	<link rel="stylesheet" href="/assets/css/vendors.css?<?= filemtime('../assets/css/vendors.css'); ?>">

	<script src="/assets/js/vendors.js?<?= filemtime('../assets/js/vendors.js'); ?>"></script>
</head>

<body>
	<div class="container text-center mt-5">

		<p class="d-inline-flex gap-1 mb-0">
			<a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseErreurs" role="button" aria-expanded="false" aria-controls="collapseErreurs">apache-errors.log</a>
			<a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseMail" role="button" aria-expanded="false" aria-controls="collapseMail">mail.log</a>
		</p>

		<div class="accordion" id="collapseGroup">
			<div class="collapse<?= ((!isset($_GET['mail'])) ? ' show' : null); ?>" id="collapseErreurs" data-bs-parent="#collapseGroup">
					<h1 class="my-5"><a href="erreurs" class="link-offset-2"><?= $fichier; ?></a></h1>

					<?php
					$f = '/home/blok/www/'.$fichier;

					echo (filesize($f) > 0 ? '<p class="mb-5"><a href="?nettoyer&erreurs" class="btn btn-outline-success">Nettoyer le fichier <span class="fw-bold">apache-errors.log</span></a></p>' : null);

					if(file_exists($f) AND is_file($f))
					{
						if(filesize($f) > 0)
						{
							if(filesize($f) < 50000000)
							{
								$erreurs = [];
								$lignes = file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

								foreach($lignes as $ligne)
									$erreurs[] = preg_replace('/Erreur 404/', '<span style="color: red;">'.$ligne.'</span>', $ligne);

								$erreurs = array_reverse($erreurs);

								foreach($erreurs as $erreur)
									p($erreur);
							}

							else
								echo alerte('danger', 'Le fichier erreur est trop grand');
						}

						else
							echo alerte('info', 'Le fichier <span class="fw-bold">'.$fichier.'<span> est vide');
					}
					?>
				</div>
			</div>

			<div class="collapse<?= (isset($_GET['mail']) ? ' show' : null); ?>" id="collapseMail" data-bs-parent="#collapseGroup">
				<h1 class="my-5"><a href="erreurs?mail" class="link-offset-2"><?= $fichierCourriel; ?></a></h1>

				<?php
				$fCourriel = '/home/blok/www/'.$fichierCourriel;

				if(file_exists($fCourriel) AND is_file($fCourriel))
				{
					if(filesize($fCourriel) > 0)
					{
						echo '<div>
							'.(filesize($fCourriel) > 0 ? '<p class="mb-5"><a href="?nettoyer&mail" class="btn btn-outline-success">Nettoyer le fichier <span class="fw-bold">mail.log</span></a></p>' : null);

							if(filesize($fCourriel) < 50000000)
							{
								$erreurs = [];
								$lignes = file($fCourriel, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

								foreach($lignes as $ligne)
									$erreurs[] = $ligne;

								$erreurs = array_reverse($erreurs);

								foreach($erreurs as $erreur)
									p($erreur);
							}

							else
								echo alerte('danger', 'Le fichier erreur est trop grand');

						echo '</div>';
					}

					else
						echo alerte('info', 'Le fichier <span class="fw-bold">'.$fichierCourriel.'<span> est vide');
				}
				?>
			</div>
		</div>
	</div>

	<script>setTimeout(function() { location.reload(true) }, 60000);</script>
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'projets/_footer.php';