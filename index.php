<?php
// 1. Mengambil data dari Environment Variables Vercel
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

// Beri fallback (cadangan) langsung ke 'rental_ps' jika variabel Vercel gagal terbaca
$database = getenv('DB_NAME') ?: 'rental_ps'; 
$port = getenv('DB_PORT') ?: 4000;

// 2. Inisialisasi koneksi
$koneksi = mysqli_init();

// 3. Mengatur CA_PATH untuk SSL (Wajib untuk TiDB)
$ssl_ca = '/etc/ssl/certs/ca-certificates.crt'; 
mysqli_ssl_set($koneksi, NULL, NULL, $ssl_ca, NULL, NULL);

// 4. Melakukan koneksi ke server database
mysqli_real_connect($koneksi, $host, $user, $password, $database, $port);

// 5. Memeriksa apakah koneksi ke server berhasil
if (!$koneksi) {
    die("Koneksi server gagal: " . mysqli_connect_error());
}

// 6. MEMAKSA memilih database (Ini solusi untuk error "No database selected")
if (!mysqli_select_db($koneksi, $database)) {
    die("Database '" . $database . "' tidak ditemukan: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental PS</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS Eksternal -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container py-5">

    <!-- Header: Judul & Tombol Login Admin -->
    <div class="header mb-4">
        <div>
            <h1 class="mb-1">🎮 Rental PlayStation</h1>
            <p class="text-muted mb-0">Silakan pilih unit PlayStation yang ingin Anda sewa.</p>
        </div>
        <a href="login.php" class="btn btn-dark">Login Admin</a>
    </div>

    <!-- Grid Kartu PS -->
    <div class="ps-grid">
        <?php while ($unit = mysqli_fetch_assoc($query_ps)) { ?>
            <a href="form-booking.php?id=<?php echo $unit['id']; ?>" class="ps-card">
                <div class="ps-card-icon">🎮</div>
                <div class="ps-title">Unit <?php echo $unit['nomer_unit']; ?></div>
                <div class="ps-type"><?php echo $unit['tipe']; ?></div>
                <div class="ps-price">
                    Rp<?php echo number_format($unit['harga_per_jam'], 0, ',', '.'); ?>/jam
                </div>
            </a>
        <?php } ?>
    </div>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
