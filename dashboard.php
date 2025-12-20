<?php
require_once 'includes/auth.php';
// Sadece 'donor' ve 'receiver' rollerine izin ver
check_auth(['donor', 'receiver']); 

$kullanici_tipi = $_SESSION['user_role'];
$kullanici_adi = $_SESSION['email']; // Veya veritabanından ad soyad çekilebilir

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | <?= ucfirst($kullanici_tipi) ?></title>
</head>
<body>
    <h1>Hoş Geldiniz, <?= $kullanici_adi ?> (Rol: <?= $kullanici_tipi ?>)</h1>
    
    <?php if ($kullanici_tipi == 'donor'): ?>
        <h2>🍽️ Bağışçı (Donor) Paneli</h2>
        <p>Burada yeni bağış listeleme formunuz olacak.</p>
        <p>Aktif Bağışlarınızın durumu görüntülenecek.</p>
    <?php elseif ($kullanici_tipi == 'receiver'): ?>
        <h2>🛒 Alıcı (Receiver) Paneli</h2>
        <p>Burada bağış listesini harita üzerinden göreceksiniz. (Aşama 8)</p>
        <p>Mevcut rezervasyonlarınız görüntülenecek.</p>
    <?php endif; ?>

    <p><a href="logout.php">Çıkış Yap</a></p>
</body>
</html>