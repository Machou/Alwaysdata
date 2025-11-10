<?php
require_once '../../config/fonctions.php';

header('Content-Type: image/jpeg');

$img = @imagecreatefromjpeg(mt_rand(0, 1000000).'.image');

// X : Horizontal - Y : Vertical
$w = 900;
$h = 600;

$img = imagecreatetruecolor($w, $h);

//											rouge			vert			bleu
$blanc	= imagecolorallocate($img,			255,			255,			255);
$rouge	= imagecolorallocate($img,			255,			0,				0);
$violet	= imagecolorallocate($img,			255,			22,				255);
$noir	= imagecolorallocate($img,			0,				0,				0);
$vert	= imagecolorallocate($img,			132,			135,			28);
$rose	= imagecolorallocate($img,			255,			105,			180);
$bleu	= hexdec(substr('BEDFFE', 4, 6));

// Image rectangulaire blanche
imagefilledrectangle($img,					2,				2,				($w - 4),			($h - 4),			$blanc);
//											x1				y1				x2					y2					couleur


// Polygone rouge
imagepolygon($img,	[
						5,		5,
						100,	100,
						150,	40
					],		$rouge);

imagepolygon($img,	[
						450,	100,
						200,	200,
						450,	350
					],		$bleu);


// Rectlange vert
//							0, 0 est le coin en haut à gauche
//
//							X : coin en haut		Y : coin en haut			X : point en bas				Y : point en bas					identificateur de couleur créé avec imagecolorallocate()
//							à gauche				à gauche					à droite						à droite
imagerectangle($img,		50,						50,							850,							550,								$rouge);
imagerectangle($img,		100,					100,						800,							500,								$rose);
imagerectangle($img,		150,					150,						750,							450,								$violet);
imagerectangle($img,		200,					200,						700,							400,								$vert);
imagerectangle($img,		250,					250,						650,							350,								$noir);
//							x1						y1							x2								y2

// Texte violet et noir
$site = (isset($_GET['site']) AND !empty($_GET['site'])) ? secuChars($_GET['site']) : 'www.google.com';

//							taille 					X							Y								texte											couleur
imagestring($img,			12,						($h / 2) + 60,				(($h / 2) - 30),				'site : '.secuChars($site),						$violet);
imagestring($img,			12,						($h / 2) + 65,				(($h / 2) + 15),				'ip : '.gethostbyname(secuChars($site)),		$noir);


// Elipse

//													X							Y								border color									color image
imageellipse($img,									50,							50,								50, 50,											imagecolorallocate($img, 0, 0, 0));

$border = imagecolorallocate($img,	0,		0,		0);
$fill = imagecolorallocate($img,	255,	0,		0);
imagefilltoborder($img,				50,		50,		$border,		$fill);


// Filtres divers - https://www.php.net/imagefilter

// imagefilter($img, IMG_FILTER_COLORIZE, 255, 255, 0);
// imagefilter($img, IMG_FILTER_GRAYSCALE);
// imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
// imagefilter($img, IMG_FILTER_PIXELATE, 3, 2);


// Triangles en pixels

$corners[0] = ['x' => 	100,	'y' => 10];
$corners[1] = ['x' =>	0,		'y' => 190];
$corners[2] = ['x' =>	200,	'y' => 190];

$rouge = imagecolorallocate($img, 255, 0, 0);

for ($i = 0; $i < 100000; $i++) {
	imagesetpixel($img, round($w), round($h), $rouge);

	$a = mt_rand(0, 2);
	$w = ($w + $corners[$a]['x']) / 2;
	$h = ($h + $corners[$a]['y']) / 2;
}


//							fichier ?			qualité | 1 - 100
imagejpeg($img,				null,				100);

imagedestroy($img);