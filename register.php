<?php
// Kişi 2'nin DB sınıfını dahil et (classes/DB.php)
require_once 'classes/DB.php'; 
$db = new DB(); // DB bağlantısını kur

$hata_mesaji = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Veri Doğrulama ve Alma
    $email = trim($_POST['email']);
    $sifre = trim($_POST['sifre']);
    $ad_soyad = trim($_POST['ad_soyad']);
    $kullanici_tipi = $_POST['kullanici_tipi'];

    if (empty($email) || empty($sifre) || empty($ad_soyad)) {
        $hata_mesaji = "Lütfen tüm alanları doldurun.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $hata_mesaji = "Geçersiz e-posta formatı.";
    } else {
        // 2. E-posta Tekrarlama Kontrolü
        $db->query("SELECT kullanici_id FROM kullanici WHERE email = :e");
        $db->bind(':e', $email);
        $kullanici_var = $db->single();

        if ($kullanici_var) {
            $hata_mesaji = "Bu e-posta adresi zaten kayıtlı.";
        } else {
            try {
                // 3. Şifreleme (HASHING - En Önemli Güvenlik Adımı!)
                $hashed_password = password_hash($sifre, PASSWORD_DEFAULT);

                // 4. Veritabanına Ekleme (Prepared Statement kullanarak)
                $db->query("INSERT INTO kullanici (email, sifre, ad_soyad, kullanici_tipi) VALUES (:e, :s, :a, :t)");
                $db->bind(':e', $email);
                $db->bind(':s', $hashed_password);
                $db->bind(':a', $ad_soyad);
                $db->bind(':t', $kullanici_tipi);
                $db->execute();

                // Başarılı Yönlendirme
                header('Location: login.php?success=1');
                exit;

            } catch (Exception $e) {
                // DB hatası (Örn: Bağlantı sorunu, SQL syntax hatası)
                $hata_mesaji = "Kayıt sırasında teknik bir hata oluştu. Lütfen tekrar deneyin.";
            }
        }
    }
}
?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol - Gıda Atığı Platformu</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <div class="container">
        <h2>Kayıt Ol</h2>
        <?php if ($hata_mesaji): ?>
            <p style="color: red;"><?= $hata_mesaji ?></p>
        <?php endif; ?>
        
        <form action="register.php" method="POST">
            
            <label for="ad_soyad">Ad Soyad:</label>
            <input type="text" id="ad_soyad" name="ad_soyad" required>

            <label for="email">E-posta:</label>
            <input type="email" id="email" name="email" required>

            <label for="sifre">Şifre:</label>
            <input type="password" id="sifre" name="sifre" required>

            <label for="kullanici_tipi">Kullanıcı Tipi:</label>
            <select id="kullanici_tipi" name="kullanici_tipi" required>
                <option value="donor">Bağışçı (İşletme)</option>
                <option value="receiver">Alıcı (STK/Birey)</option>
                </select>

            <button type="submit">HESAP OLUŞTUR</button>
        </form>
        <p>Zaten hesabınız var mı? <a href="login.php">Giriş Yapın</a></p>
    </div>
</body>
</html>
