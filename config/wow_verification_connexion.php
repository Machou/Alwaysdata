<?php
declare(strict_types=1);

$cookieOptions = [
	'expires' => time() + 86400 * 365,
	'path' => '/',
	'domain' => 'hdv.li',
	'secure' => true,
	'httponly' => true,
	'samesite' => 'Lax',
];

function nettoyerCookie(string $nomCookie, array $opts): void
{
	$optionsSupprimer = $opts;
	$optionsSupprimer['expires'] = time() - 3600;

	setcookie($nomCookie, '', $optionsSupprimer);
}

function insererNouveauJeton(PDO $pdo, int $idUtilisateur): string
{
	$selecteur = bin2hex(random_bytes(12));
	$validateur = bin2hex(random_bytes(32));
	$hash = hash('sha256', $validateur);

	$expiration = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->add(new DateInterval('P1Y'))->format('Y-m-d H:i:s');

	$ip = substr(getRemoteAddr(), 0, 45);
	$ua = !empty(getHttpUserAgent()) ? substr(getHttpUserAgent(), 0, 255) : 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0';

	$stmt = $pdo->prepare('INSERT INTO wow_jetons (id_utilisateur, selecteur, jeton, expiration, ip, user_agent) VALUES (:id_utilisateur, :selecteur, :jeton, :expiration, :ip, :user_agent)');
	$stmt->execute([
		'id_utilisateur' => $idUtilisateur,
		'selecteur' => $selecteur,
		'jeton' => $hash,
		'expiration' => $expiration,
		'ip' => $ip,
		'user_agent' => $ua,
	]);

	return base64_encode($selecteur . ':' . $validateur);
}

function supprimerJetonParId(PDO $pdo, int $idJeton): void
{
	$stmt = $pdo->prepare('DELETE FROM wow_jetons WHERE id = :id');
	$stmt->execute(['id' => $idJeton]);
}

function supprimerJetonParSelecteur(PDO $pdo, string $selecteur): void
{
	$stmt = $pdo->prepare('DELETE FROM wow_jetons WHERE selecteur = :selecteur');
	$stmt->execute(['selecteur' => $selecteur]);
}

if(!isset($_SESSION['connecte']) OR $_SESSION['connecte'] !== true)
{
	$raw = $_COOKIE['memoriser'] ?? '';
	if($raw !== '')
	{
		$cookieValeur = base64_decode($raw, true);
		$selecteur = null;
		$jetonClair = null;

		if($cookieValeur !== false) {
			$parties = explode(':', $cookieValeur, 2);

			if(count($parties) === 2) {
				[$selecteur, $jetonClair] = $parties;
			}
		}

		if(!empty($selecteur) AND !empty($jetonClair))
		{
			try {
				$stmt = $pdo->prepare('SELECT u.id AS id_compte, u.nom_utilisateur, j.id AS id_jeton, j.selecteur, j.jeton, j.expiration FROM wow_jetons j JOIN wow_utilisateurs u ON j.id_utilisateur = u.id WHERE j.selecteur = :selecteur AND j.expiration > NOW() AND u.jeton_compte IS NULL LIMIT 1');
				$stmt->execute(['selecteur' => $selecteur]);
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch (\PDOException $e) {
				$res = false;
			}

			if($res AND !empty($res['id_compte']) AND !empty($res['nom_utilisateur']) AND !empty($res['jeton']))
			{
				if(hash_equals(hash('sha256', $jetonClair), $res['jeton']))
				{
					if(empty($_SESSION['sid_regeneration_terminee']))
					{
						if(session_status() === PHP_SESSION_ACTIVE) {
							session_regenerate_id(true);
						}

						$_SESSION['sid_regeneration_terminee'] = true;
					}

					$_SESSION['id_utilisateur'] = (int) $res['id_compte'];
					$_SESSION['nom_utilisateur'] = (string) $res['nom_utilisateur'];
					$_SESSION['connecte'] = true;

					try {
						$pdo->beginTransaction();

						// 1) Invalider le jeton utilisé pour CE navigateur
						supprimerJetonParId($pdo, $res['id_jeton']);

						// 2) Insérer un nouveau jeton + cookie
						$valeurCookie = insererNouveauJeton($pdo, $res['id_compte']);

						$pdo->commit();

						setcookie('memoriser', $valeurCookie, $cookieOptions);
					} catch (\Throwable $e) {
						if($pdo->inTransaction()) {
							$pdo->rollBack();
						}

						nettoyerCookie('memoriser', $cookieOptions);
					}
				}

				else
				{
					try {
						$pdo->prepare('UPDATE wow_jetons SET date_maj = NOW() WHERE id = :id')->execute(['id' => (int) $res['id_jeton']]);
					} catch (\PDOException $e) { }

					nettoyerCookie('memoriser', $cookieOptions);
				}
			}

			else
				nettoyerCookie('memoriser', $cookieOptions);
		}

		else
			nettoyerCookie('memoriser', $cookieOptions);
	}
}