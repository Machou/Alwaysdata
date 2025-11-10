<?php
// https://github.com/maxmind/GeoIP2-php
use GeoIp2\Database\Reader;

// https://github.com/matomo-org/device-detector
use DeviceDetector\DeviceDetector;

class Grab
{
	public $pdo;
	private $ip;
	private $user_agent;
	private $temps_cache = '-3 seconds';
	// private $temps_cache = '-30 days';

	public function __construct(PDO $pdo, string $ip, ?string $user_agent)
	{
		$this->pdo			= $pdo;
		$this->ip			= $ip;
		$this->user_agent	= !empty($user_agent) ? $user_agent : getRandomUserAgent();
	}

	public function viderCache(): void
	{
		if(!is_dir($_SERVER['DOCUMENT_ROOT'].'assets/cache/ip')) {
			return;
		}

		$limiteTemps = time() - (15 * 24 * 60 * 60);

		$elements = scandir($_SERVER['DOCUMENT_ROOT'].'assets/cache/ip');
		foreach($elements as $element)
		{
			if($element === '.' OR $element === '..') {
				continue;
			}

			$fichier = rtrim($_SERVER['DOCUMENT_ROOT'].'assets/cache/ip', DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$element;

			if(is_file($fichier))
			{
				if(filemtime($fichier) < $limiteTemps) {
					unlink($fichier);
				}
			}
		}
	}

	private function isVpn(): bool
	{
		$fichierCache = $_SERVER['DOCUMENT_ROOT'].'assets/cache/ip/vpn-'.str_replace(':', '-', $this->ip);

		if(!file_exists($fichierCache) OR (filemtime($fichierCache) < strtotime($this->temps_cache)))
		{
			$params = [
				'user_agent' => $this->user_agent,
				'user_language' => (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'fr-FR'),
				'strictness' => 1,
				'allow_public_access_points' => true,
				'lighter_penalties' => 'false',
			];

			$get = get('https://www.ipqualityscore.com/api/json/ip/RwgmmuxluW5qxLCVJS4qkNdyUqaJ9x3P/'.$this->ip.'?'.http_build_query($params));
			$ip = !empty($get) ? json_decode($get, true) : null;

			$donneesIpVpn = (!empty($ip) AND $ip['success'] === true AND $ip['vpn'] === true) ? true : false;

			cache($fichierCache, $donneesIpVpn);

			return (bool) $donneesIpVpn;
		}

		else
			return (bool) (file_exists($fichierCache) AND filesize($fichierCache) > 0 AND file_get_contents($fichierCache) == 'oui') ? true : false;
	}

	private function abuseipDB(): string
	{
		$fichierCache = $_SERVER['DOCUMENT_ROOT'].'assets/cache/ip/abuse-'.str_replace(':', '-', $this->ip);
		if(!file_exists($fichierCache) OR (filemtime($fichierCache) < strtotime($this->temps_cache)))
		{
			$params = [
				'ipAddress' => $this->ip,
				'maxAgeInDays' => 90,
			];

			$headers = [
				'headers' => [
					'Accept' => 'application/json',
					'Key' => '83b773c2ab0035516d7ba5a9f3a3096e4d24f18fec54e23fa9b7dd0212fe9e6da350c4abc8249248',
				]
			];

			$get = get('https://api.abuseipdb.com/api/v2/check?'.http_build_query($params), headers: $headers);
			$ip = !empty($get) ? json_decode($get, true) : null;

			if(!empty($ip['data']['abuseConfidenceScore']) AND $ip['data']['abuseConfidenceScore'] >= 75)	$score = 'Spam détecté';
			else																							$score = 'IP de confiance';

			cache($fichierCache, $score);

			return (string) $score;
		}

		else
			return (string) (file_exists($fichierCache) AND filesize($fichierCache) > 0) ? file_get_contents($fichierCache) : 'Parfait';
	}

	private function cdnIp(string $ip): bool|string
	{
		if(isIPv4($ip))
		{
			$fichierCache = $_SERVER['DOCUMENT_ROOT'].'assets/cache/ip/cdn-'.$this->ip;
			if(!file_exists($fichierCache) OR (filemtime($fichierCache) < strtotime($this->temps_cache)))
			{
				// $ip = (cdn($ip) !== false) ? cdn($ip) : false;
				$ip = cdn($ip) ?? false;

				cache($fichierCache, $ip);

				return $ip;
			}

			else
			{
				if(file_exists($fichierCache) AND filesize($fichierCache) > 0)
				{
					$cdnArray = ['cloudflare', 'imperva', 'fastly', 'cloudfront', 'cloudfront_region', 'fly.io'];

					$fg = file_get_contents($fichierCache);

					return (string) in_array($fg, $cdnArray) ? $fg : 'cdn inconnu';
				}

				return false;
			}
		}

		return false;
	}

	private function whois(string $ip): ?string
	{
		$socket = fsockopen('whois.ripe.net', 43, $errno, $errstr, 30);
		if(!$socket)
			return 'Erreur de connexion : '.$errstr ($errno);

		fwrite($socket, $ip."\r\n");

		$reponse = '';
		while(!feof($socket)) {
			$reponse .= fgets($socket, 4096);
		}

		fclose($socket);

		return $reponse;
	}

	private function latitude(?float $latitude): ?float
	{
		return !empty($latitude) ? $latitude : null;
	}

	private function longitude(?float $longitude): ?float
	{
		return !empty($longitude) ? $longitude : null;
	}

	private function GoogleMaps(?float $latitude, ?float $longitude, $ville = false): ?string
	{
		$latitude = $this->latitude($latitude);
		$longitude = $this->longitude($longitude);

		return (string) (!empty($latitude) AND !empty($longitude)) ? 'https://www.google.com/maps/@'.$latitude.','.$longitude.','.($ville ? '13z' : '5z') : null;
	}

	private function arch(string $user_agent): string
	{
		if(preg_match('/x64|WOW64|Win64/is', $user_agent))	$cpu = '64 bit';
		elseif(preg_match('/arm64|phone/is', $user_agent))	$cpu = 'ARM 64 bit';
		elseif(preg_match('/arm32/is', $user_agent))		$cpu = 'ARM 32 bit';
		else												$cpu = '32 bit';

		return (string) $cpu;
	}

	public function get_infos(): array|string
	{
		if(filter_var($this->ip, FILTER_VALIDATE_IP))
		{
			if(filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE))
			{
				$cloudflare = !empty($_SERVER['HTTP_CF_IPCITY']) ? true : false;

				try {
					$readerCountry = new Reader($_SERVER['DOCUMENT_ROOT'].'geo/GeoLite2-Country.mmdb');
					$recordCountry = $readerCountry->country($this->ip);
				} catch (\Exception $e) { }

				try {
					$readerCity = new Reader($_SERVER['DOCUMENT_ROOT'].'geo/GeoLite2-City.mmdb');
					$recordCity = $readerCity->city($this->ip);
				} catch (\Exception $e) { }

				try {
					$readerASN = new Reader($_SERVER['DOCUMENT_ROOT'].'geo/GeoLite2-ASN.mmdb');
					$recordASN = $readerASN->asn($this->ip);
				} catch (\Exception $e) { }

				$dd = new DeviceDetector($this->user_agent);
				$dd->parse();

				/* Latitude et Longitude Payts */

				$latPays = $longPays = null;

				if(!empty($recordCountry->country->isoCode))
				{
					try {
						$stmt = $this->pdo->prepare('SELECT latitude, longitude FROM countries WHERE iso2 = :iso2 LIMIT 1');
						$stmt->execute([
							'iso2' => mb_strtoupper($recordCountry->country->isoCode)
						]);
						$resVille = $stmt->fetch();
						$latPays = $resVille['latitude'] ?? null;
						$longPays = $resVille['longitude'] ?? null;
					} catch (\PDOException $e) { }
				}

				/* Latitude et Longitude Ville */

				$latVille = $longVille = null;

				if(!empty($recordCity->city->name) AND !empty($recordCountry->country->isoCode))
				{
					try {
						$stmt = $this->pdo->prepare('SELECT latitude, longitude FROM cities WHERE name = :name AND country_code = :country_code LIMIT 1');
						$stmt->execute([
							'name' => (!empty($recordCity->city->name) ? mb_strtoupper($recordCity->city->name) : null),
							'country_code' => (!empty($recordCountry->country->isoCode) ? mb_strtoupper($recordCountry->country->isoCode) : null)
						]);
						$resVille = $stmt->fetch();
						$latVille = $resVille['latitude'] ?? null;
						$longVille = $resVille['longitude'] ?? null;
					} catch (\PDOException $e) { }
				} elseif((empty($_POST['chercher_ip']) OR empty($_GET['ip'])) AND $cloudflare) {
					$latVille = null;
					$longVille = null;
				}

				try {
					$stmt = $this->pdo->prepare('SELECT countries.*, regions.translations AS region_translations, subregions.translations AS subregion_translations
					FROM countries
					INNER JOIN regions ON countries.region_id = regions.id
					INNER JOIN subregions ON countries.subregion_id = subregions.id
					WHERE iso2 = :iso2');
					$stmt->execute([
						'iso2' => (!empty($recordCountry->country->isoCode) ? mb_strtoupper($recordCountry->country->isoCode) : null)
					]);
					$res = $stmt->fetch();
				} catch (\PDOException $e) { }

				if(!empty($res))
				{
					$paysFr				= json_decode($res['translations']) ?? null;
					$paysNom			= $paysFr->fr;

					$continentFr		= json_decode($res['region_translations']) ?? null;
					$continent			= !empty($continentFr->fr) ? $continentFr->fr : null;

					$sousContinentFr	= json_decode($res['subregion_translations']) ?? null;
					$sousContinentTrad	= !empty($sousContinentFr->fr) ? $sousContinentFr->fr : null;

					$ville				= !empty($recordCity->city->name) ? $recordCity->city->name : null;

					$tzs				= json_decode($res['timezones']) ?? null;
					$gmtZoneName		= !empty($tzs[0]->zoneName) ? $tzs[0]->zoneName : null;
					$gmtOffset			= !empty($tzs[0]->gmtOffset) ? $tzs[0]->gmtOffset : null;
					$gmtOffsetName		= !empty($tzs[0]->gmtOffsetName) ? $tzs[0]->gmtOffsetName : null;
					$gmtAbbreviation	= !empty($tzs[0]->abbreviation) ? $tzs[0]->abbreviation : null;
					$gmtTzName			= !empty($tzs[0]->tzName) ? $tzs[0]->tzName : null;

					$numericCode		= $res['numeric_code'] ?? null;
					$paysIso2			= !empty($res['iso2']) ? $res['iso2'] : null;
					$paysIso3			= !empty($res['iso3']) ? $res['iso3'] : null;
					$paysIso3Num		= !empty($res['numeric_code']) ? $res['numeric_code'] : null;
					$phoneCode			= !empty($res['phonecode']) ? $res['phonecode'] : null;
					$capitale			= !empty($res['capital']) ? $res['capital'] : null;
					$currency			= !empty($res['currency']) ? $res['currency'] : null;
					$currencyName		= !empty($res['currency_name']) ? $res['currency_name'] : null;
					$currencySymbol		= !empty($res['currency_symbol']) ? $res['currency_symbol'] : null;
					$tld				= !empty($res['tld']) ? $res['tld'] : null;
					$paysNomNative		= !empty($res['native']) ? $res['native'] : null;
					$nationalite		= !empty($res['nationality']) ? $res['nationality'] : null;
					$emoji				= !empty($res['emoji']) ? $res['emoji'] : null;
					$emojiU				= !empty($res['emojiU']) ? $res['emojiU'] : null;
					$wikiDataId			= !empty($res['wikiDataId']) ? $res['wikiDataId'] : null;
					$wikiDataIdUrl		= !empty($res['wikiDataId']) ? 'https://www.wikidata.org/wiki/'.$res['wikiDataId'] : null;
				}

				$dispositifs = [
					// Type d’appareil

					'TELEPHONE'						=> $dd->isFeaturePhone()		? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/device/mobiles.yml
					'TABLETTE'						=> $dd->isTablet()				? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/device/mobiles.yml
					'PHABLETTE'						=> $dd->isPhablet()				? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/device/mobiles.yml
					'CONSOLE'						=> $dd->isConsole()				? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/device/consoles.yml
					'LECTEUR_MULTIMEDIA_PORTABLE'	=> $dd->isPortableMediaPlayer()	? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/device/portable_media_player.yml
					'NAVIGATEUR_DE_VOITURE'			=> $dd->isCarBrowser()			? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/device/car_browsers.yml
					'TELE'							=> $dd->isTV()					? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/device/televisions.yml
					'SMART_DISPLAY'					=> $dd->isSmartDisplay()		? true		: false,
					'SMART_SPEAKER'					=> $dd->isSmartSpeaker()		? true		: false,
					'CAMERA'						=> $dd->isCamera()				? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/device/cameras.yml
					'EST_PORTATIF'					=> $dd->isWearable()			? true		: false,
					'EST_UN_PERIPHERIQUE'			=> $dd->isPeripheral()			? true		: false,

					// Type de client

					'NAVIGATEUR'					=> $dd->isBrowser()				? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/client/browsers.yml
					'LECTEUR_DE_FLUX'				=> $dd->isFeedReader()			? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/client/feed_readers.yml
					'APPLICATION_MOBILE'			=> $dd->isMobileApp()			? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/client/mobile_apps.yml
					'PIM'							=> $dd->isPIM()					? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/client/pim.yml
					'LIBRAIRIE'						=> $dd->isLibrary()				? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/client/libraries.yml
					'MEDIA_PLAYER'					=> $dd->isMediaPlayer()			? true		: false, // https://github.com/matomo-org/device-detector/blob/master/regexes/client/mediaplayers.yml
				];

				$dispos = [];
				foreach($dispositifs as $c => $v)
				{
					if($v)
						$dispos[] = $c;
				}

				$dispos = !empty($dispos) ? $dispos : null;
				$latCf = !empty($_SERVER['HTTP_CF_IPLATITUDE']) ? $_SERVER['HTTP_CF_IPLATITUDE'] : null;
				$longCf = !empty($_SERVER['HTTP_CF_IPLONGITUDE']) ? $_SERVER['HTTP_CF_IPLONGITUDE'] : null;

				// Tableau Grab

				$informationsIp = [
					'DISTANT' =>
					[
						'TIMEZONE' =>
						[
							'NOM_TIMEZONE'					=> !empty($gmtZoneName)									? $gmtZoneName									: 'n/a',
							'GMT_OFFSET'					=> !empty($gmtOffset)									? $gmtOffset									: 'n/a',
							'GMT_OFFSET_NOM'				=> !empty($gmtOffsetName)								? $gmtOffsetName								: 'n/a',
							'GMT_ABBREVIATION'				=> !empty($gmtAbbreviation)								? $gmtAbbreviation								: 'n/a',
							'GMT_TZ_NOM'					=> !empty($gmtTzName)									? $gmtTzName									: 'n/a',
						],

						'INFOS_IP' =>
						[
							'ASN'							=> !empty($recordASN->autonomousSystemNumber)			? 'AS'.$recordASN->autonomousSystemNumber		: 'n/a',
							'ASN_ORGANISATION'				=> (!empty($recordASN->autonomousSystemOrganization)	? $recordASN->autonomousSystemOrganization		: 'n/a'),
							'ADRESSE_IP'					=> $this->ip,
							'IP_DECIMALE'					=> ip2long($this->ip),
							'IP_V4'							=> (isIPv4($this->ip)									? 'true' 										: 'false'),
							'IP_V6'							=> (isIPv6($this->ip)									? 'true' 										: 'false'),
							'IP_CDN'						=> (!empty($this->cdnIp($this->ip))						? $this->cdnIp($this->ip)						: 'false'),
							'IP_VPN'						=> ($this->isVpn()										? 'true'										: 'false'),
							'NOM_HOTE'						=> gethostbyaddr($this->ip),
							'NOM_HOTE_LONG'					=> exec('host '.$this->ip),
							'WHOIS_IP'						=> trim($this->whois($this->ip))
						],

						'GEOLOCALISATION' =>
						[
							'CONTINENT'						=> (!empty($continent)									? $continent									: 'n/a'),
							'SOUS_CONTINENT'				=> (!empty($sousContinentTrad)							? $sousContinentTrad							: 'n/a'),
							'PAYS_NOM'						=> (!empty($paysNom)									? $paysNom										: 'n/a'),
							'PAYS_NOM_NATIVE'				=> (!empty($paysNomNative)								? $paysNomNative								: 'n/a'),
							'PAYS_ISO2'						=> (!empty($paysIso2)									? $paysIso2										: 'n/a'),
							'PAYS_ISO3'						=> (!empty($paysIso3)									? $paysIso3										: 'n/a'),
							'PAYS_ISO3_NUMERIQUE'			=> (!empty($paysIso3Num)								? $paysIso3Num									: 'n/a'),
							'VILLE'							=> (!empty($ville)										? $ville										: 'n/a'),
							'CAPITALE'						=> (!empty($capitale)									? $capitale										: 'n/a'),
							'CODE_POSTAL'					=> (!empty($recordCity->postal->code)					? $recordCity->postal->code						: 'n/a'),
							'LATITUDE_PAYS'					=> (!empty($latPays)									? $latPays										: 'n/a'),
							'LONGITUDE_PAYS'				=> (!empty($longPays)									? $longPays										: 'n/a'),
							'GOOGLE_MAPS_PAYS'				=> (!empty($this->GoogleMaps($latPays, $longPays))		? $this->GoogleMaps($latPays, $longPays)		: null),
							'LATITUDE_VILLE'				=> (!empty($latVille)									? $latVille										: 'n/a'),
							'LONGITUDE_VILLE'				=> (!empty($longVille)									? $longVille									: 'n/a'),
							'GOOGLE_MAPS_VILLE'				=> (!empty($this->GoogleMaps($latVille, $longVille, 1))	? $this->GoogleMaps($latVille, $longVille, 1)	: null),
						],

						'DETAILS_PAYS' =>
						[
							'CODE_TELEPHONE'				=> (!empty($phoneCode)									? $phoneCode									: 'n/a'),
							'CODE_NDD'						=> (!empty($tld)										? $tld											: 'n/a'),
							'CODE_NUMERIQUE'				=> (!empty($numericCode)								? $numericCode									: 'n/a'),
							'CODE_EMOJI'					=> (!empty($emoji)										? $emoji										: 'n/a'),
							'CODE_EMOJIU'					=> (!empty($emojiU)										? $emojiU										: 'n/a'),
							'CODE_WIKIDATA'					=> (!empty($wikiDataId)									? $wikiDataId									: 'n/a'),
							'CODE_WIKIDATA_URL'				=> (!empty($wikiDataIdUrl)								? $wikiDataIdUrl								: 'n/a'),
							'DEVISE_CODE'					=> (!empty($currency)									? $currency										: 'n/a'),
							'DEVISE_NOM'					=> (!empty($currencyName)								? $currencyName									: 'n/a'),
							'DEVISE_SYMBOLE'				=> (!empty($currencySymbol)								? $currencySymbol								: 'n/a'),
							'NATIONALITE'					=> (!empty($nationalite)								? $nationalite									: 'n/a'),
						],

						'LISTES' =>
						[
							'BITCOIN_NODES'					=> (isInFile('../geo/bitcoin_nodes.ipset', $this->ip)	? 'true'										: 'false'),
							'FIREHOL_LEVEL1'				=> (isInFile('../geo/firehol_level1.netset', $this->ip)	? 'true'										: 'false'),
							'FIREHOL_LEVEL2'				=> (isInFile('../geo/firehol_level2.netset', $this->ip)	? 'true'										: 'false'),
							'FIREHOL_LEVEL3'				=> (isInFile('../geo/firehol_level3.netset', $this->ip)	? 'true'										: 'false'),
							'FIREHOL_LEVEL4'				=> (isInFile('../geo/firehol_level4.netset', $this->ip)	? 'true'										: 'false'),
							'IP_STOP_FORUM_SPAM'			=> (isInFile('../geo/stopforumspam.ipset', $this->ip)	? 'true'										: 'false'),
							'IP_TOR'						=> (isInFile('../geo/Tor_ip_list_ALL.csv', $this->ip)	? 'true'										: 'false'),
							'IP_TOR_SORTIE'					=> (isInFile('../geo/Tor_ip_list_EXIT.csv', $this->ip)	? 'true'										: 'false'),
							'IP_CONFIANCE'					=> $this->abuseipDB(),
						],
					],

					'INFOS_CLIENT' =>
					[
						'INFOS_GLOBALE' =>
						[
							'DATE'							=>date('d-m-Y', time()),
							'HEURE_SYSTEME'					=>date('H:i:s', time()),
							'DATE_REQUETE'					=> (!empty($_SERVER['REQUEST_TIME'])					? dateFormat($_SERVER['REQUEST_TIME'], 'DATE_RFC7231')	: 'n/a'),
							'HEURE_UNIX'					=>time(),
							'HEURE_LOCALE'					=>localtime()[2].':'.localtime()[1].':'.localtime()[0],
						],

						'GEOLOCALISATION_CLIENT' =>
						[
							'CLIENT_CODE_CONTINENT'			=> (!empty($_SERVER['HTTP_CF_TIMEZONE'])				? explode('/', $_SERVER['HTTP_CF_TIMEZONE'])[0]	: 'n/a'),
							'CLIENT_CODE_CONTINENT_ISO3'	=> (!empty($_SERVER['HTTP_CF_IPCONTINENT'])				? $_SERVER['HTTP_CF_IPCONTINENT']				: 'n/a'),
							'CLIENT_REGION'					=> (!empty($_SERVER['HTTP_CF_REGION'])					? $_SERVER['HTTP_CF_REGION']					: 'n/a'),
							'CLIENT_PAYS_ISO3'				=> (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])				? $_SERVER['HTTP_CF_IPCOUNTRY']					: 'n/a'),
							'CLIENT_VILLE'					=> (!empty($_SERVER['HTTP_CF_IPCITY'])					? $_SERVER['HTTP_CF_IPCITY']					: 'n/a'),
							'CLIENT_CODE_POSTAL'			=> (!empty($_SERVER['HTTP_CF_POSTAL_CODE'])				? $_SERVER['HTTP_CF_POSTAL_CODE']				: 'n/a'),
							'CLIENT_LATITUDE_VILLE'			=> (!empty($latCf)										? $latCf										: 'n/a'),
							'CLIENT_LONGITUDE_VILLE'		=> (!empty($longCf)										? $longCf										: 'n/a'),
							'CLIENT_GOOGLE_MAPS_VILLE'		=> ($this->GoogleMaps($latCf, $longCf, 1)				? $this->GoogleMaps($latCf, $longCf, 1)			: null),
						],

						'HTTP_SERVEUR' =>
						[
							'HTTP_USER_AGENT'				=> $this->user_agent,
							'HTTP_ACCEPT'					=> (!empty($_SERVER['HTTP_ACCEPT'])						? $_SERVER['HTTP_ACCEPT']							: 'n/a'),
							'HTTP_ACCEPT_ENCODING'			=> (!empty($_SERVER['HTTP_ACCEPT_ENCODING'])			? $_SERVER['HTTP_ACCEPT_ENCODING']					: 'n/a'),
							'HTTP_ACCEPT_LANGUAGE'			=> (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])			? $_SERVER['HTTP_ACCEPT_LANGUAGE']					: 'n/a'),
							'HTTP_CONNECTION'				=> (!empty($_SERVER['HTTP_CONNECTION'])					? $_SERVER['HTTP_CONNECTION']						: 'n/a'),
							'HTTP_REFERER'					=> (!empty($_SERVER['HTTP_REFERER'])					? $_SERVER['HTTP_REFERER']							: 'n/a'),
							'HTTP_DNT'						=> (!empty($_SERVER['HTTP_DNT'])						? $_SERVER['HTTP_DNT']								: 'n/a'),
						],

						'APPAREIL' =>
						[
							'CLIENT_TYPE'					=> (!empty($dd->getClient()['type'])					? $dd->getClient()['type']							: 'n/a'),
							'CLIENT_NOM'					=> (!empty($dd->getClient()['name'])					? $dd->getClient()['name']							: 'n/a'),
							'CLIENT_NOM_COURT'				=> (!empty($dd->getClient()['short_name'])				? $dd->getClient()['short_name']					: 'n/a'),
							'CLIENT_VERSION'				=> (!empty($dd->getClient()['version'])					? $dd->getClient()['version']						: 'n/a'),
							'CLIENT_MOTEUR'					=> (!empty($dd->getClient()['engine'])					? $dd->getClient()['engine']						: 'n/a'),
							'CLIENT_MOTEUR_VERSION'			=> (!empty($dd->getClient()['engine_version'])			? $dd->getClient()['engine_version']				: 'n/a'),
							'CLIENT_FAMILLE'				=> (!empty($dd->getClient()['family'])					? $dd->getClient()['family']						: 'n/a'),

							'OS'							=> (!empty($dd->getOs()['name'])						? $dd->getOs()['name']								: 'n/a'),
							'OS_NOM'						=> (!empty($dd->getOs()['short_name'])					? $dd->getOs()['short_name']						: 'n/a'),
							'OS_VERSION'					=> (!empty($dd->getOs()['version'])						? $dd->getOs()['version']							: 'n/a'),
							'OS_PLATFORME'					=> (!empty($dd->getOs()['platform'])					? $dd->getOs()['platform']							: 'n/a'),
							'OS_FAMILLE'					=> (!empty($dd->getOs()['family'])						? $dd->getOs()['family']							: 'n/a'),

							'DISPOSITIF'					=> (!empty($dispos)										? ucfirst(mb_strtolower(implode(', ', $dispos)))	: 'n/a'),
							'DISPOSITIF_NOM'				=> (!empty($dd->getBrandName())							? $dd->getBrandName()								: 'n/a'),
							'DISPOSITIF_MODELE'				=> (!empty($dd->getModel())								? $dd->getModel()									: 'n/a'),
							'CPU'							=> $this->arch($this->user_agent),
						]
					]
				];

				return (array) $informationsIp;
			}

			return ['ERREUR' => 'L’adresse IP saisie est incorrecte'];
		}

		return ['ERREUR' => 'L’adresse IP saisie est incorrecte'];
	}
}