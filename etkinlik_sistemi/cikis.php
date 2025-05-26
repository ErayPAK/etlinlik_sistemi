<?php
    // Oturumu başlat
    session_start();

    // Oturumdaki tüm değişkenleri kaldır
    session_unset();

    // Oturumu yok et
    session_destroy();

    // Kullanıcıyı giriş sayfasına yönlendir
    header("Location: giris.html");
    exit;
?>
