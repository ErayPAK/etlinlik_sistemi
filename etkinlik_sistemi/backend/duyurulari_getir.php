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
} catch (PDOException $e) {
    die(json_encode(["hata" => "Veritabanı bağlantı hatası: " . $e->getMessage()]));
}

$sql = "SELECT id, baslik, icerik, tarih FROM duyurular ORDER BY tarih DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$duyurular = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($duyurular);
?>