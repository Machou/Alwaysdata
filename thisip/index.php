<?php
require_once 'a_body.php';

$grab = new Grab($pdo, $ip, getHttpUserAgent());
$infosArray = $grab->get_infos();

$grab->viderCache();

echo '<div class="border rounded mb-4" id="intro">
	<h1 class="mb-3"><a href="https://thisip.pw/">ThisIP.pw</a></h1>

	<p><strong>ThisIP</strong> est une boîte à outils polyvalente, conçue pour effectuer une variété d’actions sur les adresses IP. Parmi ses fonctionnalités principales, elle permet aux utilisateurs d’afficher des informations détaillées sur une adresse IP donnée. En outre, ThisIP offre la capacité de calculer les masques de sous-réseaux.</p>

	<p>Parmi ses autres atouts, ThisIP intègre des fonctionnalités telles que la possibilité de visualiser des dessins humoristiques inspirés du célèbre site <strong>xkcd</strong>, rendant l’expérience utilisateur à la fois instructive et divertissante. De plus, ThisIP propose un système de vérification des adresses de courriel.</p>

	<p class="mb-4 mb-lg-5">Une partie est spécialement dédiée à l’analyse des noms de domaine avec diverses options.</p>

	<form action="/" method="get" id="formChercherIp">
		<div class="row">
			<div class="col-12 col-lg-4 mx-auto mb-4 mb-lg-5">
				<div class="input-group input-group-thisip">
					<span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
					<input type="search" name="chercher_ip" class="form-control" minlength="7" maxlength="40" placeholder="Adresse IP" required>
					<button type="submit" class="btn btn-secondary" form="formChercherIp"><i class="fas fa-search"></i><span class="ms-2 d-none d-sm-inline-block">Chercher</span></button>
				</div>
			</div>
		</div>
	</form>

	<div class="col-12 col-lg-6 mx-auto text-center mb-3 mb-lg-0" data-bs-toggle="tooltip" data-bs-title="Adresse IP">
		<h1 class="rounded bg-warning p-3 fs-6"><a href="https://thisip.pw/ip/'.$ip.'" class="bg-transparent text-white fw-bold">'.$ip.'</a></h1>
	</div>

	<div class="text-end">
		Sortie : <span class="rounded bg-success p-2" title="Informations JSON"><i class="fa-solid fa-code"></i> <a href="https://thisip.pw/api/ip/json/'.$ip.'" class="text-white fw-bold">json</a></span>
	</div>
</div>';

if(!empty($ip) AND empty($infosArray['ERREUR']))
{
	echo '<div class="border rounded mb-4" id="ip">
		<h2 class="mb-4" id="informations-ip"><a href="#informations-ip" class="ancre"><i class="fa-solid fa-circle-nodes"></i> Informations IP</a></h2>

		<div class="row">';

		$i = 1;
		foreach($infosArray as $c => $v)
		{

		echo ($c === 'INFOS_CLIENT') ? '</div>
	</div>

	<div class="border rounded mb-4" id="informations-client">
		<div class="row">
			<h2 class="mb-4"><a href="#informations-client" class="ancre"><i class="fa-solid fa-globe"></i> Informations Client</a></h2>' : null;

			foreach($v as $cInfos => $vInfos)
			{

				if(in_array($cInfos, ['TIMEZONE', 'INFOS_IP', 'GEOLOCALISATION'], true))							{ $cols = 'col-12 col-lg-4';	$clrs = 'danger'; }
				elseif(in_array($cInfos, ['DETAILS_PAYS', 'LISTES']))												{ $cols = 'col-12 col-lg-6';	$clrs = 'danger'; }
				elseif(in_array($cInfos, ['INFOS_GLOBALE', 'GEOLOCALISATION_CLIENT', 'HTTP_SERVEUR', 'APPAREIL']))	{ $cols = 'col-12 col-lg-6';	$clrs = 'warning'; }
				else																								{ $cols = 'col-12 col-lg-3';	$clrs = 'danger'; }

				$titreSection = ucwords(mb_strtolower(str_replace('_', ' ', $cInfos)));
				$titreSectionSlug = slug($titreSection);

				echo ($i === 4) ? '</div><div class="row">' : null;

				echo '<div class="'.$cols.' mb-4">
					<div class="px-3 py-2 py-lg-3 border border-'.$clrs.'-subtle rounded-top bg-'.$clrs.'-subtle" id="'.$titreSectionSlug.'" title="Section '.$titreSection.'"><a href="#'.$titreSectionSlug.'" class="text-'.$clrs.'-emphasis text-decoration-none">'.$titreSection.'</a></div>
					<div class="border-start border-end bg-white rounded-bottom text-center">';

						foreach($vInfos as $cleGrab => $valeurGrab)
						{
							if(!empty($valeurGrab))
							{
								if($cleGrab !== 'WHOIS_IP')
								{
									$cleGrab = ucwords(mb_strtolower(str_replace('_', ' ', $cleGrab)));

									echo '<div class="row g-0 px-3 py-2 border-bottom">';

										// Gauche / Haut

										if(in_array($cleGrab, ['Nom Hote', 'Http User Agent', 'Http Accept']))		$cssGauche = 'col-12 mb-2';
										elseif($cleGrab === 'Adresse Ip' AND isIpv6($valeurGrab))					$cssGauche = 'col-12 mb-2';
										elseif($cleGrab === 'Nom Hote Long')										$cssGauche = 'col-12 mb-2';
										else																		$cssGauche = 'col-12 col-lg-6 text-lg-end mb-2 mb-lg-0 ps-1 pe-lg-1';

										echo '<div class="'.$cssGauche.'">'.$cleGrab.'</div>';

										// Droite / Bas

										if($valeurGrab == 'true')													$cssN = 'bg-success';
										elseif($valeurGrab == 'false')												$cssN = 'bg-danger';
										elseif($cleGrab == 'Ip Confiance' AND $valeurGrab == 'Parfait')				$cssN = 'bg-success';
										elseif($cleGrab == 'Ip Confiance' AND $valeurGrab == 'Moyen')				$cssN = 'bg-warning text-bg-warning';
										elseif($cleGrab == 'Ip Confiance' AND $valeurGrab == 'Danger')				$cssN = 'bg-danger';
										elseif($valeurGrab == 'n/a')												$cssN = 'bg-warning text-bg-warning';
										else																		$cssN = 'bg-success';

										$css = 'badge '.$cssN.' text-break text-wrap lh-base';

										if(in_array($cleGrab, ['Nom Hote', 'Http User Agent', 'Http Accept']))		$cssDroite = 'col-12';
										elseif($cleGrab === 'Adresse Ip' AND isIpv6($valeurGrab))					$cssDroite = 'col-12';
										elseif($cleGrab === 'Nom Hote Long')										$cssDroite = 'col-12';
										else																		$cssDroite = 'col-12 col-lg-6 text-lg-start mb-2 mb-lg-0 ps-1 pe-lg-1';

										echo '<div class="'.$cssDroite.'">
											<span class="'.$css.'">';

												if($cleGrab == 'Date')												echo '<time datetime="'.date(DATE_ATOM, time()).'">'.date('d-m-Y', time()).'</time>';
												elseif($cleGrab == 'Code Wikidata Url' AND $valeurGrab !== 'n/a')	echo '<a href="'.$valeurGrab.'" class="text-white" '.$onclick.'>Code Wikidata</a>';
												elseif($cleGrab == 'Google Maps Pays')								echo '<a href="'.$valeurGrab.'" class="text-white" '.$onclick.'><i class="fa-brands fa-google"></i> Maps Pays</a>';
												elseif($cleGrab == 'Google Maps Ville' AND $valeurGrab !== 'n/a')	echo '<a href="'.$valeurGrab.'" class="text-white" '.$onclick.'><i class="fa-brands fa-google"></i> Maps Ville</a>';
												elseif($cleGrab == 'Client Google Maps Ville')						echo '<a href="'.$valeurGrab.'" class="text-white" '.$onclick.'><i class="fa-brands fa-google"></i> Maps Ville Client</a>';
												elseif($cleGrab == 'Asn' AND $valeurGrab !== 'n/a')					echo '<a href="https://apps.db.ripe.net/db-web-ui/query?searchtext='.$valeurGrab.'" class="text-white" '.$onclick.'>'.$valeurGrab.'</a>';
												elseif($cleGrab == 'Bitcoin Nodes')									echo '<a href="geo/bitcoin_nodes.ipset" class="text-white">Bitcoin Nodes</a>'.aide('Liste des nœuds Bitcoin connectés, dans le monde entier.', ' ms-1');
												elseif($cleGrab == 'Firehol Level1')								echo '<a href="geo/firehol_level1.netset" class="text-white">Firehol Level2</a>'.aide('Liste noire des adresses IP indésirables offrant une protection maximale avec un minimum de faux positifs.', ' ms-1');
												elseif($cleGrab == 'Firehol Level2')								echo '<a href="geo/firehol_level2.netset" class="text-white">Firehol Level2</a>'.aide('Liste noire des adresses IP indésirables offrant une protection maximale avec un minimum de faux positifs.', ' ms-1');
												elseif($cleGrab == 'Firehol Level3')								echo '<a href="geo/firehol_level3.netset" class="text-white">Firehol Level2</a>'.aide('Liste noire des adresses IP indésirables offrant une protection maximale avec un minimum de faux positifs.', ' ms-1');
												elseif($cleGrab == 'Firehol Level4')								echo '<a href="geo/firehol_level4.netset" class="text-white">Firehol Level2</a>'.aide('Liste noire des adresses IP indésirables offrant une protection maximale avec un minimum de faux positifs.', ' ms-1');
												elseif($cleGrab == 'Ip Stop Forum Spam')							echo '<a href="geo/stopforumspam.ipset" class="text-white">StopSpamForum.com</a>'.aide('Liste noire des adresses IP par StopForumSpam.com', ' ms-1');
												elseif($cleGrab == 'Ip Tor')										echo '<a href="geo/Tor_ip_list_ALL.csv" class="text-white">Tor</a>'.aide('Liste des adresses IP des de tous les points de sortie Tor - TorProject.org', ' ms-1');
												elseif($cleGrab == 'Ip Tor Sortie')									echo '<a href="geo/Tor_ip_list_EXIT.csv" class="text-white">Tor Sortie</a>'.aide('Liste des adresses IP des nœuds de sortie du serveur Tor - TorProject.org', ' ms-1');
												else																echo secuChars($valeurGrab);

											echo '</span>
										</div>
									</div>';
								}
							}
						}

					echo '</div>
				</div>';

				echo ($i === 5) ? '</div><div class="row">' : null;
				$i++;
			}
		}

		$monPcVars = [
			'WH'			=> ["<script>updateElement('#WH', screen.width + ' / ' + screen.height);</script>",				'Longueur / Largeur écran'],
			'aWH'			=> ["<script>updateElement('#aWH', screen.availWidth + ' / ' + screen.availHeight);</script>",	'Longueur / Largeur écran disponibles'],
			'isExtended'	=> ["<script>updateElement('#isExtended', screen.isExtended);</script>",						'Plusieurs écrans ?'],
			'colorDepth'	=> ["<script>updateElement('#colorDepth', screen.colorDepth);</script>",						'Couleurs écran'],
			'orientation'	=> ["<script>updateElement('#orientation', screen.orientation.type);</script>",					'Orientation de l’écran'],
			'pixelDepth'	=> ["<script>updateElement('#pixelDepth', screen.pixelDepth);</script>",						'Profondeur de bit de l’écran'],
			'cookieEnabled'	=> ["<script>updateElement('#cookieEnabled', navigator.cookieEnabled);</script>",				'Cookies activés ?'],
			'jsActive'		=> ["<script>updateElement('#jsActive', Boolean(10 > 9));</script>",							'JavaScript activé ?'],
			'javaEnabled'	=> ["<script>updateElement('#javaEnabled', window.navigator.javaEnabled());</script>",			'Java activé ?'],
		];

		echo '</div>
	</div>

	<div class="border rounded mb-4" id="details-navigateur">
		<h2 class="mb-4"><a href="#details-navigateur" class="ancre"><i class="fa-solid fa-earth-europe"></i> Détails Navigateur</a></h2>

		<div class="bg-white rounded text-center">
			<noscript><p class="text-danger fw-bold fs-4">Erreur : JavaScript est désactivé dans votre navigateur. Veuillez l’activer pour afficher correctement cette page.</p></noscript>

			<div class="row g-0 px-3 py-2 border-bottom">
				<div class="col-12 col-lg-6 text-lg-end mb-2 mb-lg-0 pe-lg-0 pe-lg-2">FingerprintJS</div>
				<div class="col-12 col-lg-6 text-lg-start ps-lg-0 ps-lg-2"><span class="badge bg-success text-break text-wrap lh-base" id="visitorId"></span></div>
			</div>
			<div class="row g-0 px-3 py-2 border-bottom">
				<div class="col-12 col-lg-6 text-lg-end mb-2 mb-lg-0 pe-lg-0 pe-lg-2">Navigation privée ?</div>
				<div class="col-12 col-lg-6 text-lg-start ps-lg-0 ps-lg-2" id="directInco"></div>
			</div>
			<div class="row g-0 px-3 py-2 border-bottom">
				<div class="col-12 col-lg-6 text-lg-end mb-2 mb-lg-0 pe-lg-0 pe-lg-2">Quel GPU ?</div>
				<div class="col-12 col-lg-6 text-lg-start ps-lg-0 ps-lg-2"><span class="badge bg-success text-break text-wrap lh-base" id="gpuInfos"></span></div>
			</div>

			<script>
			"use strict";function asyncGeneratorStep(n,e,r,t,a,o,c){try{var s=n[o](c),u=s.value}catch(i){r(i);return}s.done?e(u):Promise.resolve(u).then(t,a)}function _asyncToGenerator(n){return function(){var e=this,r=arguments;return new Promise(function(t,a){var o=n.apply(e,r);function c(n){asyncGeneratorStep(o,t,a,c,s,"next",n)}function s(n){asyncGeneratorStep(o,t,a,c,s,"throw",n)}c(void 0)})}}_asyncToGenerator(regeneratorRuntime.mark(function n(){var e;return regeneratorRuntime.wrap(function n(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,DetectGPU.getGPUTier();case 2:e=r.sent,document.querySelector("#gpuInfos").innerHTML=e.gpu+" ("+e.fps+" fps)";case 4:case"end":return r.stop()}},n)}))();

			function updateElement (selector, condition) {
				const element = document.querySelector(selector);
				const span = document.createElement("span");

				if (condition === "portrait-primary")			condition = "Portrait primaire 90° (portrait-primary)";
				else if (condition === "portrait-secondary")	condition = "Portrait secondaire 270° (portrait-secondary)";
				else if (condition === "landscape-primary")		condition = "Paysage primaire 0° (landscape-primary)";
				else if (condition === "landscape-secondary")	condition = "Paysage secondaire 180° (landscape-secondary)";
				else if (condition === "natural")				condition = "Naturel (natural)";
				else if (condition === "landscape")				condition = "Paysage (landscape)";
				else if (condition === "portrait")				condition = "Portrait (portrait)";
				else if (condition === undefined)				condition = false;
				else											condition = condition;

				span.className = "badge bg-" + (condition ? "success" : "danger") + " text-break text-wrap lh-base";
				span.innerText = condition;

				element.innerHTML = "";
				element.appendChild(span);
			}

			// https://github.com/fingerprintjs/fingerprintjs

			FingerprintJS.load().then(agent =>
				agent.get()
			).then(result => {
				document.querySelector("#visitorId").innerHTML = result.visitorId;
			});

			// https://github.com/Joe12387/detectIncognito

			let exports = {};
			function getModeName(browserName) {
				switch (browserName) {
					case "Safari":
					case "Firefox":
					case "Brave":
					case "Opera":
					return "a Private Window";
					break;
					case "Chrome":
					case "Chromium":
					return "an Incognito Window";
					break;
					case "Internet Explorer":
					case "Edge":
					return "an InPrivate Window";
					break;
				}

				throw new Error("Could not get mode name");
			}

			// This function is called when the script is loaded
			function detect() {
				let a = document.querySelector("#directInco");

				// We call the detectIncognito function and handle the promise
				detectIncognito().then(function(result) {
					if (result.isPrivate) { // If the result is private, we display a message to the user
						a.innerHTML = "<span class=\'badge bg-success text-break text-wrap lh-base\'>Oui</span>";
					} else { // If the result is not private, we display a message to the user
						a.innerHTML = "<span class=\'badge bg-danger text-break text-wrap lh-base\'>Non</span>";
					}
				}).catch(function(error) { // If there is an error, we display a message to the user & log the error to console
					a.innerHTML = "Erreur. Vérifier la console pour plus d’infos.";
					console.error(error);
				});
			}

			let script = document.createElement("script"); // To handle the CDN being blocked by adblockers, we load the script using createElement
			script.onload = detect; // We then set the onload and onerror events to detect whether the script was loaded successfully

			// If the script fails to load, we display a message to the user
			script.onerror = function () {
				let a = document.querySelector("#directInco");
				a.innerHTML = "Le script n’a pas pu être chargé à partir du CDN";

				if (navigator.brave !== undefined) {
					a.innerHTML += "Si vous utilisez Brave, désactivez les boucliers en cliquant sur l’icône Brave à droite de la barre d’adresse et réessayez.";
				} else {
					a.innerHTML += "Si vous utilisez un bloqueur de publicité, veuillez le désactiver et réessayer.";
				}

				a.innerHTML += "Si le problème persiste, veuillez <a href=\'https://github.com/Joe12387/detectIncognito/issues\'>rapporter le problème</a> sur GitHub.";
			};

			script.src = "/assets/js/di.js?160";
			document.body.appendChild(script);
			</script>';

			foreach($monPcVars as $monPcVar => $monPcVarValeur)
			{
				echo '<div class="row g-0 px-3 py-2 border-bottom">
					<div class="col-12 col-lg-6 text-lg-end mb-2 mb-lg-0 pe-lg-0 pe-lg-2">'.$monPcVarValeur[1].'</div>
					<div class="col-12 col-lg-6 text-lg-start ps-lg-0 ps-lg-2" id="'.$monPcVar.'">'.$monPcVarValeur[0].'</div>
				</div>';
			}

		echo '</div>
	</div>

	<div class="border rounded mb-4" id="userAgent">
		<h2 class="mb-4"><a href="#userAgent" class="ancre"><i class="fa-solid fa-display"></i> User Agent</a></h2>

		<div class="bg-white rounded text-center p-3">
			<div class="col-12 col-lg-8 mx-auto mb-4 p-3 fw-bold border border-success-subtle rounded bg-success-subtle text-success-emphasis rounded curseur">
				<p class="m-0" id="btn-copie-ua" data-clipboard-text="" data-bs-toggle="tooltip" data-bs-title="Mon User Agent"><span id="ua"></span></p>
			</div>

			<div class="col-12 col-lg-4 px-2 mx-lg-auto">
				<ul class="list-group">';

					$uaArray = [
						'browser-name'		=> 'Nom du navigateur',
						'browser-version'	=> 'Version du navigateur',
						'cpu'				=> 'Type de CPU',
						'device-model'		=> 'Modèle de l’appareil',
						'device-type'		=> 'Type de l’appareil',
						'device-vendor'		=> 'Fabricant de l’appareil',
						'os-name'			=> 'Nom de l’OS',
						'os-version'		=> 'Version de l’OS'
					];

					foreach($uaArray as $idUa => $vUa)
					{
						echo '<li class="list-group-item list-group-item-success d-flex justify-content-between align-items-center">
							'.$vUa.'
							<span class="badge text-bg-success rounded-pill curseur" data-bs-toggle="tooltip" data-bs-title="'.$vUa.'" id="'.$idUa.'"></span>
						</li>';
					}

				echo '</ul>

				<script>
				const uap = new UAParser();
				const result = uap.getResult();

				document.querySelector("#ua").innerText = result.ua;
				document.querySelector("#btn-copie-ua").setAttribute("data-clipboard-text", result.ua);

				document.addEventListener("DOMContentLoaded", function() {
					let clipboard = new ClipboardJS("#btn-copie-ua");

					clipboard.on("success", function(e) {
						let tooltipTitleUa = "Copié !";
						let existingTooltipUa = bootstrap.Tooltip.getInstance(e.trigger);

						if (!existingTooltipUa) {
							existingTooltipUa = new bootstrap.Tooltip(e.trigger, {
								title: tooltipTitleUa,
								trigger: "manual"
							});
						} else {
							existingTooltipUa.setContent({ ".tooltip-inner": tooltipTitleUa });
						}

						existingTooltipUa.show();

						setTimeout(() => {
							existingTooltipUa.hide();
						}, 3000);

						e.clearSelection();
					});

					clipboard.on("error", function(e) {
						console.error("Erreur lors de la copie");
					});
				});

				document.querySelector("#browser-name").innerText = (result.browser.name !== undefined) ? result.browser.name : "inconnu";
				document.querySelector("#browser-version").innerText = (result.browser.version !== undefined) ? result.browser.version : "inconnu";

				document.querySelector("#cpu").innerText = (result.cpu.architecture !== undefined) ? result.cpu.architecture : "inconnu";

				document.querySelector("#device-model").innerText = (result.device.model !== undefined) ? result.device.model : "inconnu";
				document.querySelector("#device-type").innerText = (result.device.type !== undefined) ? result.device.type : "inconnu";
				document.querySelector("#device-vendor").innerText = (result.device.vendor !== undefined) ? result.device.vendor : "inconnu";

				const osReal = uap.getOS();
				uap.getOS().withClientHints().then(osReal => {
					document.querySelector("#os-name").innerText = (osReal.name !== undefined) ? osReal.name : "inconnu";
					document.querySelector("#os-version").innerText = (osReal.version !== undefined) ? osReal.version : "inconnu";
				});
				</script>
			</div>
		</div>
	</div>

	<div class="border rounded mb-4" id="whoisIP">
		<h2 class="mb-4"><a href="#whoisIP" class="ancre"><i class="fa-solid fa-globe"></i> Whois IP Client</a></h2>

		<div class="bg-white rounded text-center p-3">
			<pre class="py-0">'.$infosArray['DISTANT']['INFOS_IP']['WHOIS_IP'].'</pre>
		</div>
	</div>

	<div class="border rounded mb-4" id="ports">
		<h2 class="mb-4"><a href="#ports" class="ancre"><i class="fa-solid fa-network-wired"></i> Ports réseaux ouverts</a></h2>

		<div class="border-start border-end bg-white rounded text-center p-2">
			<button class="btn btn-primary my-3" id="chargerPorts" data-ip="'.$ip.'">Afficher les ports ouverts</button>

			<div id="ports-js"></div>

			<div class="chargement"><img src="/assets/img/chargement.svg" style="display: none;" class="mt-3" id="chargement" alt="Chargemement…" title="Chargement…"></div>
		</div>
	</div>

	<div class="border rounded mb-4" id="carte">
		<h2 class="mb-4"><a href="#carte" class="ancre"><i class="fa-regular fa-map"></i> Carte</a></h2>

		<div class="border-start border-end bg-white rounded text-center p-2">
			<noscript><p class="text-danger fw-bold fs-4">Erreur : JavaScript est désactivé dans votre navigateur. Veuillez l’activer pour afficher correctement cette page.</p></noscript>';

			if(!empty($infosArray['DISTANT']['GEOLOCALISATION']['LATITUDE_VILLE']))																					$latitude = secuChars($infosArray['DISTANT']['GEOLOCALISATION']['LATITUDE_VILLE']);
			elseif(empty($infosArray['DISTANT']['GEOLOCALISATION']['LATITUDE_VILLE']) AND !empty($infosArray['DISTANT']['GEOLOCALISATION']['LATITUDE_PAYS']))		$latitude = secuChars($infosArray['DISTANT']['GEOLOCALISATION']['LATITUDE_PAYS']);
			elseif(!empty($infosArray['INFOS_CLIENT']['GEOLOCALISATION_CLIENT']['CLIENT_LATITUDE_VILLE']))															$latitude = secuChars($infosArray['INFOS_CLIENT']['GEOLOCALISATION_CLIENT']['CLIENT_LATITUDE_VILLE']);
			else																																					$latitude = null;

			if(!empty($infosArray['DISTANT']['GEOLOCALISATION']['LONGITUDE_VILLE']))																				$longitude = secuChars($infosArray['DISTANT']['GEOLOCALISATION']['LONGITUDE_VILLE']);
			elseif(empty($infosArray['DISTANT']['GEOLOCALISATION']['LONGITUDE_VILLE']) AND !empty($infosArray['DISTANT']['GEOLOCALISATION']['LONGITUDE_PAYS']))		$longitude = secuChars($infosArray['DISTANT']['GEOLOCALISATION']['LONGITUDE_PAYS']);
			elseif(!empty($infosArray['INFOS_CLIENT']['GEOLOCALISATION_CLIENT']['CLIENT_LONGITUDE_VILLE']))															$longitude = secuChars($infosArray['INFOS_CLIENT']['GEOLOCALISATION_CLIENT']['CLIENT_LONGITUDE_VILLE']);
			else																																					$longitude = null;

			if(!empty($latitude) AND !empty($longitude) AND $longitude !== 'n/a' AND $longitude !== 'n/a')
			{
				$asn = !empty($infosArray['DISTANT']['INFOS_IP']['ASN_ORGANISATION'])								? secuChars($infosArray['DISTANT']['INFOS_IP']['ASN_ORGANISATION']).'<br><br>'					: null;

				if(!empty($infosArray['DISTANT']['GEOLOCALISATION']['VILLE']))										$ville = secuChars($infosArray['DISTANT']['GEOLOCALISATION']['VILLE']);
				elseif(!empty($infosArray['INFOS_CLIENT']['GEOLOCALISATION_CLIENT']['CLIENT_VILLE']))				$ville = secuChars($infosArray['INFOS_CLIENT']['GEOLOCALISATION_CLIENT']['CLIENT_VILLE']);
				else																								$ville = null;

				if(!empty($infosArray['DISTANT']['GEOLOCALISATION']['GOOGLE_MAPS_VILLE']))							$gMaps = secuChars($infosArray['DISTANT']['GEOLOCALISATION']['GOOGLE_MAPS_VILLE']);
				elseif(!empty($infosArray['INFOS_CLIENT']['GEOLOCALISATION_CLIENT']['CLIENT_GOOGLE_MAPS_VILLE']))	$gMaps = secuChars($infosArray['INFOS_CLIENT']['GEOLOCALISATION_CLIENT']['CLIENT_GOOGLE_MAPS_VILLE']);
				else																								$gMaps = null;

				echo '<div id="map"></div>

				<script>
				// https://leafletjs.com/
				let map = L.map("map").setView(['.$latitude.', '.$longitude.'], '.(!empty($infosArray['INFOS_CLIENT']['GEOLOCALISATION_CLIENT']['CLIENT_LATITUDE_VILLE']) ? 13 : 6).');
				let tiles = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
					attribution: "&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> - &copy; <a href=\"https://thisip.pw/\">ThisIP.pw</a>",
				}).addTo(map);

				let infos = "'.$asn.'<strong>'.$ip.'</strong>";
				'.(!empty($ville) ? 'infos += "<br><br><strong>'.$ville.'</strong>"' : null).'
				'.(!empty($gMaps) ? 'infos += "<br><br><a href=\"'.$gMaps.'\" target=\"_blank\">Google Maps</a>"' : null).'

				let faIcon = L.divIcon({
					html: "<i class=\"fa-solid fa-location-dot\" style=\"color: rgba(65, 65, 145, 1); font-size: 2.5rem;\"></i>",
					className: "fa-icon-marker",
					iconSize: [50, 50],
					iconAnchor: [20, 20],
					popupAnchor: [5, -20]
				});

				L.marker(['.$latitude.', '.$longitude.'], { icon: faIcon }).addTo(map).bindPopup(infos).openPopup();
				</script>';
			}

			else
				echo alerte('danger', 'La carte n’est pas disponible');

		echo '</div>
	</div>';
}

else
	echo '<div class="border rounded">
		<div class="row">'.alerte('danger', 'Adresse IP incorrecte').'</div>
	</div>';

require_once 'a_footer.php';