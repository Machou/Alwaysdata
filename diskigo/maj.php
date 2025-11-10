<?php
require_once '../config/diskigo_config.php';

if(preg_match('/maj/', $_SERVER['SCRIPT_NAME']))
{
	if(isset($_GET['zoopla']))
	{
		setcookie(
			'zoopla',
			password_hash('M0T_DE_P4SSE', PASSWORD_ARGON2I),
			time() + (60 * 60 * 24 * 365),
			'/',
			'diskigo.com',
			true,	// Envoyé uniquement via HTTPS
			true,	// Non accessible via JavaScript
		);

		header('Location: https://www.diskigo.com/?maj');
		exit();
	}

	else
	{
		if(isset($_COOKIE['zoopla']))
		{
			if(!password_verify('M0T_DE_P4SSE', $_COOKIE['zoopla']))
			{
				header('Location: https://www.diskigo.com/');
				exit();
			}
		}

		else
		{
			header('Location: https://www.diskigo.com/');
			exit();
		}
	}
}

$languesMajArray = ['us' => 'États-Unis', 'uk' => 'Angleterre', 'de' => 'Allemagne', 'es' => 'Espagne', 'fr' => 'France', 'it' => 'Italie', 'ca' => 'Canada', 'au' => 'Australie', 'in' => 'Inde', 'se' => 'Suède', 'ie' => 'Irlande'];

if(!isset($_GET['erreur']) AND !isset($_GET['succes']) AND !empty($_GET['locale']))
{
	if(array_key_exists($_GET['locale'], $languesMajArray))
	{
		$locale = secuChars($_GET['locale']);

		if(is_file('donnees/Disk_Prices__'.strtoupper($locale).'_.html'))
		{
			$str = file_get_contents('donnees/Disk_Prices__'.strtoupper($locale).'_.html');

			if(!empty($str))
			{
				preg_match_all('/<tr class="disk" data-product-type="(?P<dataProductType>.*)" data-condition="(?P<dataCondition>.*)" data-capacity="(?P<dataCapacity>.*)"(?: style="display:none")?>(.*)<td class="price-per-gb hidden">(?P<pricePerGb>.*)<\/td>(.*)<td class="price-per-tb">(?P<pricePerTb>.*)<\/td>(.*)<td>(?P<price>.*)<\/td>(.*)<td>(?P<capacity>.*)<\/td>(.*)<td>(?P<warranty>.*)<\/td>(.*)<td>(?P<format>.*)<\/td>(.*)<td>(?P<technology>.*)<\/td>(.*)<td>(?P<condition>.*)<\/td>(.*)<td class="name"><a href="https:\/\/www.amazon.(?P<extAmazon>.*)\/dp\/(?P<amazon>[a-zA-Z\d]{10})\?(.*)">(?P<nameDisk>.*)<\/a><\/td>/siU',
				$str,
				$matches, PREG_SET_ORDER, 0);

				$countMatches = count($matches);
				for($i = 0; $i <= $countMatches; $i++)
				{
					if(!empty($matches[$i]['amazon']))
					{
						$amazonDp = !empty($matches[$i]['amazon'])					? secuChars(clean($matches[$i]['amazon']))								: null;

						$amazonNomDisque = !empty($matches[$i]['nameDisk'])			? trim(strip_tags(emoji($matches[$i]['nameDisk'])))						: null;
						$amazonNomDisque = !empty($amazonNomDisque)					? Normalizer::normalize($amazonNomDisque, Normalizer::FORM_C)			: null;
						$amazonNomDisque = (substr($amazonNomDisque, 0, 1) == ':')	? substr($amazonNomDisque, 1)											: $amazonNomDisque;

						$dataProductType = !empty($matches[$i]['dataProductType'])	? secuChars(clean($matches[$i]['dataProductType']))						: null;

						$dataCondition = !empty($matches[$i]['dataCondition'])		? secuChars(mb_strtolower(clean($matches[$i]['dataCondition'])))		: null;
						$dataArray = ['new' => 'Neuf', 'used' => 'Occasion'];
						$condition = !empty($dataCondition)							? ucfirst(strtr($dataCondition, $dataArray))							: null;

						$dataCapacity = !empty($matches[$i]['dataCapacity'])		? secuChars(clean($matches[$i]['dataCapacity']))						: null;

						$prixParTb = !empty($matches[$i]['pricePerTb'])				? secuChars(retirerVirguelEtPoint(clean($matches[$i]['pricePerTb'])))	: null;
						$prixParGb = !empty($matches[$i]['pricePerGb'])				? secuChars(retirerVirguelEtPoint(clean($matches[$i]['pricePerGb'])))	: null;
						$prix = !empty($matches[$i]['price'])						? secuChars(clean($matches[$i]['price']))								: null;

						$capacite = !empty($matches[$i]['capacity'])				? mb_strtolower(clean($matches[$i]['capacity']))						: null;
						$capaciteArray = [
							'mb' => ' Mo',
							'gbx' => ' Go x ',
							'gb' => ' Go',
							'tb' => ' To'
						];
						$capacite = !empty($capacite)								? strtr($capacite, $capaciteArray)										: null;
						$capacite = !empty($capacite)								? secuChars($capacite)													: null;

						$garantie = !empty($matches[$i]['warranty'])				? mb_strtolower(clean($matches[$i]['warranty']))						: null;
						$garantieArray = ['1year' => '1 an', 'months' => ' mois', 'years' => ' ans'];
						$garantie = !empty($garantie)								? ucfirst(strtr($garantie, $garantieArray))								: null;
						$garantie = !empty($garantie)								? secuChars($garantie)													: '<i class="fa-solid fa-x text-danger"></i>';

						$format = !empty($matches[$i]['format'])					? mb_strtolower(clean($matches[$i]['format']))							: null;
						$formatArray = [
							'internal3.5"' => 'Interne 3.5"',
							'internal' => 'Interne ',
							'external' => 'Externe ',
							'usb' => 'USB',
							'sd' => 'SD',
							'microsd' => 'microSD',
							'cfast' => 'CFast',
							'cfexpress' => 'CFexpress',
							'optical' => 'Optique',
							'tape' => 'Cartouche'
						];
						$format = !empty($format)									? ucfirst(strtr($format, $formatArray))									: null;
						$format = !empty($format)									? secuChars($format)													: null;

						$technologie = !empty($matches[$i]['technology'])			? secuChars(clean($matches[$i]['technology']))							: null;

						$donneesFichier[] = '<tr class="disk" data-product-type="'.$dataProductType.'" data-condition="'.$dataCondition.'" data-capacity="'.$dataCapacity.'"><td class="price-per-gb d-none">'.$prixParGb.'</td><td class="price-per-tb">'.$prixParTb.'</td><td>'.$prix.'</td><td>'.$capacite.'</td><td>'.$garantie.'</td><td>'.$format.'</td><td>'.$technologie.'</td><td>'.$condition.'</td><td class="name text-start"><a href="https://www.amazon.'.pays($locale).'/dp/'.$amazonDp.'/?tag=diskigo-21&linkCode=ur2">'.$amazonNomDisque.'</a></td></tr>';

						/*
							CREATE TABLE `disques` (
							`id` int(10) NOT NULL AUTO_INCREMENT,
							`locale` char(2) NOT NULL,
							`locale_pays` varchar(255) NOT NULL,
							`data_product_type` varchar(255) NOT NULL,
							`data_condition` varchar(255) NOT NULL,
							`data_capacity` varchar(255) NOT NULL,
							`prix_par_gb` varchar(255) DEFAULT NULL,
							`prix_par_tb` varchar(255) DEFAULT NULL,
							`prix` varchar(255) DEFAULT NULL,
							`capacite` varchar(255) DEFAULT NULL,
							`garantie` varchar(255) DEFAULT NULL,
							`format_disque` varchar(255) DEFAULT NULL,
							`technologie` varchar(255) DEFAULT NULL,
							`condition_disque` varchar(255) DEFAULT NULL,
							`amazon` varchar(255) NOT NULL,
							`amazon_nom` text NOT NULL,
							PRIMARY KEY (`id`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

							$donnees = [
								'locale' => $locale,
								'locale_pays' => pays($locale),
								'data_product_type' => $dataProductType,
								'data_condition' => $dataCondition,
								'data_capacity' => $dataCapacity,
								'prix_par_gb' => $prixParGb,
								'prix_par_tb' => $prixParTb,
								'prix' => $prix,
								'capacite' => $capacite,
								'garantie' => $garantie,
								'format_disque' => $format,
								'technologie' => $technologie,
								'condition_disque' => $condition,
								'amazon' => $amazonDp,
								'amazon_nom' => $amazonNomDisque
							];

							try {
								$stmt = $pdo->prepare('INSERT INTO disques (locale, locale_pays, data_product_type, data_condition, data_capacity, prix_par_gb, prix_par_tb, prix, capacite, garantie, format_disque, technologie, condition_disque, amazon, amazon_nom)
								VALUES (:locale, :locale_pays, :data_product_type, :data_condition, :data_capacity, :prix_par_gb, :prix_par_tb, :prix, :capacite, :garantie, :format_disque, :technologie, :condition_disque, :amazon, :amazon_nom)');
								$stmt->execute($donnees);
							} catch (\PDOException $e) { }
						*/
					}
				}

				if(!empty($donneesFichier))
				{
					$file = fopen('donnees/'.$locale.'.txt', 'w+');
					fwrite($file, implode($donneesFichier));
					fclose($file);
				}

				setFlash('success', 'Mise à jour de la langue <span class="fw-bold">'.$locale.'</span> avec succès');

				header('Location: /?locale='.$locale);
				exit;
			}

			else
			{
				setFlash('danger', 'Les données pour la langue <span class="fw-bold">'.$locale.'</span> sont introuvables');

				header('Location: /maj');
				exit;
			}
		}

		else
		{
			setFlash('danger', 'Le fichier pour la langue <span class="fw-bold">'.$locale.'</span> est introuvable');

			header('Location: /maj');
			exit;
		}
	}

	else
	{
		setFlash('danger', 'La langue <span class="fw-bold">'.secuChars($_GET['locale']).'</span> est introuvable');

		header('Location: /maj');
		exit;
	}
}

else
{
require_once 'a_body.php';
?>
<div class="container mt-5">
	<h1 class="mb-5 text-center"><a href="https://www.diskigo.com/">Diskigo.com</a> > <a href="/maj">Mise à jour</a></h1>

	<?= ((isset($_GET['succes']) AND !empty($_GET['locale'])) ? alerte('success', 'La langue <span class="fw-bold">'.$locale.'</span> a été mise à jour') : null); ?>
	<?= ((isset($_GET['erreur']) AND !empty($_GET['locale'])) ? alerte('error', 'Erreur lors de la récupération des données') : null); ?>
	<?= getFlash(); ?>

	<div class="container">
		<div class="row mb-5">
			<div class="col-12 col-lg-8 mx-auto">
				<h4 class="text-center mb-4 mt-3"><a href="/blog/wp-admin/" <?= $onclick; ?>><span class="btn btn-primary col-2">Blog</span></a></h4>

				<?php
				foreach($languesMajArray as $cleLangue => $valeurLangue)
				{
					$cheminFichier	= (is_file($_SERVER['DOCUMENT_ROOT'].'donnees/'.$cleLangue.'.txt') AND filesize($_SERVER['DOCUMENT_ROOT'].'donnees/'.$cleLangue.'.txt') > 0) ? $_SERVER['DOCUMENT_ROOT'].'donnees/'.$cleLangue.'.txt' : null;
					$fichier		= !empty($cheminFichier)	? explode('/', $cheminFichier)[1]			: null;
					$fichierDate	= !empty($cheminFichier)	? filemtime($cheminFichier)					: 0;
					$fichierTaille	= !empty($cheminFichier)	? taille(filesize($cheminFichier))			: 0;

					$uniqid = idAleatoire();

					if((time() - $fichierDate) > (60 * 60 * 24 * 14))		$clr = 'border-4 border-dark';
					elseif((time() - $fichierDate) > (60 * 60 * 24 * 7))	$clr = 'border-3 border-danger';
					else													$clr = 'border-2 border-gray';

					echo '<div class="row py-2 border '.$clr.' mb-3 py-2 rounded" id="'.$uniqid.'">
						<div class="col-4 m-auto text-center fs-4" title="Diskprices : '.$valeurLangue.'"><a href="https://diskprices.com/?locale='.$cleLangue.'" class="btn btn-outline-danger" onclick="hide(\'#'.$uniqid.'\');"><i class="fa-solid fa-repeat"></i> <span class="mx-1">'.isoEmoji($cleLangue).'</span> Diskprices</a></div>
						<div class="col-4 m-auto text-center fs-4" title="Diskigo : MàJ '.$valeurLangue.'"><a href="/maj?locale='.$cleLangue.'" class="btn btn-outline-success" onclick="hide(\'#'.$uniqid.'\');"><i class="fa-solid fa-repeat"></i> <span class="mx-1">'.isoEmoji($cleLangue).'</span> Diskigo</a></div>
						<div class="col-4 m-auto text-center">
							'.(!empty($cheminFichier) ? '<span style="padding: .5rem .50rem; border-radius: .3rem !important; font-size: .90rem;" class="badge bg-primary rounded-pill curseur" title="Fichier mis à jour '.temps($fichierDate).'">'.temps($fichierDate).'</span>'			: '<span class="badge rounded-pill bg-danger" title="Fichier inconnu">fichier inconnu</span>').'
							'.(!empty($fichierTaille) ? '<span style="padding: .5rem .50rem; border-radius: .3rem !important; font-size: .90rem;" class="badge bg-primary rounded-pill curseur" title="Taille du fichier « '.$fichier.' » : '.$fichierTaille.'">'.$fichierTaille.'</span>'	: '<span class="badge rounded-pill bg-danger" title="Taille du fichier inconnue">taille inconnue</span>').'
						</div>
					</div>';
				}

			echo '</div>
		</div>
	</div>
</div>';

require_once 'a_footer.php';
}