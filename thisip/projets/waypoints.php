<?php
require_once '../../config/config.php';

if(!empty($_POST['wp']))
{
	$wps = explode(PHP_EOL, $_POST['wp']);
	$nbWps = count($wps);

	$div = (!empty($_POST['division']) AND ($_POST['division'] >= 1 OR $_POST['division'] <= 5)) ? (int) $_POST['division'] : 2;

	$wpsNouveau = [];
	for($i = 0; $i < $nbWps; $i++) {
		if($i % $div == 0)
			$wpsNouveau[] = $wps[$i];
	}
}
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title>Points de passages</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<link rel="icon" href="/assets/img/favicon-wow.svg">
	<link rel="stylesheet" href="/assets/css/vendors.css?<?= filemtime('../assets/css/vendors.css'); ?>">

	<script src="/assets/js/vendors.js?<?= filemtime('../assets/js/vendors.js'); ?>"></script>
</head>

<body>
<div class="container">
	<h1 class="my-5 text-center"><a href="waypoints" class="link-offset-2">Points de passages</a></h1>

	<div class="row">
		<div class="col-12 col-lg-8 mx-auto">
			<?php
			if(!empty($wpsNouveau))
			{
				echo '<p class="fs-5"><span class="fw-bold">'.count($wpsNouveau).'</span> ligne'.s($wpsNouveau).'</p>

				<textarea style="cursor: pointer; height: 300px;" class="form-control form-control-lg border border-danger rounded mb-5" '.$onclickSelect.'>';

				foreach($wpsNouveau as $w)
					echo $w."\n";

				echo '</textarea>';
			}

			echo (!empty($_POST['wp']) ? '<p class="fs-5"><span class="fw-bold">'.$nbWps.'</span> points de passages</p>' : null).'

			<form action="waypoints" method="post">
				<div class="input-group mb-3">
					<div class="form-floating">
						<textarea name="wp" style="cursor: pointer; height: 300px;" class="form-control" placeholder="Points de passages" id="floatingTextarea" required '.$onclickSelect.'>'.(!empty($_POST['wp']) ? $_POST['wp'] : null).'</textarea>
						<label for="floatingTextarea">Points de passages…</label>
					</div>
					<input type="submit" value="Valider" class="btn btn-success">
				</div>

				<select class="form-select" name="division">
					<option value="1" '.((!empty($_POST['division']) AND $_POST['division'] == '1') ? 'selected' : null).'>1</option>
					<option value="2" '.((empty($_POST['division']) OR (!empty($_POST['division']) AND $_POST['division'] == '2')) ? 'selected' : null).'>2</option>
					<option value="3" '.((!empty($_POST['division']) AND $_POST['division'] == '3') ? 'selected' : null).'>3</option>
					<option value="4" '.((!empty($_POST['division']) AND $_POST['division'] == '4') ? 'selected' : null).'>4</option>
					<option value="5" '.((!empty($_POST['division']) AND $_POST['division'] == '5') ? 'selected' : null).'>5</option>
					<option value="6" '.((!empty($_POST['division']) AND $_POST['division'] == '6') ? 'selected' : null).'>6</option>
					<option value="7" '.((!empty($_POST['division']) AND $_POST['division'] == '7') ? 'selected' : null).'>7</option>
					<option value="8" '.((!empty($_POST['division']) AND $_POST['division'] == '8') ? 'selected' : null).'>8</option>
				</select>
			</form>
		</div>
	</div>
</div>';

require_once $_SERVER['DOCUMENT_ROOT'].'projets/_footer.php';


// $a = [
// 	"https://www.reddit.com/r/07girlsgonewild",
// 	"https://www.reddit.com/r/07nsfw",
// 	"https://www.reddit.com/r/18_22",
// 	"https://www.reddit.com/r/18above_Roleplay",
// 	"https://www.reddit.com/r/18F",
// 	"https://www.reddit.com/r/1to1reps",
// 	"https://www.reddit.com/r/4KUHDBluray",
// 	"https://www.reddit.com/r/AdorableBoobs",
// 	"https://www.reddit.com/r/AdorableNudes",
// 	"https://www.reddit.com/r/AIAssisted",
// 	"https://www.reddit.com/r/aipromptprogramming",
// 	"https://www.reddit.com/r/alberta",
// 	"https://www.reddit.com/r/AlbumCovers",
// 	"https://www.reddit.com/r/AmateurPhotography",
// 	"https://www.reddit.com/r/androidapps",
// 	"https://www.reddit.com/r/anime_random",
// 	"https://www.reddit.com/r/Breeding_her",
// 	"https://www.reddit.com/r/BreedMeDaddy",
// 	"https://www.reddit.com/r/ButtholeSpokes",
// 	"https://www.reddit.com/r/ChatGPT",
// 	"https://www.reddit.com/r/chubby",
// 	"https://www.reddit.com/r/chubbyasians",
// 	"https://www.reddit.com/r/CitlaliMains",
// 	"https://www.reddit.com/r/clubmilfs",
// 	"https://www.reddit.com/r/CNFans",
// 	"https://www.reddit.com/r/collegesluts",
// 	"https://www.reddit.com/r/CoutureReps",
// 	"https://www.reddit.com/r/cuckoldparadise",
// 	"https://www.reddit.com/r/CumDumpsters",
// 	"https://www.reddit.com/r/cumshots",
// 	"https://www.reddit.com/r/cute_animals",
// 	"https://www.reddit.com/r/CuteFuckToys",
// 	"https://www.reddit.com/r/CuteLittleTits",
// 	"https://www.reddit.com/r/DaddysLilGirl",
// 	"https://www.reddit.com/r/DaughterPassion",
// 	"https://www.reddit.com/r/DesperateHousewives",
// 	"https://www.reddit.com/r/dommes",
// 	"https://www.reddit.com/r/DressedAndUndressed",
// 	"https://www.reddit.com/r/dubstep",
// 	"https://www.reddit.com/r/Eatventure",
// 	"https://www.reddit.com/r/EGirls",
// 	"https://www.reddit.com/r/Eldenring",
// 	"https://www.reddit.com/r/EngagementRings",
// 	"https://www.reddit.com/r/FastSexting",
// 	"https://www.reddit.com/r/Feldup",
// 	"https://www.reddit.com/r/FemBoys",
// 	"https://www.reddit.com/r/FitGirlRepack",
// 	"https://www.reddit.com/r/Foot_Island",
// 	"https://www.reddit.com/r/GetComputerHelp",
// 	"https://www.reddit.com/r/GirlfriendsNSFW",
// 	"https://www.reddit.com/r/gonewildstories",
// 	"https://www.reddit.com/r/GreatView",
// 	"https://www.reddit.com/r/GuessMyBirthYear",
// 	"https://www.reddit.com/r/gymgirls",
// 	"https://www.reddit.com/r/GymGirlsNSFW",
// 	"https://www.reddit.com/r/HairStyleAdvice",
// 	"https://www.reddit.com/r/HollowKnight",
// 	"https://www.reddit.com/r/homeowners",
// 	"https://www.reddit.com/r/HornyHotHumble",
// 	"https://www.reddit.com/r/HornyPetiteTeenGirls",
// 	"https://www.reddit.com/r/hotdommes",
// 	"https://www.reddit.com/r/Hotwife",
// 	"https://www.reddit.com/r/hugelabialove",
// 	"https://www.reddit.com/r/IncestTabooPorn",
// 	"https://www.reddit.com/r/InfluencerNSFW_global",
// 	"https://www.reddit.com/r/Insurance",
// 	"https://www.reddit.com/r/iptvx",
// 	"https://www.reddit.com/r/ITCareerQuestions",
// 	"https://www.reddit.com/r/jewelry",
// 	"https://www.reddit.com/r/JewelryReps",
// 	"https://www.reddit.com/r/Just18",
// 	"https://www.reddit.com/r/KarmaNSFW18",
// 	"https://www.reddit.com/r/LaneyaGrace",
// 	"https://www.reddit.com/r/latinas",
// 	"https://www.reddit.com/r/LegalTeens",
// 	"https://www.reddit.com/r/LifeProTips",
// 	"https://www.reddit.com/r/lingerieforsex",
// 	"https://www.reddit.com/r/LivestreamFail",
// 	"https://www.reddit.com/r/LustForSex",
// 	"https://www.reddit.com/r/masterduel",
// 	"https://www.reddit.com/r/masturbation",
// 	"https://www.reddit.com/r/memeframe",
// 	"https://www.reddit.com/r/Mexicana",
// 	"https://www.reddit.com/r/Mexicanasdesnudas",
// 	"https://www.reddit.com/r/namemypet",
// 	"https://www.reddit.com/r/namemypet_",
// 	"https://www.reddit.com/r/NavelAddict",
// 	"https://www.reddit.com/r/NFA",
// 	"https://www.reddit.com/r/NormalPeopleBBCHulu",
// 	"https://www.reddit.com/r/notinteresting",
// 	"https://www.reddit.com/r/NSFWGenuineBeauties",
// 	"https://www.reddit.com/r/Nude_Selfie",
// 	"https://www.reddit.com/r/NudeGirlsHub",
// 	"https://www.reddit.com/r/onlineSugar",
// 	"https://www.reddit.com/r/OnlyShavedGirls",
// 	"https://www.reddit.com/r/OUTFITS",
// 	"https://www.reddit.com/r/PawgLove",
// 	"https://www.reddit.com/r/PerfectBody",
// 	"https://www.reddit.com/r/phclassifieds",
// 	"https://www.reddit.com/r/PhotoshopRequest",
// 	"https://www.reddit.com/r/PokemonInfiniteFusion",
// 	"https://www.reddit.com/r/Portal",
// 	"https://www.reddit.com/r/productivity",
// 	"https://www.reddit.com/r/psychicreadings",
// 	"https://www.reddit.com/r/PTOrdenado",
// 	"https://www.reddit.com/r/PunkGirls",
// 	"https://www.reddit.com/r/r4SextChat",
// 	"https://www.reddit.com/r/Rate_my_feet",
// 	"https://www.reddit.com/r/RealAhegao",
// 	"https://www.reddit.com/r/Realamateurfucking",
// 	"https://www.reddit.com/r/realHomemade",
// 	"https://www.reddit.com/r/RealHomePorn",
// 	"https://www.reddit.com/r/royalcaribbean",
// 	"https://www.reddit.com/r/SFWRedheads",
// 	"https://www.reddit.com/r/ShareYourSelfie",
// 	"https://www.reddit.com/r/SideProject",
// 	"https://www.reddit.com/r/SitOnYourFace",
// 	"https://www.reddit.com/r/skinnytail",
// 	"https://www.reddit.com/r/sluts",
// 	"https://www.reddit.com/r/slutsofsnapchat",
// 	"https://www.reddit.com/r/SmallCutie",
// 	"https://www.reddit.com/r/solesandface",
// 	"https://www.reddit.com/r/SSBBW_FANS",
// 	"https://www.reddit.com/r/StinkyStarfish",
// 	"https://www.reddit.com/r/Supplements",
// 	"https://www.reddit.com/r/TeenRoyalty",
// 	"https://www.reddit.com/r/TeenWonderLand",
// 	"https://www.reddit.com/r/ThickThighs",
// 	"https://www.reddit.com/r/thong",
// 	"https://www.reddit.com/r/tightdresses",
// 	"https://www.reddit.com/r/tiny18_21",
// 	"https://www.reddit.com/r/TopDownThong",
// 	"https://www.reddit.com/r/TotalBabes",
// 	"https://www.reddit.com/r/traps",
// 	"https://www.reddit.com/r/TroubledTeens101",
// 	"https://www.reddit.com/r/VietNam",
// 	"https://www.reddit.com/r/vpns",
// 	"https://www.reddit.com/r/WallStreetBetsCrypto",
// 	"https://www.reddit.com/r/Warframe"
// ];

// foreach($a as $l)
// 	echo '<p id="'.slug($l).'"><a href="'.$l.'" onclick="hide(\'#'.slug($l).'\');">'.$l.'</a></p>';