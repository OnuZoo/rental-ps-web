<?php
// Hapus session_start() karena kita pakai Cookie
require 'config.php';

// Jika cookie login sudah ada, langsung lempar ke pesanan
if (isset($_COOKIE['status_login']) && $_COOKIE['status_login'] == 'true') {
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

        // Buat cookie yang bertahan selama 1 hari (86400 detik)
        setcookie('status_login', 'true', time() + 86400, "/");
        setcookie('admin_user', $username, time() + 86400, "/");

        header("Location: admin-pesanan.php");
        exit;

    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!-- HTML di bawahnya biarkan sama persis seperti sebelumnya -->
<!DOCTYPE html>
<html lang="id">
...

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
