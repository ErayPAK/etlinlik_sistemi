<?php
session_start();

// Hata ayıklama için (geliştirme sırasında kullanın, üretimde kaldırın!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bağlantısı için gerekli bilgileri ayrı bir dosyadan alıyoruz.
// Bu, güvenlik ve kodun daha düzenli olması için iyi bir uygulamadır.
require_once('config.php');

try {
    // Veritabanına bağlan
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Hata ayıklama modunu aç

    // Sepeti oturumdan al
    if (isset($_SESSION['sepet'])) {
        $sepet = $_SESSION['sepet'];
        // Sepet boşsa, boş bir dizi döndür
        if (empty($sepet)) {
             header('Content-Type: application/json');
             echo json_encode([]);
             exit();
        }

        // Sepetteki ürünleri veritabanından çekmek için bir sorgu hazırlayın.
        $urun_ids = array_keys($sepet); // Sepetteki ürün ID'lerini al
        $in_clause = implode(',', $urun_ids); // IN sorgusu için string oluştur.
        $sql = "SELECT id, baslik, bilet_fiyati, bilet_turu FROM biletler WHERE id IN ($in_clause)";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sonuçları sepet ile birleştirerek adet bilgisini ekle
        $sepet_urunler = [];
        foreach ($urunler as $urun) {
            $urun_id = $urun['id'];
            if (isset($sepet[$urun_id])) {
                $sepet_urunler[$urun_id] = [
                    'id' => $urun_id,
                    'baslik' => $urun['baslik'],
                    'bilet_fiyati' => $urun['bilet_fiyati'],
                    'bilet_turu' => $urun['bilet_turu'],
                    'adet' => $sepet[$urun_id] // Sepetteki adeti al
                ];
            }
        }
        // JSON olarak döndür
        header('Content-Type: application/json');
        echo json_encode($sepet_urunler);

    } else {
        // Sepet yoksa boş bir dizi döndür
        header('Content-Type: application/json');
        echo json_encode([]);
    }

} catch (PDOException $e) {
    // Hata durumunda JSON olarak hata mesajı döndür
    header('Content-Type: application/json');
    echo json_encode(['hata' => "Veritabanı hatası: " . $e->getMessage()]);
    exit();
}
?>
