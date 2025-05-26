<?php
session_start();
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['etkinlik_id']) && isset($_POST['bilet_turu'])) {
    $etkinlik_id = $_POST['etkinlik_id'];
    $bilet_turu = $_POST['bilet_turu'];

    // Etkinlik bilgilerini al
    $stmt_etkinlik = $conn->prepare("SELECT bilet_fiyati_tam, bilet_fiyati_ogrenci, kontenjan FROM etkinlikler WHERE id = :etkinlik_id");
    $stmt_etkinlik->bindParam(':etkinlik_id', $etkinlik_id);
    $stmt_etkinlik->execute();
    $etkinlik = $stmt_etkinlik->fetch(PDO::FETCH_ASSOC);

    if (!$etkinlik) {
        echo json_encode(['error' => 'Etkinlik bulunamadı']);
        exit();
    }

    // Bilet fiyatını belirle
    $bilet_fiyati = ($bilet_turu == 'Tam') ? $etkinlik['bilet_fiyati_tam'] : $etkinlik['bilet_fiyati_ogrenci'];
    $kontenjan = $etkinlik['kontenjan'];

     if ($kontenjan <= 0) {
        echo json_encode(['error' => 'Bu etkinlik için kontenjan kalmamıştır.']);
        exit();
    }

    // Sepete ekle
    try {
        $stmt = $conn->prepare("INSERT INTO sepet (kullanici_id, etkinlik_id, bilet_turu, bilet_fiyati, adet) VALUES (:kullanici_id, :etkinlik_id, :bilet_turu, :bilet_fiyati, 1)");
        $stmt->bindParam(':kullanici_id', $_SESSION['kullanici_id']);
        $stmt->bindParam(':etkinlik_id', $etkinlik_id);
        $stmt->bindParam(':bilet_turu', $bilet_turu);
        $stmt->bindParam(':bilet_fiyati', $bilet_fiyati);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Etkinlik sepete eklendi.']);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Sepete eklenirken hata oluştu: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['error' => 'Geçersiz istek.']);
    exit();
}
?>