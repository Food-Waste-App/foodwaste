<?php
// 1. DÜZELTME: Dosya 'donor' klasörünün içinde olduğu için 
// 'includes' ve 'classes' klasörlerine ulaşmak için bir üst dizine (../) çıkmalısın.
require_once '../includes/auth.php';
check_auth(['donor']); 

require_once '../classes/DB.php';
$db = new <?php
// 1. Üst klasördeki güvenlik ve DB dosyalarını dahil ediyoruz
require_once '../includes/auth.php';
check_auth(['donor']); 

require_once '../classes/DB.php';
$db = new DB();

$basari_mesaji = '';
$hata_mesaji = '';

// Form gönderildiğinde çalışacak kısım
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Formdan gelen veriler
    $urun_adi = trim($_POST['urun_adi']);
    $miktar_stok = (int)$_POST['miktar_stok'];
    $tur = $_POST['tur'];
    $aciliyet_seviyesi = $_POST['aciliyet_seviyesi'];
    $adres = trim($_POST['adres']);
    $donor_id = $_SESSION['user_id']; // Giriş yapan kullanıcının ID'si

    if ($miktar_stok <= 0) {
        $hata_mesaji = "Miktar 0'dan büyük olmalıdır.";
    } else {
        try {
            // SQL tablonuzdaki sütun isimlerine (name, quantity, type, location) göre ayarlandı
            $db->query("INSERT INTO listings (donor_id, name, quantity, type, priority_level, location, status) 
                       VALUES (:di, :ua, :ms, :t, :as, :ad, 'active')");
            
            $db->bind(':di', $donor_id);
            $db->bind(':ua', $urun_adi);
            $db->bind(':ms', $miktar_stok);
            $db->bind(':t', $tur);
            $db->bind(':as', $aciliyet_seviyesi);
            $db->bind(':ad', $adres);

            $db->execute();
            
            $basari_mesaji = "Bağışınız başarıyla listelenmiştir!";

        } catch (Exception $e) {
            $hata_mesaji = "Bağış eklenirken bir hata oluştu: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Bağış Ekle</title>
    <style>
        body { font-family: sans-serif; margin: 20px; line-height: 1.6; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; }
        input, select { padding: 8px; width: 300px; margin-top: 5px; }
        button { padding: 10px 20px; background-color: #28a745; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #218838; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>🍽️ Yeni Bağış Ekleme Formu</h1>
    <p><a href="../dashboard.php">← Panale Dön</a></p>

    <?php if ($basari_mesaji): ?>
        <p class="success"><?= $basari_mesaji ?></p>
    <?php endif; ?>
    
    <?php if ($hata_mesaji): ?>
        <p class="error"><?= $hata_mesaji ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="urun_adi">Ürün Adı:</label><br>
            <input type="text" id="urun_adi" name="urun_adi" placeholder="Örn: 5 Paket Makarna" required>
        </div>

        <div class="form-group">
            <label for="miktar_stok">Miktar (Adet/Kg):</label><br>
            <input type="number" id="miktar_stok" name="miktar_stok" min="1" required>
        </div>

        <div class="form-group">
            <label for="tur">Ürün Türü:</label><br>
            <select id="tur" name="tur" required>
                <option value="food">Gıda</option>
                <option value="drink">İçecek</option>
                <option value="other">Diğer</option>
            </select>
        </div>

        <div class="form-group">
            <label for="aciliyet_seviyesi">Aciliyet Seviyesi:</label><br>
            <select id="aciliyet_seviyesi" name="aciliyet_seviyesi" required>
                <option value="Düşük">Düşük (Bozulmaz)</option>
                <option value="Orta">Orta</option>
                <option value="Yüksek">Yüksek (Hemen Alınmalı)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="adres">Alınış Adresi:</label><br>
            <input type="text" id="adres" name="adres" placeholder="Sokak, No, İlçe" required>
        </div>

        <button type="submit">Bağışı Sisteme Ekle</button>
    </form>
</body>
</html>DB();

$basari_mesaji = '';
$hata_mesaji = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $urun_adi = trim($_POST['urun_adi']);
    $miktar_stok = (int)$_POST['miktar_stok'];
    $tur = $_POST['tur'];
    $aciliyet_seviyesi = $_POST['aciliyet_seviyesi'];
    $adres = trim($_POST['adres']);
    $donor_id = $_SESSION['user_id']; 

    if ($miktar_stok <= 0) {
        $hata_mesaji = "Miktar 0'dan büyük olmalıdır.";
    } else {
        try {
            // 2. DÜZELTME: SQL Tablo isminin 'listings' olduğundan emin ol (Senin SQL'de öyleydi)
            // Eğer tablo ismin hala 'bagis' ise burayı değiştirme.
            $db->query("INSERT INTO listings (donor_id, name, quantity, type, location, status) 
                        VALUES (:di, :ua, :ms, :t, :loc, 'active')");
            
            $db->bind(':di', $donor_id);
            $db->bind(':ua', $urun_adi);
            $db->bind(':ms', $miktar_stok);
            $db->bind(':t', $tur);
            $db->bind(':loc', $adres);

            $db->execute();
            
            $basari_mesaji = "Bağışınız başarıyla listelenmiştir!";

        } catch (Exception $e) {
            $hata_mesaji = "Bağış eklenirken bir hata oluştu: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bağış Listele</title>
</head>
<body>
    <h1>🍽️ Yeni Bağış Ekleme Formu</h1>
    <p><a href="../dashboard.php">← Geri Dön</a></p>

    <?php if ($basari_mesaji): ?>
        <p style="color: green;"><?= $basari_mesaji ?></p>
    <?php endif; ?>
    <?php if ($hata_mesaji): ?>
        <p style="color: red;"><?= $hata_mesaji ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        
        <label for="urun_adi">Ürün Adı:</label><br>
        <input type="text" id="urun_adi" name="urun_adi" required><br><br>

        <label for="miktar_stok">Miktar (Adet/Kg):</label><br>
        <input type="number" id="miktar_stok" name="miktar_stok" min="1" required><br><br>

        <label for="tur">Ürün Türü:</label><br>
        <select id="tur" name="tur" required>
            <option value="food">Gıda</option>
            <option value="drink">İçecek</option>
            <option value="other">Diğer</option>
        </select><br><br>

        <label for="aciliyet_seviyesi">Aciliyet Seviyesi:</label><br>
        <select id="aciliyet_seviyesi" name="aciliyet_seviyesi" required>
            <option value="Düşük">Düşük</option>
            <option value="Orta">Orta</option>
            <option value="Yüksek">Yüksek</option>
        </select><br><br>

        <label for="adres">Alınış Adresi:</label><br>
        <input type="text" id="adres" name="adres" required><br><br>

        <button type="submit">Bağışı Listele</button>
    </form>
</body>
</html>