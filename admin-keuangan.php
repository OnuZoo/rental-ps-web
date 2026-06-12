<?php
session_start();
require 'config.php';

// Cek keamanan menggunakan COOKIE
if (!isset($_COOKIE['status_login']) || $_COOKIE['status_login'] != 'true') {
    header("Location: login.php");
    exit;
}

// 2. HITUNG TOTAL PENDAPATAN KESELURUHAN
$query_total = mysqli_query($koneksi, "
    SELECT SUM(harga) as total_semua 
    FROM booking 
    WHERE status = 'Confirmed'
");
$data_total = mysqli_fetch_assoc($query_total);
$total_pendapatan = $data_total['total_semua'] ? $data_total['total_semua'] : 0;


// 3. AMBIL DAFTAR TRANSAKSI SUKSES
$query_transaksi = mysqli_query($koneksi, "
    SELECT booking.*, ps_units.nomer_unit, ps_units.tipe 
    FROM booking 
    JOIN ps_units ON booking.unit_id = ps_units.id 
    WHERE booking.status = 'Confirmed'
    ORDER BY booking.waktu_mulai DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Admin Rental PS</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- CSS Eksternal -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container-fluid py-3">

        <!-- Navigasi Admin -->
        <div class="admin-nav mb-4">
            <a href="admin-pesanan.php">Kelola Pesanan</a>
            <a href="admin-keuangan.php" class="active">Laporan Keuangan</a>
            <a href="logout.php" class="logout" onclick="return confirm('Yakin ingin keluar?');">Logout</a>
        </div>

        <h2 class="mb-4">Laporan Keuangan</h2>

        <!-- Card Metrik -->
        <div class="row g-3 mb-4 justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card all-time h-100 text-center mx-auto">
                    <div class="card-body">
                        <h3 class="card-title">Total Pendapatan Keseluruhan</h3>
                        <div class="nominal">
                            Rp<?php echo number_format($total_pendapatan, 0, ',', '.'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="mb-3">Rincian Transaksi Selesai (Confirmed)</h3>

        <!-- Tabel Transaksi -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Waktu Main</th>
                        <th>Kode Booking</th>
                        <th>Nama Customer</th>
                        <th>Unit PS</th>
                        <th>Durasi</th>
                        <th>Pemasukan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($query_transaksi) > 0) {
                        while ($row = mysqli_fetch_assoc($query_transaksi)) { 
                            $tanggal_pesan = date('Ymd', strtotime($row['created_at']));
                            $kode_booking = "BKG-" . $tanggal_pesan . "-" . str_pad($row['id'], 3, '0', STR_PAD_LEFT);
                    ?>
                            <tr>
                                <td><?php echo date('d-M-Y H:i', strtotime($row['waktu_mulai'])); ?></td>
                                <td><strong><?php echo $kode_booking; ?></strong></td>
                                <td><?php echo $row['nama_customer']; ?></td>
                                <td><?php echo $row['nomer_unit']; ?> (<?php echo $row['tipe']; ?>)</td>
                                <td><?php echo $row['durasi_jam']; ?> Jam</td>
                                <td class="fw-bold" style="color: green;">
                                    + Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                    <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>Belum ada transaksi yang selesai.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
