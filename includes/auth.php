<?php
// Oturum başlatılmamışsa başlat (Her sayfanın başında gereklidir)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Güvenlik Kontrolü Fonksiyonu
// Kullanıcının oturum açıp açmadığını ve rolünün izin verilenler arasında olup olmadığını kontrol eder.
function check_auth($allowed_roles = []) {
    
    // 1. Giriş Kontrolü
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        // Oturum açılmamışsa giriş sayfasına yönlendir
        header('Location: login.php');
        exit;
    }

    $current_role = $_SESSION['user_role'];
    
    // 2. Rol Kontrolü
    if (!empty($allowed_roles)) {
        if (!in_array($current_role, $allowed_roles)) {
            
            // İzin verilmeyen role sahipse, kendi dashboard'una yönlendir
            if ($current_role == 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: dashboard.php');
            }
            exit;
        }
    }
}

// NOT: Buraya başka güvenlik/yardımcı fonksiyonlar eklenebilir.
?>