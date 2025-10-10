<?php
require_once __DIR__ . '/autoload.php';

echo "Autoloader cargado\n";
echo "Existe TypeCode128? ";
var_export(class_exists(\Picqer\Barcode\Types\TypeCode128::class));
echo "\n";
echo "Existe BarcodeGenerator? ";
var_export(class_exists(\Picqer\Barcode\BarcodeGenerator::class));
echo "\n";
