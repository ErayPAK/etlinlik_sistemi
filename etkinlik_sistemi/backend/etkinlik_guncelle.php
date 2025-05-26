<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Yönetici girişi yapılmamışsa yönlendir
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

// Formdan gelen verileri al
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $etkinlik_id = $_POST['id'];
    $baslik = $_POST['baslik'];
    $aciklama = $_POST['aciklama'];
    $tarih = $_POST['tarih'];
    $tur = $_POST['tur'];
    $gorsel_url = $_POST['gorsel_url'];

    // Veritabanını güncelle
    $sql_guncelle = "UPDATE etkinlikler SET baslik = :baslik, aciklama = :aciklama, tarih = :tarih, tur = :tur, gorsel_url = :gorsel_url WHERE id = :id";
    $stmt_guncelle = $conn->prepare($sql_guncelle);
    $stmt_guncelle->bindParam(':baslik', $baslik, PDO::PARAM_STR);
    $stmt_guncelle->bindParam(':aciklama', $aciklama, PDO::PARAM_STR);
    $stmt_guncelle->bindParam(':tarih', $tarih, PDO::PARAM_STR);
    $stmt_guncelle->bindParam(':tur', $tur, PDO::PARAM_STR);
    $stmt_guncelle->bindParam(':gorsel_url', $gorsel_url, PDO::PARAM_STR);
    $stmt_guncelle->bindParam(':id', $etkinlik_id, PDO::PARAM_INT);

    if ($stmt_guncelle->execute()) {
        echo '<div style="color: green; font-weight: bold;">Etkinlik başarıyla güncellendi.</div><br>';
        header("Location: ../yonetici_paneli.php"); // header location düzeltildi
        exit();
    } else {
        echo '<div style="color: red; font-weight: bold;">Etkinlik güncellenirken bir hata oluştu.</div><br>';
    }
} else {
    echo '<div style="color: red; font-weight: bold;">Bu sayfaya doğrudan erişim yasaktır.</div><br>';
}
?>
