<?php
require_once '../config/wow_config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
	if(!CSRF::verifier($_POST['jetonCSRF'], 'formMotDePasseReinitialiser'))
	{
		setFlash('error', 'Jeton CSRF incorrect');

		header('Location: /mot-de-passe-oublie');
		exit;
	}

	else
	{
		if(empty($_POST['courriel']))								{ setFlash('danger', 'Merci d’indiquer votre courriel'); header('Location: /mot-de-passe-oublie'); exit; }
		if(mxJetable($_POST['courriel']))							{ setFlash('danger', 'Le courriel ne pas être jetable / temporaire, utilisez un service standard (Gmail, Outlook, Proton, etc.)'); header('Location: /mot-de-passe-oublie'); exit; }
		if(!filter_var($_POST['courriel'], FILTER_VALIDATE_EMAIL))	{ setFlash('danger', 'Courriel incorrect'); header('Location: /mot-de-passe-oublie'); exit; }

		if(!empty($_POST['g-recaptcha-response']) AND recaptcha($_POST['g-recaptcha-response']))
		{
			$courriel = trim($_POST['courriel']);

			try {
				$stmt = $pdo->prepare('SELECT * FROM wow_utilisateurs WHERE courriel = :courriel AND jeton_compte IS NULL LIMIT 1');
				$stmt->execute(['courriel' => (string) $_POST['courriel']]);
				$resVerification = $stmt->fetch();
			} catch (\PDOException $e) { }

			if(!empty($resVerification['id']))
			{
				$jeton = bin2hex(random_bytes(32));
				$expiration = (new DateTime('+30 minutes'))->format('Y-m-d H:i:s');

				$stmt = $pdo->prepare('INSERT INTO wow_utilisateurs_mdp (id_utilisateur, jeton, expiration) VALUES (:id_utilisateur, :jeton, :expiration)');
				$stmt->execute([
					'id_utilisateur' => (int) $resVerification['id'],
					'jeton' => (string) $jeton,
					'expiration' => (string) $expiration
				]);

				$lien = 'https://hdv.li/changer-mot-de-passe?jeton='.urlencode($jeton);
				$message = '<html><head><meta charset="utf-8"><title>Réinitialisation du mot de passe</title></head><body><h2>Réinitialisation de votre mot de passe</h2><p>Bonjour,</p><p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien ci-dessous :</p><p><a href="'.$lien.'">Réinitialiser mon mot de passe</a> ou</p><p>'.$lien.'</p><p>Ce lien expirera dans 30 minutes.</p><p>Si ce n’était pas vous, ignorez simplement ce message.</p><p>HdV.Li</p></body></html>';
				mailWow($courriel, '[HdV.Li] Réinitialisation de votre mot de passe', $message);
			}

			setFlash('success', 'Si un compte existe pour le courriel <span class="fw-bold">'.secuChars($courriel).'</span>, vous allez recevoir un lien pour vous permettre de réinitialiser votre mot de passe');

			header('Location: /');
			exit;
		}

		else
		{
			setFlash('danger', 'Erreur lors de la validation à Google reCAPTCHA');

			header('Location: /mot-de-passe-oublie');
			exit;
		}
	}
}

else
{
	require_once 'a_body.php';

	echo '<h1><a href="/mot-de-passe-oublie">Mot de passe oublié</a></h1>

	<div class="col-12 col-lg-4 mx-auto">
		<form action="/mot-de-passe-oublie" method="post" id="fomrulaire-recaptcha">
			<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
			<input type="hidden" name="jetonCSRF" value="'.CSRF::generer('formMotDePasseReinitialiser').'">

			<div class="form-floating mb-3">
				<input type="email" name="courriel" class="form-control" id="floatingInputCourriel" placeholder="" required>
				<label for="floatingInputCourriel" class="form-label">Courriel</label>
			</div>
			<div class="row">
				<div class="col-12 text-start"><button type="submit" class="btn btn-success">Valider</button></div>
			</div>
		</form>
	</div>';

	require_once 'a_footer.php';
}