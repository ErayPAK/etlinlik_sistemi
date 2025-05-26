<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$dbname = "etkinlik";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8");
} catch (PDOException $e) {
    echo json_encode(['hata' => 'Veritabanı bağlantı hatası: ' . $e->getMessage()]);
    exit();
}

$ilgilenilen_turler = ['Yaz Etkinliği', 'Konser']; // Öneri için göstermek istediğiniz türleri buraya ekleyin

$onerilen_etkinlikler = [];

foreach ($ilgilenilen_turler as $tur) {
    $stmt = $conn->prepare("SELECT * FROM etkinlikler WHERE tur = :tur ORDER BY RAND() LIMIT 2"); // Her türden rastgele 2 etkinlik al
    $stmt->bindParam(':tur', $tur, PDO::PARAM_STR);
    $stmt->execute();
    $sonuclar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $onerilen_etkinlikler = array_merge($onerilen_etkinlikler, $sonuclar);
}

echo json_encode($onerilen_etkinlikler);

?>