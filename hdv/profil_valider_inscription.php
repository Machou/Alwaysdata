<?php
require_once '../config/wow_config.php';

if(!empty($_GET['activation']))
{
	$jetonCompte = trim($_GET['activation']);

	try {
		$stmt = $pdo->prepare('SELECT * FROM wow_utilisateurs WHERE jeton_compte = :jeton_compte AND jeton_compte IS NOT NULL LIMIT 1');
		$stmt->execute(['jeton_compte' => (string) $jetonCompte]);
		$resJeton = $stmt->fetch();
	} catch (\PDOException $e) { }

	if(!empty($resJeton['id']) AND !empty($resJeton['nom_utilisateur']))
	{
		$dateExpiration = new DateTime($resJeton['date_creation']);
		$dateExpiration->modify('+2 hours');
		$maintenant = new DateTime();

		if($maintenant > $dateExpiration)
		{
			try {
				$stmt = $pdo->prepare('DELETE FROM wow_utilisateurs WHERE id = :id_utilisateur LIMIT 1');
				$stmt->execute(['id_utilisateur' => (int) $resJeton['id']]);
			} catch (\PDOException $e) { }

			setFlash('danger', 'Le lien pour activer votre compte a expiré. Veuillez refaire une demande');

			header('Location: /inscription');
			exit;
		}

		else
		{
			if($jetonCompte === $resJeton['jeton_compte'])
			{
				try {
					$stmt = $pdo->prepare('UPDATE wow_utilisateurs SET jeton_compte = NULL WHERE id = :id_utilisateur LIMIT 1');
					$stmt->execute(['id_utilisateur' => (int) $resJeton['id']]);
				} catch (\PDOException $e) { }

				seConnecter($pdo, $resJeton['id'], $resJeton['nom_utilisateur']);

				setFlash('success', 'Inscription confirmée');

				header('Location: /');
				exit;
			}
		}
	}

	else
	{
		setFlash('danger', 'Erreur avec votre inscription. Veuillez refaire une demande');

		header('Location: /inscription');
		exit;
	}
}

else
{
	setFlash('danger', 'Erreur avec votre inscription. Veuillez refaire une demande');

	header('Location: /inscription');
	exit;
}