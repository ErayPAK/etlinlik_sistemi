<?php
session_start(); // Oturum başlat

// Hata gösterimini aç
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Veritabanı bilgileri
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
        // E-posta adresini veritabanında ara
        $sql = "SELECT id, sifre, onay_durumu FROM kullanicilar WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($kullanici) {
            // Kullanıcı bulundu, şimdi şifreyi kontrol et
            if (password_verify($sifre, $kullanici['sifre'])) {
                // Şifre doğru ve kullanıcı onaylıysa giriş yap
                if ($kullanici['onay_durumu'] == 1) {
                    $_SESSION['kullanici_id'] = $kullanici['id'];
                    echo '<div class="basarili">Giriş başarılı! Ana sayfaya yönlendiriliyorsunuz...</div>';
                    header("refresh:2;url=../anasayfa.html"); // Ana sayfaya yönlendir (henüz oluşturmadık)
                    exit();
                } else {
                    echo '<div class="hatali">Hesabınız henüz yönetici tarafından onaylanmadı.</div>';
                }
            } else {
                echo '<div class="hatali">Yanlış şifre!</div>';
            }
        } else {
            echo '<div class="hatali">Bu e-posta adresine kayıtlı kullanıcı bulunamadı!</div>';
        }
    }
} else {
    echo "Bu sayfaya doğrudan erişim yasaktır.";
}
?>