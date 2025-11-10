<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title><?= $titleHead; ?>HdV.Li</title>

	<meta name="description" content="HdV.Li permet de vérifier les prix de certains objets / mascottes / montures sur World of Warcraft, que vous souhaitez acheter.">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="index, follow">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<!-- Open Graph / Facebook -->
	<meta property="og:type" contet="wnebsite">
	<meta property="og:url" content="https://hdv.li<?= $lienCanonical; ?>">
	<meta property="og:title" content="<?= $titleHead; ?>HdV.Li">
	<meta property="og:description" content="HdV.Li permet de vérifier les prix de certains objets / mascottes / montures sur World of Warcraft, que vous souhaitez acheter.">
	<meta property="og:image" content="https://hdv.li/assets/img/logo.png">
	<meta property="og:image:type" content="image/png">

	<!-- X (Twitter) -->
	<meta property="twitter:card" content="summary">
	<meta property="twitter:url" content="<?= $lienCanonical; ?>">
	<meta property="twitter:title" content="<?= $titleHead; ?>HdV.Li">
	<meta property="twitter:description" content="HdV.Li permet de vérifier les prix de certains objets / mascottes / montures sur World of Warcraft, que vous souhaitez acheter.">
	<meta property="twitter:image" content="https://hdv.li/assets/img/logo.png">

	<link rel="icon" type="image/svg" href="/favicon.svg">
	<link rel="apple-touch-icon" sizes="192x192" href="/apple-touch-icon-192x192.png">

	<link rel="stylesheet" href="/assets/css/vendors.css?<?= filemtime('assets/css/vendors.css'); ?>">
	<link rel="stylesheet" href="/assets/css/polices.css?<?= filemtime('assets/css/polices.css'); ?>">
	<link rel="stylesheet" href="/assets/css/style.css?<?= filemtime('assets/css/style.css'); ?>">

	<script src="/assets/js/vendors.js?<?= filemtime('assets/js/vendors.js'); ?>"></script>
	<?php
	echo in_array($_SERVER['SCRIPT_NAME'], ['/profil_connexion.php', '/profil_inscription.php', '/profil_mot_de_passe.php', '/profil_mot_de_passe_changer.php'], true) ? '<script src="https://www.google.com/recaptcha/api.js?render=6Le00VgrAAAAAAotLmrPy3LFJVFL_36lQ7XWWjll"></script>'."\n" : null;

	if(!in_array($_SERVER['SCRIPT_NAME'], ['/mascottes.php', '/prix_objet.php'], true))
	{
		echo '<!-- https://www.wowhead.com/tooltips -->
		<script>const whTooltips = {colorLinks: true, iconizeLinks: true, renameLinks: true};</script>
		<script src="https://wow.zamimg.com/js/tooltips.js"></script>'."\n\n";
	}

	echo !empty($lienCanonical) ? '<link rel="canonical" href="https://hdv.li'.$lienCanonical.'">'."\n" : null;
	?>
</head>

<body class="horde">
	<header>
		<div class="container-fluid p-0 d-none d-lg-block">
			<div class="bg bg-horde"></div>
		</div>
		<div class="container">
			<nav class="navbar navbar-expand-lg">
				<div class="col-12 col-lg-10 mx-auto">
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Afficher / masquer la navigation"><span class="navbar-toggler-icon"></span></button>
					<div class="collapse navbar-collapse text-center" id="navbarText">
						<a href="/" class="d-none d-lg-inline-block me-lg-3 logo"><img src="/assets/img/logo.png" style="width: 75px;" alt="Logo" title="Accueil"></a>

						<ul class="navbar-nav">
							<li class="nav-item d-block d-lg-none"><a href="/" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/index.php' ? ' active' : null); ?>" title="Accueil">Accueil</a></li>
							<li class="nav-item"><a href="/serveurs" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/serveurs.php' ? ' active' : null); ?>" title="Les Serveurs">Serveurs</a></li>
							<li class="nav-item"><a href="/mascottes" class="nav-link<?= (in_array($_SERVER['SCRIPT_NAME'], ['/mascottes.php', '/mascotte_fiche.php'], true) ? ' active' : null); ?>" title="Les Mascottes">Mascottes</a></li>
							<li class="nav-item"><a href="/addons" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/addons.php' ? ' active' : null); ?>" title="Les Addons">Addons</a></li>
							<li class="nav-item"><a href="/historique-des-prix-du-jeton-wow" class="nav-link<?= ($_SERVER['SCRIPT_NAME'] === '/jeton.php' ? ' active' : null); ?>" title="Prix du Jeton">Prix du Jeton</a></li>
							<?= estAdmin() ? '<li class="nav-item"><a href="/admin" class="nav-link'.($_SERVER['SCRIPT_NAME'] === '/admin.php' ? ' active' : null).'" title="Administration">Admin</a></li>' : null; ?>
						</ul>

						<ul class="navbar-nav ms-auto">
							<?php
							echo estConnecte($pdo) ? '<li class="nav-item"><a href="/profil" class="nav-link'.($_SERVER['SCRIPT_NAME'] === '/profil.php' ? ' active' : null).'" title="Mon Profil">Profil</a></li>
							<li class="nav-item"><a href="/deconnexion" class="nav-link" title="Déconnexion"><i class="fa-solid fa-right-from-bracket"></i></a></li>'
							:
							'<li class="nav-item"><a href="/connexion" class="nav-link'.($_SERVER['SCRIPT_NAME'] === '/profil_connexion.php' ? ' active' : null).'" title="Connexion">Connexion</a></li>
							<li class="nav-item"><a href="/inscription" class="nav-link'.($_SERVER['SCRIPT_NAME'] === '/profil_inscription.php' ? ' active' : null).'" title="Inscription">Inscription</a></li>';
							?>
						</ul>
					</div>
				</div>
			</nav>
		</div>
	</header>

	<main>
		<div class="container">
		<?php
		echo isset($_GET['compte-supprimer']) ? alerte('success', 'Votre compte a été supprimé') : null;
		echo isset($_GET['courriel-change']) ? alerte('success', 'Un courriel vous été envoyé, merci de confirmer votre compte') : null;
		echo isset($_GET['mot-de-passe-change']) ? alerte('success', 'Votre mot de passe a été changé') : null;

		echo getFlash();