<?php
if(isset($_GET['chargerCIDR']))
{
	require_once '../config/config.php';

	if(!empty($_POST['ipCidr']))
	{
		$sub = explode('/', $_POST['ipCidr']);

		$ip = (!empty($sub[0]) AND isIPv4($sub[0]))						? $sub[0] : null;
		$cidr = (!empty($sub[1]) AND $sub[1] >= 1 AND $sub[1] <= 32)	? $sub[1] : null;

		if(mb_strlen($_POST['ipCidr']) >= 9 AND mb_strlen($_POST['ipCidr']) <= 18)
		{
			if(!empty($ip))
			{
				if(!empty($cidr) AND filter_var($cidr, FILTER_VALIDATE_INT))
				{
					$sub = new IPv4\SubnetCalculator($sub[0], $sub[1]);

					// Various Network Information

					$numbeIpAddresses		= !empty($sub->getNumberIPAddresses())		? number_format($sub->getNumberIPAddresses(), 0, ',', ' ')		: 'ip inconnue';				// 512
					$numberHosts			= !empty($sub->getNumberAddressableHosts())	? number_format($sub->getNumberAddressableHosts(), 0, ',', ' ')	: 'ip inconnue';				// 510
					$addressRange			= !empty($sub->getIPAddressRange())			? $sub->getIPAddressRange()										: 'rangée d’ip inconnue';		// [192.168.112.0, 192.168.113.255]
					$addressableHostRange	= !empty($sub->getAddressableHostRange())	? $sub->getAddressableHostRange()								: 'rangée d’ip inconnue';		// [192.168.112.1, 192.168.113.254]
					$networkSize			= !empty($sub->getNetworkSize())			? number_format($sub->getNetworkSize(), 0, ',', ' ')			: 'ip inconnue';				// 23
					$broadcastAddress		= !empty($sub->getBroadcastAddress())		? $sub->getBroadcastAddress()									: 'ip inconnue';				// 192.168.113.255

					// IP Address

					$ipAddress				= !empty($sub->getIPAddress())				? $sub->getIPAddress()											: 'ip inconnue';				// 192.168.112.203
					$ipAddressQuads			= !empty($sub->getIPAddressQuads())			? $sub->getIPAddressQuads()										: 'ip inconnue';				// [192, 168, 112, 203]
					$ipAddressHex			= !empty($sub->getIPAddressHex())			? $sub->getIPAddressHex()										: 'ip hexadécimal inconnue';	// C0A870CB
					$ipAddressBinary		= !empty($sub->getIPAddressBinary())		? $sub->getIPAddressBinary()									: 'ip binaire inconnue';		// 11000000101010000111000011001011
					$ipAddressInteger		= !empty($sub->getIPAddressInteger())		? $sub->getIPAddressInteger()									: 'ip inconnue';				// 3232264395;

					// Reverse DNS Lookup (ARPA Domain)

					$ipv4ArpaDomain			= !empty($sub->getIPv4ArpaDomain())			? $sub->getIPv4ArpaDomain()										: 'domaine arpa inconnue';		// 203.112.168.192.in-addr.arpa

					echo '<div class="row mt-5">
						<div class="col-12 col-lg-6 mb-4">
							<ul class="list-group">
								<li class="list-group-item list-group-item-info">Informations de l’adresse IP</li>
								<li class="list-group-item d-flex justify-content-between align-items-center" title="1ère adresse IP du masque de sous-réseau">Adresse IP <span class="badge bg-primary rounded-pill">'.$ipAddress.'</span></li>
								<li class="list-group-item d-flex justify-content-between align-items-center" title="Adresse IP hexadécimal">Adresse IP en hexadécimal <span class="badge bg-primary rounded-pill">'.$ipAddressHex.'</span></li>
								<li class="list-group-item d-flex justify-content-between align-items-center" title="Adresse IP binaire">Adresse IP en binaire <span class="badge bg-primary rounded-pill">'.$ipAddressBinary.'</span></li>
								<li class="list-group-item d-flex justify-content-between align-items-center" title="Adresse IP en notation décimale">Adresse IP en notation décimale <span class="badge bg-primary rounded-pill">'.$ipAddressInteger.'</span></li>
							</ul>
						</div>
						<div class="col-12 col-lg-6 mb-4">
							<ul class="list-group">
								<li class="list-group-item list-group-item-info">Informations diverses sur le réseau</li>
								<li class="list-group-item d-flex justify-content-between align-items-center" title="Nombre d’adresse IP total contenues dans le masque">Nombre d’adresse IP <span class="badge bg-primary rounded-pill">'.$numbeIpAddresses.'</span></li>
								<li class="list-group-item d-flex justify-content-between align-items-center" title="Nombre d’hôte total contenus dans le masque">Nombre d’hôte <span class="badge bg-primary rounded-pill">'.$numberHosts.'</span></li>
								<li class="list-group-item d-flex justify-content-between align-items-center" title="Taille du réseau">Taille du réseau <span class="badge bg-primary rounded-pill">'.$networkSize.'</span></li>
								<li class="list-group-item d-flex justify-content-between align-items-center" title="Adresse de diffusion">Adresse de diffusion <span class="badge bg-primary rounded-pill">'.$broadcastAddress.'</span></li>
							</ul>
						</div>
						<div class="col-12">
							<ul class="list-group mb-4">
								<li class="list-group-item list-group-item-info">Liste de masque de sous-réseau</li>
								<li class="list-group-item d-flex justify-content-between align-items-center">
									Masque de sous-réseau de l’adresse IP
									<div class="float-end">
										<span class="badge bg-primary rounded-pill" title="1ère adresse IP du masque de sous-réseau">'.$addressRange[0].'</span> - <span class="badge bg-primary rounded-pill" title="Dernière adresse IP du masque de sous-réseau">'.$addressRange[1].'</span>
									</div>
								</li>
							</ul>
							<ul class="list-group mb-4">
								<li class="list-group-item list-group-item-info"><abbr title="Reverse DNS">Recherche DNS inversée</abbr> (domaine ARPA)</li>
								<li class="list-group-item d-flex justify-content-between align-items-center" title="Nom de domaine ARPA de l’adresse IP">Nom de domaine de l’adresse IP <span class="badge bg-primary rounded-pill">'.$ipv4ArpaDomain.'</span></li>
							</ul>
							<ul class="list-group">
								<li class="list-group-item list-group-item-info"><abbr title="Rapport brut">Rapport</li>
								<li class="list-group-item pb-0 pt-4">';
									p($sub->getPrintableReport());
								echo '</li>
							</ul>
						</div>
					</div>';
				}

				else
					echo alerte('danger', 'Plage IP incorrecte');
			}

			else
				echo alerte('danger', 'Plage IP incorrecte');
		}

		else
			echo alerte('danger', 'Plage IP incorrecte');
	}
}

else
{
require_once 'a_body.php';
?>
<div class="border rounded" id="cidr">
	<h1 class="mb-5 text-center"><a href="/calculer-ip-sous-reseau"><i class="fa-solid fa-network-wired"></i> <abbr title="Classless Inter-Domain Routing">CIDR</abbr> - Sous-réseau</a></h1>

	<form action="#cidr" method="post" id="cidrForm">
		<div class="row">
			<div class="col-12 col-lg-5 mx-auto">
				<div class="input-group input-group-thisip">
					<span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
					<input type="text" name="ipCidr" <?= !empty($_POST['ipCidr']) ? 'value="'.secuChars($_POST['ipCidr']).'"' : null; ?> class="form-control form-control-lg" id="formInputCIDR" minlength="9" maxlength="18" placeholder="142.250.201.174/32" autofocus required>
					<button type="submit" class="btn btn-primary" form="cidrForm">Valider</button>
				</div>
			</div>
		</div>
	</form>

	<div id="resultatsCIDR"></div>

	<div class="mt-5">
		<p>Un sous-réseau est une subdivision logique d'un réseau de taille plus importante. Le masque de sous-réseau permet de distinguer la partie de l'adresse commune à tous les appareils du sous-réseau et celle qui varie d'un appareil à l'autre. Un sous-réseau correspond typiquement à un réseau local sous-jacent.</p>

		<p>Historiquement, on appelle également sous-réseau chacun des réseaux connectés à Internet.</p>

		<p>La subdivision d'un réseau en sous-réseaux permet de limiter la propagation des broadcast, ceux-ci restant limités au réseau local et leur gestion étant coûteuse en bande passante et en ressource au niveau des commutateurs réseau. Les routeurs sont utilisés pour la communication entre les machines appartenant à des sous-réseaux différents.</p>

		<p class="m-0 text-center">
			[ <a href="https://fr.wikipedia.org/wiki/Sous-r%C3%A9seau" <?= $onclick; ?> title="Explication du sous-réseau sur Wikipédia"><i class="fa-brands fa-wikipedia-w text-dark"></i>Sous-réseau</a> ]
			[ <a href="https://aws.amazon.com/fr/what-is/cidr/" <?= $onclick; ?>><i style="color: #ff9900;" class="fa-brands fa-aws"></i> Qu'est-ce que le CIDR ?</a> ]
		</p>
	</div>
</div>
<?php
require_once 'a_footer.php';
}