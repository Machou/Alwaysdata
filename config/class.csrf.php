<?php
class CSRF {
	private const CLE_SESSION = '_CSRF';

	public static function generer(): string
	{
		self::verifierSession();

		$jeton = self::creerJeton();

		$_SESSION[self::CLE_SESSION] = $jeton;

		return $jeton;
	}

	public static function verifier(string $jeton): bool
	{
		self::verifierSession();

		if(!isset($_SESSION[self::CLE_SESSION])) {
			return false;
		}

		$hash = hash_equals($_SESSION[self::CLE_SESSION], $jeton) ? true : false;

		self::detruireSession();

		return $hash;
	}

	public static function detruireSession(): void
	{
		self::verifierSession();

		unset($_SESSION[self::CLE_SESSION]);
	}

	private static function creerJeton(?int $taille = null): string
	{
		$brut = hash_pbkdf2(
			'sha512',
			hash('sha512', random_bytes(100)),
			random_bytes(20),
			50
		);

		return ($taille !== null AND $taille > 0) ? substr($brut, 0, $taille) : $brut;
	}

	private static function verifierSession(): void
	{
		if(session_status() !== PHP_SESSION_ACTIVE) {
			throw new \RuntimeException('La session PHP doit être démarrée avant d’utiliser le système CSRF');
		}
	}
}