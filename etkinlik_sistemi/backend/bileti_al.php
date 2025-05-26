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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['etkinlik_id'])) {
    $etkinlik_id = $_POST['etkinlik_id'];

    try {
        // Kontenjanı 1 azalt
        $stmt = $conn->prepare("UPDATE etkinlikler SET kontenjan = kontenjan - 1 WHERE id = :etkinlik_id AND kontenjan > 0");
        $stmt->bindParam(':etkinlik_id', $etkinlik_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Sepeti temizle
            $stmt_temizle = $conn->prepare("DELETE FROM sepet WHERE kullanici_id = :kullanici_id");
            $stmt_temizle->bindParam(':kullanici_id', $_SESSION['kullanici_id']);
            $stmt_temizle->execute();

            // Kontrol için ekleyin
            if ($stmt_temizle->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Bilet alındı ve sepet temizlendi.']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Bilet alındı, ancak sepet temizlenemedi.', 'sepet_temizleme_etkisi' => 0]); //Ek bilgi
            }
            
        } else {
            echo json_encode(['error' => 'Kontenjan yetersiz veya etkinlik bulunamadı.']);
            exit();
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Kontenjan güncellenirken hata oluştu: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['error' => 'Geçersiz istek.']);
    exit();
}
?>