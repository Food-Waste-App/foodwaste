<?php
// GÜVENLİK KONTROLÜ: Sadece Donor'ler erişebilir
require_once 'includes/auth.php';
check_auth(['donor']); 

// DB Bağlantısını dahil et
require_once 'classes/DB.php';
$db = new DB();

$basari_mesaji = '';
$hata_mesaji = '';

// Form gönderildi mi kontrolü
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Verileri Alma ve Temizleme
    $urun_adi = trim($_POST['urun_adi']);
    $miktar_stok = (int)$_POST['miktar_stok'];
    $tur = $_POST['tur'];
    $aciliyet_seviyesi = $_POST['aciliyet_seviyesi'];
    $adres = trim($_POST['adres']);
    $donor_id = $_SESSION['user_id']; // Bağışçının ID'si oturumdan alınır!

    if ($miktar_stok <= 0) {
        $hata_mesaji = "Miktar 0'dan büyük olmalıdır.";
    } else {
        try {
            // 2. Veritabanına Ekleme (bagis tablosu)
            $db->query("INSERT INTO bagis (donor_id, urun_adi, miktar_stok, tur, aciliyet_seviyesi, adres) 
                       VALUES (:di, :ua, :ms, :t, :as, :ad)");
            
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
    <title>Bağış Listele</title>
</head>
<body>
    <h1>🍽️ Yeni Bağış Ekleme Formu</h1>
    <p><a href="dashboard.php">← Geri Dön</a></p>

    <?php if ($basari_mesaji): ?>
        <p style="color: green;"><?= $basari_mesaji ?></p>
    <?php endif; ?>
    <?php if ($hata_mesaji): ?>
        <p style="color: red;"><?= $hata_mesaji ?></p>
    <?php endif; ?>

    <form action="donor_add_donation.php" method="POST">
        
        <label for="urun_adi">Ürün Adı:</label><br>
        <input type="text" id="urun_adi" name="urun_adi" required><br><br>

        <label for="miktar_stok">Miktar (Adet/Kg):</label><br>
        <input type="number" id="miktar_stok" name="miktar_stok" min="1" required><br><br>

        <label for="tur">Ürün Türü:</label><br>
        <select id="tur" name="tur" required>
            <option value="Gıda">Gıda</option>
            <option value="İçecek">İçecek</option>
            <option value="Diğer">Diğer</option>
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