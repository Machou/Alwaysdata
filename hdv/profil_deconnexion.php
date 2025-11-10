<?php
require_once '../config/wow_config.php';

supprimerJeton($pdo, true);

session_destroy();

setcookie('memoriser', '', time() - 3600, '/', 'hdv.li', true, true);

if(isset($_GET['motDePasseOublie']))
{
	header('Location: /mot-de-passe-oublie');
	exit;
}

header('Location: /');