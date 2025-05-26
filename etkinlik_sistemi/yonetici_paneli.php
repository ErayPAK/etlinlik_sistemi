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

// Kullanıcı onay işlemi
if (isset($_GET['onayla']) && is_numeric($_GET['onayla'])) {
    $kullanici_id = $_GET['onayla'];
    $sql_onayla = "UPDATE kullanicilar SET onay_durumu = 1 WHERE id = :id";
    $stmt_onayla = $conn->prepare($sql_onayla);
    $stmt_onayla->bindParam(':id', $kullanici_id, PDO::PARAM_INT);
    if ($stmt_onayla->execute()) {
        echo '<div style="color: green; font-weight: bold;">Kullanıcı ID ' . $kullanici_id . ' onaylandı.</div><br>';
        // Onay sonrası listeyi güncellemek için sayfayı yeniden yükle
        header("refresh:1;url=yonetici_paneli.php");
        exit();
    } else {
        echo '<div style="color: red; font-weight: bold;">Kullanıcı onayı sırasında bir hata oluştu.</div><br>';
    }
}

// Kullanıcı silme işlemi
if (isset($_GET['sil_kullanici']) && is_numeric($_GET['sil_kullanici'])) {
    $kullanici_id = $_GET['sil_kullanici'];
    $sql_sil_kullanici = "DELETE FROM kullanicilar WHERE id = :id";
    $stmt_sil_kullanici = $conn->prepare($sql_sil_kullanici);
    $stmt_sil_kullanici->bindParam(':id', $kullanici_id, PDO::PARAM_INT);
    if ($stmt_sil_kullanici->execute()) {
        echo '<div style="color: green; font-weight: bold;">Kullanıcı ID ' . $kullanici_id . ' silindi.</div><br>';
        header("refresh:1;url=yonetici_paneli.php");
        exit();
    } else {
        echo '<div style="color: red; font-weight: bold;">Kullanıcı silinirken bir hata oluştu.</div><br>';
    }
}

// Etkinlik silme işlemi
if (isset($_GET['sil_etkinlik']) && is_numeric($_GET['sil_etkinlik'])) {
    $etkinlik_id = $_GET['sil_etkinlik'];
    $sql_sil_etkinlik = "DELETE FROM etkinlikler WHERE id = :id";
    $stmt_sil_etkinlik = $conn->prepare($sql_sil_etkinlik);
    $stmt_sil_etkinlik->bindParam(':id', $etkinlik_id, PDO::PARAM_INT);
    if ($stmt_sil_etkinlik->execute()) {
        echo '<div style="color: green; font-weight: bold;">Etkinlik ID ' . $etkinlik_id . ' silindi.</div><br>';
        header("refresh:1;url=yonetici_paneli.php");
        exit();
    } else {
        echo '<div style="color: red; font-weight: bold;">Etkinlik silinirken bir hata oluştu.</div><br>';
    }
}

// Duyuru silme işlemi
if (isset($_GET['sil_duyuru']) && is_numeric($_GET['sil_duyuru'])) {
    $duyuru_id = $_GET['sil_duyuru'];
    $sql_sil_duyuru = "DELETE FROM duyurular WHERE id = :id";
    $stmt_sil_duyuru = $conn->prepare($sql_sil_duyuru);
    $stmt_sil_duyuru->bindParam(':id', $duyuru_id, PDO::PARAM_INT);
    if ($stmt_sil_duyuru->execute()) {
        echo '<div style="color: green; font-weight: bold;">Duyuru ID ' . $duyuru_id . ' silindi.</div><br>';
        header("refresh:1;url=yonetici_paneli.php");
        exit();
    } else {
        echo '<div style="color: red; font-weight: bold;">Duyuru silinirken bir hata oluştu.</div><br>';
    }
}

// Onay bekleyen kullanıcıları listele
$sql_bekleyen = "SELECT id, email FROM kullanicilar WHERE onay_durumu = 0";
$stmt_bekleyen = $conn->prepare($sql_bekleyen);
$stmt_bekleyen->execute();
$bekleyen_kullanicilar = $stmt_bekleyen->fetchAll(PDO::FETCH_ASSOC);

// Tüm kullanıcıları listele
$sql_tum_kullanicilar = "SELECT id, email, onay_durumu FROM kullanicilar";
$stmt_tum_kullanicilar = $conn->prepare($sql_tum_kullanicilar);
$stmt_tum_kullanicilar->execute();
$tum_kullanicilar = $stmt_tum_kullanicilar->fetchAll(PDO::FETCH_ASSOC);

// Tüm etkinlikleri listele
$sql_tum_etkinlikler = "SELECT id, baslik, tarih FROM etkinlikler";
$stmt_tum_etkinlikler = $conn->prepare($sql_tum_etkinlikler);
$stmt_tum_etkinlikler->execute();
$tum_etkinlikler = $stmt_tum_etkinlikler->fetchAll(PDO::FETCH_ASSOC);

// Tüm duyuruları listele
$sql_tum_duyurular = "SELECT id, baslik FROM duyurular";
$stmt_tum_duyurular = $conn->prepare($sql_tum_duyurular);
$stmt_tum_duyurular->execute();
$tum_duyurular = $stmt_tum_duyurular->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Paneli</title>
    <style>
        body { font-family: sans-serif; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        a { text-decoration: none; padding: 5px 10px; background-color: #007bff; color: white; border-radius: 5px; }
        a:hover { background-color: #0056b3; }
        .sil-butonu { background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
        .sil-butonu:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <h1>Yönetici Paneli</h1>

    <h2>Onay Bekleyen Kullanıcılar</h2>
    <?php if (empty($bekleyen_kullanicilar)): ?>
        <p>Onay bekleyen kullanıcı bulunmuyor.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>E-posta Adresi</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bekleyen_kullanicilar as $kullanici): ?>
                    <tr>
                        <td><?php echo $kullanici['id']; ?></td>
                        <td><?php echo $kullanici['email']; ?></td>
                        <td><a href="?onayla=<?php echo $kullanici['id']; ?>">Onayla</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Tüm Kullanıcılar</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>E-posta Adresi</th>
                <th>Onay Durumu</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tum_kullanicilar as $kullanici): ?>
                <tr>
                    <td><?php echo $kullanici['id']; ?></td>
                    <td><?php echo $kullanici['email']; ?></td>
                    <td><?php echo ($kullanici['onay_durumu'] == 1) ? 'Onaylı' : 'Onay Bekliyor'; ?></td>
                    <td><a href="?sil_kullanici=<?php echo $kullanici['id']; ?>" class="sil-butonu">Sil</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Tüm Etkinlikler</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Başlık</th>
                <th>Tarih</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tum_etkinlikler as $etkinlik): ?>
                <tr>
                    <td><?php echo $etkinlik['id']; ?></td>
                    <td><?php echo $etkinlik['baslik']; ?></td>
                    <td><?php echo $etkinlik['tarih']; ?></td>
                    <td>
                        <a href="etkinlik_duzenle.php?id=<?php echo $etkinlik['id']; ?>">Düzenle</a> |
                        <a href="?sil_etkinlik=<?php echo $etkinlik['id']; ?>" class="sil-butonu">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Tüm Duyurular</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Başlık</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tum_duyurular as $duyuru): ?>
                <tr>
                    <td><?php echo $duyuru['id']; ?></td>
                    <td><?php echo $duyuru['baslik']; ?></td>
                    <td>
                        <a href="duyuru_duzenle.php?id=<?php echo $duyuru['id']; ?>">Düzenle</a> |
                        <a href="?sil_duyuru=<?php echo $duyuru['id']; ?>" class="sil-butonu">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Yeni Etkinlik Ekle</h2>
    <form action="backend/etkinlik_ekle.php" method="POST">
        <div>
            <label for="etkinlik_baslik">Başlık:</label><br>
            <input type="text" id="etkinlik_baslik" name="baslik" required style="width: 80%;">
        </div>
        <div>
            <label for="etkinlik_aciklama">Açıklama:</label><br>
            <textarea id="etkinlik_aciklama" name="aciklama" required style="width: 80%; height: 100px;"></textarea>
        </div>
        <div>
            <label for="etkinlik_tarih">Tarih:</label><br>
            <input type="date" id="etkinlik_tarih" name="tarih" required>
        </div>
        <div>
            <label for="etkinlik_tur">Tür:</label><br>
            <input type="text" id="etkinlik_tur" name="tur" required style="width: 80%;">
        </div>
        <div>
            <label for="etkinlik_gorsel">Görsel URL (isteğe bağlı):</label><br>
            <input type="url" id="etkinlik_gorsel" name="gorsel_url" style="width: 80%;">
        </div>
        <button type="submit">Etkinlik Ekle</button>
    </form>

    <h2>Yeni Duyuru Ekle</h2>
    <form action="backend/duyuru_ekle.php" method="POST">
        <div>
            <label for="duyuru_baslik">Başlık:</label><br>
            <input type="text" id="duyuru_baslik" name="baslik" required style="width: 80%;">
        </div>
        <div>
            <label for="duyuru_icerik">İçerik:</label><br>
            <textarea id="duyuru_icerik" name="icerik" required style="width: 80%; height: 100px;"></textarea>
        </div>
        <button type="submit">Duyuru Ekle</button>
    </form>

    <p><a href="logout.php">Çıkış</a></p>
</body>
</html>
