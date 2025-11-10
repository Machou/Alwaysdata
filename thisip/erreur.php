<?php
require_once 'a_body.php';

$code = !empty($_GET['code']) ? secu($_GET['code']) : null;

$uri = $_SERVER['REQUEST_URI'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

file_put_contents('../apache-errors.log', '[ThisIP.pw] ['.date('Y-m-d H:i:s').'] Erreur '.$code.' - '.secuChars($uri).' - IP : '.$ip."\n", FILE_APPEND);

http_response_code($code);

echo '<div class="border rounded">
	<h1 class="mb-5 text-center">Erreur '.$code.'</h1>

	<div class="mb-5 text-center">'.($code === 404 ? '<img src="/assets/img/erreur-404.png" class="img-fluid" alt="Erreur 404" title="Oups…">' : '<span class="fw-bold">Erreur '.$code.'</span>').'</div>

	<p class="mb-0 text-center fs-3"><a href="https://thisip.pw/">Retour à l’accueil</a></p>
</div>';

require_once 'a_footer.php';