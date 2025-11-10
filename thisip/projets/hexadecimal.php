<?php
session_start();

require_once '../../config/config.php';

function hexadecimalVersModeles(string $codeHex, ?float $alphaOverride = null): array
{
	$hex = ltrim(trim($codeHex), '#');
	$taille = strlen($hex);

	if($taille === 3 OR $taille === 4)
	{
		$etendu = '';
		for ($i = 0; $i < $taille; $i++)
		{
			$etendu .= str_repeat($hex[$i], 2);
		}

		$hex = $etendu;
		$taille = strlen($hex);
	}

	if($taille !== 6 && $taille !== 8) {
		throw new InvalidArgumentException('Format hex invalide. Exemple : #RGB, #RRGGBB, #RGBA ou #RRGGBBAA');
	}

	if(!ctype_xdigit($hex)) {
		throw new InvalidArgumentException('Le code hexadécimal contient des caractères non hexadécimaux');
	}

	$r = hexdec(substr($hex, 0, 2));
	$g = hexdec(substr($hex, 2, 2));
	$b = hexdec(substr($hex, 4, 2));
	$a = 1.0;

	if($taille === 8) {
		$a = hexdec(substr($hex, 6, 2)) / 255.0;
	}

	if($alphaOverride !== null)
	{
		if($alphaOverride < 0.0)		$alphaOverride = 0.0;
		elseif($alphaOverride > 1.0)	$alphaOverride = 1.0;

		$a = $alphaOverride;
	}

	$hsl = rgbVersHsl($r, $g, $b);

	return [
		'hex' => '#'.strtoupper($hex),
		'rgb' => ['r' => $r, 'g' => $g, 'b' => $b],
		'rgba' => ['r' => $r, 'g' => $g, 'b' => $b, 'a' => $a],
		'hsl' => $hsl,
	];
}

function rgbVersHsl(int $r, int $g, int $b): array
{
	$rf = $r / 255.0;
	$gf = $g / 255.0;
	$bf = $b / 255.0;

	$max = max($rf, $gf, $bf);
	$min = min($rf, $gf, $bf);
	$delta = $max - $min;

	$l = ($max + $min) / 2.0;

	if($delta == 0.0)
	{
		$h = 0.0;
		$s = 0.0;
	}

	else
	{
		$s = ($l > 0.5) ? ($delta / (2.0 - $max - $min)) : ($delta / ($max + $min));

		if($max === $rf)		$h = fmod((($gf - $bf) / $delta), 6.0);
		elseif($max === $gf)	$h = (($bf - $rf) / $delta) + 2.0;
		else					$h = (($rf - $gf) / $delta) + 4.0;

		$h *= 60.0;
		if($h < 0.0)
		{
			$h += 360.0;
		}
	}

	$sPerc = round($s * 100.0, 2);
	$lPerc = round($l * 100.0, 2);
	$hDeg = round($h, 2);

	return [
		'h' => $hDeg,
		's' => $sPerc,
		'l' => $lPerc
	];
}

if(!empty($_FILES['image']))
{
	$img = $_FILES['image']['tmp_name'];
	$maxPixels = 12_000_000;
	$donneesCsv = __DIR__.'/pixels.csv';

	[$l, $h, $type] = getimagesize($img) ?: [0, 0, null];
	if($l === 0 OR $h === 0)
	{
		http_response_code(400);

		setFlash('danger', 'Image invalide');

		header('Location: hexadecimal');
		exit;
	}

	if($l * $h > $maxPixels)
	{
		$echelle = sqrt($maxPixels / ($l * $h));
		$longueurN = max(1, (int)($l * $echelle));
		$hauteurN = max(1, (int)($h * $echelle));
	}

	else
	{
		$longueurN = $l;
		$hauteurN = $h;
	}

	switch($type)
	{
		case IMAGETYPE_JPEG: $im = imagecreatefromjpeg($img); break;
		case IMAGETYPE_PNG: $im = imagecreatefrompng($img); break;
		default:
			http_response_code(415);
			exit('Format non pris en charge (JPEG/PNG uniquement).');
	}

	if($longueurN !== $l || $hauteurN !== $h)
	{
		$redimensionn= imagecreatetruecolor($longueurN, $hauteurN);

		imagecopyresampled($redimension, $im, 0, 0, 0, 0, $longueurN,$hauteurN, $l,$h);
		imagedestroy($im);

		$im = $redimension;
		$l = $longueurN;
		$h = $hauteurN;
	}

	$csv = new SplFileObject($donneesCsv, 'w');
	$csv->setCsvControl(',', '"', '\\');

	for($y = 0; $y < $h; $y++)
	{
		$colonne = [];
		for ($x = 0; $x < $l; $x++)
		{
			$rgb = imagecolorat($im, $x, $y);

			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;

			$colonne[] = sprintf('#%02X%02X%02X', $r, $g, $b);
		}

		$csv->fputcsv($colonne);
	}

	imagedestroy($im);

	$fichierCSV = __DIR__.'/pixels.csv';
	if(!file_exists($fichierCSV))
	{
		setFlash('danger', 'Le fichier CSV <span class="fw-bold">'.$fichierCSV.'</span>, n’existe pas.');

		header('Location: hexadecimal');
		exit;
	}

	$csv = new SplFileObject($fichierCSV, 'r');
	$csv->setFlags(SplFileObject::READ_CSV);
	$csv->setCsvControl(',', '"', '\\');

	$nb = [];
	foreach($csv as $colonne)
	{
		if($colonne === [null] || $colonne === false) {
			continue;
		}

		foreach($colonne as $couleur)
		{
			if($couleur === null || $couleur === '') {
				continue;
			}

			$nb[$couleur] = ($nb[$couleur] ?? 0) + 1;
		}
	}

	arsort($nb);

	$top = array_slice($nb, 0, 5, true);



	$msg[] = '<div class="col-12 col-lg-4 mb-5 mx-auto">
		<h4 class="mb-4">Informations de l’image</h4>

		<p class="mb-4"><span class="fw-bold">Hauteur</span> : '.number_format($h).' pixels<br><span class="fw-bold">Longueur</span> : '.number_format($l).' pixels<br><span class="fw-bold">Type</span> : '.image_type_to_mime_type($type).'</p>

		<h4 class="mb-4">Top 5 des couleurs les plus fréquentes</h4>

		<ol class="list-group list-group-numbered">';

			foreach($top as $couleur => $freq)
			{
				$id = 'couleur-id'.strtolower(str_ireplace('#', '', $couleur));
				$clr = hexadecimalVersModeles($couleur);
				$couleurHexa = strtolower($couleur);

				$rgb = 'rgb('.$clr['rgba']['r'].','.$clr['rgba']['g'].','.$clr['rgba']['b'].')';
				$rgba = 'rgba('.$clr['rgba']['r'].','.$clr['rgba']['g'].','.$clr['rgba']['b'].', '.$clr['rgba']['a'].')';
				$hsl = 'hsl('.$clr['hsl']['h'].', '.$clr['hsl']['s'].', '.$clr['hsl']['l'].')';

				$msg[] = '<li class="list-group-item d-flex justify-content-between align-items-start">
					<div class="ms-2 me-auto">
						<div><span class="fw-bold">Code héxadécimal</span> : '.$couleurHexa.'</div>
						<div><span class="fw-bold">Code rgb</span> : '.$rgb.'</div>
						<div><span class="fw-bold">Code rgba</span> : '.$rgba.'</div>
						<div><span class="fw-bold">Code hsl</span> : '.$hsl.'</div>
						<div style="color: '.($couleurHexa === '#ffffff' ? '#000000' : $rgba).';" class="fw-bold mt-2" id="'.$id.'"></div>
					</div>
					<span style="color: '.$rgba.';" class="badge rounded-pill fw-bold '.($couleurHexa === '#ffffff' ? ' bg-primary' : null).'">'.number_format($freq).' pixels</span>
				</li>

				<script>
					(function() {
						let n_match = ntc.name("'.$couleur.'");
						let color = document.querySelector("#'.$id.'");
						if(color) {
							color.innerHTML = n_match[1];
						}
					})();
				</script>';
			}

		$msg[] = '</ol>
	</div>';

	if(!empty($msg))
	{
		$_SESSION['msg'] = $msg;
		unlink($fichierCSV);
	}
}
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
	<meta charset="utf-8">

	<title>Analyser les couleurs d’une image</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<link rel="icon" href="/assets/img/favicon.png">
	<link rel="stylesheet" href="/assets/css/vendors.css?<?= filemtime('../assets/css/vendors.css'); ?>">

	<style>
	.input-group {
		--bs-bg-opacity: 1;
		border-radius: var(--bs-border-radius) !important;
		background-color: rgba(var(--bs-white-rgb), var(--bs-bg-opacity)) !important;
		border: var(--bs-border-width) var(--bs-border-style) var(--bs-black) !important;
		padding: .5rem !important;
	}

	.input-group > .input-group-text	{ padding: .375rem !important; }
	.input-group > .form-control-lg		{ padding: .375rem !important; }
	.input-group .input-group-text		{ background-color: rgba(var(--bs-white-rgb), var(--bs-bg-opacity)) !important; border: none; }
	.input-group input					{ border-color: transparent !important; border: none !important; box-shadow: none !important; }
	.input-group input:focus			{ border-color: transparent !important; border: none !important; box-shadow: none !important; }
	.input-group input:active			{ border-color: transparent !important; border: none !important; box-shadow: none !important; }
	.input-group label,
	.input-group button					{ border-radius: var(--bs-border-radius) !important; font-weight: bold; opacity: .75; }
	</style>

	<script src="/assets/js/vendors.js?<?= filemtime('../assets/js/vendors.js'); ?>"></script>
	<script src="https://chir.ag/projects/ntc/ntc.js"></script>
</head>

<body>
<div class="container">
	<h1 class="my-5 text-center"><a href="hexadecimal" class="link-offset-2">Analyse des couleurs d’une image</a></h1>

	<?= getFlash(); ?>
	<?= !empty($msg) ? implode($msg) : null; ?>

	<div class="row">
		<div class="col-12 col-lg-6 mx-auto">
			<form action="hexadecimal" method="post" id="imageForm" enctype="multipart/form-data">
				<div class="row" id="app">
					<div class="col-12 col-lg-8 mx-auto">
						<div class="input-group justify-content-center">
							<input type="file" style="display: none;" id="image" name="image" accept="image/jpeg,image/jpg,image/png">
							<label for="image" class="btn btn-primary rounded">Sélectionner une image</label>
						</div>
					</div>
				</div>
			</form>

			<script>
			document.getElementById("image").addEventListener("change", function() {
				if(this.files.length > 0) {
					document.getElementById("imageForm").submit();
				}
			});
			</script>
		</div>
	</div>
</div>
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'projets/_footer.php';