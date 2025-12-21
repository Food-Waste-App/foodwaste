<?php
// 1. Yol kontrolü: Klasör içinde olduğumuz için ../ ile bir üst klasöre çıkıyoruz
require_once '../includes/auth.php';
check_auth(['donor']); 

// 2. DB Bağlantısı
require_once '../classes/DB.php';
$db = new DB();

$basari_mesaji = '';
$hata_mesaji = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 3. Verileri Alma (SQL'deki sütun isimlerine göre: name, quantity, type, location)
    $urun_adi = trim($_POST['urun_adi']);
    $miktar   = (int)$_POST['miktar_stok'];
    $tur      = $_POST['tur']; // SQL'de 'food' veya 'other' olmalı
    $adres    = trim($_POST['adres']);
    $donor_id = $_SESSION['user_id']; 

    if ($miktar <= 0) {
        $hata_mesaji = "Miktar 0'dan büyük olmalıdır.";
    } else {
        try {
            // 4. Veritabanına Ekleme (Tablo adı: listings)
            $db->query("INSERT INTO listings (donor_id, name, quantity, type, location, status) 
                        VALUES (:di, :ua, :qty, :t, :loc, 'active')");
            
            $db->bind(':di', $donor_id);
            $db->bind(':ua', $urun_adi);
            $db->bind(':qty', $miktar);
            $db->bind(':t', $tur);
            $db->bind(':loc', $adres);

            $db->execute();
            
            $basari_mesaji = "Bağışınız başarıyla listelenmiştir!";
            
            // İstersen başarılı olduktan sonra listeleme sayfasına yönlendirebilirsin:
            // header("Refresh: 2; url=my_donations.php");

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
        <p style="color: green; font-weight: bold;"><?= $basari_mesaji ?></p>
    <?php endif; ?>
    
    <?php if ($hata_mesaji): ?>
        <p style="color: red;"><?= $hata_mesaji ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Ürün Adı:</label><br>
        <input type="text" name="urun_adi" required><br><br>

        <label>Miktar (Adet/Kg):</label><br>
        <input type="number" name="miktar_stok" min="1" required><br><br>

        <label>Ürün Türü:</label><br>
        <select name="tur" required>
            <option value="food">Gıda</option>
            <option value="other">Diğer</option>
        </select><br><br>

        <label>Alınış Adresi:</label><br>
        <input type="text" name="adres" placeholder="Örn: Antalya/Muratpaşa" required><br><br>

        <button type="submit">Bağışı Listele</button>
    </form>
</body>
</html>s