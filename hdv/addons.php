<?php
require_once '../config/wow_config.php';
require_once 'a_body.php';

// https://www.wow-petguide.com/Guide/69/Recommended_Addons
// https://www.warcraftpets.com/downloads/pet_addons/

try {
	$stmt = $pdo->prepare('SELECT * FROM wow_addons ORDER BY nom');
	$stmt->execute();
	$res = $stmt->fetchAll();
} catch (\PDOException $e) { }

echo '<div class="banniere banniere-addons mt-4"></div>

<h1><a href="/addons">Addons</a></h1>

<div class="mb-4">
	<p>Les addons sont des extensions créées par la communauté permettant de personnaliser et d’améliorer l’interface utilisateur de World of Warcraft. Grâce à eux, chaque joueur peut adapter son expérience de jeu à ses préférences personnelles, qu’il s’agisse de faciliter la gestion des combats, d’optimiser les performances, ou simplement de rendre l’interface plus agréable et intuitive.</p>

	<p>Ils jouent un rôle essentiel dans la vie du joueur moderne, qu’il soit débutant ou vétéran. Ils offrent des outils puissants pour analyser les combats, suivre les quêtes, organiser les raids, surveiller les ennemis, ou encore automatiser certaines tâches répétitives.</p>

	<p class="mb-0"><a href="https://undermine.exchange/" '.$onclick.'>Undermine</a> est hôtel des ventes en ligne. Vous pouvez vérifier les prix des objets en direct, leur historique, etc.., un super outil qui évite de se connecter sur le jeu pour vérifier les prix. Il est impossible d’acheter / vendre, mais ça reste un excellent outil.</p>
	</div>

<h3>Liste des addons</h3>

<ul>';

	foreach($res as $cle => $v)
	{
		echo '<li><a href="#'.slug($v['nom']).'">'.$v['nom'].'</a></li>';
	}

echo '</ul>';

foreach($res as $cle => $v)
{
	$categorie = explode(';', $v['categorie']);
	$nom = $v['nom'];
	$url_curse = $v['url_curse'];
	$url_github = $v['url_github'];
	$url_wago = $v['url_wago'];
	$imgs = explode(';', $v['images']);
	$description = $v['description'];

	echo '<hr class="my-4">

	<div id="'.slug($nom).'">
		<h3 class="mb-0 mt-0 text-start"><a href="#'.slug($nom).'">'.$nom.'</a></h3>
		<p class="text-secondary">'.$categorie[1].'</p>

		<p class="mb-4">'.nl2br($v['description'], false).'</p>

		<div class="mb-5 d-flex flex-wrap justify-content-center gap-3 gap-lg-4">';

			if(!empty($v['images']))
			{
				$nbImgs = count($imgs);
				for($i = 0; $i < $nbImgs; $i++)
				{
					echo '<div class="addons-image"><a href="'.$imgs[$i].'" data-fancybox="gallerie"><img src="'.$imgs[$i].'" class="img-fluid border rounded" alt="Capture d’écran n°'.$i.' de '.$nom.'" title="Capture d’écran n°'.$i.' de '.$nom.'" loading="lazy"></a></div>';
				}
			}

		echo '</div>

		<div class="row">
			<div class="text-center">
				<div class="me-1 me-lg-4 d-inline-block">
					'.(!empty($v['url_curse']) ? '<a href="'.$v['url_curse'].'" class="text-decoration-none" '.$onclick.'>
						<div class="d-flex align-items-center border rounded px-2 py-1 px-lg-3 py-lg-2 fs-5"><img src="/assets/img/logo-wow-curse.svg" class="logo-curse" alt="Logo Curse.com" title="Logo Curse.com"> Curse.com</div>
					</a>'
					:
					'<div class="d-flex align-items-center border rounded px-2 py-1 px-lg-3 py-lg-2 fs-5 text-secondary"><img src="/assets/img/logo-wow-curse.svg" style="filter: grayscale(100%);" class="logo-curse" alt="Logo Curse.com" title="Logo Curse.com"> Curse.com</div>').'
				</div>
				<div class="me-1 me-lg-4 d-inline-block">
					'.(!empty($v['url_wago']) ? '<a href="'.$v['url_wago'].'" class="text-decoration-none" '.$onclick.'>
						<div class="d-flex align-items-center border rounded px-2 py-1 px-lg-3 py-lg-2 fs-5"><img src="/assets/img/logo-wow-wago.svg" class="logo-wago" alt="Logo Wago.io" title="Logo Wago.io"> Wago.io</div>
					</a>'
					:
					'<div class="d-flex align-items-center border rounded px-2 py-1 px-lg-3 py-lg-2 fs-5 text-secondary"><img src="/assets/img/logo-wow-wago.svg" style="filter: grayscale(100%);" class="logo-wago" alt="Logo Wago.io" title="Logo Wago.io"> Wago.io</div>').'
				</div>
				<div class="d-inline-block">
					'.(!empty($v['url_github']) ? '<a href="'.$v['url_github'].'" class="text-decoration-none" '.$onclick.'>
						<div class="d-flex align-items-center border rounded px-2 py-1 px-lg-3 py-lg-2 fs-5"><img src="/assets/img/logo-wow-github.svg" class="logo-github" alt="Logo GitHub" title="Logo GitHub"> GitHub</div>
					</a>'
					:
					'<div class="d-flex align-items-center border rounded px-2 py-1 px-lg-3 py-lg-2 fs-5 text-secondary"><img src="/assets/img/logo-wow-github.svg" style="filter: grayscale(100%);" class="logo-github" alt="Logo GitHub" title="Logo GitHub"> GitHub</div>').'
				</div>
			</div>
		</div>
	</div>';
}

require_once 'a_footer.php';