<?php
// Oturum başlatma (Oturum değişkenlerini kullanabilmek için her PHP sayfasının başında olmalı)
session_start();

// Kişi 2'nin DB sınıfını dahil et (classes/DB.php)
require_once 'classes/DB.php'; 

$db = new DB(); 
$hata_mesaji = '';
$basarili_mesaj = '';

// Kayıt başarılıysa (register.php'den gelindiyse) gösterilecek mesajı kontrol et
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $basarili_mesaj = "Kayıt başarılı! Şimdi giriş yapabilirsiniz.";
}

// Kullanıcı zaten giriş yapmışsa, yönlendir
if (isset($_SESSION['user_id'])) {
    // Rolüne göre dashboard sayfasına yönlendir. Bu sayfaları daha sonra oluşturacağız.
    header('Location: dashboard.php'); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $sifre = trim($_POST['sifre']);

    if (empty($email) || empty($sifre)) {
        $hata_mesaji = "Lütfen e-posta ve şifrenizi girin.";
    } else {
        try {
            // 1. E-posta ile Kullanıcıyı Veritabanından Çekme
            $db->query("SELECT kullanici_id, sifre, kullanici_tipi FROM kullanici WHERE email = :e");
            $db->bind(':e', $email);
            $user = $db->single(); // Tek bir kayıt çeker

            // 2. Kullanıcı bulundu mu kontrolü
            if ($user) {
                // 3. Şifre Doğrulama (password_verify) - Güvenlik İçin Kritik!
                // Girilen açık şifreyi, DB'deki hashlenmiş şifre ile karşılaştırır.
                if (password_verify($sifre, $user['sifre'])) {
                    
                    // Şifreler Eşleşti: Giriş Başarılı
                    session_start();
                    $_SESSION['user_id'] = $user['kullanici_id'];
                    $_SESSION['user_role'] = $user['kullanici_tipi'];
                    $_SESSION['email'] = $email; // Opsiyonel, kullanıcıyı selamlamak için tutulabilir

                    // Rolüne göre yönlendirme (İleride daha detaylı hale gelecek)
                    if ($user['kullanici_tipi'] == 'admin') {
                        header('Location: admin/dashboard.php');
                    } else {
                        header('Location: dashboard.php');
                    }
                    exit;

                } else {
                    // Şifre yanlış
                    $hata_mesaji = "E-posta veya şifre yanlış.";
                }
            } else {
                // Kullanıcı bulunamadı
                $hata_mesaji = "E-posta veya şifre yanlış.";
            }

        } catch (Exception $e) {
            // DB bağlantı hatası veya PDO hatası
            $hata_mesaji = "Giriş sırasında bir hata oluştu. Lütfen tekrar deneyin.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap - Gıda Atığı Platformu</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <div class="container">
        <h2>Giriş Yap</h2>
        <?php if ($basarili_mesaj): ?>
            <p style="color: green;"><?= $basarili_mesaj ?></p>
        <?php endif; ?>
        <?php if ($hata_mesaji): ?>
            <p style="color: red;"><?= $hata_mesaji ?></p>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            
            <label for="email">E-posta:</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">

            <label for="sifre">Şifre:</label>
            <input type="password" id="sifre" name="sifre" required>

            <button type="submit">GİRİŞ</button>
        </form>
        <p>Hesabınız yok mu? <a href="register.php">Kayıt Olun</a></p>
    </div>
</body>
</html>