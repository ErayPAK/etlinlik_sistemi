<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepetim</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <header>
        <h1>Sepetim</h1>
        <nav>
            <ul>
                <li><a href="anasayfa.html">Anasayfa</a></li>
                <li><a href="#">Ödeme Yap</a></li>
                <li><a href="logout.php">Çıkış</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="sepet-icerigi">
            <h2>Sepetinizdeki Ürünler</h2>
            <div id="sepet-listesi">
                </div>
            <div id="sepet-toplam">
                </div>
            <a href="odeme.php" id="odeme-yap-butonu" style="display: inline-block; padding: 10px 20px; background-color: #5cb85c; color: white; text-decoration: none; border-radius: 5px;">Ödeme Yap</a>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Etkinlik ve Duyuru Platformu</p>
    </footer>

    <script src="sepet.js"></script>
</body>
</html>
