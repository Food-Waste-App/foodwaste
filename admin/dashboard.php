<?php
require_once '../includes/auth.php';
check_auth(['admin']); // SADECE admin girebilir

require_once '../classes/DB.php';
$db = new DB();

// Örnek istatistik çekme (Backend arkadaşın burayı geliştirebilir)
$db->query("SELECT COUNT(*) as total_users FROM users");
$user_count = $db->single()['total_users'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
</head>
<body>
    <h1>🔒 Sistem Yönetim Paneli</h1>
    <p>Hoş Geldiniz, Admin!</p>
    
    <div class="stats">
        <p><strong>Toplam Kullanıcı:</strong> <?= $user_count ?></p>
    </div>

    <nav>
        <ul>
            <li><a href="verify_donors.php">Bağışçı Onayları</a></li>
            <li><a href="manage_users.php">Kullanıcı Yönetimi</a></li>
            <li><a href="../logout.php">Güvenli Çıkış</a></li>
        </ul>
    </nav>
</body>
</html>