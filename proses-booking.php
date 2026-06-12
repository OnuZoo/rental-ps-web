<?php
require 'config.php';

if (isset($_POST['submit_booking'])) {
    
    $nama_customer = $_POST['nama_customer'];
    $no_whatsapp   = $_POST['no_whatsapp'];
    $unit_id       = $_POST['unit_id'];
    $waktu_mulai   = $_POST['waktu_mulai'];
    $durasi_jam    = $_POST['durasi_jam'];

    // Hitung Total Harga
    $query_harga = mysqli_query($koneksi, "SELECT harga_per_jam FROM ps_units WHERE id = '$unit_id'");
    $data_unit   = mysqli_fetch_assoc($query_harga);
    $harga_per_jam = $data_unit['harga_per_jam'];
    $total_harga = $harga_per_jam * $durasi_jam;

    // Simpan data
    $query_insert = "INSERT INTO booking (nama_customer, no_whatsapp, unit_id, waktu_mulai, durasi_jam, harga, status) 
                     VALUES ('$nama_customer', '$no_whatsapp', '$unit_id', '$waktu_mulai', '$durasi_jam', '$total_harga', 'Pending')";

    if (mysqli_query($koneksi, $query_insert)) {
        // INI BAGIAN PENTING: Mengambil ID transaksi yang baru saja dibuat oleh MySQL
        $id_baru = mysqli_insert_id($koneksi);
        
        // Lempar ke halaman sukses sambil membawa ID tersebut
        header("Location: sukses-booking.php?id=" . $id_baru);
        exit;
    } else {
        echo "Gagal membuat booking: " . mysqli_error($koneksi);
    }

} else {
    header("Location: index.php");
}
?>