<?php
require_once '../config/wow_config.php';
require_once 'a_body.php';

if(!empty($resUtilisateur['est_confirmer']) AND $resUtilisateur['est_confirmer'] == 1 AND !empty($_POST['id_serveur_form']) AND !empty($_POST['nom_personnage_form']))
{
	$idServeurForm = $_POST['id_serveur_form'];
	$nomPersonnageForm = $_POST['nom_personnage_form'];
	$infosServeur = serveurInfos($pdo, $idServeurForm);
	$idServeurJson = $infosServeur['id_blizzard'];
	$nomServeurJson = $infosServeur['nom'];

	try {
		$stmt = $pdo->prepare('SELECT * FROM wow_personnages WHERE id_serveur = :id_serveur AND nom = :nom_personnage');
		$stmt->execute([
			'id_serveur' => (int) $idServeurForm,
			'nom' => (string) $nomPersonnageForm
		]);
		$resMascotte = $stmt->fetch();
	} catch (\PDOException $e) { }

	if($idServeurForm == $resUtilisateur['id_serveur'] AND $nomPersonnageForm == $resUtilisateur['nom_personnage'])
	{
		$mascottes = $apiClient->character()->petCollection($nomServeur, $nomPersonnageForm);
		// $apiClient->character()->mountCollection('Ysondre', 'Klep');
		// $apiClient->character()->collections('Ysondre', 'Klep');

		$_SESSION['msgAjoutMascotte'] = [];
		$_SESSION['msgModificationMascotte'] = [];

		foreach($mascottes->pets as $cle => $val)
		{
			if(!empty($val->species->name->fr_FR))
			{
				$idBlizzard = (int) $val->id;
				$idMascotte = (int) $val->species->id;
				$mascotteCreatureId = isset($val->creature_display->id) ? (int) $val->creature_display->id : null;
				$mascotteNom = (string) ($val->species->name->fr_FR ?? '');
				$mascotteNiveau = isset($val->level) ? (int) $val->level : null;
				$mascotteTypeQualite = (string) ($val->quality->type ?? '');
				$mascotteQualite = (string) ($val->quality->name->fr_FR ?? '');
				$statsRaceId = isset($val->stats->breed_id) ? (int) $val->stats->breed_id : null;
				$statsVie = isset($val->stats->health) ? (int) $val->stats->health : null;
				$statsPuissance = isset($val->stats->power) ? (int) $val->stats->power : null;
				$statsVitesse = isset($val->stats->speed) ? (int) $val->stats->speed : null;

				$stmt = $pdo->prepare('SELECT * FROM wow_mascottes_u WHERE id_blizzard = :id_blizzard');
				$stmt->execute(['id_blizzard' => $idBlizzard]);
				$resAncienne = $stmt->fetch();

				if(!empty($resAncienne)) {
					$anciennesStats = [
						'nom_mascotte' => (string) $resAncienne['nom_mascotte'],
						'niveau' => is_null($resAncienne['niveau']) ? null : (int) $resAncienne['niveau'],
						'type_qualite' => (string) $resAncienne['type_qualite'],
						'qualite' => (string) $resAncienne['qualite'],
						'stats_race_id' => is_null($resAncienne['stats_race_id']) ? null : (int) $resAncienne['stats_race_id'],
						'stats_vie' => is_null($resAncienne['stats_vie']) ? null : (int) $resAncienne['stats_vie'],
						'stats_puissance' => is_null($resAncienne['stats_puissance']) ? null : (int) $resAncienne['stats_puissance'],
						'stats_vitesse' => is_null($resAncienne['stats_vitesse']) ? null : (int) $resAncienne['stats_vitesse'],
					];

					if($anciennesStats['nom_mascotte'] !== $mascotteNom OR $anciennesStats['niveau'] !== $mascotteNiveau OR $anciennesStats['type_qualite'] !== $mascotteTypeQualite OR $anciennesStats['qualite'] !== $mascotteQualite OR $anciennesStats['stats_race_id'] !== $statsRaceId OR $anciennesStats['stats_vie'] !== $statsVie OR $anciennesStats['stats_puissance'] !== $statsPuissance OR $anciennesStats['stats_vitesse'] !== $statsVitesse)
					{
						$_SESSION['msgModificationMascotte'][] = '<div>
							<p class="d-inline-block fs-5 mb-0"><a href="/mascotte/'.$idMascotte.'-'.slug($mascotteNom, '').'" class="fw-bold '.strtolower($mascotteQualite).'">'.$mascotteNom.'</a> <a class="btn btn-primary btn-sm ms-2" data-bs-toggle="collapse" href="#collapse'.slug($mascotteNom).'" class="btn btn-outline-primary" role="button" aria-expanded="false" aria-controls="collapse'.slug($mascotteNom).'">+ Détails</a></p>
							<div class="collapse" id="collapse'.slug($mascotteNom).'">
								<div style="margin-top: 1.25rem;" class="card card-body box-mascotte-maj">
									'.($resAncienne['nom_mascotte'] !== $mascotteNom			? '<div><label class="mb-2">Nom de la mascotte</label><div><span>'.(!empty($resAncienne['nom_mascotte']) ? $resAncienne['nom_mascotte'] : 'n/a').'</span><span>→</span><span>'.$mascotteNom.'</span></div></div>'				: null).'
									'.($resAncienne['niveau'] !== $mascotteNiveau				? '<div><label class="mb-2">Niveau</label><div><span>'.(!empty($resAncienne['niveau']) ? $resAncienne['niveau'] : '1').'</span><span>→</span><span>'.$mascotteNiveau.'</span></div></div>'										: null).'
									'.($resAncienne['type_qualite'] !== $mascotteTypeQualite	? '<div><label class="mb-2">Qualité</label><div><span>'.(!empty($resAncienne['type_qualite']) ? $resAncienne['type_qualite'] : 'n/a').'</span><span>→</span><span>'.$mascotteQualite.'</span></div></div>'						: null).'
									'.($resAncienne['qualite'] !== $mascotteQualite				? '<div><label class="mb-2">Qualité</label><div><span>'.(!empty($resAncienne['qualite']) ? $resAncienne['qualite'] : 'n/a').'</span><span>→</span><span>'.$mascotteQualite.'</span></div></div>'								: null).'
									'.($resAncienne['stats_race_id'] !== $statsRaceId			? '<div><label class="mb-2"> Race ID</label><div><span>'.(!empty($resAncienne['stats_race_id']) ? $resAncienne['stats_race_id'] : 'n/a').'</span><span>→</span><span>'.$statsRaceId.'</span></div></div>'						: null).'
									'.($resAncienne['stats_vie'] !== $statsVie					? '<div><label class="mb-2">Statistique Points de vie</label><div><span>'.(!empty($resAncienne['stats_vie']) ? $resAncienne['stats_vie'] : 'n/a').'</span><span>→</span><span>'.$statsVie.'</span></div></div>'					: null).'
									'.($resAncienne['stats_puissance'] !== $statsPuissance		? '<div><label class="mb-2">Statistique Puissance</label><div><span>'.(!empty($resAncienne['stats_puissance']) ? $resAncienne['stats_puissance'] : 'n/a').'</span><span>→</span><span>'.$statsPuissance.'</span></div></div>'	: null).'
									'.($resAncienne['stats_vitesse'] !== $statsVitesse			? '<div><label class="mb-2">Statistique Vitesse</label><div><span>'.(!empty($resAncienne['stats_vitesse']) ? $resAncienne['stats_vitesse'] : 'n/a').'</span><span>→</span><span>'.$statsVitesse.'</span></div></div>'			: null).'
								</div>
							</div>
						</div>';
					}
				}

				try {
					$stmt = $pdo->prepare('INSERT INTO wow_mascottes_u (id, id_utilisateur, id_blizzard, id_serveur, nom_serveur, nom_personnage, creature_id, nom_mascotte, niveau, type_qualite, qualite, stats_race_id, stats_vie, stats_puissance, stats_vitesse)
					VALUES (:id, :id_utilisateur, :id_blizzard, :id_serveur, :nom_serveur, :nom_personnage, :creature_id, :nom_mascotte, :niveau, :type_qualite, :qualite, :stats_race_id, :stats_vie, :stats_puissance, :stats_vitesse)
					ON DUPLICATE KEY UPDATE id = VALUES(id), id_utilisateur = VALUES(id_utilisateur), id_serveur = VALUES(id_serveur), nom_serveur = VALUES(nom_serveur), nom_personnage = VALUES(nom_personnage), creature_id = VALUES(creature_id), nom_mascotte = VALUES(nom_mascotte), niveau = VALUES(niveau), type_qualite = VALUES(type_qualite), qualite = VALUES(qualite), stats_race_id = VALUES(stats_race_id), stats_vie = VALUES(stats_vie), stats_puissance = VALUES(stats_puissance), stats_vitesse = VALUES(stats_vitesse)');

					$stmt->execute([
						'id' => (int) $idMascotte,
						'id_utilisateur' => (int) $_SESSION['id_utilisateur'],
						'id_blizzard' => (int) $idBlizzard,
						'id_serveur' => $idServeurJson ?? null,
						'nom_serveur' => $nomServeurJson ?? null,
						'nom_personnage' => $nomPersonnageForm ?? null,
						'creature_id' => isset($val->creature_display->id) ? (int) $val->creature_display->id : null,
						'nom_mascotte' => (string) $mascotteNom,
						'niveau' => (int) $mascotteNiveau,
						'type_qualite' => (string) $mascotteTypeQualite,
						'qualite' => (string) $mascotteQualite,
						'stats_race_id' => (int) $statsRaceId,
						'stats_vie' => (int) $statsVie,
						'stats_puissance' => (int) $statsPuissance,
						'stats_vitesse' => (int) $statsVitesse,
					]);

					if($stmt->rowCount() === 1)
						$_SESSION['msgAjoutMascotte'][] = '<p class="fs-5">#'.$idMascotte.' <a href="/mascotte/'.$idMascotte.'-'.slug($mascotteNom, '').'" class="fw-bold '.strtolower($mascotteQualite).'">'.$mascotteNom.'</a></p>';

				} catch (\PDOException $e) { }
			}
		}

		if(empty($_SESSION['msgModificationMascotte']))
			setFlash('success', 'Toutes les mascottes sont à jour !');
	}

	else
		setFlash('danger', 'Erreur : serveur ou pseudo incorrect');

	header('Location: /mascottes'.(isset($_GET['mesMascottes']) ? '?mesMascottes' : null));
	exit;
}

$trier = mb_strtolower($_GET['trier'] ?? 'nom_mascotte');
$trierPar = in_array(mb_strtolower($_GET['trierpar'] ?? ''), ['asc', 'desc'], true) ? mb_strtolower($_GET['trierpar']) : 'asc';

$limite = in_array($_GET['limite'] ?? '', [20, 50, 100, 250]) ? secu($_GET['limite']) : 20;
$decalage = ($page - 1) * $limite;

$fTrier = !empty($limite) ? '&trier='.$trier.'&trierPar='.$trierPar : null;
$fLimite = !empty($limite) ? '&limite='.$limite : null;
$fMesMascottes = isset($_GET['mesMascottes']) ? '&mesMascottes' : null;
$fPage = !empty($page) ? '&page='.$page : null;
$fChercherMascotte = !empty($_GET['chercher_mascotte']) ? '&chercher_mascotte='.secuChars($_GET['chercher_mascotte']) : null;

if(!empty($resUtilisateur['est_confirmer']) AND $resUtilisateur['est_confirmer'] == 1 AND isset($_GET['mesMascottes']))
{
	$trier = in_array($trier, ['id', 'nom_mascotte', 'nom_mascotte_en', 'niveau', 'qualite', 'source_type_name'], true) ? $trier : 'nom_mascotte';
	$where = !empty($_GET['chercher_mascotte']) ? ' AND u.nom_mascotte LIKE :chercher_mascotte' : null;

	try {
		$stmt = $pdo->prepare('SELECT u.id, u.id_blizzard, u.nom_personnage, u.niveau AS niveau, u.type_qualite, u.qualite AS qualite, u.stats_race_id, u.stats_vie, u.stats_puissance, u.stats_vitesse,
		m.id AS id_mascotte_global, m.nom_mascotte, m.nom_mascotte_en, m.battle_pet_type_name, m.description_mascotte, m.source_type_name, m.icon
		FROM wow_mascottes_u u
		LEFT JOIN wow_mascottes m ON u.id = m.id
		WHERE u.id_utilisateur = :id_utilisateur '.$where.'
		ORDER BY '.$trier.' '.$trierPar.'
		LIMIT '.$limite.' OFFSET '.$decalage);

		!empty($_GET['chercher_mascotte']) ? $stmt->bindValue(':chercher_mascotte', '%'.$_GET['chercher_mascotte'].'%', PDO::PARAM_STR) : null;
		$stmt->bindValue(':id_utilisateur', $_SESSION['id_utilisateur'], PDO::PARAM_INT);
		$stmt->execute();

		$resMascottes = $stmt->fetchAll();
	} catch (\PDOException $e) { }

	try {
		$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM wow_mascottes_u u LEFT JOIN wow_mascottes m ON u.id = m.id WHERE u.id_utilisateur = :id_utilisateur'.$where);
		!empty($_GET['chercher_mascotte']) ? $stmt->bindValue(':chercher_mascotte', '%'.$_GET['chercher_mascotte'].'%', PDO::PARAM_STR) : null;
		$stmt->bindValue(':id_utilisateur', $_SESSION['id_utilisateur'], PDO::PARAM_INT);
		$stmt->execute();

		$resMascottesNb = $stmt->fetchColumn();
	} catch (\PDOException $e) { }
}

else
{
	$trier = in_array($trier, ['id', 'nom_mascotte', 'nom_mascotte_en', 'source_type_name'], true) ? $trier : 'nom_mascotte';
	$where = !empty($_GET['chercher_mascotte']) ? ' WHERE nom_mascotte LIKE :chercher_mascotte' : null;

	try {
		$stmt = $pdo->prepare('SELECT * FROM wow_mascottes '.$where.' ORDER BY '.$trier.' '.$trierPar.' LIMIT '.$limite.' OFFSET '.$decalage);
		!empty($_GET['chercher_mascotte']) ? $stmt->bindValue(':chercher_mascotte', '%'.$_GET['chercher_mascotte'].'%', PDO::PARAM_STR) : null;
		$stmt->execute();
		$resMascottes = $stmt->fetchAll();
	} catch (\PDOException $e) { }

	try {
		$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM wow_mascottes '.$where);
		!empty($_GET['chercher_mascotte']) ? $stmt->bindValue(':chercher_mascotte', '%'.$_GET['chercher_mascotte'].'%', PDO::PARAM_STR) : null;
		$stmt->execute();
		$resMascottesNb = $stmt->fetchColumn();
	} catch (\PDOException $e) { }

	if($resMascottesNb === 1)
	{
		header('Location: /mascotte/'.$resMascottes[0]['id'].'-'.$resMascottes[0]['nom_mascotte_slug']);
		exit;
	}
}

echo '<h1><a href="/mascottes">Mascottes</a></h1>';

if(!empty($resUtilisateur['est_confirmer']) AND $resUtilisateur['est_confirmer'] == 1)
{
	if(!empty($_SESSION['msgAjoutMascotte']))
	{
		$nbAjoutMascotte = count($_SESSION['msgAjoutMascotte']);

		echo '<div class="position-relative border rounded mb-4 p-3 p-lg-4 mascotte-ajout">
			<span class="position-absolute top-0 start-0 translate-middle-y mx-3 px-3 text-white fw-bold fs-4" style="background-color: rgba(26,28,35, 1);">Nouvelle'.s($nbAjoutMascotte).' mascotte'.s($nbAjoutMascotte).'</span>';

			for($i = 0; $i < $nbAjoutMascotte; $i++)
			{
				echo $_SESSION['msgAjoutMascotte'][$i];

				if($i > 10)
				{
					echo '<p class="fs-5">. . .</p>';

					echo alerte('info', '<span class="fw-bold">'.$nbAjoutMascotte.'</span> mascottes ajoutées !', 'col-12 col-lg-8 mb-4 mt-5');

					break;
				}
			}

		echo '</div>';
	}

	if(!empty($_SESSION['msgModificationMascotte']))
	{
		$nbModificationMascotte = count($_SESSION['msgModificationMascotte']);

		echo '<div class="position-relative border rounded mb-4 p-3 p-lg-4 mascotte-maj">
			<span class="position-absolute top-0 start-0 translate-middle-y mx-3 px-3 text-white fw-bold fs-4" style="background-color: rgba(26,28,35, 1);">Changement'.s($nbModificationMascotte).' mascotte'.s($nbModificationMascotte).'</span>';

			for($i = 0; $i < $nbModificationMascotte; $i++)
			{
				echo $_SESSION['msgModificationMascotte'][$i];

				if($i > 10)
				{
					echo '<p class="fs-5">. . .</p>';

					echo alerte('info', '<span class="fw-bold">'.$nbModificationMascotte.'</span> mascottes modifiées !', 'col-12 col-lg-8 mb-4 mt-5');

					break;
				}
			}

		echo '</div>';
	}

	(!empty($_POST['id_serveur_form']) AND !empty($_POST['nom_personnage_form']) AND empty($_SESSION['msgAjoutMascotte']) AND empty($_SESSION['msgModificationMascotte'])) ? alerte('success', 'Toutes les mascottes sont à jour') : null;

	p($resUtilisateur);
	if(!empty($resUtilisateur['nom_personnage']) AND !$resUtilisateur['est_confirmer'])	echo alerte('info', 'Vous devez <a href="/profil/ajouter-personnage" class="text-dark">confirmer votre personnage</a> pour afficher vos mascottes');
	elseif(empty($resUtilisateur['nom_personnage']))									echo alerte('info', 'Vous devez <a href="/profil/ajouter-personnage" class="text-dark">ajouter un personnage</a> avant d’afficher vos mascottes');
	else
	{
		try {
			$stmt = $pdo->prepare('SELECT date_maj FROM wow_mascottes_u WHERE id_utilisateur = :id_utilisateur ORDER BY date_maj DESC LIMIT 1');
			$stmt->execute([':id_utilisateur' => (int) $_SESSION['id_utilisateur']]);
			$resDerniereMaj = $stmt->fetch();
		} catch (\PDOException $e) { }
	}
}

echo '<div class="container" id="mascottes">
	<div class="row border rounded mb-4 py-4">
		<div id="filtres-mobile" class="d-flex d-lg-none row justify-content-center mb-4">
			<div class="col-auto">
				<div class="dropdown">
					<button type="button" style="min-width: 115px;" class="btn btn-light dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">Filtres</button>
					<ul class="dropdown-menu">
						<li><a href="/mascottes?trier=id&trierpar='.(($trier === 'id' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="dropdown-item'.($trier === 'id' ? ' active' : null).'">'.($trier === 'id' ? ' <i class="fa-solid fa-check"></i> ' : null).'# ID '.trierIcone('id', $trier, $trierPar).'</a></li>
						<li>'.(isset($_GET['mesMascottes']) ? '<a href="/mascottes?trier=niveau&trierpar='.(($trier === 'niveau' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="dropdown-item'.($trier === 'niveau' ? ' active' : null).'">'.($trier === 'niveau' ? ' <i class="fa-solid fa-check"></i> ' : null).'Niveau '.trierIcone('niveau', $trier, $trierPar).'</a>' : '<span class="btn btn-light disabled curseur-desactive fw-bold" tabindex="-1" data-bs-toggle="tooltip" data-bs-title="Uniquement disponible sur Mes mascottes">Niveau</span>').'
						<li><a href="/mascottes?trier=nom_mascotte&trierpar='.(($trier === 'nom_mascotte' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="dropdown-item'.($trier === 'nom_mascotte' ? ' active' : null).'">'.($trier === 'nom_mascotte' ? ' <i class="fa-solid fa-check"></i> ' : null).isoEmoji('fr').' Nom '.trierIcone('nom_mascotte', $trier, $trierPar).'</a></li>
						<li><a href="/mascottes?trier=nom_mascotte_en&trierpar='.(($trier === 'nom_mascotte_en' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="dropdown-item'.($trier === 'nom_mascotte_en' ? ' active' : null).'">'.($trier === 'nom_mascotte_en' ? ' <i class="fa-solid fa-check"></i> ' : null).isoEmoji('gb').' Nom '.trierIcone('nom_mascotte_en', $trier, $trierPar).'</a></li>
						<li>'.(isset($_GET['mesMascottes']) ? '<a href="/mascottes?trier=qualite&trierpar='.(($trier === 'qualite' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="dropdown-item'.($trier === 'qualite' ? ' active' : null).'">'.($trier === 'qualite' ? ' <i class="fa-solid fa-check"></i> ' : null).'Qualité '.trierIcone('qualite', $trier, $trierPar).'</a>' : '<span class="btn btn-light disabled curseur-desactive fw-bold" tabindex="-1" data-bs-toggle="tooltip" data-bs-title="Uniquement disponible sur Mes mascottes">Qualité</span>').'
						<li><a href="/mascottes?trier=source_type_name&trierpar='.(($trier === 'source_type_name' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="dropdown-item'.($trier === 'source_type_name' ? ' active' : null).'">'.($trier === 'source_type_name' ? ' <i class="fa-solid fa-check"></i> ' : null).'Source '.trierIcone('source_type_name', $trier, $trierPar).'</a></li>
					</ul>
				</div>
			</div>
			<div class="col-auto">
				<div class="dropdown">
					<button type="button" style="min-width: 115px;" class="btn btn-light dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">Par page</button>
					<ul class="dropdown-menu">
						<li><a href="/mascottes?limite=20'.$fTrier.$fMesMascottes.$fChercherMascotte.'" class="dropdown-item'.($limite === 20 ? ' active' : null).'">'.($limite === 20 ? '<i class="fa-solid fa-check"></i> ' : null).'20</a></li>
						<li><a href="/mascottes?limite=50'.$fTrier.$fMesMascottes.$fChercherMascotte.'" class="dropdown-item'.($limite === 50 ? ' active' : null).'">'.($limite === 50 ? '<i class="fa-solid fa-check"></i> ' : null).'50</a></li>
						<li><a href="/mascottes?limite=100'.$fTrier.$fMesMascottes.$fChercherMascotte.'" class="dropdown-item'.($limite === 100 ? ' active' : null).'">'.($limite === 100 ? '<i class="fa-solid fa-check"></i> ' : null).'100</a></li>
						<li><a href="/mascottes?limite=250'.$fTrier.$fMesMascottes.$fChercherMascotte.'" class="dropdown-item'.($limite === 250 ? ' active' : null).'">'.($limite === 250 ? '<i class="fa-solid fa-check"></i> ' : null).'250</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div id="filtres-bureau" class="d-lg-flex d-none row text-center">
			<div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
				<a href="/mascottes?trier=id&trierpar='.(($trier === 'id' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="btn btn-light fw-bold'.($trier === 'id' ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Trier par ID">'.($trier === 'id' ? ' <i class="fa-solid fa-check"></i> ' : null).'# ID '.trierIcone('id', $trier, $trierPar).'</a>
				'.(isset($_GET['mesMascottes']) ? '<a href="/mascottes?trier=niveau&trierpar='.(($trier === 'niveau' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="btn btn-light fw-bold'.($trier === 'niveau' ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Trier par Niveau">'.($trier === 'niveau' ? ' <i class="fa-solid fa-check"></i> ' : null).'Niveau '.trierIcone('niveau', $trier, $trierPar).'</a>' : '<span class="btn btn-light disabled curseur-desactive fw-bold" tabindex="-1" data-bs-toggle="tooltip" data-bs-title="Uniquement disponible sur Mes mascottes">Niveau</span>').'
				<a href="/mascottes?trier=nom_mascotte&trierpar='.(($trier === 'nom_mascotte' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="btn btn-light fw-bold'.($trier === 'nom_mascotte' ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Trier par Nom mascotte '.isoEmoji('fr').'">'.($trier === 'nom_mascotte' ? ' <i class="fa-solid fa-check"></i> ' : null).isoEmoji('fr').' Nom '.trierIcone('nom_mascotte', $trier, $trierPar).'</a>
				<a href="/mascottes?trier=nom_mascotte_en&trierpar='.(($trier === 'nom_mascotte_en' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="btn btn-light fw-bold'.($trier === 'nom_mascotte_en' ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Trier par Nom mascotte '.isoEmoji('gb').'">'.($trier === 'nom_mascotte_en' ? ' <i class="fa-solid fa-check"></i> ' : null).isoEmoji('gb').' Nom '.trierIcone('nom_mascotte_en', $trier, $trierPar).'</a>
				'.(isset($_GET['mesMascottes']) ? '<a href="/mascottes?trier=qualite&trierpar='.(($trier === 'qualite' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="btn btn-light fw-bold'.($trier === 'qualite' ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Trier par Qualité">'.($trier === 'qualite' ? ' <i class="fa-solid fa-check"></i> ' : null).'Qualité '.trierIcone('qualite', $trier, $trierPar).'</a>' : '<span class="btn btn-light disabled curseur-desactive fw-bold" tabindex="-1" data-bs-toggle="tooltip" data-bs-title="Uniquement disponible sur Mes mascottes">Qualité</span>').'
				<a href="/mascottes?trier=source_type_name&trierpar='.(($trier === 'source_type_name' AND $trierPar === 'asc') ? 'desc' : 'asc').$fLimite.$fMesMascottes.$fPage.$fChercherMascotte.'" class="btn btn-light fw-bold'.($trier === 'source_type_name' ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Trier par Source">'.($trier === 'source_type_name' ? ' <i class="fa-solid fa-check"></i> ' : null).'Source '.trierIcone('source_type_name', $trier, $trierPar).'</a>
			</div>

			<div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
				<a href="/mascottes?limite=20'.$fTrier.$fMesMascottes.$fChercherMascotte.'" class="btn btn-light fw-bold'.($limite === 20 ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Lister 20 mascottes">'.($limite === 20 ? '<i class="fa-solid fa-check"></i> ' : null).'20</a>
				<a href="/mascottes?limite=50'.$fTrier.$fMesMascottes.$fChercherMascotte.'" class="btn btn-light fw-bold'.($limite === 50 ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Lister 50 mascottes">'.($limite === 50 ? '<i class="fa-solid fa-check"></i> ' : null).'50</a>
				<a href="/mascottes?limite=100'.$fTrier.$fMesMascottes.$fChercherMascotte.'" class="btn btn-light fw-bold'.($limite === 100 ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Lister 100 mascottes">'.($limite === 100 ? '<i class="fa-solid fa-check"></i> ' : null).'100</a>
				<a href="/mascottes?limite=250'.$fTrier.$fMesMascottes.$fChercherMascotte.'" class="btn btn-light fw-bold'.($limite === 250 ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Lister 250 mascottes">'.($limite === 250 ? '<i class="fa-solid fa-check"></i> ' : null).'250</a>
			</div>
		</div>

		<div>
			'.(!empty($resUtilisateur) ? '<div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
				'.((!empty($resUtilisateur['est_confirmer']) AND $resUtilisateur['est_confirmer'] == 1) ? '<a href="?mesMascottes" class="btn btn-light fw-bold'.(isset($_GET['mesMascottes']) ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Lister Mes Mascottes">'.(isset($_GET['mesMascottes']) ? '<i class="fa-solid fa-check"></i> ' : null).'Mes Mascottes</a>' : '<span class="btn btn-light disabled curseur-desactive fw-bold" tabindex="-1">Mes Mascottes</span>').'
				<a href="/mascottes" class="btn btn-light fw-bold'.(($resUtilisateur['est_confirmer'] == 0 OR !isset($_GET['mesMascottes'])) ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Lister Toutes les Mascottes">'.(($resUtilisateur['est_confirmer'] == 0 OR !isset($_GET['mesMascottes'])) ? ' <i class="fa-solid fa-check"></i> ' : null).'Toutes les Mascottes</a>
			</div>' : null).'

			<form action="/mascottes" method="get" id="chercherMascotte">
				'.(isset($_GET['mesMascottes']) ? '<input type="hidden" name="mesMascottes">' : null).'
				<div class="col-12 col-lg-3 mx-auto">
					<div class="input-group">
						<input type="text" name="chercher_mascotte"'.(!empty($_GET['chercher_mascotte']) ? ' value="'.secuChars($_GET['chercher_mascotte']).'"' : null).' class="form-control" placeholder="Nom de la mascotte" required>
						<button type="submit" class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
					</div>
				</div>
			</form>
		</div>';

		echo '<div class="mt-4 text-center">';

			if(!empty($resUtilisateur['nom_personnage']) AND !empty($resUtilisateur['id_serveur']))
			{
				$nomPersonnage = secuChars($resUtilisateur['nom_personnage']);
				echo '<form action="/mascottes'.(isset($_GET['mesMascottes']) ? '?mesMascottes' : null).'" method="post" id="majMascottes">
					<input type="hidden" value="'.serveurInfos($pdo, $resUtilisateur['id_serveur'])['id_blizzard'].'" name="id_serveur_form">
					<input type="hidden" value="'.$nomPersonnage.'" name="nom_personnage_form">';

					echo '<div class="d-flex align-items-center">
						<button type="submit" class="btn btn-outline-light '.(!empty($resDerniereMaj) ? 'ms-auto' : 'mx-auto').'" data-bs-toggle="tooltip" data-bs-title="<span class=\''.slug($resUtilisateur['classe_nom']).'\'>'.$nomPersonnage.'</span> @ '.serveurInfos($pdo, $resUtilisateur['id_serveur'])['nom'].'">Mettre à jour mes mascottes</button>';
						echo (!empty($resDerniereMaj) ? '<i class="fa-solid fa-circle-info fa-xl me-auto ms-3" data-bs-toggle="tooltip" data-bs-title="Dernière mise à jour le '.dateFormat($resDerniereMaj['date_maj'], 'c').'"></i>' : null).'
					</div>
				</form>';
			}

		echo '</div>
	</div>';

	if($resMascottesNb > 0)
	{
		$i = 1;
		$liensMascottes = [];
		foreach($resMascottes as $m)
		{
			$id						= !empty($m['id'])						? secu($m['id'])						: null;
			$nomMascotte			= !empty($m['nom_mascotte'])			? secuChars($m['nom_mascotte'])			: 'n/a';
			$slugMascotte			= !empty($m['nom_mascotte'])			? slug($nomMascotte)					: 'mascotte';
			$nomMascotteEn			= !empty($m['nom_mascotte_en'])			? secuChars($m['nom_mascotte_en'])		: null;
			$slugNomMascotteEn		= !empty($m['nom_mascotte_en'])			? slug($m['nom_mascotte_en'])			: null;
			$niveau					= !empty($m['niveau'])					? secu($m['niveau'])					: null;
			$qualite				= !empty($m['qualite'])					? secuChars($m['qualite'])				: 'commun';
			$descriptionMascotte	= !empty($m['description_mascotte'])	? secuChars($m['description_mascotte'])	: 'Description inconnue';
			$typeMascotte			= !empty($m['battle_pet_type_name'])	? secuChars($m['battle_pet_type_name'])	: 'n/a';
			$sourceMascotte			= !empty($m['source_type_name'])		? secuChars($m['source_type_name'])		: 'n/a';
			$creatureId				= !empty($m['creature_id'])				? secu($m['creature_id'])				: null;
			$icone					= !empty($m['icon'])					? secuChars($m['icon'])					: '/assets/img/mascotte-manquante.png';

			$liensMascottes[] = '/mascotte/'.$id.'-'.$slugMascotte;

			$listeMascottes[] = '<div class="row border rounded mb-3 py-3 text-center liste-mascottes" id="m'.$id.'">
				<div class="col-2 col-lg-3">
					<div class="mascotte-icone"><img src="'.$icone.'" class="mascotte-media img-fluid" alt="Icône '.$nomMascotte.'" title="Icône '.$nomMascotte.'"><span>'.$niveau.'</span></div>
					<div class="d-block d-lg-none fs-6 fw-bold text-center">#'.$id.'</div>
					<div class="d-none d-lg-block fs-4 fw-bold text-center">#'.$id.'</div>
				</div>
				<div class="col-4 col-lg-3 text-start">
					<a href="/mascotte/'.$id.'-'.$slugMascotte.'" class="d-inline-block d-lg-none link-offset-2 fs-6 '.strtolower($qualite).'"'.(estAdmin() ? ' onclick="hide(\'#m'.$id.'\');"' : null).'>'.$nomMascotte.'</a>
					<a href="/mascotte/'.$id.'-'.$slugMascotte.'" class="d-none d-lg-inline-block link-offset-2 fs-3 '.strtolower($qualite).'"'.(estAdmin() ? ' onclick="hide(\'#m'.$id.'\');"' : null).'>'.$nomMascotte.'</a>

					<p class="mb-0"><span class="me-2">'.isoEmoji('gb').'</span><span>'.$nomMascotteEn.'</span></p>
				</div>
				<div class="col-3 col-lg-3">
					<span class="d-inline-block d-lg-none fs-6">'.$typeMascotte.'</span>
					<span class="d-none d-lg-inline-block fs-3">'.$typeMascotte.'</span>
				</div>
				<div class="col-3 col-lg-3">
					<span class="d-inline-block d-lg-none fs-6">'.$sourceMascotte.'</span>
					<span class="d-none d-lg-inline-block fs-3">'.$sourceMascotte.'</span>
				</div>
				<div class="col-12 mt-4 text-start">'.$descriptionMascotte.'</div>
				<div class="col-12 d-flex flex-wrap justify-content-center gap-1 mt-4">
					<a href="https://www.wowhead.com/fr/battle-pet/'.$id.'" class="btn btn-outline-info" title="Fiche de '.$nomMascotte.' sur Wowhead" '.$onclick.'>Wowhead <i class="fa-solid fa-up-right-from-square"></i></a>
					'.(!empty($nomMascotteEn) ? '<a href="https://www.warcraftpets.com/search/?q='.urlencode($m['nom_mascotte_en']).'" class="btn btn-outline-info" title="Fiche de '.$nomMascotte.' sur WarcraftPets.com" '.$onclick.'>WarcraftPets <i class="fa-solid fa-up-right-from-square"></i></a>' : '<span class="btn btn-outline-secondary" title="Fiche '.$nomMascotte.' sur WarcraftPets.com">WarcraftPets</span>').'
					'.((!empty($creatureId) AND !empty($slugNomMascotteEn)) ? '<a href="https://www.wowdb.com/npcs/'.$creatureId.'-'.$slugNomMascotteEn.'" class="btn btn-outline-info" title="Fiche de '.$nomMascotte.' sur WoWDB.com" '.$onclick.'>WoWDB <i class="fa-solid fa-up-right-from-square"></i></a>' : '<span class="btn btn-outline-secondary" title="Fiche '.$nomMascotte.' sur WoWDB.com">WoWDB</span>').'
				</div>
			</div>';
		}

		if(estAdmin() AND !empty($liensMascottes))
		{
			echo '<p class="text-center my-4"><a href="#" class="btn btn-outline-warning" onclick="ouvrirTous(); return false;">Ouvrir les '.count($liensMascottes).' liens</a></p>

			<script>
			function ouvrirTous() {
				const urls = [';

					foreach($liensMascottes as $l)
						echo '"'.$l.'",'."\n";

				echo '];
				for (const url of urls) {
					window.open(url, \'_blank\');
				}
			}
			</script>';
		}

		echo count($listeMascottes) > 0 ? implode($listeMascottes) : alerte('danger', 'Aucune mascotte trouvée');

		echo '<p class="mb-0 mt-4 text-end fs-3"><span class="fw-bold">'.$resMascottesNb.'</span> mascotte'.s($resMascottesNb).'</p>';

		$p = new Paginator($resMascottesNb, $limite, $page, '?page=(:num)'.$fTrier.$fLimite.$fMesMascottes.$fChercherMascotte);
		echo $p;
	}

	else
		echo alerte('danger', 'Aucune mascotte trouvée', 'col-12 col-lg-8 mt-5');

echo '</div>';

unset($_SESSION['msgAjoutMascotte']);
unset($_SESSION['msgModificationMascotte']);

require_once 'a_footer.php';