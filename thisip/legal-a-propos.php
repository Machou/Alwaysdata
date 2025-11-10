<?php
require_once 'a_body.php';
?>
<div class="border rounded">
	<h1 class="mb-5 text-center"><a href="/a-propos"><i class="fa-solid fa-circle-user"></i> À Propos</a></h1>

	<div>
		<h3 class="mb-4" id="informations"><a href="#informations" class="ancre"><i class="fa-solid fa-user"></i> Informations</a></h3>

		<ul class="list-group list-group-flush">
			<li style="background-color: transparent;" class="list-group-item"><a href="/politique-de-confidentialite">Politique de confidentialité</a></li>
			<li style="background-color: transparent;" class="list-group-item"><a href="/cgu">Conditions générales d’utilisation</a></li>
			<li style="background-color: transparent;" class="list-group-item"><a href="https://thisip.pw/">ThisIP.pw</a> est hébergé chez <a href="https://www.alwaysdata.com/">Alwaysdata</a></li>
			<li style="background-color: transparent;" class="list-group-item">Service gratuit</li>
			<li style="background-color: transparent;" class="list-group-item">Technologies utilisées : <a href="https://www.php.net/"><span style="background-color: #4f5b93;" class="badge">PHP</span></a> et JavaScript</li>
			<li style="background-color: transparent;" class="list-group-item"><abbr title="Réseau de diffusion de contenu">CDN</abbr> utilisé : <a href="https://www.cloudflare.com/">Cloudflare</a></li>
		</ul>
	</div>

	<hr class="my-5">

	<div>
		<h3 class="mb-4" id="logo"><a href="#logo" class="ancre"><i class="fa-solid fa-image"></i> Logo</a></h3>

		<p class="text-center">
			<a href="https://thisip.pw/logo.png" data-fancybox="gallerie"><img src="logo.png" style="width: 350px;" class="img-fluid rounded" alt="Logo ThisIP.pw" title="Logo ThisIP.pw"></a>
		</p>
	</div>

	<hr class="my-5">

	<div>
		<h3 class="mb-4" id="bibliothequesJavaScript"><a href="#bibliothequesJavaScript" class="ancre"><i class="fa-solid fa-download"></i> Liste des bibliothèques CSS / JavaScript</a></h3>

		<div class="d-flex">
			<div class="col-12 col-lg-8 mx-auto">
				<ul class="list-group">
					<?php
					$assetsArray = [
						// Nom						Version			GitHub								UNPNKG								Site Officiel						Étiquettes
						['Bootstrap',				'5.3.8',		'twbs/bootstrap',					'bootstrap',						'https://getbootstrap.com/',		'CSS / JavaScript'],
						['LeafLet',					'1.9.4',		'leaflet/leaflet',					'leaflet',							'https://leafletjs.com/',			'JavaScript'],
						['Fancybox',				'6.1.4',		'fancyapps/ui',						'@fancyapps/ui',					'https://fancyapps.com/',			'CSS / JavaScript'],
						['clipboard.js',			'2.0.11',		'zenorocha/clipboard.js',			'clipboard',						'https://clipboardjs.com/',			'JavaScript'],
						['Detect GPU',				'5.0.70',		'pmndrs/detect-gpu',				'detect-gpu',						null,								'JavaScript'],
						['Babel',					'6.5.0',		'babel/babel',						'babel-regenerator-runtime',		'https://babel.dev/',				'JavaScript'],
						['UAParser.js',				'2.0.6',		'faisalman/ua-parser-js',			'ua-parser-js',						'https://uaparser.dev/',			'JavaScript'],
						['Exif.js',					'2.3.0',		'exif-js/exif-js',					'exif-js',							null,								'JavaScript'],
						['Font Awesome',			'7.1.0',		'fortawesome/font-awesome',			'@fortawesome/fontawesome-free',	'https://fontawesome.com/',			'CSS'],
						['detectIncognito.js',		'1.6.2',		'joe12387/detectincognito',			'detectincognitojs',				'https://detectincognito.com/',		'JavaScript'],
						['Fingerprint',				'5.0.1',		'fingerprintjs/fingerprintjs',		'@fingerprintjs/fingerprintjs',		'https://fingerprint.com/',			'JavaScript'],
						['js-beautify',				'1.15.4',		'beautifier/js-beautify',			'js-beautify',						'https://beautifier.io/',			'JavaScript'],
					];

					foreach($assetsArray as $cleAsset => $vAsset)
					{
						$etiquettes = str_ireplace('CSS', '<span class="badge text-bg-success">CSS</span>', $vAsset[5]);
						$etiquettes = str_ireplace('JavaScript', '<span class="badge text-bg-warning">JavaScript</span>', $etiquettes);

						echo '<li class="list-group-item">
							<div class="row py-3">
								<div class="col-6 col-lg-4 text-center text-lg-start mb-3 mb-lg-0 px-0 px-lg-2" title="Nom"><strong>'.$vAsset[0].'</strong><span class="badge text-bg-secondary ms-2">'.$vAsset[1].'</span></div>
								<div class="col-6 col-lg-3 text-center text-lg-start mb-3 mb-lg-0">'.$etiquettes.'</div>

								<div class="col-12 col-lg-5 text-center">
									<a href="https://github.com/'.$vAsset[2].'" class="btn btn-outline-primary btn-sm" '.$onclick.' title="Dépôt GitHub de '.$vAsset[0].'"><i class="fa-brands fa-github"></i> GitHub</a>
									<a href="https://app.unpkg.com/'.$vAsset[3].'@latest" class="btn btn-outline-primary btn-sm" '.$onclick.' title="Dépôt UNPKG de '.$vAsset[0].'"><i class="fa-regular fa-file-code"></i> UNPKG</a>
									'.(!empty($vAsset[4]) ? '<a href="'.$vAsset[4].'" class="btn btn-outline-primary btn-sm" '.$onclick.' title="Site officiel de '.$vAsset[0].'"><i class="fa-solid fa-globe"></i> Site Officiel</a>' : '<span class="btn btn-outline-secondary btn-sm" title="Site officiel de '.$vAsset[0].' inconnu"><i class="fa-solid fa-globe"></i> Site Officiel</span>
									').'
								</div>
							</div>
						</li>';
					}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php
require_once 'a_footer.php';