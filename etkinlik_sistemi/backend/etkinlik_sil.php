<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Yönetici girişi yapılmamışsa yönlendir
if (!isset($_SESSION['yonetici_giris']) || $_SESSION['yonetici_giris'] !== true) {
    header("Location: ../yonetici_giris.html"); // ../ eklendi
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

// Formdan gelen id'yi al
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $etkinlik_id = $_POST['id'];

    // Veritabanından etkinliği sil
    $sql_sil = "DELETE FROM etkinlikler WHERE id = :id";
    $stmt_sil = $conn->prepare($sql_sil);
    $stmt_sil->bindParam(':id', $etkinlik_id, PDO::PARAM_INT);

    if ($stmt_sil->execute()) {
        echo '<div style="color: green; font-weight: bold;">Etkinlik başarıyla silindi.</div><br>';
        header("refresh:2;url=../yonetici_paneli.php"); // ../ eklendi
        exit();
    } else {
        echo '<div style="color: red; font-weight: bold;">Etkinlik silinirken bir hata oluştu.</div><br>';
    }
} else {
    echo '<div style="color: red; font-weight: bold;">Bu sayfaya doğrudan erişim yasaktır.</div><br>';
}
?>
