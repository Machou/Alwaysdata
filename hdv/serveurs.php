<?php
require_once '../config/wow_config.php';
require_once 'a_body.php';

$trier = in_array($_GET['trier'] ?? '', ['nom', 'type_serveur_nom'], true) ? secuChars($_GET['trier']) : 'nom';
$trierPar = in_array(mb_strtolower($_GET['trierpar'] ?? ''), ['asc', 'desc'], true) ? mb_strtolower($_GET['trierpar']) : 'asc';

try {
	$langues = !empty($_GET['langue']) ? explode(',', mb_strtolower($_GET['langue'])) : null;
	if(!empty($langues))
	{
		$conditions = [];
		$params = [];
		$langueUrl = [];

		foreach($langues as $langue)
		{
			if(in_array($langue, ['de', 'en', 'es', 'fr', 'it', 'pt', 'ru']))
			{
				$conditions[] = 'locale LIKE ?';
				$params[] = '%'.$langue.'%';
				$langueUrl[] = $langue;
			}
		}

		if(!empty($conditions) AND !empty($params))
		{
			$stmt = $pdo->prepare('SELECT * FROM wow_serveurs WHERE '.implode(" OR ", $conditions).' ORDER BY langue, '.$trier.' '.$trierPar);
			$stmt->execute($params);
		}
	}

	else
		$stmt = $pdo->query('SELECT * FROM wow_serveurs ORDER BY langue, '.$trier.' '.$trierPar);

	$resServeurs = $stmt->fetchAll();
} catch (\PDOException $e) { }

try {
	$stmt = $pdo->prepare('SELECT langue, locale, COUNT(*) AS nombre_serveurs FROM wow_serveurs GROUP BY locale ORDER BY locale');
	$stmt->execute();
	$resNbServeurs = $stmt->fetchAll();
} catch (\PDOException $e) { }

echo '<h1><a href="/serveurs">Les Serveurs</a></h1>';

if(!empty($resNbServeurs))
{
	echo '<div class="container">
		<div class="row border rounded py-4 mb-4 text-center">
			<div class="d-flex flex-wrap justify-content-center gap-2 gap-lg-4 mb-4">';

				foreach($resNbServeurs as $n)
				{
					$locale = strtolower(substr($n['locale'], 0, 2));
					$langue = ($locale === 'pt') ? 'Portugais' : $n['langue'];

					echo '<span class="border rounded px-2 py-1'.((!empty($_GET['langue']) AND $locale === $_GET['langue']) ? ' opacity-75' : null).' " data-bs-toggle="tooltip" data-bs-title="'.$langue.'">
						<a href="/serveurs/'.$locale.'" class="text-decoration-none">
							<span class="me-1 me-lg-2">'.isoEmoji($locale).'</span>
							<span class="me-1 d-none d-lg-inline">'.$langue.'</span>
						</a>
						<span class="text-white" id="count-'.$locale.'">0</span>
					</span>';
				}
			echo '</div>

			<div>
				<label class="form-check d-inline-flex align-items-center curseur">
					<input type="checkbox" id="masquerNonConnectes" class="form-check-input me-2 curseur" checked>
					<span>Masquer les serveurs non connectés</span>
				</label>
			</div>

			<div class="col-12 col-lg-8 mx-auto my-3">
				<hr>
			</div>

			<div class="row">
				<div class="col-10 col-lg-3 mx-auto">
					<input type="text" class="form-control form-control-lg mx-auto" id="fS" placeholder="Filtrer les serveurs">
				</div>
			</div>
		</div>';

		$listeServeurs = [];
		foreach($resServeurs as $res)
		{
			$idBlizzard = secu($res['id_blizzard']);
			$idConnecte = secu($res['id_connecte']);
			$region = secuChars($res['region']);
			$nomServeur = secuChars($res['nom']);
			$slugNomServeur = secuChars($res['slug']);
			$nomServeurRu = !empty($res['nom_ru']) ? secuChars($res['nom_ru']) : null;
			$langue = secuChars($res['langue']);
			$locale = secuChars($res['locale']);
			$localeDrapeau = ($locale === 'enGB') ? 'gb' : strtolower(substr($locale, 0, 2));
			$timezone = secuChars($res['timezone']);
			$typeServeur = secuChars($res['type_serveur']);
			$typeServeurNom = secuChars($res['type_serveur_nom']);
			$typeNom = strtr($typeServeurNom, ['JdR' => 'Jeu de Rôle']);

			try {
				$stmt = $pdo->prepare('SELECT langue, locale, COUNT(*) AS nombre_serveurs FROM wow_serveurs GROUP BY locale ORDER BY locale');
				$stmt->execute();
				$resNbServeurs = $stmt->fetchAll();
			} catch (\PDOException $e) { }

			$liensServeurs[] = '/'.$idBlizzard.'-'.$slugNomServeur;

			$listeServeurs[] = '<div class="row border-bottom py-3 text-center" id="'.$slugNomServeur.'" data-connecte="'.($idBlizzard === $idConnecte ? 'true' : 'false').'">
				<div class="col-4	col-lg-4">'.(estConnecte() ? '<a href="/'.$idBlizzard.'-'.$slugNomServeur.'" class="lien-alliance'.($idBlizzard === $idConnecte ? ' text-decoration-underline link-offset-3' : null).'"'.($idBlizzard === $idConnecte ? ' data-bs-toggle="tooltip" data-bs-title="Serveur connecté"' : null).(estAdmin() ? ' onclick="hide(\'#'.$slugNomServeur.'\');"' : null).'>'.$nomServeur.'</a>' : '<span class="lien-alliance"'.($idBlizzard === $idConnecte ? ' data-bs-toggle="tooltip" data-bs-title="Serveur connecté"' : null).'>'.$nomServeur.'</a>').'</div>
				<div class="col-2	col-lg-3" data-bs-toggle="tooltip" data-bs-title="Langue : '.$langue.'">'.isoEmoji($localeDrapeau).' <span class="d-none d-lg-inline-block">'.$langue.'</span></div>
				<div class="col-3	col-lg-3">'.$typeNom.'</div>
				<div class="col-3	col-lg-2">
					'.(estConnecte() ? '<a href="/profil-verification?id_serveur_favori='.$idBlizzard.'" class="text-decoration-none me-3" data-bs-toggle="tooltip" data-bs-title="'.($resUtilisateur['id_serveur_favori'] !== $idBlizzard ? 'Marquer <span class=\'fw-bold\'>'.$nomServeur.'</span> comme favori"><i ': 'Supprimer <span class=\'fw-bold\'>'.$nomServeur.'</span> de mon serveur favori"><i style="color: yellow;"').' class="fa-solid fa-star"></i></a>' : null).'
					<i class="fa-solid fa-question curseur" data-bs-toggle="tooltip" data-bs-title="
						<span class=\'fw-bold\'>ID Blizzard</span> : '.$idBlizzard.'<br>
						<span class=\'fw-bold\'>ID Connecté</span> : '.$idConnecte.'<br><br>

						<span class=\'fw-bold\'>Serveur</span> : '.$nomServeur.'<br>
						'.(!empty($nomServeurRu) ? '<span class=\'fw-bold\'>Nom Russe</span> : '.$nomServeurRu.'<br>' : null).'
						<span class=\'fw-bold\'>Slug</span> : '.$slugNomServeur.'<br><br>

						<span class=\'fw-bold\'>Région</span> : '.$region.'<br>
						<span class=\'fw-bold\'>Langue</span> : '.$langue.'<br>
						<span class=\'fw-bold\'>Locale</span> : '.$locale.'<br>
						<span class=\'fw-bold\'>Timezone</span> : '.$timezone.'<br><br>

						<span class=\'fw-bold\'>Type de serveur</span> : '.$typeNom.'
					"></i>
				</div>
			</div>';
		}

		if(estAdmin() AND !empty($liensServeurs))
		{
			echo '<p class="text-center my-4"><a href="#" class="btn btn-outline-warning" onclick="ouvrirTous(); return false;">Ouvrir les '.count($liensServeurs).' liens</a></p>

			<script>
			function ouvrirTous() {
				const urls = [';

					foreach($liensServeurs as $l)
						echo '"'.$l.'",'."\n";

				echo '];
				for (const url of urls) {
					window.open(url, \'_blank\');
				}
			}
			</script>';
		}

		echo '<div class="row border rounded py-3 mb-4 text-center" id="listeServeurs">
			<div class="col-4 fs-4">
				<a href="/serveurs?trier=nom&trierpar='.(($trier === 'nom') ? ($trierPar === 'asc' ? 'desc' : 'asc') : 'asc').(!empty($langueUrl) ? '&langue='.implode(',', $langueUrl) : null).'" data-bs-toggle="tooltip" data-bs-title="Trier par Nom du serveur">
				<span class="d-none d-lg-inline">Nom du serveur</span>
				<span class="d-inline d-lg-none">Nom</span> </a>
				'.trierIcone('nom', $trier, $trierPar).'
			</div>
			<div class="col-4 col-lg-3">
				<div class="dropdown-center">
					<button type="button" class="btn btn-secondary btn-sm d-inline d-md-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">par Langue</button>
					<button type="button" class="btn btn-secondary d-none d-md-inline dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">Filtrer par Langue</button>
					<form method="get" id="filtresLangues">
						<input type="hidden" name="serveurs">
						<input type="hidden" name="trier" value="'.$trier.'">
						<input type="hidden" name="trierpar" value="'.$trierPar.'">
						<ul class="dropdown-menu p-2">
							<li>
								<div class="form-check">
									<input type="checkbox" value="de" name="langue" class="form-check-input" id="de"'.((!empty($langues) AND in_array('de', $langueUrl, true)) ? ' checked' : null).'>
									<label class="form-check-label" for="de">🇩🇪 Allemand</label>
								</div>
							</li>
							<li>
								<div class="form-check">
									<input type="checkbox" value="en" name="langue" class="form-check-input" id="en"'.((!empty($langues) AND in_array('en', $langueUrl, true)) ? ' checked' : null).'>
									<label class="form-check-label" for="en">🇬🇧 Anglais</label>
								</div>
							</li>
							<li>
								<div class="form-check">
									<input type="checkbox" value="es" name="langue" class="form-check-input" id="es"'.((!empty($langues) AND in_array('es', $langueUrl, true)) ? ' checked' : null).'>
									<label class="form-check-label" for="es">🇪🇸 Espagnol</label>
								</div>
							</li>
							<li>
								<div class="form-check">
									<input type="checkbox" value="fr" name="langue" class="form-check-input" id="fr"'.((!empty($langues) AND in_array('fr', $langueUrl, true)) ? ' checked' : null).'>
									<label class="form-check-label" for="fr">🇫🇷 Français</label>
								</div>
							</li>
							<li>
								<div class="form-check">
									<input type="checkbox" value="it" name="langue" class="form-check-input" id="it"'.((!empty($langues) AND in_array('it', $langueUrl, true)) ? ' checked' : null).'>
									<label class="form-check-label" for="it">🇮🇹 Italien</label>
								</div>
							</li>
							<li>
								<div class="form-check">
									<input type="checkbox" value="pt" name="langue" class="form-check-input" id="pt"'.((!empty($langues) AND in_array('pt', $langueUrl, true)) ? ' checked' : null).'>
									<label class="form-check-label" for="pt">🇵🇹 Portugais</label>
								</div>
							</li>
							<li>
								<div class="form-check">
									<input type="checkbox" value="ru" name="langue" class="form-check-input" id="ru"'.((!empty($langues) AND in_array('ru', $langueUrl, true)) ? ' checked' : null).'>
									<label class="form-check-label" for="ru">🇷🇺 Russe</label>
								</div>
							</li>
							<li class="mt-2 text-center">
								<button type="submit" class="btn btn-success">Filtrer</button>
							</li>
						</ul>
					</form>
				</div>
			</div>
			<div class="col-4 col-lg-3 fs-4">
				<a href="/serveurs?trier=type_serveur_nom&trierpar='.(($trier === 'type_serveur_nom' AND $trierPar === 'asc') ? 'desc' : 'asc').(!empty($langueUrl) ? '&langue='.implode(',', $langueUrl) : null).'" data-bs-toggle="tooltip" data-bs-title="Trier par Type de serveur">Type</a>
				'.trierIcone('type_serveur_nom', $trier, $trierPar).'
			</div>
			<div class="col-lg-2"></div>
		</div>';

		echo count($listeServeurs) > 0 ? implode($listeServeurs) : alerte('danger', 'Aucun serveur');

	echo '</div>';
}

require_once 'a_footer.php';