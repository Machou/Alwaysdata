<?php
require_once '../config/wow_config.php';

if(estConnecte($pdo) AND isset($_GET['majEncheres']))
{
	try {
		$stmt = $pdo->prepare('UPDATE wow_objets_encheres_temps SET date_maj = DATE_SUB(date_maj, INTERVAL 6 HOUR) WHERE id_utilisateur = :id_utilisateur AND id_serveur = :id_serveur');
		$stmt->execute([
			'id_utilisateur' => (int) $_SESSION['id_utilisateur'],
			'id_serveur' => (int) $idServeur
		]);
	} catch (\PDOException $e) { }

	setFlash('success', 'Enchères mises à jour pour le serveur <span class="fw-bold">'.$nomServeur.'</span>');

	header('Location: /encheres-serveur/'.$idServeur.'-'.slug(serveurInfos($pdo, $idServeur)['nom']));
	exit;
}

if(estConnecte($pdo))
{
	require_once 'a_body.php';

	$idServeur = !empty($_GET['id_serveur']) ? secu($_GET['id_serveur']) : null;

	if(!empty($idServeur))
	{
		if($idServeur == $idConnecte)
		{
			$trier = $_GET['trier'] ?? 'prix';
			$trierPar = in_array(mb_strtolower($_GET['trierpar'] ?? ''), ['asc', 'desc'], true) ? mb_strtolower($_GET['trierpar']) : 'asc';

			$colonnesTriables = [
				'nom' => 'o.nom',
				'prix' => 'e.prix',
				'temps_restant' => 'e.temps_restant',
			];

			$trier = strtolower((string) $trier);
			if(!isset($colonnesTriables[$trier])) { $trier = 'prix'; }

			echo '<h1 class="mb-5"><span class="curseur">'.$paysServeur.'</span> <a href="/encheres-serveur/'.$idServeur.'-'.$slugNomServeur.'" title="Serveur '.$nomServeur.'">'.$nomServeur.'</a></h1>

			<div class="text-center mb-4"><a href="?majEncheres" class="link-success border border-success rounded me-2 p-2">Mettre à jour les enchères sur <span class="fw-bold">'.$nomServeur.'</a></div>';

			try {
				$stmt = $pdo->prepare('SELECT e.id_enchere, e.id_serveur, e.prix, e.quantite, e.temps_restant, o.id AS id_objet, o.nom, o.id_blizzard, o.id_mascotte, o.date_ajout FROM wow_objets_encheres AS e JOIN wow_objets AS o ON o.id = e.id_objet WHERE e.id_serveur = :id_serveur ORDER BY '.$trier.' '.$trierPar);
				$stmt->execute(['id_serveur' => (int) $idServeur]);
				$res = $stmt->fetchAll();
			} catch (\PDOException $e) { }

			if(!empty($res[0]['id_objet']))
			{
				echo '<div class="container text-center">
					<div class="row row-entete border rounded px-0 py-3 mb-3">
						<div class="col-4	col-lg-6"><a href="?trier=nom&trierpar='.(($trier === 'nom' AND $trierPar === 'asc') ? 'desc' : 'asc').'" data-bs-toggle="tooltip" data-bs-title="Trier par Nom de l’objet">Nom</a> '.trierIcone('nom', $trier, $trierPar).'</div>
						<div class="col-3	col-lg-2"><a href="?trier=prix&trierpar='.(($trier === 'prix' AND $trierPar === 'asc') ? 'desc' : 'asc').'" data-bs-toggle="tooltip" data-bs-title="Trier par Prix de l’enchère">Prix</a> '.trierIcone('prix', $trier, $trierPar).'</div>
						<div class="col-3	col-lg-2"><a href="?trier=temps_restant&trierpar='.(($trier === 'temps_restant' AND $trierPar === 'asc') ? 'desc' : 'asc').'" data-bs-toggle="tooltip" data-bs-title="Trier par Temps restant"><span class="d-inline-block d-lg-none text-decoration-underline">Temps</span><span class="d-none d-lg-inline-block text-decoration-underline">Temps restant</span></a> '.trierIcone('temps_restant', $trier, $trierPar).'</div>
						<div class="col-1	col-lg-2"></div>
					</div>
				</div>';

				foreach($res as $c => $v)
				{
					$idObjet		= (int) $v['id_objet'];
					$idBlizzard		= !empty($v['id_blizzard'])	? (int) $v['id_blizzard']					: 'n/a';
					$idMascotte		= !empty($v['id_mascotte'])	? (int) $v['id_mascotte']					: null;
					$idEnchere		= !empty($v['id_enchere'])	? secu($v['id_enchere'])					: 'n/a';
					$nomObjet		= !empty($v['nom'])			? secuChars($v['nom'])						: 'n/a';
					$nomObjetSlug	= !empty($v['nom'])			? slug($v['nom'])							: 'nom-inconnu';
					$prix			= !empty($v['prix'])		? convertirPieces($v['prix'])['or']			: 'n/a';
					$tempsRestant	= tempsRestant($v['temps_restant']);
					$dateAjout		= !empty($v['date_ajout'])	? dateFormat(strtotime($v['date_ajout']))	: 'n/a';

					echo '<div class="row border-bottom m-0 py-3 text-center">
						<div class="col-4	col-lg-6">
							'.(!empty($idMascotte) ? '<a href="/prix-objet/'.$idObjet.'-'.$nomObjetSlug.'" class="rare">'.$nomObjet.'</a>':
							'<a href="/prix-objet/'.$idObjet.'-'.$nomObjetSlug.'" data-wowhead="domain=fr&item='.$idBlizzard.'">'.$nomObjet.'</a>').'
						</div>
						<div class="col-3	col-lg-2">'.$prix.' '.PO.'</div>
						<div class="col-3	col-lg-2">'.$tempsRestant.'</div>
						<div class="col-1	col-lg-2">
							<i class="fa-solid fa-circle-info me-2 curseur" data-bs-custom-class="tooltip-gauche" data-bs-toggle="tooltip" data-bs-title="
								<span class=\'fw-bold\'>Date d’ajout</span> : '.$dateAjout.'<br><br>
								'.(!empty($idEnchere) ? '<span class=\'fw-bold\'>ID Enchère</span> : '.$idEnchere.'<br>' : null).'
								<span class=\'fw-bold\'>ID Objet</span> : '.$idBlizzard.'<br>
								'.(!empty($idMascotte) ? '<span class=\'fw-bold\'>ID Mascotte</span> : '.$idMascotte.'<br>' : null).'
								<br><span class=\'fw-bold\'>Serveur</span> : '.$nomServeur.'<br>
								<span class=\'fw-bold\'>ID Serveur</span> : '.$idServeur.'
							"></i>
							<a href="/profil-verification?supprimer_id='.$idObjet.'&id_serveur='.$idServeur.'&serveur" data-bs-toggle="tooltip" data-bs-title="Supprimer cet objet" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cet objet de mon inventaire ?\')"><i class="fa-solid fa-trash-can text-danger"></i></a>
						</div>
					</div>';
				}
			}

			else
				echo alerte('danger', 'Aucune enchère trouvée sur <span class="fw-bold">'.$nomServeur.'</span>.');
		}

		else
		{
			$infosServeur = serveurInfos($pdo, $idConnecte);

			echo alerte('danger', 'Ce serveur est connecté à <a href="/encheres-serveur/'.$infosServeur['id_connecte'].'-'.$infosServeur['slug'].'" class="text-danger">'.$infosServeur['nom'].'</a>');
		}
	}

	else
	{
		setFlash('danger', 'Objet introuvable');

		header('Location: /');
		exit;
	}

	require_once 'a_footer.php';
}

else
{
	header('Location: /');
	exit;
}