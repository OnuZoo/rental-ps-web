<?php
// 1. Panggil koneksi dari config.php agar rapi
require 'config.php';

// 2. MENGAMBIL DATA UNIT PS (Ini baris yang hilang dan menyebabkan error)
$query_ps = mysqli_query($koneksi, "SELECT * FROM ps_units");
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
    <div class="header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1">🎮 Rental PlayStation</h1>
            <p class="text-muted mb-0">Silakan pilih unit PlayStation yang ingin Anda sewa.</p>
        </div>
        <a href="login.php" class="btn btn-dark">Login Admin</a>
    </div>

    <!-- Grid Kartu PS -->
    <div class="ps-grid">
        <?php 
        // Memastikan query berhasil dan ada datanya sebelum dilooping
        if ($query_ps && mysqli_num_rows($query_ps) > 0) {
            while ($unit = mysqli_fetch_assoc($query_ps)) { 
        ?>
            <a href="form-booking.php?id=<?php echo $unit['id']; ?>" class="ps-card">
                <div class="ps-card-icon">🎮</div>
                <div class="ps-title">Unit <?php echo htmlspecialchars($unit['nomer_unit']); ?></div>
                <div class="ps-type"><?php echo htmlspecialchars($unit['tipe']); ?></div>
                <div class="ps-price">
                    Rp<?php echo number_format($unit['harga_per_jam'], 0, ',', '.'); ?>/jam
                </div>
            </a>
        <?php 
            } 
        } else {
            echo "<p class='text-center w-100'>Belum ada unit PS yang tersedia atau gagal memuat data.</p>";
        }
        ?>
    </div>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>