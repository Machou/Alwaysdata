<?php
require_once 'a_body.php';
?>
<div class="border rounded">
	<h1 class="mb-5 text-center"><a href="/changements"><i class="fa-solid fa-clipboard-list"></i> Changements</a></h1>

	<?php
	$log = [
		['version' => '1.14.4',	'date' => 'june 2025',			'desc' => 'EXIF : ajout de la miniature, changement des input, diverses améliorations',								'wm' => '',],
		['version' => '1.14.3',	'date' => 'april 2025',			'desc' => 'changement du bouton « Remonter la page »',																'wm' => '',],
		['version' => '1.14.2',	'date' => 'february 2025',		'desc' => 'réréarrangement de la page d’accueil',																	'wm' => '',],
		['version' => '1.14.1',	'date' => 'november 2024',		'desc' => 'fusion de la page « uaparser » vers la page d’accueil',													'wm' => '',],
		['version' => '1.14',	'date' => 'november 2024',		'desc' => 'ajout de la possibilité de changer de Police pour <a href="https://opendyslexic.org/">OpenDyslexic</a>',	'wm' => '',],
		['version' => '1.13',	'date' => 'august 2024',		'desc' => 'ajout du Whois pour les adresses IP v4 et v6',															'wm' => '',],
		['version' => '1.12.2',	'date' => 'august 2024',		'desc' => 'vérification des courriels renforcé',																	'wm' => '',],
		['version' => '1.12.1',	'date' => 'august 2024',		'desc' => 'accueil réorganisé',																						'wm' => 'https://web.archive.org/web/20240826181451/https://thisip.pw/',],
		['version' => '1.11',	'date' => 'july 2024',			'desc' => 'refonte du module « Whois »',																			'wm' => '',],
		['version' => '1.12',	'date' => 'july 2024',			'desc' => 'ajout du module « Réputation d’un Courriel »',															'wm' => '',],
		['version' => '1.10',	'date' => 'june 2024',			'desc' => 'ajout du module « EXIF »',																				'wm' => '',],
		['version' => '1.9',	'date' => 'may 2024',			'desc' => 'ajout du module « Whois »',																				'wm' => '',],
		['version' => '1.8',	'date' => 'may 2024',			'desc' => 'ajout de la partie « User Agent »',																		'wm' => '',],
		['version' => '1.7',	'date' => 'may 2024',			'desc' => 'ajout de la partie « Qu’est-ce qu’une adresse IP ? »',													'wm' => '',],
		['version' => '1.6',	'date' => 'april 2024',			'desc' => 'ajout du module « RSS Finder »',																			'wm' => '',],
		['version' => '1.5',	'date' => 'june 2023',			'desc' => 'ajout de la détection du <a href="https://github.com/fingerprintjs/fingerprintjs">FingerprintJS</a>',	'wm' => '',],
		['version' => '1.5.0',	'date' => 'june 2023',			'desc' => 'tutoriels comment modifier ses DNS sur un OS (Windows, Linux)',											'wm' => 'https://web.archive.org/web/20230607142008/https://thisip.pw/'],
		['version' => '1.4',	'date' => 'september 2022',		'desc' => 'ajout des DNS',																							'wm' => '',],
		['version' => '1.3',	'date' => 'september 2022',		'desc' => 'ajout du calcul des sous-réseau pour une IPv4 (CIDR)',													'wm' => 'https://web.archive.org/web/20220903234242/https://thisip.pw/'],
		['version' => '1.2',	'date' => 'august 2022',		'desc' => 'ajout des <a href="https://xkcd.com">Webcomics par Randall Munroe</a>',									'wm' => 'https://web.archive.org/web/20220804234219/https://thisip.pw/'],
		['version' => '1.1',	'date' => 'november 2021',		'desc' => 'ajout de la sortie Json des données d’une adresse IPv4',													'wm' => 'https://web.archive.org/web/20211107233903/https://thisip.pw/'],
		['version' => '1.0',	'date' => 'october 2021',		'desc' => 'sortie initiale',																						'wm' => 'https://web.archive.org/web/20211008233754/https://thisip.pw',],
	];
	?>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-lg-8 p-0">
				<p>Afficher l’histiorique complet de <strong>ThisIP</strong> sur <a href="https://web.archive.org/web/20240000000000*/https://thisip.pw/" class="badge text-bg-info text-white">Archive.org</a> (<a href="https://web.archive.org/web/20211008233754/https://thisip.pw/">octobre 2021</a> à aujourd’hui)</p>
				<div class="card">
					<div class="border-bottom"><p class="m-0 p-3 fw-bold">Dernières mises à jour de ThisIP</p></div>
					<div class="card-body p-3">
						<div class="timeline timeline-one-side">
							<?php
							foreach($log as $c => $v)
							{
								$date = strtotime($v['date']);
								$i = '<i class="fa-solid fa-globe"></i>';

								echo '<div class="timeline-block mb-3">
									<span class="timeline-step">'.(!empty($v['wm']) ? '<a href="'.$v['wm'].'" data-bs-toggle="tooltip" data-bs-title="Accèder à la machine à remonter le temps de l’intenet en date de '.dateFormat($date, 'm').'" '.$onclick.'>'.$i.'</a>' : $i).'</span>
									<div class="timeline-content">
										<h6 class="fw-bold mb-0"><span class="badge text-bg-info text-white curseur" data-bs-toggle="tooltip" data-bs-title="Version : '.$v['version'].'">'.$v['version'].'</span> - <time datetime="'.date(DATE_ATOM, $date).'">'.dateFormat($date, 'm').'</time></h6>
										<p class="mt-3">'.$v['desc'].'</p>
									</div>
								</div>';
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
require_once 'a_footer.php';