<?php
// Hapus cookie dengan mengatur waktunya mundur (kadaluarsa)
setcookie('status_login', '', time() - 3600, "/");
setcookie('admin_user', '', time() - 3600, "/");

// Tendang kembali ke halaman login
header("Location: login.php");
exit;
?>
