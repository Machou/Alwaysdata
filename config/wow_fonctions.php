<?php
function estAdmin(): bool
{
	return (!empty($_SESSION['nom_utilisateur']) AND (string) $_SESSION['nom_utilisateur'] === 'Klep') ? true : false;
}

function estConnecte(): bool
{
	return !empty($_SESSION['id_utilisateur']);
}

function tempsRestant(?string $tempsRestant): string
{
	$tempsRestantArray = ['SHORT' => '- de 12 heures', 'MEDIUM' => '12 heures', 'LONG' => '24 heures', 'VERY_LONG' => '48 heures'];

	return !empty($tempsRestant) ? strtr(mb_strtoupper($tempsRestant), $tempsRestantArray) : 'n/a';
}

function serveurExiste(PDO $pdo, int|string $serveur): bool
{
	if(!estConnecte())
		return false;

	if(!empty($serveur))
	{
		try {
			$stmt = $pdo->prepare('SELECT 1 FROM wow_serveurs WHERE id_blizzard = :id_blizzard OR id_connecte = :id_connecte OR slug = :slug LIMIT 1');
			$stmt->execute([
				'id_blizzard' => (int) $serveur,
				'id_connecte' => (int) $serveur,
				'slug' => (string) $serveur
			]);
		} catch (\PDOException $e) { }
	}

	return ($stmt->fetch() !== false);
}

function serveurLangue(string $langue): string
{
	if($langue === 'de')		return 'Allemand';
	elseif($langue === 'gb')	return 'Anglais';
	elseif($langue === 'es')	return 'Espagnol';
	elseif($langue === 'fr')	return 'Français';
	elseif($langue === 'it')	return 'Italien';
	elseif($langue === 'pt')	return 'Portguais';
	elseif($langue === 'ru')	return 'Russe';
	else						return 'inconnu';
}

function mascotteNom(PDO $pdo, int $idMascotte)
{
	if(!estConnecte())
		return false;

	try {
		$stmt = $pdo->prepare('SELECT nom_mascotte FROM wow_mascottes WHERE id = :id LIMIT 1');
		$stmt->execute(['id' => (int) $idMascotte]);
		$res = $stmt->fetch();
	} catch (\PDOException $e) { }

	return !empty($res['nom_mascotte']) ? $res['nom_mascotte'] : 'nom inconnu';
}

function mascotteMaj(PDO $pdo, BlizzardApi\Wow\Wow $apiClient, int $idMascotte): bool
{
	try {
		$stmtDate = $pdo->prepare('SELECT 1 FROM wow_mascottes WHERE id = :id_mascotte AND (description_mascotte IS NULL OR description_mascotte = "" OR date_maj IS NULL OR date_maj = "" OR date_maj <= DATE_SUB(NOW(), INTERVAL 365 DAY)) LIMIT 1');
		$stmtDate->execute(['id_mascotte' => (int) $idMascotte]);
		$resMascotte = $stmtDate->fetch();
	} catch (\PDOException $e) { }


	// if((!empty($resMascotte) OR isset($_GET['maj'])) AND !in_array($idMascotte, [3590, 3598, 3599, 4792], true))
	if((!empty($resMascotte) OR isset($_GET['maj'])) AND !in_array($idMascotte, [4792], true))
	{
		$mascotteDetailsBnet = $apiClient->pet()->get($idMascotte);

		if(!empty($mascotteDetailsBnet->code))
		{
			if($mascotteDetailsBnet->code === 404)
				setFlash('danger', 'La mascotte n’a pas encore été ajoutée à l’API Battle.net');

			return false;
		}

		$description_mascotte = $mascotteDetailsBnet->description->fr_FR ?? null;

		if(!empty($description_mascotte))
		{
			try {
				$stmt = $pdo->prepare('UPDATE wow_mascottes SET
					date_maj = NOW(),
					nom_mascotte = :nom_mascotte,
					nom_mascotte_en = :nom_mascotte_en,
					nom_mascotte_slug = :nom_mascotte_slug,
					nom_mascotte_slug_en = :nom_mascotte_slug_en,
					battle_pet_type_id = :battle_pet_type_id,
					battle_pet_type_type = :battle_pet_type_type,
					battle_pet_type_name = :battle_pet_type_name,
					battle_pet_type_name_en = :battle_pet_type_name_en,
					description_mascotte = :description_mascotte,
					description_mascotte_en = :description_mascotte_en,
					is_capturable = :is_capturable,
					is_tradable = :is_tradable,
					is_battlepet = :is_battlepet,
					is_alliance_only = :is_alliance_only,
					is_horde_only = :is_horde_only,
					abilities_1_name = :abilities_1_name,
					abilities_1_name_en = :abilities_1_name_en,
					abilities_1_id = :abilities_1_id,
					abilities_1_slot = :abilities_1_slot,
					abilities_1_required_level = :abilities_1_required_level,
					abilities_1_media = :abilities_1_media,
					abilities_1_file_data_id = :abilities_1_file_data_id,
					abilities_2_name = :abilities_2_name,
					abilities_2_name_en = :abilities_2_name_en,
					abilities_2_id = :abilities_2_id,
					abilities_2_slot = :abilities_2_slot,
					abilities_2_required_level = :abilities_2_required_level,
					abilities_2_media = :abilities_2_media,
					abilities_2_file_data_id = :abilities_2_file_data_id,
					abilities_3_name = :abilities_3_name,
					abilities_3_name_en = :abilities_3_name_en,
					abilities_3_id = :abilities_3_id,
					abilities_3_slot = :abilities_3_slot,
					abilities_3_required_level = :abilities_3_required_level,
					abilities_3_media = :abilities_3_media,
					abilities_3_file_data_id = :abilities_3_file_data_id,
					abilities_4_name = :abilities_4_name,
					abilities_4_name_en = :abilities_4_name_en,
					abilities_4_id = :abilities_4_id,
					abilities_4_slot = :abilities_4_slot,
					abilities_4_required_level = :abilities_4_required_level,
					abilities_4_media = :abilities_4_media,
					abilities_4_file_data_id = :abilities_4_file_data_id,
					abilities_5_name = :abilities_5_name,
					abilities_5_name_en = :abilities_5_name_en,
					abilities_5_id = :abilities_5_id,
					abilities_5_slot = :abilities_5_slot,
					abilities_5_required_level = :abilities_5_required_level,
					abilities_5_media = :abilities_5_media,
					abilities_5_file_data_id = :abilities_5_file_data_id,
					abilities_6_name = :abilities_6_name,
					abilities_6_name_en = :abilities_6_name_en,
					abilities_6_id = :abilities_6_id,
					abilities_6_slot = :abilities_6_slot,
					abilities_6_required_level = :abilities_6_required_level,
					abilities_6_media = :abilities_6_media,
					abilities_6_file_data_id = :abilities_6_file_data_id,
					source_type = :source_type,
					source_type_name = :source_type_name,
					source_type_name_en = :source_type_name_en,
					icon = :icon,
					creature_id = :creature_id,
					is_random_creature_display = :is_random_creature_display,
					media_id = :media_id
				WHERE id = :id');

				$id = $idMascotte;

				$nom_mascotte = $mascotteDetailsBnet->name->fr_FR ?? null;
				$nom_mascotte_en = $mascotteDetailsBnet->name->en_GB ?? null;
				$nom_mascotte_slug = isset($mascotteDetailsBnet->name->fr_FR) ? slug($mascotteDetailsBnet->name->fr_FR) : null;
				$nom_mascotte_slug_en = isset($mascotteDetailsBnet->name->en_GB) ? slug($mascotteDetailsBnet->name->en_GB) : null;
				$battle_pet_type_id = $mascotteDetailsBnet->battle_pet_type->id ?? null;
				$battle_pet_type_type = $mascotteDetailsBnet->battle_pet_type->type ?? null;
				$battle_pet_type_name = $mascotteDetailsBnet->battle_pet_type->name->fr_FR ?? null;
				$battle_pet_type_name_en = $mascotteDetailsBnet->battle_pet_type->name->en_GB ?? null;
				$description_mascotte_en = $mascotteDetailsBnet->description->en_GB ?? null;
				$is_capturable = isset($mascotteDetailsBnet->is_capturable) ? (int) $mascotteDetailsBnet->is_capturable : null;
				$is_tradable = isset($mascotteDetailsBnet->is_tradable) ? (int) $mascotteDetailsBnet->is_tradable : null;
				$is_battlepet = isset($mascotteDetailsBnet->is_battlepet) ? (int) $mascotteDetailsBnet->is_battlepet : null;
				$is_alliance_only = isset($mascotteDetailsBnet->is_alliance_only) ? (int) $mascotteDetailsBnet->is_alliance_only : null;
				$is_horde_only = isset($mascotteDetailsBnet->is_horde_only) ? (int) $mascotteDetailsBnet->is_horde_only : null;

				$abilities = [
					'name' => null, 'name_en' => null, 'id' => null,
					'slot' => null, 'required_level' => null
				];

				$abilitiesList = [];
				for($i = 0; $i < 6; $i++) {
					$abilitiesList[$i] = $abilities;
				}

				if(isset($mascotteDetailsBnet->abilities) AND is_array($mascotteDetailsBnet->abilities))
				{
					foreach($mascotteDetailsBnet->abilities as $index => $ability)
					{
						if($index < 6)
						{
							$abilitiesList[$index] = [
								'name' => $ability->ability->name->fr_FR ?? null,
								'name_en' => $ability->ability->name->en_GB ?? null,
								'id' => $ability->ability->id ?? null,
								'slot' => $ability->slot ?? null,
								'required_level' => $ability->required_level ?? null
							];
						}
					}
				}

				$abilities_1_name = $abilitiesList[0]['name'] ?? null;
				$abilities_1_name_en = $abilitiesList[0]['name_en'] ?? null;
				$abilities_1_id = $abilitiesList[0]['id'] ?? null;
				$abilities_1_slot = $abilitiesList[0]['slot'] ?? null;
				$abilities_1_required_level = $abilitiesList[0]['required_level'] ?? null;

				$mascotteAbilityMediaBnet = !empty($abilities_1_id) ? $apiClient->pet()->abilityMedia($abilities_1_id) : null;
				$abilities_1_media = $mascotteAbilityMediaBnet->assets[0]->value ?? null;
				$abilities_1_file_data_id = $mascotteAbilityMediaBnet->assets[0]->file_data_id ?? null;

				$abilities_2_name = $abilitiesList[1]['name'] ?? null;
				$abilities_2_name_en = $abilitiesList[1]['name_en'] ?? null;
				$abilities_2_id = $abilitiesList[1]['id'] ?? null;
				$abilities_2_slot = $abilitiesList[1]['slot'] ?? null;
				$abilities_2_required_level = $abilitiesList[1]['required_level'] ?? null;

				$mascotteAbilityMediaBnet = !empty($abilities_2_id) ? $apiClient->pet()->abilityMedia($abilities_2_id) : null;
				$abilities_2_media = $mascotteAbilityMediaBnet->assets[0]->value ?? null;
				$abilities_2_file_data_id = $mascotteAbilityMediaBnet->assets[0]->file_data_id ?? null;

				$abilities_3_name = $abilitiesList[2]['name'] ?? null;
				$abilities_3_name_en = $abilitiesList[2]['name_en'] ?? null;
				$abilities_3_id = $abilitiesList[2]['id'] ?? null;
				$abilities_3_slot = $abilitiesList[2]['slot'] ?? null;
				$abilities_3_required_level = $abilitiesList[2]['required_level'] ?? null;

				$mascotteAbilityMediaBnet = !empty($abilities_3_id) ? $apiClient->pet()->abilityMedia($abilities_3_id) : null;
				$abilities_3_media = $mascotteAbilityMediaBnet->assets[0]->value ?? null;
				$abilities_3_file_data_id = $mascotteAbilityMediaBnet->assets[0]->file_data_id ?? null;

				$abilities_4_name = $abilitiesList[3]['name'] ?? null;
				$abilities_4_name_en = $abilitiesList[3]['name_en'] ?? null;
				$abilities_4_id = $abilitiesList[3]['id'] ?? null;
				$abilities_4_slot = $abilitiesList[3]['slot'] ?? null;
				$abilities_4_required_level = $abilitiesList[3]['required_level'] ?? null;

				$mascotteAbilityMediaBnet = !empty($abilities_4_id) ? $apiClient->pet()->abilityMedia($abilities_4_id) : null;
				$abilities_4_media = $mascotteAbilityMediaBnet->assets[0]->value ?? null;
				$abilities_4_file_data_id = $mascotteAbilityMediaBnet->assets[0]->file_data_id ?? null;

				$abilities_5_name = $abilitiesList[4]['name'] ?? null;
				$abilities_5_name_en = $abilitiesList[4]['name_en'] ?? null;
				$abilities_5_id = $abilitiesList[4]['id'] ?? null;
				$abilities_5_slot = $abilitiesList[4]['slot'] ?? null;
				$abilities_5_required_level = $abilitiesList[4]['required_level'] ?? null;

				$mascotteAbilityMediaBnet = !empty($abilities_5_id) ? $apiClient->pet()->abilityMedia($abilities_5_id) : null;
				$abilities_5_media = $mascotteAbilityMediaBnet->assets[0]->value ?? null;
				$abilities_5_file_data_id = $mascotteAbilityMediaBnet->assets[0]->file_data_id ?? null;

				$abilities_6_name = $abilitiesList[5]['name'] ?? null;
				$abilities_6_name_en = $abilitiesList[5]['name_en'] ?? null;
				$abilities_6_id = $abilitiesList[5]['id'] ?? null;
				$abilities_6_slot = $abilitiesList[5]['slot'] ?? null;
				$abilities_6_required_level = $abilitiesList[5]['required_level'] ?? null;

				$mascotteAbilityMediaBnet = !empty($abilities_6_id) ? $apiClient->pet()->abilityMedia($abilities_6_id) : null;
				$abilities_6_media = $mascotteAbilityMediaBnet->assets[0]->value ?? null;
				$abilities_6_file_data_id = $mascotteAbilityMediaBnet->assets[0]->file_data_id ?? null;

				$source_type = $mascotteDetailsBnet->source->type ?? null;
				$source_type_name = $mascotteDetailsBnet->source->name->fr_FR ?? null;
				$source_type_name_en = $mascotteDetailsBnet->source->name->en_GB ?? null;
				$icon = $mascotteDetailsBnet->icon ?? null;
				$creature_id = $mascotteDetailsBnet->creature->id ?? null;
				$is_random_creature_display = isset($mascotteDetailsBnet->is_random_creature_display) ? (int) $mascotteDetailsBnet->is_random_creature_display : null;
				$media_id = $mascotteDetailsBnet->media->id ?? null;

				$stmt->execute([
					':id' => (int) $id,
					':nom_mascotte' => (string) $nom_mascotte,
					':nom_mascotte_en'=> (string) $nom_mascotte_en,
					':nom_mascotte_slug'=> (string) $nom_mascotte_slug,
					':nom_mascotte_slug_en'=> (string) $nom_mascotte_slug_en,
					':battle_pet_type_id' => (int) $battle_pet_type_id,
					':battle_pet_type_type'=> (string) $battle_pet_type_type,
					':battle_pet_type_name'=> (string) $battle_pet_type_name,
					':battle_pet_type_name_en'=> (string) $battle_pet_type_name_en,
					':description_mascotte'=> (string) $description_mascotte,
					':description_mascotte_en'=> (string) $description_mascotte_en,
					':is_capturable' => (int) $is_capturable,
					':is_tradable' => (int) $is_tradable,
					':is_battlepet' => (int) $is_battlepet,
					':is_alliance_only' => (int) $is_alliance_only,
					':is_horde_only' => (int) $is_horde_only,

					':abilities_1_name' => (string) $abilities_1_name,
					':abilities_1_name_en' => (string) $abilities_1_name_en,
					':abilities_1_id' => (int) $abilities_1_id,
					':abilities_1_slot' => (int) $abilities_1_slot,
					':abilities_1_required_level' => (int) $abilities_1_required_level,
					':abilities_1_media' => (string) $abilities_1_media,
					':abilities_1_file_data_id' => (int) $abilities_1_file_data_id,

					':abilities_2_name' => (string) $abilities_2_name,
					':abilities_2_name_en' => (string) $abilities_2_name_en,
					':abilities_2_id' => (int) $abilities_2_id,
					':abilities_2_slot' => (int) $abilities_2_slot,
					':abilities_2_required_level' => (int) $abilities_2_required_level,
					':abilities_2_media' => (string) $abilities_2_media,
					':abilities_2_file_data_id' => (int) $abilities_2_file_data_id,

					':abilities_3_name' => (string) $abilities_3_name,
					':abilities_3_name_en' => (string) $abilities_3_name_en,
					':abilities_3_id' => (int) $abilities_3_id,
					':abilities_3_slot' => (int) $abilities_3_slot,
					':abilities_3_required_level' => (int) $abilities_3_required_level,
					':abilities_3_media' => (string) $abilities_3_media,
					':abilities_3_file_data_id' => (int) $abilities_3_file_data_id,

					':abilities_4_name' => (string) $abilities_4_name,
					':abilities_4_name_en' => (string) $abilities_4_name_en,
					':abilities_4_id' => (int) $abilities_4_id,
					':abilities_4_slot' => (int) $abilities_4_slot,
					':abilities_4_required_level' => (int) $abilities_4_required_level,
					':abilities_4_media' => (string) $abilities_4_media,
					':abilities_4_file_data_id' => (int) $abilities_4_file_data_id,

					':abilities_5_name' => (string) $abilities_5_name,
					':abilities_5_name_en' => (string) $abilities_5_name_en,
					':abilities_5_id' => (int) $abilities_5_id,
					':abilities_5_slot' => (int) $abilities_5_slot,
					':abilities_5_required_level' => (int) $abilities_5_required_level,
					':abilities_5_media' => (string) $abilities_5_media,
					':abilities_5_file_data_id' => (int) $abilities_5_file_data_id,

					':abilities_6_name' => (string) $abilities_6_name,
					':abilities_6_name_en' => (string) $abilities_6_name_en,
					':abilities_6_id' => (int) $abilities_6_id,
					':abilities_6_slot' => (int) $abilities_6_slot,
					':abilities_6_required_level' => (int) $abilities_6_required_level,
					':abilities_6_media' => (string) $abilities_6_media,
					':abilities_6_file_data_id' => (int) $abilities_6_file_data_id,

					':source_type' => (string) $source_type,
					':source_type_name' => (string) $source_type_name,
					':source_type_name_en' => (string) $source_type_name_en,
					':icon' => (string) $icon,
					':creature_id' => (int) $creature_id,
					':is_random_creature_display' => (int) $is_random_creature_display,
					':media_id' => (int) $media_id
				]);

				$stmt->execute();

			} catch (\PDOException $e) { }

			return true;
		}

		setFlash('danger', 'La description de la mascotte est inconnue');

		return false;
	}

	return false;
}

function mascotteExiste(PDO $pdo, ?string $idMascotte = null, ?string $nomMascotte = null): bool
{
	try {
		if($idMascotte !== null)
		{
			$stmt = $pdo->prepare('SELECT 1 FROM wow_mascottes WHERE id = :id LIMIT 1');
			$stmt->execute([':id' => (int) trim($idMascotte)]);
		}

		elseif($nomMascotte !== null)
		{
			$stmt = $pdo->prepare('SELECT 1 FROM wow_mascottes WHERE nom_mascotte = :nom OR nom_mascotte_en = :nom_en LIMIT 1');
			$stmt->execute([
				':nom' => (string) $nomMascotte,
				':nom_en' => (string) $nomMascotte,
			]);
		}

		return ($stmt->fetch() !== false);
	} catch (\PDOException $e) { }

	return false;
}

function mascotteUtilisateur(PDO $pdo, ?string $nomMascotte): bool
{
	if(!estConnecte())
		return false;

	if($nomMascotte === null OR $nomMascotte === '')
		return false;

	try {
		$stmt = $pdo->prepare('SELECT 1 FROM wow_mascottes_u WHERE nom_mascotte = :nom_mascotte AND id_utilisateur = :id_utilisateur');
		$stmt->execute([
			':nom_mascotte' => (string) $nomMascotte,
			'id_utilisateur' => (int) $_SESSION['id_utilisateur'],
		]);

		return $stmt->fetchColumn() !== false;
	} catch (\PDOException $e) { }

	return false;
}

function jetonMaj(PDO $pdo)
{
	try {
		$stmtDate = $pdo->prepare('SELECT date_jour FROM wow_prix_jeton ORDER BY date_jour DESC LIMIT 1');
		$stmtDate->execute();

		$resDate = $stmtDate->fetch();
	} catch (\PDOException $e) { }

	if(!empty($resDate['date_jour']))
	{
		$derniereMaj = new DateTime($resDate['date_jour']);
		$hier = new DateTime('yesterday');

		if($derniereMaj->format('Y-m-d') <= $hier->format('Y-m-d'))
		{
			$stmtMaj = $pdo->prepare('TRUNCATE TABLE wow_prix_jeton');
			$stmtMaj->execute();

			$donneesJson = json_decode(get('https://data.wowtoken.app/v2/relative/retail/eu/all.json'), 1);

			if(!empty($donneesJson))
			{
				foreach($donneesJson as $c => $v)
				{
					try {
						$stmt = $pdo->prepare('INSERT INTO wow_prix_jeton (prix, date_jour, timestamp) VALUES (:prix, :date_jour, :timestamp) ON DUPLICATE KEY UPDATE prix = VALUES(prix), timestamp = VALUES(timestamp)');

						$stmt->execute([
							':prix' => $v[1],
							':date_jour' => (new DateTimeImmutable($v[0]))->format('Y-m-d'),
							':timestamp' => strtotime($v[0]),
						]);
					} catch (\PDOException $e) { }
				}

				return true;
			}
		}
	}

	return false;
}

function trierIcone(string $colonne, string $trier, string $trierPar): string
{
	if($colonne !== $trier)
		return '';

	return $trierPar === 'asc' ? '<span>↑</span>' : '<span>↓</span>';
}

function personnageInfosSql(PDO $pdo, string $idServeur, string $nom): ?array
{
	if(!estConnecte())
		return [];

	if(!empty($idServeur) AND !empty($nom))
	{
		try {
			$stmt = $pdo->prepare('SELECT * FROM wow_personnages WHERE id_serveur = :id_serveur AND nom = :nom LIMIT 1');
			$stmt->execute([
				'id_serveur' => (int) $idServeur,
				'nom' => (string) $nom
			]);

			return $stmt->fetch() ?: null;
		} catch (\PDOException $e) { }
	}

	return [];
}

function serveurInfos(PDO $pdo, string|int $serveur)
{
	if(!estConnecte())
		return false;


	try {
		$stmt = $pdo->prepare('SELECT * FROM wow_serveurs WHERE id_blizzard = :id_blizzard OR id_connecte = :id_connecte OR nom = :nom OR nom_ru = :nom_ru LIMIT 1');
		$stmt->execute([
			'id_blizzard' => (int) $serveur,
			'id_connecte' => (int) $serveur,
			'nom' => (string) $serveur,
			'nom_ru' => (string) $serveur
		]);
		$res = $stmt->fetch();
	} catch (\PDOException $e) { }

	if(!empty($res['id_blizzard']) AND !empty($res['nom']) AND !empty($res['locale']) AND !empty($res['slug']))
	{
		return [
			'id_blizzard' => (int) $res['id_blizzard'],
			'id_connecte' => (int) $res['id_connecte'],
			'nom' => (string) $res['nom'],
			'nom_ru' => (string) $res['nom_ru'],
			'locale' => (string) $res['locale'],
			'slug' => (string) $res['slug'],
		];
	}

	return false;
}

function personnageInfos($pdo, $apiClient, string $serveur, string $nom)
{
	if(!estConnecte())
		return false;

	$nom = mb_strtolower($nom);

	$estValide = $apiClient->character()->profileStatus($serveur, $nom, ['locale' => 'fr_FR']);

	if(!empty($estValide->is_valid) AND $estValide->is_valid)
	{
		$personnage = $apiClient->character()->get($serveur, $nom, ['locale' => 'fr_FR']);
		$personnageMedia = $apiClient->character()->media($serveur, $nom, ['locale' => 'fr_FR']);
		$personnageReputations = $apiClient->character()->reputations($serveur, $nom, ['locale' => 'fr_FR']);
		$personnageQuetes = $apiClient->character()->completedQuests($serveur, $nom, ['locale' => 'fr_FR']);
		$personnageVictoiresHonorables = $apiClient->character()->pvpSummary($serveur, $nom, ['locale' => 'fr_FR']);

		$idPersonnage		= !empty($personnage->id) ? secu($personnage->id) : null;
		$nom				= !empty($personnage->name) ? secuChars($personnage->name) : null;
		$genreType			= !empty($personnage->gender->type) ? ($personnage->gender->type === 'FEMALE' ? 1 : 0) : null;
		$genreNom			= !empty($personnage->gender->name) ? secuChars($personnage->gender->name) : null;
		$factionType		= !empty($personnage->faction->type) ? secuChars($personnage->faction->type) : null;
		$factionNom			= !empty($personnage->faction->name) ? secuChars($personnage->faction->name) : null;
		$raceNom			= !empty($personnage->race->name) ? secuChars($personnage->race->name) : null;
		$raceId				= !empty($personnage->race->id) ? secu($personnage->race->id) : null;
		$classeNom			= !empty($personnage->character_class->name) ? secuChars($personnage->character_class->name) : null;
		$classeId			= !empty($personnage->character_class->id) ? secuChars($personnage->character_class->id) : null;
		$specialisationNom	= !empty($personnage->active_spec->name) ? secuChars($personnage->active_spec->name) : null;
		$specialisationId	= !empty($personnage->active_spec->id) ? secuChars($personnage->active_spec->id) : null;
		$serveur			= !empty($personnage->realm->name) ? secuChars($personnage->realm->name) : null;
		$serveurId			= !empty($personnage->realm->id) ? secuChars($personnage->realm->id) : null;
		$serveurSlug		= !empty($personnage->realm->slug) ? secuChars($personnage->realm->slug) : null;
		$guildeNom			= !empty($personnage->guild->name) ? secuChars($personnage->guild->name) : null;
		$guildeId			= !empty($personnage->guild->id) ? secu($personnage->guild->id) : null;
		$guildeServeurNom	= !empty($personnage->guild->realm->name) ? secuChars($personnage->guild->realm->name) : null;
		$guildeServeurId	= !empty($personnage->guild->realm->id) ? secu($personnage->guild->realm->id) : null;
		$guildeServeurSlug	= !empty($personnage->guild->realm->slug) ? secuChars($personnage->guild->realm->slug) : null;
		$guildeFactionType	= !empty($personnage->guild->faction->type) ? secuChars($personnage->guild->faction->type) : null;
		$guildeFactionNom	= !empty($personnage->guild->faction->name) ? secuChars($personnage->guild->faction->name) : null;
		$niveau				= !empty($personnage->level) ? secu($personnage->level) : null;
		$experience			= !empty($personnage->experience) ? secu($personnage->experience) : null;
		$hautfaitPoints		= !empty($personnage->achievement_points) ? secu($personnage->achievement_points) : null;
		$lastLoginTimestamp	= !empty($personnage->last_login_timestamp) ? secu($personnage->last_login_timestamp) : null;
		$lastLoginDate		= !empty($lastLoginTimestamp) ? dateFormat($lastLoginTimestamp / 1000, 'yyyy-MM-dd HH:mm:ss') : null;
		$ilvlMoyen			= !empty($personnage->average_item_level) ? secu($personnage->average_item_level) : null;
		$ilvlEquipe			= !empty($personnage->equipped_item_level) ? secu($personnage->equipped_item_level) : null;

		$compteurReputationsExaltes = 0;
		if(!empty($personnageReputations->reputations) AND is_array($personnageReputations->reputations))
		{
			foreach($personnageReputations->reputations as $a)
			{
				if(isset($a->standing->name) AND $a->standing->name === 'Exalté')
					$compteurReputationsExaltes++;
			}
		}

		$reputations			= !empty($personnageReputations->reputations) ? (int) count($personnageReputations->reputations) : 0;
		$reputationsExaltes 	= $compteurReputationsExaltes > 0 ? secu($compteurReputationsExaltes) : 0;
		$quetesTerminees		= !empty($personnageQuetes->quests) ? (int) count($personnageQuetes->quests) : 0;
		$niveauHonneur			= !empty($personnageVictoiresHonorables->honor_level) ? secu($personnageVictoiresHonorables->honor_level) : 0 ;
		$victoiresHonorables	= !empty($personnageVictoiresHonorables->honorable_kills) ? secu($personnageVictoiresHonorables->honorable_kills) : 0;
		$nomRecherche			= !empty($personnage->name_search) ? secuChars($personnage->name_search) : null;

		$avatar					= !empty($personnageMedia->assets[0]->value) ? secuChars($personnageMedia->assets[0]->value) : null;
		$avatarInset			= !empty($personnageMedia->assets[1]->value) ? secuChars($personnageMedia->assets[1]->value) : null;
		$avatarRaw				= !empty($personnageMedia->assets[2]->value) ? secuChars($personnageMedia->assets[2]->value) : null;

		try {
			$stmt = $pdo->prepare('SELECT objet_validation_1, objet_validation_2 FROM wow_personnages WHERE serveur_slug = :serveur_slug AND nom = :nom LIMIT 1');
			$stmt->execute([
				'serveur_slug' => (string) $serveurSlug,
				'nom' => (string) $nom
			]);
			$resObjets = $stmt->fetch();
		} catch (\PDOException $e) { }

		return [
			'id_utilisateur' => $_SESSION['id_utilisateur'],
			'id_blizzard' => $idPersonnage,
			'nom' => $nom,
			'genre_type' => $genreType,
			'genre_nom' => $genreNom,
			'faction_type' => $factionType,
			'faction_nom' => $factionNom,
			'race_nom' => $raceNom,
			'race_id' => $raceId,
			'classe_nom' => $classeNom,
			'classe_id' => $classeId,
			'specialisation_nom' => $specialisationNom,
			'specialisation_id' => $specialisationId,
			'serveur_nom' => $serveur,
			'id_serveur' => $serveurId,
			'serveur_slug' => $serveurSlug,
			'guilde_nom' => $guildeNom,
			'guilde_id' => $guildeId,
			'guilde_serveur_nom' => $guildeServeurNom,
			'guilde_serveur_id' => $guildeServeurId,
			'guilde_serveur_slug' => $guildeServeurSlug,
			'guilde_faction_type' => $guildeFactionType,
			'guilde_faction_nom' => $guildeFactionNom,
			'niveau' => $niveau,
			'experience' => $experience,
			'hauts_faits' => $hautfaitPoints,
			'derniere_connexion_wow' => $lastLoginTimestamp,
			'derniere_connexion_wow_date' => $lastLoginDate,
			'ilvl_moyen' => $ilvlMoyen,
			'ilvl_equipe' => $ilvlEquipe,
			'reputations' => $reputations,
			'reputations_exaltes' => $reputationsExaltes,
			'quetes_terminees' => $quetesTerminees,
			'niveau_honneur' => $niveauHonneur,
			'victoires_honorables' => $victoiresHonorables,
			'nom_recherche' => $nomRecherche,
			'avatar' => $avatar,
			'avatar_inset' => $avatarInset,
			'avatar_raw' => $avatarRaw,
			'objet_validation_1' => !empty($resObjets['objet_validation_1']) ? $resObjets['objet_validation_1'] : null,
			'objet_validation_2' => !empty($resObjets['objet_validation_2']) ? $resObjets['objet_validation_2'] : null,
		];
	}

	return false;
}

function champsSqlPersonnage()
{
	return [
		'id', 'id_utilisateur', 'id_blizzard', 'nom', 'genre_type', 'genre_nom', 'faction_type', 'faction_nom', 'race_nom', 'race_id', 'classe_nom', 'classe_id', 'specialisation_nom',
		'specialisation_id', 'serveur_nom', 'id_serveur', 'serveur_slug', 'guilde_nom', 'guilde_id', 'guilde_serveur_nom', 'guilde_serveur_id', 'guilde_serveur_slug', 'guilde_faction_type',
		'guilde_faction_nom', 'niveau', 'experience', 'hauts_faits', 'derniere_connexion_wow', 'ilvl_moyen', 'ilvl_equipe', 'reputations', 'reputations_exaltes', 'quetes_terminees',
		'niveau_honneur', 'victoires_honorables', 'nom_recherche', 'avatar', 'avatar_inset', 'avatar_raw', 'objet_validation_1', 'objet_validation_2', 'est_confirmer'
	];
}

function objetsPersonnage()
{
	return [
		'HEAD' => 'Tête', 'NECK' => 'Cou', 'SHOULDER' => 'Épaules', 'SHIRT' => 'Chemise', 'CHEST' => 'Torse', 'WAIST' => 'Taille', 'LEGS' => 'Jambes', 'FEET' => 'Pieds',
		'WRIST' => 'Poignets', 'HANDS' => 'Mains', 'FINGER_1' => '1er anneau', 'FINGER_2' => '2e anneau', 'TRINKET_1' => '1er bijou', 'TRINKET_2' => '2e bijou', 'BACK' => 'Dos'
	];
}

function personnageAjouter(PDO $pdo, array $donnees): bool
{
	$champs = champsSqlPersonnage();

	$champsObjets = array_rand(objetsPersonnage(), 2);

	$donnees = array_merge($donnees, [
		'objet_validation_1' => $champsObjets[0],
		'objet_validation_2' => $champsObjets[1],
		'est_confirmer' => '0'
	]);

	$params = [];
	foreach($donnees as $champ)
	{
		if(!array_key_exists($champ, $donnees)) {
			throw new InvalidArgumentException('Champ manquant pour la requête : '.$champ);
		}

		$params[$champ] = $donnees[$champ];
	}

	p($params);
	exit;

	try {
		$stmt = $pdo->prepare('INSERT INTO wow_personnages (
			id_utilisateur, id_blizzard, nom, genre_type, genre_nom, faction_type, faction_nom, race_nom, race_id, classe_nom, classe_id, specialisation_nom, specialisation_id,
			serveur_nom, id_serveur, serveur_slug, guilde_nom, guilde_id, guilde_serveur_nom, guilde_serveur_id, guilde_serveur_slug, guilde_faction_type, guilde_faction_nom, niveau,
			experience, hauts_faits, derniere_connexion_wow, ilvl_moyen, ilvl_equipe, reputations, reputations_exaltes, quetes_terminees, niveau_honneur, victoires_honorables,
			nom_recherche, avatar, avatar_inset, avatar_raw, objet_validation_1, objet_validation_2, est_confirmer
		)

		VALUES (
			:id_utilisateur, :id_blizzard, :nom, :genre_type, :genre_nom, :faction_type, :faction_nom, :race_nom, :race_id, :classe_nom, :classe_id, :specialisation_nom,
			:specialisation_id, :serveur_nom, :id_serveur, :serveur_slug, :guilde_nom, :guilde_id, :guilde_serveur_nom, :guilde_serveur_id, :guilde_serveur_slug, :guilde_faction_type,
			:guilde_faction_nom, :niveau, :experience, :hauts_faits, :derniere_connexion_wow, :ilvl_moyen, :ilvl_equipe, :reputations, :reputations_exaltes, :quetes_terminees, :niveau_honneur,
			:victoires_honorables, :nom_recherche, :avatar, :avatar_inset, :avatar_raw, :objet_validation_1, :objet_validation_2, :est_confirmer
		)');

		return $stmt->execute($params);
	} catch (\PDOException $e) {
		p($e);
	}

	return false;
}

function personnageMaj(PDO $pdo, array $donnees): bool
{
	$params = [];
	foreach(champsSqlPersonnage() as $champ)
	{
		if(!array_key_exists($champ, $donnees)) {
			throw new InvalidArgumentException('Champ manquant pour la requête : '.$champ);
		}

		$params[$champ] = $donnees[$champ];
	}

	try {
		$stmt = $pdo->prepare('UPDATE wow_personnages SET
			id_blizzard = :id_blizzard, nom = :nom, genre_type = :genre_type, genre_nom = :genre_nom, faction_type = :faction_type, faction_nom = :faction_nom, race_nom = :race_nom, race_id = :race_id,
			classe_nom = :classe_nom, classe_id = :classe_id, specialisation_nom = :specialisation_nom, specialisation_id = :specialisation_id, serveur_nom = :serveur_nom, id_serveur = :id_serveur,
			serveur_slug = :serveur_slug, guilde_nom = :guilde_nom, guilde_id = :guilde_id, guilde_serveur_nom = :guilde_serveur_nom, guilde_serveur_id = :guilde_serveur_id,
			guilde_serveur_slug = :guilde_serveur_slug, guilde_faction_type = :guilde_faction_type, guilde_faction_nom = :guilde_faction_nom, niveau = :niveau, experience = :experience,
			hauts_faits = :hauts_faits, derniere_connexion_wow = :derniere_connexion_wow, ilvl_moyen = :ilvl_moyen, ilvl_equipe = :ilvl_equipe, reputations = :reputations,
			reputations_exaltes = :reputations_exaltes, quetes_terminees = :quetes_terminees, niveau_honneur = :niveau_honneur, victoires_honorables = :victoires_honorables, nom_recherche = :nom_recherche,
			avatar = :avatar, avatar_inset = :avatar_inset, avatar_raw = :avatar_raw, objet_validation_1 = :objet_validation_1, objet_validation_2 = :objet_validation_2, est_confirmer = :est_confirmer
			WHERE id = :id AND id_utilisateur = :id_utilisateur
		');

		return $stmt->execute($params);
	} catch (\PDOException $e) {
		p($e);
	}

	return false;
}

function afficherServeursHtml(PDO $pdo, ?string $idServeurFavori): string
{
	try {
		$stmt = $pdo->prepare('SELECT * FROM wow_serveurs ORDER BY langue, nom');
		$stmt->execute();
		$rServeurs = $stmt->fetchAll();
	} catch (\PDOException $e) { }

	$groupes = [];
	foreach($rServeurs as $rServeur)
	{
		$langue = $rServeur['langue'];

		if(!isset($groupes[$langue]))
			$groupes[$langue] = [];

		$groupes[$langue][] = [
			'nom' => $rServeur['nom'],
			'nom_ru' => $rServeur['nom_ru'],
			'id_blizzard' => $rServeur['id_blizzard'],
			'locale' => $rServeur['locale'],
		];
	}

	$html = '';
	foreach($groupes as $langue => $serveurs)
	{
		$count = count($serveurs);
		$html .= "\n".'<optgroup label="'.$langue.' ('.(($langue === 'Anglais' ? ($count - 1) : $count)).')'.'">'."\n\t";

		foreach($serveurs as $srv)
		{
			$idBlizzard = secu($srv['id_blizzard']);
			$nom = secuChars($srv['nom']);
			$nomSlug = slug($nom);
			$nomRu = !empty($srv['nom_ru']) ? secuChars($srv['nom_ru']) : null;
			$locale = !empty($srv['locale']) ? secuChars($srv['locale']) : null;
			$drapeau = ['deDE' => '🇩🇪', 'enGB' => '🇺🇸', 'esES' => '🇺🇸', 'frFR' => '🇫🇷', 'itIT' => '🇺🇸', 'ptPT' => '🇵🇹', 'ruRU' => '🇷🇺'];
			$option = (!empty($nomRu) AND $locale === 'ruRU') ? $nomRu.' ('.$nom.')' : $nom;

			$html .= '<option value="'.$idBlizzard.'"'.($idServeurFavori == $idBlizzard ? ' selected' : null).'>'.$drapeau[$srv['locale']].' '.$option.'</option>';
		}

		$html .= "\n".'</optgroup>'."\n";
	}

	return '<select name="ajouterServeur" id="serveursChoice" data-placeholder="Sélectionner un serveur">'.$html.'</select>';
}

function objetExiste(PDO $pdo, ?string $nom): bool
{
	if(!estConnecte())
		return false;

	if($nom === null OR $nom === '')
		return false;

	try {
		$stmt = $pdo->prepare('SELECT 1 FROM wow_objets WHERE nom = :nom AND id_utilisateur = :id_utilisateur LIMIT 1');
		$stmt->execute([
			'nom' => (string) $nom,
			'id_utilisateur' => (int) $_SESSION['id_utilisateur'],
		]);

		return $stmt->fetchColumn() !== false;
	} catch (\PDOException $e) { }

	return false;
}

function detailsWowhead(string $objet, ?int $mascotteId = null, ?int $mediaPetSpeciesId = null)
{
	$wowhead = get('https://www.wowhead.com/fr/item='.(!empty($mascotteId) ? $mascotteId : urlencode($objet)).'&xml');
	$xml = simplexml_load_string($wowhead);

	if(!empty($xml) AND empty($xml->error))
	{
		$item = $xml->item;

		if(!empty($item['id']) AND !empty($item->name))
		{
			$resultat = [
				'objetId' => (int) $item['id'],
				'nom' => !empty($item->name) ? (string) $item->name : 'inconnu',
				'niveau' => !empty($item->level) ? (int) $item->level : null,
				'qualite' => !empty($item->quality) ? (string) $item->quality : null,
				'qualiteId' => isset($item->quality['id']) ? (int) $item->quality['id'] : null,
				'classeeType' => !empty($item->class) ? (string) $item->class : null,
				'icone' => !empty($item->icon) ? (string) $item->icon : null,
				'htmlTooltip' => (string) !empty($item->htmlTooltip) ? (string) $item->htmlTooltip : null,
				'lien' => (string) !empty($item->link) ? (string) $item->link : null,
				'media_pet_species_id' => !empty($mediaPetSpeciesId) ? (int) $mediaPetSpeciesId : null,
				'json' => [],
				'jsonEquip' => [],
			];

			if(!empty($item->json[0])) {
				$brut = trim((string)$item->json[0]);
				$json = json_decode('{'.$brut.'}', true);
				if(is_array($json)) {
					$resultat['json'] = $json;
				}
			}

			if(!empty($item->jsonEquip[0])) {
				$brut = trim((string)$item->jsonEquip[0]);
				$jsonEquip = json_decode('{'.$brut.'}', true);
				if(is_array($jsonEquip)) {
					$resultat['jsonEquip'] = $jsonEquip;
				}
			}

			return $resultat;
		}
	}

	return null;
}

function seConnecter(PDO $pdo, int $idUtilisateur, string $nomUtilisateur)
{
	$selecteur = bin2hex(random_bytes(16));
	$jeton = bin2hex(random_bytes(32));
	$jetonHash = hash('sha256', $jeton);

	try {
		$stmt = $pdo->prepare('INSERT INTO wow_jetons (id_utilisateur, selecteur, jeton, expiration, ip, user_agent) VALUES (:id_utilisateur, :selecteur, :jeton, :expiration, :ip, :user_agent)');
		$donnees = [
			'id_utilisateur' => (int) $idUtilisateur,
			'selecteur' => (string) $selecteur,
			'jeton' => (string) $jetonHash,
			'expiration' => (string) date('Y-m-d H:i:s', time() + 86400 * 365),
			'ip' => (string) getRemoteAddr(),
			'user_agent' => (string) getHttpUserAgent()
		];
		$stmt->execute($donnees);
	} catch (\PDOException $e) { }

	$_SESSION['id_utilisateur'] = $idUtilisateur;
	$_SESSION['nom_utilisateur'] = secuChars($nomUtilisateur);
	$_SESSION['connecte'] = true;
	$_SESSION['derniere_visite'] = time();

	setcookie('memoriser', base64_encode($selecteur.':'.$jeton), time() + (86400 * 365), '/', 'hdv.li', true, true);
}

function supprimerJeton(PDO $pdo, ?bool $selecteur = false): void
{
	if($selecteur === true)
	{
		if(!empty($_COOKIE['memoriser']))
		{
			$cookieValeur = base64_decode($_COOKIE['memoriser']);
			$jetonParts = explode(':', $cookieValeur);

			if(count($jetonParts) === 2) {
				list($selecteur, $jeton) = $jetonParts;
			}

			else {
				$selecteur = $jeton = null;

				setcookie('memoriser', '', time() - 3600, '/');
			}
		}
	}
}

function convertirPieces(int $piecesCuivre): array
{
	$argent = floor($piecesCuivre / 100);
	$or = floor($argent / 100);

	$resteCuivre = $piecesCuivre % 100;
	$resteArgent = $argent % 100;

	return [
		'or' => number_format($or),
		'argent' => number_format($resteArgent),
		'cuivre' => number_format($resteCuivre)
	];
}

function mxJetable(string $courriel): bool
{
	if(!empty($courriel))
	{
		if(preg_match('/@/', $courriel))
		{
			// https://github.com/disposable-email-domains/disposable-email-domains
			$contenu = @file_get_contents('https://raw.githubusercontent.com/disposable-email-domains/disposable-email-domains/refs/heads/main/disposable_email_blocklist.conf');

			if($contenu === false) {
				throw new Exception('Impossible de récupérer la liste des domaines autorisés');
			}

			$servicesJetable = explode("\n", $contenu);
			$servicesJetable = array_map('trim', $servicesJetable);
			$servicesJetable = array_filter($servicesJetable);

			$domaine = mb_strtolower(explode('@', trim($courriel))[1]);

			return in_array($domaine, $servicesJetable, true);
		}
	}

	return true;
}

function mailWow(string $courriel, string $sujet, string $message): void
{
	$headers = "From: HDV <noreply@hdv.li>\r\n";
	$headers .= "Reply-To: noreply@hdv.li\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

	mail($courriel, $sujet, $message, $headers);
}

function getValeurProfonde($objet, string $chemin)
{
	$niveaux = explode('.', $chemin);

	foreach($niveaux as $niveau)
	{
		if(is_object($objet) AND isset($objet->$niveau))
			$objet = $objet->$niveau;

		elseif(is_array($objet) AND isset($objet[$niveau]))
			$objet = $objet[$niveau];

		else
			return null;
	}

	return $objet;
}

function trierObjetsParChemin(array &$array, string $chemin, string $ordre = 'asc'): void
{
	usort($array, function ($a, $b) use ($chemin, $ordre)
	{
		$valA = getValeurProfonde($a, $chemin);
		$valB = getValeurProfonde($b, $chemin);

		if(is_string($valA)) $valA = mb_strtolower($valA);
		if(is_string($valB)) $valB = mb_strtolower($valB);

		$cmp = $valA <=> $valB;

		return $ordre === 'desc' ? -$cmp : $cmp;
	});
}

function recaptcha($recaptchaReponse): bool
{
	if(!empty($recaptchaReponse))
	{
		$context = stream_context_create([
			'http' => [
				'header' => "Content-type: application/x-www-form-urlencoded\r\n",
				'method' => 'POST',
				'content' => http_build_query([
					'secret' => RECAPTCHA_PRIVEE,
					'response' => $recaptchaReponse,
					'remoteip' => getRemoteAddr(),
				]),
				'timeout' => 10,
			],
		]);

		$resultat = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);

		if(!empty($resultat))
		{
			$json = json_decode($resultat, true);

			return (!empty($json['success']) AND $json['score'] >= 0.1 AND $json['action'] === 'submit') ? true : false;
		}
	}

	return false;
}

// function jetonBearer(): ?string
// {
// 	$ch = curl_init('https://oauth.battle.net/token');
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
// 	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
// 	curl_setopt($ch, CURLOPT_USERPWD, API_KEY.':'.API_SECRET);
// 	curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
// 	$reponse = curl_exec($ch);

// 	if(curl_errno($ch))
// 	{
// 		curl_close($ch);

// 		throw new Exception('Erreur cURL : '.curl_error($ch));
// 	}

// 	curl_close($ch);

// 	return !empty($reponse) ? json_decode($reponse)->access_token : null;
// }