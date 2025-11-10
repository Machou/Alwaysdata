<?php
require_once '../config/wow_config.php';

// Ajouter un objet

if(isset($_POST['nom_objet']))
{
	// Premier formulaire : Chercher ID / nom sur Wowhead
	$nomObjet = !empty($_POST['nom_objet']) ? (string) trim($_POST['nom_objet']) : null;

	// Second formulaire : Ajouter l’objet / mascotte en BDD

	$idBlizzard = !empty($_POST['id_blizzard']) ? (int) trim($_POST['id_blizzard']) : null;
	$idMascotte = !empty($_POST['id_mascotte']) ? (int) trim($_POST['id_mascotte']) : null;

	// Premier formulaire : Chercher ID / nom sur Wowhead

	if(!empty($nomObjet) AND empty($idBlizzard) AND strlen($nomObjet) > 2)
	{
		preg_match('/item=(\d+)/is', trim(strtolower($nomObjet)), $m);
		$idObjet = !empty($m[1]) ? (int) $m[1] : null;

		preg_match('/\[(.*?)\]/u', $nomObjet, $m);
		$nomObjet = !empty($m[1]) ? $m[1] : $nomObjet;

		// Ajout Objet via Texte
		if(empty($idObjet) AND !empty($nomObjet) AND !preg_match('/wowhead.com/i', $nomObjet) AND !is_int($nomObjet))
		{
			$mascottesBnet = $apiClient->pet()->index();

			$mascotteBnet = [];
			foreach($mascottesBnet->pets as $mascotte)
			{
				if(!empty($mascotte->name->fr_FR) AND preg_match('/'.preg_quote($nomObjet, '/').'/i', $mascotte->name->fr_FR))
				{
					$mascotteDetailsBnet = $apiClient->pet()->get($mascotte->id);

					if(!empty($mascotteDetailsBnet));
					{
						$mascotteBnet[] = [
							'id' => $mascotteDetailsBnet->creature->id,
							'nom' => $mascotteDetailsBnet->name->fr_FR,
							'description' => $mascotteDetailsBnet->description->fr_FR,
							'source' => $mascotteDetailsBnet->source->name->fr_FR,
							'type' => $mascotteDetailsBnet->battle_pet_type->type,
							'type_nom' => $mascotteDetailsBnet->battle_pet_type->name->fr_FR,
							'description' => $mascotteDetailsBnet->description->fr_FR,
							'media_pet_species_id' => $mascotteDetailsBnet->media->id,
							'is_tradable' => $mascotteDetailsBnet->is_tradable,
						];
					}
				}
			}

			$objetDonnees = [];
			$i = 0;
			foreach($mascotteBnet as $cle => $val)
			{
				$objetsBnet = $apiClient->pet()->get($val['media_pet_species_id']);

				$idMascotte = secu($objetsBnet->media->id);
				$nomMascotteFr = !empty($objetsBnet->creature->name->fr_FR) ? secuChars($objetsBnet->creature->name->fr_FR) : secuChars($objetsBnet->creature->name->en_GB);
				$nomMascotteEn = secuChars($objetsBnet->creature->name->en_GB);
				$descriptionFr = !empty($objetsBnet->description->fr_FR) ? secuChars($objetsBnet->description->fr_FR) : 'description inconnue';
				$battlePetTypePowerEn = secuChars($objetsBnet->battle_pet_type->name->en_GB);

				$objetDonnees = [
					'objetId' => $objetsBnet->creature->id,
					'nom' => $objetsBnet->creature->name->fr_FR,
					'media_pet_species_id' => $objetsBnet->media->id,
					'iconeBnet' => $objetsBnet->icon,
					'htmlTooltip' => '<a href="/mascotte/'.$idMascotte.'-'.slug($nomMascotteFr).'" class="fw-bold rare fs-3 mb-3">'.$nomMascotteFr.'</a><p class="mb-0 fw-bold">Description</p><p>'.$descriptionFr.'</p><div class="text-center"><a href="https://www.wowhead.com/fr/battle-pet/'.$idMascotte.'" class="btn btn-outline-info btn-sm" title="Fiche Wowhead.com" data-wh-rename-link="false" '.$onclick.'>Wowhead <i class="fa-solid fa-up-right-from-square"></i></a>'.(!empty($nomMascotteEn) ? '<a href="https://www.warcraftpets.com/search/?q='.urlencode($nomMascotteEn).'" class="btn btn-outline-info btn-sm ms-2" title="Fiche WarcraftPets.com" '.$onclick.'>WarcraftPets <i class="fa-solid fa-up-right-from-square"></i></a>' : '<span class="btn btn-outline-info btn-sm" title="Fiche WarcraftPets.com">WarcraftPets</span>').'</div>',
				];
			}

			if(empty($mascotteBnet))
			{
				$objetDonnees = detailsWowhead($nomObjet);

				if(empty($objetDonnees))
				{
					// $objetsBnet = $apiClient->item()->search(['search' => 'name.en_US='.urlencode($nomObjet), '&namespace=static-eu', '&locale=fr_FR', '&orderby=name', '&_page=1', '&_pageSize=1000']);
					$objetsBnet = $apiClient->item()->search(['search' => 'name.fr_FR='.urlencode($nomObjet), '&namespace=static-eu', '&locale=fr_FR', '&orderby=name', '&_page=1', '&_pageSize=1000']);

					foreach($objetsBnet->results as $cle => $val)
					{
						$nomObjet = trim(str_replace("\u{00A0}", ' ', $nomObjet));
						$objetBnet = trim(str_replace("\u{00A0}", ' ', $val->data->name->fr_FR));

						if(strtolower($nomObjet) === strtolower($objetBnet))
						{
							$idBnet = (int) $val->data->id;
							$nomBnet = (string) $val->data->name->fr_FR;
						}
					}

					if(!empty($idBnet))
						$objetDonnees = detailsWowhead($idBnet);
				}
			}
		}

		// Ajout Objet via ID
		elseif(!empty($idObjet) AND is_int($idObjet))
		{
			$objetDonnees = detailsWowhead($idObjet);
		}

		if(!empty($objetDonnees)) {
			$_SESSION['nom_objet_formulaire'] = !empty($nomObjet) ? $nomObjet : null;
			$_SESSION['id_objet_donnees'] = $objetDonnees;
		}

		elseif(empty($idObjet) AND objetExiste($pdo, $nomObjet))
			setFlash('danger', '<span class="fw-bold">'.secuChars($nomObjet).'</span> est déjà dans votre inventaire');

		else
		{
			if(!empty($nomObjet) AND preg_match('/wowhead\.com/i', $nomObjet))	setFlash('danger', '<span class="fw-bold">'.secuChars($nomObjet).'</span> est introuvable<br><br><a href="'.secuChars($nomObjet).'" class="text-danger" data-wh-rename-link="false" '.$onclick.'>Afficher sur Wowhead</a>');
			else																setFlash('danger', 'L’objet ou la mascotte est introuvable. Veuillez recommencer !');
		}

		header('Location: /');
		exit;
	}

	// Second formulaire : Ajouter l’objet / mascotte en BDD

	elseif(!empty($nomObjet) AND !empty($idBlizzard))
	{
		try {
			$pdo->beginTransaction();

			$u = $pdo->prepare('UPDATE wow_objets_encheres_temps SET date_maj = DATE_SUB(date_maj, INTERVAL 6 HOUR) WHERE id_utilisateur = :id_utilisateur AND id_serveur = :id_serveur');
			$u->execute([
				':id_utilisateur' => (int) $_SESSION['id_utilisateur'],
				':id_serveur' => (int) $idServeur,
			]);

			$stmt = $pdo->prepare('INSERT INTO wow_objets (id_utilisateur, id_blizzard, id_mascotte, nom) VALUES (:id_utilisateur, :id_blizzard, :id_mascotte, :nom)');
			$stmt->execute([
				'id_utilisateur' => (int) $_SESSION['id_utilisateur'],
				'id_blizzard' => (int) $idBlizzard,
				'id_mascotte' => $idMascotte,
				'nom' => (string) $nomObjet
			]);

			$dernierId = $pdo->lastInsertId();

			$pdo->commit();
		} catch (\PDOException $e) {
			$pdo->rollBack();
		}

		($stmt->rowCount() === 0) ? setFlash('danger', '<span class="fw-bold">'.secuChars($nomObjet).'</span> est déjà dans votre inventaire') : setFlash('success', '<a href="/prix-objet/'.$dernierId.'-'.secuChars(slug($nomObjet)).'" class="link-success fw-bold">'.secuChars($nomObjet).'</a> a été ajouté à votre inventaire');

		header('Location: /');
		exit;
	}

	else
	{
		setFlash('danger', 'Veuillez saisir le nom ou l’ID de l’objet');

		header('Location: /');
		exit;
	}
}

// Supprimer un objet

elseif(isset($_GET['supprimer_id']) OR isset($_GET['supprimer_nom']))
{
	if(!empty($_GET['supprimer_id']) OR !empty($_GET['supprimer_nom']))
	{
		$supprimerId = !empty($_GET['supprimer_id']) ? trim($_GET['supprimer_id']): null;
		$supprimerNom = !empty($_GET['supprimer_nom']) ? trim($_GET['supprimer_nom']): null;
		$idServeur = !empty($_GET['id_serveur']) ? secu($_GET['id_serveur']) : null;

		try {
			$stmt = $pdo->prepare('DELETE FROM wow_objets WHERE id = :id OR nom = :nom');
			$stmt->execute([
				'id' => (int) $supprimerId,
				'nom' => (string) $supprimerNom,
			]);
		} catch (\PDOException $e) { }

		($stmt->rowCount() > 0) ? setFlash('success', 'L’objet a été supprimé avec succès') : setFlash('danger', 'Aucun objet trouvé avec cet identifiant');

		if(!empty($idServeur))
		{
			if(isset($_GET['serveur']))
			{
				header('Location: /encheres-serveur/'.$idServeur.'-'.slug(serveurInfos($pdo, $idServeur)['nom']));
				exit;
			}

			header('Location: /'.(!empty($idServeur) ? '?id_serveur='.$idServeur : null));
			exit;
		}

		header('Location: /');
		exit;
	}

	else
	{
		setFlash('danger', 'Objet introuvable');

		header('Location: /');
		exit;
	}
}

// Serveur favori

elseif(isset($_GET['id_serveur_favori']))
{
	if(!empty($_GET['id_serveur_favori']))
	{
		$idServeur = trim($_GET['id_serveur_favori']);

		try {
			$stmt = $pdo->prepare('SELECT * FROM wow_serveurs WHERE id_blizzard = :id_serveur_favori');
			$stmt->execute(['id_serveur_favori' => (int) $idServeur]);
			$resServeurFavori = $stmt->fetch();
		} catch (\PDOException $e) { }

		if(!empty($resServeurFavori['id_blizzard']))
		{
			try {
				$stmt = $pdo->prepare('UPDATE wow_utilisateurs SET id_serveur_favori = :id_serveur_favori WHERE id = :id_utilisateur LIMIT 1');
				$stmt->execute([
					'id_serveur_favori' => (int) $idServeur,
					'id_utilisateur' => (int) $_SESSION['id_utilisateur'],
				]);
			} catch (\PDOException $e) { }

			($stmt->rowCount() > 0) ? setFlash('success', '<span class="fw-bold">'.secuChars($resServeurFavori['nom']).'</span> est maintenant votre serveur favori') : null;

			header('Location: /serveurs');
			exit;
		}

		else
		{
			setFlash('danger', 'Serveur introuvable');

			header('Location: /serveurs');
			exit;
		}
	}
}

// Changer mot de passe

elseif(isset($_POST['jetonCSRF']) AND isset($_POST['mot_de_passe']) AND isset($_POST['mot_de_passe_nouveau']) AND isset($_POST['mot_de_passe_nouveau_confirmation']))
{
	if(!empty($_POST['jetonCSRF']) AND !empty($_POST['mot_de_passe']) AND !empty($_POST['mot_de_passe_nouveau']) AND !empty($_POST['mot_de_passe_nouveau_confirmation']))
	{
		if(!CSRF::verifier($_POST['jetonCSRF'], 'formChangementMotDePasse'))
		{
			setFlash('error', 'Jeton CSRF incorrect');

			header('Location: /profil');
			exit;
		}

		try {
			$stmt = $pdo->prepare('SELECT mot_de_passe FROM wow_utilisateurs WHERE id = :id AND jeton_compte IS NULL LIMIT 1');
			$stmt->execute(['id' => (int) $_SESSION['id_utilisateur']]);
			$resChangerMdP = $stmt->fetch();
		} catch (\PDOException $e) { }

		if(mb_strlen($_POST['mot_de_passe_nouveau']) < 10)									{ setFlash('danger', 'Le mot de passe doit contenir au moins 10 caractères'); header('Location: /profil'); exit; }
		if(!preg_match('/[a-zA-Z]/', $_POST['mot_de_passe_nouveau']))						{ setFlash('danger', 'Le mot de passe doit contenir au moins une lettre'); header('Location: /profil'); exit; }
		if(!preg_match('/\d/', $_POST['mot_de_passe_nouveau']))					 			{ setFlash('danger', 'Le mot de passe doit contenir au moins un chiffre'); header('Location: /profil'); exit; }
		if(!preg_match('/[\W_]/', $_POST['mot_de_passe_nouveau']))							{ setFlash('danger', 'Le mot de passe doit contenir au moins un caractère spécial'); header('Location: /profil'); exit; }

		if($_POST['mot_de_passe_nouveau'] !== $_POST['mot_de_passe_nouveau_confirmation'])	{ setFlash('danger', 'Les mots de passe ne correspondent pas'); header('Location: /profil'); exit; }

		if(!empty($resChangerMdP['mot_de_passe']))
		{
			if(password_verify($_POST['mot_de_passe'], $resChangerMdP['mot_de_passe']))
			{
				try {
					$stmt = $pdo->prepare('UPDATE wow_utilisateurs SET mot_de_passe = :mot_de_passe WHERE id = :id_utilisateur AND jeton_compte IS NULL LIMIT 1');
					$stmt->execute([
						'id_utilisateur' => (int) $_SESSION['id_utilisateur'],
						'mot_de_passe' => (string) password_hash($_POST['mot_de_passe_nouveau'], PASSWORD_ARGON2I)
					]);
				} catch (\PDOException $e) { }

				setFlash('success', 'Votre mot de mot de passe a été changé');
			}

			else
				setFlash('danger', 'Mot de passe incorrect');
		}

		else
			setFlash('danger', 'Veuillez renseigner tous les champs');
	}

	else
		setFlash('danger', 'Veuillez renseigner tous les champs');
}

// Supprimer utilisateur

elseif(isset($_POST['jetonCSRF']) AND isset($_POST['supprimer_compte']))
{
	if(!CSRF::verifier($_POST['jetonCSRF'], 'formSuppressionValidation'))
	{
		setFlash('error', 'Jeton CSRF incorrect');

		header('Location: /profil');
		exit;
	}

	try {
		$stmt = $pdo->prepare('SELECT mot_de_passe FROM wow_utilisateurs WHERE id = :id AND jeton_compte IS NULL LIMIT 1');
		$stmt->execute(['id' => (int) $_SESSION['id_utilisateur']]);
		$resSupprimer = $stmt->fetch();
	} catch (\PDOException $e) { }

	if(!empty($resSupprimer['mot_de_passe']) AND password_verify($_POST['mot_de_passe_suppression'], $resSupprimer['mot_de_passe']))
	{
		try {
			$stmt = $pdo->prepare('DELETE FROM wow_utilisateurs WHERE id = :id');
			$stmt->execute(['id' => (int) $_SESSION['id_utilisateur']]);
		} catch (\PDOException $e) { }

		if($stmt->rowCount() > 0)
		{
			session_destroy();

			setcookie('memoriser', '', time() - 3600, '/', 'hdv.li', true, true);

			header('Location: /?compte-supprimer');
			exit;
		}

		else
			setFlash('danger', 'Erreur lors de la suppression du compte');
	}

	else
		setFlash('danger', 'Mot de passe incorrect');
}

header('Location: /profil');
exit;