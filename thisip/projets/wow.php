<?php
require_once '../../config/config.php';

/*

Taléa > Grotte de Zaralek

Klep : vérifier de temps en temps les quêtes de Zereth Mortis pour : Âme de gronil

- Tourmenteur de Tourment
- Toujours penser à ouvrir « Cache du tourmenteur » pour rendre la quête « Fantasmagorie »

Klep
	Taléa
	Tidar
	Héming
	Shaina
Kouettes
	Isthellum
Shosix
Drogmote
	Moly
Tuttle
Powlol
	Awplol
	Pateacrepe

-----

Fragile
https://www.wowhead.com/fr/achievement=10602/fragile#comments:id=2558732

21 janvier au 3 février 2026
Les Collines mauves d'Érédath
https://worldofwarcraft.judgehype.com/article/rotation-et-dates-des-objets-d-archeologie-de-legion/#12.-les-collines-mauves-d'%C3%A9r%C3%A9dath

1 au 14 avril 2026
Joyaux de la couronne de Suramar
https://worldofwarcraft.judgehype.com/article/rotation-et-dates-des-objets-d-archeologie-de-legion/#4.-joyaux-de-la-couronne-de-suramar

*/

$getLegion = get('https://www.wowhead.com/fr/world-quests/legion/eu');

$quetesJournalieres = [
	[true,			'_TWT_',			'Ajouter les jeux sur <a href="https://www.twitch.tv/" class="text-decoration-underline link-offset-1 fw-bold">Twitch</a> - <a href="?fichier&twitch" class="text-danger"><i class="fa-solid fa-trash-can"></i> Supprimer le fichier</a>'],

	// [true, null, '<hr id="hgtrb">'],

	// [true,			'_QST_Klep',		'25-05-2025 14h00|Ramasser les <span>Graînes</span> dans <span>Jardin d’hiver de la Reine</span> en <a href="https://www.wowhead.com/fr/zone=11510">Sylvarden</a>'],
	// [true,			'_BOSS_Klep',		'Tuer le <span>Conseil</span> pour <a href="https://www.wowhead.com/fr/item=183741">Crin-de-Ciel transcendé</a> au <a href="https://www.wowhead.com/fr/zone=10534">Bastion</a>'],

	// [true, null, '<hr id="atghy">'],

	[true,			'_QST_Taléa',		'Faire la quête de <span>Joaillerie</span> à <a href="https://www.wowhead.com/fr/zone=4395">Dalaran</a>'],
	[true,			'_QST_Taléa',		'Faire la quête de <span>Joaillerie</span> à <a href="https://www.wowhead.com/fr/zone=1637">Orgrimmar</a>'],
	[true,			'_QST_Taléa',		'Vérifier les quêtes journalières du <span>Sanctum de la congrégation</span> pour <a href="https://www.wowhead.com/fr/item=190381">Technique : Glyphe du vulpin spectral</a>'],
	[true,			'_QST_Taléa',		'Faire les quêtes des <a href="https://www.wowhead.com/fr/faction=1105"><span>Oracles</span></a>'],

	[true, null, '<hr id="zfrvg">'],

	// [true,			'_QST_Tidar',		'xxx'],

	// [true, null, '<hr id="plmpl">'],

	[true,			'_BOSS_Héming',		'Tuer <a href="https://www.wowhead.com/fr/npc=210045">Moragh la Paresseuse</a> pour <a href="https://www.wowhead.com/fr/item=210729">Marque du hoursute verdoyant</a> au <a href="https://www.wowhead.com/fr/zone=14529">Rêve d’émeraude</a>'],

	// [true, null, '<hr id="fgftg">'],

	// [true,			'_QST_Shaina',		'xxx'],

	//[true, null, '<hr id="fdcfd">'],

	// [true,			'_QST_Kouettes',	'Faires les 3 quêtes journalières des <span>Garde du corps</span> à <a href="https://www.wowhead.com/zone=10052">Nazjatar</a>'],
];

$qetesHebdomadaires = [
	['hebdo',	'_QST_Klep',					'Faire la <a href="https://www.wowhead.com/fr/item=235053">Liste C.H.E.T.T.</a> à <a href="https://www.wowhead.com/fr/zone=15347">Terremine</a>'],
	['hebdo',	'_DIV_Klep',					'Récupérer l’<a href="https://www.wowhead.com/fr/currency=1813">Anima</a> sur <a href="https://www.wowhead.com/fr/npc=182466">Antros</a> à <a href="https://www.wowhead.com/fr/zone=13536">Zereth Mortis</a>'],
	['hebdo',	'_DIV_Klep',					'Récupérer l’<a href="https://www.wowhead.com/fr/currency=1813">Anima</a> au <span>Locus</span> à <a href="https://www.wowhead.com/fr/zone=13536">Zereth Mortis</a>'],
	['hebdo',	'_DIV_Klep',					'Récupérer l’<a href="https://www.wowhead.com/fr/currency=1813">Anima</a> sur <a href="https://www.wowhead.com/fr/npc=178958">Mor’geth</a> dans l’<a href="https://www.wowhead.com/fr/zone=11400">Antre</a>'],
	['hebdo',	'_DIV_Klep',					'Récupérer l’<a href="https://www.wowhead.com/fr/currency=1813">Anima</a> sur un <span>Boss rare</span> en <span>Ombreterre</span>'],
	['hebdo',	'_DIV_Klep',					'Récupérer les <a href="https://www.wowhead.com/fr/currency=1816">Fragments de stèle du vice</a> à <a href="https://www.wowhead.com/fr/zone=10413">Revendreth</a> en <span>/way #1525 73 52</span>'],
	['hebdo',	'_DIV_Klep',					'Faire <span>Retour à Karazhan</span> pour la <a href="https://www.wowhead.com/fr/item=142246">Montre de gousset cassée</a>'],
	// ['hebo',	'_BOSS_Klep',					'Tuer <a href="https://www.wowhead.com/fr/npc=83746">Rukhmar</a> pour <a href="https://www.wowhead.com/fr/item=116771">Faucon des flèches solaire</a> aux <a href="https://www.wowhead.com/fr/zone=6722">Flèches d’Arak</a>'],
	['hebdo',	'_PCH_Klep',					'Faire le <a href="https://worldofwarcraft.judgehype.com/article/guide-du-concours-de-peche-de-strangleronce/">Concours de pêche de Strangleronce</a> à <span>14h</span> à la <span style="text-decoration: none; color: rgba(255,245,105, 1); background-image: linear-gradient(45deg, rgba(255,245,105, 1) 0%, rgba(255,184,54, 1) 20%, rgba(255,138,13, 1) 40%, rgba(255,21,0, 1) 60%, rgba(255,0,0, 1) 80%, rgba(255,0,0, 1) 100%); background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Vallée de Strangleronce</span>'],

	['hebdo', null, '<hr id="ikjpl">'],

	['hebdo',	'_QST_Taléa',					'Faire la <a href="https://www.wowhead.com/fr/item=235053">Liste C.H.E.T.T.</a> à <a href="https://www.wowhead.com/fr/zone=15347">Terremine</a>'],
	['hebdo',	'_BOSS_Taléa',					'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],

	['hebdo', null, '<hr id="zerty">'],

	['hebdo',	'_QST_Tidar',					'Faire la quête d’<span>Herboristerie</span> à <a href="https://www.wowhead.com/fr/zones/khaz-algar"><span>Khaz Algar</span></a>'],
	['hebdo',	'_TRT_Tidar',					'Utiliser le <a href="https://www.wowhead.com/fr/item=222552">Traité algari d’herboristerie</a>'],
	['hebdo',	'_QST_Tidar',					'Faire la quête d’<span>Herboristerie</span> aux <a href="https://www.wowhead.com/fr/zone=13642">Îles aux Dragons</a>'],
	['hebdo',	'_TRT_Tidar',					'Utiliser le <a href="https://www.wowhead.com/fr/item=194704">Traité d’herboristerie draconique</a>'],
	['hebdo',	'_BOSS_Tidar',					'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],

	['hebdo', null, '<hr id="pmfrt">'],

	['hebdo',	'_QST_Héming',					'Faire la quête de <span>Dépeçage</span> à <a href="https://www.wowhead.com/fr/zones/khaz-algar"><span>Khaz Algar</span></a>'],
	['hebdo',	'_TRT_Héming',					'Utiliser le <a href="https://www.wowhead.com/fr/item=222649">Traité algari de dépeçage</a>'],
	['hebdo',	'_QST_Héming',					'Faire les quêtes de <span>Dépeçage</span> et de <span>Travail du cuir</span> aux <a href="https://www.wowhead.com/fr/zone=13642">Îles aux Dragons</a>'],
	['hebdo',	'_TRT_Héming',					'Utiliser le <a href="https://www.wowhead.com/fr/item=201023">Traité de dépeçage draconique</a> et le <a href="https://www.wowhead.com/fr/item=194700">Traité de travail du cuir draconique</a>'],
	['hebdo',	'_RAID_Héming',					'Faire <a href="https://www.wowhead.com/fr/zone=14643">Amirdrassil, l’Espoir du Rêve</a> pour <a href="https://www.wowhead.com/fr/item=210536">Proto-drake renouvelé : incarnation du Flamboyant</a>'],
	['hebdo',	'_QST_Héming',					'Tuer <a href="https://www.wowhead.com/fr/npc=150927">Xue</a> en <span>Farouche</span> (1 x <a href="https://www.wowhead.com/fr/item=169333">Pierre volcanique étrange</a> et 1 x <a href="https://www.wowhead.com/fr/item=169332">Eau minéralisée étrange</a>) pour <a href="https://www.wowhead.com/fr/item=170127">Hallebarde pyroclastique</a>'],
	['hebdo',	'_BOSS_Héming',					'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],

	['hebdo', null, '<hr id="gfbvn">'],

	['hebdo',	'_BOSS_Shaina',					'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],
	['hebdo',	'_QST_Shaina',					'Faire la quête d’<span>Ingénierie</span> aux <a href="https://www.wowhead.com/fr/zone=13642">Îles aux Dragons</a>'],
	['hebdo',	'_TRT_Shaina',					'Utiliser le <a href="https://www.wowhead.com/fr/item=198510">Traité d’ingénierie draconique</a>'],

	['hebdo', null, '<hr id="jhgbn">'],

	['hebdo',	'_QST_Kouettes',				'Faire la quête de <span>Minage</span> à <a href="https://www.wowhead.com/fr/zones/khaz-algar"><span>Khaz Algar</span></a>'],
	['hebdo',	'_TRT_Kouettes',				'Utiliser le <a href="https://www.wowhead.com/fr/item=222553">Traité algari de minage</a>'],
	['hebdo',	'_BOSS_Kouettes',				'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> (supprimer à 5)'],
	['hebdo',	'_QST_Kouettes',				'Faire les quêtes de <span>Forge</span> et <span>Minage</span> aux <a href="https://www.wowhead.com/fr/zone=13642">Îles aux Dragons</a>'],
	['hebdo',	'_TRT_Kouettes',				'Utiliser le <a href="https://www.wowhead.com/fr/item=198454">Traité de forge draconique</a> et le <a href="https://www.wowhead.com/fr/item=194708">Traité de minage draconique</a>'],

	['hebdo', null, '<hr id="pokjd">'],

	['hebdo',	'_BOSS_Isthellum',				'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],

	['hebdo', null, '<hr id="kidfg">'],

	['hebdo',	'_BOSS_Shosix',					'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],

	['hebdo', null, '<hr id="fdvcd">'],

	['hebdo',	'_BOSS_Drogmote',				'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],

	['hebdo', null, '<hr id="ujedf">'],

	['hebdo',	'_BOSS_Moly',					'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],

	['hebdo', null, '<hr id="juyhg">'],

	['hebdo',	'_QST_Tuttle',					'Tuer <a href="https://www.wowhead.com/fr/npc=150926">Amalgame ardent</a> (1 x <a href="https://www.wowhead.com/fr/item=169333">Pierre volcanique étrange</a>) pour <a href="https://www.wowhead.com/fr/item=170126">Arc long igné</a>'],
	['hebdo',	'_BOSS_Tuttle',					'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],

	['hebdo', null, '<hr id="yhnhj">'],

	['hebdo',	'_BOSS_Powlol',					'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],

	['hebdo', null, '<hr id="asdfr">'],

	['hebdo',	'_BOSS_Awplol',					'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],

	['hebdo', null, '<hr id="pmerf">'],

	['hebdo',	'_QST_Pateacrepe',				'Tuer <a href="https://www.wowhead.com/fr/npc=150927">Xue</a> en <span>Maître brasseur</span> (1 x <a href="https://www.wowhead.com/fr/item=169333">Pierre volcanique étrange</a> et 1 x <a href="https://www.wowhead.com/fr/item=169332">Eau minéralisée étrange</a>) pour <a href="https://www.wowhead.com/fr/item=170127">Hallebarde pyroclastique</a>'],
	['hebdo',	'_BOSS_Pateacrepe',				'Récupérer un <a href="https://www.wowhead.com/fr/quest=52834">Sceau du destin érodé</a> et tuer le <span>World Boss</span> à <a href="https://www.wowhead.com/fr/zone=10052">Nazjatar</a>'],

];
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title>Quêtes de World of Warcraft</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<link rel="icon" href="/assets/img/favicon-wow.svg">
	<link rel="stylesheet" href="/assets/css/vendors.css?<?= filemtime('../assets/css/vendors.css'); ?>">
	<link rel="stylesheet" href="/assets/css/brain-video.css?<?= filemtime('../assets/css/brain-video.css'); ?>">

	<style>
	h1 a {
		border-bottom: 1px dashed;
		position: relative;
		text-underline-offset: .375em !important;
	}

	h1 a:hover {
		opacity: .75;
	}

	.chasseur				{ color: rgba(170,211,114, 1) !important; }
	.chaman					{ color: rgba(0,112,221, 1) !important; }
	.chasseur-de-demons		{ color: rgba(163,48,201, 1) !important; }
	.chevalier-de-la-mort	{ color: rgba(196,30,58, 1) !important; }
	.demoniste				{ color: rgba(135,136,238, 1) !important; }
	.druide					{ color: rgba(255,124,10, 1) !important; }
	.evocateur				{ color: rgba(51,147,127, 1) !important; }
	.guerrier				{ color: rgba(198,155,109, 1) !important; }
	.mage					{ color: rgba(63,199,235, 1) !important; }
	.moine					{ color: rgba(0,255,152, 1) !important; }
	.paladin				{ color: rgba(244,140,186, 1) !important; }
	.pretre					{ color: rgba(255,255,255, 1) !important; }
	.voleur					{ color: rgba(255,244,104, 1) !important; }

	.normal					{ color: rgba(255,255,255, 1) !important; font-weight: bold !important; }
	.rare					{ color: rgba(0,112,221, 1) !important; font-weight: bold !important; }
	.epique					{ color: rgba(163,53,238, 1) !important; font-weight: bold !important; }

	.orange					{ color: rgba(158,63,18, 1) !important; font-weight: bold !important; }
	.rose					{ color: rgba(255,20,147, 1) !important; font-weight: bold !important; }
	.quete					{ color: rgba(255,209,0, 1) !important; font-weight: bold !important; }
	.fa-twitch				{ color: rgba(145,71,255, 1) !important; font-weight: bold !important; }

	.quetes:hover			{ opacity: .75; }
	.fa-check:hover			{ color: rgba(25,135,84, 1) !important; }
	.curseur				{ cursor: pointer; }
	a						{ text-decoration: none; }
	input, label			{ cursor: pointer; }

	#journalieres > p > span, #journalieres > p> a > span,
	#hebdomadaires > p > span, #hebdomadaires > p > a > span {
		font-weight: bold;
		text-decoration: underline;
		text-underline-offset: .15em;
	}

	#journalieres hr,
	#hebdomadaires hr {
		border: .175rem solid rgba(214,104,83, .5) !important;
		border-radius: .5rem !important;
		cursor: pointer !important;
		margin: 1.5rem auto !important;
	}
	</style>

	<script src="/assets/js/vendors.js?<?= filemtime('../assets/js/vendors.js'); ?>"></script>
	<!-- https://www.wowhead.com/tooltips -->
	<script>const whTooltips = {colorLinks: true, iconizeLinks: true, renameLinks: true};</script>
	<script src="https://wow.zamimg.com/js/tooltips.js"></script>
	<script>setTimeout(function() { location.reload(true) }, 1800000);</script>
</head>

<body>
<div class="container">
	<?php
	$remplacementsArray = [
		'Klep'			=> '<span class="text-decoration-underline link-offset-1 fw-bold voleur">Klep</span>',
		'Taléa'			=> '<span class="text-decoration-underline link-offset-1 fw-bold paladin">Taléa</span>',
		'Tidar'			=> '<span class="text-decoration-underline link-offset-1 fw-bold demoniste">Tidar</span>',
		'Héming'		=> '<span class="text-decoration-underline link-offset-1 fw-bold druide">Héming</span>',
		'Shaina'		=> '<span class="text-decoration-underline link-offset-1 fw-bold mage">Shaina</span>',
		'Kouettes'		=> '<span class="text-decoration-underline link-offset-1 fw-bold voleur">Kouettes</span>',
		'Isthellum'		=> '<span class="text-decoration-underline link-offset-1 fw-bold chasseur-de-demons">Isthellum</span>',
		'Shosix'		=> '<span class="text-decoration-underline link-offset-1 fw-bold guerrier">Shosix</span>',
		'Drogmote'		=> '<span class="text-decoration-underline link-offset-1 fw-bold evocateur">Drogmote</span>',
		'Moly'			=> '<span class="text-decoration-underline link-offset-1 fw-bold pretre">Moly</span>',
		'Tuttle'		=> '<span class="text-decoration-underline link-offset-1 fw-bold chasseur">Tuttle</span>',
		'Powlol'		=> '<span class="text-decoration-underline link-offset-1 fw-bold chaman">Powlol</span>',
		'Awplol'		=> '<span class="text-decoration-underline link-offset-1 fw-bold chevalier-de-la-mort">Awplol</span>',
		'Pateacrepe'	=> '<span class="text-decoration-underline link-offset-1 fw-bold moine">Pateacrepe</span>',

		// Divers
		'<span>'		=> '<span>',
		'<code>'		=> '<br><code class="fs-6">',

		// Icônes
		'_BOSS_'		=> '<i class="fa-solid fa-user me-2"></i>',					// Boss
		'_DIV_'			=> '<i class="fa-solid fa-user me-2"></i>',					// Divers
		'_JcJ_'			=> '<i class="fa-solid fa-khanda me-2"></i>',				// JcJ
		'_QST_'			=> '<i class="fa-solid fa-question me-2"></i>',				// Quêtes
		'_RAID_'		=> '<i class="fa-solid fa-sun me-2"></i>',					// Raid
		'_TWT_'			=> '<i class="fa-brands fa-twitch me-2"></i>',				// Twitch
		'_PCH_'			=> '<i class="fa-solid fa-fish me-2"></i>',					// Pêche
		'_TRT_'			=> '<i class="fa-solid fa-book me-2"></i>',					// Traités
	];

	echo '<div id="journalieres">
		<h1 class="my-4"><a href="wow">World of Warcraft</a></h1>';

		echo !empty($msg) ? $msg : null;

		if(isset($_GET['fichier'])) {
			(isset($_GET['twitch']) AND is_file('/home/blok/www/thisip/projets/twitch')) ? unlink('/home/blok/www/thisip/projets/twitch') : null;
		}

		$i = 0;
		foreach($quetesJournalieres as $q) {
			$titre = !empty($q[1]) ? strtr($q[1], $remplacementsArray) : $q[1];
			$titre = !empty($titre) ? preg_replace('/<a href="https:\/\/www.wowhead.com\/fr\/(achievement|faction|item|npc|spell|zone)=(\d+)">/is',		"<a href=\"https://www.wowhead.com/fr/\\1=\\2\">", $titre) : $titre;

			$desc = !empty($q[2]) ? strtr($q[2], $remplacementsArray) : $q[2];
			$desc = !empty($desc) ? preg_replace('/<a href="https:\/\/www.wowhead.com\/fr\/(achievement|faction|item|npc|spell|zone)=(\d+)">/is',		"<a href=\"https://www.wowhead.com/fr/\\1=\\2\">", $desc) : $desc;

			$id = substr(preg_replace('/[^a-zA-Z]/is', '', crypt($titre.'-'.$desc, '$6$rounds=5000$AmericanDad$')), 10, 20);
			$affichage = !preg_match('/<hr/is', $desc) ? '<p class="curseur quetes quetesJournas '.$i.'-quetes" id="'.$id.'"><i class="fa-solid fa-check fa-2xl me-2"></i>'.($titre ? '<strong>'.$titre.' :</strong> ' : null).$desc.'</p>' : '<hr class="curseur quetes quetesJournas '.$i.'-quetes" id="'.$id.'">';

			if($q[0]) {
				if(is_file('/home/blok/www/thisip/projets/twitch') AND preg_match('/Twitch/is', $q[2]))		echo $affichage;
				elseif($q[0] AND preg_match('/Graînes/is', $q[2])) {
					$dateExplode = explode('|', $q[2]);

					$dateTime = DateTime::createFromFormat('d-m-Y H\hi', $dateExplode[0]);
					$dateTime = DateTime::createFromFormat('d-m-Y H\hi', $dateExplode[0]);
					$maintenant = new DateTime();
					$intervalle = $maintenant->diff($dateTime);
					$heuresEcoulees = ($intervalle->days * 24) + $intervalle->h;

					$resultat = preg_replace('/\d{2}-\d{2}-\d{4}\s\d{2}h\d{2}\|/', '', $affichage);

					echo ($maintenant > $dateTime AND $heuresEcoulees >= 72) ? $resultat : null;
				}
				elseif($q[0] AND !preg_match('/Graînes|Twitch/is', $q[2]))									echo $affichage;
				else																						null;
			}

			else
				null;

			$i++;
		}

	echo '</div>

	<div id="messageJournasTerminees" style="display: none;">'.alerte('info', 'Toutes les quêtes journalières ont été effectuées').'</div>
	<div id="messageHebdosTerminees" style="display: none;">'.alerte('info', 'Toutes les quêtes ont été effectuées').'</div>

	<div id="hebdomadaires">
		<h1 class="mb-4 mt-5" id="h1-hebdo"><a href="#h1-hebdo">Quêtes hebdomadaires</a></h1>';

		$i = (count($quetesJournalieres) + 1);
		foreach($qetesHebdomadaires as $qh) {
			$titre = !empty($qh[1]) ? strtr($qh[1], $remplacementsArray) : $qh[1];
			$titre = !empty($titre) ? preg_replace('/<a href="https:\/\/www\.wowhead\.com\/fr\/(achievement|faction|item|npc|spell|zone)=(\d+)">/is',	"<a href=\"https://www.wowhead.com/fr/\\1=\\2\">", $titre) : $titre;

			$desc = !empty($qh[2]) ? strtr($qh[2], $remplacementsArray) : $qh[2];
			$desc = !empty($desc) ? preg_replace('/<a href="https:\/\/www\.wowhead\.com\/fr\/(achievement|faction|item|npc|spell|zone)=(\d+)">/is',		"<a href=\"https://www.wowhead.com/fr/\\1=\\2\">", $desc) : $desc;

			$id = substr(preg_replace('/[^a-zA-Z]/is', '', crypt($titre.'-'.$desc, '$6$rounds=5000$AmericanDad$')), 10, 20);
			$affichageHebdo = !preg_match('/<hr/is', $desc) ? '<p class="curseur quetes quetesHebdos '.$i.'-quetes " id="hebdo-'.$id.'"><i class="fa-solid fa-check fa-2xl me-2"></i>'.($titre ? '<strong>'.$titre.' :</strong> ' : null).$desc.'</p>' : '<hr class="curseur quetes quetesHebdos '.$i.'-quetes" id="hebdo-'.$id.'">';

			if($qh[0]) {
				if(date('l') === 'Sunday' AND date('G') > 14 AND date('G') < 16 AND preg_match('/Strangleronce/is', $qh[2]))		echo $affichageHebdo;
				elseif(date('l') === 'Saturday' AND preg_match('/Mereldar/is', $qh[2]))							echo $affichageHebdo;
				elseif($qh[0] AND !preg_match('/pêche/is', $qh[2]))												echo $affichageHebdo;
				else																							null;
			}

			else
				null;

			$i++;
		}
		?>
	</div>

	<div class="text-center my-5">
		<div id="aucuneQuete"></div>

		<div id="messageJourna" style="display: none; color: green;"><?= alerte('success', 'Les quêtes journalières ont été réinitialisées !'); ?></div>
		<div id="messageHebdo" style="display: none; color: green;"><?= alerte('success', 'Les quêtes hebdomadaires ont été réinitialisées !'); ?></div>

		<div>
			<form action="wow" method="post" name="reset" id="formQuetes">
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="reset" id="btnResetJournas" value="qJournas">
					<label class="form-check-label" for="btnResetJournas">Réinitialiser les quêtes <span class="fw-bold">Journalières</span></label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="reset" id="btnResetHebdos" value="qHebdos">
					<label class="form-check-label" for="btnResetHebdos">Réinitialiser les quêtes <span class="fw-bold">Hebdobadaires</span></label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="reset" id="btnResetToutes" value="qToutes">
					<label class="form-check-label" for="btnResetToutes">Réinitialiser toutes les quêtes</label>
				</div>
			</form>
		</div>

		<div id="messageToutes" style="display: none; color: green;"><?= alerte('success', 'Le Local Storage a été vidé'); ?></div>
		<div id="messageAnnuler" style="display: none; color: red;"><?= alerte('danger', 'Formulaire annuler !'); ?></div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	function checkAndRemoveKey(key) {
		const dateAjoutQuete = Number(localStorage.getItem(key));
		if (dateAjoutQuete) {
			const patternHebdo = /hebdo/i;
			const resultHebdo = patternHebdo.exec(key);

			if (resultHebdo) {
				const dateActuelle = new Date();
				const dateSupprimerMercredi = new Date(dateActuelle);

				const dateMercredi = (3 - dateActuelle.getDay() + 7) % 7;
				const dateVerifMercredi = (dateMercredi === 0) ? (dateMercredi + 1) : dateMercredi;

				dateSupprimerMercredi.setDate(dateActuelle.getDate() + dateVerifMercredi);
				dateSupprimerMercredi.setHours(7, 0, 0, 0);

				const tempsSupprimerHebdo = Date.parse(dateSupprimerMercredi);

				if (Number(new Date().getTime()) > tempsSupprimerHebdo) {
					localStorage.removeItem(key);
				}
			} else {
				const dateSupprimerUnJour = new Date().getTime() - ((60 * 60 * 24) * 1000);

				if (dateAjoutQuete < dateSupprimerUnJour) {
					localStorage.removeItem(key);
				}
			}
		}
	}

	function handleDivClick(event) {
		const divId = this.id;
		localStorage.setItem(divId, new Date().getTime());
		checkAndRemoveKey(divId);
		this.style.display = 'none';
	}

	document.querySelectorAll('.quetes').forEach(function(div) {
		const divId = div.id;

		checkAndRemoveKey(divId);

		if (localStorage.getItem(divId)) {
			div.style.display = 'none';
		}
	});

	document.querySelectorAll('#formQuetes input[type="radio"]').forEach(function(radio) {
		radio.addEventListener('change', function(event) {
			event.preventDefault();

			const radioSelectionne = this.value;
			const confirmation = window.confirm('Confirmer ?');

			if (confirmation) {
				const isChecked = document.querySelectorAll('#formQuetes input[type="radio"]:checked').length > 0;

				if (isChecked) {
					if (radioSelectionne === 'qJournas') {
						const objects = [];

						for (let i = 0; i < localStorage.length; i++) {
							const cleNettoyageJournas = localStorage.key(i);
							const valeurNettoyageJournas = localStorage.getItem(cleNettoyageJournas);
							const regexNettoyageJournas = /hebdo/i;
							const resultatNettoyageJournas = regexNettoyageJournas.exec(cleNettoyageJournas);

							if (!resultatNettoyageJournas) {
								objects.push({
									key: cleNettoyageJournas,
									value: valeurNettoyageJournas
								});
							}
						}

						objects.forEach(function(object) {
							localStorage.removeItem(object.key);
						});

						document.querySelector('#messageJourna').style.display = 'block';

						setTimeout(function() {
							window.location.href = 'https://thisip.pw/projets/wow';
						}, 200);
					} else if (radioSelectionne === 'qHebdos') {
						const objects = [];

						for (let i = 0; i < localStorage.length; i++) {
							const cleNettoyageHebdos = localStorage.key(i);
							const valeurNettoyageHebdos = localStorage.getItem(cleNettoyageHebdos);
							const regexNettoyageHebdos = /hebdo/i;
							const resultatNettoyageHebdos = regexNettoyageHebdos.exec(cleNettoyageHebdos);

							if (resultatNettoyageHebdos) {
								objects.push({
									key: cleNettoyageHebdos,
									value: valeurNettoyageHebdos
								});
							}
						}

						objects.forEach(function(object) {
							localStorage.removeItem(object.key);
						});

						document.querySelector('#messageHebdo').style.display = 'block';

						setTimeout(function() {
							window.location.href = 'https://thisip.pw/projets/wow';
						}, 200);
					} else if (radioSelectionne === 'qToutes') {
						localStorage.clear();
						document.querySelector('#messageToutes').style.display = 'block';
						setTimeout(function() {
							window.location.href = 'https://thisip.pw/projets/wow';
						}, 200);
					}
				}
			} else {
				document.querySelector('#messageAnnuler').style.display = 'block';
			}
		});
	});

	document.querySelectorAll('.quetes').forEach(function(div) {
		div.addEventListener('click', handleDivClick);
	});

	function checkHebdosStatus() {
		const hebdosDiv = document.querySelector('#hebdomadaires');
		const qToutesHebdos = hebdosDiv.querySelectorAll('p.quetesHebdos, span.quetesHebdos');
		const qHebdosCaches = Array.from(qToutesHebdos).filter((el) => getComputedStyle(el).display === 'none');
		const totalHebdosCaches = qHebdosCaches.length;
		const totalHebdos = qToutesHebdos.length;

		const messageHebdosTerminees = document.querySelector('#messageHebdosTerminees');
		if (totalHebdosCaches === totalHebdos) {
			/*
			if (messageHebdosTerminees) {
				messageHebdosTerminees.style.display = 'block';
			}
			*/

			const h1Hebdos = document.querySelector('#h1-hebdo');
			if (h1Hebdos) {
				h1Hebdos.style.display = 'none';
			}
		} else {
			if (messageHebdosTerminees) {
				messageHebdosTerminees.style.display = 'none';
			}
		}
	}

	checkHebdosStatus();

	const hebdosObserveDiv = document.querySelector('#hebdomadaires');
	const observerHebdos = new MutationObserver(checkHebdosStatus);
	observerHebdos.observe(hebdosObserveDiv, {childList: true, attributes: true, subtree: true, characterData: true});
});
</script>
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'projets/_footer.php';