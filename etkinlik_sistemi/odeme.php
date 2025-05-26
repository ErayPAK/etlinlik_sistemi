<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Bilgileri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Ödeme Bilgileri</h1>
        <nav>
            <ul>
                <li><a href="anasayfa.html">Anasayfa</a></li>
                <li><a href="sepet.php">Sepet</a></li>
                <li><a href="logout.php">Çıkış</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="odeme-formu">
            <h2>Ödeme Bilgilerinizi Girin</h2>
            <form id="paymentForm">
                <div class="form-grup">
                    <label for="ad">Ad:</label>
                    <input type="text" id="ad" name="ad" required>
                </div>
                <div class="form-grup">
                    <label for="soyad">Soyad:</label>
                    <input type="text" id="soyad" name="soyad" required>
                </div>
                <div class="form-grup">
                    <label for="kartNumarasi">Kart Numarası:</label>
                    <input type="text" id="kartNumarasi" name="kartNumarasi" required>
                </div>
                <button type="submit">Ödemeyi Tamamla</button>
            </form>
            <div id="odeme-mesaji" style="display: none; margin-top: 20px; font-weight: bold;"></div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Etkinlik ve Duyuru Platformu</p>
    </footer>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Sayfanın yeniden yüklenmesini engelle

            var ad = document.getElementById('ad').value;
            var soyad = document.getElementById('soyad').value;
            var kartNumarasi = document.getElementById('kartNumarasi').value;

            var odemeMesajiDiv = document.getElementById('odeme-mesaji');
            odemeMesajiDiv.textContent = "Ödemeniz başarıyla alınmıştır. Teşekkür ederiz!";
            odemeMesajiDiv.style.display = 'block';

            // İsteğe bağlı: Sepeti temizleme veya başka işlemler
        });
    </script>
</body>
</html>