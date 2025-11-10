<?php
require_once '../config/wow_config.php';

if(estAdmin())
{
	if(isset($_GET['majServeurs']))
	{
		$serveursListeBlanche = ['Khadgar', 'Chromaggus', 'Dentarg', 'La Croisade écarlate', 'Runetotem', 'Shadowsong', 'Silvermoon', 'Sunstrider', 'Executus', 'Mazrigos', 'Karazhan', 'Auchindoun', 'Shattered Halls', 'Gordunni', 'Soulflayer', 'Deathguard', 'Sporeggar', 'Nethersturm', 'Shattrath', 'Deepholm', 'Echsenkessel', 'Blutkessel', 'Galakrond', 'Razuvious', 'Deathweaver', 'Terenas', 'Thunderhorn', 'Turalyon', 'Ravencrest', 'Shattered Hand', 'Spinebreaker', 'Stormscale', 'Earthen Ring', 'Alexstrasza', 'Alleria', 'Antonidas', 'Blackhand', 'Gilneas', 'Kargath', 'Khaz\'goroth', 'Madmortem', 'Zuluhed', 'Nozdormu', 'Perenolde', 'Aegwynn', 'Dun Morogh', 'Thrall', 'Theradras', 'Genjuros', 'Balnazzar', 'Wrathbringer', 'Talnivarr', 'Emeriss', 'Drak\'thul', 'Ahn\'Qiraj', 'Ysera', 'Malygos', 'Anetheron', 'Nazjatar', 'Tichondrius', 'Steamwheedle Cartel', 'Die ewige Wacht', 'Die Todeskrallen', 'Die Arguswacht', 'Uldaman', 'Confrérie du Thorium', 'Dragonblight', 'Tarren Mill', 'C\'Thun', 'Alonsus', 'Blade\'s Edge', 'Doomhammer', 'Twilight\'s Hammer', 'Daggerspine', 'Sargeras', 'Emerald Dream', 'Quel\'Thalas', 'Bloodscalp', 'Burning Blade', 'Crushridge', 'Dragonmaw', 'Dunemaul', 'Dethecus', 'Durotan', 'Aggramar', 'Arathor', 'Azjol-Nerub', 'Draenor', 'Vol\'jin', 'Arak-arahm', 'Zenedar', 'Agamaggan', 'Bladefist', 'Culte de la Rive noire', 'Argent Dawn', 'Onyxia', 'Nefarian', 'Das Syndikat', 'Laughing Skull', 'Neptulon', 'Twisting Nether', 'The Maelstrom', 'Bloodfeather', 'Frostwhisper', 'Kor\'gall', 'Defias Brotherhood', 'Rashgarroth', 'Les Sentinelles', 'Suramar', 'Garrosh', 'Arygos', 'Teldrassil', 'Lordaeron', 'Aggra (Português)', 'Terokkar', 'Baelgun', 'Cho\'gall', 'Nordrassil', 'Sylvanas', 'Drek\'Thar', 'Ghostlands', 'The Sha\'tar', 'Chants éternels', 'Marécage de Zangar', 'Naxxramas', 'Arthas', 'Azshara', 'Blackmoore', 'Destromath', 'Eredar', 'Frostwolf', 'Kil\'jaeden', 'Mal\'Ganis', 'Zirkel des Cenarius', 'Vashj', 'Hyjal', 'Ulduar', 'Howling Fjord', 'Al\'Akir', 'Stormreaver', 'Magtheridon', 'Lightning\'s Blade', 'Kirin Tor', 'Archimonde', 'Elune', 'Chamber of Aspects', 'Ravenholdt', 'Pozzo dell\'Eternità', 'Eonar', 'Vek\'nilash', 'Frostmane', 'Bloodhoof', 'Kael\'thas', 'Haomarush', 'Khaz Modan', 'Varimathras', 'Hakkar', 'Blackrock', 'Kel\'Thuzad', 'Mannoroth', 'Proudmoore', 'Garona', 'Darkmoon Faire', 'Vek\'lor', 'Mug\'thol', 'Taerar', 'Dalvengyr', 'Rajaxx', 'Malorne', 'Der abyssische Rat', 'Der Mithrilorden', 'Ambossar', 'Krasus', 'Arathi', 'Ysondre', 'Eldre\'Thalas', 'Kilrogg', 'Wildhammer', 'Saurfang', 'Nemesis', 'Fordragon', 'Borean Tundra', 'Les Clairvoyants', 'Skullcrusher', 'Lothar', 'Sinstralis', 'Terrordar', 'Scarshield Legion', 'Kul Tiras', 'Stormrage', 'Ner\'zhul', 'Dun Modr', 'Zul\'jin', 'Uldum', 'Sanguino', 'Shen\'dralar', 'Tyrande', 'Exodar', 'Los Errantes', 'Lightbringer', 'Darkspear', 'Burning Steppes', 'Bronze Dragonflight', 'Anachronos', 'Colinas Pardas', 'Un\'Goro', 'Illidan', 'Rexxar', 'Festung der Stürme', 'Gul\'dan', 'Aszune', 'Aerie Peak', 'Xavius', 'Throk\'Feroth', 'Minahonda', 'Tirion', 'Sen\'jin', 'Trollbane', 'Aman\'Thul', 'Bronzebeard', 'Die Aldor', 'Temple noir', 'Eversong', 'Thermaplugg', 'Grom', 'Goldrinn', 'Blackscar', 'Forscherliga', 'Eitrigg', 'Todeswache', 'Dalaran', 'Frostmourne', 'Malfurion', 'Krag\'jin', 'Gorgonnash', 'Burning Legion', 'Azuremyst', 'Anub\'arak', 'Nera\'thor', 'Kult der Verdammten', 'Der Rat von Dalaran', 'Hellscream', 'Ragnaros', 'Darksorrow', 'The Venture Co', 'Grim Batol', 'Jaedenar', 'Kazzak', 'Moonglade', 'Conseil des Ombres', 'Nathrezim', 'Das Konsortium', 'Boulderfist', 'Deathwing', 'Area 52', 'Die Nachtwache', 'Booty Bay', 'Lich King', 'Hellfire', 'Outland', 'Greymane', 'Medivh', 'Die Silberne Hand', 'Nagrand', 'Azuregos', 'Ashenvale', 'Norgannon'];

		$serveursIndex = $apiClient->realm()->index()->realms;
		foreach($serveursIndex as $c => $serveur)
		{
			if(in_array($serveur->name->fr_FR, $serveursListeBlanche, true))
			{
				$infosServeur = $apiClient->realm()->get($serveur->id);
				if(!empty($infosServeur->id) AND !empty($infosServeur->connected_realm->href) AND !empty($infosServeur->region->name->fr_FR) AND !empty($infosServeur->name->fr_FR) AND !empty($infosServeur->slug) AND !empty($infosServeur->category->fr_FR) AND !empty($infosServeur->locale) AND !empty($infosServeur->timezone) AND !empty($infosServeur->type->type) AND !empty($infosServeur->type->name->fr_FR))
				{
					preg_match('/\d+/', $infosServeur->connected_realm->href, $m);
					$connected_realms = $m[0];

					try {
						$stmt = $pdo->prepare('INSERT INTO wow_serveurs (id_blizzard, id_connecte, region, nom, nom_ru, slug, langue, locale, timezone, type_serveur, type_serveur_nom) VALUES (:id_blizzard, :id_connecte, :region, :nom, :nom_ru, :slug, :langue, :locale, :timezone, :type_serveur, :type_serveur_nom)');
						$stmt->execute([
							'id_blizzard'		=> (int) $infosServeur->id,
							'id_connecte'		=> (int) $connected_realms,
							'region'			=> (string) $infosServeur->region->name->fr_FR,
							'nom'				=> (string) $infosServeur->name->fr_FR,
							'nom_ru'			=> ($infosServeur->locale == 'ruRU' ? (string) $infosServeur->name->ru_RU : null),
							'slug'				=> (string) $infosServeur->slug,
							'langue'			=> (string) ($infosServeur->locale === 'ptPT' ? 'Portugais' : $infosServeur->category->fr_FR),
							'locale'			=> (string) $infosServeur->locale,
							'timezone'			=> (string) $infosServeur->timezone,
							'type_serveur'		=> (string) $infosServeur->type->type,
							'type_serveur_nom'	=> (string) $infosServeur->type->name->fr_FR,
						]);
					} catch (\PDOException $e) { }
				}
			}
		}

		setFlash('success', 'Serveurs mis à jour !');

		header('Location: /admin');
		exit;
	}

	elseif(isset($_GET['majListeMascottes']))
	{
		$mascottesBdd = $apiClient->pet()->index();

		foreach($mascottesBdd->pets as $val)
		{
			if(!empty($val->id) AND !empty($val->name->fr_FR))
			{
				$stmt = $pdo->prepare('INSERT IGNORE INTO wow_mascottes (id, nom_mascotte) VALUES (:id, :nom_mascotte)');
				$stmt->execute([
					'id' => $val->id,
					'nom_mascotte' => $val->name->fr_FR
				]);

				if($stmt->rowCount() > 0)
					$nouvelleMascotte[] = 'Nouvelle mascotte ajoutée : <span class="fw-bold">'.secuChars($val->name->fr_FR).'</span> ('.$val->id.')';
			}
		}

		!empty($nouvelleMascotte) ? setFlash('success', count($nouvelleMascotte).' mascotte'.s($nouvelleMascotte).' ajoutée'.s($nouvelleMascotte)) : setFlash('danger', 'Aucune mascotte a été ajoutée');

		header('Location: /admin');
		exit;
	}

	elseif(isset($_GET['majPrixJeton']))
	{
		if(jetonMaj($pdo))
		{
			setFlash('success', 'Mise à jour des prix du jeton');

			header('Location: /admin');
			exit;
		}

		else
		{
			setFlash('danger', 'Erreur lors de la récupération des données sur Wowtoken.app');

			header('Location: /admin');
			exit;
		}
	}

	else
	{
		require_once 'a_body.php';

		echo '<h1><a href="/admin">Administration</a></h1>

		<div class="col-12 col-lg-10 mx-auto">
			<div class="row mb-4">
				<div class="col-12 d-flex flex-wrap justify-content-center gap-3">
					<a href="?majServeurs" class="btn btn-danger btn-lg">MàJ les serveurs</a>
					<a href="?majListeMascottes" class="btn btn-warning btn-lg">MàJ les mascottes</a>
					<a href="?majPrixJeton" class="btn btn-success btn-lg">MàJ prix Jeton</a>
				</div>
			</div>

			<div class="row">
				<div class="col-12 d-flex flex-wrap justify-content-center gap-3">
					<a href="https://thisip.pw/projets/adminer.php?server=mysql-blok.alwaysdata.net&username=blok&db=blok_hdv" class="btn btn-primary btn-lg" '.$onclick.'><i class="fa-solid fa-database"></i>Adminer</a>
				</div>
			</div>
		</div>';

		require_once 'a_footer.php';
	}
}

else
{
	header('Location: /');
	exit;
}