<?php
$parPage = (int) 40;
$maxXkcd = (int) 3163; // https://xkcd.com/ https://thisip.pw/xkcd
$page = isset($_GET['page']) ? (($_GET['page'] >= 1 AND $_GET['page'] <= 1000) ? (int) $_GET['page'] : 1) : 1;

$xkcdGlog = glob('assets/cache/xkcd/*.json');
$xkcdTotal = count($xkcdGlog);

if(isset($_GET['load']))
{
	header('Content-Type: application/json; charset=utf-8');

	require_once '../config/config.php';

	$n = (int) (!empty($_GET['id']) AND $_GET['id'] >= 1 AND $_GET['id'] <= $maxXkcd) ? secu($_GET['id']) : mt_rand(1, $maxXkcd);
	$n = ($n === 404) ? ($n - 1) : $n;
	$n = ($n === 1608) ? ($n - 1) : $n;
	$n = ($n === 1663) ? ($n - 1) : $n;

	$localXkcd = $_SERVER['DOCUMENT_ROOT'].'assets/cache/xkcd/'.$n.'.json';

	!is_file($localXkcd) ? telecharger('https://xkcd.com/'.$n.'/info.0.json', $localXkcd) : null;
	$xkcd = json_decode(file_get_contents($localXkcd), true);

	$imgLocal = 'assets/cache/xkcd/'.$n.'.'.pathinfo($xkcd['img'])['extension'];
	!is_file($imgLocal) ? telecharger($xkcd['img'], $imgLocal) : null;

	$num = secu($xkcd['num']);

	$imgLocal = 'assets/cache/xkcd/'.$num.'.'.getExt($xkcd['img']);

	$title = !empty($xkcd['title']) ? secuChars($xkcd['title']) : 'inconnu';
	$safeTitle = !empty($xkcd['safe_title']) ? secuChars($xkcd['safe_title']) : 'inconnu';
	$alt = secuChars(str_replace('"', '', $xkcd['alt']));

	$day = !empty($xkcd['day']) ? secuChars($xkcd['day']) : null;
	$month = !empty($xkcd['month']) ? secuChars($xkcd['month']) : null;
	$year = !empty($xkcd['year']) ? secuChars($xkcd['year']) : null;

	$dateXkcd = DateTime::createFromFormat('d m Y', $day.' '.$month.' '.$year);
	$date = !empty($dateXkcd) ? $dateXkcd->getTimestamp() : strtotime('01 01 2025');
	$transcript = !empty($xkcd['transcript']) ? secuChars($xkcd['transcript']) : null;

	echo json_encode(
	[
		'id' => $n,
		'idMaximum' => $maxXkcd,
		'box' => '<div class="card">
			<a href="https://thisip.pw/'.$imgLocal.'" class="mx-auto mt-3" data-fancybox="gallerie">
				<img src="/'.$imgLocal.'" class="card-img-top img-fluid px-2 px-sm-0" alt="Comic xkcd N°'.$num.'" title="'.$alt.'">
			</a>

			<div class="card-footer border-0 bg-transparent mt-3">
				<div class="float-start">
					<h1 class="card-title fs-4">'.$title.'</h1>
					<p class="mb-0" title="Comic publié le '.dateFormat($date).'"><time datetime="'.date(DATE_ATOM, $date).'">'.temps($date).'</time></p>
				</div>
				<div class="float-end">
					<p class="text-end"><a href="https://explainxkcd.com/wiki/index.php/'.$n.'" title="Explications du comic n°'.$n.' sur explainxkcd.com" '.$onclick.'>Explications</a> <span class="curseur" title="Site Anglais">🇺🇸</span></p>
					<p class="mb-0">Original sur <a href="https://xkcd.com/'.$n.'/" '.$onclick.'>xkcd.com</a></p>
				</div>
			</div>
			'.(!empty($transcript) ? '<!-- Transcript : '.$transcript.' -->' : null).'
		</div>'
	]);
}

elseif(isset($_GET['loadXkcd']) AND !empty($_GET['page']))
{
	require_once '../config/config.php';

	header('Content-Type: application/json; charset=utf-8');

	$start = ($page > 1 ? (($parPage * $page) - ($parPage + 1)) : 0);
	$end = ($page <= 1 ? ($parPage * $page) : (($parPage * $page) - 1));

	foreach($xkcdGlog as $c => $v)
	{
		$nouveauGlob[] = str_replace('assets/cache/xkcd/', '', $v);
	}

	isset($_GET['inverser']) ? sort($nouveauGlob, SORT_NUMERIC) : rsort($nouveauGlob, SORT_NUMERIC);
	$xkcdResultats = $nouveauGlob;

	for($i = $start; $i < $end; $i++)
	{
		$json = 'assets/cache/xkcd/'.$xkcdResultats[$i];

		if(isset($json) AND is_file($json))
		{
			$xkcd = json_decode(file_get_contents($json), true);

			if(!empty($xkcd))
			{
				$num = secu($xkcd['num']);

				$imgLocal = 'assets/cache/xkcd/'.$num.'.'.getExt($xkcd['img']);

				$title = !empty($xkcd['title']) ? secuChars($xkcd['title']) : 'inconnu';
				$safeTitle = !empty($xkcd['safe_title']) ? secuChars($xkcd['safe_title']) : 'inconnu';
				$alt = secuChars(str_replace('"', '', $xkcd['alt']));

				$day = !empty($xkcd['day']) ? secuChars($xkcd['day']) : null;
				$month = !empty($xkcd['month']) ? secuChars($xkcd['month']) : null;
				$year = !empty($xkcd['year']) ? secuChars($xkcd['year']) : null;

				$dateXkcd = DateTime::createFromFormat('d m Y', $day.' '.$month.' '.$year);
				$date = !empty($dateXkcd) ? $dateXkcd->getTimestamp() : strtotime('01 01 2025');
				$transcript = !empty($xkcd['transcript']) ? secuChars($xkcd['transcript']) : null;

				$xkcdJson[] = '<div class="card h-100">
					<a href="https://thisip.pw/'.$imgLocal.'" class="mx-auto mt-3 text-center" data-fancybox="gallerie">
						<img src="/'.$imgLocal.'" style="height: 150px; width: 90%;" class="card-img-top img-fluid px-2 px-sm-0" alt="Comic xkcd N°'.$num.'" title="'.$alt.'">
					</a>

					<div class="card-body"><h5 class="card-title text-center">'.$safeTitle.'</h5></div>

					<div style="font-size: .95rem;" class="card-footer text-truncate text-center">
						<span title="Comic publié le '.dateFormat($date).'"><time datetime="'.date(DATE_ATOM, $date).'">'.temps($date).'</time></span>
						<span class="mx-2">|</span>
						<a href="https://xkcd.com/'.$num.'/" '.$onclick.'>xkcd</a>
						<span class="mx-2">|</span>
						<a href="https://explainxkcd.com/wiki/index.php/'.$num.'" title="Explications du comic n°'.$num.' sur explainxkcd.com" '.$onclick.'><i class="fa-solid fa-comments"></i></a>
					</div>
					'.(!empty($transcript) ? '<!-- Transcript : '.$transcript.' -->' : null).'
				</div>';
			}
		}
	}

	echo (isset($xkcdJson) AND !empty($xkcdJson)) ? json_encode($xkcdJson) : null;
}

else
{
	require_once 'a_body.php';
	?>
	<div class="border rounded mb-4" id="xkcd">
		<h1 class="mb-5 text-center"><a href="/xkcd"><i class="fa-regular fa-comment-dots"></i> xkcd</a></h1>
		<div class="container text-center">
			<div class="p-0" id="resultatXkcd"></div>

			<div class="row justify-content-center my-5 p-0">
				<div class="order-first		order-lg-first	col-4 col-lg-2 my-1"><a href="#xkcd" class="btn btn-xkcd btn-outline-primary" id="btnPrecedent" title="Comic précédent"><i class="fa-solid fa-angles-left"></i><span class="d-none d-lg-inline"> Précédent</span></a></div>
				<div class="order-2			order-lg-1		col-4 col-lg-2 my-1"><a href="#xkcd" class="btn btn-xkcd btn-outline-warning" id="btnAleatoire" title="Comic aléatoire">Aléatoire</a></div>
				<div class="order-4			order-lg-2		col-4 col-lg-2 my-1"><a href="#xkcd" class="btn btn-xkcd btn-outline-info" id="btnPremier" title="Premier comic">Le premier</a></div>
				<div class="order-5			order-lg-3		col-4 col-lg-2 my-1"><a href="#xkcd" class="btn btn-xkcd btn-outline-info" id="btnDernier" title="Dernier comic">Le dernier</a></div>
				<div class="order-last		order-lg-4		col-4 col-lg-2 my-1"><input value="" type="number" minlength="1" maxlength="4" pattern="[0-9]+" class="form-control mx-auto" id="xkcdIdInput" placeholder="ID xkcd"></div>
				<div class="order-3			order-lg-last	col-4 col-lg-2 my-1"><a href="#xkcd" class="btn btn-xkcd btn-outline-primary" id="btnSuivant" title="Comic suivant"><span class="d-none d-lg-inline">Suivant </span><i class="fa-solid fa-angles-right"></i></a></div>
			</div>
		</div>

		<hr class="mb-5 mt-0">

		<div id="derniers-xkcd">
			<div class="container mb-4 p-0">
				<div class="row p-0">
					<div class="col-6">
						<div class="btn-group btn-group" role="group">
							<a href="/xkcd#derniers-xkcd" class="btn btn-outline-primary" data-bs-toggle="tooltip" data-bs-title="Trier les dessins du plus récent au plus ancien">
								<span class="d-block d-md-none"><i class="fa-solid fa-arrow-up"></i></span>
								<span class="d-none d-md-block">Plus récent au plus ancien</span>
							</a>
							<a href="/xkcd?inverser#derniers-xkcd" class="btn btn-outline-primary" data-bs-toggle="tooltip" data-bs-title="Trier les dessins du plus ancien au plus récent">
								<span class="d-block d-md-none"><i class="fa-solid fa-arrow-down"></i></span>
								<span class="d-none d-md-block">Plus ancien au plus récent</span>
							</a>
						</div>
					</div>

					<div class="col-6 text-end"><span class="fs-4"><?= $xkcdTotal; ?> dessins</span></div>
				</div>
			</div>

			<div class="card-group" id="xkcd-chargement"></div>
		</div>
	</div>


		<?php
		if(!empty($_GET['id']) AND strlen($_GET['id'] <= 4))
			$id = (int) secu($_GET['id']);
		?>

	<script>
	document.addEventListener('DOMContentLoaded', function () {
		let xkcdActuelId = null;

		const url = '/xkcd';
		const spinner = '<img src="/assets/img/chargement.svg" style="width: 100px;" class="d-flex mx-auto" alt="Chargement…" title="Chargement…">';
		const resultatElement = document.querySelector('#resultatXkcd');
		const idInput = document.querySelector('#xkcdIdInput');
		const listeNoire = [404, 1663];

		function getNextValidId(id, direction) {
			let nextId = id;
			do {
				nextId += direction;
			} while (listeNoire.includes(nextId));
			return nextId;
		}

		function loadContent(id) {
			resultatElement.innerHTML = spinner;

			const fetchUrl = (id !== null) ? `${url}?load&id=${id}` : `${url}?load&action=aleatoire`;

			fetch(fetchUrl, { cache: 'no-store' })
				.then(response => response.json())
				.then(data => {
					xkcdActuelId = data.id;
					displayXkcd(data);
				})
				.catch(error => console.error('Erreur de chargement : ', error));
		}

		function displayXkcd(data) {
			const parser = new DOMParser();
			const parsedHTML = parser.parseFromString(data.box, "text/html").body.firstChild;

			resultatElement.innerHTML = ""; // Vide le conteneur avant d'ajouter le nouvel élément
			resultatElement.appendChild(parsedHTML);

			if (idInput) {
				idInput.value = xkcdActuelId;
			}
		}

		idInput.addEventListener('keydown', function (event) {
			if (event.key === 'Enter') {
				const inputId = parseInt(idInput.value, 10);
				if (!isNaN(inputId)) {
					loadContent(inputId);
				}
			}
		});

		document.querySelector('#btnPrecedent').addEventListener('click', function () { if (xkcdActuelId !== null) { loadContent(getNextValidId(xkcdActuelId, -1)); }});
		document.querySelector('#btnAleatoire').addEventListener('click', function () { loadContent(null); });
		document.querySelector('#btnPremier').addEventListener('click', function () { if (xkcdActuelId !== null) { loadContent(1); } });
		document.querySelector('#btnDernier').addEventListener('click', function () { if (xkcdActuelId !== null) { loadContent(<?= $maxXkcd; ?>); } });
		document.querySelector('#btnSuivant').addEventListener('click', function () { if (xkcdActuelId !== null) { loadContent(getNextValidId(xkcdActuelId, 1)); }});

		loadContent(null);
	});

	const contentXkcd = document.querySelector('#xkcd-chargement');
	if (contentXkcd) {
		document.addEventListener('DOMContentLoaded', function() {
			let loadingXkcd = false;
			let pageXkcd = 1;
			const maxPages = <?= round($xkcdTotal / $parPage); ?>;

			function loadMoreData() {
				if (loadingXkcd) return;
				loadingXkcd = true;

				fetch('/xkcd?loadXkcd&<?= (isset($_GET['inverser']) ? 'inverser&' : null); ?>page=' + pageXkcd)
					.then((response) => {
						if (!response.ok) {
							throw new Error('Erreur réseau');
						}

						return response.json();
					})
					.then((data) => {
						if (Array.isArray(data) && data.length > 0) {
							data.forEach((item) => {
								const listItemXkcd = document.createElement('div');
								listItemXkcd.classList.add('xkcd-item', 'col-12', 'col-lg-3', 'mx-auto', 'px-3', 'mb-3');
								listItemXkcd.innerHTML = item;

								contentXkcd.appendChild(listItemXkcd);
							});

							if (pageXkcd < maxPages) {
								pageXkcd += 1;
							} else {
								window.removeEventListener('scroll', checkScrollXkcd);
							}
						} else {
							window.removeEventListener('scroll', checkScrollXkcd);
						}

						loadingXkcd = false;
					})
					.catch(error => {
						console.error('Erreur de chargement : ', error);
						loadingXkcd = false;
						window.removeEventListener('scroll', checkScrollXkcd);
					});
			}

			function checkScrollXkcd() {
				if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
					loadMoreData();
				}
			}

			window.addEventListener('scroll', checkScrollXkcd);

			loadMoreData();
		});
	}
	</script>
	<?php
	require_once 'a_footer.php';
}