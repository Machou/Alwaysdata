<?php
require_once 'a_body.php';

/*
Quelle est la différence entre un serveur DNS de référence et un résolveur DNS récursif ?
https://www.cloudflare.com/fr-fr/learning/dns/what-is-dns/

Menu à gauche

https://worldofwarcraft.judgehype.com/article/meilleurs-addons-wow-pour-ameliorer-l-interface/
*/
?>
<div class="border rounded" id="dns">
	<h1 class="mb-5 text-center"><a href="/dns"><i class="fa-solid fa-robot"></i> DNS</a></h1>

	<h3>Sommaire</h3>

	<ul>
		<li><a href="#qu-est-ce-qu-un-dns">Qu’est-ce qu’un DNS ?</a></li>
		<li>
			<a href="#fonctionnement-des-dns">Fonctionnement des DNS</a>
			<ul>
				<li><a href="#qu-est-ce-qu-un-resolveur-recursif">Qu’est-ce qu’un résolveur récursif DNS ?</a></li>
				<li><a href="#qu-est-ce-qu-un-dns-racine">Qu’est-ce qu’un DNS racine ?</a></li>
				<li><a href="#qu-est-ce-qu-un-serveur-tld">Qu’est-ce qu’un serveur TLD ?</a></li>
				<li><a href="#qu-est-ce-qu-un-nom-de-reference">Qu’est-ce qu’un nom de référence ?</a></li>
				<li>
					<a href="#dns-mx-pour-gestion-des-courriels">Les DNS MX pour la gestion des courriels</a>
					<ul>
						<li><a href="#qu-est-ce-qu-un-dns-mx">Qu’est-ce qu’un DNS MX ?</a></li>
						<li>
							<a href="#qu-est-ce-que-le-spf">Qu’est-ce que le SPF ?</a>
							<ul>
								<li><a href="#comment-fonctionne-spf">Comment fonctionne le SPF ?</a></li>
								<li><a href="#avantages-spf">Avantages du SPF</a></li>
								<li><a href="#exemple-enregistrement-spf">Exemple d'un enregistrement SPF</a></li>
								<li><a href="#points-importants-lors-de-la-mise-en-oeuvre-du-spf">Points importants lors de la mise en œuvre du SPF</a>
								<li><a href="#combinaison-avec-dkim-et-dmarc">Combinaison avec DKIM et DMARC</a>
								<li><a href="#comment-configurer-un-enregistrement-sfp">Comment configurer un enregistrement SPF</a>

							</ul>
						</li>
						<li>
							<a href="#qu-est-ce-que-dkim">Qu’est-ce que DKIM ?</a>
							<ul>
								<li><a href="#comment-fonctionne-dkim">Comment fonctionne DKIM ?</a></li>
								<li><a href="#avantages-dkim">Avantages DKIM</a></li>
								<li><a href="#integration-avec-spf-dmarc">Intégration avec SPF et DMARC</a></li>
								<li><a href="#exemple-enregistrement-dkim-dans-le-DNS">Exemple d'un enregistrement DKIM dans le DNS</a></li>
							</ul>
						</li>
						<li>
							<a href="#qu-est-ce-que-dmarc">Qu’est-ce que DMARC ?</a>
							<ul>
								<li><a href="#comment-fonctionne-dmarc">Comment fonctionne DMARC ?</a></li>
								<li><a href="#avantages-dmarc">Avantages DMARC</a></li>
								<li><a href="#exemple-enregistrement-dmarc">Exemple d'un enregistrement DMARC</a></li>
								<li><a href="#etapes-pour-implementer-dmarc">Étapes pour implémenter DMARC</a></li>
								<li><a href="#alignement-dans-dmarc">Alignement dans DMARC</a></li>
								<li><a href="#integration-avec-spf-et-dkim">Intégration avec SPF et DKIM</a></li>
							</ul>
						</li>
					</ul>
				</li>
				<li>
					<a href="#qu-est-ce-qu-un-dns-txt">Qu’est-ce qu’un DNS TXT ?</a>
					<ul>
						<li><a href="#fonctionnalites-princiaples-des-enregistrements-dns-txt">Fonctionnalités principales des enregistrements DNS TXT</a></li>
						<li><a href="#exemples-enregistrement-dns-txt">Exemples d'enregistrements DNS TXT</a></li>
						<li><a href="#pourquoi-les-enregistrement-txt-sont-importants">Pourquoi les enregistrements TXT sont importants</a></li>
						<li><a href="#comment-ajouter-enregistrement-dns-txt">Comment ajouter un enregistrement DNS TXT</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li>
			<a href="#changer-dns">Comment changer mes DNS ?</a>
			<ul>
				<li><a href="#windows-10-11" style="color: #0278d4;"><i class="fa-brands fa-windows"></i> Windows 10 et 11</a></li>
				<li><a href="#windows-xp-vista-7-8-10-11" style="color: #0278d4;"><i class="fa-brands fa-windows"></i> Windows XP, Vista, 7, 8, 8.1, 10 et 11</a></li>
				<li><a href="#windows-10-11-powershell" style="color: #0278d4;"><i class="fa-brands fa-windows"></i> Windows 10 et 11 (PowerShell)</a></li>
				<li><a href="#windows-xp-vista-7-8-10-11-cmd" style="color: #0278d4;"><i class="fa-brands fa-windows"></i> Windows XP, Vista, 7, 8, 8.1, 10 et 11 (invite de commandes)</a></li>
				<li>
					<a href="#debian-cli" style="color: #a80030;"><i class="fa-brands fa-debian"></i> Debian (CLI)</a>
					<ul>
						<li><a href="#debian-methode-1">Méthode 1 : modification du fichier /etc/resolv.conf</a></li>
						<li><a href="#debian-methode-2">Méthode 2 : configuration via /etc/network/interfaces</a></li>
						<li><a href="#debian-methode-3">Méthode 3 : utilisation de nmcli (pour NetworkManager)</a></li>
					</ul>
				</li>
				<li>
					<a href="#ubuntu" style="color: #e95420;"><i class="fa-brands fa-ubuntu"></i> Ubuntu</a>
					<ul>
						<li><a href="#ubuntu-methode-1">Méthode 1 : via Fichier de configuration</a></li>
						<li><a href="#ubuntu-methode-2">Méthode 2 : via l’interface graphique</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li><a href="#dns-prives">DNS privés</a></li>
		<li><a href="#dns-fai">DNS des FAI</a></li>
	</ul>

	<h2 class="mb-4 mt-5" id="qu-est-ce-qu-un-dns"><a href="#qu-est-ce-qu-un-dns" class="ancre"><i class="fa-solid fa-link"></i> Qu’est-ce qu’un DNS ?</a></h2>

	<div>
		<p>Les <strong>DNS</strong> (Domain Name System, ou système de noms de domaine) jouent un rôle crucial en tant que répertoire de l’Internet, en facilitant la navigation sur le web. En permettant la traduction des noms de domaine, tels que <em>nytimes.com</em> ou <em>wikipedia.org</em>, en adresses IP numériques que les navigateurs peuvent comprendre, le DNS élimine le besoin pour les utilisateurs de mémoriser des adresses IP complexes, qu’elles soient en format IPv4 comme <code>192.168.1.1</code>, ou en format plus récent et alphanumérique d’IPv6, comme <code>2400:cb00:2048:1::c629:d7a2</code>.</p>

		<p>Créé par <strong>Jon Postel</strong> et <strong>Paul Mockapetris</strong> en 1983 sous la direction de la <abbr title="Defense Advanced Research Projects Agency">DARPA</abbr>, le DNS a évolué pour devenir un service distribué fondamental au développement de l’Internet.</p>

		<p>Il est important de noter que les DNS sont gérés par une infrastructure mondiale de serveurs DNS, qui travaillent ensemble pour assurer une connectivité continue sur Internet. Cette infrastructure est répartie en différents niveaux, appelés zones DNS, qui sont gérés par différents organismes et entreprises.</p>

		<p class="mb-0">En résumé, les DNS contribue de manière significative à la fluidité et à l’efficacité des communications en ligne, soutenant ainsi l’expansion continue du réseau Internet mondial.</p>
	</div>

	<figure class="mb-0 mt-5 text-center">
		<blockquote class="blockquote"><a href="assets/img/tutoriels/dns-all-dns-record-types.png" data-fancybox="gallerie"><img src="assets/img/tutoriels/dns-all-dns-record-types.png" class="col-0 col-lg-6 img-fluid border rounded col-12 col-lg-6" alt="DNS - un aperçu graphique de tous les types d'enregistrements DNS actifs"></a></blockquote>
		<figcaption class="blockquote-footer mb-0 text-end">Les DNS; piliers de l’Internet<br>Source : <a href="https://www.nslookup.io/learning/dns-record-types/">NsLookup.io</a></figcaption>
	</figure>

	<h2 class="mb-4 mt-5" id="fonctionnement-des-dns"><a href="#fonctionnement-des-dns" class="ancre"><i class="fa-solid fa-link"></i> Fonctionnement des DNS</a></h2>

	<div>
		<a href="assets/img/tutoriels/dns-image-2-hierarchie-dns.svg"><img src="assets/img/tutoriels/dns-image-2-hierarchie-dns.svg" class="float-end col-0 col-lg-4 img-fluid border border-2 rounded ms-lg-3 mb-3 p-1" alt="DNS - résolution itérative d’un nom dans le DNS" data-fancybox="gallerie"></a>

		<p>Le processus de résolution DNS est essentiel pour le fonctionnement d’Internet, car il transforme un nom d’hôte, tel que <code>thisip.pw</code>, en une adresse IP « au format informatique », comme <code>156.169.42.13</code>. Cette adresse IP unique est attribuée à chaque appareil connecté à Internet, jouant un rôle similaire à celui d’une adresse postale en permettant de localiser et d’identifier un appareil sur le réseau mondial.</p>

		<p>Lorsqu’un utilisateur souhaite accéder à une page web, il saisit un nom de domaine dans son navigateur. Le DNS intervient alors pour traduire ce nom en une adresse IP compréhensible par les machines, permettant ainsi au navigateur de localiser le serveur hébergeant la page web souhaitée. Ce processus de traduction est crucial pour la navigation web fluide et rapide.</p>

		<p>La résolution DNS implique plusieurs composants clés qui travaillent ensemble pour acheminer la requête. Parmi eux, on trouve le résolveur DNS, qui est généralement fourni par le fournisseur d’accès Internet (FAI) de l’utilisateur, les serveurs racine, qui dirigent la requête vers les serveurs de noms de domaine de niveau supérieur (TLD), et enfin les serveurs de noms autoritaires, qui contiennent les enregistrements DNS finaux nécessaires pour résoudre le nom de domaine en adresse IP.</p>

		<p>Lors du chargement d’une page web, quatre serveurs DNS jouent un rôle crucial :</p>

		<ol>
			<li>Le <a href="#qu-est-ce-qu-un-resolveur-recursif" class="text-dark"><strong>récurseur DNS</strong></a> reçoit la requête du client et commence la recherche</li>
			<li>Le <a href="#qu-est-ce-qu-un-dns-racine" class="text-dark"><strong>serveur racine DNS</strong></a> est consulté pour obtenir des informations sur le domaine de premier niveau (TLD)</li>
			<li>Le <a href="#qu-est-ce-qu-un-dns-tld" class="text-dark"><strong>serveur DNS TLD</strong></a> est alors contacté pour localiser le serveur de noms faisant autorité pour le domaine spécifique</li>
			<li>Le <a href="#qu-est-ce-qu-un-nom-de-reference" class="text-dark"><strong>serveur de nom de référence</strong></a> faisant autorité fournit l’adresse IP de la page web demandé</li>
		</ol>

		<p class="mb-4">Pour l’utilisateur final, la résolution DNS se déroule en arrière-plan et ne nécessite aucune interaction supplémentaire après la saisie de l’adresse web. Le navigateur initie une requête DNS qui traverse ces différents composants, sans que l’utilisateur ne s’en rende compte. La rapidité et l’efficacité de ce processus sont essentielles pour garantir une expérience utilisateur fluide, permettant aux pages web de se charger rapidement et de manière transparente. Ainsi, le DNS joue un rôle fondamental en facilitant l’accès à l’immense quantité de ressources disponibles sur Internet.</p>

		<div class="text-center">
			<a href="assets/img/tutoriels/dns-image-3-resolution-iterative-dns.svg" data-fancybox="gallerie"><img src="assets/img/tutoriels/dns-image-3-resolution-iterative-dns.svg" class="img-fluid border rounded col-0 col-lg-6" alt="DNS - résolution itérative d’un nom dans le DNS"></a>
			<p style="font-size: .85rem;" class="mb-0 mt-2 text-black-50">Résolution itérative d’un nom dans le DNS par un serveur DNS (étapes 2 à 7) et réponse (étape 8) suite à l’interrogation récursive (étape 1) effectuée par un client (resolver) DNS<br>Source : <a href="https://fr.wikipedia.org/wiki/Domain_Name_System">Wikipédia</a></p>
		</div>
	</div>

	<h3 class="mb-4 mt-5" id="qu-est-ce-qu-un-resolveur-recursif"><a href="#qu-est-ce-qu-un-resolveur-recursif" class="ancre"><i class="fa-solid fa-link"></i> Qu’est-ce qu’un résolveur récursif DNS ?</a></h3>

	<div>
		<p>Un résolveur récursif, également connu sous le nom de récurseur DNS, est le point de départ d’une requête DNS. Il fonctionne comme un intermédiaire entre un client et un serveur de noms DNS. Lorsqu’il reçoit une requête DNS d’un client web, le résolveur récursif peut répondre avec des données mises en cache ou transmettre la requête à un serveur de noms racine. Ensuite, il envoie une requête à un serveur de noms TLD (domaine de premier niveau), suivie d’une requête finale à un serveur de noms faisant autorité. Une fois qu’il obtient la réponse du serveur de noms faisant autorité avec l’adresse IP demandée, le résolveur récursif transmet cette information au client.</p>

		<p>Pendant ce processus, le résolveur récursif conserve en cache les informations provenant des serveurs de noms faisant autorité. Ainsi, si un client demande l’adresse IP d’un nom de domaine récemment demandé par un autre client, le résolveur peut éviter de contacter à nouveau les serveurs de noms et fournir directement l’enregistrement depuis son cache.</p>

		<p class="mb-4">La plupart des utilisateurs d’Internet utilisent un résolveur récursif fourni par leur fournisseur d’accès à Internet (FAI), mais il existe d’autres options, comme le résolveur 1.1.1.1 de Cloudflare.</p>

		<p class="text-center"><a href="assets/img/tutoriels/dns-image-4-recurseur-dns.png"><img src="assets/img/tutoriels/dns-image-4-recurseur-dns.png" class="img-fluid border rounded col-12 col-lg-6" alt="Résolveur DNS récursif" data-fancybox="gallerie"></a>
	</div>

	<h3 class="mb-4 mt-5" id="qu-est-ce-qu-un-dns-racine"><a href="#qu-est-ce-qu-un-dns-racine" class="ancre"><i class="fa-solid fa-link"></i> Qu’est-ce qu’un DNS racine ?</a></h3>

	<div>
		<p>Tous les résolveurs DNS connaissent les 13 serveurs DNS racine. Ces serveurs représentent la première étape dans la recherche d’enregistrements DNS par un résolveur récursif. Lorsqu’un résolveur récursif envoie une requête contenant un nom de domaine, un serveur racine répond en orientant le résolveur vers un serveur de noms TLD, selon l’extension du domaine (comme .com, .net, .org, etc.). Ces serveurs de noms racine sont gérés par une organisation à but non lucratif appelée Internet Corporation for Assigned Names and Numbers (ICANN).</p>

		<p>Il est important de noter que même s’il existe 13 types de serveurs de noms racine, cela ne signifie pas qu’il n’y a que 13 machines dans le système. Il y a de nombreuses copies de chaque type de serveur réparties à travers le monde, utilisant le routage Anycast pour offrir des réponses rapides. En tout, il existe environ 600 serveurs de noms racine différents.</p>

		<p class="mb-4">Les serveurs DNS racines sont nommé par lettre, de A à M, en voici la liste :</p>

		<ul>
			<li>A Root : opéré par <em>Verisign, Inc</em></li>
			<li>B Root : opéré par l’<em>Institut des sciences de l’information de l’USC</em> (ISI)</li>
			<li>C Root : opéré par <em>Cogent Communications</em></li>
			<li>D Root : opéré par l’<em>Université du Maryland</em></li>
			<li>E Root : opéré par la <em>NASA Ames Research Center</em></li>
			<li>F Root : opéré par <em>Internet Systems Consortium, Inc.</em> (ISC)</li>
			<li>G Root : opéré par la <em>Défense des systèmes d’information de la DISA</em></li>
			<li>H Root : opéré par l’<em>US Army Research Lab</em></li>
			<li>I Root : opéré par <em>Netnod</em></li>
			<li>J Root : opéré par <em>Verisign, Inc</em></li>
			<li>K Root : opéré par le <em>RIPE NCC</em></li>
			<li>L Root : opéré par l’<em>ICANN</em></li>
			<li>M Root : opéré par <em>WIDE Project</em></li>
		</ul>

		<p class="text-center"><a href="assets/img/tutoriels/dns-image-5-dns-racine.png"><img src="assets/img/tutoriels/dns-image-5-dns-racine.png" class="img-fluid border rounded col-12 col-lg-6" alt="DNS racine" data-fancybox="gallerie"></a></p>
	</div>

	<div>
		<h3 class="mb-4 mt-5" id="qu-est-ce-qu-un-serveur-tld"><a href="#qu-est-ce-qu-un-serveur-tld" class="ancre"><i class="fa-solid fa-link"></i> Qu’est-ce qu’un serveur TLD ?</a></h3>

		<p>Un serveur de noms TLD stocke les informations relatives à tous les noms de domaine qui partagent une même extension, comme <em>.com</em>, <em>.net</em>, ou tout ce qui suit le dernier point dans une URL. Par exemple, un serveur de noms TLD pour <em>.com</em> contient les informations pour chaque site web se terminant par <em>.com</em>. Ainsi, si un utilisateur recherche <em>thisip.pw</em>, après avoir obtenu une réponse d’un serveur de noms racine, le résolveur récursif enverra une requête à un serveur de noms TLD .com, qui répondra en indiquant le serveur de noms faisant autorité pour ce domaine.</p>

		<p>Les serveurs de noms TLD sont gérés par l’<abbr title="Internet Assigned Numbers Authority">IANA</abbr>, qui est une branche de l’<abbr title="Internet Corporation for Assigned Names and Numbers">ICANN</abbr>. L’IANA divise les serveurs TLD en deux catégories principales :</p>

		<ul>
			<li>Domaines génériques de premier niveau : ce sont des domaines qui ne sont pas liés à un pays spécifique. Les TLD génériques les plus connus incluent <em>.com</em>, <em>.org</em>, <em>.net</em>, <em>.edu</em>, et <em>.gov</em></li>
			<li>Domaines de premier niveau de code de pays : ces domaines sont spécifiques à un pays ou à une région, comme <em>.uk</em>, <em>.us</em>, <em>.ru</em>, et <em>.jp</em></li>
		</ul>

		<p class="mb-4">Il existe également une troisième catégorie pour les domaines d’infrastructure, bien qu’elle soit rarement utilisée. Cette catégorie a été créée pour le domaine <em>.arpa</em>, un domaine de transition utilisé lors de la création du DNS moderne. Son importance aujourd’hui est principalement historique.</p>

		<p class="text-center"><a href="assets/img/tutoriels/dns-image-6-dns-tld.png"><img src="assets/img/tutoriels/dns-image-6-serveur-tld.png" class="img-fluid border rounded col-12 col-lg-6" alt="DNS TLD" data-fancybox="gallerie"></a></p>
	</div>

	<div>
		<h3 class="mb-4 mt-5" id="qu-est-ce-qu-un-nom-de-reference"><a href="#qu-est-ce-qu-un-nom-de-reference" class="ancre"><i class="fa-solid fa-link"></i> Qu’est-ce qu’un nom de référence ?</a></h3>

		<p>Lorsqu’un résolveur récursif reçoit une réponse d’un serveur de noms de domaine de premier niveau (TLD), cette réponse redirige le résolveur vers un serveur de noms faisant autorité. Ce serveur est généralement la dernière étape du processus de résolution vers une adresse IP. Il contient des informations spécifiques au nom de domaine qu’il gère, comme <em>thisip.pw</em>, et peut fournir l’adresse IP à partir de l’enregistrement DNS A au résolveur récursif.</p>

		<p class="mb-4">Si le domaine possède un enregistrement CNAME (alias), il donnera au résolveur un domaine alias. Dans ce cas, le résolveur récursif devra effectuer une nouvelle requête DNS pour obtenir l’enregistrement final d’un serveur de noms faisant autorité, souvent un enregistrement A avec une adresse IP.</p>

		<p class="text-center"><a href="assets/img/tutoriels/dns-image-7-nom-reference.png"><img src="assets/img/tutoriels/dns-image-7-nom-reference.png" class="img-fluid border rounded col-12 col-lg-6" alt="Nom de référence" data-fancybox="gallerie"></a></p>
	</div>

	<div>
		<h3 class="mb-4 mt-5" id="dns-mx-pour-gestion-des-courriels"><a href="#dns-mx-pour-gestion-des-courriels" class="ancre"><i class="fa-solid fa-link"></i> Les DNS MX pour la gestion des courriels</a></h3>

		<h4 class="mb-4 mt-5" id="qu-est-ce-qu-un-dns-mx"><a href="#qu-est-ce-qu-un-dns-mx" class="ancre"><i class="fa-solid fa-link"></i> Qu’est-ce qu’un DNS MX ?</a></h4>

		<p>Un <strong>enregistrement DNS MX</strong> (Mail Exchange) est un type d'enregistrement dans le système de noms de domaine (DNS) qui spécifie les serveurs de messagerie responsables de la réception des courriels pour un nom de domaine particulier. Lorsqu'un courriel est envoyé à une adresse se terminant par <code>@thisip.pw</code>, le serveur de messagerie de l'expéditeur interroge le DNS pour obtenir les enregistrements MX associés à <code>@thisip.pw</code>.</p>

		<p>Ces enregistrements indiquent :</p>

		<ul>
			<li><span class="fw-bold">Quels serveurs</span> acceptent les courriels pour le domaine</li>
			<li><span class="fw-bold">La priorité</span> des serveurs si plusieurs sont listés, grâce à une valeur numérique : un nombre plus petit indique une priorité plus élevée</li>
		</ul>

		<p>Par exemple, si le domaine dispose de plusieurs serveurs de messagerie, les enregistrements MX permettent de définir un ordre de préférence. Si le serveur avec la priorité la plus élevée est indisponible, le système tentera de contacter le suivant dans la liste.</p>

		<p class="mb-0">En résumé, les <strong>enregistrements DNS MX</strong> sont essentiels pour le routage correct des courriels, en indiquant aux expéditeurs où diriger les messages destinés à un domaine spécifique.</p>
	</div>

	<div>
		<h4 class="mb-4 mt-5" id="qu-est-ce-que-le-spf"><a href="#qu-est-ce-que-le-spf" class="ancre"><i class="fa-solid fa-link"></i> Qu’est-ce que le SPF ?</a></h4>

		<p>Le <strong>SPF</strong> (Sender Policy Framework) est un protocole d'authentification des courriels qui permet aux propriétaires de domaines de spécifier quels serveurs de messagerie sont autorisés à envoyer des courriels en leur nom. En publiant une politique SPF dans les enregistrements DNS de leur domaine, les serveurs de messagerie récepteurs peuvent vérifier si un courriel provient d'une source autorisée, contribuant ainsi à réduire le spam, le phishing et l'usurpation d'identité.</p>

		<h5 class="mb-4 mt-5" id="comment-fonctionne-spf"><a href="#comment-fonctionne-spf" class="ancre"><i class="fa-solid fa-link"></i> Comment fonctionne le SPF ?</a></h5>

		<ol>
			<li>
				<span class="fw-bold">Publication d'un enregistrement SPF</span> :
				<ul>
					<li>Le propriétaire du domaine crée un enregistrement DNS de type TXT contenant la politique SPF</li>
					<li>Cet enregistrement énumère les adresses IP ou les serveurs autorisés à envoyer des courriels pour ce domaine</li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Envoi du courriel</span> :
				<ul>
					<li>Lorsqu'un courriel est envoyé depuis le domaine, le serveur de messagerie de l'expéditeur inclut le domaine</li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Vérification par le serveur récepteur</span> :
				<ul>
					<li>Le serveur de messagerie du destinataire reçoit le courriel et extrait le domaine de l'expéditeur</li>
					<li>Il consulte l'enregistrement SPF de ce domaine dans le DNS</li>
					<li>Il compare l'adresse IP de l'expéditeur avec les adresses autorisées dans l'enregistrement SPF</li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Décision du serveur récepteur</span> :
				<ul>
					<li><span class="fw-bold">Si l'adresse IP est autorisée</span> : le courriel est considéré comme authentique et est livré normalement</li>
					<li><span class="fw-bold">Si l'adresse IP n'est pas autorisée</span> : le serveur récepteur peut choisir de rejeter le courriel, de le marquer comme spam ou de le traiter selon ses propres politiques</li>
				</ul>
			</li>
		</ol>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="avantages-spf"><a href="#avantages-spf" class="ancre"><i class="fa-solid fa-link"></i> Avantages du SPF</a></h5>

		<ul>
			<li><span class="fw-bold">Réduction du spam et du phishing</span> : empêche les expéditeurs non autorisés d'envoyer des courriels en utilisant votre domaine, protégeant ainsi les destinataires contre les tentatives de fraude</li>
			<li><span class="fw-bold">Protection de la réputation du domaine</span> : maintient la confiance des destinataires et des fournisseurs de services de messagerie envers votre domaine, améliorant la délivrabilité de vos courriels légitimes</li>
			<li><span class="fw-bold">Conformité aux normes de sécurité</span> : SPF est souvent requis pour se conformer aux meilleures pratiques en matière de sécurité des courriels, notamment dans les environnements professionnels</li>
		</ul>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="exemple-enregistrement-spf"><a href="#exemple-enregistrement-spf" class="ancre"><i class="fa-solid fa-link"></i> Exemple d'un enregistrement SPF</a></h5>

		<p><code>v=spf1 ip4:192.0.2.0/24 include:_spf.google.com -all</code></p>

		<p>Explication des composants :</p>

		<ul>
			<li><code>v=spf1</code> : indique la version du protocole SPF utilisée</li>
			<li><code>ip4:192.0.2.0/24</code> : autorise les adresses IP dans la plage spécifiée à envoyer des courriels pour le domaine</li>
			<li><code>include:_spf.google.com</code> : inclut les serveurs autorisés par Google (utile si vous utilisez G Suite pour les courriels)</li>
			<li><code>-all</code> : signifie que toutes les autres adresses IP non spécifiées doivent être rejetées</li>
		</ul>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="points-importants-lors-de-la-mise-en-oeuvre-du-spf"><a href="#points-importants-lors-de-la-mise-en-oeuvre-du-spf" class="ancre"><i class="fa-solid fa-link"></i> Points importants lors de la mise en œuvre du SPF</a></h5>

		<ul>
			<li><span class="fw-bold">Exactitude de l'enregistrement</span> : une configuration incorrecte peut entraîner le rejet de vos courriels légitimes. Assurez-vous que tous les serveurs de messagerie que vous utilisez sont inclus</li>
			<li><span class="fw-bold">Limite de 10 recherches DNS</span> : SPF impose une limite de 10 mécanismes qui génèrent des recherches DNS (<code>include</code>, <code>a</code>, <code>mx</code>, etc.). Dépasser cette limite peut entraîner un échec de la vérification SPF</li>
			<li><span class="fw-bold">Problèmes avec le transfert des courriels</span> : SPF peut échouer lorsque les courriels sont transférés, car l'adresse IP du serveur intermédiaire n'est pas autorisée dans votre enregistrement SPF. L'utilisation conjointe de DKIM et DMARC peut aider à atténuer ce problème</li>
			<li><span class="fw-bold">Mises à jour régulières</span> : si vous ajoutez de nouveaux serveurs de messagerie ou utilisez des services tiers pour envoyer des courriels (comme des services de marketing par courriel), vous devez mettre à jour votre enregistrement SPF en conséquence</li>
		</ul>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="combinaison-avec-dkim-et-dmarc"><a href="#combinaison-avec-dkim-et-dmarc" class="ancre"><i class="fa-solid fa-link"></i> Combinaison avec DKIM et DMARC</a></h5>

		<ul>
			<li><span class="fw-bold">DKIM (DomainKeys Identified Mail)</span> : ajoute une signature numérique à vos courriels, permettant aux serveurs récepteurs de vérifier que le message n'a pas été altéré</li>
			<li><span class="fw-bold">DMARC (Domain-based Message Authentication, Reporting, and Conformance)</span> : utilise SPF et DKIM pour fournir une politique sur la manière de traiter les courriels non authentifiés et fournit des rapports sur les tentatives d'usurpation</li>
		</ul>

		<p class="mb-0">En combinant <strong>SPF</strong>, <strong>DKIM</strong> et <strong>DMARC</strong>, vous renforcez considérablement la sécurité de vos communications par courriel.</p>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="comment-configurer-un-enregistrement-sfp"><a href="#comment-configurer-un-enregistrement-sfp" class="ancre"><i class="fa-solid fa-link"></i> Comment configurer un enregistrement SPF</a></h5>

		<ol>
			<li>
				<span class="fw-bold">Identifiez tous les serveurs qui envoient des courriels pour votre domaine</span> :
				<ul>
					<li>Serveurs de messagerie internes</li>
					<li>Services tiers (marketing par courriel, CRM, etc.)</li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Créez l'enregistrement SPF</span> :
				<ul>
					<li>Rassemblez les adresses IP et les domaines à inclure</li>
					<li>Utilisez les mécanismes SPF appropriés (<code>ip4</code>, <code>ip6</code>, <code>include</code>, etc.)</li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Ajoutez l'enregistrement au DNS de votre domaine</span> :
				<ul>
					<li>Accédez à la zone DNS de votre domaine via votre hébergeur ou registraire</li>
					<li>Ajoutez un nouvel enregistrement de type TXT avec le nom de votre domaine (ou '@' pour le domaine racine) et collez la politique SPF</li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Vérifiez la configuration</span> :
				<ul>
					<li>Utilisez des outils en ligne pour valider votre enregistrement SPF et vous assurer qu'il ne dépasse pas les limites</li>
				</ul>
			</li>
		</ol>
	</div>

	<div>
		<p>En résumé, le <strong>SPF</strong> est un élément essentiel pour sécuriser vos communications par courriel. Il permet aux serveurs récepteurs de vérifier que les courriels proviennent de sources autorisées par le propriétaire du domaine, réduisant ainsi le risque de spam et de phishing. Une mise en œuvre correcte du SPF, en combinaison avec <strong>DKIM</strong> et <strong>DMARC</strong>, contribue à protéger votre domaine et à maintenir la confiance dans vos courriels.</p>
	</div>

	<div>
		<h4 class="mb-4 mt-5" id="qu-est-ce-que-dkim"><a href="#qu-est-ce-que-dkim" class="ancre"><i class="fa-solid fa-link"></i> Qu’est-ce que DKIM ?</a></h4>

		<p>Le <strong>DKIM</strong> (DomainKeys Identified Mail) est un protocole d'authentification des courriels qui permet au destinataire de vérifier que le message reçu provient bien du domaine de l'expéditeur et qu'il n'a pas été altéré en transit. Il fonctionne en ajoutant une signature numérique aux en-têtes du courriel, qui peut être vérifiée par le serveur récepteur à l'aide d'une clé publique publiée dans le DNS du domaine de l'expéditeur.</p>

		<h5 class="mb-4 mt-5" id="comment-fonctionne-dkim"><a href="#comment-fonctionne-dkim" class="ancre"><i class="fa-solid fa-link"></i> Comment fonctionne DKIM ?</a></h5>

		<ol>
			<li>
				<span class="fw-bold">Signature du courriel par l'expéditeur</span> :
				<ul>
					<li>Lorsqu'un courriel est envoyé, le serveur de messagerie de l'expéditeur génère une signature numérique unique en utilisant une clé privée. Cette signature est basée sur certains éléments du courriel, tels que les en-têtes et le corps du message</li>
					<li>La signature est ajoutée au courriel sous la forme d'un en-tête <code>DKIM-Signature</code></li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Publication de la clé publique dans le DNS</span> :
				<ul>
					<li>Le propriétaire du domaine publie la clé publique correspondante dans un enregistrement DNS de type TXT ou CNAME, généralement sous un sous-domaine spécifique appelé « sélecteur »</li>
					<li>Ce sélecteur est inclus dans l'en-tête DKIM pour que le serveur récepteur sache où trouver la clé publique</li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Vérification par le serveur récepteur</span> :
				<ul>
					<li>Le serveur de messagerie du destinataire extrait l'en-tête <code>DKIM-Signature</code> du courriel</li>
					<li>Il utilise le sélecteur pour récupérer la clé publique dans le DNS du domaine de l'expéditeur</li>
					<li>La clé publique sert à vérifier la signature numérique. Si la vérification réussit, cela confirme que le courriel n'a pas été modifié et qu'il provient bien du domaine indiqué</li>
				</ul>
			</li>
		</ol>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="avantages-dkim"><a href="#avantages-dkim" class="ancre"><i class="fa-solid fa-link"></i> Avantages DKIM</a></h5>

		<ul>
			<li><span class="fw-bold">Authenticité du message</span> : assure que le courriel a été envoyé par un serveur autorisé par le propriétaire du domaine</li>
			<li><span class="fw-bold">Intégrité du message</span> : garantit que le contenu du courriel n'a pas été altéré pendant le transit</li>
			<li><span class="fw-bold">Protection contre le spam et le phishing</span> : réduit les risques que des expéditeurs malveillants envoient des courriels en se faisant passer pour votre domaine</li>
			<li><span class="fw-bold">Amélioration de la délivrabilité</span> : les courriels signés avec DKIM ont plus de chances d'atteindre la boîte de réception du destinataire plutôt que le dossier spam</li>
		</ul>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="integration-avec-spf-dmarc"><a href="#integration-avec-spf-dmarc" class="ancre"><i class="fa-solid fa-link"></i> Intégration avec SPF et DMARC</a></h5>

		<ul>
			<li><a href="#qu-est-ce-que-le-spf" class="fw-bold">SPF</a> : spécifie quelles adresses IP sont autorisées à envoyer des courriels pour un domaine</li>
			<li><a href="#qu-est-ce-que-dkim" class="fw-bold">DKIM</a> : fournit une signature numérique pour vérifier l'intégrité et l'authenticité du courriel</li>
			<li><a href="#qu-est-ce-que-dmarc" class="fw-bold">DMARC</a> : utilise <strong>SPF</strong> et <strong>DKIM</strong> pour permettre aux propriétaires de domaines de définir une politique sur la manière dont les courriels non authentifiés doivent être traités et fournit des rapports sur les tentatives d'usurpation</li>
		</ul>

		<p>En combinant <strong>SPF</strong>, <strong>DKIM</strong> et <strong>DMARC</strong>, les domaines peuvent renforcer significativement la sécurité de leurs communications par courriel et réduire les risques liés au spam et au phishing.</p>

		<h5 class="mb-4 mt-5" id="exemple-enregistrement-dkim-dans-le-dns"><a href="#exemple-enregistrement-dkim-dans-le-dns" class="ancre"><i class="fa-solid fa-link"></i> Exemple d'un enregistrement DKIM dans le DNS</a></h5>

		<p>Supposons que vous avez un sélecteur nommé <code>default</code> pour votre domaine <code>thisip.pw</code>. L'enregistrement DNS ressemblerait à ceci : <code>default._domainkey.thisip.pw IN TXT "v=DKIM1; k=rsa; p=MIIBIjANBgkqh…"</code></p>

		<ul>
			<li><code>default</code> : le sélecteur utilisé pour identifier la clé spécifique</li>
			<li><code>_domainkey</code> : un sous-domaine standard utilisé pour les enregistrements DKIM</li>
			<li><code>v=DKIM1</code> : indique la version du protocole DKIM utilisée</li>
			<li><code>k=rsa</code> : spécifie l'algorithme cryptographique utilisé</li>
			<li><code>p=</code> : contient la clé publique en elle-même, qui est une longue chaîne de caractères encodée</li>
		</ul>

		<p class="mb-0">En résumé, le <strong>DKIM</strong> est un mécanisme essentiel pour sécuriser les courriels, en permettant aux destinataires de vérifier que les messages proviennent réellement du domaine indiqué et qu'ils n'ont pas été modifiés en cours de route. L'implémentation de DKIM, en conjonction avec <strong>SPF</strong> et <strong>DMARC</strong>, contribue à protéger la réputation de votre domaine et à assurer la confiance dans vos communications par courriel.</p>
	</div>

	<div>
		<h4 class="mb-4 mt-5" id="qu-est-ce-que-dmarc"><a href="#qu-est-ce-que-dmarc" class="ancre"><i class="fa-solid fa-link"></i> Qu’est-ce que DMARC ?</a></h4>

		<p>Le <strong>DMARC</strong> (Domain-based Message Authentication, Reporting, and Conformance) est un protocole d'authentification des courriels qui vise à réduire les abus tels que le phishing et le spam en permettant aux propriétaires de domaines de publier une politique indiquant comment les courriels non authentifiés doivent être traités. DMARC s'appuie sur les protocoles SPF (Sender Policy Framework) et DKIM (DomainKeys Identified Mail) pour vérifier l'authenticité des messages.</p>

		<h5 class="mb-4 mt-5" id="comment-fonctionne-dmarc"><a href="#comment-fonctionne-dmarc" class="ancre"><i class="fa-solid fa-link"></i> Comment fonctionne DMARC ?</a></h5>

		<ol>
			<li>
				Publication d'un enregistrement DMARC dans le DNS :

				<ul>
					<li>Le propriétaire du domaine crée un enregistrement DNS de type TXT sous la forme <code>_dmarc.thisip.pw</code></li>
					<li>Cet enregistrement contient des informations sur la politique DMARC, y compris comment traiter les courriels qui échouent aux vérifications SPF et DKIM, et où envoyer les rapports</li>
				</ul>
			</li>
			<li>
				Vérification par le serveur récepteur :

				<ul>
					<li>Lorsqu'un courriel est reçu, le serveur de messagerie du destinataire effectue les vérifications SPF et DKIM</li>
					<li>Il compare ensuite le domaine de l'expéditeur dans l'en-tête <code>From:</code> avec les domaines utilisés dans les vérifications SPF et DKIM pour s'assurer qu'ils sont alignés (c'est-à-dire qu'ils correspondent ou sont sous le même domaine)</li>
				</ul>
			</li>
			<li>
				Application de la politique DMARC :

				<ul>
					<li>
						En fonction des résultats des vérifications et de la politique DMARC du domaine de l'expéditeur, le serveur récepteur décide de l'action à entreprendre :

						<ul>
							<li><span class="fw-bold">Aucune action</span> (<code>p=none</code>) : le message est traité normalement</li>
							<li><span class="fw-bold">Quarantaine</span> (<code>p=quarantine</code>) :le message est marqué comme suspect, souvent envoyé dans le dossier spam</li>
							<li><span class="fw-bold">Rejet</span> (<code>p=reject</code>) : le message est rejeté et ne parvient pas au destinataire</li>
						</ul>
					</li>
					<li>
						Rapports DMARC :

						<ul>
							<li>Les serveurs récepteurs envoient des rapports agrégés et/ou forensiques à l'adresse spécifiée dans l'enregistrement DMARC</li>
							<li>Ces rapports permettent au propriétaire du domaine de surveiller les tentatives d'usurpation et l'efficacité de sa politique DMARC</li>
						</ul>
					</li>
				</ul>
			</li>
		</ol>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="avantages-dmarc"><a href="#avantages-dmarc" class="ancre"><i class="fa-solid fa-link"></i> Avantages DMARC</a></h5>

		<ul>
			<li><span class="fw-bold">Protection renforcée contre le phishing et le spam</span> : en combinant SPF et DKIM avec une politique claire, DMARC aide à empêcher les acteurs malveillants d'envoyer des courriels en utilisant frauduleusement votre domaine</li>
			<li><span class="fw-bold">Visibilité accrue</span> : les rapports DMARC fournissent des informations précieuses sur qui envoie des courriels en votre nom, ce qui peut aider à identifier et à corriger les problèmes</li>
			<li><span class="fw-bold">Amélioration de la délivrabilité</span> : les fournisseurs de messagerie considèrent les domaines avec DMARC comme plus sûrs, ce qui peut augmenter les chances que vos courriels légitimes atteignent la boîte de réception des destinataires</li>
			<li><span class="fw-bold">Contrôle sur l'utilisation du domaine</span> : DMARC permet aux propriétaires de domaines de spécifier comment traiter les courriels non conformes, offrant ainsi un meilleur contrôle sur leur domaine</li>
		</ul>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="exemple-enregistrement-dmarc"><a href="#exemple-enregistrement-dmarc" class="ancre"><i class="fa-solid fa-link"></i> Exemple d'un enregistrement DMARC</a></h5>

		<code>_dmarc.thisip.pw IN TXT "v=DMARC1; p=reject; rua=mailto:rapports@thisip.pw; ruf=mailto:alerte@thisip.pw; pct=100; sp=none; aspf=r; adkim=r;"</code>

		<p>Explication des paramètres :</p>

		<ul>
			<li><code>v=DMARC1</code> : version du protocole DMARC</li>
			<li><code>p=reject</code> : politique pour le domaine principal (ici, rejeter les courriels non conformes)</li>
			<li><code>rua=mailto:rapports@thisip.pw</code> : adresse où les rapports agrégés doivent être envoyés</li>
			<li><code>ruf=mailto:forensic@thisip.pw</code> : adresse pour les rapports forensiques (détaillés)</li>
			<li><code>pct=100</code> : pourcentage des courriels auxquels appliquer la politique (ici, 100%)</li>
			<li><code>sp=none</code> : politique pour les sous-domaines (ici, aucune action)</li>
			<li><code>aspf=r</code> : mode d'alignement SPF (relâché)</li>
			<li><code>adkim=r</code> : mode d'alignement DKIM (relâché)</li>
		</ul>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="etapes-pour-implementer-dmarc"><a href="#etapes-pour-implementer-dmarc" class="ancre"><i class="fa-solid fa-link"></i> Étapes pour implémenter DMARC</a></h5>

		<ol>
			<li>
				Assurez-vous que SPF et DKIM sont correctement configurés :

				<ul>
					<li><span class="fw-bold">SPF</span> : publiez un enregistrement DNS spécifiant les serveurs autorisés à envoyer des courriels pour votre domaine</li>
					<li><span class="fw-bold">DKIM</span> : configurez la signature des courriels sortants et publiez la clé publique dans votre DNS</li>
				</ul>
			</li>
			<li>
				Créez un enregistrement DMARC avec une politique initiale permissive :

				<ul>
					<li>Commencez avec <code>p=none</code> pour surveiller sans affecter la délivrabilité</li>
					<li>Configurez les adresses pour recevoir les rapports</li>
				</ul>
			</li>
			<li>
				Analysez les rapports DMARC :

				<ul>
					<li>Examinez qui envoie des courriels en utilisant votre domaine</li>
					<li>Identifiez et corrigez les sources légitimes non conformes</li>
					<li>Détectez les tentatives d'usurpation</li>
				</ul>
			</li>
			<li>
				Renforcez progressivement la politique DMARC :

				<ul>
					<li>Passez à <code>p=quarantine</code> pour mettre en quarantaine les courriels non conformes</li>
					<li>Finalement, utilisez <code>p=reject</code> pour rejeter les courriels non authentifiés</li>
				</ul>
			</li>
			<li>
				Surveillez continuellement :

				<ul>
					<li>Continuez à analyser les rapports pour maintenir la sécurité et la conformité</li>
				</ul>
			</li>
		</ol>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="alignement-dans-dmarc"><a href="#alignement-dans-dmarc" class="ancre"><i class="fa-solid fa-link"></i> Alignement dans DMARC</a></h5>

		<p>L'alignement est un concept clé dans DMARC qui assure que les domaines utilisés dans SPF et DKIM correspondent au domaine de l'en-tête <code>From:</code> visible par le destinataire.</p>

		<ul>
			<li><span class="fw-bold">Alignement SPF</span> : vérifie si le domaine utilisé dans l'enveloppe de l'expéditeur (Mail From) correspond au domaine de l'en-tête <code>From:</code></li>
			<li><span class="fw-bold">Alignement DKIM</span> : vérifie si le domaine utilisé dans la signature DKIM correspond au domaine de l'en-tête <code>From:</code></li>
		</ul>

		<p>Il existe deux modes d'alignement :</p>

		<ul>
			<li><span class="fw-bold">Strict</span> (<code>s</code>) : les domaines doivent correspondre exactement</li>
			<li><span class="fw-bold">Relâché</span> (<code>r</code>) : les domaines peuvent être des sous-domaines du domaine principal</li>
		</ul>
	</div>

	<div>
		<h5 class="mb-4 mt-5" id="integration-avec-spf-et-dkim"><a href="#integration-avec-spf-et-dkim" class="ancre"><i class="fa-solid fa-link"></i> Intégration avec SPF et DKIM</a></h5>

		<ul>
			<li><span class="fw-bold">SPF seul</span> : vérifie l'adresse IP de l'expéditeur, mais peut être contourné si l'en-tête <code>From:</code> est falsifié</li>
			<li><span class="fw-bold">DKIM seul</span> : assure l'intégrité du message, mais ne spécifie pas comment traiter les échecs</li>
			<li><span class="fw-bold">DMARC avec SPF et DKIM</span> : fournit une politique claire et des rapports, en s'assurant que le domaine de l'en-tête <code>From:</code> est authentifié par SPF et/ou DKIM</li>
		</ul>

		<p>En résumé, DMARC est un outil puissant pour renforcer la sécurité des courriels en permettant aux domaines d'indiquer aux serveurs récepteurs comment traiter les courriels non authentifiés. Ils fournissant des rapports détaillés pour surveiller et analyser l'utilisation de votre domaine en réduisant les risques associés au phishing, au spam et à l'usurpation d'identité.</p>

		<p>La mise en œuvre de DMARC, en combinaison avec SPF et DKIM, est fortement recommandée pour toute organisation souhaitant protéger sa réputation et assurer la confiance dans ses communications par courriel.</p>

		<p class="text-center"><a href="assets/img/tutoriels/dns-image-8-spf-dkim-dmarc.png"><img src="assets/img/tutoriels/dns-image-8-spf-dkim-dmarc.png" class="img-fluid border rounded col-12 col-lg-8" alt="Schéma de fonctionnement de SPF, DKIM et DMARC" title="Schéma de fonctionnement de SPF, DKIM et DMARC" data-fancybox="gallerie"></a></p>

		<h3 class="mb-4 mt-5" id="qu-est-ce-qu-un-dns-txt"><a href="#qu-est-ce-qu-un-dns-txt" class="ancre"><i class="fa-solid fa-link"></i> Qu’est-ce qu’un DNS TXT ?</a></h3>

		<p>Un <strong>enregistrement DNS TXT</strong> (pour « Text ») est un type d'enregistrement dans le système de noms de domaine (DNS) qui permet aux administrateurs de domaines d'associer des informations textuelles à un nom de domaine. Ces enregistrements sont polyvalents et sont largement utilisés pour diverses applications, notamment en matière de sécurité, d'authentification des courriels et de vérification de propriété.</p>
	</div>

	<div>
		<h4 class="mb-4 mt-5" id="fonctionnalites-princiaples-des-enregistrements-dns-txt"><a href="#fonctionnalites-princiaples-des-enregistrements-dns-txt" class="ancre"><i class="fa-solid fa-link"></i> Fonctionnalités principales des enregistrements DNS TXT</a></h4>

		<ul>
			<li>
				<span class="fw-bold">Stockage de données textuelles arbitraires</span> :
				<ul>
					<li>Les enregistrements TXT peuvent contenir n'importe quelle chaîne de texte, offrant ainsi une grande flexibilité pour stocker des informations spécifiques au domaine</li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Authentification des courriels</span> :
				<ul>
					<li><span class="fw-bold">SPF (Sender Policy Framework)</span> : les politiques SPF sont publiées via des enregistrements TXT pour spécifier quels serveurs sont autorisés à envoyer des courriels pour le domaine</li>
					<li><span class="fw-bold">DKIM (DomainKeys Identified Mail)</span> : les clés publiques utilisées pour vérifier les signatures <a href="#qu-est-ce-que-dkim">DKIM</a> sont souvent stockées dans des enregistrements TXT</li>
					<li><span class="fw-bold">DMARC (Domain-based Message Authentication, Reporting, and Conformance)</span> : les politiques <a href="#qu-est-ce-que-dmarc">DMARC</a> utilisent des enregistrements TXT pour définir comment les courriels non conformes doivent être traités</li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Vérification de propriété de domaine</span> :
				<ul>
					<li>Des services tels que Google Search Console, Microsoft Office 365 ou d'autres plateformes demandent aux propriétaires de domaines d'ajouter un enregistrement TXT spécifique pour prouver qu'ils contrôlent le domaine</li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Configuration de services et d'applications</span> :
				<ul>
					<li>Certains services utilisent des enregistrements TXT pour stocker des configurations ou des paramètres spécifiques nécessaires à leur fonctionnement</li>
				</ul>
			</li>
			<li>
				<span class="fw-bold">Politiques de sécurité</span> :
				<ul>
					<li>CSP (Content Security Policy) pour les courriels : utilisé pour définir des politiques de sécurité liées au contenu des courriels</li>
				</ul>
			</li>
		</ul>
	</div>

	<div>
		<h4 class="mb-4 mt-5" id="exemples-enregistrement-dns-txt"><a href="#exemples-enregistrement-dns-txt" class="ancre"><i class="fa-solid fa-link"></i> Exemples d'enregistrements DNS TXT</a></h4>

		<p>Enregistrement SPF : <code>v=spf1 include:_spf.thisip.pw ~all</code></p>
		<p>Cet enregistrement indique que les serveurs listés dans <code>_spf.thisip.pw</code> sont autorisés à envoyer des courriels pour le domaine <em>thisip.pw</em>, et que tous les autres doivent être traités avec souplesse (<code>~all</code>).</p>

		<p>Clé publique DKIM : <code>v=DKIM1; k=rsa; p=MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8A…</code></p>
		<p>Ici, la clé publique utilisée pour vérifier les signatures DKIM est publiée, permettant aux serveurs récepteurs de valider l'authenticité des courriels.</p>

		<p>Politique DMARC : <code>v=DMARC1; p=quarantine; rua=mailto:rapports@thisip.pw</code>
		<p>Cette politique indique que les courriels non conformes doivent être mis en quarantaine, et que les rapports DMARC doivent être envoyés à <code>rapports@thisip.pw</code>.</p>

		<p>Vérification de propriété pour <em>Google</em> sur le domaine <em>google.com</em> : <code>google-site-verification=4ibFUgB-wXLQ_S7vsXVomSTVamuOXBiVAzpR5IZ87D0</code>.</p>
		<p>Cet enregistrement est utilisé par Google pour vérifier que vous êtes le propriétaire du domaine.</p>

		<p>Vérification de propriété pour <em>Apple</em> sur le domaine <em>google.com</em> : <code>apple-domain-verification=30afIBcvSuDV2PLX</code>.</p>
		<p>Cet enregistrement est utilisé par <em>Apple</em> pour vérifier que le propriétaire est en possession du domaine.</p>
	</div>

	<div>
		<h4 class="mb-4 mt-5" id="pourquoi-les-enregistrement-txt-sont-importants"><a href="#pourquoi-les-enregistrement-txt-sont-importants" class="ancre"><i class="fa-solid fa-link"></i> Pourquoi les enregistrements TXT sont importants</a></h4>

		<p><span class="fw-bold">Sécurité des courriels</span> : ils jouent un rôle crucial dans la lutte contre le spam et le phishing en permettant la mise en place de <a href="#qu-est-ce-que-le-spf">SPF</a>, <a href="#qu-est-ce-que-dkim">DKIM</a> et <a href="#qu-est-ce-que-dmarc">DMARC</a>.

		<p><span class="fw-bold">Vérification de domaine</span> : facilitent la validation de la propriété du domaine par des services tiers, améliorant ainsi l'intégration avec divers outils et plateformes.</p>

		<p><span class="fw-bold">Flexibilité</span> : permettent d'ajouter des informations personnalisées pour des besoins spécifiques, offrant une grande souplesse dans la gestion du domaine.</p>
	</div>

	<div>
		<h4 class="mb-4 mt-5" id="comment-ajouter-enregistrement-dns-txt"><a href="#comment-ajouter-enregistrement-dns-txt" class="ancre"><i class="fa-solid fa-link"></i> Comment ajouter un enregistrement DNS TXT</a></h4>

		<ol>
			<li><span class="fw-bold">Accéder à la gestion DNS de votre domaine</span> : connectez-vous au panneau de contrôle de votre hébergeur ou registraire de domaine</li>
			<li><span class="fw-bold">Créer un nouvel enregistrement TXT</span> : ajoutez un enregistrement de type TXT en spécifiant le nom (souvent '@' pour le domaine racine ou un sous-domaine spécifique) et la valeur texte requise</li>
			<li><span class="fw-bold">Enregistrer les modifications</span> : sauvegardez l'enregistrement et attendez la propagation DNS, qui peut prendre de quelques minutes à 48 heures</li>
		</ol>

		<p>Points à considérer :</p>

		<ul>
			<li><span class="fw-bold">Exactitude des informations</span> : une erreur dans un enregistrement TXT, notamment pour SPF, DKIM ou DMARC, peut entraîner des problèmes de délivrabilité des courriels ou compromettre la sécurité</li>
			<li><span class="fw-bold">Longueur maximale</span> : bien que chaque chaîne dans un enregistrement TXT puisse contenir jusqu'à 255 caractères, il est possible de concaténer plusieurs chaînes pour stocker des valeurs plus longues</li>
			<li><span class="fw-bold">Visibilité publique</span> : les enregistrements DNS sont publics. Évitez d'y inclure des informations sensibles ou confidentielles</li>
		</ul>

		<p class="mb-0">En résumé, un <strong>enregistrement DNS TXT</strong> est un outil polyvalent essentiel dans la gestion d'un domaine. Il permet d'associer des informations textuelles à un nom de domaine, jouant un rôle clé dans l'authentification des courriels, la sécurité, la vérification de propriété et la configuration de divers services. Une gestion appropriée des enregistrements TXT contribue à renforcer la confiance dans vos communications et services en ligne.</p>
	</div>

	<div>
		<h2 class="mb-4 mt-5" id="changer-dns"><a href="#changer-dns" class="ancre"><i class="fa-solid fa-link"></i> Comment changer mes DNS ?</a></h2>

		<div id="windows-10-11">
			<h3 class="mb-3"><a href="#windows-10-11" class="ancre"><i class="fa-solid fa-link"></i> <i style="color: #0278d4;" class="fa-brands fa-windows"></i> <span style="color: #0278d4;">Windows 10 et 11</span></a></h3>

			<ol>
				<li>Ouvrez les <span class="fw-bold">Paramètres</span> > <span class="fw-bold">Paramètres du PC</span></li>
				<li>Cliquez sur <span class="fw-bold">Réseau et Internet</span></li>
				<li>À gauche, sélectionnez sur <span class="fw-bold">Wi-Fi</span> ou <span class="fw-bold">Ethernet</span> selon le type de connexion que vous utilisez</li>
				<li>Cliquez sur votre connexion réseau active</li>
				<li>À la ligne <span class="fw-bold">Attribution du serveur DNS</span>, cliquez sur <span class="fw-bold">Modifier</span></li>
				<li>Choisir <span class="fw-bold">Manuel</span> dans la liste déroulante</li>
				<li>Activez <span class="fw-bold">IPv4</span> puis remplissez les champs <span class="fw-bold">DNS préféré</span> et <span class="fw-bold">Autre DNS</span> par les serveurs DNS primaires et secondaires souhaités</li>
				<li>Si disponible, faites de même avec <span class="fw-bold">IPv6</span></li>
				<li>Cliquez sur <span class="fw-bold">Enregistrer</span></li>
				<li>Redémarrer votre ordinateur</li>
			</ol>
		</div>

		<div id="windows-xp-vista-7-8-10-11">
			<h3 class="mb-3"><a href="#windows-xp-vista-7-8-10-11" class="ancre"><i class="fa-solid fa-link"></i> <i style="color: #0278d4;" class="fa-brands fa-windows"></i> <span style="color: #0278d4;">Windows XP, Vista, 7, 8, 8.1, 10 et 11</span> (<em>Panneau de configuration</em>)</a></h3>

			<ol>
				<li>Ouvrez le <span class="fw-bold">Panneau de configuration</span> > <span class="fw-bold">Réseau et Internet</span></li>
				<li>Cliquez sur <span class="fw-bold">Centre Réseau et partage</span></li>
				<li>Dans le panneau de gauche, cliquez sur <span class="fw-bold">Modifier les paramètres de la carte</span></li>
				<li>Double-cliquez ou faites un clic droit sur la connexion dont vous souhaitez modifier les DNS</li>
				<li>Dans la fenêtre, cliquez sur <span class="fw-bold">Propriétés</span></li>
				<li>Sélectionnez <span class="fw-bold">Protocole Internet version 4 (TCP/IPv4)</span> puis cliquez sur <span class="fw-bold">Propriétés</span></li>
				<li>Cochez <span class="fw-bold">Utiliser l’adresse de serveur DNS suivante</span> puis entrez les adresses IPv4 de votre DNS dans <span class="fw-bold">Serveur DNS préféré</span> et <span class="fw-bold">Serveur DNS auxiliaire</span></li>
				<li>Si disponible, faites de même avec le protocole <span class="fw-bold">Protocole Internet version 6 (TCP/IPv6)</span></li>
				<li>Fermez les différentes fenêtres</li>
				<li>Redémarrer votre ordinateur</li>
			</ol>
		</div>

		<div id="windows-10-11-powershell">
			<h3 class="mb-3"><a href="#windows-10-11-powershell" class="ancre"><i class="fa-solid fa-link"></i> <i style="color: #0278d4;" class="fa-brands fa-windows"></i> <span style="color: #0278d4;">Windows 10 et 11</span> (PowerShell)</a></h3>

			<ol>
				<li>Ouvrez <span class="fw-bold">PowerShell</span> en tant qu’administrateur</li>
				<li>Identifier le nom de votre carte réseau :<br><code>Get-NetAdapter</code><?= btnCopie('Get-NetAdapter'); ?></li>
				<li>Notez le nom de l’interface réseau (<span class="fw-bold">InterfaceAlias</span>) dont vous souhaitez modifier les serveurs DNS. Par exemple : « <span class="fw-bold">Ethernet</span> »</li>
				<li>Pour définir les DNS de <a href="https://www.fdn.fr/actions/dns/" <?= $onclick; ?>><span class="fw-bold">FDN</span></a> sur l’interface réseau « <span class="fw-bold">Ethernet</span> » pour le <span class="fw-bold">protocole IPv4</span> :<br><code>Set-DnsClientServerAddress -InterfaceAlias "Ethernet" -ServerAddresses ("80.67.169.12", "80.67.169.40")</code><?= btnCopie('Set-DnsClientServerAddress -InterfaceAlias &quot;Ethernet&quot; -ServerAddresses (&quot;80.67.169.12&quot;, &quot;80.67.169.40&quot;)'); ?></li>
				<li>Pour le <span class="fw-bold">protocole IPv6</span> :<br><code>Set-DnsClientServerAddress -InterfaceAlias "Ethernet" -ServerAddresses ("2001:910:800::12", "2001:910:800::40") -AddressFamily IPv6</code><?= btnCopie('Set-DnsClientServerAddress -InterfaceAlias &quot;Ethernet&quot; -ServerAddresses (&quot;2001:910:800::12&quot;, &quot;2001:910:800::40&quot;) -AddressFamily IPv6'); ?></li>
				<li>Vérifier que les DNS ont bien été ajoutés à votre réseau :<br><code>Get-DnsClientServerAddress</code><?= btnCopie('Get-DnsClientServerAddress'); ?></li>
				<li>Pour réinitialiser les serveurs DNS à leur valeur par défaut (ils seront désormais automatiquement définis via le DHCP) :<br><code>Set-DnsClientServerAddress -InterfaceAlias "Ethernet" -ResetServerAddresses</code><?= btnCopie('Set-DnsClientServerAddress -InterfaceAlias &quot;Ethernet&quot; -ResetServerAddresses'); ?></li>
				<li>Redémarrer votre ordinateur</li>
			</ol>
		</div>

		<div id="windows-xp-vista-7-8-10-11-cmd">
			<h3 class="mb-3"><a href="#windows-xp-vista-7-8-10-11-cmd" class="ancre"><i class="fa-solid fa-link"></i> <i style="color: #0278d4;" class="fa-brands fa-windows"></i> <span style="color: #0278d4;">Windows XP, Vista, 7, 8, 8.1, 10 et 11</span> (invite de commandes)</a></h3>

			<ol>
				<li>Ouvrez l’invite de commandes en tant qu’administrateur</li>
				<li>Identifier le nom de votre carte réseau :<br><code>netsh interface show interface</code><?= btnCopie('netsh interface show interface'); ?></li>
				<li>Notez le nom de l’interface réseau dont vous souhaitez modifier les serveurs DNS. Par exemple : « <span class="fw-bold">Wi-Fi</span> » ou « <span class="fw-bold">Connexion au réseau local</span> »</li>
				<li>Pour définir les serveurs DNS d’une interface réseau, saisissez les commandes suivantes :</li>
				<li>
					<ul>
						<li>
							pour le protocole IPv4 :
							<ul>
								<li><code>netsh interface ipv4 set dnsservers "Wi-Fi" static 1.1.1.1 primary</code><?= btnCopie('netsh interface ipv4 set dnsservers &quot;Nom de l’interface réseau&quot; static 1.1.1.1 primary'); ?></li>
								<li><code>netsh interface ipv4 add dnsservers "Wi-Fi" 1.0.0.1 index=2</code><?= btnCopie('netsh interface ipv4 add dnsservers &quot;Nom de l’interface réseau&quot; 1.0.0.1 index=2'); ?></li>
							</ul>
						</li>
						<li>
							pour le protocole IPv6 :
							<ul>
								<li><code>netsh interface ipv6 set dnsservers "Wi-Fi" static 1.1.1.1 primary</code><?= btnCopie('netsh interface ipv6 set dnsservers &quot;Nom de l’interface réseau&quot; static 1.1.1.1 primary'); ?></li>
								<li><code>netsh interface ipv6 add dnsservers "Wi-Fi" 1.0.0.1 index=2</code><?= btnCopie('netsh interface ipv6 add dnsservers &quot;Nom de l’interface réseau&quot; 1.0.0.1 index=2'); ?></li>
							</ul>
						</li>
					</ul>
				</li>
				<li>
					Pour définir les DNS de <a href="https://one.one.one.one/" <?= $onclick; ?>>Cloudflare</a> sur l’interface réseau « <span class="fw-bold">Wi-Fi</span> » :

					<ul>
						<li>pour le protocole IPv4 :
							<ul>
								<li><code>netsh interface ipv4 set dnsservers "Wi-Fi" static 1.1.1.1 primary</code><?= btnCopie('netsh interface ipv4 set dnsservers &quot;Wi-Fi&quot; static 1.1.1.1 primary'); ?></li>
								<li><code>netsh interface ipv4 add dnsservers "Wi-Fi" 1.0.0.1 index=2</code><?= btnCopie('netsh interface ipv4 add dnsservers &quot;Wi-Fi&quot; 1.0.0.1 index=2'); ?></li>
							</ul>
						</li>
						<li>
							pour le protocole IPv6 :
							<ul>
								<li><code>netsh interface ipv6 set dnsservers "Wi-Fi" static 2606:4700:4700::1111 primary</code><?= btnCopie('netsh interface ipv6 set dnsservers &quot;Wi-Fi&quot; static 2606:4700:4700::1111 primary'); ?></li>
								<li><code>netsh interface ipv6 add dnsservers "Wi-Fi" 2606:4700:4700::1001 index=2</code><?= btnCopie('netsh interface ipv6 add dnsservers &quot;Wi-Fi&quot; 2606:4700:4700::1001 index=2'); ?></li>
							</ul>
						</li>
					</ul>
				</li>
				<li>
					Vérifiez que les DNS ont bien été définis sur l’interface réseau :

					<ul>
						<li>
							pour le protocole IPv4 :
							<ul>
								<li><code>netsh interface ipv4 show dnsservers "Wi-Fi"</code><?= btnCopie('netsh interface ipv4 show dnsservers &quot;Wi-Fi&quot;'); ?></li>
							</ul>
						</li>
						<li>
							pour le protocole IPv6 :
							<ul>
								<li><code>netsh interface ipv6 show dnsservers "Wi-Fi"</code><?= btnCopie('netsh interface ipv6 show dnsservers &quot;Wi-Fi&quot;'); ?></li>
							</ul>
						</li>
					</ul>
				</li>
				<li>Pour réinitialiser les serveurs DNS à leur valeur par défaut (ils seront désormais automatiquement définis via le DHCP) :<br><code>netsh interface ipv4 set dnsservers "Wi-Fi" dhcp</code></li>
				<li>Redémarrer votre ordinateur</li>
			</ol>
		</div>

		<div id="debian-cli">
			<h3 class="mb-3"><a href="#debian-cli" class="ancre"><i class="fa-solid fa-link"></i> <i style="color: #a80030;" class="fa-brands fa-debian"></i> <span style="color: #a80030;">Debian</span> (CLI)</a></h3>

			<div class="mb-4" id="debian-methode-1">
				<p><span class="fw-bold">Méthode 1</span> : modification du fichier <strong>/etc/resolv.conf</strong></p>

				<ol>
					<li>
						Ouvrez le fichier <code>/etc/resolv.conf</code> :
						<ul>
							<li><code>sudo nano /etc/resolv.conf</code><?= btnCopie('sudo nano /etc/resolv.conf'); ?></li>
						</ul>
					</li>
					<li>
						Ajoutez ou modifiez les lignes pour spécifier vos serveurs DNS. Par exemple, pour utiliser les DNS de Google, vous pouvez écrire :
						<ul>
							<li><code>nameserver 8.8.8.8<br>nameserver 8.8.4.4</code><?= btnCopie('nameserver 8.8.8.8'."\n".'nameserver 8.8.4.4'); ?></li>
						</ul>
					</li>
					<li>Sauvegardez et quittez l'éditeur</li>
					<li>
						Redémarrez les services réseau pour appliquer les changements :
						<ul>
							<li><code>sudo systemctl restart networking</code><?= btnCopie('sudo systemctl restart networking'); ?></li>
						</ul>
					</li>
				</ol>
			</div>

			<div id="debian-methode-2">
				<p><span class="fw-bold">Méthode 2</span> : configuration via <strong>/etc/network/interfaces</strong></p>

				<p>Si vous utilisez une configuration statique pour vos interfaces réseau, vous pouvez ajouter vos serveurs DNS dans le fichier <code>/etc/network/interfaces</code></p>

				<ol>
					<li>
						Ouvrez le fichier <code>/etc/network/interfaces</code> :
						<ul>
							<li><code>sudo nano /etc/network/interfaces</code><?= btnCopie('sudo nano /etc/network/interfaces'); ?></li>
						</ul>
					</li>
					<li>
						Trouvez l'interface que vous souhiatez modifier (par exemple <code>eth0</code>) et ajoutez les lignes DNS comme suit :
						<ul>
							<li><code>iface eth0 inet static<br>&nbsp;&nbsp;&nbsp;&nbsp;address 192.168.1.100<br>&nbsp;&nbsp;&nbsp;&nbsp;netmask 255.255.255.0<br>&nbsp;&nbsp;&nbsp;&nbsp;gateway 192.168.1.1<br>&nbsp;&nbsp;&nbsp;&nbsp;dns-nameservers 8.8.8.8 8.8.4.4</code><?= btnCopie('iface eth0 inet static'."\n".'&nbsp;&nbsp;&nbsp;&nbsp;address 192.168.1.100'."\n".'&nbsp;&nbsp;&nbsp;&nbsp;netmask 255.255.255.0'."\n".'&nbsp;&nbsp;&nbsp;&nbsp;gateway 192.168.1.1'."\n".'&nbsp;&nbsp;&nbsp;&nbsp;dns-nameservers 8.8.8.8 8.8.4.4'); ?></li>
						</ul>
					</li>
					<li>Sauvegardez et quittez l'éditeur</li>
					<li>
						Redémarrez les services réseau pour appliquer les changements :
						<ul>
							<li><code>sudo systemctl restart networking</code><?= btnCopie('sudo systemctl restart networking'); ?></li>
						</ul>
					</li>
				</ol>
			</div>

			<div id="debian-methode-3">
				<p><span class="fw-bold">Méthode 3</span> : utilisation de <a href="https://wiki.debian.org/fr/NetworkManager">nmcli</a> (pour <strong>NetworkManager</strong>)</p>

				<p>Si vous utilisez NetworkManager pour gérer vos connexions réseau, vous pouvez utiliser l'<strong>outil nmcli</strong> pour configurer les serveurs DNS.</p>

				<ol>
					<li>
						Affichez la liste des connexions disponibles :
						<ul>
							<li><code>nmcli connection show</code><?= btnCopie('nmcli connection show'); ?></li>
						</ul>
					</li>
					<li>
						Modifiez la connexion souhaitée (par exemple, Wired connection 1) pour définir les serveurs DNS :
						<ul>
							<li><code>sudo nmcli connection modify 'Wired connection 1' ipv4.dns "8.8.8.8 8.8.4.4"</code><?= btnCopie('sudo nmcli connection modify \'Wired connection 1\' ipv4.dns "8.8.8.8 8.8.4.4"'); ?></li>
						</ul>
					</li>
					<li>
						Appliquez les changements en réactivant la connexion :
						<ul>
							<li><code>sudo nmcli connection down 'Wired connection 1' && sudo nmcli connection up 'Wired connection 1'</code><?= btnCopie('sudo nmcli connection down \'Wired connection 1\' && sudo nmcli connection up \'Wired connection 1\''); ?></li>
						</ul>
					</li>
					<li>Sauvegardez et quittez l'éditeur</li>
					<li>
						Redémarrez les services réseau pour appliquer les changements :
						<ul>
							<li><code>sudo systemctl restart networking</code><?= btnCopie('sudo systemctl restart networking'); ?></li>
						</ul>
					</li>
				</ol>
			</div>
		</div>

		<div id="ubuntu">
			<h3 class="mb-3"><a href="#ubuntu" class="ancre"><i class="fa-solid fa-link"></i> <i style="color: #e95420;" class="fa-brands fa-ubuntu"></i> <span style="color: #e95420;">Ubuntu</span></a></h3>

			<div id="ubuntu-methode-1">
				<p><span class="fw-bold">Méthode 1</span> : via Fichier de configuration</p>

				<p>Depuis <span class="fw-bold">Ubuntu 18.04</span>, vous devez effectuer la modification dans les configurations <code>netplan</code> à <code>/etc/netplan/*.yaml</code>, ce fichier pourrait être <code>50-cloud-init.yaml</code> ou quelque chose comme <code>01-netcfg.yaml</code>.</p>

				<ol>
					<li>
						Ouvrez le fichier <code>/etc/netplan/01-netcfg.yaml</code> :
						<ul>
							<li><code>sudo nano /etc/netplan/01-netcfg.yaml</code><?= btnCopie('sudo nano /etc/netplan/01-netcfg.yaml'); ?></li>
						</ul>
					</li>
					<li>
						Remplacez :<br>
						<code>nameservers:<br>addresses:<br>- 198.51.100.1<br>- 203.0.113.1</code><br>
						par<br>

						<code>nameservers:<br>addresses:<br>- 185.222.222.222<br>- 45.11.45.11</code>
					</li>
					<li>
						Appliquez les modifications :
						<ul>
							<li><code>sudo netplan apply</code><?= btnCopie('sudo netplan apply'); ?></li>
						</ul>
					</li>
					<li>
						Vous pouvez vérifier votre attribution DNS actuelle :
						<ul>
							<li><code>systemd-resolve --status</code><?= btnCopie('systemd-resolve --status'); ?></li>
						</ul>
					</li>
				</ol>
			</div>

			<div id="ubuntu-methode-2">
				<p><span class="fw-bold">Méthode 2</span> : via l’interface graphique</p>

				<ol>
					<li>Cliquez sur l’icône <span class="fw-bold">Applications</span> <i class="fa-solid fa-table-cells"></i> dans la barre de menu de gauche</li>
					<li>Cliquez sur <span class="fw-bold">Paramètres</span>, puis sur <span class="fw-bold">Réseau</span></li>
					<li>Trouvez votre connexion Internet dans le volet de droite, puis cliquez sur l’icône en forme d'engrenage <i class="fa-solid fa-gear"></i></li>
					<li>Cliquez sur l’onglet <span class="fw-bold">IPv4</span> ou <span class="fw-bold">IPv6</span> pour afficher vos paramètres DNS</li>
					<li>Définissez l’option Automatique sur l’entrée DNS sur <span class="fw-bold">Désactivé</span></li>
					<li>Remplacez ces adresses par les adresses DNS :
						<ul>
							<li>IPv4 : <span class="fw-bold">1.1.1.1, 1.0.0.1</span></li>
							<li>IPv6 : <span class="fw-bold">2606:4700:4700::1111, 2606:4700:4700::1001</span></li>
						</ul>
					</li>
					<li>Cliquez sur <span class="fw-bold">Appliquer</span>, puis redémarrez votre navigateur</li>
					<li>Votre appareil dispose désormais de serveurs DNS plus rapides et plus privés</li>
				</ol>
			</div>
		</div>
	</div>

	<div>
		<h2 class="mb-4 mt-5" id="dns-prives"><a href="#dns-prives" class="ancre"><i class="fa-solid fa-link"></i> DNS privés</a></h2>

		<div class="container">
			<div class="row justify-content-center align-items-center text-center border border-bottom-0 px-0 py-2">
				<div class="col-2 col-lg-2 d-none d-lg-block">Fournisseur</div>
				<div class="col-3 col-lg-4">IPv4</div>
				<div class="col-3 col-lg-4">IPv6</div>
				<div class="col-3 col-lg-1">DNS over HTTPS</div>
				<div class="col-3 col-lg-1">DNS over TLS</div>
			</div>

			<?php
			$dns = [
				'Cloudflare'			=> ['https://one.one.onet.one/fr-FR/',							'1.1.1.1',				'1.0.0.1',				'2606:4700:4700::1111',			'2606:4700:4700::1001',			true,	true],
				'Stéphane Bortzmeyer'	=> ['https://www.bortzmeyer.org/doh-bortzmeyer-fr-policy.html',	'193.70.85.11',			'',						'2001:41d0:302:2200::180',		'',								true,	true],
				'Wikimédia'				=> ['https://meta.wikimedia.org/wiki/Wikimedia_DNS',			'185.71.138.138',		'',						'2001:67c:930::1',				'',								true,	true],
				'DNS4ALL'				=> ['https://dns4all.eu/',										'194.0.5.3',			'',						'2001:678:8::3',				'',								true,	true],
				'FDN'					=> ['https://www.fdn.fr/actions/dns/',							'80.67.169.12',			'80.67.169.40',			'2001:910:800::12',				'2001:910:800::40',				true,	true],
				'Quad9'					=> ['https://www.quad9.net/',									'9.9.9.9',				'149.112.112.112',		'2620:fe::fe',					'2620:fe::9',					true,	true],
				'OpenDNS'				=> ['https://www.opendns.com/',									'208.67.222.222',		'208.67.220.220',		'2620:119:35::35',				'2620:119:53::53',				true,	true],
				'Google'				=> ['https://dns.google/',										'8.8.8.8',				'8.8.4.4',				'2001:4860:4860::8888',			'2001:4860:4860::8844',			true,	true],
				'Verisign'				=> ['https://www.verisign.com/',								'64.6.64.6',			'64.6.65.6',			'2620:74:1b::1:1',				'2620:74:1c::2:2',				false,	true],
				'DNS.WATCH'				=> ['https://dns.watch/',										'84.200.69.80',			'84.200.70.40',			'2001:1608:10:25::1c04:b12f',	'2001:1608:10:25::9249:d69b',	false,	false],
				'DNS.SB'				=> ['https://dns.sb/',											'185.222.222.222',		'45.11.45.11',			'2a09::',						'2a11::',						true,	true],
			];

			foreach($dns as $d => $v)
			{
				echo '<div class="row justify-content-center align-items-center text-center border '.($d !== 'DNS.SB' ? 'border-bottom-0' : 'border-bottom').' p-2">
					<div class="col-12 col-lg-2 mb-3 mb-lg-0"><a href="'.$v[0].'" '.$onclick.'>'.$d.'</a></div>
					<div class="col-12 col-lg-4 mb-3 mb-lg-0"><code>'.$v[1].'</code> '.btnCopie($v[1]).(!empty($v[2]) ? '<br><code>'.$v[2].'</code> '.btnCopie($v[2]) : null).'</div>
					<div class="col-12 col-lg-4 mb-3 mb-lg-0 text-break">'.(!empty($v[3]) ? '<code>'.$v[3].'</code> '.btnCopie($v[3]) : '<i class="fa-solid fa-x text-danger"></i>').(!empty($v[4]) ? '<br><code>'.$v[4].'</code> '.btnCopie($v[4]) : null).'</div>
					<div class="col-6 col-lg-1"><span class="text-'.($v[5] === true ? 'success"><i class="fa-solid fa-check me-2"></i>' : 'danger"><i class="fa-solid fa-x me-2"></i>').'<abbr title="DNS over HTTPS">DoH</abbr></span></div>
					<div class="col-6 col-lg-1"><span class="text-'.($v[6] === true ? 'success"><i class="fa-solid fa-check me-2"></i>' : 'danger"><i class="fa-solid fa-x me-2"></i>').'<abbr title="DNS over TLS">DoT</abbr></span></div>
				</div>';
			}

			echo '<h2 class="mb-4 mt-5" id="dns-fai"><a href="#dns-fai" class="ancre"><i class="fa-solid fa-link"></i> DNS des <abbr title="Fournisseur d’accès à Internet">FAI</abbr></a></h2>';

			$dnsFAI = [
				'Free'					=> ['https://www.free.fr/',										'212.27.40.240',		'212.27.40.241',		'2a01:e0c:1:1599::22',			'2a01:e0c:1:1599::23',			false,	false],
				'SFR'					=> ['https://www.sfr.fr/',										'109.0.66.10',			'109.0.66.20',			'',								'',								false,	false],
				'Orange'				=> ['https://www.orange.fr/',									'80.10.246.2',			'80.10.246.129',		'',								'',								false,	false],
				'Bouygues'				=> ['https://www.bouyguestelecom.fr/',							'194.158.122.10',		'194.158.122.15',		'2001:860:b0ff:1::1',			'2001:860:b0ff:1::2',			false,	false],
				'OVH'					=> ['https://www.ovhtelecom.fr/',								'91.121.161.184',		'91.121.164.227',		'2001:41d0:1:e2b8::1',			'2001:41d0:1:e5e3::1',			false,	false],
			];

			foreach($dnsFAI as $d => $v)
			{
				echo '<div class="row justify-content-center align-items-center text-center border '.($d !== 'DNS.SB' ? 'border-bottom-0' : 'border-bottom').' p-2">
					<div class="col-12 col-lg-2 mb-3 mb-lg-0"><a href="'.$v[0].'" '.$onclick.'>'.$d.'</a></div>
					<div class="col-12 col-lg-4 mb-3 mb-lg-0"><code>'.$v[1].'</code> '.btnCopie($v[1]).(!empty($v[2]) ? '<br><code>'.$v[2].'</code> '.btnCopie($v[2]) : null).'</div>
					<div class="col-12 col-lg-4 mb-3 mb-lg-0 text-break">'.(!empty($v[3]) ? '<code>'.$v[3].'</code> '.btnCopie($v[3]) : '<i class="fa-solid fa-x text-danger"></i>').(!empty($v[4]) ? '<br><code>'.$v[4].'</code> '.btnCopie($v[4]) : null).'</div>
					<div class="col-6 col-lg-1"><span class="text-'.($v[5] === true ? 'success"><i class="fa-solid fa-check me-2"></i>' : 'danger"><i class="fa-solid fa-x me-2"></i>').'<abbr title="DNS over HTTPS">DoH</abbr></span></div>
					<div class="col-6 col-lg-1"><span class="text-'.($v[6] === true ? 'success"><i class="fa-solid fa-check me-2"></i>' : 'danger"><i class="fa-solid fa-x me-2"></i>').'<abbr title="DNS over TLS">DoT</abbr></span></div>
				</div>';
			}
			?>
		</div>
	</div>
</div>
<?php
require_once 'a_footer.php';