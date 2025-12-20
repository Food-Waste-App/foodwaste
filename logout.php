<?php
// Oturum başlatma (Session'ı etkilemek için gerekli)
session_start();

// Oturum değişkenlerini temizleme
$_SESSION = array(); 

// Oturum çerezini de silme
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Oturumu tamamen sonlandırma
session_destroy();

// Kullanıcıyı giriş sayfasına yönlendirme
header("Location: login.php");
exit;
?>