<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Yönetici girişi yapılmamışsa yönlendir
if (!isset($_SESSION['yonetici_giris']) || $_SESSION['yonetici_giris'] !== true) {
    header("Location: yonetici_giris.html");
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

// Etkinlik ID'sini al
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $etkinlik_id = $_GET['id'];
} else {
    die("Geçersiz etkinlik ID.");
}

// Etkinlik bilgilerini al
$sql_etkinlik = "SELECT baslik, aciklama, tarih, tur, gorsel_url FROM etkinlikler WHERE id = :id";
$stmt_etkinlik = $conn->prepare($sql_etkinlik);
$stmt_etkinlik->bindParam(':id', $etkinlik_id, PDO::PARAM_INT);
$stmt_etkinlik->execute();
$etkinlik = $stmt_etkinlik->fetch(PDO::FETCH_ASSOC);

if (!$etkinlik) {
    die("Etkinlik bulunamadı.");
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etkinlik Düzenle</title>
    <style>
        body { font-family: sans-serif; }
        form { width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="date"], input[type="url"], textarea { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        textarea { height: 100px; }
        button { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>Etkinlik Düzenle</h1>

    <form action="backend/etkinlik_guncelle.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $etkinlik_id; ?>">
        <div>
            <label for="etkinlik_baslik">Başlık:</label><br>
            <input type="text" id="etkinlik_baslik" name="baslik" value="<?php echo htmlspecialchars($etkinlik['baslik']); ?>" required>
        </div>
        <div>
            <label for="etkinlik_aciklama">Açıklama:</label><br>
            <textarea id="etkinlik_aciklama" name="aciklama" required><?php echo htmlspecialchars($etkinlik['aciklama']); ?></textarea>
        </div>
        <div>
            <label for="etkinlik_tarih">Tarih:</label><br>
            <input type="date" id="etkinlik_tarih" name="tarih" value="<?php echo $etkinlik['tarih']; ?>" required>
        </div>
        <div>
            <label for="etkinlik_tur">Tür:</label><br>
            <input type="text" id="etkinlik_tur" name="tur" value="<?php echo htmlspecialchars($etkinlik['tur']); ?>" required>
        </div>
        <div>
            <label for="etkinlik_gorsel">Görsel URL (isteğe bağlı):</label><br>
            <input type="url" id="etkinlik_gorsel" name="gorsel_url" value="<?php echo htmlspecialchars($etkinlik['gorsel_url']); ?>">
        </div>
        <button type="submit">Etkinliği Güncelle</button>
    </form>

    <p><a href="yonetici_paneli.php">Yönetici Paneline Dön</a></p>
</body>
</html>
