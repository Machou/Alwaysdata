<?php
require_once '../config/wow_config.php';

if(empty($_GET['jeton']))
{
	setFlash('danger', 'Jeton de réinitialisation incorrect. Veuillez réessayer');

	header('Location: /mot-de-passe-oublie');
	exit;
}

try {
	$stmt = $pdo->prepare('SELECT * FROM wow_utilisateurs_mdp WHERE jeton = :jeton LIMIT 1');
	$stmt->execute(['jeton' => (string) trim($_GET['jeton'])]);
	$resJeton = $stmt->fetch();
} catch (\PDOException $e) { }

if(!empty($_POST['mot_de_passe']) AND !empty($_POST['mot_de_passe_confirmation']) AND !empty($_GET['jeton']))
{
	if(!empty($resJeton['id']))
	{
		$dateExpiration = new DateTime($resJeton['expiration']);
		$maintenant = new DateTime();

		if($maintenant > $dateExpiration)
		{
			try {
				$stmt = $pdo->prepare('DELETE FROM wow_utilisateurs_mdp WHERE id = :id AND jeton = :jeton');
				$stmt->execute([
					'jeton' => (string) trim($_GET['jeton']),
					'id' => (int) $resJeton['id']
				]);
			} catch (\PDOException $e) { }

			setFlash('danger', 'Le lien pour activer votre compte a expiré. Veuillez réessayer.');

			header('Location: /mot-de-passe-oublie');
			exit;
		}

		elseif($_GET['jeton'] === $resJeton['jeton'] AND !empty($_POST['mot_de_passe']) AND !empty($_POST['mot_de_passe_confirmation']))
		{
			if($_SERVER['REQUEST_METHOD'] === 'POST')
			{
				if(!CSRF::verifier($_POST['jetonCSRF'], 'formChangerMotDePasse'))
				{
					setFlash('error', 'Jeton CSRF incorrect');

					header('Location: /changer-mot-de-passe'.(!empty($_GET['jeton']) ? '?jeton='.secuChars($_GET['jeton']) : null));
					exit;
				}

				else
				{
					if(empty($_POST['mot_de_passe']))									{ setFlash('danger', 'Merci d’indiquer votre mot de passe'); header('Location: /inscription'); exit; }
					if(mb_strlen($_POST['mot_de_passe']) < 10)							{ setFlash('danger', 'Le mot de passe doit contenir au moins 10 caractères'); header('Location: /inscription'); exit; }
					if(!preg_match('/[a-zA-Z]/', $_POST['mot_de_passe']))				{ setFlash('danger', 'Le mot de passe doit contenir au moins une lettre'); header('Location: /inscription'); exit; }
					if(!preg_match('/\d/', $_POST['mot_de_passe']))					 	{ setFlash('danger', 'Le mot de passe doit contenir au moins un chiffre'); header('Location: /inscription'); exit; }
					if(!preg_match('/[\W_]/', $_POST['mot_de_passe']))					{ setFlash('danger', 'Le mot de passe doit contenir au moins un caractère spécial'); header('Location: /inscription'); exit; }

					if(empty($_POST['mot_de_passe_confirmation']))						{ setFlash('danger', 'Veuillez confirmer votre mot de passe'); header('Location: /inscription'); exit; }
					if($_POST['mot_de_passe'] !== $_POST['mot_de_passe_confirmation'])	{ setFlash('danger', 'Les mots de passe ne correspondent pas'); header('Location: /inscription'); exit; }

					if(!empty($_POST['g-recaptcha-response']) AND recaptcha($_POST['g-recaptcha-response']))
					{
						if(!empty($resJeton['id_utilisateur']))
						{
							try {
								$stmt = $pdo->prepare('SELECT id, nom_utilisateur FROM wow_utilisateurs WHERE id = :id_utilisateur AND jeton_compte IS NULL LIMIT 1');
								$stmt->execute(['id_utilisateur' => (int) $resJeton['id_utilisateur']]);
								$resVerifUtilisateur = $stmt->fetch();
							} catch (\PDOException $e) { }

							if(!empty($resVerifUtilisateur['id']))
							{
								try {
									$stmt = $pdo->prepare('UPDATE wow_utilisateurs SET mot_de_passe = :mot_de_passe WHERE id = :id_utilisateur LIMIT 1');
									$stmt->execute([
										'mot_de_passe' => (string) password_hash($_POST['mot_de_passe'], PASSWORD_ARGON2I),
										'id_utilisateur' => (int) $resJeton['id_utilisateur']
									]);
								} catch (\PDOException $e) { }

								try {
									$stmt = $pdo->prepare('DELETE FROM wow_utilisateurs_mdp WHERE id = :id AND jeton = :jeton LIMIT 1');
									$stmt->execute([
										'id' => (int) $resJeton['id'],
										'jeton' => (string) trim($_GET['jeton'])
									]);
								} catch (\PDOException $e) { }

								session_destroy();

								seConnecter($pdo, $resVerifUtilisateur['id'], $resVerifUtilisateur['nom_utilisateur']);

								header('Location: /?mot-de-passe-change');
								exit;
							}

							else
							{
								setFlash('danger', 'Erreur avec votre compte');

								header('Location: /');
								exit;
							}
						}

						else
						{
							setFlash('danger', 'Erreur avec votre compte');

							header('Location: /');
							exit;
						}
					}

					else
					{
						setFlash('danger', 'Erreur lors de la validation à Google reCAPTCHA');

						header('Location: /inscription');
						exit;
					}
				}
			}
		}
	}
}

else
{
	if(!empty($resJeton['id']))
	{
		require_once 'a_body.php';

		echo '<h1><a href="/changer-mot-de-passe'.(!empty($_GET['jeton']) ? '?jeton='.secuChars($_GET['jeton']) : null).'">Changer mon mot de passe</a></h1>

		<div class="col-12 col-lg-4 mx-auto">
			<form action="/changer-mot-de-passe'.(!empty($_GET['jeton']) ? '?jeton='.secuChars($_GET['jeton']) : null).'" method="post" id="fomrulaire-recaptcha">
				<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
				<input type="hidden" name="jetonCSRF" value="'.CSRF::generer('formChangerMotDePasse').'">

				<div class="form-floating mb-3">
					<input type="password" name="mot_de_passe" class="form-control" id="floatingInputMotDePasseChanger" placeholder="" pattern="'.PATTERN_MOTDEPASSE.'" required>
					<i class="fa-solid fa-eye text-dark position-absolute position-oeil toggle-mot-de-passe"></i>
					<label for="floatingInputMotDePasseChanger" class="form-label">Mot de passe</label>
				</div>
				<ul id="regles-mot-de-passe">
					<li class="mb-0 text-danger" data-regle="longueur">10+ caractères minimum</li>
					<li class="mb-0 text-danger" data-regle="chiffre">1+ chiffre minimum</li>
					<li class="text-danger" data-regle="special">1+ caractère spécial (minimum</li>
				</ul>
				<div class="form-floating mb-3">
					<input type="password" name="mot_de_passe_confirmation" class="form-control" id="floatingInputMotDePasseConfirmation" placeholder="" pattern="'.PATTERN_MOTDEPASSE.'" required>
					<i class="fa-solid fa-eye text-dark position-absolute position-oeil toggle-mot-de-passe"></i>
					<label for="floatingInputMotDePasseConfirmation" class="form-label">Confirmer le mot de passe</label>
				</div>
				<div class="row">
					<div class="col-4 text-start"><button type="submit" class="btn btn-success">Valider</button></div>
				</div>
			</form>
		</div>';

		require_once 'a_footer.php';
	}

	else
	{
		setFlash('danger', 'Jeton de réinitialisation incorrect. Veuillez réessayer');

		header('Location: /mot-de-passe-oublie');
		exit;
	}
}