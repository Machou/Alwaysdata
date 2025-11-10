<?php
require_once '../config/wow_config.php';

if(estConnecte($pdo))
{
	require_once 'a_body.php';

	$idObjet = !empty($_GET['id_objet']) ? secu($_GET['id_objet']) : null;

	if(!empty($idObjet) AND !empty($nomObjet))
	{
		$trier = in_array($_GET['trier'] ?? '', ['id_enchere', 'prix', 'id_serveur', 'temps_restant']) ? secuChars($_GET['trier']) : 'prix';
		$trierPar = in_array(mb_strtolower($_GET['trierpar'] ?? ''), ['asc', 'desc'], true) ? mb_strtolower($_GET['trierpar']) : 'asc';

		echo '<h1><a href="/prix-objet/'.$idObjet.'-'.$slugNomObjet.'">'.$nomObjet.'</a></h1>

		<div class="d-flex flex-wrap justify-content-center flex-wrap gap-2 mb-4">
			<div data-bs-toggle="tooltip" data-bs-title="Ajouté le '.dateFormat($dateAjout).'">
				<span class="d-lg-inline-block d-none border rounded p-2">Ajouté le '.dateFormat($dateAjout).'</span>
				<span class="d-inline-block d-lg-none border rounded p-2">'.dateFormat($dateAjout, 's').'</span>
			</div>

			<span class="border rounded p-2" data-bs-toggle="tooltip" data-bs-title="ID de '.(!empty($idMascotte) ? 'la mascotte' : 'l’objet').' sur World of Warcraft : '.(!empty($idMascotte) ? $idMascotte : $idBlizzard).'">ID WoW : '.(!empty($idMascotte) ? $idMascotte : $idBlizzard).'</span>

			'.(!empty($idMascotte) ? '<span class="border border-warning rounded p-2" data-bs-toggle="tooltip" data-bs-title="Afficher la fiche de la mascotte sur HdV.Li"><a href="/mascotte/'.$idMascotte.'-'.$slugNomObjet.'" class="link-warning">'.$nomObjet.' <i class="fa-solid fa-up-right-from-square"></i></a></span>' : null).'

			<span class="border border-info rounded p-2" data-bs-toggle="tooltip" data-bs-title="Afficher la fiche de '.(!empty($idMascotte) ? 'la mascotte' : 'l’objet').' sur Wowhead"><a href="https://www.wowhead.com/fr/'.(!empty($idMascotte) ? 'battle-pet/'.$idMascotte : 'item='.$idBlizzard).'" class="link-info" '.$onclick.'>Wowhead <i class="fa-solid fa-up-right-from-square"></i></a></span>

			<div data-bs-toggle="tooltip" data-bs-title="Supprimer '.(!empty($idMascotte) ? 'la mascotte' : 'l’objet').' de mon inventaire">
				<span class="d-lg-inline-block d-none border border-danger rounded p-2"><a href="/profil-verification?supprimer_id='.$idObjet.'&id_serveur='.$idServeur.'" class="link-danger" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer '.(!empty($idMascotte) ? 'cette mascotte' : 'cet objet').' de mon inventaire ?\')"><i class="fa-solid fa-trash-can"></i> Supprimer '.(!empty($idMascotte) ? 'la mascotte' : 'l’objet').'</a></span>
				<span class="d-inline-block d-lg-none border border-danger rounded p-2"><a href="/profil-verification?supprimer_id='.$idObjet.'&id_serveur='.$idServeur.'" class="link-danger" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer '.(!empty($idMascotte) ? 'cette mascotte' : 'cet objet').' de mon inventaire ?\')"><i class="fa-solid fa-trash-can"></i></a></span>
			</div>
		</div>

		<p class="text-center mb-4">
			'.(!isset($_GET['russe']) ? '<a href="?russe" class="btn btn-outline-danger">Afficher les serveurs Russes</a>' : '<a href="/prix-objet/'.$idObjet.'-'.$slugNomObjet.'" class="btn btn-outline-warning">Masquer les serveurs Russes</a>').'
			'.(!isset($_GET['5000']) ? '<a href="?5000" class="btn btn-outline-success"data-bs-toggle="tooltip" data-bs-title="Afficher les prix supérieur à 5,000 Pièces d’or">Prix > 5,000 '.PO.'</a>' : null).'
		</p>';

		try {
			$russeFiltre = !isset($_GET['russe']) ? 'AND id_serveur NOT IN (1923, 1922, 1929, 1605, 1925, 1623, 1614, 1928, 1602, 1615, 1604)' : null;

			$stmt = $pdo->prepare('SELECT o.*, e.* FROM wow_objets o LEFT JOIN wow_objets_encheres e ON e.id_objet = o.id WHERE o.id = :id '.$russeFiltre.' ORDER BY '.$trier.' '.$trierPar);
			$stmt->execute(['id' => (int) $idObjet]);
			$res = $stmt->fetchAll();
		} catch (\PDOException $e) { }

		if(!empty($res[0]['id']) AND !empty($res[0]['id_enchere']))
		{
			echo '<div class="container text-center">
				<div class="row row-entete border rounded px-0 py-3 mb-3">
					<div class="		col-lg-3 d-none d-lg-inline-block"><a href="?trier=id_enchere&trierpar='.(($trier === 'id_enchere' AND $trierPar === 'asc') ? 'desc' : 'asc').'" data-bs-toggle="tooltip" data-bs-title="Trier par ID de l’enchère"><span class="d-inline-block d-lg-none text-decoration-underline">#</span><span class="d-none d-lg-inline-block text-decoration-underline">ID enchère</span></a> '.trierIcone('id_enchere', $trier, $trierPar).'</div>
					<div class="col-4	col-lg-3"><a href="?trier=prix&trierpar='.(($trier === 'prix' AND $trierPar === 'asc') ? 'desc' : 'asc').'" data-bs-toggle="tooltip" data-bs-title="Trier par Prix de l’enchère">Prix</a> '.trierIcone('prix', $trier, $trierPar).'</div>
					<div class="col-5	col-lg-3"><a href="?trier=id_serveur&trierpar='.(($trier === 'id_serveur' AND $trierPar === 'asc') ? 'desc' : 'asc').'" data-bs-toggle="tooltip" data-bs-title="Trier par Serveur">Serveur</a> '.trierIcone('id_serveur', $trier, $trierPar).'</div>
					<div class="col-3	col-lg-3"><a href="?trier=temps_restant&trierpar='.(($trier === 'temps_restant' AND $trierPar === 'asc') ? 'desc' : 'asc').'" data-bs-toggle="tooltip" data-bs-title="Trier par Temps restant"><span class="d-inline-block d-lg-none text-decoration-underline">Temps</span><span class="d-none d-lg-inline-block text-decoration-underline">Temps restant</span></a> '.trierIcone('temps_restant', $trier, $trierPar).'</div>
				</div>
			</div>';

			$objets = [];
			foreach($res as $c => $v)
			{
				if(isset($_GET['5000']))
				{
					$idEnchereObjet = !empty($v['id_enchere']) ? secu($v['id_enchere']) : 'n/a';
					$prix = convertirPieces($v['prix'])['or'];
					$infosServeurObjet = serveurInfos($pdo, $v['id_serveur']);
					$localeDrapeauObjet = ($infosServeurObjet['locale'] === 'enGB') ? 'gb' : strtolower(substr($infosServeurObjet['locale'], 0, 2));
					$paysServeurObjet = '<span class="me-1">'.isoEmoji($localeDrapeauObjet).'</span>';
					$idServeurObjet = $infosServeurObjet['id_blizzard'];
					$tempsRestant = tempsRestant($v['temps_restant']);

					$objets[] = '<div class="row border-bottom m-0 py-3 text-center">
						<div class="		col-lg-3 d-none d-lg-inline-block">'.$idEnchereObjet.'</div>
						<div class="col-4	col-lg-3">'.(!empty($prix) ? $prix.' '.PO : 'n/a').'</div>
						<div class="col-5	col-lg-3"'.($localeDrapeauObjet == 'ru' ? ' data-bs-toggle="tooltip" data-bs-title="Nom Cyrillique : '.secuChars($infosServeurObjet['nom_ru']).'"' : null).'>'.$paysServeurObjet.' <a href="/encheres-serveur/'.$idServeurObjet.'-'.slug($infosServeurObjet['nom']).'">'.$infosServeurObjet['nom'].'</a></div>
						<div class="col-3	col-lg-3">'.$tempsRestant.'</div>
					</div>';
				}
			}

			if(count($objets) > 0)
				echo implode($objets);

			else
			{
				echo alerte('danger', 'Aucun objet en dessous de 5,000 '.PO);

				echo '<script>setTimeout(function() {
					window.close();
				}, 50000);
				</script>';
			}
		}

		else
			echo alerte('danger', 'Aucune enchère trouvée pour <span class="fw-bold">'.$nomObjet.'</span>.<br><br>Parcourez quelques <a href="/serveurs" class="link-danger">serveurs</a> pour en trouver une !');
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