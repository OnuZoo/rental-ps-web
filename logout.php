<?php
session_start();
// Hapus semua data session
session_unset();
session_destroy();

// Tendang kembali ke halaman login
header("Location: login.php");
exit;
?>