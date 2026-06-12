<?php
session_start();

require 'config.php';

if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
    header("Location: admin-pesanan.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM admin WHERE username='$username' AND password='$password'"
    );

    if (mysqli_num_rows($query) > 0) {

        $_SESSION['status_login'] = true;
        $_SESSION['username'] = $username;

        header("Location: admin-pesanan.php");
        exit;

    } else {

        $error = "Username atau password salah!";

    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Rental PS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

<div class="login-card">

    <h2>Login Admin</h2>
    <p class="login-sub">Masuk untuk mengelola pesanan</p>

    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger" style="font-size:13px; padding:10px 14px; border-radius:7px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php } ?>

    <form action="" method="POST">

        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            required>

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            required>

        <button type="submit" name="login" class="btn-login">
            Masuk
        </button>

    </form>

    <div class="login-divider"></div>

    <a href="index.php" class="btn-back-login">
        ← Kembali ke Halaman Utama
    </a>

</div>

</body>
</html>