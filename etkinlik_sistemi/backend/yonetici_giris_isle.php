<?php
session_start();
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
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $sifre = $_POST["sifre"];

    if (empty($email) || empty($sifre)) {
        echo '<div class="hatali">Lütfen e-posta ve şifrenizi girin.</div>';
    } else {
        // Yönetici bilgilerini veritabanından kontrol et (ayrı bir tablo olabilir veya kullanıcılar tablosunda bir rol alanı olabilir)
        // Şimdilik basitçe belirli bir e-posta ve şifreye göre kontrol edelim.
        $yonetici_email = "admin@etkinlik.com"; // Örnek yönetici e-postası
        $yonetici_sifre = "admin123";         // Örnek yönetici şifresi

        if ($email === $yonetici_email && $sifre === $yonetici_sifre) {
            $_SESSION['yonetici_giris'] = true;
            echo '<div class="basarili">Yönetici girişi başarılı! Yönetici paneline yönlendiriliyorsunuz...</div>';
            header("refresh:2;url=../yonetici_paneli.php"); // Yönetici paneline yönlendir
            exit();
        } else {
            echo '<div class="hatali">Yanlış yönetici e-postası veya şifresi!</div>';
        }
    }
} else {
    echo "Bu sayfaya doğrudan erişim yasaktır.";
}
?>