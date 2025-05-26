<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$api_key = '0bc199614d2846529d5120623251505'; // Buraya kendi API anahtarınızı yapıştırın
$q = 'Suluova'; // İstediğiniz konum (şehir adı, enlem/boylam vb.)
$url = "http://api.weatherapi.com/v1/current.json?key={$api_key}&q={$q}&aqi=no";

$response = file_get_contents($url);

if ($response === false) {
    echo json_encode(['hata' => 'Hava durumu API\'sine bağlanılamadı.']);
} else {
    echo $response;
}
?>