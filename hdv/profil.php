<?php
require_once '../config/wow_config.php';

if(!empty($_POST['ajouterServeur']) AND !empty($_POST['ajouterNomPersonnage']))
{
	$idServeurFormulaire = trim($_POST['ajouterServeur']);
	$nomFormulaire = trim($_POST['ajouterNomPersonnage']);

	$nomFormulaire = trim(ucfirst(strtolower($nomFormulaire)));
	$longueur = mb_strlen($nomFormulaire, 'UTF-8');
	if($longueur <= 2)								{ setFlash('danger', 'Votre pseudo est trop court'); header('Location: /profil'); exit; }
	if($longueur >= 15)								{ setFlash('danger', 'Votre pseudo est trop long'); header('Location: /profil'); exit; }
	if(!preg_match('/^\p{L}+$/u', $nomFormulaire))	{ setFlash('danger', 'Erreur lors de validation de votre pseudo'); header('Location: /profil'); exit; }
	if(preg_match('/\s/u', $nomFormulaire))			{ setFlash('danger', 'Erreur lors de validation de votre pseudo'); header('Location: /profil'); exit; }

	$personnageInfosSql = personnageInfosSql($pdo, $idServeurFormulaire, $nomFormulaire);
	$infosServeur = serveurInfos($pdo, $idServeurFormulaire);

	// Le personnage n’existe pas

	if(empty($personnageInfosSql) AND !empty($infosServeur['nom']))
	{
		$personnageInfos = personnageInfos($pdo, $apiClient, $infosServeur['nom'], $nomFormulaire);

		personnageAjouter($pdo, $personnageInfos);
	}

}

// Le personnage existe

elseif(!empty($personnageInfosSql) AND empty($_POST['ajouterServeur']) AND empty($_POST['ajouterNomPersonnage']) AND !empty($_GET['serveur']) AND !empty($_GET['nom']))
{
	$dateDerniereConnexion = ($personnageInfos['derniere_connexion_wow'] / 1000);
	$derniereConnexion = dateFormat($dateDerniereConnexion, 'c');

	$equipements = [
		'HEAD' => 'Tête', 'NECK' => 'Cou', 'SHOULDER' => 'Épaules', 'SHIRT' => 'Chemise', 'CHEST' => 'Torse', 'WAIST' => 'Taille', 'LEGS' => 'Jambes', 'FEET' => 'Pieds',
		'WRIST' => 'Poignets', 'HANDS' => 'Mains', 'FINGER_1' => '1er anneau', 'FINGER_2' => '2e anneau', 'TRINKET_1' => '1er bijou', 'TRINKET_2' => '2e bijou', 'BACK' => 'Dos'
	];

	$classe = $personnageInfos['classe_nom'];
	$classeSlug = slug($personnageInfos['classe_nom']);
	$idPersonnage = $personnageInfos['id_blizzard'];
	$serveur = $personnageInfos['serveur_nom'];
	$serveurSlug = slug($personnageInfos['serveur_nom']);
	$faction = $personnageInfos['faction_nom'];
	$race = $personnageInfos['race_nom'];
	$raceId = $personnageInfos['race_id'];
	$genre = $personnageInfos['genre_nom'];
	$classe = $personnageInfos['classe_nom'];
	$nom = $personnageInfos['nom'];
	$hf = $personnageInfos['hauts_faits'];
	$ilvlMoyen = $personnageInfos['ilvl_moyen'];
	$ilvlEquipe = $personnageInfos['ilvl_equipe'];
	$reputations = $personnageInfos['reputations'];
	$reputationsExaltes = $personnageInfos['reputations_exaltes'];
	$quetesTerminees = $personnageInfos['quetes_terminees'];
	$niveauHonneur = $personnageInfos['niveau_honneur'];
	$victoiresHonorables = $personnageInfos['victoires_honorables'];
	$avatar = $personnageInfos['avatar'];
	$avatarInset = $personnageInfos['avatar_inset'];
	$avatarRaw = $personnageInfos['avatar_raw'];
	$objet_1 = !empty($persoBdd['objet_validation_1']) ? $equipements[$persoBdd['objet_validation_1']] : null;
	$objet_2 = !empty($persoBdd['objet_validation_2']) ? $equipements[$persoBdd['objet_validation_2']] : null;

	$persoApi = !empty($persoBdd['id']) ? array_merge(['id' => $persoBdd['id'], 'est_confirmer' => $persoBdd['est_confirmer']], $personnageInfos) : $personnageInfos;

	$informationPersonnage[] = '<div>
		<div class=" border rounded text-center fs-3 mb-5 mx-auto p-3 box-perso bg-personnage bg-'.$classeSlug.'">
			<a href="'.$avatarRaw.'" data-fancybox="gallerie"><img src="'.$avatar.'" class="avatar rounded" alt="Avatar '.$classe.'" title="Avatar '.$classe.'"></a>
			<div>
				<p><span class="'.$classeSlug.' fw-bold">'.$nom.'</span> @ '.$serveur.'</p>
				<p><span class="faction-'.strtolower($faction).'">'.$faction.' '.$race.'</span> <span class="'.$classeSlug.' fw-bold">'.$classe.'</span></p>
				<p><span class="fw-bold">'.number_format($hf).'</span> points de hauts faits</p>
				<p><span class="fw-bold">iLvl Moyen</span> : '.$ilvlMoyen.' <span class="small">(iLvl équipé : <span class="fw-bold">'.$ilvlEquipe.'</span>)</span></p>
				<p><span class="fw-bold">'.number_format($reputations).'</span> réputations (<span class="fw-bold small">'.number_format($reputationsExaltes).'</span> exaltées)</p>
				<p><span class="fw-bold">'.number_format($victoiresHonorables).'</span> victoires honorables — <span class="fw-bold">'.$niveauHonneur.'</span> Niveau d’honneur</p>
				<p class="mb-0">Dernière connexion à World of Warcraft le <time datetime="'.date(DATE_ATOM, $dateDerniereConnexion).'" class="fw-light">'.$derniereConnexion.'</time></p>
			</div>
			<div class="row my-4">
				<div class="col-12">
					<a href="https://raider.io/characters/eu/'.$serveurSlug.'/'.$nom.'" '.$onclick.'"><img src="/assets/img/logo-wow-raiderio.svg" class="logo-site" alt="Logo Raider.io" title="Logo Raider.io" ></a></a>
					<a href="https://worldofwarcraft.com/fr-fr/character/eu/'.$serveurSlug.'/'.$nom.'" '.$onclick.'"><img src="/assets/img/logo-wow-battlenet.svg" class="logo-site mx-4 my-4 my-lg-0" alt="Logo Battle.net" title="Logo Battle.net"></a>
					<a href="https://www.warcraftlogs.com/character/eu/'.$serveurSlug.'/'.$nom.'/" '.$onclick.'"><img src="/assets/img/logo-wow-warcraftlogs.png" class="logo-site" alt="Logo Warcraft Logs" title="Logo Warcraft Logs"></a>
				</div>
			</div>';

			if($persoBdd['est_confirmer'] == 0)
			{
				$informationPersonnage[] = '<div class="row">
					<div class="col-12">
						<div data-bs-toggle="tooltip" data-bs-title="Confirmer votre personnage">
							<a href="#" class="fs-1 fw-bold" data-bs-toggle="modal" data-bs-target="#modalConfirmerPersonnage">
								<i class="fa-solid fa-arrow-right-long text-success fa-xl"></i> Vérifier <span class="'.$classeSlug.'">'.$nom.'</span> <i class="fa-solid fa-arrow-left-long text-success fa-xl"></i>
							</a>
						</div>
					</div>
				</div>';
			}

		$informationPersonnage[] = '</div>';

		if($persoBdd['est_confirmer'] == 0)
		{
			$informationPersonnage[] = '<div class="modal fade" id="modalConfirmerPersonnage" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
					<div class="modal-content">
						<div class="modal-body py-0">
							<h1>Vérification</h1>

							<p>Si <span class="'.$classeSlug.' fw-bold">'.$nom.'</span> @ <span class="fw-bold">'.$serveur.'</span> vous appartient, suivez les étapes ci-dessous pour confirmer le personnage sur votre compte.</p>

							<ol>
								<li>Connectez-vous à votre personnage sur World of Warcraft</li>
								<li><span>Retirez les objets suivant de votre armure :</span>
									<ul>
										<li>'.$objet_1.'</li>
										<li>'.$objet_2.'</li>
									</ul>
								</li>
								<li>Déconnectez-vous</li>
								<li>Ensuite, validez ici !</li>
							</ol>

							<p class="text-center"><a href="https://hdv.li/profil/" class="btn btn-success text-white">Valider</a></p>
						</div>
						<div class="modal-footer">
							<div class="input-group">
								<div class="input-group-text "><a href="https://hdv.li/profil/" '.$onclick.'>#</a></div>
									<input type="text" value="https://hdv.li/profil/" class="form-control curseur" '.$onclickSelect.'>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>';
		}

	$informationPersonnage[] = '</div>';

	$_SESSION['$informationPersonnage'] = $informationPersonnage;

		header('Location: /profil/eu/'.$serveurSlug.'/'.$nom);
}

require_once 'a_body.php';

if(isset($_POST['btnSuppressionCompte']))
{
	echo '<h1>Supprimer mon compte</h1>

	<p class="text-center text-danger fw-bold">Pour confirmer la suppression de votre compte, merci de saisir votre mot de passe.</p>

	<form action="/profil-verification" method="post" class="needs-validation" id="formSuppressionValidation" novalidate>
		<input type="hidden" name="jetonCSRF" value="'.CSRF::generer('formSuppressionValidation').'">

		<div class="row">
			<div class="col-12 col-lg-4 mx-auto text-center">
				<div class="form-floating mb-3">
					<input type="password" name="mot_de_passe_suppression" class="form-control border-3 border-danger pe-5" id="motDePasseSuppression" placeholder="" pattern="'.PATTERN_MOTDEPASSE.'" required>
					<label for="motDePasseSuppression">Mot de passe</label>
					<i class="fa-solid fa-eye text-dark position-absolute position-oeil toggle-mot-de-passe"></i>
					<div class="invalid-feedback">Mot de passe incorrect</div>
				</div>

				<button type="submit" name="supprimer_compte" class="btn btn-danger" form="formSuppressionValidation">Je valide</button>
				<a href="/profil" class="btn btn-primary">Annuler</a>
			</div>
		</div>
	</form>';
}

elseif(isset($_GET['ajouterPersonnage']))
{
	echo '<h1><a href="/profil/ajouter-personnage">Ajouter mon personnage</a></h1>';

	if(!empty($_SESSION['formulaire_confirmation']))
	{
		echo implode($_SESSION['formulaire_confirmation']);

		unset($_SESSION['formulaire_confirmation']);
	}

	else
	{
		$idServeurSql = !empty($resUtilisateur['id_serveur_favori']) ? $resUtilisateur['id_serveur_favori'] : (!empty($resUtilisateur['id_serveur']) ? $resUtilisateur['id_serveur'] : null);

		echo alerte('info', 'Vous devez vous connecter à <strong>World of Warcraft</strong>, sinon vous ne pourrez pas confirmer votre personnage !', 'mx-auto col-12 col-lg-8 mb-4').'

		<form action="/profil/ajouter-personnage" method="post" id="personnageMaj">
			<div class="row border rounded mx-auto py-4">
				<div class="col-6 col-lg-3 ms-auto">'.afficherServeursHtml($pdo, $idServeurSql).'</div>
				<div class="col-6 col-lg-3 me-auto">
					<div class="input-group">
						<input type="text" name="ajouterNomPersonnage" '.(!empty($resUtilisateur['nom_personnage']) ? 'value="'.secuChars($resUtilisateur['nom_personnage']).'"' : null).' class="form-control" placeholder="Nom du personnage" aria-describedby="btnMascotte" required>
						<button type="submit" class="btn btn-success" form="personnageMaj">Ok</button>
					</div>
				</div>
			</div>
		</form>';
	}
}

elseif(empty($_POST['ajouterServeur']) AND empty($_POST['ajouterNomPersonnage']) AND !empty($_GET['serveur']) AND !empty($_GET['nom']))
{

}

else
{
	echo '<div class="mt-5">
		<p class="bienvenue">Bienvenue, <strong>'.secuChars($resUtilisateur['nom_utilisateur']).'</strong>.</p>

		<div class="btn-group d-flex flex-row justify-content-center mb-5" id="pills-tab" role="tablist">
			<a href="#pills-informations-tab"	class="btn btn-outline-light active"	id="pills-informations-tab"		data-bs-toggle="tab" data-bs-target="#pills-informations"	role="tab" aria-controls="pills-informations"		aria-selected="true">	<i class="fa-solid fa-list-ul me-lg-2"></i><span class="d-none d-lg-inline-block">Informations</span></a>
			<a href="#pills-compte-tab"			class="btn btn-outline-light"			id="pills-compte-tab"			data-bs-toggle="tab" data-bs-target="#pills-compte"			role="tab" aria-controls="pills-compte"				aria-selected="false"	><i class="fa-solid fa-user me-lg-2"></i><span class="d-none d-lg-inline-block">Mon Compte</span></a>
			<a href="#pills-sessions-tab"		class="btn btn-outline-light"			id="pills-sessions-tab"			data-bs-toggle="tab" data-bs-target="#pills-sessions"		role="tab" aria-controls="pills-sessions"			aria-selected="false"	><i class="fa-solid fa-network-wired me-lg-2"></i><span class="d-none d-lg-inline-block">Mes Sessions</span></a>
			<a href="#pills-faq-tab"			class="btn btn-outline-light"			id="pills-faq-tab"				data-bs-toggle="tab" data-bs-target="#pills-faq"			role="tab" aria-controls="pills-faq"				aria-selected="false"	><i class="fa-solid fa-robot me-lg-2"></i><span class="d-none d-lg-inline-block">FAQ</span></a>
		</div>

		<div class="tab-content" id="pills-tabContent">
			<div class="tab-pane fade show active" id="pills-informations" role="tabpanel" aria-labelledby="pills-informations-tab" tabindex="0">';

				$idServeurFavori = !empty($resUtilisateur['id_serveur_favori']) ? secu($resUtilisateur['id_serveur_favori']) : null;
				$infosServeurFav = !empty($idServeurFavori) ? serveurInfos($pdo, $idServeurFavori) : null;
				$localeDrapeauFav = (!empty($infosServeurFav['locale']) AND $infosServeurFav['locale'] === 'enGB') ? 'gb' : (!empty($infosServeurFav['locale']) ? strtolower(substr($infosServeurFav['locale'], 0, 2)) : null);
				$paysServeurFav = '<span>'.isoEmoji($localeDrapeauFav).'</span>';
				$nomServeurFav = !empty($infosServeurFav['nom']) ? secuChars($infosServeurFav['nom']) : null;
				$serveur = !empty($resUtilisateur['serveur_nom']) ? secuChars($resUtilisateur['serveur_nom']) : null;
				$race = !empty($resUtilisateur['race_nom']) ? secuChars($resUtilisateur['race_nom']) : null;
				$classe = !empty($resUtilisateur['classe_nom']) ? secuChars($resUtilisateur['classe_nom']) : null;
				$nomPersonnage = !empty($resUtilisateur['nom_personnage']) ? secuChars($resUtilisateur['nom_personnage']) : null;
				$estConfirmer = $resUtilisateur['est_confirmer'] ?? false;

				echo '<div class="col-12 col-lg-4 mx-auto">
					<h4 class="mb-4"><a href="#mes-informations" class="text-decoration-none">Mes informations</a></h4>

					<div>
						<p><i class="fa-regular fa-id-badge me-2"></i>ID : <span class="fw-bold">'.$resUtilisateur['id'].'</span></p>
						<p><i class="fa-regular fa-user me-2"></i>Nom d’utilisateur : <span class="fw-bold">'.$resUtilisateur['nom_utilisateur'].'</span></p>
						<p><i class="fa-regular fa-calendar-plus me-2"></i>Date d’inscription : <span class="fw-bold">'.dateFormat($resUtilisateur['date_creation'], 'c').'</span></p>
						<p><i class="fa-regular fa-clock me-2"></i>Dernière visite : '.(!empty($resUtilisateur['derniere_visite']) ? '<span class="fw-bold">'.dateFormat($resUtilisateur['derniere_visite'], 'c').'</span>' : 'n/a').'</p>
						<p><i class="fa-solid fa-flag me-2"></i>Faction favorite : '.((!empty($_COOKIE['faction']) AND $_COOKIE['faction'] === 'alliance') ? '<span class="faction-alliance">Alliance</span>' : '<span class="faction-horde">Horde</span>').'</p>
						<p><i class="fa-solid fa-server me-2"></i>Serveur favori : '.(empty($nomServeurFav) ? '<a href="/serveurs" class="btn btn-success btn-sm" title="Modifier le serveur favori">Choisir mon serveur favori</a>' : $paysServeurFav.' <a href="/serveurs">'.$nomServeurFav.'</a>').'</p>';

						if(!empty($nomPersonnage) AND !empty($classe))
						{
							echo '<p><i class="fa-solid fa-person-running me-2"></i>Mon personnage : <a href="/profil/eu/'.slug($serveur).'/'.slug($nomPersonnage).'" class="'.slug($classe).'" title="Modifier le nom de personnage">'.$nomPersonnage.'</a>'.(!empty($serveur) ? ' @ '.$serveur : null);
							echo '<span class="ms-1">'.($estConfirmer ? '<i class="fa-solid fa-circle-check text-success align-middle" data-bs-toggle="tooltip" data-bs-title="Personnage confirmé"></i>' : '<i class="fa-solid fa-circle-xmark text-danger align-middle" data-bs-toggle="tooltip" data-bs-title="Personnage non confirmé"></i>').'</span></p>';

							echo '<p><a href="/profil/eu/'.$slugNomServeur.'/'.$nom.'">Modifier mon personnage</a></p>';

						}

						else
						{
							echo '<p><a href="/profil/ajouter-personnage" class="btn btn-success">Ajouter mon personnage</a>';

						}

					echo '</div>
				</div>
			</div>
			<div class="tab-pane fade" id="pills-compte" role="tabpanel" aria-labelledby="pills-compte-tab" tabindex="0">
				<div class="col-12 col-lg-8 mx-auto">
					<div id="mon-courriel">
						<h4 class="mb-4"><a href="#mon-courriel" class="text-decoration-none">Mon courriel</a></h4>

						<div class="form-floating mb-3">
							<input type="email" value="'.secuChars($resUtilisateur['courriel']).'" class="form-control is-valid" id="courrielActuel" placeholder="" readonly>
							<label for="courrielActuel">Mon courriel</label>
						</div>

						<p class="mb-0">Pour modifier votre courriel, merci de contacter l’administrateur à : contact@hdv.li</p>
					</div>

					<div class="col-12 mx-auto">
						<hr class="my-5">
					</div>

					<div id="changer-mot-de-passe">
						<h4 class="mb-4"><a href="#changer-mot-de-passe" class="text-decoration-none">Changer mon mot de passe</a></h4>

						<form action="/profil-verification" method="post" class="needs-validation" id="changerMotDePasse" novalidate>
							<input type="hidden" name="jetonCSRF" value="'.CSRF::generer('formChangementMotDePasse').'">

							<div class="mb-3">
								<div class="form-floating mb-3">
									<input type="password" name="mot_de_passe" class="form-control" id="motDePasseActuel" placeholder="Mot de passe actuel" pattern="'.PATTERN_MOTDEPASSE.'" required>
									<label for="motDePasseActuel">Mot de passe actuel</label>
									<i class="fa-solid fa-eye text-dark position-absolute position-oeil toggle-mot-de-passe" data-target="motDePasseActuel"></i>
									<div class="invalid-feedback">Mot de passe incorrect</div>
								</div>
							</div>

							<hr class="my-4">

							<div class="form-floating mb-3">
								<input type="password" name="mot_de_passe_nouveau" class="form-control" id="motDePasse" placeholder="" pattern="'.PATTERN_MOTDEPASSE.'" required>
								<label for="motDePasse">Nouveau mot de passe</label>
								<i class="fa-solid fa-eye text-dark position-absolute position-oeil toggle-mot-de-passe"></i>
								<div class="invalid-feedback">Votre nouveau mot de passe est incorrecte</div>
							</div>

							<div class="form-floating mb-3">
								<input type="password" name="mot_de_passe_nouveau_confirmation" class="form-control" id="motDePasseConfirmation" placeholder="" pattern="'.PATTERN_MOTDEPASSE.'" required>
								<label for="motDePasseConfirmation">Confirmer le mot de passe</label>
								<i class="fa-solid fa-eye text-dark position-absolute position-oeil toggle-mot-de-passe"></i>
								<div class="invalid-feedback">Merci de confirmer votre nouveau mot de passe</div>
							</div>

							<div class="row">
								<div class="col-6 text-start"><button type="submit" class="btn btn-success" form="changerMotDePasse">Valider</button></div>
								<div class="col-6 text-end"><a href="/deconnexion?motDePasseOublie" onclick="return confirm(\'Vous allez être déconnecter, continuez ?\')">Mot de passe oublié ?</a></div>
							</div>
						</form>
					</div>

					<div class="col-12 mx-auto">
						<hr class="my-5">
					</div>

					<div class="text-center" id="zone-danger">
						<h4 class="mb-4"><a href="#zone-danger" class="text-decoration-none text-danger fw-bold">Zone Danger</a></h4>

						<form action="/profil" method="post" id="formSuppression">
							<button type="submit" name="btnSuppressionCompte" class="btn btn-danger" form="formSuppression">Supprimer mon compte</button>
						</form>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="pills-sessions" role="tabpanel" aria-labelledby="pills-sessions-tab" tabindex="0">
				<div class="col-12 col-lg-8 mx-auto">
					<h4 class="mb-4"><a href="#mes-sessions" class="text-decoration-none">Mes Sessions</a></h4>';

					try {
						$stmt = $pdo->prepare('SELECT * FROM wow_jetons WHERE id_utilisateur = :id_utilisateur ORDER BY id DESC LIMIT 50');
						$stmt->execute(['id_utilisateur' => (int) $_SESSION['id_utilisateur']]);
						$resSessions = $stmt->fetchAll();
					} catch (\PDOException $e) { }

					echo '<div class="container" id="uaparser">
						<div class="row border-bottom pb-3">
							<div class="col-2 fw-bold">Date</div>
							<div class="col-6 fw-bold">IP</div>
							<div class="col-4 fw-bold">User Agent</div>
						</div>';

						foreach($resSessions as $session)
						{
							$ip = secuChars($session['ip']);
							$userAgent = secuChars($session['user_agent']);

							if(!empty($_COOKIE['memoriser']))
							{
								$cookieValeur = base64_decode($_COOKIE['memoriser']);
								$jetonParts = explode(':', $cookieValeur);

								if(count($jetonParts) === 2)
								{
									list($selecteur, $jeton) = $jetonParts;

									$maSession = hash_equals(hash('sha256', $jeton), $session['jeton']) ? true : false;;
								}

								else
									$maSession = null;
							}

							else
								$maSession = null;

							$tempsSessions = temps(strtotime($session['date_creation']));

							echo '<div class="row border-bottom py-3'.($maSession ? ' text-success' : null).'">
								<div class="col-2 text-wrap'.($tempsSessions === 'Ajourd’hui' ? ' text-truncate' : null).'" data-bs-toggle="tooltip" data-bs-title="Créée le '.dateFormat(strtotime($session['date_creation'])).'">'.$tempsSessions.'</div>
								<div class="col-6 text-wrap text-truncate" data-bs-toggle="tooltip" data-bs-title="IP de la session : '.$ip.'">'.$ip.'</div>
								<div class="col-4 text-wrap session" data-bs-toggle="tooltip" data-bs-title="User Agent : '.$userAgent.'">'.$userAgent.'</div>
							</div>';
						}

					echo '</div>
				</div>
			</div>
			<div class="tab-pane fade" id="pills-faq" role="tabpanel" aria-labelledby="pills-faq-tab" tabindex="0">
				<div class="col-12 col-lg-8 mx-auto">
					<h4 class="mb-4"><a href="#faq" class="text-decoration-none">FAQ</a></h4>

					<div class="accordion" id="accordionFaq">
						<div class="accordion-item">
							<h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseA" aria-expanded="false" aria-controls="collapseA">Comment sont mis à jour les prix des objets que j’ajoute dans mon inventaire ?</button></h2>
							<div class="accordion-collapse collapse" id="collapseA" data-bs-parent="#accordionFaq">
								<div class="accordion-body">
									<p>Étant donné les restrictions de l’API Blizzard, la seule solution viable pour le moment consiste à effectuer une mise à jour manuelle.</p>

									<p>Chaque serveur doit être vérifié toutes les <span class="fw-bold">6 heures</span>.</p>

									<p class="mb-0">Cette contrainte devrait évoluer à l’avenir, le temps de mettre en place une solution plus stable.</p>
								</div>
							</div>
						</div>
						<div class="accordion-item">
							<h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB" aria-expanded="false" aria-controls="collapseB">Pourquoi Blizzard restreint l’accès à ses API ?</button></h2>
							<div class="accordion-collapse collapse" id="collapseB" data-bs-parent="#accordionFaq">
								<div class="accordion-body">
									<p><strong>Blizzard</strong> propose des API publiques permettant d’accéder aux données de ses jeux, comme <strong>World of Warcraft</strong>. Toutefois, cet accès est encadré par des restrictions strictes, notamment pour des raisons de sécurité, de stabilité des serveurs et de gestion du trafic. De plus, les appels sont soumis à des quotas (rate limits), qui limitent le nombre de requêtes autorisées par minute.</p>

									<p>Blizzard ne fournit pas de méthode directe pour récupérer des listes complètes (comme tous les objets du jeu). À la place, il faut connaître ou deviner les identifiants des objets et les interroger un par un, ce qui rend la constitution de bases de données complètes plus complexe et longue.</p>

									<p class="mb-0">Ces limitations visent à préserver les ressources de Blizzard et à garantir un usage raisonnable de l’API, essentiellement pour des applications communautaires, des sites de guilde, ou des outils personnels - et non des usages massifs automatisés.</p>
								</div>
							</div>
						</div>

						<div class="accordion-item">
							<h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseC" aria-expanded="false" aria-controls="collapseC">Combien d’objet puis-je ajouter ?</button></h2>
							<div class="accordion-collapse collapse" id="collapseC" data-bs-parent="#accordionFaq">
								<div class="accordion-body">
									<p>Les clients API sont limités à <span class="fw-bold">36.000 requêtes par heure</span>, à un rythme de <span class="fw-bold">100 requêtes par seconde</span>.</p>

									<p>Dépasser ce quota horaire ralentit le service jusqu’à ce que le trafic diminue.</p>

									<p class="mb-0">Dépasser la limite par seconde génère une <span class="text-danger">erreur 429</span> pendant le reste de la seconde, jusqu’à l’actualisation du quota.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>';
}

require_once 'a_footer.php';