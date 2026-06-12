<?php
session_start();
require 'config.php';

// Cek keamanan menggunakan COOKIE
if (!isset($_COOKIE['status_login']) || $_COOKIE['status_login'] != 'true') {
    header("Location: login.php");
    exit;
}

// 1. hapus data
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    $hapus_query = mysqli_query($koneksi, "DELETE FROM booking WHERE id = '$id_hapus'");
    
    if ($hapus_query) {
        header("Location: admin-pesanan.php");
        exit;
    } else {
        echo "<script>alert('Gagal menghapus data!');</script>";
    }
}

// 2. ubah status
if (isset($_POST['ubah_status'])) {
    $id_booking = $_POST['id_booking'];
    $status_baru = $_POST['status_baru'];
    
    $update_query = mysqli_query($koneksi, "UPDATE booking SET status = '$status_baru' WHERE id = '$id_booking'");
    
    if ($update_query) {
        header("Location: admin-pesanan.php");
        exit;
    } else {
        echo "<script>alert('Gagal mengubah status!');</script>";
    }
}

// 3. query join
$query_booking = mysqli_query($koneksi, "
    SELECT booking.*, ps_units.nomer_unit, ps_units.tipe 
    FROM booking 
    JOIN ps_units ON booking.unit_id = ps_units.id 
    ORDER BY booking.waktu_mulai DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin Rental PS</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- CSS Eksternal -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container-fluid py-3">

        <!-- Navigasi Admin -->
        <div class="admin-nav mb-4">
            <a href="admin-pesanan.php" class="active">Kelola Pesanan</a>
            <a href="admin-keuangan.php">Laporan Keuangan</a>
            <a href="logout.php" class="logout" onclick="return confirm('Yakin ingin keluar?');">Logout</a>
        </div>

        <h2 class="mb-3">Daftar Pesanan (Booking)</h2>

        <!-- Wrapper scroll horizontal di mobile -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode Booking</th>
                        <th>Nama Customer</th>
                        <th>No. WhatsApp</th>
                        <th>Unit PS</th>
                        <th>Jadwal Main</th>
                        <th>Durasi</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Ubah Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query_booking)) { 
                        $tanggal_pesan = date('Ymd', strtotime($row['created_at']));
                        $kode_booking = "GAY-" . $tanggal_pesan . "-" . str_pad($row['id'], 3, '0', STR_PAD_LEFT);
                    ?>
                        <tr>
                            <td><strong><?php echo $kode_booking; ?></strong></td>
                            <td><?php echo $row['nama_customer']; ?></td>
                            <td>
                                <a href="https://wa.me/<?php echo $row['no_whatsapp']; ?>" target="_blank">
                                    <?php echo $row['no_whatsapp']; ?>
                                </a>
                            </td>
                            <td><?php echo $row['nomer_unit']; ?> (<?php echo $row['tipe']; ?>)</td>
                            <td><?php echo date('d-M-Y H:i', strtotime($row['waktu_mulai'])); ?></td>
                            <td><?php echo $row['durasi_jam']; ?> Jam</td>
                            <td>Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?></td>

                            <!-- Badge Status -->
                            <td>
                                <span class="badge <?php echo $row['status']; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>

                            <!-- Form Ubah Status -->
                            <td>
                                <form action="" method="POST" class="form-status d-flex gap-1">
                                    <input type="hidden" name="id_booking" value="<?php echo $row['id']; ?>">
                                    <select name="status_baru" class="form-select form-select-sm">
                                        <option value="Pending"   <?php if($row['status'] == 'Pending')   echo 'selected'; ?>>Pending</option>
                                        <option value="Confirmed" <?php if($row['status'] == 'Confirmed') echo 'selected'; ?>>Confirmed</option>
                                        <option value="Rejected"  <?php if($row['status'] == 'Rejected')  echo 'selected'; ?>>Rejected</option>
                                    </select>
                                    <button type="submit" name="ubah_status" class="btn-update">ACC</button>
                                </form>
                            </td>

                            <!-- Tombol Hapus -->
                            <td>
                                <a href="?hapus=<?php echo $row['id']; ?>" class="btn-hapus"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus data ini secara permanen? Jadwal ini akan kembali kosong di halaman pelanggan.');">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
