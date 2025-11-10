<?php
require_once '../config/wow_config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
	if(!CSRF::verifier($_POST['jetonCSRF'], 'formInscription'))
	{
		setFlash('error', 'Jeton CSRF incorrect');

		header('Location: /inscription');
		exit;
	}

	else
	{
		if(empty($_POST['courriel']))										{ setFlash('danger', 'Merci d’indiquer votre courriel'); header('Location: /inscription'); exit; }
		if(mxJetable($_POST['courriel']))									{ setFlash('danger', 'Le courriel ne pas être jetable / temporaire, utilisez un service standard (Gmail, Outlook, Proton, etc.)'); header('Location: /inscription'); exit; }
		if(!filter_var($_POST['courriel'], FILTER_VALIDATE_EMAIL))			{ setFlash('danger', 'Courriel incorrect'); header('Location: /inscription'); exit; }

		if(empty($_POST['nom_utilisateur']))								{ setFlash('danger', 'Merci d’indiquer votre nom d’utilisateur'); header('Location: /inscription'); exit; }
		if(mb_strlen($_POST['nom_utilisateur']) < 2)						{ setFlash('danger', 'Votre nom d’utilisateur est trop court'); header('Location: /inscription'); exit; }

		if(empty($_POST['mot_de_passe']))									{ setFlash('danger', 'Merci d’indiquer votre mot de passe'); header('Location: /inscription'); exit; }
		if(mb_strlen($_POST['mot_de_passe']) < 10)							{ setFlash('danger', 'Le mot de passe doit contenir au moins 10 caractères'); header('Location: /inscription'); exit; }
		if(!preg_match('/[a-zA-Z]/', $_POST['mot_de_passe']))				{ setFlash('danger', 'Le mot de passe doit contenir au moins une lettre'); header('Location: /inscription'); exit; }
		if(!preg_match('/\d/', $_POST['mot_de_passe']))					 	{ setFlash('danger', 'Le mot de passe doit contenir au moins un chiffre'); header('Location: /inscription'); exit; }
		if(!preg_match('/[\W_]/', $_POST['mot_de_passe']))					{ setFlash('danger', 'Le mot de passe doit contenir au moins un caractère spécial'); header('Location: /inscription'); exit; }

		if(empty($_POST['mot_de_passe_confirmation']))						{ setFlash('danger', 'Veuillez confirmer votre mot de passe'); header('Location: /inscription'); exit; }
		if($_POST['mot_de_passe'] !== $_POST['mot_de_passe_confirmation'])	{ setFlash('danger', 'Les mots de passe ne correspondent pas'); header('Location: /inscription'); exit; }

		if(!$_POST['cgu'])													{ setFlash('danger', 'Vous devez accepter les conditions générales d’utilisation'); header('Location: /inscription'); exit; }

		if(!empty($_POST['g-recaptcha-response']) AND recaptcha($_POST['g-recaptcha-response']))
		{
			$courriel = trim($_POST['courriel']);
			$nomUtilisateur = trim($_POST['nom_utilisateur']);
			$motDePasse = password_hash($_POST['mot_de_passe'], PASSWORD_ARGON2I);

			try {
				$stmt = $pdo->prepare('SELECT * FROM wow_utilisateurs WHERE courriel = :courriel OR nom_utilisateur = :nom_utilisateur AND jeton_compte IS NULL LIMIT 1');
				$stmt->execute([
					'courriel' => (string) $courriel,
					'nom_utilisateur' => (string) $nomUtilisateur,
				]);
				$resVerification = $stmt->fetch();
			} catch (\PDOException $e) { }

			if(!empty($resVerification['id']))
			{
				if($resVerification['courriel'] === $courriel)
				{
					setFlash('danger', 'Ce courriel est déjà utilisé');

					header('Location: /inscription');
					exit;
				}

				elseif($resVerification['nom_utilisateur'] === $nomUtilisateur)
				{
					setFlash('danger', 'Ce nom d’utilisateur est déjà utilisé');

					header('Location: /inscription');
					exit;
				}
			}

			else
			{
				$jetonCompte = bin2hex(random_bytes(32));

				try {
					$stmt = $pdo->prepare('INSERT INTO wow_utilisateurs (courriel, nom_utilisateur, mot_de_passe, ip, jeton_compte) VALUES (:courriel, :nom_utilisateur, :mot_de_passe, :ip, :jeton_compte)');
					$donnees = [
						'courriel' => (string) $courriel,
						'nom_utilisateur' => (string) $nomUtilisateur,
						'mot_de_passe' => (string) $motDePasse,
						'ip' => (string) getRemoteAddr(),
						'jeton_compte' => (string) $jetonCompte
					];
					$stmt->execute($donnees);
				} catch (\PDOException $e) { }

				$lienActivation = 'https://hdv.li/valider-inscription?activation='.urlencode($jetonCompte);
				$message = '<html><head><meta charset="utf-8"><title>Valider votre compte sur HdV.Li</title></head><body><h2>Valider votre compte sur HdV.Li</h2><p>Bonjour,</p><p>Merci de valider votre compte sur <a href="https://hdv.li/">HdV.Li</a>. Cliquez sur le lien ci-dessous :</p><p><a href="'.$lienActivation.'">Valider mon compte</a> ou</p><p>'.$lienActivation.'</p><p style="font-weight: bold;">HdV.Li</p></body></html>';
				mailWow($courriel, '[HdV.Li] Validation de votre compte', $message);

				setFlash('success', 'Votre compte a été créé, merci de le confirmer');

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

else
{
	require_once 'a_body.php';

	echo '<h1><a href="/inscription">Créer un compte</a></h1>

	<div class="col-12 col-lg-4 mx-auto">
		<form action="/inscription" method="post" id="fomrulaire-recaptcha">
			<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
			<input type="hidden" name="jetonCSRF" value="'.CSRF::generer('formInscription').'">

			<div class="form-floating mb-3">
				<input type="email" name="courriel" class="form-control" id="floatingInputCourriel" placeholder="" required>
				<label for="floatingInputCourriel" class="form-label">Courriel</label>
			</div>
			<div class="form-floating mb-3">
				<input type="text" name="nom_utilisateur" class="form-control" id="floatingInputNomUtilisateur" placeholder="" required>
				<label for="floatingInputNomUtilisateur" class="form-label">Nom d’utilisateur</label>
			</div>
			<div class="form-floating mb-3">
				<input type="password" name="mot_de_passe" class="form-control" id="floatingInputMotDePasse" placeholder="" pattern="'.PATTERN_MOTDEPASSE.'" required>
				<i class="fa-solid fa-eye text-dark position-absolute position-oeil toggle-mot-de-passe"></i>
				<label for="floatingInputMotDePasse" class="form-label">Mot de passe</label>
			</div>
			<div class="form-floating mb-3">
				<input type="password" name="mot_de_passe_confirmation" class="form-control" id="floatingInputMotDePasseConfirmation" placeholder="" pattern="'.PATTERN_MOTDEPASSE.'" required>
				<i class="fa-solid fa-eye text-dark position-absolute position-oeil toggle-mot-de-passe"></i>
				<label for="floatingInputMotDePasseConfirmation" class="form-label">Confirmer le mot de passe</label>
			</div>
			<div class="form-check mb-3">
				<input type="checkbox" name="cgu" class="form-check-input" id="floatingInputCgu">
				<label for="floatingInputCgu" class="form-check-label">J’accepte les <a href="#" data-bs-toggle="modal" data-bs-target="#modalCgu">Conditions Générales d’Utilisation (CGU)</a></label>
			</div>
			<div class="row">
				<div class="col-4 text-start"><button type="submit" class="btn btn-success">Valider</button></div>
				<div class="col-8 text-end"><a href="/connexion">Vous avez déjà un compte ?</a></div>
			</div>
		</form>

		<div class="modal fade" id="modalCgu" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<p class="mb-0 modal-title" id="titreCgu">Détails supplémentaires à propos de <span class="fw-bold">Conditions Générales d’Utilisation (CGU)</span></p>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
					</div>
					<div class="modal-body py-0">'.get('https://hdv.li/legal-cgu.php').'</div>
					<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button></div>
				</div>
			</div>
		</div>
	</div>';

	require_once 'a_footer.php';
}