<?php
require_once '../../config/config.php';

// https://github.com/picqer/php-barcode-generator

header('Content-Type: image/png');

$generator = new Picqer\Barcode\BarcodeGeneratorPNG();

$generator->useGd();

$generated = $generator->getBarcode('11111111', $generator::TYPE_CODE_128);

echo $generated;