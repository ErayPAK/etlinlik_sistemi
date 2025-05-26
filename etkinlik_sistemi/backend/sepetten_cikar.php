<?php
session_start();

// Hata ayıklama için (geliştirme sırasında kullanın, üretimde kaldırın!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bağlantısı için gerekli bilgileri al
require_once('config.php');

try {
    // Veritabanına bağlan
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // POST isteği ile gelen ürün ID'sini al
    $urun_id = isset($_POST['urun_id']) ? intval($_POST['urun_id']) : 0; //integer a cast ettim.

    if ($urun_id <= 0) {
        hata_mesaji("Geçersiz ürün ID'si."); //hatamesajı fonksiyonu ekledim
    }

    // Sepeti oturumdan al
    if (isset($_SESSION['sepet'])) {
        $sepet = $_SESSION['sepet'];

        // Ürün sepette varsa, adedini 1 azalt veya ürünü kaldır
        if (isset($sepet[$urun_id])) {
            $sepet[$urun_id]--; // Önce adedi azalt
            if ($sepet[$urun_id] <= 0) {
                unset($sepet[$urun_id]); // Adet 0 veya daha azsa ürünü kaldır
            }
            $_SESSION['sepet'] = $sepet; // Güncellenmiş sepeti oturuma geri yaz

            // Başarılı yanıt gönder
            header('Content-Type: application/json');
            echo json_encode(['durum' => 'success', 'mesaj' => 'Ürün sepetten çıkarıldı.']);
        } else {
            hata_mesaji("Ürün sepette bulunamadı."); //hatamesajı fonksiyonu ekledim
        }
    } else {
        hata_mesaji("Sepet zaten boş."); //hatamesajı fonksiyonu ekledim
    }
} catch (PDOException $e) {
    // Hata durumunda JSON olarak hata mesajı gönder
    hata_mesaji("Veritabanı hatası: " . $e->getMessage()); //hatamesajı fonksiyonu ekledim
}

//hatamesajı fonksiyonu
function hata_mesaji($mesaj){
    header('Content-Type: application/json');
    echo json_encode(['durum' => 'error', 'mesaj' => $mesaj]);
    exit();
}
?>
