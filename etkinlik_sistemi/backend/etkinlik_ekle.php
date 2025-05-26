<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Yönetici girişi kontrolü (basit)
if (!isset($_SESSION['yonetici_giris']) || $_SESSION['yonetici_giris'] !== true) {
    header("Location: ../yonetici_giris.html");
    exit();
}

$host = "localhost";
$dbname = "etkinlik";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $baslik = $_POST["baslik"];
    $aciklama = $_POST["aciklama"];
    $tarih = $_POST["tarih"];
    $tur = $_POST["tur"];
    $gorsel_url = $_POST["gorsel_url"] ?? ''; // İsteğe bağlı

    if (empty($baslik) || empty($aciklama) || empty($tarih) || empty($tur)) {
        echo '<div style="color: red; font-weight: bold;">Lütfen tüm zorunlu alanları doldurun.</div><br>';
        echo '<p><a href="../yonetici_paneli.php">Geri dön</a></p>';
        exit();
    }

    $sql = "INSERT INTO etkinlikler (baslik, aciklama, tarih, tur, gorsel_url) VALUES (:baslik, :aciklama, :tarih, :tur, :gorsel_url)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':baslik', $baslik);
    $stmt->bindParam(':aciklama', $aciklama);
    $stmt->bindParam(':tarih', $tarih);
    $stmt->bindParam(':tur', $tur);
    $stmt->bindParam(':gorsel_url', $gorsel_url);

    if ($stmt->execute()) {
        echo '<div style="color: green; font-weight: bold;">Etkinlik başarıyla eklendi.</div><br>';
        echo '<p><a href="../yonetici_paneli.php">Yönetici paneline dön</a></p>';
    } else {
        echo '<div style="color: red; font-weight: bold;">Etkinlik eklenirken bir hata oluştu.</div><br>';
        echo '<p><a href="../yonetici_paneli.php">Geri dön</a></p>';
    }
} else {
    echo "Bu sayfaya doğrudan erişim yasaktır.";
}
?>
