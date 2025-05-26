<?php
header('Content-Type: application/json');
$host = "localhost";
$dbname = "etkinlik";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Veritabanı bağlantı hatası: ' . $e->getMessage()]);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT id, baslik, aciklama, tarih, gorsel_url, bilet_fiyati_tam, bilet_fiyati_ogrenci, kontenjan FROM etkinlikler");
    $stmt->execute();
    $etkinlikler = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($etkinlikler);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Veritabanı sorgu hatası: ' . $e->getMessage()]);
    exit();
}
?>