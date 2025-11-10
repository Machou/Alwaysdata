<?php
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\RFCValidation;

if(isset($_GET['chargerCourriel']) OR !empty($_REQUEST['courriel']))
{
	require_once '../config/config.php';

	header('Content-Type: application/json; charset=utf-8');

	$courriel = trim(mb_strtolower($_REQUEST['courriel']));

	if(!empty($courriel))
	{
		$validator = new EmailValidator();
		if($validator->isValid($courriel, new RFCValidation()))
		{
			if($validator->isValid($courriel, new DNSCheckValidation()))
			{
				if(filter_var($courriel, FILTER_VALIDATE_EMAIL))
				{
					// https://app.apivoid.com/dashboard/api-keys/
					$courrielApi = get('https://endpoint.apivoid.com/emailverify/v1/pay-as-you-go/?key=8c0de37426ee9f41cde9dbb11729ea8e01d13e64&email='.urlencode($courriel));

					/*
						Array
						(
							[data] => Array
								(
									[email] => zom@gfmail.com
									[valid_format] => 1
									[username] => zom
									[role_address] =>
									[suspicious_username] =>
									[dirty_words_username] =>
									[suspicious_email] =>
									[domain] => gfmail.com
									[valid_tld] => 1
									[disposable] =>
									[email_forwarder] =>
									[has_a_records] => 1
									[has_mx_records] =>
									[has_spf_records] =>
									[is_spoofable] => 1
									[dmarc_configured] =>
									[dmarc_enforced] =>
									[free_email] =>
									[russian_free_email] =>
									[china_free_email] =>
									[did_you_mean] =>
									[suspicious_domain] =>
									[dirty_words_domain] =>
									[domain_popular] =>
									[risky_tld] =>
									[police_domain] =>
									[government_domain] =>
									[educational_domain] =>
									[should_block] => 1
									[score] => 0
								)

							[credits_remained] => 24.82
							[estimated_queries] => 413
							[elapsed_time] => 0.34
							[success] => 1
						)
					*/

					if(!empty($courrielApi))
					{
						$jsonAPIVoid = json_decode($courrielApi, true);

						if(empty($jsonAPIVoid['error']))
						{
							$donneesCourriel = [];
							if(!empty($jsonAPIVoid['data']['disposable']))			$donneesCourriel = ['error' => 'Le courriel est jetable / temporaire'];
							if(!empty($jsonAPIVoid['data']['suspicious_username']))	$donneesCourriel = ['error' => 'Le nom d’utilisateur du courriel est suspect'];
							if(empty($jsonAPIVoid['data']['valid_tld']))			$donneesCourriel = ['error' => 'Le domaine de premier niveau du courriel est incorrect'];
							if(!empty($jsonAPIVoid['data']['risky_tld']))			$donneesCourriel = ['error' => 'Le domaine de premier niveau du courriel est risqué'];
							if(!empty($jsonAPIVoid['data']['suspicious_domain']))	$donneesCourriel = ['error' => 'Le domaine de premier niveau du courriel est suspect'];

							$allNull = true;
							foreach($donneesCourriel as $v)
							{
								if($v !== null)
								{
									$allNull = false;
									break;
								}
							}

							echo json_encode(!$allNull ? $donneesCourriel : ['succes' => '<span class="fw-bold">'.secuChars($courriel).'</span> : est valide et ne présente à priori aucun risque']);
						}

						else
							echo json_encode(['error' => 'Le courriel incorrect']);
					}

					else
						echo json_encode(['error' => '<span class="fw-bold">'.secuChars($courriel).'</span> : nous rencontrons un problème avec la base de données']);
				}

				else
					echo json_encode(['error' => '<span class="fw-bold">'.secuChars($courriel).'</span> : PHP, via <code>filter_var($courriel, FILTER_VALIDATE_EMAIL)</code>, ne valide pas ce courriel']);
			}

			else
				echo json_encode(['error' => '<span class="fw-bold">'.secuChars($courriel).'</span> : le DNS MX est incorrect']);
		}

		else
			echo json_encode(['error' => '<span class="fw-bold">'.secuChars($courriel).'</span> : une RFC n’est pas respectée (<a href="https://datatracker.ietf.org/doc/html/rfc5321">5321</a>, <a href="https://datatracker.ietf.org/doc/html/rfc5322">5322</a>, <a href="https://datatracker.ietf.org/doc/html/rfc6530">6530</a>, <a href="https://datatracker.ietf.org/doc/html/rfc6531">6531</a>, <a href="https://datatracker.ietf.org/doc/html/rfc6532">6532</a> et <a href="https://datatracker.ietf.org/doc/html/rfc1035">1035</a>)']);
	}

	else
		echo json_encode(['error' => '<span class="fw-bold">'.secuChars($courriel).'</span> : courriel incorrect']);
}

else
{
require_once 'a_body.php';

echo '<div class="border rounded" id="courriel">
	<h1 class="mb-5 text-center"><a href="/reputation-courriel"><i class="fa-solid fa-at"></i> Réputation d’un courriel</a></h1>

	<form action="#courriel" method="post" id="courrielForm">
		<div class="row">
			<div class="col-12 col-lg-5 mx-auto'.(!empty($courrielHtml) ? ' mb-5' : null).'">
				<div class="input-group input-group-thisip">
					<span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
					<input type="email" name="courriel" '.(!empty($courriel) ? 'value="'.$courriel.'"' : null).' class="form-control form-control-lg" id="formInputCourriel" minlength="5" maxlength="254" placeholder="elon.musk@tesla.com" '.(empty($_POST['courriel']) ? 'autofocus' : null).' required>
					<button type="submit" class="btn btn-primary" form="courrielForm">Valider</button>
				</div>
			</div>
		</div>
	</form>

	<div>
		<div class="mx-auto my-5 col-12 col-lg-6" id="resultatsCourriel"></div>

		<div style="display: none;" class="mb-5" id="chargement" title="Chargement…">
			<img src="/assets/img/chargement.svg" style="height: 60px;" class="d-flex mx-auto" alt="Chargement…">
			<p class="mb-0 text-center fw-bold">Chargement…</p>
		</div>
	</div>

	<div class="mt-5 p-3 text-primary-emphasis bg-primary-subtle border border-primary-subtle rounded-3">
		<div class="d-flex align-items-center justify-content-center">
			<i class="fa-solid fa-circle-info fs-1 me-3"></i>
			<span>Vous souhaitez vérifier si un courriel est digne de confiance ou et jetable ? Copiez le courriel dans le formulaire ci-dessus pour afficher les résultats.</span>
		</div>
	</div>

	<hr class="my-5">

	<div id="api-courriel">
		<div class="mb-4">
			<h5>API Courriel</h5>

			<div class="row mb-2">
				<div class="col-3	col-lg-1 fw-bold">TYPE</div>
				<div class="col-9	col-lg-5 fw-bold">URL</div>
				<div class="		col-lg-6 fw-bold d-none d-lg-flex">Description</div>
			</div>
			<div class="row">
				<div class="col-3 col-lg-1"><span class="badge text-white bg-info fs-6">GET</span></div>
				<div class="col-9 col-lg-5"><code>https://thisip.pw/reputation-courriel/identifiant@domaine.fr</code></div>
				<div class="col-12 col-lg-6 mt-3 mt-lg-0"><span class="fw-bold">Vérification du courriel</span> : vérifie si un courriel est jetable / temporaire, suspect, incorrect ou risqué</div>
			</div>
		</div>

		<div class="mb-4">
			<h5>Exemple</h5>

			<code>curl -X GET "https://thisip.pw/reputation-courriel/thisip@yopmail.com"</code>
		</div>

		<div>
			<h5>Réponse</h5>

			<p>Réponse avec le code <code>HTTP 200</code> :</p>

			<code>[{"erreur": "Le courriel est jetable \/ temporaire"}]</code>
		</div>
	</div>
</div>';

require_once 'a_footer.php';
}