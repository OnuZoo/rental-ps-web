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

// 6. Memilih database
if (!mysqli_select_db($koneksi, $database)) {
    die("Database '" . $database . "' tidak ditemukan: " . mysqli_error($koneksi));
}
?>