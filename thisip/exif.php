<?php
if(isset($_GET['telecharger-avec']))		$fichier = 'assets/img/exif-exemple.jpg';
elseif(isset($_GET['telecharger-sans']))	$fichier = 'assets/img/exif-exemple-propre.jpg';
elseif(isset($_GET['exif-outil']))			$fichier = 'exif.htm';
else										$fichier = null;

if(!empty($fichier) AND is_file($fichier))
{
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($fichier).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: '.filesize($fichier));

	readfile($fichier);
	exit;
}

require_once 'a_body.php';
?>
<div class="border rounded">
	<h1 class="mb-5 text-center"><a href="/exif"><i class="fa-regular fa-image"></i> EXIF</a></h1>

	<form action="#" method="post" id="exifForm">
		<div class="row" id="app">
			<div class="col-12 col-lg-5 mx-auto">
				<div class="input-group input-group-thisip justify-content-center">
					<input type="file" style="display: none;" id="exifFichier" accept="image/jpeg,image/jpg,image/png">
					<label for="exifFichier" class="btn btn-primary rounded">Sélectionner une image</label>
					<span id="nomFichier"></span>
				</div>
			</div>

			<div id="miniature"></div>
			<div id="output"></div>
			<canvas></canvas>
		</div>
	</form>

	<div class="mt-5 p-3 text-primary-emphasis bg-primary-subtle border border-primary-subtle rounded-3">
		<div class="d-flex align-items-center justify-content-center">
			<i class="fa-solid fa-circle-info fs-1 me-3"></i>
			<span>Télécharger une <a href="https://thisip.pw/assets/img/exif-exemple.jpg" class="fw-bold" data-fancybox="gallerie">image</a> d’exemple : <a href="https://thisip.pw/exif/telecharger-image-avec-exif" class="fw-bold"><i class="fa-regular fa-eye"></i> avec données EXIF</a> ou <a href="https://thisip.pw/exif/telecharger-image-sans-exif" class="fw-bold"><i class="fa-regular fa-eye-slash"></i> sans données EXIF</a></span>
		</div>
	</div>

	<div class="mb-5 mt-3 p-3 text-danger-emphasis bg-danger-subtle border border-danger-subtle rounded-3">
		<div class="d-flex align-items-center justify-content-center">
			<i class="fa-solid fa-circle-exclamation fs-1 me-3"></i>
			<span>L’image sera traitée uniquement sur votre appareil, directement dans votre navigateur sans aucun téléchargement externe.</span>
		</div>
	</div>

	<p>Si vous souhaitez utiliser cet outils hors ligne ou sur votre ordinateur, téléchargez le fichier <a href="https://thisip.pw/exif-outil">EXIF</a> et ouvrez le avec un navigateur (Firefox, Edge, Chrome, etc.).</p>

	<p>Avant de télécharger des images sur le Web, vous souhaiterez peut-être vérifier si vous ne donnez pas trop d'informations. Les appareils photo, smartphones et autres matériels stockent non seulement les informations sur l'image, mais également l'heure et la date, l'appareil photo utilisé et éventuellement même l'emplacement sur la planète dans chaque image dans les données <a href="https://fr.wikipedia.org/wiki/Exchangeable_image_file_format">EXIF</a>.</p>

	<p>En utilisant cet outil, vous pouvez voir ces données et télécharger une image dont toutes les données ont été supprimées pour l'envoyer.</p>

	<p>Téléchargez simplement votre image ici et vous obtiendrez toutes les informations qu'elle contient. S'il n'y a pas de données supplémentaires dans l'image, il vous le dira.</p>

	<p class="mb-0 text-center">Inspiré par <a href="http://removephotodata.com/"><strong>Chris Heilmann</strong></a>. Utilise <a href="https://github.com/exif-js/exif-js"><strong>exif.js</strong></a> de Jacob Seidelin</p>
</div>

<script src="/assets/js/exif.js?<?= filemtime('assets/js/exif.js'); ?>"></script>
<?php
require_once 'a_footer.php';