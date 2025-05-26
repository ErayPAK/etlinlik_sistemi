<?php
// Hata gösterimini aç
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Veritabanı bilgileri
$host = "localhost";
$dbname = "etkinlik"; // Oluşturduğunuz veritabanının adı
$username = "root";   // XAMPP'in varsayılan MySQL kullanıcı adı
$password = "";       // XAMPP'in varsayılan MySQL parolası (genellikle boş)

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $sifre = $_POST["sifre"];
    $sifreTekrar = $_POST["sifreTekrar"];

    // Basit doğrulama (istemci tarafında da yapılıyor ama sunucu tarafında da önemli)
    if (empty($email) || empty($sifre || empty($sifreTekrar))) {
        die("Lütfen tüm alanları doldurun.");
    }
    if ($sifre !== $sifreTekrar) {
        die("Şifreler eşleşmiyor!");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Geçersiz e-posta adresi!");
    }
    if (strlen($sifre) < 6) {
        die("Şifre en az 6 karakter olmalıdır.");
    }

    // E-posta adresinin veritabanında olup olmadığını kontrol et
    $sql_check = "SELECT email FROM kullanicilar WHERE email = :email";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':email', $email);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        die("Bu e-posta adresi zaten kayıtlı!");
    }

    // Şifreyi hashle
    $hashedPassword = password_hash($sifre, PASSWORD_DEFAULT);

    // Kullanıcıyı veritabanına ekle (onay_durumu başlangıçta 0)
    $sql_insert = "INSERT INTO kullanicilar (email, sifre, onay_durumu) VALUES (:email, :password, 0)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bindParam(':email', $email);
    $stmt_insert->bindParam(':password', $hashedPassword);

    if ($stmt_insert->execute()) {
        echo "Kayıt başarılı! Yönetici onayı bekliyor.";
    } else {
        echo "Kayıt sırasında bir hata oluştu.";
    }
} else {
    echo "Bu sayfaya doğrudan erişim yasaktır.";
}
?>