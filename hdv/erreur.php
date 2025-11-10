<?php
require_once '../config/wow_config.php';
require_once 'a_body.php';

$code = !empty($_GET['code']) ? secu($_GET['code']) : null;

$uri = $_SERVER['REQUEST_URI'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

file_put_contents('../apache-errors.log', '[HdV.Li] ['.date('Y-m-d H:i:s').'] Erreur '.$code.' - '.$uri.' - IP : '.$ip."\n", FILE_APPEND);

http_response_code($code);

echo '<div>
	<h1>Erreur '.$code.'</h1>

	<div class="mb-5 text-center">
		<p class="mb-5">'.($code === 404 ? 'Page introuvable' : 'Erreur').'</p>

		<img src="/assets/img/wow-logo-erreur.png" class="img-fluid" alt="Erreur '.$code.'" title="Oups…">
	</div>

	<p class="mb-0 text-center fs-3"><a href="https://hdv.li/">Retour à l’accueil</a></p>
</div>';

require_once 'a_footer.php';