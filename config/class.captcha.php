<?php



/* captcha.php */

// session_start();

// require_once '../config/class.captcha.php';

// $captcha = new Captcha();

// $_SESSION['captcha'] = $captcha->getCode();

// $captcha->afficherImage();



/* formulaire.php */

// $formulaire->ajouterChamp('captcha',		'text',			['label' => 'Captcha',		'placeholder' => '', 	'autocomplete' => 'off']);

// $formulaire->validerChamp('captcha', function($v) use ($formulaire) {
// 	if($v === null OR trim($v) === '')				return 'Captcha incorrect. Veuillez réessayer';
// 	if(mb_strlen($_POST['captcha']) !== 6)			return 'Captcha incorrect. Veuillez réessayer';
// 	if($_POST['captcha'] !== $_SESSION['captcha'])	return 'Captcha incorrect. Veuillez réessayer';
// });


// echo '<div class="d-flex mb-3">
// 	<div class="col-12 align-items-center">
// 		<img src="captcha.png" alt="Captcha" id="image-captcha">
// 		<button type="button" class="btn btn-light ms-1" id="rafraichir-captcha" title="Rafraîchir le code"><i class="fa-solid fa-rotate-right"></i></button>
// 	</div>
// </div>';

// echo $formulaire->ajouterChampHtml('captcha');



/* scripts.js */

// Captcha
/*
document.addEventListener('DOMContentLoaded', function () {
	const bouton = document.querySelector('#rafraichir-captcha');
	const image = document.querySelector('#image-captcha');

	if (bouton && image) {
		bouton.addEventListener('click', function () {
			image.src = 'captcha.png?t=' + new Date().getTime();
		});
	}
});
*/


class Captcha
{
	private string $code;
	private int $largeur;
	private int $hauteur;
	private int $longueurCode;
	private string $cheminPolice;
	private array $stylesBruit;

	public function __construct(int $largeur = 250, int $hauteur = 50, int $longueurCode = 6)
	{
		$this->largeur = $largeur;
		$this->hauteur = $hauteur;
		$this->longueurCode = $longueurCode;
		$this->code = $this->genererCode();

		$polices = [
			'Barriecito-Regular',
			'Eater-Regular',
			'Roboto-Regular',
		];

		$this->cheminPolice = '../hdv/assets/webfonts/'.$polices[rand(0, 2)].'.ttf';

		$this->stylesBruit = [
			'bruitLignes',
			'bruitPoints',
			'bruitCercles',
			'bruitGrille',
			'bruitVagues',
			'bruitChiffresEtLettres',
		];
	}

	private function genererCode(): string
	{
		$caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		// $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';

		return substr(str_shuffle(str_repeat($caracteres, 5)), 0, $this->longueurCode);
	}

	private function hexaVersRgb(string $couleurHex): array
	{
		$couleurHex = ltrim($couleurHex, '#');

		if(strlen($couleurHex) === 3)
		{
			$rouge = hexdec(str_repeat($couleurHex[0], 2));
			$vert = hexdec(str_repeat($couleurHex[1], 2));
			$bleu = hexdec(str_repeat($couleurHex[2], 2));
		}

		elseif(strlen($couleurHex) === 6)
		{
			$rouge = hexdec(substr($couleurHex, 0, 2));
			$vert = hexdec(substr($couleurHex, 2, 2));
			$bleu = hexdec(substr($couleurHex, 4, 2));
		}

		else {
			throw new InvalidArgumentException('Format de couleur hexadécimale incorrecte : '.$couleurHex);
		}

		return [
			'rouge' => $rouge,
			'vert' => $vert,
			'bleu' => $bleu
		];
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function afficherImage(): void
	{
		header('Content-Type: image/png');

		$image = imagecreatetruecolor($this->largeur, $this->hauteur);

		$couleurs = [
			[[218, 248, 227],			[0, 194, 199],			[0, 85, 130]],		// Underwater Scene - 74932
			[[179, 205, 224],			[0, 91, 150],			[1, 31, 75]],		// Beautiful Blues - 1294
			[[255, 255, 255],			[223, 227, 238],		[59, 89, 152]],		// Facebook - 185
			[[179, 236, 236],			[67, 232, 216],			[59, 214, 198]],	// Shades of Turquoise - 1836
			[[255, 8, 74],				[255, 98, 137],			[255, 194, 205]],	// Princess Pink Color - 6658
			[[218, 248, 227],			[0, 194, 199],			[0, 85, 130]],		// Underwater Scene - 74932
			[[179, 205, 224],			[0, 91, 150],			[1, 31, 75]],		// Beautiful Blues - 1294
			[[255, 255, 255],			[223, 227, 238],		[59, 89, 152]],		// Facebook - 185
			[[179, 236, 236],			[67, 232, 216],			[59, 214, 198]],	// Shades of Turquoise - 1836
			[[255, 194, 205],			[255, 98, 137],			[255, 8, 74]],		// Princess Pink Color - 6658
			[[0, 119, 119],				[0, 85, 85],			[0, 51, 51]],		// s+b teal - 309
			[[239, 187, 255],			[190, 41, 236],			[102, 0, 102]],		// Shades of Purple - 1835
			[[113, 199, 236],			[24, 154, 211],			[0, 80, 115]],		// cool blue - 30415
			[[229, 208, 255],			[204, 163, 255],		[191, 139, 255]],	// Violet - 292
			[[173, 255, 0],				[0, 255, 131],			[2, 137, 0]]		// I Loved In Shades of Green - 1325
		];

		shuffle($couleurs);

		$rougeTexte = $couleurs[0][0][0];
		$vertTexte = $couleurs[0][0][1];
		$bleuTexte = $couleurs[0][0][2];

		$bgCouleurs = $couleurs[0][rand(1, 2)];

		$rougeBgCouleurs = $bgCouleurs[0];
		$vertBgCouleurs = $bgCouleurs[1];
		$bleuBgCouleurs = $bgCouleurs[2];

		//									Image		Rouge						Vert						Bleu
		$couleurTexte = imagecolorallocate(	$image,		$rougeTexte,				$vertTexte,					$bleuTexte);
		$couleurFond = imagecolorallocate(	$image,		$rougeBgCouleurs,			$vertBgCouleurs,			$bleuBgCouleurs);

		imagefilledrectangle($image, 0, 0, $this->largeur, $this->hauteur, $couleurFond);

		$style = $this->stylesBruit[array_rand($this->stylesBruit)];
		$this->$style($image);

		if(file_exists($this->cheminPolice))
		{
			$tailleTexte = rand(20, 30);
			$x = rand(20, 150);
			$y = rand(26, 38);
			$angle = rand(-8, 8);

			imagettftext($image, $tailleTexte, $angle, $x, $y, $couleurTexte, $this->cheminPolice, $this->code);
		}

		else
		{
			$taille = 5;
			$longueurTexte = imagefontwidth($taille) * mb_strlen($this->code);
			$x = ($this->largeur - $longueurTexte) / 2;
			$y = ($this->hauteur - imagefontheight($taille)) / 2;
			imagestring($image, $taille, (int) $x, (int) $y, $this->code, $couleurTexte);
		}

		imagepng($image);
		imagedestroy($image);
	}

	// Bruit 1 : Lignes
	private function bruitLignes($image): void
	{
		$couleur = imagecolorallocate($image, rand(1, 255), rand(1, 255), rand(1, 255));

		for($i = 0; $i < rand(15, 35); $i++) {
			imageline($image, rand(-20, $this->largeur), rand(-20, $this->hauteur),
				rand(0, $this->largeur), rand(0, $this->hauteur), $couleur);
		}
	}

	// Bruit 2 : Points
	private function bruitPoints($image): void
	{
		$couleur = imagecolorallocate($image, rand(1, 255), rand(1, 255), rand(1, 255));
		for($i = 500; $i < 800; $i++) {
			imagesetpixel($image, rand(0, $this->largeur), rand(0, $this->hauteur), $couleur);
		}
	}

	// Bruit 3 : Cercles
	private function bruitCercles($image): void
	{
		$couleur = imagecolorallocate($image, 180, 180, 220);

		for($i = 0; $i < 15; $i++) {
			imageellipse($image, rand(0, $this->largeur), rand(0, $this->hauteur), rand(35, 55), rand(45, 65), $couleur);
		}
	}

	// Bruit 4 : Grille
	private function bruitGrille($image): void
	{
		$couleur = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
		$espacement = rand(10, 30);

		for($x = 0; $x < $this->largeur; $x += $espacement) {
			imageline($image, $x, 0, $x, $this->hauteur, $couleur);
		}

		for($y = 0; $y < $this->hauteur; $y += $espacement) {
			imageline($image, 0, $y, $this->largeur, $y, $couleur);
		}
	}

	// Bruit 5 : Vagues douces (lignes sinusoïdales)
	private function bruitVagues($image): void
	{
		$couleur = imagecolorallocate($image, rand(1, 255), rand(1, 255), rand(1, 255));

		for($y = 0; $y < $this->hauteur; $y += 5)
		{
			$amplitude = rand(10, 20);
			$frequence = rand(20, 50) / 250;

			for($x = 0; $x < $this->largeur; $x++)
			{
				$offset = (int) ($amplitude * sin($x * $frequence));

				imagesetpixel($image, $x, $y + $offset, $couleur);
			}
		}
	}

	// Bruit 6 : Des chiffres et des lettres
	private function bruitChiffresEtLettres($image): void
	{
		if(!file_exists($this->cheminPolice)) {
			return;
		}

		$caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

		for ($i = 0; $i < 15; $i++)
		{
			$char = $caracteres[rand(0, mb_strlen($caracteres) - 1)];
			$taille = rand(20, 30);
			$angle = rand(-40, 40);
			$x = rand(0, $this->largeur - 20);
			$y = rand(20, $this->hauteur - 5);

			$couleur = imagecolorallocatealpha($image, rand(160, 200), rand(160, 200), rand(160, 200), 100);

			imagettftext($image, $taille, $angle, $x, $y, $couleur, $this->cheminPolice, $char);
		}
	}
}