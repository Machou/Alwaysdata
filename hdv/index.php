<?php
require_once '../config/wow_config.php';

if(estConnecte($pdo))
{
	if($infosServeur['id_blizzard'] == $idServeur AND $idServeur != $infosServeur['id_connecte'])
		header('Location: /'.$infosServeur['id_connecte'].'-'.$infosServeur['slug']);

	if($idServeur == $idConnecte)
	{
		if(!empty($_GET['chercher_objet']))
		{
			try {
				$stmtChercherObjet = $pdo->prepare('SELECT * FROM wow_objets WHERE id_utilisateur = :id_utilisateur AND nom = :chercher_objet LIMIT 1');
				$stmtChercherObjet->execute([
					':id_utilisateur' => (int) $_SESSION['id_utilisateur'],
					':chercher_objet' => (string) $_GET['chercher_objet'],
				]);
				$stmtChercherObjet->execute();
				$resChercherObjet = $stmtChercherObjet->fetch();
			} catch (\PDOException $e) { }

			if(!empty($resChercherObjet['nom']))
				header('Location: /prix-objet/'.$resChercherObjet['id'].'-'.slug($resChercherObjet['nom']));
		}

		$mascotteSeulement = static function(object $enchere): bool {
			$item = $enchere->item ?? null;
			return is_object($item) AND property_exists($item, 'pet_species_id') AND $item->pet_species_id !== null AND $item->pet_species_id !== '' AND $item->pet_species_id !== 0;
		};

		$objetSeulement = static function(object $enchere) use ($mascotteSeulement): bool {
			$item = $enchere->item ?? null;
			return is_object($item) AND property_exists($item, 'id') AND $item->id !== null AND $item->id !== '' AND $item->id !== 0 AND !$mascotteSeulement($enchere);
		};

		$prixEnchere = static function (object $e): bool {
			if(isset($e->buyout) AND $e->buyout !== null) { return true; }
			if(isset($e->unit_price) AND $e->unit_price !== null) { return true; }
			return false;
		};

		try {
			$stmtDateEncheres = $pdo->prepare('SELECT * FROM wow_objets_encheres_temps WHERE id_utilisateur = :id_utilisateur AND id_serveur = :id_serveur');
			$stmtDateEncheres->execute([
				':id_utilisateur' => (int) $_SESSION['id_utilisateur'],
				':id_serveur' => (int) $idServeur,
			]);
			$resDateEncheres = $stmtDateEncheres->fetch();
		} catch (\PDOException $e) { }

		try {
			$stmt = $pdo->prepare('SELECT id AS id_objet, id_blizzard, id_mascotte, nom, date_ajout FROM wow_objets WHERE id_utilisateur = :id_utilisateur ORDER BY id');
			$stmt->execute(['id_utilisateur' => (int) $_SESSION['id_utilisateur']]);
			$resObjetsBdd = $stmt->fetchAll();
		} catch (\PDOException $e) { }

		if(empty($resDateEncheres['date_maj']) OR strtotime($resDateEncheres['date_maj']) <= strtotime('-6 hours'))
		{
			$encheres = $apiClient->connected_realms()->getAuctions($idServeur);

			if(!empty($resObjetsBdd) AND !empty($encheres->auctions))
			{
				$encheresParObjet = [];
				$encheresParMascotte = [];

				foreach($encheres->auctions as $enchere)
				{
					if(!$prixEnchere($enchere)) { continue; }

					if($mascotteSeulement($enchere)) {
						$speciesId = (int) $enchere->item->pet_species_id;
						$encheresParMascotte[$speciesId] ??= [];
						$encheresParMascotte[$speciesId][] = $enchere;
						continue;
					}

					if($objetSeulement($enchere)) {
						$objetId = (int) $enchere->item->id;
						$encheresParObjet[$objetId] ??= [];
						$encheresParObjet[$objetId][] = $enchere;
					}
				}

				try {
					// Supprimer anciennes enchères de 48 heures
					$stmt = $pdo->prepare('DELETE FROM wow_objets_encheres WHERE date_ajout < (NOW() - INTERVAL 48 HOUR)');
					$stmt->execute();

					$stmtPrix = $pdo->prepare('INSERT INTO wow_objets_encheres (id_enchere, id_utilisateur, id_objet, id_serveur, prix, quantite, temps_restant) VALUES (:id_enchere, :id_utilisateur, :id_objet, :id_serveur, :prix, :quantite, :temps_restant)');

					$idsObjets = array_column($resObjetsBdd, 'id_objet');
					if($idsObjets)
					{
						$place = implode(',', array_fill(0, count($idsObjets), '?'));

						$stmtSupprimer = $pdo->prepare('DELETE FROM wow_objets_encheres WHERE id_serveur = ? AND id_objet IN ('.$place.')');
						$stmtSupprimer->execute(array_merge([$idServeur], $idsObjets));
					}

					$pdo->beginTransaction();

					foreach($resObjetsBdd as $objet)
					{
						$idObjet = (int) $objet['id_objet'];
						$idMascotte = (isset($objet['id_mascotte']) AND (int) $objet['id_mascotte'] > 0) ? (int) $objet['id_mascotte'] : null;
						$idBlizzard = (isset($objet['id_blizzard']) AND (int) $objet['id_blizzard'] > 0) ? (int) $objet['id_blizzard'] : null;

						if($idMascotte !== null)		$matches = $encheresParMascotte[$idMascotte] ?? [];
						elseif($idBlizzard !== null)	$matches = $encheresParObjet[$idBlizzard] ?? [];
						else {
							continue;
						}

						foreach($matches as $m)
						{
							$stmtPrix->execute([
								':id_enchere' => (int) $m->id,
								':id_utilisateur' => (int) $_SESSION['id_utilisateur'],
								':id_objet' => (int) $idObjet,
								':id_serveur' => (int) $idServeur,
								':prix' => ((isset($m->buyout) AND (int) $m->buyout > 0) ? (int) $m->buyout : null),
								':quantite' => ((isset($m->quantity) AND (int) $m->quantity > 0) ? (int) $m->quantity : null),
								':temps_restant' => (isset($m->time_left) ? (string) $m->time_left : null),
							]);
						}
					}

					$pdo->commit();
				} catch (Throwable $e) { }

				try {
					$stmtEncheresTemps = $pdo->prepare('INSERT INTO wow_objets_encheres_temps (id_utilisateur, id_serveur, date_maj) VALUES (:id_utilisateur, :id_serveur, NOW()) ON DUPLICATE KEY UPDATE date_maj = IF(date_maj <= NOW() - INTERVAL 6 HOUR, VALUES(date_maj), date_maj)');
					$stmtEncheresTemps->execute([
						':id_utilisateur' => (int) $_SESSION['id_utilisateur'],
						':id_serveur' => (int) $idServeur
					]);

					$msg = alerte('success', 'Enchères mises à jour pour le serveur <span class="fw-bold">'.$nomServeur.'</span>');
				} catch (\PDOException $e) { }
			}
		}
	}
}

require_once 'a_body.php';

if(estConnecte($pdo))
{
	if($idServeur == $idConnecte)
	{
		if(!empty($idServeur) AND !empty($paysServeur) AND !empty($nomServeur))
		{
			if(!empty($idServeur))
			{
				try {
					$stmt = $pdo->prepare('SELECT * FROM wow_serveurs WHERE id_blizzard = :id_blizzard OR id_connecte = :id_connecte LIMIT 1');
					$stmt->execute([
						'id_blizzard' => (int) $idServeur,
						'id_connecte' => (int) $idServeur,
					]);
					$resServeur = $stmt->fetch();
				} catch (\PDOException $e) { }

				try {
					$stmt = $pdo->prepare('SELECT * FROM wow_serveurs WHERE id_connecte = :id_connecte ORDER BY nom');
					$stmt->execute(['id_connecte' => (int) $resServeur['id_connecte']]);
					$resServeurConnecte = $stmt->fetchAll();
				} catch (\PDOException $e) { }

				$idServeurConnecte = $resServeurConnecte[0]['id_connecte'];
			}

			echo !empty($msg) ? $msg : null;

			$nomServeur = !empty($resServeur['nom']) ? secuChars($resServeur['nom']) : 'Ysondre';
			$nomServeurRu = !empty($resServeur['nom_ru']) ? secuChars($resServeur['nom_ru']) : null;

			echo (!empty($nomServeur) ? '<h1 class="mb-5">'.$paysServeur.' <a href="/'.$idServeur.'-'.$slugNomServeur.'" '.(!empty($nomServeurRu) ? 'data-bs-toggle="tooltip" data-bs-title="Nom en cyrilique : '.$nomServeurRu.'"' : null).'>'.$nomServeur.'</a></h1>' : null);

			echo '<form action="/profil-verification" method="post" id="chercherObjet">
				<div class="container">
					<div class="row mb-5">
						<div class="col-12 col-lg-5 mx-auto">
							<div class="input-group input-group-hdvli">
								<span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
								<input type="text" name="nom_objet" class="form-control form-control-lg border-0" id="nomObjet" placeholder="ID, nom de l’objet ou Mascotte" '.(empty($_POST['objet']) ? ' autofocus' : null).' required>
								<button type="submit" class="btn btn-success" form="chercherObjet">Chercher</button>
							</div>
						</div>
					</div>
				</div>
			</form>';

			if(!empty($_SESSION['id_objet_donnees']))
			{
				$tooltip = $_SESSION['id_objet_donnees'];

				if(!empty($tooltip['objetId']) AND !empty($tooltip['nom']))
				{
					$objetId			= !empty($tooltip['objetId'])				? secu($tooltip['objetId']) : null;
					$nomObjet			= !empty($tooltip['nom'])					? secuChars($tooltip['nom']) : null;
					$slugNomObjet		= !empty($tooltip['nom'])					? slug($tooltip['nom']) : null;
					$mediaPetSpeciesId	= !empty($tooltip['media_pet_species_id'])	? secu($tooltip['media_pet_species_id']) : null;
					$htmlTooltip		= !empty($tooltip['htmlTooltip'])			? $tooltip['htmlTooltip'] : null;
					$icone				= !empty($tooltip['icone'])					? secuChars($tooltip['icone']) : null;
					$imgObjet			= !empty($icone)							? 'https://wow.zamimg.com/images/wow/icons/large/'.$icone.'.jpg' : (!empty($tooltip['iconeBnet']) ? secuChars($tooltip['iconeBnet']) : null);

					if(!empty($tooltip['htmlTooltip']))
					{
						$tooltip['htmlTooltip'] = preg_replace('/Prix au Comptoir : (\d+)/ims', '', $tooltip['htmlTooltip']);
						$tooltip['htmlTooltip'] = preg_replace('/<a href="https:\/\/www.wowhead.com\/fr\/currency=(\d+)\/deniers" class="icontinyl" style="background-image: url\(https:\/\/wow.zamimg.com\/images\/wow\/icons\/tiny\/tradingpostcurrency.gif\)">Deniers<\/a><br>/ims', '', $tooltip['htmlTooltip']);
					}

					echo '<div class="container text-center">

					<form action="/profil-verification" method="post">
						<div class="row text-center mx-auto">
							<div class="col-12 col-lg-6 p-0 position-relative text-start mx-auto">
								<input type="hidden" name="nom_objet" value="'.$nomObjet.'">
								<input type="hidden" name="id_blizzard" value="'.$objetId.'">
								'.(!empty($mediaPetSpeciesId) ? '<input type="hidden" name="id_mascotte" value="'.$mediaPetSpeciesId.'">' : null).'
								'.(!empty($_GET['trier']) ? '<input type="hidden" name="trier"'.(in_array($_GET['trier'], ['id_blizzard', 'nom', 'date_ajout'], true) ? ' value="'.secuChars($_GET['trier']).'"' : null) : null).'

								<img src="'.$imgObjet.'" style="height: 50px; width: 50px" class="position-absolute top-0 end-0 rounded-3 mt-4 mt-lg-3 me-4" alt="Icône '.$nomObjet.'" title="Icône '.$nomObjet.'">

								'.(!empty($tooltip['htmlTooltip']) ? '<div class="bg-dark border border-1 border-white rounded p-3 d-flex flex-column justify-content-between">'.$tooltip['htmlTooltip'].'</div>' : null).'

								<div class="mb-0 mt-3 text-center">';

									if(!empty($mascotteId) OR (!objetExiste($pdo, $tooltip['nom']) AND !empty($tooltip['htmlTooltip']) AND !mascotteUtilisateur($pdo, $tooltip['nom']) AND !preg_match('/Lié quand ramassé/i', $tooltip['htmlTooltip'])))
										echo '<button class="btn btn-outline-success mb-0">Ajouter <strong>'.$nomObjet.'</strong> à mon inventaire</button>';

									elseif(!objetExiste($pdo, $tooltip['nom']) AND mascotteUtilisateur($pdo, $tooltip['nom']))
										echo '<p class="text-success mb-4">La mascotte <span class="fw-bold">'.$tooltip['nom'].'</span> est déjà dans votre liste de mascotte.</p>
										<button class="btn btn-outline-success mb-0">Ajouter <strong>'.$nomObjet.'</strong> à mon inventaire</button>';

									elseif(objetExiste($pdo, $tooltip['nom']) AND !mascotteUtilisateur($pdo, $tooltip['nom']))
										echo '<p class="text-success mb-0">L’objet <a href="?chercher_objet='.$tooltip['nom'].'" class="fw-bold">'.$tooltip['nom'].'</a> est déjà dans votre inventaire.</p>';

									elseif(!empty($mascotteId) OR (objetExiste($pdo, $tooltip['nom']) AND mascotteUtilisateur($pdo, $tooltip['nom'])))
										echo '<a href="/profil-verification?supprimer_nom='.secuChars(urlencode($tooltip['nom'])).'" class="btn btn-outline-danger" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cet objet de mon inventaire ?\')">Supprimer <strong>'.$nomObjet.'</strong> de mon inventaire</a>';

									elseif(preg_match('/Lié quand ramassé/i', $tooltip['htmlTooltip']))
										echo '<p class="text-danger">Impossible d’ajouter un objet <span class="fw-bold">Lié quand ramassé</span></p>';

									else
										echo '<p class="text-danger">Impossible d’ajouter l’objet <span class="fw-bold">'.$tooltip['nom'].'</span>';

								echo '</div>
							</form>
						</div>
					</div>';
				}

				echo '<div class="container">
					<hr class="my-5">
				</div>';
			}

			if(serveurExiste($pdo, $idServeurConnecte))
			{
				$trieAutorise = ['nom' => 'o.nom', 'prix' => 'r.prix', 'temps_restant' => 'r.temps_restant'];
				$trier = $trieAutorise[$_GET['trier'] ?? 'date_ajout'] ?? 'r.prix';
				$trierPar = in_array(mb_strtolower($_GET['trierpar'] ?? ''), ['asc', 'desc'], true) ? mb_strtolower($_GET['trierpar']) : 'asc';

				$fTrier = !empty($limite) ? '&trier='.$trier.'&trierPar='.$trierPar : null;

				if(isset($_GET['objets']))				$fFiltre = '&objets';
				elseif(isset($_GET['tousObjets']))		$fFiltre = '&tousObjets';
				elseif(isset($_GET['mascottes']))		$fFiltre = '&mascottes';
				elseif(isset($_GET['toutesMascottes']))	$fFiltre = '&toutesMascottes';
				elseif(isset($_GET['tout']))			$fFiltre = '&tout';
				else									$fFiltre = '&defaut';

				$fPrix = !empty($_GET['prix']) ? '&prix='.secuChars($_GET['prix']) : null;
				$fPrix = (!empty($_GET['prix']) AND in_array($_GET['prix'], [1000, 2500, 5000, 10000, 25000, 50000, 10000])) ? '&prix='.secu($_GET['prix']) : null;
				$ftempsRestant = (!empty($_GET['tempsRestant']) AND in_array($_GET['tempsRestant'], ['SHORT', 'MEDIUM', 'LONG', 'VERY_LONG'])) ? '&tempsRestant='.secuChars($_GET['tempsRestant']) : null;

				$stmt = $pdo->prepare('WITH ranked AS (SELECT e.*, ROW_NUMBER() OVER (PARTITION BY e.id_objet ORDER BY e.prix ASC, e.id_enchere ASC) AS rn
					FROM wow_objets_encheres e
					WHERE e.id_utilisateur = :id_utilisateur_ench AND e.id_serveur = :id_serveur
				)
				SELECT o.id AS id_objet, o.id_blizzard, o.id_mascotte, o.nom, o.date_ajout, r.id_enchere, r.id_serveur, r.prix, r.quantite, r.temps_restant
				FROM wow_objets o
				LEFT JOIN ranked r
				ON r.id_objet = o.id AND r.rn = 1
				WHERE o.id_utilisateur = :id_utilisateur_obj
				ORDER BY '.$trier.' '.$trierPar);
				$stmt->execute([
					':id_utilisateur_ench' => (int) $_SESSION['id_utilisateur'],
					':id_utilisateur_obj' => (int) $_SESSION['id_utilisateur'],
					':id_serveur' => (int) $idServeur,
				]);
				$resObjet = $stmt->fetchAll();

				if(isset($_GET['objets'])) {
					$filtres = '&objets';

					$resObjet = array_filter($resObjet, function ($objet) {
						return empty($objet['id_mascotte']) AND $objet['prix'] > 0;
					});
				}

				elseif(isset($_GET['tousObjets'])) {
					$filtres = '&tousObjets';

					$resObjet = array_filter($resObjet, function ($objet) {
						return empty($objet['id_mascotte']);
					});

				}

				elseif(isset($_GET['mascottes'])) {
					$filtres = '&mascottes';

					$resObjet = array_filter($resObjet, function ($objet) {
						return !empty($objet['id_mascotte']) AND $objet['prix'] > 0;
					});
				}

				elseif(isset($_GET['toutesMascottes'])) {
					$filtres = '&toutesMascottes';

					$resObjet = array_filter($resObjet, function ($objet) {
						return !empty($objet['id_mascotte']);
					});
				}

				elseif(isset($_GET['tout'])) {
					$filtres = '&tout';
				}

				else {
					$filtres = null;

					$resObjet = array_filter($resObjet, function ($objet) {
						return $objet['prix'] > 0;
					});
				}

				if(!empty($_GET['prix']))
				{
					if($_GET['prix'] == 1000) {
						$filtres = $filtres.'&prix=1000';

						$resObjet = array_filter($resObjet, function ($objet) {
							return (!empty($objet['prix']) AND $objet['prix'] < 10000000);
						});
					}

					elseif($_GET['prix'] == 2500) {
						$filtres = $filtres.'&prix=2500';

						$resObjet = array_filter($resObjet, function ($objet) {
							return (!empty($objet['prix']) AND $objet['prix'] < 25000000);
						});
					}

					elseif($_GET['prix'] == 5000) {
						$filtres = $filtres.'&prix=5000';

						$resObjet = array_filter($resObjet, function ($objet) {
							return (!empty($objet['prix']) AND $objet['prix'] <= 50000000);
						});
					}

					elseif($_GET['prix'] == 10000) {
						$filtres = $filtres.'&prix=10000';

						$resObjet = array_filter($resObjet, function ($objet) {
							return (!empty($objet['prix']) AND $objet['prix'] <= 100000000);
						});
					}

					elseif($_GET['prix'] == 25000) {
						$filtres = $filtres.'&prix=25000';

						$resObjet = array_filter($resObjet, function ($objet) {
							return (!empty($objet['prix']) AND $objet['prix'] <= 250000000);
						});
					}

					elseif($_GET['prix'] == 50000) {
						$filtres = $filtres.'&prix=50000';

						$resObjet = array_filter($resObjet, function ($objet) {
							return (!empty($objet['prix']) AND $objet['prix'] <= 500000000);
						});
					}

					elseif($_GET['prix'] == 100000) {
						$filtres = $filtres.'&prix=100000';

						$resObjet = array_filter($resObjet, function ($objet) {
							return (!empty($objet['prix']) AND $objet['prix'] > 1000000000);
						});
					}

					else { }
				}

				if(!empty($_GET['tempsRestant']))
				{
					if($_GET['tempsRestant'] === 'SHORT') {
						$filtres = $filtres.'&tempsRestant=SHORT';

						$resObjet = array_filter($resObjet, function ($objet) {
							return (!empty($objet['temps_restant']) AND $objet['temps_restant'] === 'SHORT');
						});
					}

					elseif($_GET['tempsRestant'] === 'MEDIUM') {
						$filtres = $filtres.'&tempsRestant=MEDIUM';

						$resObjet = array_filter($resObjet, function ($objet) {
							return (!empty($objet['temps_restant']) AND $objet['temps_restant'] === 'MEDIUM');
						});
					}

					elseif($_GET['tempsRestant'] === 'LONG') {
						$filtres = $filtres.'&tempsRestant=LONG';

						$resObjet = array_filter($resObjet, function ($objet) {
							return (!empty($objet['temps_restant']) AND $objet['temps_restant'] === 'LONG');
						});
					}

					elseif($_GET['tempsRestant'] === 'VERY_LONG') {
						$filtres = $filtres.'&tempsRestant=VERY_LONG';

						$resObjet = array_filter($resObjet, function ($objet) {
							return (!empty($objet['temps_restant']) AND $objet['temps_restant'] === 'VERY_LONG');
						});
					}

					else { }
				}

				$listeObjets[] = '<div id="filtres">
					<div id="filtres-mobile" class="d-flex d-lg-none row justify-content-center mb-4">
						<div class="col-auto ps-1">
							<div class="dropdown">
								<button type="button" style="min-width: 115px;" class="btn btn-light dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">Filtres</button>
								<ul class="dropdown-menu">
									<li><a href="/?id_serveur='.$idServeur.'" class="dropdown-item'.((!isset($_GET['objets']) AND !isset($_GET['tousObjets']) AND !isset($_GET['mascottes']) AND !isset($_GET['toutesMascottes']) AND !isset($_GET['tout'])) ? ' active' : null).'">'.((!isset($_GET['objets']) AND !isset($_GET['mascottes']) AND !isset($_GET['tout'])) ? '<i class="fa-solid fa-check"></i> ' : null).'Défaut</a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fPrix.$ftempsRestant.'&objets" class="dropdown-item'.(isset($_GET['objets']) ? ' active' : null).'">'.(isset($_GET['objets']) ? '<i class="fa-solid fa-check"></i> ' : null).'Objets</a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fPrix.$ftempsRestant.'&tousObjets" class="dropdown-item'.(isset($_GET['tousObjets']) ? ' active' : null).'">'.(isset($_GET['tousObjets']) ? '<i class="fa-solid fa-check"></i> ' : null).'Tous les Objets</a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fPrix.$ftempsRestant.'&mascottes" class="dropdown-item'.(isset($_GET['mascottes']) ? ' active' : null).'">'.(isset($_GET['mascottes']) ? '<i class="fa-solid fa-check"></i> ' : null).'Mascottes</a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fPrix.$ftempsRestant.'&toutesMascottes" class="dropdown-item'.(isset($_GET['toutesMascottes']) ? ' active' : null).'">'.(isset($_GET['toutesMascottes']) ? '<i class="fa-solid fa-check"></i> ' : null).'Toutes les Mascottes</a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fPrix.$ftempsRestant.'&tout" class="dropdown-item'.(isset($_GET['tout']) ? ' active' : null).'">'.(isset($_GET['tout']) ? '<i class="fa-solid fa-check"></i> ' : null).'Tous les Objets et Mascottes</a></li>
								</ul>
							</div>
						</div>
						<div class="col-auto p-0">
							<div class="dropdown">
								<button type="button" style="min-width: 115px;" class="btn btn-light dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">Prix</button>
								<ul class="dropdown-menu">
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=1000" class="dropdown-item'.((!empty($_GET['prix']) AND $_GET['prix'] == 1000) ? ' active' : null).'">'.((!empty($_GET['prix']) AND $_GET['prix'] == 1000) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 1.000 <i class="fa-solid fa-coins or"></i></a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=2500" class="dropdown-item'.((!empty($_GET['prix']) AND $_GET['prix'] == 2500) ? ' active' : null).'">'.((!empty($_GET['prix']) AND $_GET['prix'] == 2500) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 2.500 <i class="fa-solid fa-coins or"></i></a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=5000" class="dropdown-item'.((!empty($_GET['prix']) AND $_GET['prix'] == 5000) ? ' active' : null).'">'.((!empty($_GET['prix']) AND $_GET['prix'] == 5000) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 5.000 <i class="fa-solid fa-coins or"></i></a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=10000" class="dropdown-item'.((!empty($_GET['prix']) AND $_GET['prix'] == 10000) ? ' active' : null).'">'.((!empty($_GET['prix']) AND $_GET['prix'] == 10000) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 10.000 <i class="fa-solid fa-coins or"></i></a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=25000" class="dropdown-item'.((!empty($_GET['prix']) AND $_GET['prix'] == 25000) ? ' active' : null).'">'.((!empty($_GET['prix']) AND $_GET['prix'] == 25000) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 25.000 <i class="fa-solid fa-coins or"></i></a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=50000" class="dropdown-item'.((!empty($_GET['prix']) AND $_GET['prix'] == 50000) ? ' active' : null).'">'.((!empty($_GET['prix']) AND $_GET['prix'] == 50000) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 50.000 <i class="fa-solid fa-coins or"></i></a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=100000" class="dropdown-item'.((!empty($_GET['prix']) AND $_GET['prix'] == 100000) ? ' active' : null).'">'.((!empty($_GET['prix']) AND $_GET['prix'] == 100000) ? '<i class="fa-solid fa-check"></i> ' : null).'+ de 100.000 <i class="fa-solid fa-coins or"></i></a></li>
								</ul>
							</div>
						</div>
						<div class="col-auto pe-1">
							<div class="dropdown">
								<button type="button" style="min-width: 115px;" class="btn btn-light dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">Temps Restant</button>
								<ul class="dropdown-menu">
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$fPrix.'&tempsRestant=SHORT" class="dropdown-item'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'SHORT') ? ' active' : null).'">'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'SHORT') ? '<i class="fa-solid fa-check"></i> ' : null).'- de 12 heures</a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$fPrix.'&tempsRestant=MEDIUM" class="dropdown-item'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'MEDIUM') ? ' active' : null).'">'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'MEDIUM') ? '<i class="fa-solid fa-check"></i> ' : null).'entre 12 hrs à 24 hrs</a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$fPrix.'&tempsRestant=LONG" class="dropdown-item'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'LONG') ? ' active' : null).'">'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'LONG') ? '<i class="fa-solid fa-check"></i> ' : null).'entre 24 hrs à 48 hrs</a></li>
									<li><a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$fPrix.'&tempsRestant=VERY_LONG" class="dropdown-item'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'VERY_LONG') ? ' active' : null).'">'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'VERY_LONG') ? '<i class="fa-solid fa-check"></i> ' : null).'48 heures</a></li>
								</ul>
							</div>
						</div>
					</div>

					<div id="filtres-bureau" class="d-lg-flex d-none row border rounded mb-4 py-3 text-center">
						<div class="d-flex justify-content-center flex-wrap gap-3 mb-4">
							<a href="/?id_serveur='.$idServeur.$fTrier.$fPrix.$ftempsRestant.'&objets" class="btn btn-light fw-bold'.(isset($_GET['objets']) ? ' active' : null).'"						data-bs-toggle="tooltip" data-bs-title="Afficher uniquement les Objets avec enchères">'.(isset($_GET['objets']) ? '<i class="fa-solid fa-check"></i> ' : null).'Objets</a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fPrix.$ftempsRestant.'&tousObjets" class="btn btn-light fw-bold'.(isset($_GET['tousObjets']) ? ' active' : null).'"				data-bs-toggle="tooltip" data-bs-title="Afficher uniquement les Objets avec et sans enchères">'.(isset($_GET['tousObjets']) ? '<i class="fa-solid fa-check"></i> ' : null).'Tous les Objets</a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fPrix.$ftempsRestant.'&mascottes" class="btn btn-light fw-bold'.(isset($_GET['mascottes']) ? ' active' : null).'"				data-bs-toggle="tooltip" data-bs-title="Afficher uniquement les Mascottes avec enchères">'.(isset($_GET['mascottes']) ? '<i class="fa-solid fa-check"></i> ' : null).'Mascottes</a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fPrix.$ftempsRestant.'&toutesMascottes" class="btn btn-light fw-bold'.(isset($_GET['toutesMascottes']) ? ' active' : null).'"	data-bs-toggle="tooltip" data-bs-title="Afficher uniquement les Mascottes avec et sans enchères">'.(isset($_GET['toutesMascottes']) ? '<i class="fa-solid fa-check"></i> ' : null).'Toutes les Mascottes</a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fPrix.$ftempsRestant.'&tout" class="btn btn-light fw-bold'.(isset($_GET['tout']) ? ' active' : null).'"							data-bs-toggle="tooltip" data-bs-title="Afficher uniquement tous les Objets et les Mascottes avec et sans enchères">'.(isset($_GET['tout']) ? '<i class="fa-solid fa-check"></i> ' : null).'Tous les Objets et Mascottes</a>
							<a href="/?id_serveur='.$idServeur.'" class="btn btn-light fw-bold'.((!isset($_GET['objets']) AND !isset($_GET['tousObjets']) AND !isset($_GET['mascottes']) AND !isset($_GET['toutesMascottes']) AND !isset($_GET['tout'])) ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Par défaut">'.((!isset($_GET['objets']) AND !isset($_GET['mascottes']) AND !isset($_GET['tout'])) ? '<i class="fa-solid fa-check"></i> ' : null).'Défaut</a>
						</div>

						<div class="d-flex justify-content-center flex-wrap gap-3 mb-4">
							<a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=1000" class="btn btn-light fw-bold'.((!empty($_GET['prix']) AND $_GET['prix'] == 1000) ? ' active' : null).'"		data-bs-toggle="tooltip" data-bs-title="Afficher objets de moins de 1.000 Pièces d¹or">'.((!empty($_GET['prix']) AND $_GET['prix'] == 1000) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 1.000 <i class="fa-solid fa-coins or"></i></a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=2500" class="btn btn-light fw-bold'.((!empty($_GET['prix']) AND $_GET['prix'] == 2500) ? ' active' : null).'"		data-bs-toggle="tooltip" data-bs-title="Afficher objets de moins de 2.500 Pièces d¹or">'.((!empty($_GET['prix']) AND $_GET['prix'] == 2500) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 2.500 <i class="fa-solid fa-coins or"></i></a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=5000" class="btn btn-light fw-bold'.((!empty($_GET['prix']) AND $_GET['prix'] == 5000) ? ' active' : null).'"		data-bs-toggle="tooltip" data-bs-title="Afficher objets de moins de 5.000 Pièces d¹or">'.((!empty($_GET['prix']) AND $_GET['prix'] == 5000) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 5.000 <i class="fa-solid fa-coins or"></i></a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=10000" class="btn btn-light fw-bold'.((!empty($_GET['prix']) AND $_GET['prix'] == 10000) ? ' active' : null).'"	data-bs-toggle="tooltip" data-bs-title="Afficher objets de moins de 10.000 Pièces d¹or">'.((!empty($_GET['prix']) AND $_GET['prix'] == 10000) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 10.000 <i class="fa-solid fa-coins or"></i></a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=25000" class="btn btn-light fw-bold'.((!empty($_GET['prix']) AND $_GET['prix'] == 25000) ? ' active' : null).'"	data-bs-toggle="tooltip" data-bs-title="Afficher objets de moins de 25.000 Pièces d¹or">'.((!empty($_GET['prix']) AND $_GET['prix'] == 25000) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 25.000 <i class="fa-solid fa-coins or"></i></a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=50000" class="btn btn-light fw-bold'.((!empty($_GET['prix']) AND $_GET['prix'] == 50000) ? ' active' : null).'"	data-bs-toggle="tooltip" data-bs-title="Afficher objets de moins de 50.000 Pièces d¹or">'.((!empty($_GET['prix']) AND $_GET['prix'] == 50000) ? '<i class="fa-solid fa-check"></i> ' : null).'- de 50.000 <i class="fa-solid fa-coins or"></i></a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$ftempsRestant.'&prix=100000" class="btn btn-light fw-bold'.((!empty($_GET['prix']) AND $_GET['prix'] == 100000) ? ' active' : null).'"	data-bs-toggle="tooltip" data-bs-title="Afficher objets de plus de 100.000 Pièces d¹or">'.((!empty($_GET['prix']) AND $_GET['prix'] == 100000) ? '<i class="fa-solid fa-check"></i> ' : null).'+ de 100.000 <i class="fa-solid fa-coins or"></i></a>
						</div>

						<div class="d-flex justify-content-center flex-wrap gap-3 mb-4">
							<a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$fPrix.'&tempsRestant=SHORT" class="btn btn-light fw-bold'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'SHORT') ? ' active' : null).'"			data-bs-toggle="tooltip" data-bs-title="Afficher les enchères - de 12 heures">'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'SHORT') ? '<i class="fa-solid fa-check"></i> ' : null).'- de 12 heures</a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$fPrix.'&tempsRestant=MEDIUM" class="btn btn-light fw-bold'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'MEDIUM') ? ' active' : null).'"			data-bs-toggle="tooltip" data-bs-title="Afficher les enchères entre 12 heures et 24 heures">'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'MEDIUM') ? '<i class="fa-solid fa-check"></i> ' : null).'entre 12 hrs à 24 hrs</a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$fPrix.'&tempsRestant=LONG" class="btn btn-light fw-bold'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'LONG') ? ' active' : null).'"				data-bs-toggle="tooltip" data-bs-title="Afficher les enchères entre 24 heures et 48 heures">'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'LONG') ? '<i class="fa-solid fa-check"></i> ' : null).'entre 24 hrs à 48 hrs</a>
							<a href="/?id_serveur='.$idServeur.$fTrier.$fFiltre.$fPrix.'&tempsRestant=VERY_LONG" class="btn btn-light fw-bold'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'VERY_LONG') ? ' active' : null).'"	data-bs-toggle="tooltip" data-bs-title="Afficher les enchères de 48 heures">'.((!empty($_GET['tempsRestant']) AND $_GET['tempsRestant'] === 'VERY_LONG') ? '<i class="fa-solid fa-check"></i> ' : null).'48 heures</a>
						</div>

						<p class="mb-0"><a href="/encheres-serveur/'.$idServeur.'-'.slug($nomServeur).'" class="btn btn-light">Afficher toutes les enchères du serveur <span class="fw-bold">'.$nomServeur.'</span></a></p>';

						if(!empty($resServeurConnecte) AND count($resServeurConnecte) > 1)
						{
							$serveursConnectes = [];
							foreach($resServeurConnecte as $detailsServeur)
								$serveursConnectes[] = '<span class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-title="'.secuChars($detailsServeur['nom']).' ('.secuChars($detailsServeur['id_blizzard']).') est connecté à '.$nomServeur.'">'.secuChars($detailsServeur['nom']).'</span>';

								// $serveursConnectes[] = ($detailsServeur['id_blizzard'] !== $idServeur) ? '<a href="?id_serveur='.secuChars($detailsServeur['id_blizzard']).'" class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-title="'.secuChars($detailsServeur['nom']).' ('.secuChars($detailsServeur['id_blizzard']).') est connecté à '.$nomServeur.'">'.secuChars($detailsServeur['nom']).'</a>' : secuChars($detailsServeur['nom']);

							$listeObjets[] = '<div class="d-flex justify-content-center flex-wrap gap-2 mt-4">'.implode(' ', $serveursConnectes).'</div>';
						}

					$listeObjets[] = '</div>
				</div>

				<div class="container text-center">
					<div class="row row-entete border rounded px-0 py-3 mb-3">
						<div class="col-4	col-lg-6"><a href="/?id_serveur='.$idServeur.'&trier=nom&trierpar='.(($trier === 'o.nom' AND $trierPar === 'asc') ? 'desc' : 'asc').$filtres.'" data-bs-toggle="tooltip" data-bs-title="Trier par Objet">Objet</a> '.trierIcone('o.nom', $trier, $trierPar).'</div>
						<div class="col-4	col-lg-2"><a href="/?id_serveur='.$idServeur.'&trier=prix&trierpar='.(($trier === 'r.prix' AND $trierPar === 'asc') ? 'desc' : 'asc').$filtres.'" data-bs-toggle="tooltip" data-bs-title="Trier par Prix">Prix</a> '.trierIcone('r.prix', $trier, $trierPar).'</div>
						<div class="col-4	col-lg-2"><a href="/?id_serveur='.$idServeur.'&trier=temps_restant&trierpar='.(($trier === 'r.temps_restant' AND $trierPar === 'asc') ? 'desc' : 'asc').$filtres.'" data-bs-toggle="tooltip" data-bs-title="Trier par Temps restant"><span class="d-inline-block d-lg-none text-decoration-underline">Temps</span><span class="d-none d-lg-inline-block text-decoration-underline">Temps restant</span></a> '.trierIcone('r.temps_restant', $trier, $trierPar).'</div>
						<div class="		col-lg-2 fs-4"></div>
					</div>
				</div>';

				$i = 1;
				foreach($resObjet as $enchere)
				{
					$idObjet				= (int) $enchere['id_objet'];
					$idBlizzard				= !empty($enchere['id_blizzard'])	? (int) $enchere['id_blizzard']							: 'n/a';
					$idMascotte				= !empty($enchere['id_mascotte'])	? (int) $enchere['id_mascotte']							: null;
					$idEnchere				= !empty($enchere['id_enchere'])	? (int) $enchere['id_enchere']							: null;
					$nomObjet				= !empty($enchere['nom'])			? secuChars($enchere['nom'])							: 'nom inconnu';
					$nomObjetSlug			= !empty($enchere['nom'])			? slug($enchere['nom'])									: 'nom-inconnu';
					$lien					= '<a href="/prix-objet/'.$idObjet.'-'.$nomObjetSlug.'" '.(!empty($idMascotte) ? 'class="rare"' : 'data-wowhead="domain=fr&item='.$idBlizzard.'"').' '.(estAdmin() ? ' onclick="hide(\'#'.$nomObjetSlug.'\');"' : null).'>'.$nomObjet.'</a>';
					$prix					= !empty($enchere['prix'])			? convertirPieces($enchere['prix'])['or']				: null;
					$tempsRestant			= tempsRestant($enchere['temps_restant']);
					$dateAjout				= !empty($enchere['date_ajout'])	? dateFormat(strtotime($enchere['date_ajout']))			: 'n/a';
					$mascotteUtilisateur	= !empty($idMascotte)				? (mascotteUtilisateur($pdo, $nomObjet) ? true : false)	: false;

					$liens[] = '/prix-objet/'.$idObjet.'-'.$nomObjetSlug;

					$listeObjets[] = '<div class="row border-bottom m-0 py-3 text-center" id="'.$nomObjetSlug.'">
						<div class="col-12	col-lg-6 mb-3 mb-lg-0">'.$lien.'</div>
						<div class="col-4	col-lg-2">'.(!empty($prix) ? $prix.' '.PO : 'n/a').'</div>
						<div class="col-4	col-lg-2">'.$tempsRestant.'</div>
						<div class="col-4	col-lg-2">
							'.($mascotteUtilisateur === true ? '<i class="fa-solid fa-bug me-2 text-danger curseur" data-bs-toggle="tooltip" data-bs-title="<strong>'.$nomObjet.'</strong> fait partie de votre liste de mascotte dans le jeu"></i>' : null).'
							'.(!empty($idMascotte) ? '<i class="fa-solid fa-bug me-2 rare curseur" data-bs-toggle="tooltip" data-bs-title="Mascotte"></i>' : null).'
							<i class="fa-solid fa-circle-info me-2 curseur" data-bs-custom-class="tooltip-gauche" data-bs-toggle="tooltip" data-bs-title="
								<span class=\'fw-bold\'>Date d’ajout</span> : '.$dateAjout.'<br><br>
								'.(!empty($idEnchere) ? '<span class=\'fw-bold\'>ID Enchère</span> : '.$idEnchere.'<br>' : null).'
								<span class=\'fw-bold\'>ID Objet</span> : '.$idBlizzard.'<br>
								'.(!empty($idMascotte) ? '<span class=\'fw-bold\'>ID Mascotte</span> : '.$idMascotte.'<br>' : null).'
								<br><span class=\'fw-bold\'>Serveur</span> : '.$nomServeur.'<br>
								<span class=\'fw-bold\'>ID Serveur</span> : '.$idServeur.'
							"></i>
							<a href="/profil-verification?supprimer_id='.$idObjet.'&id_serveur='.$idServeur.'" data-bs-toggle="tooltip" data-bs-title="Supprimer cet objet" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cet objet de mon inventaire ?\')"><i class="fa-solid fa-trash-can text-danger"></i></a>
						</div>
					</div>';

					$i++;
				}

				if(estAdmin() AND !empty($liens))
				{
					echo '<p class="text-center my-4"><a href="#" class="btn btn-outline-warning" onclick="ouvrirTous(); return false;">Ouvrir les '.count($liens).' liens</a></p>

					<script>
					function ouvrirTous() {
						const urls = [';

							foreach($liens as $l)
								echo '"'.$l.'",'."\n";

						echo '];
						for (const url of urls) {
							window.open(url, \'_blank\');
						}
					}
					</script>';
				}

				echo count($listeObjets) > 0 ? implode($listeObjets) : alerte('danger', 'Aucun objet trouvé dans l’inventaire');

				try {
					$stmt = $pdo->prepare('SELECT COUNT(*) AS nb FROM wow_objets WHERE id_utilisateur = :id_utilisateur');
					$stmt->execute([
						'id_utilisateur' => (int) $_SESSION['id_utilisateur']
					]);
					$resNb = $stmt->fetch();
				} catch (\PDOException $e) { }

				$nb = ($i - 1);

				if($nb === 0 AND $resNb['nb'] === 0)	echo alerte('danger', 'Aucun objet dans l’inventaire');
				elseif($nb === 0 AND $resNb['nb'] > 0)	echo alerte('danger', 'Aucun objet trouvé');
				elseif($nb > 0 AND $resNb['nb'] > 0)	echo '<p class="mb-0 mt-4 text-end fs-4"><span class="fw-bold">'.$nb.'</span> objet'.s($nb).' dans l’inventaire</p>';
			}

			else
				echo alerte('danger', 'L’API Battle.net ne réponds pas, veuillez réessayer ultérieurement');
		}

		else
		{
			echo alerte('danger', 'Vous devez sélectionner un serveur favori avant d’ajouter des objets dans l’inventaire').

			'<div class="text-center"><a href="/serveurs" class="btn btn-success btn-lg">Sélectionner mon serveur favori</a></div>';
		}
	}

	else
	{
		if(!empty($infosServeur))
		{
			$infosServeur = serveurInfos($pdo, $idConnecte);

			echo alerte('danger', 'Ce serveur est connecté à <a href="/'.$infosServeur['id_connecte'].'-'.$infosServeur['slug'].'" class="text-danger">'.$infosServeur['nom'].'</a>');

			// redirection('/'.$infosServeur['id_connecte'].'-'.$infosServeur['slug'], rand(10000, 50000));
		}

		else
			echo alerte('danger', 'Erreur inconnue');
	}
}

else
	echo '<div style="margin: 10rem auto;" class="text-center">Vous devez être <a href="/connexion">connecté</a>.</div';

unset($_SESSION['nom_objet_formulaire']);
unset($_SESSION['id_objet_donnees']);

require_once 'a_footer.php';