<?php
require_once '../../config/config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
	$nom = trim($_POST['nom_logiciel']);
	$image = trim($_POST['image']);
	$description = $_POST['description'];
	$site_officiel = trim($_POST['site_officiel']);
	$infos = $_POST['infos'];
	$site_adobe_plus_informations = trim($_POST['site_adobe_plus_informations']);
	$version = trim($_POST['version']);
	$nfo = trim($_POST['nfo']);
	$comment_installer = trim($_POST['comment_installer']);

	$modifications = $_POST['modifications'];
	$modifications = str_replace(["\r\n", "\r"], "\n", $modifications);
	$modifications = preg_replace('/\n{2,}/', "\n", $modifications);
	$modifications = preg_replace('/^- /m', '[*]', $modifications);
	$modifications = preg_replace('/\.$/m', '[/*]', $modifications);

	$nom_du_lien = !empty($_POST['nom_du_lien']) ? str_ireplace('.x64.Multilingual.part1.rar', '', trim($_POST['nom_du_lien'])) : null;

	$liens = [];
	for($i = 0; $i < 10; $i++)
	{
		if(!empty($nom_du_lien) AND !empty($_POST['fichier1'][$i]) AND !empty($_POST['taille_fichier'][$i]))
		{
			$iRar = ($i + 1);

			$idFichier = secuChars($_POST['fichier1'][$i]);
			$tailleFichier = secuChars($_POST['taille_fichier'][$i]);

			$liens[] = $nom_du_lien.'.Multilingual.part'.$iRar.'.rar - [b]'.$tailleFichier.'[/b] : [url]https://1fichier.com/?'.$idFichier.'[/url]'."\n";
		}
	}

	$msg = '[center][h]'.$nom.' '.$version.' [Win x64 Multi Précracké][/h][/center]

[img]'.$image.'[/img]

[quote]'.$description.'[/quote]

[url='.$site_officiel.']Site officiel '.$nom.' [img]https://i.ibb.co/wpKCGCW/fr.png[/img][/url]

[center][h]Fonctionnalités d’'.$nom.'[/h][/center]

'.$infos.'

[url='.$site_adobe_plus_informations.']Plus d’informations sur les fonctionnalités et nouveautés d’'.$nom.' (Adobe — [img]https://i.ibb.co/wpKCGCW/fr.png[/img])[/url]

[center][h]Modifications '.$version.'[/h][/center]

[list=*]'.htmlspecialchars($modifications).'[/list]

[center][h]Télécharger '.$nom.'[/h][/center]

[b]1fichier[/b]

'.(!empty($liens) ? implode($liens) : null).'
[b]Mot de passe :[/b] [spoiler]ultimeZone2026[/spoiler]

[center][h]Note Importante[/h][/center]

[color=red]Les logiciels Adobe en version crackée ainsi que les outils d’IA ne seront jamais entièrement fonctionnels, car les calculs complexes et le traitement des images s’effectuent exclusivement sur leurs serveurs officiels.[/color]

[color=red]Bien qu’il s’agisse d’une version piratée, il est impossible de garantir une sécurité à 100 %. Toutefois, le cracker [b]M0nkrus[/b] est réputé pour sa fiabilité. Après le téléchargement, j’effectue systématiquement une vérification complète de tous les fichiers à l’aide de l’API de Virustotal.com sans y apporter la moindre modification.[/color]

[center][h]NFO[/h][/center]

[code]
'.$nfo.'[/code]

[center][h]Installer '.$nom.'[/h][/center]

'.$comment_installer;
}
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title>Ultim-Zone</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<link rel="icon" href="/favicon.ico">
	<link rel="stylesheet" href="/assets/css/vendors.css?<?= filemtime('../assets/css/vendors.css'); ?>">

	<script src="/assets/js/vendors.js?<?= filemtime('../assets/js/vendors.js'); ?>"></script>
</head>

<body>
<div class="container">
	<h1 style="font-size: 2rem;" class="my-5 text-center"><a href="ultim-zone" class="link-offset-2">Ultim-Zone</a></h1>

	<?php
	if(!empty($msg))
	{
		echo '<div class="col-12 col-lg-10 mx-auto">
			<h1 style="font-size: 2rem;" class="mb-4 text-center user-select-all">'.$nom.' '.$version.' [Win x64 Multi Précracké]</h1>

			<textarea style="height: 400px;" class="rounded p-3 w-100" onclick="this.select()">'.$msg.'</textarea>

			<hr style="height: 5px;" class="my-5 border border-0 bg-danger">
		</div>';
	}
	?>

	<div class="row">
		<div class="col-12 col-lg-11 mx-auto">
			<form action="/projets//ultim-zone" method="post" id="logiciels">
				<div class="row mb-3">
					<label class="col-2 col-form-label">Charger le modèle</label>
					<div class="col-10">
						<select class="form-select" id="modele" name="modele" required>
							<option value="" selected disabled>Choisir un modèle</option>
							<option disabled>──────────</option>
							<option value="acrobat_pro">Adobe Acrobat Pro 2025</option>
							<option value="after_effects">Adobe After Effects 2025</option>
							<option value="audition">Adobe Audition 2025</option>
							<option value="illustrator">Adobe Illustrator 2026</option>
							<option value="indesign">Adobe InDesign 2026</option>
							<option value="lightroom_classic">Adobe Lightroom Classic</option>
							<option value="photoshop">Adobe Photoshop 2026</option>
							<option value="premiere_pro">Adobe Premiere Pro 2025</option>
							<option value="substance_3d_designer">Adobe Substance 3D Designer</option>
							<option value="substance_3d_stager">Adobe Substance 3D Stager</option>
						</select>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-12 text-center" id="liens"></div>
				</div>

				<hr style="height: 5px;" class="my-4 border border-0 bg-danger">

				<div class="row mb-3">
					<label class="col-2 col-form-label">Version</label>
					<div class="col-10"><input type="text" name="version" class="form-control" autocomplete="off" required></div>
				</div>

				<div class="row mb-3">
					<label class="col-2 col-form-label">Modiciations</label>
					<div class="col-10"><textarea name="modifications" style="height: 300px;" class="rounded p-2 w-100" required></textarea> </div>
				</div>

				<div class="row mb-3">
					<label class="col-2 col-form-label">NFO</label>
					<div class="col-10"><textarea name="nfo" style="height: 300px;" class="rounded p-2 w-100" required></textarea> </div>
				</div>

				<div class="row mb-3">
					<label class="col-2 col-form-label">Noms des liens<br><br><strong>Adobe.XXXX.2026.uXX</strong><br><br><strong class="user-select-all">ultimeZone2026</strong></label>
					<div class="col-10">
						<div class="row mb-2">
							<div class="col">
								<input type="text" name="nom_du_lien" class="form-control" placeholder="Nom du lien" autocomplete="off" required>
							</div>
						</div>
						<hr>
						<?php
						for($i = 1; $i < 9; $i++)
						{
							echo '<div class="row mb-2">
								<div class="col-4">
									<input type="text" name="fichier1[]" class="form-control" placeholder="ID 1fichier '.$i.'" autocomplete="off" '.($i < 3 ? 'required' : null).'>
								</div>
								<div class="col-3">
									<input type="text" name="taille_fichier[]" value="750 Mo" class="form-control" placeholder="Taille du fichier '.$i.'" autocomplete="off">
								</div>
							</div>';
						}
						?>
					</div>
				</div>

				<hr style="height: 5px;" class="my-4 border border-0 bg-danger">

				<div class="row mb-3">
					<label class="col-2 col-form-label">Nom du logiciel</label>
					<div class="col-10">
						<div id="nom_logicielView" class="form-control overflow-auto" style="max-height: 250px; white-space: pre-wrap;"></div>
						<input type="hidden" name="nom_logiciel" id="nom_logiciel" autocomplete="off">
					</div>
				</div>

				<div class="row mb-3">
					<label class="col-2 col-form-label">Image</label>
					<div class="col-10">
						<div id="imageView" class="form-control overflow-auto" style="max-height: 250px; white-space: pre-wrap;"></div>
						<input type="hidden" name="image" id="image" autocomplete="off">
					</div>
				</div>

				<div class="row mb-3">
					<label class="col-2 col-form-label">Description</label>
					<div class="col-10">
						<div id="descriptionView" class="form-control overflow-auto" style="max-height: 250px; white-space: pre-wrap;"></div>
						<input type="hidden" name="description" id="description" autocomplete="off">
					</div>
				</div>

				<div class="row mb-3">
					<label class="col-2 col-form-label">Informations</label>
					<div class="col-10">
						<div id="infosView" class="form-control overflow-auto" style="max-height: 250px; white-space: pre-wrap;"></div>
						<input type="hidden" name="infos" id="infos" autocomplete="off">
					</div>
				</div>

				<div class="row mb-3">
					<label class="col-2 col-form-label">Site officiel</label>
					<div class="col-10">
						<div id="site_officielView" class="form-control overflow-auto" style="max-height: 250px; white-space: pre-wrap;"></div>
						<input type="hidden" name="site_officiel" id="site_officiel" autocomplete="off">
					</div>
				</div>

				<div class="row mb-3">
					<label class="col-2 col-form-label">Site officiel nouveautés</label>
					<div class="col-10">
						<div id="site_adobe_plus_informationsView" class="form-control overflow-auto" style="max-height: 250px; white-space: pre-wrap;"></div>
						<input type="hidden" name="site_adobe_plus_informations" id="site_adobe_plus_informations" autocomplete="off">
					</div>
				</div>

				<div class="row mb-3">
					<label class="col-2 col-form-label">Comment installer</label>
					<div class="col-10">
						<div id="comment_installerView" class="form-control overflow-auto" style="max-height: 250px; white-space: pre-wrap;"></div>
						<input type="hidden" name="comment_installer" id="comment_installer" autocomplete="off">
					</div>
				</div>

				<div class="text-center"><button type="submit" class="btn btn-primary">Valider</button></div>
			</form>
		</div>
	</div>
</div>

<script>
const commentInstallerCommun = `Si vous rencontrez une erreur avec l’installation ou un problème avec la licence du logiciel voici quelques conseils :\n\n[list=1]\n[*]On désinstalle tous les logiciels en rapport avec les logiciels [b]Adobe[/b] (on repart proprement)[/*]\n[*]On supprime les dossiers suivants :\n- C: Users _Utilisateur_ AppData Local Adobe\n- C: Users _Utilisateur_ AppData Roaming Adobe\n- C: ProgramData Adobe\n- C: Program Files (x86) Adobe\n- C: Program Files Adobe\n- C: Program Files (x86) Common Files Adobe\n[/*]\n[*]On télécharge [url=https://www.ccleaner.com/fr-fr/ccleaner/download/standard][b]CCleaner[/b][/url] et on nettoie [b]Windows[/b][/*]\n[*]On vérifie les mises à jour [b]Windows[/b] via [b]Windows Update[/b] : [url=https://lecrabeinfo.net/comment-faire-les-mises-a-jour-sur-windows-10.html]Tutoriel sur LeCrabeInfo.net[/url][/*]\n[*]On redémarre l’ordinateur[/*]\n[*]On re-lance le fichier [b]Set-up.exe[/b][/*]\n[*]Laissez vous guider ![/*]\n[/list]\n\n[color=#ff7b24][b]Note à propos du crack[/b][/color] : Si un message contextuel s’affiche concernant la fin de la période d’essai lors de l’utilisation du programme, exécutez le script [b]wintrust.cmd[/b] en tant qu’administrateur, avec l’antivirus désactivé. Vous devez redémarrer votre ordinateur et problème disparaîtra.\n\nSi ça ne fonctionne pas, suivez les [url=https://helpx.adobe.com/fr/creative-cloud/kb/cc-cleaner-tool-installation-problems.html]modalités d’utilisation de l’outil Creative Cloud Cleaner[/url] fournit par Adobe pour résoudre de nombreux problèmes courants concernant l’installation des produits Adobe.\n\n> [url=https://ultim-zone.in/topic-408385-tutoriel-bloquer-la-popup-adobe-page-1.html]Comment bloquer la popup Adobe[/url]\n> [url=https://helpx.adobe.com/ch_fr/enterprise/kb/network-endpoints.html]Domaines officiels des applications et services Adobe[/url]`;

const modeles = {
	acrobat_pro: {
		nom_logiciel: `Adobe Acrobat Pro 2025`,
		image: `https://iili.io/KL12FV4.jpg`,
		description: `Créez, modifiez et gérez des PDF où que vous soyez avec Adobe Acrobat, inclus dans Creative Cloud Pro.`,
		site_officiel: `https://www.adobe.com/fr/products/acrobat-pro-cc.html`,
		infos: `[list=*]\n[*]Édition directe des PDF : Modifier texte, images et mise en page comme un document Word[/*]\n[*]Création et gestion de formulaires interactifs : Champs remplissables, listes déroulantes, cases à cocher, collecte automatique des réponses[/*]\n[*]Reconnaissance de texte (OCR) avancée : Numériser des documents papier en PDF modifiables avec reconnaissance multilingue[/*]\n[*]Protection et sécurisation : Chiffrement, mot de passe, autorisations d’accès, rédaction définitive des données sensibles[/*]\n[*]Signature électronique intégrée (Adobe Sign) : Envoi, signature et suivi des documents entièrement en ligne et sécurisé[/*]\n[*]Compression et optimisation des PDF : Réduction efficace de la taille des fichiers sans perte critique de qualité[/*]\n[*]Comparaison de versions de documents PDF : Analyse rapide des différences entre deux fichiers pour révision ou validation[/*]\n[/list]`,
		site_adobe_plus_informations: `https://helpx.adobe.com/fr/acrobat/using/whats-new.html`,
		liens: [
			{ titre: "1fichier", url: "https://1fichier.com/" },
			{ titre: "Ultim-Zone", url: "https://ultim-zone.in/topic-411234-adobe-acrobat-pro-2025-25120693-win-x64-multi-precracke-page-1.html#p2518181" },
			{ titre: "1fichier (fichiers)", url: "https://1fichier.com/console/index.pl?mf" },
		]
	},

	after_effects: {
		nom_logiciel: `Adobe After Effects 2025`,
		image: `https://iili.io/KL12BlS.jpg`,
		description: `Créez des animations graphiques d’exception. Animez un logo ou un personnage. Ajoutez des effets spéciaux captivants. Avec After Effects, vous pouvez insuffler une dynamique inédite à un projet vidéo.`,
		site_officiel: `https://www.adobe.com/fr/products/aftereffects.html`,
		infos: `[list=*]\n[*]Permet de créer des animations en utilisant des images, des vidéos et des graphiques sur plusieurs calques.[/*]\n[*]Inclut une vaste gamme d'effets visuels comme le flou, la distorsion, les particules, et plus encore.[/*]\n[*]Suivi et stabilisation de mouvement pour intégrer des éléments graphiques dans des séquences vidéo.[/*]\n[*]Fusionner plusieurs éléments vidéo en une seule scène.[/*]\n[*]Utilisation de l'incrustation chromatique pour supprimer des arrière-plans (écran vert/bleu).[/*]\n[*]Utilisation de scripts pour automatiser les animations et les processus.[/*]\n[*]Création d'animations de texte sophistiquées et dynamiques.[/*]\n[*]Outils pour ajuster la couleur et le ton des séquences vidéo.[/*]\n[*]Options variées pour rendre et exporter des projets dans différents formats.[/*]\n[*]Compatibilité avec des plugins tiers pour étendre les fonctionnalités du logiciel.[/*]\n[*]Organisation des compositions complexes en les imbriquant dans d'autres compositions.[/*]\n[*]Création et animation de masques pour isoler ou modifier certaines parties d'une image.[/*]\n[*]Manipulation et animation d'objets en 3D, y compris l'importation de fichiers 3D.[/*]\n[*]Création d'éléments comme la fumée, le feu, la neige, etc.[/*]\n[*]Ajout et synchronisation d'effets sonores et de musique avec l'animation.[/*]\n[*]Création et animation de formes vectorielles directement dans le logiciel.[/*]\n[*]Interface utilisateur modulable selon les préférences de l'utilisateur.[/*]\n[*]Voir les changements et ajustements en temps réel sans avoir à rendre la composition.[/*]\n[/list]`,
		site_adobe_plus_informations: `https://helpx.adobe.com/fr/after-effects/using/whats-new.html`,
		liens: [
			{ titre: "1fichier", url: "https://1fichier.com/" },
			{ titre: "Ultim-Zone", url: "https://ultim-zone.in/topic-393992-adobe-after-effects-2024-2450052-win-x64-multi-precracke-page-1.html#p2361208" },
			{ titre: "1fichier (fichiers)", url: "https://1fichier.com/console/index.pl?mf" },
		]
	},

	audition: {
		nom_logiciel: `Adobe Audition 2025`,
		image: `https://iili.io/KbupwCu.jpg`,
		description: `Créez, mixez et concevez des effets sonores avec l’application de montage audio numérique de référence.`,
		site_officiel: `https://www.adobe.com/fr/products/audition.html`,
		infos: `[list=*]\n[*]Permet l'enregistrement de plusieurs pistes audio simultanément[/*]\n[*]Édition de fichiers audio sans altérer les fichiers source d'origine[/*]\n[*]Outils avancés pour mixer plusieurs pistes audio[/*]\n[*]Large gamme d'effets audio intégrés, tels que réverbération, égalisation, compression, et plus encore[/*]\n[*]Outils pour réduire ou éliminer les bruits de fond et les interférences[/*]\n[*]Fonctions pour réparer et restaurer des enregistrements audio endommagés[/*]\n[*]Visualisation et édition des fréquences audio dans le domaine spectral[/*]\n[*]Accès à une vaste bibliothèque de boucles et d'effets sonores pour enrichir les projets audio[/*]\n[*]Support des plugins VST et AU pour étendre les fonctionnalités[/*]\n[*]Automatisation des effets et des réglages de mixage sur des segments spécifiques[/*]\n[*]Synchronisation et montage de l'audio avec des fichiers vidéo[/*]\n[*]Gestion avancée des canaux audio pour des mixages complexes[/*]\n[*]Outils de mesure et d'analyse des niveaux audio, des fréquences, et de la dynamique[/*]\n[*]Support de divers formats audio pour l'importation et l'exportation[/*]\n[*]Outils spécialisés pour la création et l'édition de podcasts[/*]\n[*]Outils pour la gestion et l'optimisation de l'audio pour les diffusions en direct[/*]\n[*]Utilisation de modèles et de préréglages pour accélérer les workflows audio[/*]\n[*]Intégration transparente avec d'autres applications Adobe pour des workflows créatifs complets[/*]\n[*]Outils optimisés pour enregistrer et traiter les voix off[/*]\n[*]Outils de mastering pour finaliser et polir les projets audio pour la distribution[/*]\n[/list]`,
		site_adobe_plus_informations: `https://helpx.adobe.com/fr/audition/using/whats-new.html`,
		liens: [
			{ titre: "1fichier", url: "https://1fichier.com/" },
			{ titre: "Ultim-Zone", url: "https://ultim-zone.in/topic-393993-adobe-audition-2024-2441003-win-x64-multi-precracke-page-1.html#p2357824" },
			{ titre: "1fichier (fichiers)", url: "https://1fichier.com/console/index.pl?mf" },
		]
	},

	illustrator: {
		nom_logiciel: `Adobe Illustrator 2026`,
		image: `https://iili.io/KbYQ2oX.jpg`,
		description: `Gagnez du temps grâce aux performances décuplées de la dernière version d'Illustrator. Partez d’une image vectorielle, choisissez les couleurs et ajoutez les détails qui correspondent à votre style. Créez des motifs parfaits et personnalisez-les jusqu’à obtenir le résultat souhaité. Les effets les plus utilisés sont jusqu’à 5 fois plus rapides, vous permettant de finir un projet en un temps record.`,
		site_officiel: `https://www.adobe.com/fr/products/illustrator.html`,
		infos: `[list=*]\n[*][b]Text to Vector Graphic[/b] : Utilisez des descriptions textuelles pour générer rapidement des graphismes vectoriels basés sur un thème ou un style, parfait pour les logos, les scènes, et les motifs répétitifs. Cette fonctionnalité permet également des gradients vectoriels complexes et des motifs sans couture, simplifiant les effets visuels avec des courbes précises et des regroupements logiques des éléments[/*]\n[*][b]Generative Shape Fill[/b] : Remplissez des formes avec des motifs ou textures en utilisant des prompts textuels. Ce processus alimenté par l’IA Firefly permet de créer des designs vectoriels élaborés en quelques secondes[/*]\n[*][b]Objects on Path[/b] : Alignez et disposez facilement des objets le long de chemins droits ou courbés, facilitant la mise en page dynamique pour divers éléments sur la toile[/*]\n[*][b]Mockup[/b] : Visualisez rapidement des maquettes pour voir vos graphismes appliqués sur des objets réels comme des t-shirts ou des emballages, ce qui est idéal pour tester l’apparence de vos conceptions sur des supports variés[/*]\n[*][b]Retype[/b] : Identifiez et remplacez les polices de texte rasterisé ou vectorisé pour le rendre modifiable dans Illustrator, en recherchant automatiquement les polices similaires sur Adobe Fonts[/*]\n[*][b]Outils améliorés pour la manipulation du texte asiatique[/b] : Le Reflow Viewer simplifie la gestion des textes en langues asiatiques, permettant des ajustements rapides pour les versions plus anciennes et nouvelles[/*]\n[/list]`,
		site_adobe_plus_informations: `https://helpx.adobe.com/fr/illustrator/using/whats-new.html`,
		liens: [
			{ titre: "1fichier", url: "https://1fichier.com/" },
			{ titre: "Ultim-Zone", url: "https://ultim-zone.in/topic-399228-adobe-illustrator-2025-2901-win-x64-multi-precracke-page-1.html#p2405128" },
			{ titre: "1fichier (fichiers)", url: "https://1fichier.com/console/index.pl?mf" },
		]
	},

	indesign: {
		nom_logiciel: `Adobe InDesign 2026`,
		image: `https://iili.io/KL12CU7.png`,
		description: `Boostez vos workflows grâce aux nouvelles fonctionnalités d’InDesign et gagnez en efficacité sur chaque mise en page.`,
		site_officiel: `https://www.adobe.com/fr/products/indesign.html`,
		infos: `[list=*]\n[*][b]Gabarits et styles[/b] : création de modèles réutilisables avec des pages maîtres, styles de paragraphe, caractère, objet et tableau pour une mise en forme cohérente[/*]\n[*][b]Grilles et repères[/b] : gestion précise des alignements, marges et colonnes pour des compositions équilibrées[/*]\n[*][b]Contrôle typographique avancé[/b] : gestion fine de l’interlignage, du crénage, des ligatures et des styles OpenType[/*]\n[*][b]Chaînage de blocs de texte[/b] : permet de relier plusieurs cadres pour un flux de texte continu[/*]\n[*][b]Compatibilité native avec Photoshop et Illustrator[/b] : importation directe de fichiers PSD, AI ou PDF avec gestion des calques[/*]\n[*][b]Gestion des couleurs (CMJN, RVB, Pantone)[/b] : profil ICC intégré pour l’impression ou le numérique[/*]\n[*][b]Variables de texte et champs de fusion de données[/b] : création automatique de catalogues, cartes de visite ou brochures personnalisées[/*]\n[*][b]Scripts et automatisations (JavaScript, AppleScript, VBScript)[/b] : automatisation de tâches répétitives[/*]\n[*][b]Export PDF professionnel[/b] : prise en charge du PDF/X, des repères, fonds perdus et profils colorimétriques[/*]\n[*][b]Export ePub et HTML5[/b] : création de livres numériques, magazines interactifs ou contenus web[/*]\n[*][b]Intégration avec Adobe Creative Cloud[/b] : travail collaboratif, commentaires en ligne et versionnage[/*]\n[*][b]Publication en ligne (Publish Online)[/b] : partage immédiat de documents interactifs sur le web sans export manuel[/*]\n[/list]`,
		site_adobe_plus_informations: `https://helpx.adobe.com/fr/indesign/using/whats-new.html`,
		liens: [
			{ titre: "1fichier", url: "https://1fichier.com/" },
			{ titre: "Ultim-Zone", url: "https://ultim-zone.in/topic-417942-adobe-indesign-2026-210-win-x64-multi-precracke-page-1.html#p2600027" },
			{ titre: "1fichier (fichiers)", url: "https://1fichier.com/console/index.pl?mf" },
		]
	},

	lightroom_classic: {
		nom_logiciel: `Adobe Lightroom Classic`,
		image: `https://iili.io/KL12oJ9.jpg`,
		description: `Tirez parti d’outils de retouche performants et effectuez des ajustements précis sur ordinateur.`,
		site_officiel: `https://www.adobe.com/fr/products/photoshop-lightroom-classic.html`,
		infos: `[list=*]\n[*]Importation et exportation des photos[/*]\n[*]Classement par dossiers et collections[/*]\n[*]Notation (étoiles, couleurs, drapeaux)[/*]\n[*]Filtrage et recherche d’images[/*]\n[*]Réglages de base : exposition, contraste, hautes lumières, ombres, blancs, noirs[/*]\n[*]Balance des blancs (température, teinte)[/*]\n[*]Correction optique et suppression des aberrations[/*]\n[*]Carte : géolocalisation des images[/*]\n[*]Diaporama : création de présentations[/*]\n[*]Impression : mise en page et tirage photo[/*]\n[/list]`,
		site_adobe_plus_informations: `https://helpx.adobe.com/fr/lightroom-classic/help/whats-new.html`,
		liens: [
			{ titre: "1fichier", url: "https://1fichier.com/" },
			{ titre: "Ultim-Zone", url: "https://ultim-zone.in/topic-417750-adobe-lightroom-classic-150-win-x64-multi-precracke-page-1.html#p2597890" },
			{ titre: "1fichier (fichiers)", url: "https://1fichier.com/console/index.pl?mf" },
		]
	},

	photoshop: {
		nom_logiciel: `Adobe Photoshop 2026`,
		image: `https://iili.io/KbG169j.jpg`,
		description: `Boostez votre imagination grâce à la version la plus puissante de Photoshop. L’application, désormais disponible sur ordinateur, appareils mobiles et le web, est incluse dans votre formule.`,
		site_officiel: `https://www.adobe.com/fr/products/photoshop.html`,
		infos: `[list=*]\n[*]Application puissante pour éditer et améliorer les images numériques[/*]\n[*]Application simple et puissante avec une variété de personnalisations[/*]\n[*]Un ensemble d’outils simples avec le meilleur ensemble de fonctionnalités d’édition[/*]\n[*]Travailler sur des calques et personnaliser divers détails des images numériques[/*]\n[*]Fonctions de sélection et personnalisations précises pour différents aspects de l’image[/*]\n[*]Un ensemble d’outils performants dans un environnement convivial[/*]\n[*]Prend en charge les documents avec une résolution allant jusqu’à 30 000 pixels[/*]\n[*]Édition des images RAW et amélioration des photos[/*]\n[*]La sélection de sujets calculée dans le cloud, donc de manière plus détaillée[/*]\n[*]La possibilité de supprimer un objet dans une photo en un seul clic droit[/*]\n[*]L’ajout d’une fenêtre « matières » qui permet d’ajouter de la texture en 3D[/*]\n[*]De nouvelles fonctionnalités de partage pour le travail collaboratif en ligne[/*]\n[*]Quelques nouveautés spécifiques à la version Beta de Photoshop dont l’amélioration de l’outil dégradé et du flou gaussien[/*]\n[/list]`,
		site_adobe_plus_informations: `https://helpx.adobe.com/fr/photoshop/using/whats-new.html`,
		liens: [
			{ titre: "1fichier", url: "https://1fichier.com/" },
			{ titre: "Ultim-Zone", url: "https://ultim-zone.in/topic-371541-adobe-photoshop-2024-2591626-win-x64-multi-precracke-page-1.html#p2156914" },
			{ titre: "1fichier (fichiers)", url: "https://1fichier.com/console/index.pl?mf" },
		]
	},

	premiere_pro: {
		nom_logiciel: `Adobe Premiere Pro 2025`,
		image: `https://iili.io/KL12zOu.jpg`,
		description: `Montez et raccordez des vidéos. Appliquez des effets, mixez des sons et animez des titres. Boostez votre workflow avec l’IA. Créez des masques complexes en un clin d’œil grâce à l’outil Masque d’objet de Premiere (beta), et montez des vidéos où que vous soyez avec la nouvelle application pour iPhone.`,
		site_officiel: `https://www.adobe.com/fr/products/premiere.html`,
		infos: `[list=*]\n[*]Interface plus intuitive et personnalisable et meilleure organisation des espaces de travail[/*]\n[*]Amélioration de la Performance, accélération matérielle pour des rendus plus rapides et ptimisation pour les processeurs multi-cœurs et les GPU modernes[/*]\n[*]Montage vidéo avancé, Montage multicam plus fluide, outils de découpe et d’édition améliorés[/*]\n[*]Support des formats vidéo haute résolution (4K, 8K)[/*]\n[*]Effets Visuels et Transitions, prévisualisation en temps réel des effets et justement facile des paramètres d’effet[/*]\n[*]Audio Avancé, panneau audio redessiné pour un contrôle plus précis, outils de nettoyage audio automatiques (réduction de bruit, suppression d’écho) et intégration améliorée avec Adobe Audition[/*]\n[*]Couleur et Étanchéité, outils de correction et de gradation des couleurs améliorés[/*]\n[*]Préréglages de couleur personnalisables et compatibilité avec les LUTs (Look-Up Tables) de tiers[/*]\n[*]Outils de collaboration en temps réel pour les équipes, partage et synchronisation faciles avec Adobe Creative Cloud et historique des versions et commentaires intégrés[/*]\n[*]Intégration fluide avec d’autres logiciels Adobe (Photoshop, After Effects), compatibilité étendue avec les plugins de tiers et support des derniers formats de caméra et codecs[/*]\n[*]Utilisation de l’IA pour le montage automatique (Auto Reframe, scene edit detection), outils de transcription et de sous-titrage automatiques et suggestions d’édition basées sur l’IA[/*]\n[*]Options d’exportation optimisées pour les réseaux sociaux, préréglages d’exportation personnalisables et exportation en lot pour plusieurs formats simultanés[/*]\n[*]Mise en Œuvre de Réalité Virtuelle et Augmentée, outils améliorés pour la création de contenus VR et AR, prévisualisation immersive via casques VR et compatibilité avec les formats de réalité augmentée[/*]\n[/list]`,
		site_adobe_plus_informations: `https://helpx.adobe.com/fr/premiere-pro/using/whats-new.html`,
		liens: [
			{ titre: "1fichier", url: "https://1fichier.com/" },
			{ titre: "Ultim-Zone", url: "https://ultim-zone.in/topic-393833-adobe-premiere-pro-2024-2441002-win-x64-multi-precracke-page-1.html#p2355999" },
			{ titre: "1fichier (fichiers)", url: "https://1fichier.com/console/index.pl?mf" },
		]
	},

	substance_3d_designer: {
		nom_logiciel: `Adobe Substance 3D Designer`,
		image: `https://iili.io/KL12IDb.jpg`,
		description: `Créez des matériaux et des motifs, des filtres d’image et des éclairages d’environnement de qualité, et déclinez-les à l’infini.`,
		site_officiel: `https://www.adobe.com/fr/products/substance3d/apps/designer.html`,
		infos: `[list=*]\n[*]Création de matériaux et textures procédurales basées sur des graphes nodaux[/*]\n[*]Génération de textures PBR (Base Color, Normal, Roughness, etc.)[/*]\n[*]Paramétrage dynamique et création de variantes à partir de valeurs ajustables[/*]\n[*]Export de matériaux au format SBSAR pour d’autres logiciels (Painter, Unreal, Blender…)[/*]\n[*]Rendu temps réel via le moteur Iray intégré[/*]\n[*]Baking de cartes à partir de modèles 3D haute définition[/*]\n[*]Compatibilité avec la plupart des moteurs 3D et pipelines de production[/*]\n[/list]`,
		site_adobe_plus_informations: `https://helpx.adobe.com/fr/substance-3d-designer/release-notes/all-changes.html`,
		liens: [
			{ titre: "1fichier", url: "https://1fichier.com/" },
			{ titre: "Ultim-Zone", url: "https://ultim-zone.in/topic-417481-adobe-substance-3d-designer-15039784-win-x64-multi-precracke-page-1.html#p2594296" },
			{ titre: "1fichier (fichiers)", url: "https://1fichier.com/console/index.pl?mf" },
		]
	},

	substance_3d_stager: {
		nom_logiciel: `Adobe Substance 3D Stager`,
		image: `https://iili.io/KL12uxj.jpg`,
		description: `Assemblez des scènes 3D dans notre application de rendu de référence. Agencez des assets, des éclairages et des caméras pour obtenir un plan parfait.`,
		site_officiel: `https://www.adobe.com/fr/products/substance3d/apps/stager.html`,
		infos: `[list=*]\n[*]Placement et manipulation d’objets 3D[/*]\n[*]Application de matériaux et textures réalistes[/*]\n[*]Gestion de l’éclairage et des caméras[/*]\n[*]Rendu photoréaliste avec ray tracing[/*]\n[*]Intégration avec les autres outils Substance et Adobe Creative Cloud.[/*]\n[/list]`,
		site_adobe_plus_informations: `https://helpx.adobe.com/fr/substance-3d-stager/release-notes/all-changes.html`,
		liens: [
			{ titre: "1fichier", url: "https://1fichier.com/" },
			{ titre: "Ultim-Zone", url: "https://ultim-zone.in/topic-416782-adobe-substance-3d-stager-3156321-win-x64-multi-precracke-page-1.html#p2594294" },
			{ titre: "1fichier (fichiers)", url: "https://1fichier.com/console/index.pl?mf" },
		]
	},
}

const selectModele			= document.querySelector('#modele');

const nomLogicielView		= document.querySelector('#nom_logicielView');
const imageView				= document.querySelector('#imageView');
const descView				= document.querySelector('#descriptionView');
const siteOffView			= document.querySelector('#site_officielView');
const infosView				= document.querySelector('#infosView');
const siteAdobeView			= document.querySelector('#site_adobe_plus_informationsView');
const commentView			= document.querySelector('#comment_installerView');

const liensView				= document.querySelector('#liens');

const nomLogicielHidden		= document.querySelector('#nom_logiciel');
const imageHidden			= document.querySelector('#image');
const descHidden			= document.querySelector('#description');
const siteOffHidden			= document.querySelector('#site_officiel');
const infosHidden			= document.querySelector('#infos');
const siteAdobeHidden		= document.querySelector('#site_adobe_plus_informations');
const commentHidden			= document.querySelector('#comment_installer');

function resetForm() {
	[nomLogicielView, descView, siteOffView, infosView, siteAdobeView, commentView, liensView]
		.forEach(el => el.textContent = '');
	imageView.innerHTML = '';

	[nomLogicielHidden, imageHidden, descHidden, siteOffHidden, infosHidden, siteAdobeHidden, commentHidden]
		.forEach(el => el.value = '');
}

function renderImage(url, nom = '') {
	imageView.innerHTML = '';

	if (!url) {
		return;
	}

	const img = document.createElement('img');

	img.src = url;
	img.alt = nom ? `Visuel ${nom}` : 'Aperçu image';
	img.className = 'img-fluid rounded';
	img.loading = 'lazy';
	img.referrerPolicy = 'no-referrer';

	img.onerror = () => {
		imageView.textContent = url;
	};

	imageView.appendChild(img);

	const linkWrap = document.createElement('div');
	linkWrap.className = 'text-center my-4 small';
	linkWrap.innerHTML = `<a href="${url}" <?= $onclick; ?> class="btn btn-outline-primary">Ouvrir l’image</a>`;
	imageView.appendChild(linkWrap);
}


function setData(data) {
	nomLogicielView.textContent = data.nom_logiciel || '';
	descView.textContent		= data.description || '';
	siteOffView.textContent		= data.site_officiel || '';
	infosView.textContent		= data.infos || '';
	siteAdobeView.textContent	= data.site_adobe_plus_informations || '';
	commentView.textContent		= commentInstallerCommun || '';

	nomLogicielHidden.value		= data.nom_logiciel || '';
	imageHidden.value			= data.image || '';
	descHidden.value			= data.description || '';
	siteOffHidden.value			= data.site_officiel || '';
	infosHidden.value			= data.infos || '';
	siteAdobeHidden.value		= data.site_adobe_plus_informations || '';
	commentHidden.value			= commentInstallerCommun || '';

	renderImage(data.image, data.nom_logiciel);

	if (data.liens && data.liens.length > 0) {
		liensView.innerHTML = data.liens.map(lien =>
			`<a href="${lien.url}" class="badge text-bg-primary me-2 p-2" <?= $onclick; ?>>${lien.titre}</a>`
		).join('');
	} else {
		liensView.textContent = '';
	}
}

selectModele.addEventListener('change', () => {
	const key = selectModele.value;
	const data = modeles[key];
	if (!data) {
		resetForm();
		return;
	}
	setData(data);
});

document.querySelector('#logiciels').addEventListener('submit', () => {
	nomLogicielHidden.value = nomLogicielView.textContent;
	imageHidden.value = imageHidden.value || '';
	descHidden.value = descView.textContent;
	infosHidden.value = infosView.textContent;
	siteOffHidden.value = siteOffView.textContent;
	siteAdobeHidden.value = siteAdobeView.textContent;
	commentHidden.value = commentInstallerCommun;
});
</script>
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'projets/_footer.php';