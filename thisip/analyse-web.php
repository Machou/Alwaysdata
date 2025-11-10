<?php
// https://github.com/symfony/http-client

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

// https://github.com/io-developer/php-whois

use Iodev\Whois\Factory;
use Iodev\Whois\Loaders\CurlLoader;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;

require_once 'a_body.php';

if(!empty($_GET['uniqid']) OR !empty($_POST['url']))
{
	if(!empty($uniqid))
	{
		if(!empty($resSelect['id']))
		{
			$url = secuChars($resSelect['url_formulaire']);
			$urlNdd = secuChars($resSelect['url']);

			if(!empty($resSelect['date']))
			{
				$dateDerniereAnalyse = new DateTime($resSelect['date']);
				$derniereAnalyse = (time() - $dateDerniereAnalyse->getTimestamp());
			}
		}

		else
			redirection('https://thisip.pw/analyse-web');
	}

	elseif(!empty($_POST['url']))
	{
		$url = clean(mb_strtolower($_POST['url']));
		$url = !preg_match('/^https?:\/\//is', $url) ? 'https://'.$url : $url;

		$urlParse = parse_url($url);

		if(!empty($urlParse['host']) AND mb_strlen($urlParse['host']) < 253)
		{
			if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{0,63}\.[a-z\.]{2,6})$/', $urlParse['host'], $regs))
			{
				$urlNdd = preg_replace('/^https?:\/\/(www\d*\.)?/i', '$2$3', $regs['domain']);

				if(filter_var($url, FILTER_VALIDATE_URL))
				{
					if(preg_match('/\.cv/is', $urlNdd))
					{
						unset($url);
						unset($urlNdd);

						$erreur = 'Le domaine <strong>.cv</strong> n’est pas encore pris en charge';
					}
				}

				else
					goto format;
			}

			else
				goto format;
		}
	}

	else
	{
		format:

		$erreur = 'Le domaine est mal formaté';
	}
}

if(empty($erreur) AND empty($_GET['id']) AND isset($_POST['url']))
{
	if(!empty($urlNdd) AND !empty($url))
	{
		if(checkdnsrr($urlNdd, 'NS'))
		{
			try
			{
				$loader = new CurlLoader();
				$loader->replaceOptions([
					CURLOPT_TIMEOUT => 60,
					CURLOPT_CONNECTTIMEOUT => 15,
				]);

				$whois				= Factory::get()->createWhois($loader);
				$domainLook			= $whois->lookupDomain($urlNdd);
				$domainInfo			= $whois->loadDomainInfo($urlNdd);
				$uniqid				= idAleatoire(15);
				$dateAnalyse		= (string) date(DATE_ATOM);
				$userIp				= (string) getRemoteAddr();

				$userAgent			= (string) !empty($_POST['formAnalyseUserAgent'])																	? secuChars($_POST['formAnalyseUserAgent'])			: getHttpUserAgent();
				$userAgentPerso		= !empty($_POST['formAnalyseUserAgentPerso'])																		? secuChars($_POST['formAnalyseUserAgentPerso'])	: null;
				$visiArray			= ['analyseVisible' => 1, 'analyseNonListee' => 2, 'analysePrivee' => 0];
				$visibilite			= (int) (!empty($_POST['formAnalyseVisibilite']) AND array_key_exists($_POST['formAnalyseVisibilite'], $visiArray))	? $visiArray[$_POST['formAnalyseVisibilite']]		: 1;
				$httpAcceptLanguage = (string) ($_POST['formAnalysePays'] !== 'auto' AND preg_match('/^[a-z]{2}$/is', $_POST['formAnalysePays']))		? mb_strtolower(trim($_POST['formAnalysePays']))	: getHttpAcceptLanguage();
				$refererPerso		= !empty($_POST['formAnalyseRefererPerso'])																			? secuChars($_POST['formAnalyseRefererPerso'])		: null;

				$ip					= gethostbyname($urlNdd);
				$grab				= new Grab($pdo, $ip, (!empty($userAgentPerso) ? $userAgentPerso : $userAgent));
				$ipdata				= $grab->get_infos();

				if($domainInfo) {
					$infosProprietaire			= secuChars($domainInfo->owner);
					$infosBureauEnrgst			= secuChars($domainInfo->registrar);
					$infosStatusBureauEnrgst	= json_encode($domainInfo->states);
					$infosServeurWhois			= secuChars($domainInfo->whoisServer);
					$infosNs					= json_encode($domainInfo->nameServers);
					$domainDateCreation			= (int) $domainInfo->creationDate;
					$domainDateExpiration		= (int) $domainInfo->expirationDate;
					$domainDateMaj				= (int) $domainInfo->updatedDate;
					$infosDateCreation			= !empty($domainDateCreation)	? date('Y-m-d H:i:s', $domainDateCreation)		: 0;
					$infosDateExpiration		= !empty($domainDateExpiration)	? date('Y-m-d H:i:s', $domainDateExpiration)	: 0;
					$infosDateMaj				= !empty($domainDateMaj)		? date('Y-m-d H:i:s', $domainDateMaj)			: 0;
					$infosDnssec				= preg_match('/signedDelegation/is', $domainInfo->dnssec) ? true : false;
				}

				else {
					$infosProprietaire = null;
					$infosBureauEnrgst = null;
					$infosStatusBureauEnrgst = null;
					$infosServeurWhois = null;
					$infosNs = null;
					$infosDateCreation = null;
					$infosDateExpiration = null;
					$infosDateMaj = null;
					$infosDnssec = false;
				}

				$infosEmplacementAdresseIp = json_encode([
					'Continent'			=> ((!empty($ipdata['DISTANT']['GEOLOCALISATION']['CONTINENT']) AND $ipdata['DISTANT']['GEOLOCALISATION']['CONTINENT'] !== 'n/a')				? $ipdata['DISTANT']['GEOLOCALISATION']['CONTINENT']					: null),
					'Sous continent'	=> ((!empty($ipdata['DISTANT']['GEOLOCALISATION']['SOUS_CONTINENT']) AND $ipdata['DISTANT']['GEOLOCALISATION']['SOUS_CONTINENT'] !== 'n/a')		? $ipdata['DISTANT']['GEOLOCALISATION']['SOUS_CONTINENT']				: null),
					'Pays'				=> ((!empty($ipdata['DISTANT']['GEOLOCALISATION']['PAYS_NOM']) AND $ipdata['DISTANT']['GEOLOCALISATION']['PAYS_NOM'] !== 'n/a')					? $ipdata['DISTANT']['GEOLOCALISATION']['PAYS_NOM']						: null),
					'Pays native'		=> ((!empty($ipdata['DISTANT']['GEOLOCALISATION']['PAYS_NOM_NATIVE']) AND $ipdata['DISTANT']['GEOLOCALISATION']['PAYS_NOM_NATIVE'] !== 'n/a')	? $ipdata['DISTANT']['GEOLOCALISATION']['PAYS_NOM_NATIVE']				: null),
					'Code ISO2'			=> ((!empty($ipdata['DISTANT']['GEOLOCALISATION']['PAYS_ISO2']) AND $ipdata['DISTANT']['GEOLOCALISATION']['PAYS_ISO2'] !== 'n/a')				? mb_strtolower($ipdata['DISTANT']['GEOLOCALISATION']['PAYS_ISO2'])		: null),
					'Code ISO3'			=> ((!empty($ipdata['DISTANT']['GEOLOCALISATION']['PAYS_ISO3']) AND $ipdata['DISTANT']['GEOLOCALISATION']['PAYS_ISO3'] !== 'n/a')				? $ipdata['DISTANT']['GEOLOCALISATION']['PAYS_ISO3']					: null),
					'Capitale'			=> ((!empty($ipdata['DISTANT']['GEOLOCALISATION']['CAPITALE']) AND $ipdata['DISTANT']['GEOLOCALISATION']['CAPITALE'] !== 'n/a')					? $ipdata['DISTANT']['GEOLOCALISATION']['CAPITALE']						: null),
					'Ville'				=> ((!empty($ipdata['DISTANT']['GEOLOCALISATION']['VILLE']) AND $ipdata['DISTANT']['GEOLOCALISATION']['VILLE'] !== 'n/a')						? $ipdata['DISTANT']['GEOLOCALISATION']['VILLE']						: null),
					'Code postal'		=> ((!empty($ipdata['DISTANT']['GEOLOCALISATION']['CODE_POSTAL']) AND $ipdata['DISTANT']['GEOLOCALISATION']['CODE_POSTAL'] !== 'n/a')			? $ipdata['DISTANT']['GEOLOCALISATION']['CODE_POSTAL']					: null),
				]);

				$infosAdresseIp			= isIPv4($ip)	? $ip : null;
				$infosNomHoteAdresseIp	= !empty($ip)	? gethostbyaddr($ip) : null;
				$infosAsn				= (!empty($ipdata['DISTANT']['INFOS_IP']['ASN']) AND $ipdata['DISTANT']['INFOS_IP']['ASN'] !== 'n/a')											? $ipdata['DISTANT']['INFOS_IP']['ASN']									: null;
				$infosAsnOrganisation	= (!empty($ipdata['DISTANT']['INFOS_IP']['ASN_ORGANISATION']) AND $ipdata['DISTANT']['INFOS_IP']['ASN_ORGANISATION'] !== 'n/a')					? $ipdata['DISTANT']['INFOS_IP']['ASN_ORGANISATION']					: null;

				try {
					$client = HttpClient::create([
						'headers' => [
							'Accept'			=> 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/png,image/svg+xml,*/*;q=0.8',
							'Accept-Language'	=> $httpAcceptLanguage,
							'Cache-Control'		=> 'no-cache',
							'max_redirects'		=> 10,
							'User-Agent'		=> (!empty($userAgentPerso) ? $userAgentPerso : $userAgent),
							'Referer'			=> (!empty($_POST['formAnalyseRefererPerso']) ? $_POST['formAnalyseRefererPerso'] : 'https://'.$urlNdd)
						],
						'verify_peer'			=> false
					]);

					$reponse = $client->request('GET', $url);
					if($reponse->getStatusCode() === 200)
					{
						$html = $reponse->getContent();

						if(preg_match('/[\p{Cyrillic}]/u', $html))
							$html = mb_encode_numericentity(htmlspecialchars_decode(htmlentities($html, ENT_NOQUOTES, 'UTF-8', false), ENT_NOQUOTES), [0x80, 0x10FFFF, 0, ~0], 'UTF-8');

						$domDoc = new DOMDocument();
						libxml_use_internal_errors(true);
						$domDoc->loadHTML($html);
						libxml_clear_errors();

						$titreTag = $domDoc->getElementsByTagName('title');
						$domNbLiens = $domDoc->getElementsByTagName('a');
						$domNbImgs = $domDoc->getElementsByTagName('img');

						$urlDerniere				= (string) !empty($reponse->getInfo('url'))					? secuChars($reponse->getInfo('url'))						: $url;
						$infosTechCodeReponse		= !empty($reponse->getStatusCode())							? (int) $reponse->getStatusCode()							: null;
						$infosTechDateServeur		= !empty($reponse->getHeaders()['date'][0])					? (string) $reponse->getHeaders()['date'][0]				: null;
						$infosTechEncodage			= !empty($reponse->getHeaders()['content-encoding'][0])		? (string) $reponse->getHeaders()['content-encoding'][0]	: null;
						$infosTechXFrameOptions		= !empty($reponse->getHeaders()['x-frame-options'][0])		? (string) $reponse->getHeaders()['x-frame-options'][0]		: null;
						$infosTechNbCookies			= (int) !empty($reponse->getHeaders()['set-cookie'])		? count($reponse->getHeaders()['set-cookie'])				: 0;
						$infosTechTitreSite			= ($titreTag->length > 0)									? $titreTag->item(0)->nodeValue								: null;
						$infosTechAdresseExacte		= !empty($reponse->getInfo('url'))							? (string) $reponse->getInfo('url')							: null;
						$infosTechNbLiens			= (int) $domNbLiens->length;
						$infosTechNbImages			= (int) $domNbImgs->length;
						$infosTechNbLettres			= (int) mb_strlen(preg_replace('/[^a-zA-Z]/', '', $html));
						$dom 						= !empty($html)												? (string) base64_encode(trim($html))						: null;
					}

					else
					{
						$urlDerniere				= $url;
						$infosTechCodeReponse		= null;
						$infosTechDateServeur		= null;
						$infosTechEncodage			= null;
						$infosTechXFrameOptions		= null;
						$infosTechNbCookies			= null;
						$infosTechTitreSite			= null;
						$infosTechAdresseExacte		= null;
						$infosTechNbLiens			= 0;
						$infosTechNbImages			= 0;
						$infosTechNbLettres			= 0;
						$dom 						= null;
					}
				} catch (\Exception $e) {
					$erreur = 'Erreur DNS : '.$e->getMessage();
					// throw new Exception('Erreur DNS : '.$e->getMessage());
				}

				$infosTechCdn = !empty(cdn($ip)) ? cdn($ip) : false;

				$listesGeoArray = [
					'bitcoin_nodes.ipset'	=> 'https://iplists.firehol.org/?ipset=bitcoin_nodes',
					'firehol_level1.netset'	=> 'https://iplists.firehol.org/?ipset=firehol_level1',
					'firehol_level2.netset'	=> 'https://iplists.firehol.org/?ipset=firehol_level2',
					'firehol_level3.netset'	=> 'https://iplists.firehol.org/?ipset=firehol_level3',
					'firehol_level4.netset'	=> 'https://iplists.firehol.org/?ipset=firehol_level4',
					'stopforumspam.ipset'	=> 'https://iplists.firehol.org/?ipset=stopforumspam',
					'Tor_ip_list_ALL.csv'	=> 'https://torstatus.rueckgr.at/',
					'Tor_ip_list_EXIT.csv'	=> 'https://torstatus.rueckgr.at/'
				];

				foreach($listesGeoArray as $geo => $lien)
					$infosTechVerificationsIp[$geo] = (isInFile('../geo/'.$geo, $ip) ? true : false);

				$infosTechVerificationsIp = json_encode($infosTechVerificationsIp);

				$whoisBrute = str_replace('>', '', $domainLook->text);
				$whoisBrute = str_replace('<', '', $whoisBrute);
				$whois = print_r($whoisBrute, true);

				$whoisIp = $ipdata['DISTANT']['INFOS_IP']['WHOIS_IP'];

				$dig = [];
				$dnsGetRecord = @dns_get_record($urlNdd);
				if(!empty($dnsGetRecord))
				{
					foreach($dnsGetRecord as $cles => $dns)
					{
						if(!empty($dns['target']))							$content[1] = $dns['target'];
						if(!empty($dns['mname']))							$content[1] = $dns['mname'];
						if(!empty($dns['rname']))							$content[1] = $dns['rname'];
						if(!empty($dns['ip']))								$content[1] = $dns['ip'];
						if(!empty($dns['ipv6']))							$content[1] = $dns['ipv6'];
						if($dns['type'] == 'TXT' AND !empty($dns['txt']))	$content[1] = $dns['txt'];
						if(!empty($dns['cpu']))								$content[1] = $dns['cpu'];
						if(!empty($dns['os']))								$content[1] = $dns['os'];

						$type	= !empty($dns['type'])	? $dns['type']	: '-';
						$host	= !empty($dns['host'])	? $dns['host']	: '-';
						$ttl	= !empty($dns['ttl'])	? $dns['ttl']	: '-';
						$pri	= !empty($dns['pri'])	? $dns['pri']	: '-';

						$ipv4	= (!empty($content[1]) AND isIPv4($content[1]) AND $type == 'A')	? true : false;
						$ipv6	= (!empty($content[1]) AND isIPv6($content[1]) AND $type == 'AAAA')	? true : false;

						foreach(dns() as $cDns => $vDns)
						{
							if($vDns[0] == $type) {
								$tooltip = [
									'Code IANA'		=> $vDns[1],
									'RFC'			=> $vDns[2],
									'Signification'	=> $vDns[4],
									'Fonction'		=> $vDns[5]
								];
							}
						}

						$dig[] = [
								'tooltip'	=> $tooltip,
								'type'		=> $type,
								'hote'		=> $host,
								'valeur'	=> $content[1],
								'ttl'		=> $ttl
						];
					}
				}

				$dig = json_encode($dig);

				try {
					$getCertificatSSL = getCertificatSSL($urlNdd);

					$certificatSsl = json_encode($getCertificatSSL);
				} catch (\Exception $e) {
					$certificatSsl = json_encode(['Certifiat introuvable']);
				}

				try {
					$stmt = $pdo->prepare('INSERT INTO `whois`
						(`uniqid`, `date`, `user_ip`, `user_agent`, `url`, `url_formulaire`, `url_derniere`, `visibilite`, `http_accept_language`, `user_agent_perso`, `http_referer_perso`, `infos_proprietaire`, `infos_bureau_enregistrement`, `infos_status_bureau`, `infos_serveur_whois`, `infos_ns`, `infos_date_creation`, `infos_date_expiration`, `infos_date_maj`, `infos_dnssec`, `infos_adresse_ip`, `infos_emplacement_adresse_ip`, `infos_nom_hote_adresse_ip`, `infos_asn`, `infos_asn_organisation`, `infos_tech_titre_site`, `infos_tech_a_finale`, `infos_tech_code_reponse`, `infos_tech_date_srv`, `infos_tech_encodage`, `infos_tech_x_frame_options`, `infos_tech_nb_cookies`, `infos_tech_nb_liens`, `infos_tech_nb_images`, `infos_tech_nb_lettres`, `infos_tech_cdn`, `infos_tech_verifications_ip`, `whois`, `whois_ip`, `dom`, `dig`, `certificat_ssl`)
						VALUES
						(:uniqid, :date, :user_ip, :user_agent, :url, :url_formulaire, :url_derniere, :visibilite, :http_accept_language, :user_agent_perso, :http_referer_perso, :infos_proprietaire, :infos_bureau_enregistrement, :infos_status_bureau, :infos_serveur_whois, :infos_ns, :infos_date_creation, :infos_date_expiration, :infos_date_maj, :infos_dnssec, :infos_adresse_ip, :infos_emplacement_adresse_ip, :infos_nom_hote_adresse_ip, :infos_asn, :infos_asn_organisation, :infos_tech_titre_site, :infos_tech_a_finale, :infos_tech_code_reponse, :infos_tech_date_srv, :infos_tech_encodage, :infos_tech_x_frame_options, :infos_tech_nb_cookies, :infos_tech_nb_liens, :infos_tech_nb_images, :infos_tech_nb_lettres, :infos_tech_cdn, :infos_tech_verifications_ip, :whois, :whois_ip, :dom, :dig, :certificat_ssl)'
					);

					$stmt->execute([
						'uniqid'						=> (string) $uniqid,
						'date'							=> (string) $dateAnalyse,
						'user_ip'						=> (string) $userIp,
						'user_agent'					=> (string) $userAgent,
						'url'							=> (string) $urlNdd,
						'url_formulaire'				=> (string) $url,
						'url_derniere'					=> (string) $urlDerniere,
						'visibilite'					=> (int) $visibilite,
						'http_accept_language'			=> (string) $httpAcceptLanguage,
						'user_agent_perso'				=> (string) $userAgentPerso,
						'http_referer_perso'			=> (string) $refererPerso,
						'infos_proprietaire'			=> (string) $infosProprietaire,
						'infos_bureau_enregistrement'	=> (string) $infosBureauEnrgst,
						'infos_status_bureau'			=> (string) $infosStatusBureauEnrgst,
						'infos_serveur_whois'			=> (string) $infosServeurWhois,
						'infos_ns'						=> (string) $infosNs,
						'infos_date_creation'			=> (string) $infosDateCreation,
						'infos_date_expiration'			=> (string) $infosDateExpiration,
						'infos_date_maj'				=> (string) $infosDateMaj,
						'infos_dnssec'					=> (bool) $infosDnssec,
						'infos_adresse_ip'				=> (string) $infosAdresseIp,
						'infos_emplacement_adresse_ip'	=> (string) $infosEmplacementAdresseIp,
						'infos_nom_hote_adresse_ip'		=> (string) $infosNomHoteAdresseIp,
						'infos_asn'						=> (string) $infosAsn,
						'infos_asn_organisation'		=> (string) $infosAsnOrganisation,
						'infos_tech_titre_site'			=> (string) $infosTechTitreSite,
						'infos_tech_a_finale'			=> (string) $infosTechAdresseExacte,
						'infos_tech_code_reponse'		=> (int) $infosTechCodeReponse,
						'infos_tech_date_srv'			=> (string) $infosTechDateServeur,
						'infos_tech_encodage'			=> (string) $infosTechEncodage,
						'infos_tech_x_frame_options'	=> (string) $infosTechXFrameOptions,
						'infos_tech_nb_cookies'			=> (int) $infosTechNbCookies,
						'infos_tech_nb_liens'			=> (int) $infosTechNbLiens,
						'infos_tech_nb_images'			=> (int) $infosTechNbImages,
						'infos_tech_nb_lettres'			=> (int) $infosTechNbLettres,
						'infos_tech_cdn'				=> (string) $infosTechCdn,
						'infos_tech_verifications_ip'	=> (string) $infosTechVerificationsIp,
						'whois'							=> (string) $whois,
						'whois_ip'						=> (string) $whoisIp,
						'dom'							=> (string) $dom,
						'dig'							=> (string) $dig,
						'certificat_ssl'				=> (string) $certificatSsl,
					]);

					redirection('https://thisip.pw/analyse-web/'.$uniqid, 3);
				} catch (\PDOException $e) { }
			} catch (\ConnectionException $e) {
				$erreur = 'ConnectionException : '.$e->getMessage();
			} catch (\ServerMismatchException $e) {
				$erreur = 'ServerMismatchException : '.$e->getMessage();
			} catch (\WhoisException $e) {
				$erreur = 'WhoisException : '.$e->getMessage();
			}
		}

		else
			$erreur = 'Adresse est incorrecte';
	}

	else
		$erreur = 'Formulaire incorrect';
}

// Dernières analyses

$dernieresAnalyses[] = '<h3><a href="#dernieresAnalyses" class="text-decoration-none link-dark"><i class="fa-solid fa-magnifying-glass me-2"></i> Les dernières analyses</a></h3>';

try {
	$stmt = $pdo->query('SELECT w.id, w.uniqid, w.url, w.url_formulaire, w.date, w.http_accept_language, w.infos_emplacement_adresse_ip, c.iso2, c.name AS country_name, c.translations FROM whois w JOIN countries c ON c.iso2 COLLATE utf8mb4_unicode_ci = w.http_accept_language COLLATE utf8mb4_unicode_ci WHERE w.visibilite = 1 ORDER BY w.id DESC LIMIT 100');
	$resultats = $stmt->fetchAll();
} catch (\PDOException $e) { }

if(!empty($resultats))
{
	$dernieresAnalyses[] = '<div class="container mt-4 p-0">';

	foreach($resultats as $row)
	{
		$cadenas						= '<span class="curseur align-middle" data-bs-toggle="tooltip" data-bs-title="'.(preg_match('/http:\/\//is', $row['url_formulaire']) ? 'http"><i class="fa-solid fa-lock-open"></i>' : 'https"><i class="fa-solid fa-lock"></i>').'</span>';
		$uniqidDerniereAnalyse			= secuChars($row['uniqid']);
		$urlFormDerniereAnalyse			= secuChars($row['url_formulaire']);
		$dateDerniereAnalyse			= secuChars($row['date']);
		$dateTimeDerniereAnalyse		= !empty($dateDerniereAnalyse) ? strtotime($row['date']) : null;
		$translations					= json_decode($row['translations']);
		$paysNomDerniereAnalyse			= !empty($dateDerniereAnalyse) ? secuChars($translations->fr) : 'inconnu';
		$paysDrapeauDerniereAnalyse		= !empty($dateDerniereAnalyse) ? isoEmoji($row['iso2']) : '🏁';

		$dernieresAnalyses[] = '<div class="row my-2 px-0 py-2 lignes-analyse border rounded">
			<div class="order-2	order-lg-1	col-3	col-lg-1 mt-2 mt-lg-0 text-center">'.$cadenas.'</div>
			<div class="order-1 order-lg-2	col-12	col-lg-7 lien-da"><a href="/analyse-web/'.$uniqidDerniereAnalyse.'" data-bs-toggle="tooltip" data-bs-title="'.$urlFormDerniereAnalyse.'">'.$urlFormDerniereAnalyse.'</a></div>
			<div class="order-3	order-lg-3	col-6	col-lg-3 mt-2 mt-lg-0 text-center curseur" data-bs-toggle="tooltip" data-bs-title="Le '.dateFormat($dateTimeDerniereAnalyse, 'c').'"><time datetime="'.date(DATE_ATOM, $dateTimeDerniereAnalyse).'" class="align-middle">'.temps($dateTimeDerniereAnalyse).'</time></div>
			<div class="order-4	order-lg-4	col-3	col-lg-1 mt-2 mt-lg-0 text-center fs-4 curseur" data-bs-toggle="tooltip" data-bs-title="Pays : '.$paysNomDerniereAnalyse.'">'.$paysDrapeauDerniereAnalyse.'</div>
		</div>';
	}

	$dernieresAnalyses[] = '</div>';
}

else
	$dernieresAnalyses[] = alerte('danger', 'Aucune analyse trouvée', 'mx-auto col-12 col-lg-8 mt-4');

// Analyse

echo '<div class="border rounded" id="whois">
	<h1 class="mb-3 text-center"><a href="/analyse-web"><i class="fa-solid fa-expand"></i> Analyse d’adresses web</a></h1>
	<h4 class="whois-description d-none d-lg-block mx-auto">Obtenez des informations techniques détaillées</h4>
	<h4 class="whois-description d-block d-md-none mx-auto">Informations techniques détaillées</h4>';

	echo !empty($erreur) ? alerte('danger', $erreur) : null;

	if(!empty($uniqid))
	{
		try {
			$stmt = $pdo->prepare('SELECT * FROM whois WHERE uniqid = :uniqid');
			$stmt->execute(['uniqid' => (string) $uniqid]);
			$res = $stmt->fetch();
		} catch (\PDOException $e) { }

		if(!empty($res['id']))
		{
			$spanAucuneInfo			= '<span class="badge bg-danger-subtle border border-danger-subtle text-danger-emphasis rounded-pill">n/a</span>';
			$classDanger			= 'badge bg-danger-subtle border border-danger-subtle text-danger-emphasis rounded-pill';
			$classPrimary			= 'badge bg-primary-subtle border border-primary-subtle text-primary-emphasis rounded-pill';
			$classInfo				= 'badge bg-info-subtle border border-info-subtle text-info-emphasis rounded-pill';
			$classSuccess			= 'badge bg-success-subtle border border-success-subtle text-success-emphasis rounded-pill';
			$titleAI				= ' data-bs-toggle="tooltip" data-bs-title="Chercher les informations ASN dans la base de données RIPE"';
			$titleADT				= ' data-bs-toggle="tooltip" data-bs-title="Chercher le nom de domaine sur DomainTools.com"';

			// Informations diverses

			$uniqid					= secuChars($res['uniqid']);
			$uniqidHtml				= '<span class="'.$classPrimary.'">'.$uniqid.'</span>';
			$dateTemps				= !empty($res['date'])							? strtotime($res['date'])																										: 0;
			$proprietaire			= !empty($res['infos_proprietaire'])			? '<span class="'.$classPrimary.'">'.secuChars($res['infos_proprietaire']).'</span>'											: $spanAucuneInfo;
			$registrar				= !empty($res['infos_bureau_enregistrement'])	? '<span class="'.$classPrimary.'">'.secuChars($res['infos_bureau_enregistrement']).'</span>'									: $spanAucuneInfo;
			$statusRegistrar		= !empty($res['infos_status_bureau'])			? json_decode($res['infos_status_bureau'])																						: null;
			$serveurWhois			= !empty($res['infos_serveur_whois'])			? '<span class="'.$classPrimary.'">'.secuChars($res['infos_serveur_whois']).'</span>'											: $spanAucuneInfo;
			$serveursNs				= !empty($res['infos_ns'])						? json_decode($res['infos_ns'])																									: null;
			$dateCrnT				= !empty($res['infos_date_creation'])			? secuChars(strtotime($res['infos_date_creation']))																				: null;
			$dateCreation			= !empty($dateCrnT)								? '<span class="'.$classPrimary.'"><time datetime="'.date(DATE_ATOM, $dateCrnT).'">'.dateFormat($dateCrnT).'</time></span>'		: $spanAucuneInfo;
			$dateCreationTemps		= !empty($dateCrnT)								? '<span class="'.$classPrimary.'">'.temps($dateCrnT).'</span>'																	: $spanAucuneInfo;
			$dateCreationJrs		= !empty($dateCrnT)								? '<span class="'.$classPrimary.'">'.(floor((time() - $dateCrnT) / (60 * 60 * 24))).' jours</span>'								: $spanAucuneInfo;
			$dateExpT				= !empty($res['infos_date_expiration'])			? secuChars(strtotime($res['infos_date_expiration']))																			: null;
			$dateExpiration			= !empty($dateExpT)								? '<span class="'.$classPrimary.'"><time datetime="'.date(DATE_ATOM, $dateExpT).'">'.dateFormat($dateExpT).'</time></span>'		: $spanAucuneInfo;
			$dateExpirationTemps	= !empty($dateExpT)								? '<span class="'.$classPrimary.'">'.temps($dateExpT).'</span>'																	: $spanAucuneInfo;
			$dateExpirationJrs		= !empty($dateExpT)								? '<span class="'.$classPrimary.'">'.(floor(($dateExpT - time()) / (60 * 60 * 24))).' jours</span>'								: $spanAucuneInfo;
			$dateMajT				= !empty($res['infos_date_maj'])				? secuChars(strtotime($res['infos_date_maj']))																					: null;
			$dateMaj				= !empty($dateMajT)								? '<span class="'.$classPrimary.'"><time datetime="'.date(DATE_ATOM, $dateMajT).'">'.dateFormat($dateMajT).'</time></span>'		: $spanAucuneInfo;
			$dateMajTemps			= !empty($dateMajT)								? '<span class="'.$classPrimary.'">'.temps($dateMajT).'</span>'																	: $spanAucuneInfo;
			$dateMajJrs				= !empty($dateMajT)								? '<span class="'.$classPrimary.'">'.(floor((time() - $dateMajT) / (60 * 60 * 24))).' jours</span>'								: $spanAucuneInfo;
			$dnssec					= ($res['infos_dnssec'] === 1)					? '<span class="'.$classSuccess.'">domaine signé</span>' 																		: '<span class="'.$classDanger.'">domaine non signé</span>';
			$ipNdd					= (isIPv4($res['infos_adresse_ip']) OR isIPv6($res['infos_adresse_ip'])) ? secuChars($res['infos_adresse_ip'])																	: null;
			$ipNddHtml				= !empty($ipNdd)								? '<span title="Adresse IP du serveur" class="'.$classPrimary.'">'.$ipNdd.'</span>'												: $spanAucuneInfo;
			$emplacementIp			= !empty($res['infos_emplacement_adresse_ip'])	? json_decode($res['infos_emplacement_adresse_ip'], true)																		: null;
			$gethostbyaddr			= !empty($ip)									? '<span class="'.$classPrimary.'">'.gethostbyaddr($ip).'</span>'																: '<span class="'.$classDanger.'">n/a</span>';
			$ripeDb					= !empty($res['infos_asn'])						? 'https://apps.db.ripe.net/db-web-ui/query?searchtext='.secuChars($res['infos_asn'])											: null;
			$asnInfo				= !empty($res['infos_asn'])						? '<a href="'.$ripeDb.'" class="'.$classInfo.'"'.$titleAI.' '.$onclick.'>'.secuChars($res['infos_asn']).'</a>'					: $spanAucuneInfo;
			$asnOrg					= !empty($res['infos_asn_organisation'])		? '<span class="'.$classPrimary.'">'.secuChars($res['infos_asn_organisation']).'</span>'										: $spanAucuneInfo;
			$asnDomainTools			= !empty($urlNdd)								? '<a href="https://whois.domaintools.com/'.$urlNdd.'" class="'.$classInfo.'"'.$titleADT.' '.$onclick.'>Domain Tools</a>'		: $spanAucuneInfo;

			// Informations techniques

			$titreSite				= !empty($res['infos_tech_titre_site'])			? '<span class="'.$classPrimary.'">'.secuChars($res['infos_tech_titre_site']).'</span>'											: $spanAucuneInfo;
			$adresseFinale			= !empty($res['infos_tech_a_finale'])			? '<a href="'.secuChars($res['infos_tech_a_finale']).'" class="'.$classInfo.'">'.secuChars($res['infos_tech_a_finale']).'</a>'	: $spanAucuneInfo;
			$codeReponse			= !empty($res['infos_tech_code_reponse'])		? '<span class="'.$classPrimary.'">'.secuChars($res['infos_tech_code_reponse']).'</span>'										: $spanAucuneInfo;
			$dST					= !empty($res['infos_tech_date_srv'])			? secuChars(strtotime($res['infos_tech_date_srv']))																				: null;
			$dateServeur			= !empty($dST)									? '<span class="'.$classPrimary.'"><time datetime="'.date(DATE_ATOM, $dST).'">'.dateFormat($dST, 'c').'</time></span>'			: $spanAucuneInfo;
			$techEncodage			= !empty($res['infos_tech_encodage'])			? '<span class="'.$classPrimary.'">'.secuChars($res['infos_tech_encodage']).'</span>'											: $spanAucuneInfo;
			$xFrameOptions			= !empty($res['infos_tech_x_frame_options'])	? '<span class="'.$classPrimary.'">'.secuChars($res['infos_tech_x_frame_options']).'</span>'									: $spanAucuneInfo;
			$nbCookies				= !empty($res['infos_tech_nb_cookies'])			? '<span class="'.$classInfo.'">'.secuChars($res['infos_tech_nb_cookies']).'</span>'											: $spanAucuneInfo;
			$nbLiens				= !empty($res['infos_tech_nb_liens'])			? '<span class="'.$classInfo.'">'.secuChars($res['infos_tech_nb_liens']).'</span>'												: $spanAucuneInfo;
			$nbImages				= !empty($res['infos_tech_nb_images'])			? '<span class="'.$classInfo.'">'.secuChars($res['infos_tech_nb_images']).'</span>'												: $spanAucuneInfo;
			$estCdn					= !empty($res['infos_tech_cdn'])				? '<span class="'.$classSuccess.'">'.secuChars($res['infos_tech_cdn']).'</span>'												: '<span class="'.$classDanger.'">non</span>';
			$listesIp				= !empty($res['infos_tech_verifications_ip'])	? json_decode($res['infos_tech_verifications_ip'], true)																		: null;

			// Informations brutes

			$whois					= !empty($res['whois'])							? print_r($res['whois'], true) 																	: null;
			$whoisIp				= !empty($res['whois_ip'])						? print_r($res['whois_ip'], true) 																: null;

			$dom					= !empty($res['dom'])							? base64_decode($res['dom'])																	: null;
			$dom					= !empty($dom)									? str_replace("\r\r", "\r", $dom)																: null;
			$dom					= !empty($dom)									? str_replace("\n\n", "\n", $dom)																: 'Les données HTML du serveur n’ont pas pu être récupérées.';

			$cacheDom = $_SERVER['DOCUMENT_ROOT'].'assets/cache/dom/'.$res['id'].'-'.$uniqid;
			if(!file_exists($cacheDom) OR (filemtime($cacheDom) < strtotime('-1 year')))
				cache($cacheDom, $dom);

			else
				$dom = (file_exists($cacheDom) AND filesize($cacheDom) > 0) ? file_get_contents($cacheDom) : 'Erreur';

			$certificatSsl			= !empty($res['certificat_ssl'])					? print_r($res['certificat_ssl'], true) 													: null;
			$codeIso2				= (string) !empty($emplacementIp['Code ISO2'])		? secuChars($emplacementIp['Code ISO2'])													: 'xx';
			$nomDuPays				= (string) !empty($emplacementIp['Pays'])			? secuChars($emplacementIp['Pays'])															: 'Inconnu';
			$visibiliteHtml			= (int) $res['visibilite'];
			$visibiliteIcone		= [0 => '<i class="fa-solid fa-bars-staggered"></i> <span class="d-none d-block">Analyse non listée</span>', 1 => '<i class="fa-solid fa-eye"></i> <span class="d-none d-block">Analyse publique</span>', 2 => '<i class="fa-solid fa-eye-slash"></i> <span class="d-none d-block">Analyse privée</span>'];
			$visibiliteCss			= [0 => 'warning', 1 => 'success', 2 => 'danger'];
			$paysUtilisateur		= (string) !empty($res['http_accept_language'])		? secuChars($res['http_accept_language'])													: 'Inconnu';
			$drapeauUtilisateur		= !empty($res['http_accept_language'])				? isoEmoji($res['http_accept_language'])													: '🏁';
			$ua						= !empty($res['user_agent'])						? secuChars($res['user_agent'])																: null;
			$refererPerso			= !empty($res['http_referer_perso'])				? secuChars($res['http_referer_perso'])														: null;
			$uaPerso				= !empty($res['user_agent_perso'])					? secuChars($res['user_agent_perso'])														: null;
			$userAgent				= (string) (!empty($uaPerso) AND $ua !== $uaPerso)	? $uaPerso																					: (!empty($ua) ? $ua : 'inconnu');
			$urlAnalysee			= !empty($res['url_formulaire'])					? secuChars($res['url_formulaire'])															: null;
			$urlAnalyseeHtml		= !empty($urlAnalysee)								? lien($urlAnalysee)																		: null;
			$urlDerniere			= !empty($res['url_derniere'])						? secuChars($res['url_derniere'])															: null;
			$urlDerniereHtml		= !empty($urlDerniere)								? lien($urlDerniere)																		: null;
			$urlAnalyseeNbLettres	= mb_strlen(strtr($res['url_formulaire'], ['http://' => '', 'https://' => '']));
			$textePartage			= '[ThisIP.pw] Analyse du site : '.$urlNdd.' - '.'https://thisip.pw/analyse-web/'.$uniqid;

			echo '<div class="row mb-4">
				<div class="col-12">
					<div class="d-flex flex-wrap flex-lg-nowrap mb-4">
						<div class="col-12 col-lg-8 text-start mb-4 mb-lg-0"><h1 class="d-inline align-middle mb-0"><a href="https://thisip.pw/analyse-web/'.$uniqid.'" class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-bs-toggle="tooltip" data-bs-title="Nom de domaine">'.$urlNdd.'</a></h1> <span class="badge text-bg-'.$visibiliteCss[$visibiliteHtml].' align-bottom ms-4 fs-5 curseur" data-bs-toggle="tooltip" data-bs-title="Analyse '.strip_tags($visibiliteIcone[$visibiliteHtml]).'">'.$visibiliteIcone[$visibiliteHtml].'</span></div>
						<div class="col-12 col-lg-4 text-center text-lg-end">
							<button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Outils distants"><i class="fa-solid fa-magnifying-glass"></i> Outils distants</button>
							<ul class="dropdown-menu">
								<li class="dropdown-header">ThisIP</li>

								<li><a href="https://thisip.pw/ip/'.$ipNdd.'" class="dropdown-item">ThisIP.pw (IP)</a></li>
								<li><a href="https://thisip.pw/analyse-web/'.$uniqid.'" class="dropdown-item">ThisIP.pw (Domaine)</a></li>

								<li><hr class="dropdown-divider"></li>

								<li class="dropdown-header">Nom de domaine</li>

								<li><a href="https://www.virustotal.com/#/domain/'.$urlNdd.'" rel="nofollow" class="dropdown-item" '.$onclick.'>VirusTotal</a></li>
								<li><a href="https://securitytrails.com/domain/'.$urlNdd.'/dns" rel="nofollow" class="dropdown-item" '.$onclick.'>SecurityTrails</a></li>
								<li><a href="http://whois.domaintools.com/'.$urlNdd.'" rel="nofollow" class="dropdown-item" '.$onclick.'>DomainTools</a></li>
								<li><a href="https://who.is/whois/'.$urlNdd.'" rel="nofollow" class="dropdown-item" '.$onclick.'>Who.is</a></li>
								<li><a href="https://crt.sh/?q='.$urlNdd.'" rel="nofollow" class="dropdown-item" '.$onclick.'>crt.sh</a></li>

								<li><hr class="dropdown-divider"></li>

								<li class="dropdown-header">URL analysée</li>

								<li><a href="https://transparencyreport.google.com/safe-browsing/search?url='.$urlAnalysee.'/" rel="nofollow" class="dropdown-item" '.$onclick.'>Google Safe Browsing</a></li>
								<li><a href="https://web.archive.org/web/*/'.$urlAnalysee.'/" rel="nofollow" class="dropdown-item" '.$onclick.'>Archive.org</a></li>

								<li><hr class="dropdown-divider"></li>

								<li class="dropdown-header">Adresse IP</li>

								<li><a href="https://www.virustotal.com/#/ip-address/'.$ipNdd.'" rel="nofollow" class="dropdown-item" '.$onclick.'>VirusTotal</a></li>
								<li><a href="https://securitytrails.com/list/ip/'.$ipNdd.'" rel="nofollow" class="dropdown-item" '.$onclick.'>SecurityTrails</a></li>
								<li><a href="https://community.riskiq.com/search/'.$ipNdd.'" rel="nofollow" class="dropdown-item" '.$onclick.'>RiskIQ</a></li>
								<li><a href="http://whois.domaintools.com/'.$ipNdd.'" rel="nofollow" class="dropdown-item" '.$onclick.'>DomainTools</a></li>
								<li><a href="https://who.is/whois/'.$ipNdd.'" rel="nofollow" class="dropdown-item" '.$onclick.'>Who.is</a></li>
								<li><a href="https://www.censys.io/ipv4/'.$ipNdd.'" rel="nofollow" class="dropdown-item" '.$onclick.'>Censys</a></li>
							</ul>
							<button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPartage"><span data-bs-toggle="tooltip" data-bs-title="Partager l’analyse"><i class="fa-solid fa-arrow-up-from-bracket"></i></span></button>

							<div class="modal fade" id="modalPartage" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered">
									<div class="modal-content">
										<div class="modal-header">
											<h2 class="modal-title fs-5" id="modalLabel">Partager l’analyse</h2>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer" title="Fermer"></button>
										</div>
										<div class="modal-body">
											<div class="d-flex align-content-start flex-wrap text-center">
												<div class="col col-lg-2 mx-auto" data-bs-toggle="tooltip" data-bs-title="Partager sur Twitter">
													<button class="btn px-1 boutonPartage" data-location="https://twitter.com/intent/tweet?text='.urlencode('[ThisIP.pw] Analyse du site : '.$urlNdd.' - https://thisip.pw/analyse-web/'.$uniqid).'"><i class="fa-brands fa-square-x-twitter fa-3x"></i></button><br>
													<span>Twitter</span>
												</div>
												<div class="col col-lg-2 mx-auto" data-bs-toggle="tooltip" data-bs-title="Partager sur Facebook">
													<button class="btn px-1 boutonPartage" data-location="https://www.facebook.com/sharer/sharer.php?u='.urlencode('[ThisIP.pw] Analyse du site : '.$urlNdd.' - https://thisip.pw/analyse-web/'.$uniqid).'"><i class="fa-brands fa-square-facebook fa-3x"></i></button><br>
													<span>Facebook</span>
												</div>
												<div class="col col-lg-2 mx-auto" data-bs-toggle="tooltip" data-bs-title="Partager sur Reddit">
													<button class="btn px-1 boutonPartage" data-location="https://reddit.com/submit?url='.urlencode('[ThisIP.pw] Analyse du site : '.$urlNdd.' - https://thisip.pw/analyse-web/'.$uniqid).'"><i class="fa-brands fa-square-reddit fa-3x"></i></button><br>
													<span>Reddit</span>
												</div>
												<div class="col col-lg-2 mx-auto" data-bs-toggle="tooltip" data-bs-title="Partager sur Telegram">
													<button class="btn px-1 boutonPartage" data-location="https://t.me/share/url?url='.urlencode('[ThisIP.pw] Analyse du site : '.$urlNdd.' - https://thisip.pw/analyse-web/'.$uniqid).'"><i class="fa-brands fa-telegram fa-3x"></i></button><br>
													<span>Telegram</span>
												</div>
												<div class="col col-lg-2 mx-auto" data-bs-toggle="tooltip" data-bs-title="Partager sur Pinterest">
													<button class="btn px-1 boutonPartage" data-location="http://pinterest.com/pin/create/link/?url='.urlencode('[ThisIP.pw] Analyse du site : '.$urlNdd.' - https://thisip.pw/analyse-web/'.$uniqid).'"><i class="fa-brands fa-square-pinterest fa-3x"></i></button><br>
													<span>Pinterest</span>
												</div>
												<div class="col col-lg-2 mx-auto" data-bs-toggle="tooltip" data-bs-title="Partager sur Whatsapp">
													<button class="btn px-1 boutonPartage" data-location="https://api.whatsapp.com/send?text='.urlencode('[ThisIP.pw] Analyse du site : '.$urlNdd.' - https://thisip.pw/analyse-web/'.$uniqid).'"><i class="fa-brands fa-square-whatsapp fa-3x"></i></button><br>
													<span>Whatsapp</span>
												</div>
											</div>
										</div>
										<div class="modal-footer align-content-center">
											<div class="input-group">
												<span class="input-group-text">Lien</span>
												<input type="text" value="https://thisip.pw/analyse-web/'.$uniqid.'" class="form-control form-control curseur" id="urlACopie">
												<button class="btn btn-outline-success" id="copyButton" data-clipboard-target="#urlACopie" data-bs-toggle="tooltip" data-bs-title="Copier vers le presse-papiers"><i class="fa-regular fa-clipboard"></i></button>
											</div>
										</div>
									</div>
								</div>
							</div>

							<script>
							document.body.addEventListener("click", function(e) {
								let buttonLink = e.target.closest(".boutonPartage");
								if (buttonLink) {
									e.preventDefault();
									let loc = buttonLink.getAttribute("data-location");
									let target = buttonLink.getAttribute("data-target");

									window.open(loc, "_blank", "noopener, noreferrer");
								}
							});

							document.addEventListener("DOMContentLoaded", function() {
								let clipboard = new ClipboardJS("#copyButton");

								clipboard.on("success", function(e) {
									let tooltipTitle = "Copié !";
									let existingTooltip = bootstrap.Tooltip.getInstance(e.trigger);

									if (!existingTooltip) {
										existingTooltip = new bootstrap.Tooltip(e.trigger, {
											title: tooltipTitle,
											trigger: "manual"
										});
									} else {
										existingTooltip.setContent({ ".tooltip-inner": tooltipTitle });
									}

									e.clearSelection();
								});

								clipboard.on("error", function(e) {
									console.error("Erreur lors de la copie");
								});
							});
							</script>
						</div>
					</div>

					<div class="d-flex col-12 fs-4"><span class="mb-0 me-3" data-bs-toggle="tooltip" data-bs-title="IP du nom de domaine">'.$ipNdd.'</span> <span class="mb-0 curseur" data-bs-toggle="tooltip" data-bs-title="Localisation de l’adresse IP du seveur : '.$nomDuPays.'">'.isoEmoji($codeIso2).'</span></div>
				</div>
			</div>

			<div class="row mb-4">
				<div class="mb-3"><span class="fw-bold">User Agent</span> :<br class="d-block d-lg-none"><span class="whoisUserAgent curseur rounded ms-lg-2 p-0 p-lg-2" data-bs-toggle="tooltip" data-bs-title="User Agent utilisé lors de l’analyse">'.$userAgent.'</span></div>
				'.(!empty($refererPerso) ? '<div class="mb-3"><span class="fw-bold">Referer Personnalisé</span> :<br class="d-block d-lg-none"><span class="refererPerso curseur rounded ms-lg-2 p-0 p-lg-2" data-bs-toggle="tooltip" data-bs-title="Referer Personnalisé utilisé lors de l’analyse">'.$refererPerso.'</span></div>' : null).'
				<div class="mb-3 text-truncate"><span class="fw-bold">URL</span> :<br class="d-block d-lg-none">'.$urlAnalyseeHtml.btnCopie($urlAnalysee).'</div>
				'.((!empty($urlDerniere) AND $urlAnalysee !== $urlDerniere) ? '<div class="mb-3 text-truncate"><span class="fw-bold">URL Effective</span> :<br class="d-block d-lg-none">'.$urlDerniereHtml.btnCopie($urlDerniere).'</div>' : null).'
				'.($urlAnalyseeNbLettres >= 42 ? '<div class="d-block d-lg-none"><p style="font-size: .85rem;" class="text-start">> <a data-bs-toggle="collapse" href="#collapseUrlAnalysee" role="button" aria-expanded="false" aria-controls="collapseUrlAnalysee">Afficher l’URL entière</a></p>
				<div class="collapse mb-3" id="collapseUrlAnalysee"><div class="card card-body">'.$urlAnalysee.'</div></div></div>' : null).'
				<p class="mb-0">Analyse réalisée le <span class="fw-bold curseur" data-bs-toggle="tooltip" data-bs-title="Date de l’analyse"><time datetime="'.date(DATE_ATOM, $dateTemps).'">'.dateFormat($dateTemps, 'c').'</time></span> via formulaire — Analyse réalisée depuis <span class="curseur" data-bs-toggle="tooltip" data-bs-title="'.$paysUtilisateur.'">'.$drapeauUtilisateur.'</span></p>
			</div>

			<div>
				<div class="btn-group d-flex flex-row justify-content-center mb-4 px-0" id="pills-tab" role="tablist">
					<a href="#pills-home-tab"		class="btn btn-outline-primary active"	id="pills-home-tab"		data-bs-toggle="tab" data-bs-target="#pills-home"		role="tab" aria-controls="pills-home"		aria-selected="true">	<i class="fa-solid fa-list-ul me-lg-2"></i><span class="d-none d-lg-inline-block">Résultats</span></a>
					<a href="#pills-whois-tab"		class="btn btn-outline-primary"			id="pills-whois-tab"	data-bs-toggle="tab" data-bs-target="#pills-whois"		role="tab" aria-controls="pills-whois"		aria-selected="false"	><i class="fa-solid fa-globe me-lg-2"></i><span class="d-none d-lg-inline-block">Whois</span></a>
					<a href="#pills-whois-ip-tab"	class="btn btn-outline-primary"			id="pills-whois-ip-tab"	data-bs-toggle="tab" data-bs-target="#pills-whois-ip"	role="tab" aria-controls="pills-whois-ip"	aria-selected="false"	><i class="fa-solid fa-network-wired me-lg-2"></i><span class="d-none d-lg-inline-block">Whois IP</span></a>
					<a href="#pills-dig-tab"		class="btn btn-outline-primary"			id="pills-dig-tab"		data-bs-toggle="tab" data-bs-target="#pills-dig"		role="tab" aria-controls="pills-dig"		aria-selected="false"	><i class="fa-solid fa-robot me-lg-2"></i><span class="d-none d-lg-inline-block">DiG</span></a>
					<a href="#pills-liens-tab"		class="btn btn-outline-primary"			id="pills-liens-tab"	data-bs-toggle="tab" data-bs-target="#pills-liens"		role="tab" aria-controls="pills-liens"		aria-selected="false"	><i class="fa-solid fa-link me-lg-2"></i><span class="d-none d-lg-inline-block">Liens</span></a>
					<a href="#pills-dom-tab"		class="btn btn-outline-primary"			id="pills-dom-tab"		data-bs-toggle="tab" data-bs-target="#pills-dom"		role="tab" aria-controls="pills-dom"		aria-selected="false"	><i class="fa-solid fa-code me-lg-2"></i><span class="d-none d-lg-inline-block">Dom</span></a>
					<a href="#pills-ssl-tab"		class="btn btn-outline-primary"			id="pills-ssl-tab"		data-bs-toggle="tab" data-bs-target="#pills-ssl"		role="tab" aria-controls="pills-ssl"		aria-selected="false"	><i class="fa-brands fa-expeditedssl me-lg-2"></i><span class="d-none d-lg-inline-block">Certificat SSL</span></a>
					<a href="#pills-da-tab"			class="btn btn-outline-success"			id="pills-da-tab"		data-bs-toggle="tab" data-bs-target="#pills-da"			role="tab" aria-controls="pills-da"			aria-selected="false"	><i class="fa-solid fa-magnifying-glass me-lg-2"></i><span class="d-none d-lg-inline-block">Analyses récentes</span></a>
				</div>

				<div class="col-12 col-lg-11 mx-auto">
					<div class="tab-content" id="pills-tabContent">
						<div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="-1">
							<h3 class="mb-4"><i class="fa-solid fa-list-ul"></i> Résultats</h3>

							<div class="row border bg-light mb-1" title="Propriétaire du nom de domaine">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Propriétaire</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start text-truncate">'.$proprietaire.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Bureau d’enregistrement du nom de domaine">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Bureau d’enregistrement</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start text-truncate">'.$registrar.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Status du bureau d’enregistrement du nom de domaine">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Status du bureau d’enregistrement (Protocole <abbr title="Extensible Provisioning Protocol">EPP</abbr>) '.aide('Extensible Provisioning Protocol (EPP) ou protocole d’avitaillement extensible est un protocole informatique basé sur XML pour des échanges entre registres et registrars. Cette méthode, unifiée et commune entre les acteurs, permet de faire toutes les opérations liées aux domaines de façon sécurisée.').'</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">';

									if(!empty($statusRegistrar) AND is_array($statusRegistrar))
									{
										$statusArray = [
											mb_strtolower('clientDeleteProhibited')			=> 'Le domaine ne peut pas être supprimé par le client. Ce statut est généralement appliqué pour protéger le domaine contre une suppression accidentelle ou malveillante.',
											mb_strtolower('clientHold')						=> 'Le domaine est inactif et ne sera pas résolu dans le DNS. Cela peut être appliqué par le client, souvent en cas de non-paiement ou de litige.',
											mb_strtolower('clientRenewProhibited')			=> 'Le domaine ne peut pas être renouvelé. Utilisé pour empêcher le renouvellement du domaine.',
											mb_strtolower('clientTransferProhibited')		=> 'Le domaine ne peut pas être transféré à un autre Registrar. Appliqué pour protéger le domaine contre les transferts non autorisés.',
											mb_strtolower('clientUpdateProhibited')			=> 'Les informations du domaine ne peuvent pas être mises à jour. Cela empêche toute modification des informations associées au domaine.',
											mb_strtolower('serverDeleteProhibited')			=> 'Le domaine ne peut pas être supprimé par le serveur. Cela protège le domaine contre une suppression involontaire ou malveillante.',
											mb_strtolower('serverRecoverProhibited')		=> 'La récupération (ou restauration) automatique du domaine par le serveur est désactivée ou interdite.',
											mb_strtolower('serverHold')						=> 'Le domaine est inactif au niveau du serveur et ne sera pas résolu dans le DNS.',
											mb_strtolower('serverRenewProhibited')			=> 'Le domaine ne peut pas être renouvelé au niveau du serveur.',
											mb_strtolower('serverTransferProhibited')		=> 'Le domaine ne peut pas être transféré à un autre Registrar au niveau du serveur.',
											mb_strtolower('serverUpdateProhibited')			=> 'Les informations du domaine ne peuvent pas être mises à jour au niveau du serveur.',
											mb_strtolower('pendingCreate')					=> 'Le domaine est en attente de création. Ce statut est temporaire et est utilisé lors du processus de création d’un domaine.',
											mb_strtolower('pendingDelete')					=> 'Le domaine est en attente de suppression. Ce statut indique qu’une demande de suppression a été initiée.',
											mb_strtolower('pendingRenew')					=> 'Le domaine est en attente de renouvellement.',
											mb_strtolower('pendingTransfer')				=> 'Le domaine est en attente de transfert vers un autre Registrar.',
											mb_strtolower('pendingUpdate')					=> 'Le domaine est en attente de mise à jour des informations.',
											mb_strtolower('registered until expiry date')	=> 'Le domaine est enregistré jusqu’à la date d’expiration.',
											mb_strtolower('active')							=> 'Le domaine est actif et opérationnel. Il n’est soumis à aucune restriction ou blocage spécifique, ce qui signifie qu’il peut être mis à jour, transféré, renouvelé ou supprimé sans aucune limitation supplémentaire.',
											mb_strtolower('ok')								=> 'Le domaine est actif et opérationnel. Il n’est soumis à aucune restriction ou blocage spécifique, ce qui signifie qu’il peut être mis à jour, transféré, renouvelé ou supprimé sans aucune limitation supplémentaire.',
										];

										foreach($statusRegistrar as $s)
										{
											$s = str_replace('.', '', $s);

											$sR[] = '<span class="'.$classPrimary.'">'.secuChars($s).'</span>'.(!empty($statusArray[$s]) ? aide($statusArray[$s], ' ms-1') : null);
										}
									}

									else
										$sR[] = $spanAucuneInfo;

										echo implode('<br>', $sR);

								echo '</div>
							</div>
							<div class="row border bg-light mb-1" title="Serveur Whois">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Serveur Whois</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start text-truncate">'.$serveurWhois.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Serveur'.s($serveursNs).' de nom">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Serveur'.s($serveursNs).' de nom</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">';

									if(!empty($serveursNs) AND is_array($serveursNs))
									{
										foreach($serveursNs as $s)
											$sNS[] = '<span class="'.$classPrimary.'">'.secuChars($s).'</span>';
									}

									else
										$sNS[] = $spanAucuneInfo;

									echo implode('<br>', $sNS);

								echo '</div>
							</div>
							<div class="row border bg-light mb-1" title="Date de création du nom de domaine">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Date de création</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">'.$dateCreation.' / '.$dateCreationTemps.' / '.$dateCreationJrs.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Date d’expiration du nom de domaine">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Date d’expiration</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">'.$dateExpiration.' / '.$dateExpirationTemps.' / '.$dateExpirationJrs.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Date de mise à jour du nom de domaine">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Date de mise à jour</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">'.$dateMaj.' / '.$dateMajTemps.' / '.$dateMajJrs.'</div>
							</div>
							<div class="row border bg-light mb-1" title="DNSSEC du nom de domaine">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">
									<abbr title="Domain Name System Security Extensions">DNSSEC</abbr>
									'.aide('DNSSEC (« Domain Name System Security Extensions ») est un protocole standardisé par l’IETF permettant de résoudre certains problèmes de sécurité liés au protocole DNS. Les spécifications sont publiées dans la RFC 4033 et les suivantes (une version antérieure de DNSSEC n’a eu aucun succès).').'
								</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">'.$dnssec.'</div>
							</div>
							<div class="row border bg-light mb-1">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Adresse IP du serveur</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">'.$ipNddHtml.' <a href="https://thisip.pw/ip/'.$ipNdd.'" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-title="Analyser l’adresse IP du serveur"><i class="fa-solid fa-magnifying-glass fa-xs"></i></a></div>
							</div>
							<div class="row border bg-light mb-1" title="Emplacement géographique de l’adresse IP">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Emplacement géographique de l’adresse IP</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">';

									if(is_array($emplacementIp) AND (!empty($emplacementIp['Continent']) OR !empty($emplacementIp['Sous continent']) OR !empty($emplacementIp['Pays'])))
									{
										echo !empty($emplacementIp['Continent'])		? '<span class="'.$classPrimary.' curseur" data-bs-toggle="tooltip" data-bs-title="'.secuChars($emplacementIp['Continent']).'">'.secuChars($emplacementIp['Continent']).'</span>'				: null;
										echo !empty($emplacementIp['Sous continent'])	? ' / <span class="'.$classPrimary.' curseur" data-bs-toggle="tooltip" data-bs-title="'.secuChars($emplacementIp['Sous continent']).'">'.secuChars($emplacementIp['Sous continent']).'</span>'	: null;
										echo !empty($emplacementIp['Pays'])				? ' / <span class="'.$classPrimary.' curseur"'.(!empty($emplacementIp['Pays native']) ? ' data-bs-toggle="tooltip" data-bs-title="'.secuChars($emplacementIp['Pays native']).(!empty($emplacementIp['Code ISO3']) ? ' / '.secuChars($emplacementIp['Code ISO3']) : null) : null).'">'.secuChars($emplacementIp['Pays']).'</span>' : null;
										echo !empty($emplacementIp['Code ISO2'])		? ' <span class="'.$classPrimary.' curseur" data-bs-toggle="tooltip" data-bs-title="'.secuChars($emplacementIp['Code ISO2']).'">'.secuChars($emplacementIp['Code ISO2']).'</span>'				: null;
										echo !empty($emplacementIp['Capitale'])			? ' / <span class="'.$classPrimary.' curseur" data-bs-toggle="tooltip" data-bs-title="'.secuChars($emplacementIp['Capitale']).'">'.secuChars($emplacementIp['Capitale']).'</span>'				: null;
										echo !empty($emplacementIp['Ville'])			? ' / <span class="'.$classPrimary.' curseur" data-bs-toggle="tooltip" data-bs-title="'.secuChars($emplacementIp['Ville']).'">'.secuChars($emplacementIp['Ville']).'</span>'					: null;
										echo !empty($emplacementIp['Code postal'])		? ' / <span class="'.$classPrimary.' curseur" data-bs-toggle="tooltip" data-bs-title="'.secuChars($emplacementIp['Code postal']).'">'.secuChars($emplacementIp['Code postal']).'</span>'		: null;
									}

									else
										echo $spanAucuneInfo;

								echo '</div>
							</div>
							<div class="row border bg-light mb-1" title="Nom d’hôte correspondant à l’adresse IP">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Nom d’hôte correspondant à l’adresse IP</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">'.$gethostbyaddr.'</div>
							</div>
							<div class="row border bg-light mb-1" title="ASN">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">
									<abbr title="Autonomous System Number">ASN</abbr>
									'.aide('Un Autonomous System (abrégé AS), ou système autonome, est un ensemble de réseaux informatiques IP intégrés à Internet et dont la politique de routage interne (routes à choisir en priorité, filtrage des annonces) est cohérente.').'
								</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">'.$asnInfo.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Organisation ASN">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Organisation ASN</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">'.$asnOrg.' '.$asnDomainTools.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Titre de l’adresse analysée">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Titre du site</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 py-1 p-lg-2 text-center text-lg-start text-truncate">'.$titreSite.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Adresse finale">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Adresse finale</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 py-1 p-lg-2 text-center text-lg-start text-truncate">'.$adresseFinale.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Code de réponse">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Code de réponse</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 py-1 p-lg-2 text-center text-lg-start">'.$codeReponse.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Date et heure du serveur">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Date et heure du serveur</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 py-1 p-lg-2 text-center text-lg-start">'.$dateServeur.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Type d’encodage de l’adresse">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Type d’encodage de l’adresse</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 py-1 p-lg-2 text-center text-lg-start">'.$techEncodage.'</div>
							</div>
							<div class="row border bg-light mb-1" title="X-Frame-Options">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">
									<span>X-Frame-Options</span>
									'.aide('L’en-tête de réponse HTTP X-Frame-Options peut être utilisé afin d’indiquer si un navigateur devrait être autorisé à afficher une page au sein d’un élément « frame », « iframe », « embed » ou « object ». Les sites peuvent utiliser cet en-tête afin d’éviter les attaques de clickjacking (ou « détournement de clic ») pour s’assurer que leur contenu ne soit pas embarqué dans d’autres sites.').'
								</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 p-lg-2 text-center text-lg-start">'.$xFrameOptions.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Nombre de cookies">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Nombre de cookies</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 py-1 p-lg-2 text-center text-lg-start">'.$nbCookies.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Nombre de liens">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Nombre de liens</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 py-1 p-lg-2 text-center text-lg-start">'.$nbLiens.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Nombre d’images">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">Nombre d’images</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 py-1 p-lg-2 text-center text-lg-start">'.$nbImages.'</div>
							</div>
							<div class="row border bg-light mb-1" title="L’adresse est-elle sur un CDN ?">
								<div class="col-12 col-lg-6 pb-1 pt-2 p-lg-2 text-center text-lg-end fw-bold">L’adresse est-elle sur un CDN ?</div>
								<div class="col-12 col-lg-6 pb-2 pt-1 py-1 p-lg-2 text-center text-lg-start">'.$estCdn.'</div>
							</div>
							<div class="row border bg-light mb-1" title="Vérification de l’IP dans les listes de sécurité, nœuds Bitcoin, etc.">
								<div class="col-12 p-2 text-center text-break">
									<p class="fw-bold">Vérification de l’IP</p>';

									if(!empty($listesIp))
									{
										$listesArray = [
											'bitcoin_nodes.ipset'	=> 'https://iplists.firehol.org/?ipset=bitcoin_nodes|Liste des nœuds Bitcoin connectés, dans le monde entier.',
											'firehol_level1.netset'	=> 'https://iplists.firehol.org/?ipset=firehol_level1|Liste noire des adresses IP indésirables offrant une protection maximale avec un minimum de faux positifs.',
											'firehol_level2.netset'	=> 'https://iplists.firehol.org/?ipset=firehol_level2|Liste noire des adresses IP indésirables offrant une protection maximale avec un minimum de faux positifs.',
											'firehol_level3.netset'	=> 'https://iplists.firehol.org/?ipset=firehol_level3|Liste noire des adresses IP indésirables offrant une protection maximale avec un minimum de faux positifs.',
											'firehol_level4.netset'	=> 'https://iplists.firehol.org/?ipset=firehol_level4|Liste noire des adresses IP indésirables offrant une protection maximale avec un minimum de faux positifs.',
											'stopforumspam.ipset'	=> 'https://iplists.firehol.org/?ipset=stopforumspam|Liste noire des adresses IP par StopForumSpam.com',
											'Tor_ip_list_ALL.csv'	=> 'https://torstatus.rueckgr.at/|Liste des adresses IP des de tous les points de sortie Tor - TorProject.org',
											'Tor_ip_list_EXIT.csv'	=> 'https://torstatus.rueckgr.at/|Liste des adresses IP des nœuds de sortie du serveur Tor - TorProject.org'
										];

										foreach($listesIp as $cListe => $vListe)
										{
											$e = explode('|', $listesArray[$cListe]);

											$lIP[] = '<a href="'.$e[0].'" class="text-decoration-none '.($vListe ? $classSuccess : $classDanger).'" data-bs-toggle="tooltip" data-bs-title="L’IP '.($vListe ? 'est' : 'n’est pas').' présente dans la liste '.$cListe.'">'.$cListe.'</a>'.aide($e[1], ' mx-1');
										}

										echo implode('<br class="d-block d-lg-none">', $lIP);
									}

									else
										echo 'non disponible';

								echo '</div>
							</div>
						</div>

						<div class="tab-pane fade" id="pills-whois" role="tabpanel" aria-labelledby="pills-whois-tab" tabindex="-1">
							<h3 class="mb-4"><i class="fa-solid fa-globe"></i> Whois</h3>';

							echo !empty($whois) ? '<pre class="border rounded bg-light mb-4 p-3 p-lg-4">'.$whois.'</pre>'.modalTexte($whois, 'Whois') : alerte('danger', 'non disponible');

							echo '<div class="border-start border-success border-5 mt-4 ps-3">
								<p class="ms-2"><strong>Whois</strong> désigne un protocole ainsi que le nom de services de recherche reposant sur ce protocole. Ces services sont proposés par divers registres Internet, tels que les Registres Internet régionaux (RIR) ou les registres de noms de domaine.</p>
								<p class="ms-2">Ils permettent d’accéder à des informations relatives à une adresse IP ou à un nom de domaine. Ces données peuvent être utilisées dans des contextes variés, comme la coordination entre ingénieurs réseaux pour résoudre des problèmes techniques, ou encore la recherche du propriétaire d’un nom de domaine par une entreprise souhaitant l’acquérir.</p>
							</div>
						</div>

						<div class="tab-pane fade" id="pills-whois-ip" role="tabpanel" aria-labelledby="pills-whois-ip-tab" tabindex="-1">
							<h3 class="mb-4"><i class="fa-solid fa-network-wired"></i> Whois IP</h3>';

							echo !empty($whoisIp) ? '<pre class="border rounded bg-light mb-4 p-3 p-lg-4">'.$whoisIp.'</pre>'.modalTexte($whoisIp, 'Whois IP') : alerte('danger', 'non disponible');

							echo '<div class="border-start border-success border-5 mt-4 ps-3">
								<p class="ms-2"><strong>Whois</strong> est un protocole utilisé pour interroger des bases de données qui contiennent des informations sur l’enregistrement d’adresses IP. Lorsqu’une recherche Whois est effectuée pour une adresse IP, elle fournit des détails tels que le nom de l’organisation propriétaire de l’adresse IP, le pays d’origine, les coordonnées administratives et techniques, ainsi que les dates d’enregistrement et d’expiration associées à cette adresse.</p>
								<p class="ms-2">Le Whois est un outil précieux pour identifier les entités responsables des adresses IP sur Internet, souvent utilisé dans des contextes de sécurité, de recherche ou de résolution de problèmes techniques.</p>
							</div>
						</div>

						<div class="tab-pane fade" id="pills-dig" role="tabpanel" aria-labelledby="pills-dig-tab" tabindex="-1">
							<h3 class="mb-4"><i class="fa-solid fa-robot"></i> DiG</h3>';

							$digArray = json_decode($res['dig'], true);

							if(!empty($digArray))
							{
								echo '<div id="digAnalyse">
									<div class="row border rounded-top mb-1">
										<div class="col-6 col-lg-1 p-1 p-lg-3 fs-5 fw-bold">Type</div>
										<div class="col-6 col-lg-5 p-1 p-lg-3 fs-5 fw-bold">Nom</div>
										<div class="col-6 col-lg-4 p-1 p-lg-3 fs-5 fw-bold">-</div>
										<div class="col-6 col-lg-2 p-1 p-lg-3 fs-5 text-center fw-bold"><abbr title="Time To Live (durée de vie)">TTL</abbr></div>
									</div>';

									foreach($digArray as $digCle => $digValeur)
									{
										$digVal	= !empty($digValeur['valeur']) ? $digValeur['valeur'] : null;
										$ipv4	= (!empty($digVal) AND isIPv4($digVal) AND $digValeur['type'] == 'A')		? true : false;
										$ipv6	= (!empty($digVal) AND isIPv6($digVal) AND $digValeur['type'] == 'AAAA')	? true : false;

										echo '<div class="row border bg-light mb-1">
											<div class="col-6 col-lg-1 p-1 p-lg-3 text-break">
												<span class="curseur text-decoration-underline fw-bold" data-bs-toggle="tooltip" data-bs-title="';

													echo (!empty($digValeur['tooltip']['Code IANA']) ? 'Code IANA : '.$digValeur['tooltip']['Code IANA'].'<br><br>' : null).
													(!empty($digValeur['tooltip']['RFC']) ? 'RFC : '.$digValeur['tooltip']['RFC'].'<br><br>' : null).
													(!empty($digValeur['tooltip']['Signification']) ? 'Signification : '.$digValeur['tooltip']['Signification'].'<br><br>' : null).
													(!empty($digValeur['tooltip']['Fonction']) ? $digValeur['tooltip']['Fonction'] : null);

												echo '">'.$digValeur['type'].'</span>
											</div>
											<div class="col-6 col-lg-5 p-1 p-lg-3 text-break" title="Hôte">'.$digValeur['hote'].'</div>
											<div class="col-6 col-lg-4 p-1 p-lg-3 text-break" title="Valeur">'.(($ipv4 OR $ipv6) ? '<a href="https://thisip.pw/ip/'.$digVal.'" class="badge bg-success">'.$digVal.'</a>' : $digVal).'</div>
											<div class="col-6 col-lg-2 p-1 p-lg-3 text-center text-break" title="TTL"><span class="border rounded curseur bg-light px-1 py-0 curseur">'.$digValeur['ttl'].'</span></div>
										</div>';
									}

								echo '</div>';
							}

							else
								echo alerte('danger', 'Aucun DNS trouvé');

							echo '<div class="border-start border-success border-5 mb-3 mt-4 ps-3">
								<p class="ms-2"><strong>dig</strong> (de l’anglais domain information groper) est un client de type Unix en ligne de commande, permettant d’interroger des serveurs DNS. Sous Linux, il est disponible dans le package dnsutils. Sous Fedora, le package est bind-utils.</p>

								<p class="mb-0 ms-2">Source : <a href="https://fr.wikipedia.org/wiki/Dig_(programme_informatique)">Wikipédia</a></p>
							</div>

							<p class="mb-0 text-center"><a href="https://upload.wikimedia.org/wikipedia/commons/5/59/All_active_dns_record_types.png" class="btn btn-outline-success" data-fancybox="gallerie"><i class="fa-regular fa-image me-2"></i> Représentation graphique de tous les types d’enregistrements DNS</a></p>
						</div>

						<div class="tab-pane fade" id="pills-liens" role="tabpanel" aria-labelledby="pills-liens-tab" tabindex="0">
							<h3 class="mb-4"><i class="fa-solid fa-link"></i> Liens de la page</h3>';

							if(!empty($dom))
							{
								$domLiens = new DOMDocument();
								@$domLiens->loadHTML($dom);
								$liens = $domLiens->getElementsByTagName('a');

								$resLiens = [];
								foreach($liens as $lien)
								{
									$href = $lien->getAttribute('href');
									if(!empty($href) AND mb_strlen($href) > 4)
										$resLiens[] = $href;
								}

								$resLiens = array_filter($resLiens);
								$resLiens = array_unique($resLiens);
								if(!empty($resLiens))
								{
									function isLien(?string $lien): bool
									{
										$protocoles = [
											'http://', 'https://',
											// Fichier					Communication					Données			Script				Réseaux								Applications
											// 'ftp://', 'file://',		'mailto:', 'tel:', 'sms:',		'data:',		'javascript:',		'irc://', 'magnet:', 'geo:',		'whatsapp://', 'tg://', 'skype://'
										];

										foreach($protocoles as $protLiens => $protLien)
										{
											if(strpos($lien, $protLien) === 0)
												return true;
										}

										return false;
									}

									$liensPage = [];
									foreach($resLiens as $lien)
									{
										if(isLien($lien))
										{
											$lienParse = parse_url($lien);
											$lienParseFromUrlAnalysee = parse_url($urlAnalysee);

											if((!isset($_GET['liensLocaux']) AND !isset($_GET['liensSortants'])) OR isset($_GET['liensLocaux'])) {
												if(empty($lienParse['scheme']) AND empty($lienParse['host'])) {
													$liensPage[] = $lienParseFromUrlAnalysee['scheme'].'://'.$lienParseFromUrlAnalysee['host'].$lien;
												}

												elseif(preg_match('/'.$urlNdd.'/is', $lien)) {
													$liensPage[] = $lien;
												}
											}

											if((!isset($_GET['liensLocaux']) AND !isset($_GET['liensSortants'])) OR isset($_GET['liensSortants'])) {
												if(!preg_match('/'.$urlNdd.'/is', $lien)) {
													$liensPage[] = $lien;
												}
											}
										}
									}

									$liensPage = array_filter($liensPage);
									$liensPage = array_unique($liensPage);
									if(!empty($liensPage))
									{
										$i = 0;
										foreach($liensPage as $lien)
										{
											$lien = secuChars($lien);

											if(!empty($lien))
											{
												$i++;

												$liensImplode[] = '<div class="liens-dom" id="lien-'.$i.'">
													<div class="d-block d-lg-inline-block ms-lg-1 text-wrap text-break">'.lien($lien).'</div>
													<p class="mb-0 mt-3">
														<a href="'.$lien.'" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-title="Lien n°'.$i.'" '.$onclick.'><i class="fa-solid fa-link"></i> Lien #'.$i.'</a>
														<a href="https://thisip.pw/analyse-web?url='.urlencode($lien).'" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-title="Analyser le lien n°'.$i.'"><i class="fa-solid fa-magnifying-glass"></i> Analyser</a>
													</p>
												</div>';
											}
										}
									}

									else
										goto erreurAucunLien;
								}

								else
									goto erreurAucunLien;
							}

							else
							{
								erreurAucunLien:

								$i = 0;
								$liensImplode[] = alerte('danger', 'Aucun lien trouvé');
							}

							echo '<div class="row">
								<div class="col"><span class="fw-bold">'.$i.'</span> lien'.s($i).'</div>
								<div class="col-8 col-lg-4 btn-group" role="group" aria-label="Trie des liens">
									<a href="'.$uniqid.'#pills-liens-tab"				class="btn btn-outline-primary btn-sm'.((!isset($_GET['liensLocaux']) AND !isset($_GET['liensSortants'])) ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Afficher tous les liens de la page">'.((!isset($_GET['liensLocaux']) AND !isset($_GET['liensSortants'])) ? '<i class="fa-solid fa-check"></i>' : null).' tous</a>
									<a href="'.$uniqid.'?liensSortants#pills-liens-tab"	class="btn btn-outline-primary btn-sm'.(isset($_GET['liensSortants']) ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Afficher les liens pointant hors du site">'.(isset($_GET['liensSortants']) ? '<i class="fa-solid fa-check"></i>' : null).' sortants</a>
									<a href="'.$uniqid.'?liensLocaux#pills-liens-tab"	class="btn btn-outline-primary btn-sm'.(isset($_GET['liensLocaux']) ? ' active' : null).'" data-bs-toggle="tooltip" data-bs-title="Afficher les liens à l’intérieur de la page">'.(isset($_GET['liensLocaux']) ? '<i class="fa-solid fa-check"></i>' : null).' locaux</a>
								</div>

								'.((!isset($_GET['liensLocaux']) AND !isset($_GET['liensSortants'])) ? '<p style="font-size: .9rem;" class="mb-4 text-end fst-italic">Tous les liens de la page : <strong>'.$urlNdd.'</strong></p>' : null).'
								'.(isset($_GET['liensLocaux']) ? '<p style="font-size: .9rem;" class="mb-4 text-end fst-italic">Tous les liens pointant vers et hors du domaine : <strong>'.$urlNdd.'</strong></p>' : null).'
								'.(isset($_GET['liensSortants']) ? '<p style="font-size: .9rem;" class="mb-4 text-end fst-italic">Tous les liens pointant vers le domaine : <strong>'.$urlNdd.'</strong></p>' : null).

								implode('<hr class="mt-3">', $liensImplode).'
							</div>
						</div>

						<div class="tab-pane fade" id="pills-dom" role="tabpanel" aria-labelledby="pills-dom-tab" tabindex="0">
						</div>

						<div class="tab-pane fade" id="pills-ssl" role="tabpanel" aria-labelledby="pills-ssl-tab" tabindex="0">
							<h3 class="mb-4"><i class="fa-brands fa-expeditedssl"></i> Certificat SSL</h3>';

							$certificatSslArray = json_decode($res['certificat_ssl'], true);

							if(!empty($certificatSslArray) AND !empty($certificatSslArray['name']))
							{
								$sslSha256 = exec('echo | openssl s_client -connect '.$urlNdd.':443 2>/dev/null | openssl x509 -noout -fingerprint -sha256');

								$name				= !empty($certificatSslArray['name'])				? secuChars($certificatSslArray['name']) : 'inconnu';
								$hash				= !empty($certificatSslArray['name'])				? secuChars($certificatSslArray['name']) : 'inconnu';
								$version			= !empty($certificatSslArray['version'])			? secuChars($certificatSslArray['version']) : 'inconnu';
								$serialNumber		= !empty($certificatSslArray['serialNumber'])		? secuChars($certificatSslArray['serialNumber']) : 'inconnu';
								$serialNumberHex	= !empty($certificatSslArray['serialNumberHex'])	? secuChars($certificatSslArray['serialNumberHex']) : 'inconnu';
								$signatureTypeLN	= !empty($certificatSslArray['signatureTypeLN'])	? secuChars($certificatSslArray['signatureTypeLN']) : 'inconnu';
								$signatureTypeSN	= !empty($certificatSslArray['signatureTypeSN'])	? secuChars($certificatSslArray['signatureTypeSN']) : 'inconnu';
								$signatureTypeNID	= !empty($certificatSslArray['signatureTypeNID'])	? secuChars($certificatSslArray['signatureTypeNID']) : 'inconnu';

								$validFrom_time_t	= !empty($certificatSslArray['validFrom_time_t'])	? (int) $certificatSslArray['validFrom_time_t'] : null;
								$validTo_time_t		= !empty($certificatSslArray['validTo_time_t'])		? (int) $certificatSslArray['validTo_time_t'] : null;

								$validFromDate	= !empty($certificatSslArray['validFrom_time_t'])		? dateFormat($validFrom_time_t, 'DATE_RFC7231') : 'inconnue';
								$validToDate		= !empty($certificatSslArray['validTo_time_t'])		? dateFormat($validTo_time_t, 'DATE_RFC7231') : 'inconnue';

								$ssl[] = '<div style="background-color: rgba(248,248,248, 1);" class="text-center border border-secondary-subtle rounded-3 mb-3 p-2"><h3 class="mb-0">'.$name.'</h3></div>

								<div>
									<div class="card mb-3" id="InformationsGenerales">
										<div class="card-header"><h5 class="my-2">Informations générales</h5></div>
										<div class="card-body">
											<ul class="list-group">
												<li class="list-group-item"><span class="fw-bold">Hash</span> : '.$hash.'</li>
												<li class="list-group-item"><span class="fw-bold">Version</span> : '.$version.'</li>
												<li class="list-group-item"><span class="fw-bold">Numéro de série</span> : '.$serialNumber.'</li>
												<li class="list-group-item"><span class="fw-bold">Numéro de série (Hex)</span> : '.$serialNumberHex.'</li>
												<li class="list-group-item"><span class="fw-bold">Signature</span> : '.$signatureTypeLN.' ('.$signatureTypeSN.' / '.$signatureTypeNID.')</li>
												<li class="list-group-item"><span class="fw-bold">Empreinte sha256</span> : <code>'.secuChars($sslSha256).'</code></li>
											</ul>
										</div>
									</div>

									<div class="card mb-3" id="Sujet">
										<div class="card-header"><h5 class="my-2">Sujet</h5></div>
										<div class="card-body">
											<ul class="list-group">';
												if(!empty($certificatSslArray['subject']))
												{
													foreach($certificatSslArray['subject'] as $cle => $valeur)
													{
														$cle = secuChars($cle);
														$valeur = secuChars($valeur);

														if($cle == 'CN')	$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="CN = Common Name / Nom commun de l’émetteur">CN</span> : <span class="fw-bold curseur">'.$valeur.'</span></li>';
														else				$ssl[] = '<li class="list-group-item"><span class="fw-bold">'.$cle.'</span> : '.$valeur.'</li>';
													}
												}

												else
													$ssl[] = '<li class="list-group-item">inconnu</li>';

											$ssl[] = '</ul>
										</div>
									</div>

									<div class="card mb-3" id="Emetteur">
										<div class="card-header"><h5 class="my-2">Émetteur</h5></div>
										<div class="card-body">
											<ul class="list-group">';

												if(!empty($certificatSslArray['issuer']))
												{
													foreach($certificatSslArray['issuer'] as $cle => $valeur)
													{
														$cle = secuChars($cle);
														$valeur = secuChars($valeur);

														if($cle == 'CN')		$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="CN = Common Name / Nom du certificat de l’autorité émettrice">CN</span> : <span class="fw-bold">'.$valeur.'</span></li>';
														elseif($cle == 'C')		$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="C = Country / Pays">C</span> : <span class="fw-bold curseur">'.$valeur.'</span></li>';
														elseif($cle == 'O')		$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="O = Organization Name / Nom de l’organisation">O</span> : <span class="fw-bold curseur">'.$valeur.'</span></li>';
														else					$ssl[] = '<li class="list-group-item"><span class="fw-bold">'.$cle.'</span> : '.$valeur.'</li>';
													}
												}

												else
													$ssl[] = '<li class="list-group-item">inconnu</li>';

											$ssl[] = '</ul>
										</div>
									</div>

									<div class="card mb-3" id="Validite">
										<div class="card-header"><h5 class="my-2">Validité</h5></div>
										<div class="card-body">
											<ul class="list-group">
												<li class="list-group-item"><span class="fw-bold">Valide depuis</span> : <time datetime="'.date(DATE_ATOM, $validFrom_time_t).'">'.$validFromDate.'</time>'.(!empty($validFrom_time_t) ? ' (<em>'.temps($validFrom_time_t).'</em>)' : null).'</li>
												<li class="list-group-item"><span class="fw-bold">Valide jusqu’au</span> : <time datetime="'.date(DATE_ATOM, $validTo_time_t).'">'.$validToDate.'</time>'.(!empty($validFrom_time_t) ? ' (<em>'.temps($validTo_time_t).'</em>)' : null).'</li>
											</ul>

										</div>
									</div>

									<div class="card mb-3" id="Usages">
										<div class="card-header"><h5 class="my-2">Usages</h5></div>
										<div class="card-body">

											<ul class="list-group">';

												if(!empty($certificatSslArray['purposes']))
												{
													foreach($certificatSslArray['purposes'] as $usage)
													{
														$usage[0] = secuChars($usage[0]);
														$usage[2] = secuChars($usage[2]);

														if($usage[2] == 'sslclient')			$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="Authentifie un client SSL (ex : navigateur, application)">'.$usage[2].'</span> : '.($usage[0] ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-x text-danger"></i>').'</li>';
														elseif($usage[2] == 'sslserver')		$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="Authentifie un serveur SSL (ex : site web HTTPS)">'.$usage[2].'</span> : '.($usage[0] ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-x text-danger"></i>').'</li>';
														elseif($usage[2] == 'nssslserver')		$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="Spécifique à NSS (Network Security Services, de Mozilla), équivalent à sslserver">'.$usage[2].'</span> : '.($usage[0] ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-x text-danger"></i>').'</li>';
														elseif($usage[2] == 'smimesign')		$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="Signature des courriels avec S/MIME">'.$usage[2].'</span> : '.($usage[0] ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-x text-danger"></i>').'</li>';
														elseif($usage[2] == 'smimeencrypt')		$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="Chiffrement des courriels avec S/MIME">'.$usage[2].'</span> : '.($usage[0] ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-x text-danger"></i>').'</li>';
														elseif($usage[2] == 'crlsign')			$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="Autorisé à signer des listes de révocation (CRL)">'.$usage[2].'</span> : '.($usage[0] ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-x text-danger"></i>').'</li>';
														elseif($usage[2] == 'any')				$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="Peut servir à n’importe quel usage (peu courant et peu recommandé)">'.$usage[2].'</span> : '.($usage[0] ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-x text-danger"></i>').'</li>';
														elseif($usage[2] == 'ocsphelper')		$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="Utilisé pour des requêtes OCSP (validation de certificats)">'.$usage[2].'</span> : '.($usage[0] ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-x text-danger"></i>').'</li>';
														elseif($usage[2] == 'timestampsign')	$ssl[] = '<li class="list-group-item"><span class="fw-bold curseur text-decoration-underline link-offset-3" data-bs-toggle="tooltip" data-bs-title="Sert à signer des horodatages">'.$usage[2].'</span> : '.($usage[0] ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-x text-danger"></i>').'</li>';
														else									$ssl[] = '<li class="list-group-item"><span class="fw-bold">'.$usage[2].'</span> : '.($usage[0] ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-x text-danger"></i>').'</li>';
													}
												}

												else
													$ssl[] = '<li class="list-group-item">inconnu</li>';

											$ssl[] = '</ul>
										</div>
									</div>';

									if(!empty($certificatSslArray['extensions']))
									{
										$ssl[] = '<div class="card mb-3" id="Extensions">
											<div class="card-header"><h5 class="my-2">Extensions</h5></div>
											<div class="card-body">
												<ul class="list-group">';

													foreach($certificatSslArray['extensions'] as $cle => $valeur)
													{
														$cle = secuChars($cle);
														$valeur = secuChars($valeur);

														$ssl[] = '<li class="list-group-item"><span class="fw-bold">'.$cle.' :</span><br>
															'.($cle !== 'ct_precert_scts' ? '<div class="bg-light border rounded p-2"><small>'.$valeur.'</small></div>'
															:
															'<div class="col-12 col-lg-10"><code>'.nl2br($valeur, false).'</code></div>').'
														</li>';
													}

												$ssl[] = '</ul>
											</div>
										</div>';
									}

								$ssl[] = '</div>';

								echo implode($ssl);

								echo modalTexte($certificatSslArray, 'Certificat SSL');
							}

							else
								echo alerte('danger', 'Aucun Certificat SSL trouvé');

						echo '</div>

						<div class="tab-pane fade" id="pills-da" role="tabpanel" aria-labelledby="pills-da-tab" tabindex="0">';

							echo implode($dernieresAnalyses);

						echo '</div>
					</div>
				</div>
			</div>

			<script>
			document.querySelector("#pills-dom-tab").addEventListener("shown.bs.tab", function (event) {
				let target = event.target.getAttribute("data-bs-target");
				let targetContent = document.querySelector(target);

				if (!targetContent.innerHTML.trim()) {
					fetch("/analyse-web/dom/'.$uniqid.'")
						.then(response => response.text())
						.then(data => {
							targetContent.innerHTML = data;
							fetchAndDisplayWhois();
						})
						.catch(error => {
							targetContent.innerHTML = "<p>Erreur lors du chargement du contenu.</p>";
						});
				}
			});

			async function fetchAndDisplayWhois() {
				try {
					const response = await fetch("/assets/cache/dom/'.$res['id'].'-'.$uniqid.'");
					const textContent = await response.text();

					const beautifiedContent = html_beautify(textContent);
					const escapedContent = beautifiedContent;
					const highlightedCode = hljs.highlight(escapedContent, { language: "xml" }).value;

					document.querySelector("#domHtml").innerHTML = highlightedCode;
				} catch (error) {
					console.error("Erreur dans la récupération du fichier Dom : ", error);
				}
			}
			</script>';
		}

		else
			echo alerte('danger', 'L’analyse est introuvable');
	}



	else
	{
		echo '<form action="/analyse-web" method="post" id="formAnalyse">
			<div class="row mb-5">
				<div class="col-12 col-lg-8 mx-auto">
					<div class="input-group input-group-thisip">
						<input type="text" name="url" '.(!empty($_GET['url']) ? 'value="'.secuChars($_GET['url']).'"' : null).' class="form-control form-control-lg" id="inputAnalyse" placeholder="google.com" autofocus required>
						<button class="btn btn-success me-2" type="submit" form="formAnalyse" title="Valider"><i class="fa-solid fa-check"></i> <span class="d-none d-sm-inline-block">Valider</span></button>
						<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOptions" aria-expanded="false" aria-controls="collapseOptions" id="optionsButton" title="Afficher les options"><i class="fa-solid fa-gear"></i> <span class="d-none d-sm-inline-block">Options</span></button>
					</div>

					<div class="collapse mt-3" id="collapseOptions">
						<div class="row mb-3">
							<label class="col-12 col-lg-3 col-form-label" title="Visibilité de l’analyse">Visibilité de l’analyse</label>
							<div class="col-12 col-lg-9">
								<div class="d-inline-block" data-bs-toggle="tooltip" data-bs-title="Lister publiquement l’analyse">
									<input type="radio" value="analyseVisible" name="formAnalyseVisibilite" class="btn-check" id="visibiliteAnalyse" checked>
									<label class="btn btn-outline-success btn-sm" for="visibiliteAnalyse">Publique</label>
								</div>
								<div class="d-inline-block" data-bs-toggle="tooltip" data-bs-title="Ne pas lister l’analyse">
									<input type="radio" value="analyseNonListee" name="formAnalyseVisibilite" class="btn-check" id="analyseNonListee">
									<label class="btn btn-outline-warning btn-sm" for="analyseNonListee">Non listée</label>
								</div>

								<div class="d-inline-block" data-bs-toggle="tooltip" data-bs-title="Analyse personnelle">
									<input type="radio" value="analysePrivee" name="formAnalyseVisibilite" class="btn-check" id="analysePrivee">
									<label class="btn btn-outline-danger btn-sm" for="analysePrivee">Privée</label>
								</div>
							</div>
						</div>

						<div class="row mb-3">
							<label class="col-12 col-lg-3 col-form-label" title="Pays de l’analyse">Pays de l’analyse</label>

							<div class="col-12 col-lg-9 d-flex flex-wrap gap-2">';

								$pays = [
									'France'			=> 'FR|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path fill="#fff" d="M10 4H22V28H10z"></path><path d="M5,4h6V28H5c-2.208,0-4-1.792-4-4V8c0-2.208,1.792-4,4-4Z" fill="#092050"></path><path d="M25,4h6V28h-6c-2.208,0-4-1.792-4-4V8c0-2.208,1.792-4,4-4Z" transform="rotate(180 26 16)" fill="#be2a2c"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Allemagne'			=> 'DE|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path fill="#cc2b1d" d="M1 11H31V21H1z"></path><path d="M5,4H27c2.208,0,4,1.792,4,4v4H1v-4c0-2.208,1.792-4,4-4Z"></path><path d="M5,20H27c2.208,0,4,1.792,4,4v4H1v-4c0-2.208,1.792-4,4-4Z" transform="rotate(180 16 24)" fill="#f8d147"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Angleterre'		=> 'GB|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#071b65"></rect><path d="M5.101,4h-.101c-1.981,0-3.615,1.444-3.933,3.334L26.899,28h.101c1.981,0,3.615-1.444,3.933-3.334L5.101,4Z" fill="#fff"></path><path d="M22.25,19h-2.5l9.934,7.947c.387-.353,.704-.777,.929-1.257l-8.363-6.691Z" fill="#b92932"></path><path d="M1.387,6.309l8.363,6.691h2.5L2.316,5.053c-.387,.353-.704,.777-.929,1.257Z" fill="#b92932"></path><path d="M5,28h.101L30.933,7.334c-.318-1.891-1.952-3.334-3.933-3.334h-.101L1.067,24.666c.318,1.891,1.952,3.334,3.933,3.334Z" fill="#fff"></path><rect x="13" y="4" width="6" height="24" fill="#fff"></rect><rect x="1" y="13" width="30" height="6" fill="#fff"></rect><rect x="14" y="4" width="4" height="24" fill="#b92932"></rect><rect x="14" y="1" width="4" height="30" transform="translate(32) rotate(90)" fill="#b92932"></rect><path d="M28.222,4.21l-9.222,7.376v1.414h.75l9.943-7.94c-.419-.384-.918-.671-1.471-.85Z" fill="#b92932"></path><path d="M2.328,26.957c.414,.374,.904,.656,1.447,.832l9.225-7.38v-1.408h-.75L2.328,26.957Z" fill="#b92932"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Pays-bas'			=> 'NL|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path fill="#fff" d="M1 11H31V21H1z"></path><path d="M5,4H27c2.208,0,4,1.792,4,4v4H1v-4c0-2.208,1.792-4,4-4Z" fill="#a1292a"></path><path d="M5,20H27c2.208,0,4,1.792,4,4v4H1v-4c0-2.208,1.792-4,4-4Z" transform="rotate(180 16 24)" fill="#264387"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Italie'			=> 'IT|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path fill="#fff" d="M10 4H22V28H10z"></path><path d="M5,4h6V28H5c-2.208,0-4-1.792-4-4V8c0-2.208,1.792-4,4-4Z" fill="#41914d"></path><path d="M25,4h6V28h-6c-2.208,0-4-1.792-4-4V8c0-2.208,1.792-4,4-4Z" transform="rotate(180 26 16)" fill="#bf393b"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Espagne'			=> 'ES|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path fill="#f1c142" d="M1 10H31V22H1z"></path><path d="M5,4H27c2.208,0,4,1.792,4,4v3H1v-3c0-2.208,1.792-4,4-4Z" fill="#a0251e"></path><path d="M5,21H27c2.208,0,4,1.792,4,4v3H1v-3c0-2.208,1.792-4,4-4Z" transform="rotate(180 16 24.5)" fill="#a0251e"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path><path d="M12.614,13.091c.066-.031,.055-.14-.016-.157,.057-.047,.02-.15-.055-.148,.04-.057-.012-.144-.082-.13,.021-.062-.042-.127-.104-.105,.01-.068-.071-.119-.127-.081,.004-.068-.081-.112-.134-.069-.01-.071-.11-.095-.15-.035-.014-.068-.111-.087-.149-.028-.027-.055-.114-.057-.144-.004-.03-.047-.107-.045-.136,.002-.018-.028-.057-.044-.09-.034,.009-.065-.066-.115-.122-.082,.002-.07-.087-.111-.138-.064-.013-.064-.103-.087-.144-.036-.02-.063-.114-.075-.148-.017-.036-.056-.129-.042-.147,.022-.041-.055-.135-.031-.146,.036-.011-.008-.023-.014-.037-.016,.006-.008,.01-.016,.015-.025h.002c.058-.107,.004-.256-.106-.298v-.098h.099v-.154h-.099v-.101h-.151v.101h-.099v.154h.099v.096c-.113,.04-.169,.191-.11,.299h.002c.004,.008,.009,.017,.014,.024-.015,.002-.029,.008-.04,.017-.011-.067-.106-.091-.146-.036-.018-.064-.111-.078-.147-.022-.034-.057-.128-.046-.148,.017-.041-.052-.131-.028-.144,.036-.051-.047-.139-.006-.138,.064-.056-.033-.131,.017-.122,.082-.034-.01-.072,.006-.091,.034-.029-.047-.106-.049-.136-.002-.03-.054-.117-.051-.143,.004-.037-.059-.135-.04-.149,.028-.039-.06-.14-.037-.15,.035-.053-.043-.138,0-.134,.069-.056-.038-.137,.013-.127,.081-.062-.021-.125,.044-.104,.105-.05-.009-.096,.033-.096,.084h0c0,.017,.005,.033,.014,.047-.075-.002-.111,.101-.055,.148-.071,.017-.082,.125-.016,.157-.061,.035-.047,.138,.022,.154-.013,.015-.021,.034-.021,.055h0c0,.042,.03,.077,.069,.084-.023,.048,.009,.11,.06,.118-.013,.03-.012,.073-.012,.106,.09-.019,.2,.006,.239,.11-.015,.068,.065,.156,.138,.146,.06,.085,.133,.165,.251,.197-.021,.093,.064,.093,.123,.118-.013,.016-.043,.063-.055,.081,.024,.013,.087,.041,.113,.051,.005,.019,.004,.028,.004,.031,.091,.501,2.534,.502,2.616-.001v-.002s.004,.003,.004,.004c0-.003-.001-.011,.004-.031l.118-.042-.062-.09c.056-.028,.145-.025,.123-.119,.119-.032,.193-.112,.253-.198,.073,.01,.153-.078,.138-.146,.039-.104,.15-.129,.239-.11,0-.035,.002-.078-.013-.109,.044-.014,.07-.071,.049-.115,.062-.009,.091-.093,.048-.139,.069-.016,.083-.12,.022-.154Zm-.296-.114c0,.049-.012,.098-.034,.141-.198-.137-.477-.238-.694-.214-.002-.009-.006-.017-.011-.024,0,0,0-.001,0-.002,.064-.021,.074-.12,.015-.153,0,0,0,0,0,0,.048-.032,.045-.113-.005-.141,.328-.039,.728,.09,.728,.393Zm-.956-.275c0,.063-.02,.124-.054,.175-.274-.059-.412-.169-.717-.185-.007-.082-.005-.171-.011-.254,.246-.19,.81-.062,.783,.264Zm-1.191-.164c-.002,.05-.003,.102-.007,.151-.302,.013-.449,.122-.719,.185-.26-.406,.415-.676,.73-.436-.002,.033-.005,.067-.004,.101Zm-1.046,.117c0,.028,.014,.053,.034,.069,0,0,0,0,0,0-.058,.033-.049,.132,.015,.152,0,0,0,.001,0,.002-.005,.007-.008,.015-.011,.024-.219-.024-.495,.067-.698,.206-.155-.377,.323-.576,.698-.525-.023,.015-.039,.041-.039,.072Zm3.065-.115s0,0,0,0c0,0,0,0,0,0,0,0,0,0,0,0Zm-3.113,1.798v.002s-.002,0-.003,.002c0-.001,.002-.003,.003-.003Z" fill="#9b8028"></path><path d="M14.133,16.856c.275-.65,.201-.508-.319-.787v-.873c.149-.099-.094-.121,.05-.235h.072v-.339h-.99v.339h.075c.136,.102-.091,.146,.05,.235v.76c-.524-.007-.771,.066-.679,.576h.039s0,0,0,0l.016,.036c.14-.063,.372-.107,.624-.119v.224c-.384,.029-.42,.608,0,.8v1.291c-.053,.017-.069,.089-.024,.123,.007,.065-.058,.092-.113,.083,0,.026,0,.237,0,.269-.044,.024-.113,.03-.17,.028v.108s0,0,0,0v.107s0,0,0,0v.107s0,0,0,0v.108s0,0,0,0v.186c.459-.068,.895-.068,1.353,0v-.616c-.057,.002-.124-.004-.17-.028,0-.033,0-.241,0-.268-.054,.008-.118-.017-.113-.081,.048-.033,.034-.108-.021-.126v-.932c.038,.017,.073,.035,.105,.053-.105,.119-.092,.326,.031,.429l.057-.053c.222-.329,.396-.743-.193-.896v-.35c.177-.019,.289-.074,.319-.158Z" fill="#9b8028"></path><path d="M8.36,16.058c-.153-.062-.39-.098-.653-.102v-.76c.094-.041,.034-.115-.013-.159,.02-.038,.092-.057,.056-.115h.043v-.261h-.912v.261h.039c-.037,.059,.039,.078,.057,.115-.047,.042-.108,.118-.014,.159v.873c-.644,.133-.611,.748,0,.945v.35c-.59,.154-.415,.567-.193,.896l.057,.053c.123-.103,.136-.31,.031-.429,.032-.018,.067-.036,.105-.053v.932c-.055,.018-.069,.093-.021,.126,.005,.064-.059,.089-.113,.081,0,.026,0,.236,0,.268-.045,.024-.113,.031-.17,.028v.401h0v.215c.459-.068,.895-.068,1.352,0v-.186s0,0,0,0v-.108s0,0,0,0v-.107s0,0,0,0v-.107s0,0,0,0v-.108c-.056,.002-.124-.004-.169-.028,0-.033,0-.241,0-.269-.055,.008-.119-.018-.113-.083,.045-.034,.03-.107-.024-.124v-1.29c.421-.192,.383-.772,0-.8v-.224c.575,.035,.796,.314,.653-.392Z" fill="#9b8028"></path><path d="M12.531,14.533h-4.28l.003,2.572v1.485c0,.432,.226,.822,.591,1.019,.473,.252,1.024,.391,1.552,.391s1.064-.135,1.544-.391c.364-.197,.591-.587,.591-1.019v-4.057Z" fill="#a0251e"></path></svg>',
									'Portugal'			=> 'PT|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M5,4H13V28H5c-2.208,0-4-1.792-4-4V8c0-2.208,1.792-4,4-4Z" fill="#2b6519"></path><path d="M16,4h15V28h-15c-2.208,0-4-1.792-4-4V8c0-2.208,1.792-4,4-4Z" transform="rotate(180 21.5 16)" fill="#ea3323"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path><circle cx="12" cy="16" r="5" fill="#ff5"></circle><path d="M14.562,13.529l-5.125-.006v3.431h0c.004,.672,.271,1.307,.753,1.787,.491,.489,1.132,.759,1.805,.759,.684,0,1.328-.267,1.813-.75,.485-.484,.753-1.126,.753-1.808v-3.413Z" fill="#ea3323"></path></svg>',
									'Danemark'			=> 'DK|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#b92932"></rect><path fill="#fff" d="M31 14L15 14 15 4 11 4 11 14 1 14 1 18 11 18 11 28 15 28 15 18 31 18 31 14z"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Autriche'			=> 'AT|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path fill="#fff" d="M1 11H31V21H1z"></path><path d="M5,4H27c2.208,0,4,1.792,4,4v4H1v-4c0-2.208,1.792-4,4-4Z" fill="#b92932"></path><path d="M5,20H27c2.208,0,4,1.792,4,4v4H1v-4c0-2.208,1.792-4,4-4Z" transform="rotate(180 16 24)" fill="#b92932"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Pologne'			=> 'PL|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M1,24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V15H1v9Z" fill="#cb2e40"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4v8H31V8c0-2.209-1.791-4-4-4Z" fill="#fff"></path><path d="M5,28H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4ZM2,8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Suisse'			=> 'CH|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#c93927"></rect><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path><path fill="#fff" d="M14 10H18V22H14z"></path><path transform="rotate(90 16 16)" fill="#fff" d="M14 10H18V22H14z"></path></svg>',
									'Norvège'			=> 'NO|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#ac2431"></rect><path fill="#fff" d="M31 12L17 12 17 4 9 4 9 12 1 12 1 20 9 20 9 28 17 28 17 20 31 20 31 12z"></path><path fill="#061a57" d="M31 14L15 14 15 4 11 4 11 14 1 14 1 18 11 18 11 28 15 28 15 18 31 18 31 14z"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Suède'				=> 'SE|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#2e69a4"></rect><path fill="#f7cf46" d="M31 14L15 14 15 4 11 4 11 14 1 14 1 18 11 18 11 28 15 28 15 18 31 18 31 14z"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Finlande'			=> 'FI|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#fff"></rect><path fill="#0e2a69" d="M31 14L15 14 15 4 11 4 11 14 1 14 1 18 11 18 11 28 15 28 15 18 31 18 31 14z"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Canada'			=> 'CA|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path fill="#fff" d="M8 4H24V28H8z"></path><path d="M5,4h4V28H5c-2.208,0-4-1.792-4-4V8c0-2.208,1.792-4,4-4Z" fill="#c53a28"></path><path d="M27,4h4V28h-4c-2.208,0-4-1.792-4-4V8c0-2.208,1.792-4,4-4Z" transform="rotate(180 27 16)" fill="#c53a28"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M16.275,22.167l-.138-2.641c-.007-.16,.117-.296,.277-.304,.021,0,.042,0,.063,.004l2.629,.462-.355-.979c-.03-.08-.005-.17,.061-.223l2.88-2.332-.649-.303c-.091-.043-.135-.146-.104-.242l.569-1.751-1.659,.352c-.093,.019-.186-.029-.223-.116l-.321-.756-1.295,1.389c-.076,.08-.201,.083-.281,.007-.049-.047-.071-.115-.058-.182l.624-3.22-1.001,.578c-.095,.056-.217,.024-.272-.071-.002-.004-.004-.008-.006-.012l-1.016-1.995-1.016,1.995c-.049,.098-.169,.138-.267,.089-.004-.002-.008-.004-.012-.006l-1.001-.578,.624,3.22c.021,.108-.05,.212-.158,.233-.067,.013-.135-.009-.182-.058l-1.295-1.389-.321,.756c-.037,.087-.131,.136-.223,.116l-1.659-.352,.569,1.751c.031,.095-.013,.199-.104,.242l-.649,.303,2.88,2.332c.066,.054,.091,.144,.061,.223l-.355,.979,2.629-.462c.158-.027,.309,.079,.336,.237,.004,.021,.005,.042,.004,.063l-.138,2.641h.551Z" fill="#c53a28"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'États-Unis'		=> 'US|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#fff"></rect><path d="M1.638,5.846H30.362c-.711-1.108-1.947-1.846-3.362-1.846H5c-1.414,0-2.65,.738-3.362,1.846Z" fill="#a62842"></path><path d="M2.03,7.692c-.008,.103-.03,.202-.03,.308v1.539H31v-1.539c0-.105-.022-.204-.03-.308H2.03Z" fill="#a62842"></path><path fill="#a62842" d="M2 11.385H31V13.231H2z"></path><path fill="#a62842" d="M2 15.077H31V16.923000000000002H2z"></path><path fill="#a62842" d="M1 18.769H31V20.615H1z"></path><path d="M1,24c0,.105,.023,.204,.031,.308H30.969c.008-.103,.031-.202,.031-.308v-1.539H1v1.539Z" fill="#a62842"></path><path d="M30.362,26.154H1.638c.711,1.108,1.947,1.846,3.362,1.846H27c1.414,0,2.65-.738,3.362-1.846Z" fill="#a62842"></path><path d="M5,4h11v12.923H1V8c0-2.208,1.792-4,4-4Z" fill="#102d5e"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path><path fill="#fff" d="M4.601 7.463L5.193 7.033 4.462 7.033 4.236 6.338 4.01 7.033 3.279 7.033 3.87 7.463 3.644 8.158 4.236 7.729 4.827 8.158 4.601 7.463z"></path><path fill="#fff" d="M7.58 7.463L8.172 7.033 7.441 7.033 7.215 6.338 6.989 7.033 6.258 7.033 6.849 7.463 6.623 8.158 7.215 7.729 7.806 8.158 7.58 7.463z"></path><path fill="#fff" d="M10.56 7.463L11.151 7.033 10.42 7.033 10.194 6.338 9.968 7.033 9.237 7.033 9.828 7.463 9.603 8.158 10.194 7.729 10.785 8.158 10.56 7.463z"></path><path fill="#fff" d="M6.066 9.283L6.658 8.854 5.927 8.854 5.701 8.158 5.475 8.854 4.744 8.854 5.335 9.283 5.109 9.979 5.701 9.549 6.292 9.979 6.066 9.283z"></path><path fill="#fff" d="M9.046 9.283L9.637 8.854 8.906 8.854 8.68 8.158 8.454 8.854 7.723 8.854 8.314 9.283 8.089 9.979 8.68 9.549 9.271 9.979 9.046 9.283z"></path><path fill="#fff" d="M12.025 9.283L12.616 8.854 11.885 8.854 11.659 8.158 11.433 8.854 10.702 8.854 11.294 9.283 11.068 9.979 11.659 9.549 12.251 9.979 12.025 9.283z"></path><path fill="#fff" d="M6.066 12.924L6.658 12.494 5.927 12.494 5.701 11.799 5.475 12.494 4.744 12.494 5.335 12.924 5.109 13.619 5.701 13.19 6.292 13.619 6.066 12.924z"></path><path fill="#fff" d="M9.046 12.924L9.637 12.494 8.906 12.494 8.68 11.799 8.454 12.494 7.723 12.494 8.314 12.924 8.089 13.619 8.68 13.19 9.271 13.619 9.046 12.924z"></path><path fill="#fff" d="M12.025 12.924L12.616 12.494 11.885 12.494 11.659 11.799 11.433 12.494 10.702 12.494 11.294 12.924 11.068 13.619 11.659 13.19 12.251 13.619 12.025 12.924z"></path><path fill="#fff" d="M13.539 7.463L14.13 7.033 13.399 7.033 13.173 6.338 12.947 7.033 12.216 7.033 12.808 7.463 12.582 8.158 13.173 7.729 13.765 8.158 13.539 7.463z"></path><path fill="#fff" d="M4.601 11.104L5.193 10.674 4.462 10.674 4.236 9.979 4.01 10.674 3.279 10.674 3.87 11.104 3.644 11.799 4.236 11.369 4.827 11.799 4.601 11.104z"></path><path fill="#fff" d="M7.58 11.104L8.172 10.674 7.441 10.674 7.215 9.979 6.989 10.674 6.258 10.674 6.849 11.104 6.623 11.799 7.215 11.369 7.806 11.799 7.58 11.104z"></path><path fill="#fff" d="M10.56 11.104L11.151 10.674 10.42 10.674 10.194 9.979 9.968 10.674 9.237 10.674 9.828 11.104 9.603 11.799 10.194 11.369 10.785 11.799 10.56 11.104z"></path><path fill="#fff" d="M13.539 11.104L14.13 10.674 13.399 10.674 13.173 9.979 12.947 10.674 12.216 10.674 12.808 11.104 12.582 11.799 13.173 11.369 13.765 11.799 13.539 11.104z"></path><path fill="#fff" d="M4.601 14.744L5.193 14.315 4.462 14.315 4.236 13.619 4.01 14.315 3.279 14.315 3.87 14.744 3.644 15.44 4.236 15.01 4.827 15.44 4.601 14.744z"></path><path fill="#fff" d="M7.58 14.744L8.172 14.315 7.441 14.315 7.215 13.619 6.989 14.315 6.258 14.315 6.849 14.744 6.623 15.44 7.215 15.01 7.806 15.44 7.58 14.744z"></path><path fill="#fff" d="M10.56 14.744L11.151 14.315 10.42 14.315 10.194 13.619 9.968 14.315 9.237 14.315 9.828 14.744 9.603 15.44 10.194 15.01 10.785 15.44 10.56 14.744z"></path><path fill="#fff" d="M13.539 14.744L14.13 14.315 13.399 14.315 13.173 13.619 12.947 14.315 12.216 14.315 12.808 14.744 12.582 15.44 13.173 15.01 13.765 15.44 13.539 14.744z"></path></svg>',
									'Australie'			=> 'AU|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#061b65"></rect><path d="M6.5,13.774v2.226h4v-2.227l3.037,2.227h2.463v-1.241l-3.762-2.759h3.762v-4h-2.74l2.74-2.009v-1.991h-1.441l-4.059,2.977v-2.977H6.5v2.794l-3.257-2.389c-.767,.374-1.389,.983-1.786,1.738l2.532,1.858H1s0,0,0,0v4h3.763l-3.763,2.76v1.24H3.464l3.036-2.226Z" fill="#fff"></path><path d="M1.805,5.589l3.285,2.411h1.364L2.359,4.995c-.204,.18-.39,.377-.554,.594Z" fill="#d22d32"></path><path fill="#d22d32" d="M1 16L6.454 12 6.454 13 2.363 16 1 16z"></path><path id="1705926025352-5861297_Star7" d="M6.838,18.741l.536,1.666,1.636-.62-.968,1.457,1.505,.893-1.743,.152,.24,1.733-1.205-1.268-1.205,1.268,.24-1.733-1.743-.152,1.505-.893-.968-1.457,1.636,.62,.536-1.666Z" fill="#fff"></path><path id="1705926025352-5861297_Star7-2" d="M23.113,21.755l.291,.906,.89-.337-.527,.793,.819,.486-.948,.082,.131,.943-.656-.69-.656,.69,.131-.943-.948-.082,.819-.486-.527-.793,.89,.337,.291-.906Z" fill="#fff"></path><path id="1705926025352-5861297_Star7-3" d="M20.166,13.127l.291,.906,.89-.337-.527,.793,.819,.486-.948,.082,.131,.943-.656-.69-.656,.69,.131-.943-.948-.082,.819-.486-.527-.793,.89,.337,.291-.906Z" fill="#fff"></path><path id="1705926025352-5861297_Star7-4" d="M23.43,7.127l.291,.906,.89-.337-.527,.793,.819,.486-.948,.082,.131,.943-.656-.69-.656,.69,.131-.943-.948-.082,.819-.486-.527-.793,.89,.337,.291-.906Z" fill="#fff"></path><path id="1705926025352-5861297_Star7-5" d="M28.132,10.817l.291,.906,.89-.337-.527,.793,.819,.486-.948,.082,.131,.943-.656-.69-.656,.69,.131-.943-.948-.082,.819-.486-.527-.793,.89,.337,.291-.906Z" fill="#fff"></path><path id="1705926025352-5861297_Star5" d="M25.742,16l.23,.565,.608,.045-.466,.393,.146,.592-.518-.321-.518,.321,.146-.592-.466-.393,.608-.045,.23-.565Z" fill="#fff"></path><path fill="#d22d32" d="M9.5 16L7.5 16 7.5 11 1 11 1 9 7.5 9 7.5 4 9.5 4 9.5 9 16 9 16 11 9.5 11 9.5 16z"></path><path fill="#d22d32" d="M16 15.667L11 12 11 13 15.091 16 16 16 16 15.667z"></path><path fill="#d22d32" d="M16 4L15.752 4 10.291 8.004 11.655 8.004 16 4.818 16 4z"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Nouvelle-Zélande'	=> 'NZ|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#071b65"></rect><path d="M6.5,13.774v2.226h4v-2.227l3.037,2.227h2.463v-1.241l-3.762-2.759h3.762v-4h-2.74l2.74-2.009v-1.991h-1.441l-4.059,2.977v-2.977H6.5v2.794l-3.257-2.389c-.767,.374-1.389,.983-1.786,1.738l2.532,1.858H1s0,0,0,0v4h3.763l-3.763,2.76v1.24H3.464l3.036-2.226Z" fill="#fff"></path><path d="M1.806,5.589l3.285,2.411h1.364L2.36,4.995c-.204,.18-.39,.377-.554,.594Z" fill="#b92831"></path><path fill="#b92831" d="M1 16L6.454 12 6.454 13 2.363 16 1 16z"></path><path fill="#b92831" d="M9.5 16L7.5 16 7.5 11 1 11 1 9 7.5 9 7.5 4 9.5 4 9.5 9 16 9 16 11 9.5 11 9.5 16z"></path><path fill="#b92831" d="M16 15.667L11 12 11 13 15.091 16 16 16 16 15.667z"></path><path fill="#b92831" d="M16 4L15.752 4 10.291 8.004 11.655 8.004 16 4.818 16 4z"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path><path fill="#b92831" d="M23.495 8.062L23.008 9.56 21.433 9.56 22.707 10.486 22.22 11.984 23.495 11.058 24.769 11.984 24.282 10.486 25.556 9.56 23.981 9.56 23.495 8.062z"></path><path d="M25.007,12.311l-1.512-1.098-1.512,1.098,.578-1.777-1.512-1.099h1.869l.578-1.777,.578,1.777h1.869l-1.512,1.099,.578,1.777Zm-1.512-1.407l1.037,.752-.396-1.218,1.036-.753h-1.281l-.396-1.219-.396,1.219h-1.281l1.036,.753-.396,1.218,1.037-.752Z" fill="#fff"></path><path fill="#b92831" d="M23.495 19.076L23.008 20.574 21.433 20.574 22.707 21.5 22.22 22.998 23.495 22.072 24.769 22.998 24.282 21.5 25.556 20.574 23.981 20.574 23.495 19.076z"></path><path d="M25.007,23.325l-1.512-1.099-1.512,1.099,.578-1.777-1.512-1.099h1.869l.578-1.777,.578,1.777h1.869l-1.512,1.099,.578,1.777Zm-1.512-1.407l1.037,.753-.396-1.219,1.036-.753h-1.281l-.396-1.219-.396,1.219h-1.281l1.036,.753-.396,1.219,1.037-.753Z" fill="#fff"></path><path fill="#b92831" d="M27.503 12.774L27.111 13.983 25.84 13.983 26.868 14.73 26.475 15.938 27.503 15.191 28.531 15.938 28.139 14.73 29.167 13.983 27.896 13.983 27.503 12.774z"></path><path d="M28.769,16.265l-1.266-.92-1.266,.92,.483-1.488-1.266-.919h1.564l.483-1.488,.483,1.488h1.564l-1.266,.919,.483,1.488Zm-1.266-1.229l.79,.574-.302-.929,.79-.574h-.977l-.302-.929-.302,.929h-.977l.79,.574-.302,.929,.79-.574Z" fill="#fff"></path><path fill="#b92831" d="M19.77 13.417L19.377 14.625 18.106 14.625 19.134 15.372 18.742 16.58 19.77 15.833 20.798 16.58 20.405 15.372 21.433 14.625 20.162 14.625 19.77 13.417z"></path><path d="M21.035,16.907l-1.266-.919-1.266,.919,.483-1.487-1.266-.92h1.564l.483-1.488,.483,1.488h1.565l-1.266,.92,.483,1.487Zm-1.266-1.228l.79,.574-.302-.929,.791-.574h-.977l-.302-.929-.302,.929h-.977l.79,.574-.302,.929,.79-.574Z" fill="#fff"></path></svg>',
									'Singapour'			=> 'SG|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M1,24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V15H1v9Z" fill="#fff"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4v8H31V8c0-2.209-1.791-4-4-4Z" fill="#db3c3f"></path><path d="M5,28H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4ZM2,8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path><path d="M6.811,10.5c0-1.898,1.321-3.487,3.094-3.897-.291-.067-.594-.103-.906-.103-2.209,0-4,1.791-4,4s1.791,4,4,4c.311,0,.615-.036,.906-.103-1.773-.41-3.094-1.999-3.094-3.897Z" fill="#fff"></path><path fill="#fff" d="M10.81 8.329L10.576 9.048 11.189 8.603 11.801 9.048 11.567 8.329 12.179 7.884 11.423 7.884 11.189 7.164 10.955 7.884 10.198 7.884 10.81 8.329z"></path><path fill="#fff" d="M14.361 9.469L13.605 9.469 13.371 8.749 13.137 9.469 12.38 9.469 12.992 9.914 12.759 10.634 13.371 10.189 13.983 10.634 13.749 9.914 14.361 9.469z"></path><path fill="#fff" d="M10.074 12.034L9.84 11.315 9.606 12.034 8.85 12.034 9.462 12.479 9.228 13.199 9.84 12.754 10.452 13.199 10.218 12.479 10.831 12.034 10.074 12.034z"></path><path fill="#fff" d="M12.771 12.034L12.537 11.315 12.303 12.034 11.547 12.034 12.159 12.479 11.925 13.199 12.537 12.754 13.149 13.199 12.916 12.479 13.528 12.034 12.771 12.034z"></path><path fill="#fff" d="M9.24 9.469L9.007 8.75 8.773 9.469 8.016 9.469 8.628 9.914 8.394 10.634 9.007 10.189 9.619 10.634 9.385 9.914 9.997 9.469 9.24 9.469z"></path></|svg>',
									'Israël'			=> 'IL|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#fff"></rect><path fill="#1433b3" d="M1 8H31V12H1z"></path><path fill="#1433b3" d="M1 20H31V24H1z"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M16,19.464l-1-1.732h-2l1-1.732-1-1.732h2l1-1.732,1,1.732h2l-1,1.732,1,1.732h-2l-1,1.732Zm-.365-1.732l.365,.632,.365-.632h-.73Zm1.682-.55h.73l-.365-.632-.365,.632Zm-2,0h1.365l.682-1.182-.682-1.182h-1.365l-.682,1.182,.682,1.182Zm-1.365,0h.73l-.365-.632-.365,.632Zm3.365-2.364l.365,.632,.365-.632h-.73Zm-3.365,0l.365,.632,.365-.632h-.73Zm1.682-.55h.73l-.365-.632-.365,.632Z" fill="#1437b0"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Japon'				=> 'JP|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#fff"></rect><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><circle cx="16" cy="16" r="6" fill="#ae232f"></circle><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Thaïlande'			=> 'TH|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path fill="#282646" d="M1 11H31V21H1z"></path><path d="M5,4H27c2.208,0,4,1.792,4,4v4H1v-4c0-2.208,1.792-4,4-4Z" fill="#992532"></path><path d="M5,20H27c2.208,0,4,1.792,4,4v4H1v-4c0-2.208,1.792-4,4-4Z" transform="rotate(180 16 24)" fill="#992532"></path><path fill="#fff" d="M1 9H31V12H1z"></path><path fill="#fff" d="M1 20H31V23H1z"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Chine'				=> 'CN|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#db362f"></rect><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path fill="#ff0" d="M7.958 10.152L7.19 7.786 6.421 10.152 3.934 10.152 5.946 11.614 5.177 13.979 7.19 12.517 9.202 13.979 8.433 11.614 10.446 10.152 7.958 10.152z"></path><path fill="#ff0" d="M12.725 8.187L13.152 8.898 13.224 8.072 14.032 7.886 13.269 7.562 13.342 6.736 12.798 7.361 12.035 7.037 12.461 7.748 11.917 8.373 12.725 8.187z"></path><path fill="#ff0" d="M14.865 10.372L14.982 11.193 15.37 10.46 16.187 10.602 15.61 10.007 15.997 9.274 15.253 9.639 14.675 9.044 14.793 9.865 14.048 10.23 14.865 10.372z"></path><path fill="#ff0" d="M15.597 13.612L16.25 13.101 15.421 13.13 15.137 12.352 14.909 13.149 14.081 13.179 14.769 13.642 14.541 14.439 15.194 13.928 15.881 14.391 15.597 13.612z"></path><path fill="#ff0" d="M13.26 15.535L13.298 14.707 12.78 15.354 12.005 15.062 12.46 15.754 11.942 16.402 12.742 16.182 13.198 16.875 13.236 16.047 14.036 15.827 13.26 15.535z"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Corée du Sud'		=> 'KR|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#fff"></rect><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path transform="rotate(-56.31 8.143 10.762)" d="M5.877 10.384H10.41V11.139000000000001H5.877z"></path><path transform="rotate(-56.31 9.086 11.39)" d="M6.819 11.013H11.352V11.768H6.819z"></path><path transform="rotate(-56.31 10.028 12.02)" d="M7.762 11.641H12.295V12.396H7.762z"></path><path transform="rotate(-56.31 24.538 20.216)" d="M23.499 19.839H25.576V20.593999999999998H23.499z"></path><path transform="rotate(-56.31 23.176 22.26)" d="M22.137 21.882H24.215V22.637H22.137z"></path><path transform="rotate(-56.31 23.595 19.588)" d="M22.556 19.21H24.633000000000003V19.965H22.556z"></path><path transform="rotate(-56.31 22.234 21.632)" d="M21.195 21.253H23.272V22.008H21.195z"></path><path transform="rotate(-56.31 22.653 18.96)" d="M21.614 18.582H23.691000000000003V19.337H21.614z"></path><path transform="rotate(-56.31 21.29 21.002)" d="M20.252 20.625H22.329V21.38H20.252z"></path><path d="M12.229,13.486c1.389-2.083,4.203-2.646,6.286-1.257s2.646,4.203,1.257,6.286l-7.543-5.029Z" fill="#be3b3e"></path><path d="M12.229,13.486c-1.389,2.083-.826,4.897,1.257,6.286s4.897,.826,6.286-1.257c.694-1.041,.413-2.449-.629-3.143s-2.449-.413-3.143,.629l-3.771-2.514Z" fill="#1c449c"></path><circle cx="14.114" cy="14.743" r="2.266" fill="#be3b3e"></circle><path transform="rotate(-33.69 8.143 21.238)" d="M7.765 18.972H8.52V23.505000000000003H7.765z"></path><path transform="rotate(-33.69 10.03 19.98)" d="M9.651 17.715H10.406V22.248H9.651z"></path><path transform="rotate(-33.69 22.915 11.39)" d="M22.537 9.124H23.291999999999998V13.657H22.537z"></path><path transform="rotate(-33.69 8.405 19.588)" d="M8.027 18.549H8.782V20.625999999999998H8.027z"></path><path transform="rotate(-33.691 9.767 21.632)" d="M9.389 20.592H10.144V22.668999999999997H9.389z"></path><path transform="rotate(-33.69 21.29 10.998)" d="M20.913 9.959H21.668V12.036H20.913z"></path><path transform="rotate(-33.69 22.652 13.04)" d="M22.275 12.002H23.029999999999998V14.079H22.275z"></path><path transform="rotate(-33.69 23.176 9.741)" d="M22.798 8.702H23.552999999999997V10.779H22.798z"></path><path transform="rotate(-33.691 24.539 11.783)" d="M24.16 10.745H24.915V12.822H24.16z"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>',
									'Corée du Nord'		=> 'KP|<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path fill="#da3530" d="M1 8H31V24H1z"></path><path d="M5,4H27c2.208,0,4,1.792,4,4v1H1v-1c0-2.208,1.792-4,4-4Z" fill="#1f4d9e"></path><path d="M5,23H27c2.208,0,4,1.792,4,4v1H1v-1c0-2.208,1.792-4,4-4Z" transform="rotate(180 16 25.5)" fill="#1f4d9e"></path><path fill="#fff" d="M1 9H31V10H1z"></path><path fill="#fff" d="M1 22H31V23H1z"></path><path d="M27,4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4Zm3,20c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path><path d="M10.312,12c-2.037,0-3.687,1.651-3.687,3.688s1.651,3.688,3.687,3.688,3.688-1.651,3.688-3.688-1.651-3.688-3.688-3.688Zm2.1,6.578l-2.1-1.526-2.1,1.526,.802-2.468-2.1-1.526h2.595l.802-2.468,.802,2.468h2.595l-2.1,1.526,.802,2.468Z" fill="#fff"></path></svg>'
								];

								echo '<input type="radio" value="auto" name="formAnalysePays" class="btn-check" id="paysAnalyseAuto" checked>
								<label class="btn btn-outline-success px-1 py-0" for="paysAnalyseAuto" data-bs-toggle="tooltip" data-bs-title="Automatique">🏁 Auto</label>';

								foreach($pays as $p => $e) {
									$e = explode('|', $e);

									echo '<input type="radio" value="'.mb_strtolower($e[0]).'" name="formAnalysePays" class="btn-check" id="paysAnalyse'.slug($p).'">
									<label class="btn btn-outline-success px-1 py-0" for="paysAnalyse'.slug($p).'" data-bs-toggle="tooltip" data-bs-title="'.$p.'">'.isoEmoji($e[0]).' '.$e[0].'</label>';
								}

							echo '</div>
						</div>

						<div class="row mb-3" title="User Agent">
							<label for="selectUserAgent" class="col-12 col-lg-3 col-form-label">User Agent</label>
							<div class="col-12 col-lg-9">
								<select name="formAnalyseUserAgent" class="form-control curseur" id="selectUserAgent">
									<optgroup label="Firefox sur Windows">
										<option value="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0" selected>Défaut - Firefox 143.0 sur Windows 11</option>
										<option value="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0">Firefox 125.0 sur Windows 10</option>
										<option value="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:100.0) Gecko/20100101 Firefox/100.0">Firefox 100.0 sur Windows 10</option>
									</optgroup>

									<optgroup label="Chrome sur Windows">
										<option value="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.6943.60 Safari/537.36">Google Chrome 133 sur Windows 10 (Chrome 128 sur Windows 10)</option>
									</optgroup>

									<optgroup label="Chrome sur macOS">
										<option value="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36">Chrome 128 sur macOS 14.5</option>
									</optgroup>

									<optgroup label="Chrome sur Linux">
										<option value="Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36">Chrome 129.0.0 sur Linux</option>
										<option value="Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36">Chrome 128.0.0 sur Linux</option>
									</optgroup>

									<optgroup label="Chrome sur iOS">
										<option value="Mozilla/5.0 (iPhone; CPU iPhone OS 17_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/128.0.6613.92 Mobile/15E148 Safari/604.1">Chrome 128 sur iOS 17.5 (iPhone)</option>
										<option value="Mozilla/5.0 (iPad; CPU OS 17_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/128.0.6613.92 Mobile/15E148 Safari/604.1">Chrome 128 sur iOS 17.5 (iPad)</option>
									</optgroup>

									<optgroup label="Safari sur iOS">
										<option value="Mozilla/5.0 (iPhone; CPU iPhone OS 17_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1">Safari 17.5 sur iOS 17.5.1 (iPhone)</option>
									</optgroup>

									<optgroup label="Firefox sur macOS">
										<option value="Mozilla/5.0 (Macintosh; Intel Mac OS X 14.6; rv:129.0) Gecko/20100101 Firefox/129.0">Firefox 129 sur macOS 14.5</option>
									</optgroup>

									<optgroup label="Chrome sur Android">
										<option value="Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.6613.88 Mobile Safari/537.36">Chrome 128 sur Android 10</option>
									</optgroup>

									<optgroup label="HTTP Libraries / CLI">
										<option value="curl/8.12.0">cURL 8.12.0</option>
										<option value="Symfony HttpClient/7.2.3">Symfony HttpClient/7.2.3</option>
										<option value="python-requests/2.32.3">Python Requests 2.32.3</option>
									</optgroup>

									<optgroup label="Robots">
										<option value="Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.141 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)">Googlebot (Googlebot)</option>
										<option value="Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)">Googlebot (Googlebot)</option>
										<option value="Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)">Bingbot (Bingbot)</option>
									</optgroup>
								</select>
							</div>
						</div>

						<div class="row mb-3" title="User Agent personnalisé">
							<label for="inputUserAgentPerso" class="col-12 col-lg-3 col-form-label">User Agent personnalisé</label>
							<div class="col-12 col-lg-9"><input type="text" name="formAnalyseUserAgentPerso" class="form-control curseur" id="inputUserAgentPerso" placeholder="Modifier l’User Agent par défaut"></div>
						</div>

						<div class="row" title="HTTP Referer personnalisé">
							<label for="inputRefererPerso" class="col-12 col-lg-3 col-form-label">HTTP Referer personnalisé</label>
							<div class="col-12 col-lg-9"><input type="text" name="formAnalyseRefererPerso" class="form-control curseur" id="inputRefererPerso" placeholder="https://www.facebook.com/"></div>
						</div>
					</div>
				</div>
			</div>
		</form>

		<div style="display: none;" class="mb-5" id="chargementAnalyseWeb" title="Chargement…">
			<img src="/assets/img/chargement.svg" style="height: 60px;" class="d-flex mx-auto" alt="Chargement…">
			<p class="mb-0 text-center fw-bold">Chargement…</p>
		</div>

		<script>
		document.querySelector("#formAnalyse").addEventListener("submit", function(event) {
			document.querySelector("#chargementAnalyseWeb").style.display = "block";
		});

		document.querySelector("#optionsButton").addEventListener("click", function() {
			if (this.classList.contains("btn-outline-primary")) {
				this.classList.remove("btn-outline-primary");
				this.classList.add("btn-primary");
			} else {
				this.classList.remove("btn-primary");
				this.classList.add("btn-outline-primary");
			}
		});
		</script>

		<div>
			<p>Analyse complète d’adresses web : obtenez des informations techniques détaillées</p>

			<p>Découvrez notre outil avancé d’analyse web qui vous permet d’explorer les détails techniques et pratiques d’une adresse web. Avec notre service, vous pouvez :</p>

			<ul>
				<li>Analyser une adresse web avec des pptions personnalisées : simulez l’accès à une adresse en utilisant un <strong>User Agent</strong> spécifique, en sélectionnant un pays pour l’analyse, en personnalisant le <strong>Referer</strong> pour une exploration précise</li>
				<li>Effectuer une recherche <strong>Whois</strong> : Obtenez des informations complètes sur un nom de domaine ou une adresse IP, incluant le registar, les dates de création, d’expiration et de mise à jour, etc.</li>
				<li>Accéder un large éventail d’informations techniques sur l’URL et le serveur distant, y compris les configurations serveur, l’infrastructure réseau, et bien plus</li>
				<li>Découvrez si l’adresse IP du serveur distant est listée dans des bases de données connues, fait partie d’un <strong>nœud Bitcoin</strong>, est utilisée sur le <strong>réseau Tor</strong>, ou est présente dans d’autres listes de surveillance</li>
				<li>Afficher le code source totalement déployer coloré syntaxiquement</li>
				<li>Serveur DNS du nom de domaine</li>
			</ul>

			<p>Que vous soyez un administrateur réseau, un webmaster, ou simplement un curieux du web, notre page d’analyse vous offre quelques outils pour comprendre et diagnostiquer en n’importe quelle adresse web.</p>
		</div>

		<div class="col-12 col-lg-10 mt-4 mx-auto" id="dernieresAnalyses">';

			echo implode($dernieresAnalyses);

		echo '</div>';
	}

echo '</div>';

require_once $_SERVER['DOCUMENT_ROOT'].'a_footer.php';