<?php
require_once '../config/wow_config.php';

if(isset($_GET['prix']))
{
	header('Content-Type: application/json; charset=utf-8');

	if(empty($_GET['periode']))
	{
		$tz = new DateTimeZone('Europe/Paris');
		$aujourdhui = (new DateTimeImmutable('now', $tz))->setTime(0, 0, 0);
		$minJour = $aujourdhui->sub(new DateInterval('P29D'));
		$maxJour = $aujourdhui;

		$stmt = $pdo->prepare('SELECT * FROM wow_prix_jeton WHERE date_jour BETWEEN :min_day AND :max_day ORDER BY date_jour ASC');
		$stmt->execute([
			':min_day' => (string) $minJour->format('Y-m-d'),
			':max_day' => (string) $maxJour->format('Y-m-d')
		]);
		$res = $stmt->fetchAll();

		$map = [];
		foreach($res as $r)
		{
			$d = (new DateTimeImmutable($r['date_jour'], $tz))->format('Y-m-d');
			$map[$d] = (!empty($r['prix']) AND is_numeric($r['prix'])) ? (int)$r['prix'] : null;
		}

		$x = [];
		$y = [];

		$cursor = $minJour;
		while($cursor <= $maxJour)
		{
			$d = $cursor->format('Y-m-d');
			$x[] = $d;
			$y[] = array_key_exists($d, $map) ? $map[$d] : null;
			$cursor = $cursor->add(new DateInterval('P1D'));
		}

		echo json_encode(['x' => $x, 'y' => $y], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}

	elseif(!empty($_GET['periode']))
	{
		$periode = trim($_GET['periode'] ?? '30j');
		$periodeAutorisee = ['24h', '7j', '30j', '6m', '12m', '24m', 't'];
		if(!in_array($periode, $periodeAutorisee, true)) {
			setFlash('danger', 'Période non autorisée');

			header('Location: /historique-des-prix-du-jeton-wow');
			exit;
		}

		if($periode === 't')
		{
			$stmt = $pdo->prepare('SELECT date_jour, prix FROM wow_prix_jeton ORDER BY date_jour ASC');
			$stmt->execute();
			$res = $stmt->fetchAll();

			$stmtAgg = $pdo->prepare('SELECT MAX(prix) AS prix_max, MIN(prix) AS prix_min, (MAX(prix) - MIN(prix)) AS ecart FROM wow_prix_jeton');
			$stmtAgg->execute();
			$agg = $stmtAgg->fetch();
		}

		else
		{
			function dateDepuis(string $periode): DateTimeImmutable
			{
				$maintenant = new DateTimeImmutable('now', new DateTimeZone('UTC'));

				return match ($periode) {
					'24h' => $maintenant->sub(new DateInterval('PT24H')),
					'7j' => $maintenant->sub(new DateInterval('P7D')),
					'30j' => $maintenant->sub(new DateInterval('P30D')),
					'6m' => $maintenant->sub(new DateInterval('P6M')),
					'12m' => $maintenant->sub(new DateInterval('P12M')),
					'24m' => $maintenant->sub(new DateInterval('P24M')),
					default => $maintenant->sub(new DateInterval('P30D'))
				};
			}

			$depuis = dateDepuis($periode)->format('Y-m-d');

			$stmt = $pdo->prepare('SELECT date_jour, prix FROM wow_prix_jeton WHERE date_jour >= :since ORDER BY date_jour ASC');
			$stmt->execute([':since' => $depuis]);
			$res = $stmt->fetchAll();

			$stmtAgg = $pdo->prepare('SELECT MAX(prix) AS prix_max, MIN(prix) AS prix_min, (MAX(prix) - MIN(prix)) AS ecart FROM wow_prix_jeton WHERE date_jour >= :since');
			$stmtAgg->execute([':since' => $depuis]);
			$agg = $stmtAgg->fetch();
		}

		echo json_encode([
			// 'periode'	=> $periode,
			// 'since'		=> $depuis,
			'x'			=> array_column($res, 'date_jour'),
			'y'			=> array_map('intval', array_column($res, 'prix')),
			'stats'		=> [
				'max'	=> isset($agg['prix_max']) ? (int) $agg['prix_max'] : null,
				'min'	=> isset($agg['prix_min']) ? (int) $agg['prix_min'] : null,
				'ecart'	=> isset($agg['ecart']) ? (int) $agg['ecart'] : null,
			],
		], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}
}

elseif(isset($_GET['majPrix']))
{
	header('Content-Type: application/json; charset=utf-8');

	if(jetonMaj($pdo))
	{
		echo json_encode(['ok' => true]);
		exit;
	}

	echo json_encode(['erreur' => false]);
	exit;
}

else
{
$prix = $apiClient->wow_token()->index()->price;
$lastUpdatedTimestamp = ($apiClient->wow_token()->index()->last_updated_timestamp / 1000);

$or = convertirPieces($prix)['or'];
$argent = convertirPieces($prix)['argent'];
$cuivre = convertirPieces($prix)['cuivre'];

if((!empty($prix) AND !is_numeric($prix)) OR (!empty($lastUpdatedTimestamp) AND !is_numeric($lastUpdatedTimestamp))) {
	setFlash('danger', 'Données API invalides');

	header('Location: /historique-des-prix-du-jeton-wow');
	exit;
}

require_once 'a_body.php';

echo '<div class="banniere banniere-jeton mt-4"></div>

<h1><a href="/historique-des-prix-du-jeton-wow">Historique de prix du Jeton WoW</a></h1>

<p>Le <strong>Jeton WoW</strong> est un objet virtuel introduit par Blizzard dans World of Warcraft qui permet d’échanger argent réel et or en jeu, de manière sécurisée :</p>

<ul>
	<li>Achat avec argent réel : tu peux acheter un Jeton via la boutique Blizzard (prix fixe en euros)</li>
	<li>Vente en jeu : le Jeton peut ensuite être mis en vente à l’hôtel des ventes contre de l’or, au prix déterminé automatiquement par Blizzard en fonction de l’offre et de la demande</li>
	<li>Utilisation le joueur qui achète le Jeton avec de l’or peut soit
		<ul>
			<li>l’utiliser pour ajouter 30 jours de temps de jeu</li>
			<li>soit le convertir en solde Battle.net (utilisable pour d’autres jeux Blizzard, services, ou contenus numériques)</li>
		</ul>
	</li>
</ul>

<p>En résumé, c’est un système officiel qui sert de pont entre monnaie réelle et monnaie virtuelle, tout en évitant les pratiques de gold selling illégales.</p>

<p class="fs-3 text-center my-5">Prix du Jeton le '.dateFormat(time()).' : '.$or.' '.PO.'</p>';
?>
<div id="graphiquePrixJeton" style="height: 480px; width: 100%;"></div>

<script>
<?php
if(isset($_GET['7j']))		echo 'let periode = "7j";';
elseif(isset($_GET['30j']))	echo 'let periode = "30j";';
elseif(isset($_GET['6m']))	echo 'let periode = "6m";';
elseif(isset($_GET['1a']))	echo 'let periode = "12m";';
elseif(isset($_GET['2a']))	echo 'let periode = "24m";';
elseif(isset($_GET['t']))	echo 'let periode = "t";';
else						echo 'let periode = "30j";';
?>

const el = document.getElementById('graphiquePrixJeton');
const chart = echarts.init(el);
const nf = new Intl.NumberFormat('fr-FR');

chart.showLoading('default', { text: 'Chargement…' });

fetch('/api-prix-jeton?periode=' + periode, { cache: 'no-store' })
.then(r => r.json())
.then(payload => {
	const xs = payload.x || [];
	const ys = (payload.y || []).map(v => (v === null ? null : Number(v)));
	const data = xs.map((d, i) => (ys[i] === null ? null : [d, ys[i]])).filter(v => v !== null);

	const titres = {
		'7j': 'Prix du jeton : 7 jours',
		'30j': 'Prix du jeton : 30 jours',
		'6m': 'Prix du jeton : 6 mois',
		'12m': 'Prix du jeton : 1 an',
		'24m': 'Prix du jeton : 2 ans',
		't': 'Prix du jeton : Total'
	};

	const option = {
		title: {
			text: (titres[periode] || titres['30j']),
			left: 'center',
			top: 20,
			padding: [0, 0, 15, 0],
			textStyle: {
				color: 'rgba(255,255,255, 1)',
				fontSize: 20,
				fontWeight: 'bold'
			},
			// subtext: 'Source : API interne',
			// subtextStyle: {
			// 	color: '#999999', // gris clair
			// 	fontSize: 12
			// }
		},
		tooltip: {
			trigger: 'axis',
			axisPointer: { type: 'cross' },
			position: function (pt) { return [pt[0], '10%']; },
			formatter: function (params) {
				const p = Array.isArray(params) ? params[0] : params;
				const [dateStr, val] = p.data;
				const d = new Date(dateStr);
				const jour	= String(d.getDate()).padStart(2, '0');
				const mois	= String(d.getMonth() + 1).padStart(2, '0');
				const annee	= d.getFullYear();
				const dateFormatee = `${jour}-${mois}-${annee}`;

				return `<div><strong>${dateFormatee}</strong><br/>Prix : ${nf.format(val)}</div>`;
			}
		},
		toolbox: {
			feature: {
				dataZoom: { yAxisIndex: 'none', title: { zoom: 'Zoom', back: 'Réinitialiser zoom' } },
				restore: { title: 'Réinitialiser' },
				saveAsImage: { title: 'Enregistrer l\'image' }
			}
		},
		grid: { left: 48, right: 16, top: 40, bottom: 48 },
		xAxis: {
			type: 'time',
			boundaryGap: false
		},
		yAxis: {
			type: 'value',
			boundaryGap: [0, '5%'],
			axisLabel: { formatter: (val) => nf.format(val) }
		},
		dataZoom: [
			{ type: 'inside', start: 0, end: 100 },
			{ start: 0, end: 100 }
		],
		series: [{
			name: 'Prix',
			type: 'line',
			symbol: 'none',
			sampling: 'lttb',
			lineStyle: { width: 2 },
			areaStyle: {
				color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
					{ offset: 0, color: 'rgba(255,158,68,1)' },
					{ offset: 1, color: 'rgba(255,70,131,1)' }
				])
			},
			itemStyle: { color: 'rgba(255,70,131,1)' },
			data: data
		}]
	};

	chart.setOption(option);
})
.catch(err => {
	console.error('Erreur de chargement des données :', err);
	chart.setOption({
		title: { left: 'center', text: 'Erreur de chargement des données' }
	});
})
.finally(() => chart.hideLoading());

window.addEventListener('resize', () => chart.resize());
</script>

<?php
$cacheT = '-6 hours';
$cacheStatsJeton = $_SERVER['DOCUMENT_ROOT'].'assets/cache/stats_jeton.cache';
if(!file_exists($cacheStatsJeton) OR (filemtime($cacheStatsJeton) < strtotime($cacheT)))
{
	function statsJetonInterval(PDO $pdo, ?int $debutTs, ?int $finTs): array
	{
		if($debutTs === null OR $finTs === null)
		{
			$stmt = $pdo->prepare('WITH t AS (SELECT id, prix, timestamp FROM wow_prix_jeton)
			SELECT
				(SELECT prix FROM t ORDER BY prix ASC, id ASC LIMIT 1)							AS prix_min,
				(SELECT FROM_UNIXTIME(timestamp) FROM t ORDER BY prix ASC, id ASC LIMIT 1)		AS date_min,
				(SELECT prix FROM t ORDER BY prix DESC, id DESC LIMIT 1)						AS prix_max,
				(SELECT FROM_UNIXTIME(timestamp) FROM t ORDER BY prix DESC, id DESC LIMIT 1)	AS date_max,
				((SELECT MAX(prix) FROM t) - (SELECT MIN(prix) FROM t))							AS ecart,
				(SELECT SUM(prix) FROM t)														AS prix_total');
			$stmt->execute();
		}

		else
		{
			$stmt = $pdo->prepare('WITH t AS (SELECT id, prix, timestamp FROM wow_prix_jeton WHERE timestamp BETWEEN :debut AND :fin)
			SELECT
				(SELECT prix FROM t ORDER BY prix ASC, id ASC LIMIT 1)							AS prix_min,
				(SELECT FROM_UNIXTIME(timestamp) FROM t ORDER BY prix ASC, id ASC LIMIT 1)		AS date_min,
				(SELECT prix FROM t ORDER BY prix DESC, id DESC LIMIT 1)						AS prix_max,
				(SELECT FROM_UNIXTIME(timestamp) FROM t ORDER BY prix DESC, id DESC LIMIT 1)	AS date_max,
				((SELECT MAX(prix) FROM t) - (SELECT MIN(prix) FROM t))							AS ecart,
				(SELECT SUM(prix) FROM t)														AS prix_total');
			$stmt->execute([
				':debut' => (string) $debutTs,
				':fin' => (string) $finTs
			]);
		}

		return $stmt->fetch() ?: [
			'prix_min' => null,
			'date_min' => null,
			'prix_max' => null,
			'date_max' => null,
			'ecart' => null,
			'prix_total' => null,
		];
	}

	function statsJetonToutesPeriodes(PDO $pdo): array
	{
		$maintenant = new DateTimeImmutable('now', new DateTimeZone('UTC'));

		$donneesStatsPeriodes = [
			'7j' => [$maintenant->sub(new DateInterval('P7D'))->getTimestamp(), $maintenant->getTimestamp()],
			'30j' => [$maintenant->sub(new DateInterval('P30D'))->getTimestamp(), $maintenant->getTimestamp()],
			'6m' => [$maintenant->sub(new DateInterval('P6M'))->getTimestamp(), $maintenant->getTimestamp()],
			'1a' => [$maintenant->sub(new DateInterval('P1Y'))->getTimestamp(), $maintenant->getTimestamp()],
			'2a' => [$maintenant->sub(new DateInterval('P2Y'))->getTimestamp(), $maintenant->getTimestamp()],
			'total' => [null, null],
		];

		$out = [];
		foreach($donneesStatsPeriodes as $label => [$debut, $fin]) {
			$out[$label] = statsJetonInterval($pdo, $debut, $fin);
		}

		return $out;
	}

	$stats = statsJetonToutesPeriodes($pdo);

	$dateMin7j = dateFormat($stats['7j']['date_min']);			$dateMax7j = dateFormat($stats['7j']['date_max']);
	$dateMin30j = dateFormat($stats['30j']['date_min']);		$dateMax30j = dateFormat($stats['30j']['date_max']);
	$dateMin6m = dateFormat($stats['6m']['date_min']);			$dateMax6m = dateFormat($stats['6m']['date_max']);
	$dateMin1a = dateFormat($stats['1a']['date_min']);			$dateMax1a = dateFormat($stats['1a']['date_max']);
	$dateMin2a = dateFormat($stats['2a']['date_min']);			$dateMax2a = dateFormat($stats['2a']['date_max']);
	$dateMinTotal = dateFormat($stats['total']['date_min']);	$dateMaxTotal = dateFormat($stats['total']['date_max']);

	$donneesStatsJeton = '<div class="table-responsive mt-5">
		<table class="table table-hover" id="statsJeton">
			<thead class="table-light">
				<tr>
					<th class="pt-1"></th>
					<th class="text-center pb-2 pt-1"><a href="?7j#graphiquePrixJeton" class="fs-5">7 jours</a></th>
					<th class="text-center pb-2 pt-1"><a href="?30j#graphiquePrixJeton" class="fs-5">30 jours</a></th>
					<th class="text-center pb-2 pt-1"><a href="?6m#graphiquePrixJeton" class="fs-5">6 mois</a></th>
					<th class="text-center pb-2 pt-1"><a href="?1a#graphiquePrixJeton" class="fs-5">1 an</a></th>
					<th class="text-center pb-2 pt-1"><a href="?2a#graphiquePrixJeton" class="fs-5">2 ans</a></th>
					<th class="text-center pb-2 pt-1"><a href="?t#graphiquePrixJeton" class="fs-5">Total</a></th>
				</tr>
			</thead>

			<tbody class="table-group-divider">
				<tr>
					<td>Maximum</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMin7j.'">'.number_format($stats['7j']['prix_max']).' '.PO.'</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMin30j.'">'.number_format($stats['30j']['prix_max']).' '.PO.'</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMin6m.'">'.number_format($stats['6m']['prix_max']).' '.PO.'</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMin1a.'">'.number_format($stats['1a']['prix_max']).' '.PO.'</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMin2a.'">'.number_format($stats['2a']['prix_max']).' '.PO.'</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMinTotal.'">'.number_format($stats['total']['prix_max']).' '.PO.'</td>
				</tr>
				<tr>
					<td>Minimum</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMax7j.'">'.number_format($stats['7j']['prix_min']).' '.PO.'</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMax30j.'">'.number_format($stats['30j']['prix_min']).' '.PO.'</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMax6m.'">'.number_format($stats['6m']['prix_min']).' '.PO.'</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMax1a.'">'.number_format($stats['1a']['prix_min']).' '.PO.'</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMax2a.'">'.number_format($stats['2a']['prix_min']).' '.PO.'</td>
					<td class="text-center" data-bs-toggle="tooltip" data-bs-title="'.$dateMaxTotal.'">'.number_format($stats['total']['prix_min']).' '.PO.'</td>
				</tr>
				<tr>
					<td>Écart</td>
					<td class="text-center">'.number_format($stats['7j']['ecart']).' '.PO.'</td>
					<td class="text-center">'.number_format($stats['30j']['ecart']).' '.PO.'</td>
					<td class="text-center">'.number_format($stats['6m']['ecart']).' '.PO.'</td>
					<td class="text-center">'.number_format($stats['1a']['ecart']).' '.PO.'</td>
					<td class="text-center">'.number_format($stats['2a']['ecart']).' '.PO.'</td>
					<td class="text-center">'.number_format($stats['total']['ecart']).' '.PO.'</td>
				</tr>
				<tr>
					<td>Total</td>
					<td class="text-center">'.number_format($stats['7j']['prix_total']).' '.PO.'</td>
					<td class="text-center">'.number_format($stats['30j']['prix_total']).' '.PO.'</td>
					<td class="text-center">'.number_format($stats['6m']['prix_total']).' '.PO.'</td>
					<td class="text-center">'.number_format($stats['1a']['prix_total']).' '.PO.'</td>
					<td class="text-center">'.number_format($stats['2a']['prix_total']).' '.PO.'</td>
					<td class="text-center">'.number_format($stats['total']['prix_total']).' '.PO.'</td>
				</tr>
			</tbody>
		</table>
	</div>';

	if(!empty($donneesStatsJeton))
	{
		echo $donneesStatsJeton;

		cache($cacheStatsJeton, $donneesStatsJeton);
	}
}

else
	echo (file_exists($cacheStatsJeton) AND filesize($cacheStatsJeton) > 0) ? file_get_contents($cacheStatsJeton) : null;

require_once 'a_footer.php';
}