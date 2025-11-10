<?php
require_once '../config/diskigo_config.php';
require_once 'a_body.php';
?>

<header class="mb-5" id="header">
	<div class="container-fluid">
		<h1><a href="https://www.diskigo.com/">Diskigo.com</a></h1>
		<p>Si votre ordinateur présente des contraintes d'espace de stockage, si la libération de mémoire s'avère difficile, si vous êtes simplement à court d'espace, ou si vous assemblez votre propre PC, alors l'acquisition d'un <strong>disque dur interne</strong> ou un <strong>disque dur externe</strong> s'impose ! Sur <a href="https://www.diskigo.com/">diskigo.com</a>, vous découvrirez un large éventail de références adaptées à divers besoins. Que vous recherchiez une option abordable avec un excellent rapport qualité-prix, une capacité importante, ou une fiabilité maximale, notre catalogue répond à toutes les exigences. Grâce aux filtres disponibles sur <a href="https://www.diskigo.com/">diskigo.com</a>, il vous sera aisé de sélectionner le disque dur interne, externe, clé usb, etc. qui correspond à vos critères en termes de capacité, de format et de connectivité (SATA, USB, etc.).</p>

		<p>[ <a href="https://www.diskigo.com/">Accueil</a> ] [ <a href="https://www.diskigo.com/blog/">Blog</a> ]</p>
		<div class="d-flex flex-wrap justify-content-center gap-1 mb-3" id="marques">
			<?php
			$m[] = '<strong>Marques</strong> : <span>[ '.(!empty($recherche) ? '<a href="https://www.diskigo.com/'.$locale.'" data-bs-toggle="tooltip" data-bs-title=" marques">Toutes</a>' : 'Toutes').' ]</span>';

			foreach($marques as $marque)
				$m[] = '<span class="text-center">'.($marque !== $recherche ? '[ <a href="https://www.diskigo.com/'.$locale.'/'.urlencode($marque).'#marques">'.$marque.'</a> ]' : '[ '.$marque.' ]').'</span>';

			echo implode($m);
			?>
		</div>
	</div>
</header>

<main>
	<div class="container-fluid">
		<?php
		$flash = getFlash();
		echo !empty($flash) ? '<div class="row"><div class="col-12 col-lg-6 mx-auto">'.$flash.'</div></div>' : null;
		echo (!empty($_SERVER['HTTP_REFERER']) AND $_SERVER['HTTP_REFERER'] === 'https://www.diskigo.com/maj' AND !empty($_GET['locale'])) ? redirection('https://www.diskigo.com/'.$_GET['locale'], 5000) : null;
		?>

		<div class="row">
			<div class="col-12 col-lg-1" id="filtres">
				<fieldset id="locales">
					<legend>Source <strong>Amazon</strong></legend>
					<ul>
						<li title="Amazon États-unis"><a href="/us"<?= ($locale == 'us') ? ' class="active"' : null; ?>>🇺🇸 amazon.com</a></li>
						<li title="Amazon Angleterre"><a href="/uk"<?= ($locale == 'uk') ? ' class="active"' : null; ?>>🇬🇧 amazon.co.uk</a></li>
						<li title="Amazon Allemagne"><a href="/de"<?= ($locale == 'de') ? ' class="active"' : null; ?>>🇩🇪 amazon.de</a></li>
						<li title="Amazon Espagne"><a href="/es"<?= ($locale == 'es') ? ' class="active"' : null; ?>>🇪🇸 amazon.es</a></li>
						<li title="Amazon France"><a href="/fr"<?= ($locale == 'fr') ? ' class="active"' : null; ?>>🇫🇷 amazon.fr</a></li>
						<li title="Amazon Italie"><a href="/it"<?= ($locale == 'it') ? ' class="active"' : null; ?>>🇮🇹 amazon.it</a></li>
						<li title="Amazon Canada"><a href="/ca"<?= ($locale == 'ca') ? ' class="active"' : null; ?>>🇨🇦 amazon.ca</a></li>
						<li title="Amazon Australie"><a href="/au"<?= ($locale == 'au') ? ' class="active"' : null; ?>>🇦🇺 amazon.com.au</a></li>
						<li title="Amazon Inde"><a href="/in"<?= ($locale == 'in') ? ' class="active"' : null; ?>>🇮🇳 amazon.in</a></li>
						<li title="Amazon Suède"><a href="/se"<?= ($locale == 'se') ? ' class="active"' : null; ?>>🇸🇪 amazon.se</a></li>
						<li title="Amazon Irlande"><a href="/ie"<?= ($locale == 'ie') ? ' class="active"' : null; ?>>🇮🇪 amazon.ie</a></li>
					</ul>
				</fieldset>
				<fieldset class="condition">
					<legend>Condition</legend>
					<div class="form-check">
						<input type="checkbox" class="form-check-input" id="checkNeuf" data-condition="new" checked>
						<label class="form-check-label" for="checkNeuf">Neuf</label>
					</div>
					<div class="form-check">
						<input type="checkbox" class="form-check-input" id="checkOccasion" data-condition="used" checked>
						<label class="form-check-label" for="checkOccasion">Occasion</label>
					</div>
				</fieldset>
				<fieldset class="units">
					<legend>Prix par</legend>
					<div class="form-check">
						<input type="radio" name="units" class="form-check-input" id="radioPrixParTo" data-units="tb" checked>
						<label class="form-check-label" for="radioPrixParTo">To (Tera)</label>
					</div>
					<div class="form-check">
						<input type="radio" name="units" class="form-check-input" id="radioPrixParGo" data-units="gb">
						<label class="form-check-label" for="radioPrixParGo">Go (Giga)</label>
					</div>
				</fieldset>
				<fieldset class="capacity">
					<legend>Capacité</legend>
					<div class="row mb-2 ms-1">
						<input type="number" class="form-control form-control-sm" id="capacity_min" placeholder="To" min="0" max="24">
						<label for="capacity_min" class="col-form-label col-form-label-sm">Minimum</label>
					</div>
					<div class="row ms-1">
						<input type="number" class="form-control form-control-sm" id="capacity_max" placeholder="To" min="0" max="24">
						<label for="capacity_max" class="col-form-label col-form-label-sm">Maximum</label>
					</div>
				</fieldset>
				<fieldset class="category">
					<legend><label><input type="checkbox" data-category="hdd" checked>Type de disque dur</label></legend>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeExterne312" data-category="hdd" data-product-type="external_hdd" checked>
						<label class="form-check-label" for="checkTypeExterne312">Externe 3" 1/2</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeExterne212" data-category="hdd" data-product-type="external_hdd25" checked>
						<label class="form-check-label" for="checkTypeExterne212">Externe 2" 1/2</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeInterne312" data-category="hdd" data-product-type="internal_hdd" checked>
						<label class="form-check-label" for="checkTypeInterne312">Interne 3" 1/2</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeInterne212" data-category="hdd" data-product-type="internal_hdd25" checked>
						<label class="form-check-label" for="checkTypeInterne212">Interne 2" 1/2</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeInterneHybrid" data-category="hdd" data-product-type="internal_sshd" checked>
						<label class="form-check-label" for="checkTypeInterneHybrid">Interne Hybrid</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeInterneSAS" data-category="hdd" data-product-type="internal_sas" checked>
						<label class="form-check-label" for="checkTypeInterneSAS">Interne SAS</label>
					</div>
				</fieldset>
				<fieldset class="category">
					<legend><label><input type="checkbox" data-category="ssd" checked>Type de SSD</label></legend>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeSSDExterne" data-category="ssd" data-product-type="external_ssd" checked>
						<label class="form-check-label" for="checkTypeSSDExterne">SSD Externe</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeSSDInterne" data-category="ssd" data-product-type="internal_ssd" checked>
						<label class="form-check-label" for="checkTypeSSDInterne">SSD Interne</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeM2SATA" data-category="ssd" data-product-type="m2_ssd" checked>
						<label class="form-check-label" for="checkTypeM2SATA">M.2 SATA</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeM2NVMe" data-category="ssd" data-product-type="m2_nvme" checked>
						<label class="form-check-label" for="checkTypeM2NVMe">M.2 NVMe</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeU2U3" data-category="ssd" data-product-type="u2" checked>
						<label class="form-check-label" for="checkTypeU2U3">U.2 / u.3</label>
					</div>
				</fieldset>
				<fieldset class="category">
					<legend><label><input type="checkbox" data-category="removable">Amovibles</label></legend>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypemicroSDFlash" data-category="removable" data-product-type="microsd">
						<label class="form-check-label" for="checkTypemicroSDFlash">microSD Flash</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeSDFlash" data-category="removable" data-product-type="sd_card">
						<label class="form-check-label" for="checkTypeSDFlash">SD Flash</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeCompactFlash" data-category="removable" data-product-type="cf_card">
						<label class="form-check-label" for="checkTypeCompactFlash">Compact Flash</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeCFastFlash" data-category="removable" data-product-type="cfast_card">
						<label class="form-check-label" for="checkTypeCFastFlash">CFast Flash</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeCFexpressFlash" data-category="removable" data-product-type="cfexpress">
						<label class="form-check-label" for="checkTypeCFexpressFlash">CFexpress Flash</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeUSBFlash" data-category="removable" data-product-type="usb_flash">
						<label class="form-check-label" for="checkTypeUSBFlash">USB Flash</label>
					</div>
				</fieldset>
				<fieldset class="category">
					<legend><label><input type="checkbox" data-category="optical">Optiques</label></legend>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeBDRE" data-category="optical" data-product-type="bdrw">
						<label class="form-check-label" for="checkTypeBDRE">BD-RE</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeBDR" data-category="optical" data-product-type="bdr">
						<label class="form-check-label" for="checkTypeBDR">BD-R</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeDVDRW" data-category="optical" data-product-type="dvdrw">
						<label class="form-check-label" for="checkTypeDVDRW">DVD-RW</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeDVDR" data-category="optical" data-product-type="dvdr">
						<label class="form-check-label" for="checkTypeDVDR">DVD-R</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeCDRW" data-category="optical" data-product-type="cdrw">
						<label class="form-check-label" for="checkTypeCDRW">CD-RW</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeCDR" data-category="optical" data-product-type="cdr">
						<label class="form-check-label" for="checkTypeCDR">CD-R</label>
					</div>
				</fieldset>
				<fieldset class="category">
					<legend><label><input type="checkbox" data-category="tape"><abbr title="Bande magnétique">Cartouches</abbr></label></legend>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeLTO3" data-category="tape" data-product-type="lto3">
						<label class="form-check-label" for="checkTypeLTO3">LTO-3 (80 Mo/s)</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeLTO4" data-category="tape" data-product-type="lto4">
						<label class="form-check-label" for="checkTypeLTO4">LTO-4 (120 Mo/s)</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeLTO5" data-category="tape" data-product-type="lto5">
						<label class="form-check-label" for="checkTypeLTO5">LTO-5 (140 Mo/s)</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeLTO6" data-category="tape" data-product-type="lto6">
						<label class="form-check-label" for="checkTypeLTO6">LTO-6 (160 Mo/s)</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeLTO7" data-category="tape" data-product-type="lto7">
						<label class="form-check-label" for="checkTypeLTO7">LTO-7 (300 Mo/s)</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeLTO8" data-category="tape" data-product-type="lto8">
						<label class="form-check-label" for="checkTypeLTO8">LTO-8 (360 Mo/s)</label>
					</div>
					<div class="form-check product_type">
						<input type="checkbox" class="form-check-input filter" id="checkTypeLTO9" data-category="tape" data-product-type="lto9">
						<label class="form-check-label" for="checkTypeLTO9">LTO-9 (440 Mo/s)</label>
					</div>
				</fieldset>
			</div>
			<?php
			echo '<div class="col-12 col-lg-11">
				<table class="table table-hover table-striped table-bordered" id="tarifstera"
					data-toggle="table"
					data-search="true"
					'.(!empty($recherche) ? 'data-search-text="'.$recherche.'"' : null).'
				>
					<thead>
						<tr id="tarifstera-head">
							<th		class="price-per-gb d-none"								data-field="prix-par-gb"	>Prix par Go</th>
							<th		class="price-per-tb"									data-field="prix-par-tb"	>Prix par To</th>
							<th																data-field="prix"			>Prix</th>
							<th																data-field="capacite"		>Capacité</th>
							<th																data-field="garantie"		>Garantie</th>
							<th																data-field="format"			>Format</th>
							<th																data-field="technologie"	>Technologie</th>
							<th																data-field="condition"		>Condition</th>
							<th	data-search-highlight-formatter="customSearchFormatter"		data-field="nom"			>Nom</th>
						</tr>
					</thead>
					<tbody id="tarifstera-body" lang="'.$locale.'">

						'.((is_file('donnees/'.$locale.'.txt') AND filesize('donnees/'.$locale.'.txt') > 10000) ? file_get_contents('donnees/'.$locale.'.txt') : null).'

					</tbody>
				</table>
			</div>
		</div>';
		?>

		<div class="mt-4" id="marquesFooter">
			<?php
			echo implode($m);
			?>
		</div>
	</div>
</main>
<?php
require_once 'a_footer.php';