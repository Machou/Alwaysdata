<?php
require_once '../config/wow_config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
	if(!CSRF::verifier($_POST['jetonCSRF'], 'formConnexion'))
	{
		setFlash('error', 'Jeton CSRF incorrect');

		header('Location: /connexion');
		exit;
	}

	else
	{
		if(empty($_POST['courriel']))								{ setFlash('danger', 'Merci d’indiquer votre courriel'); header('Location: /connexion'); exit; }
		if(mxJetable($_POST['courriel']))							{ setFlash('danger', 'Le courriel ne pas être jetable / temporaire, utilisez un service standard (Gmail, Outlook, Proton, etc.)'); header('Location: /connexion'); exit; }
		if(!filter_var($_POST['courriel'], FILTER_VALIDATE_EMAIL))	{ setFlash('danger', 'Courriel incorrect'); header('Location: /connexion'); exit; }

		if(empty($_POST['mot_de_passe']))							{ setFlash('danger', 'Merci d’indiquer votre mot de passe'); header('Location: /connexion'); exit; }
		if(mb_strlen($_POST['mot_de_passe']) < 10)					{ setFlash('danger', 'Le mot de passe doit contenir au moins 10 caractères'); header('Location: /connexion'); exit; }
		if(!preg_match('/[a-zA-Z]/', $_POST['mot_de_passe']))		{ setFlash('danger', 'Le mot de passe doit contenir au moins une lettre'); header('Location: /connexion'); exit; }
		if(!preg_match('/\d/', $_POST['mot_de_passe']))				{ setFlash('danger', 'Le mot de passe doit contenir au moins un chiffre'); header('Location: /connexion'); exit; }
		if(!preg_match('/[\W_]/', $_POST['mot_de_passe']))			{ setFlash('danger', 'Le mot de passe doit contenir au moins un caractère spécial'); header('Location: /connexion'); exit; }

		if(!empty($_POST['g-recaptcha-response']) AND recaptcha($_POST['g-recaptcha-response']))
		{
			try {
				$stmt = $pdo->prepare('SELECT * FROM wow_utilisateurs WHERE courriel = :courriel AND jeton_compte IS NULL LIMIT 1');
				$stmt->execute(['courriel' => (string) $_POST['courriel']]);
				$resConnexion = $stmt->fetch();
			} catch (\PDOException $e) { }

			if(!empty($resConnexion))
			{
				if(password_verify($_POST['mot_de_passe'], $resConnexion['mot_de_passe']))
				{
					supprimerJeton($pdo);

					seConnecter($pdo, $resConnexion['id'], $resConnexion['nom_utilisateur']);

					setFlash('success', 'Vous êtes connecté');

					header('Location: /connexion');
					exit;
				}

				setFlash('danger', 'Mot de passe incorrect');

				header('Location: /connexion');
				exit;
			}

			setFlash('danger', 'Compte inconnu');

			header('Location: /connexion');
			exit;
		}

		else
		{
			setFlash('danger', 'Erreur lors de la validation à Google reCAPTCHA');

			header('Location: /connexion');
			exit;
		}
	}
}

else
{
	require_once 'a_body.php';

	echo '<h1><a href="/connexion">Connexion</a></h1>

	<div class="col-12 col-lg-4 mx-auto">
		<form action="/connexion" method="post" id="fomrulaire-recaptcha">
			<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
			<input type="hidden" name="jetonCSRF" value="'.CSRF::generer('formConnexion').'">

			<div class="form-floating mb-3">
				<input type="email" name="courriel" class="form-control" id="floatingInputCourriel" placeholder="" required>
				<label for="floatingInputCourriel" class="form-label">Courriel</label>
			</div>
			<div class="form-floating mb-3">
				<input type="password" name="mot_de_passe" class="form-control" id="floatingInputMot-de-passe" placeholder="" pattern="'.PATTERN_MOTDEPASSE.'" required>
				<i class="fa-solid fa-eye text-dark position-absolute position-oeil toggle-mot-de-passe"></i>
				<label for="floatingInputMot-de-passe" class="form-label">Mot de passe</label>
			</div>
			<div class="row">
				<div class="col-6 text-start"><button type="submit" class="btn btn-success">Valider</button></div>
				<div class="col-6 text-end"><a href="/mot-de-passe-oublie">Mot de passe oublié ?</a></div>
			</div>
		</form>
	</div>';

	require_once 'a_footer.php';
}