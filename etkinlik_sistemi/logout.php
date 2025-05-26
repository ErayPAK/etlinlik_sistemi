<?php
session_start();
session_destroy();
header("Location: giris.html"); // Kullanıcı giriş sayfasına yönlendir
exit();
?>