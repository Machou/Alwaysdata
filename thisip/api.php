<?php
require_once '../config/bdd.php';
require_once '../config/config.php';

header('Content-Type: application/json; charset=utf-8');

$grab = new Grab($pdo, getRemoteAddr(), getHttpUserAgent());
$infos_Array = $grab->get_infos();

echo (isset($_GET['api']) AND isset($_GET['json'])) ? json_encode($infos_Array) : json_encode(['ERREUR' => 'Erreur inconnue']);