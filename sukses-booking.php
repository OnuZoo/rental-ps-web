<?php
require 'config.php';

// Cek apakah ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_booking = $_GET['id'];

// Ambil data booking beserta detail unit PS-nya menggunakan JOIN
$query = mysqli_query($koneksi, "
    SELECT booking.*, ps_units.nomer_unit, ps_units.tipe 
    FROM booking 
    JOIN ps_units ON booking.unit_id = ps_units.id 
    WHERE booking.id = '$id_booking'
");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan
if (!$data) {
    header("Location: index.php");
    exit;
}

// BIKIN KODE BOOKING OTOMATIS
$tanggal_format = date('Ymd', strtotime($data['created_at']));
$kode_booking = "GAY-" . $tanggal_format . "-" . str_pad($data['id'], 3, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Sukses - Rental PS</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- CSS Eksternal -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-5">

                <div class="ticket">

                    <!-- Header Sukses -->
                    <div class="text-center mb-3">
                        <div class="success-icon fs-1">✅</div>
                        <h2 class="mt-1 mb-1">Booking Berhasil!</h2>
                        <p class="text-secondary mb-0">
                            Tunjukkan kode di bawah ini kepada admin saat tiba di rental.
                        </p>
                    </div>

                    <!-- Kode Booking -->
                    <div class="kode-box my-3">
                        <?php echo $kode_booking; ?>
                    </div>

                    <!-- Detail Baris -->
                    <div class="detail-row">
                        <span>Nama:</span>
                        <strong><?php echo $data['nama_customer']; ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Unit PS:</span>
                        <strong><?php echo $data['nomer_unit'] . " (" . $data['tipe'] . ")"; ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Jadwal Main:</span>
                        <strong><?php echo date('d-m-Y H:i', strtotime($data['waktu_mulai'])); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Durasi:</span>
                        <strong><?php echo $data['durasi_jam']; ?> Jam</strong>
                    </div>
                    <div class="detail-row">
                        <span>Status:</span>
                        <strong style="color: orange;"><?php echo $data['status']; ?></strong>
                    </div>

                    <!-- Total Bayar -->
                    <div class="total-row mt-2">
                        <span>TOTAL BAYAR:</span>
                        <span>Rp<?php echo number_format($data['harga'], 0, ',', '.'); ?></span>
                    </div>

                    <!-- Tombol Kembali -->
                    <a href="index.php" class="btn-home d-block text-center mt-3">
                        Kembali ke Halaman Utama
                    </a>

                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>