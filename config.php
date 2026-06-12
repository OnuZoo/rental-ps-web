<?php
// 1. Mengambil data dari Environment Variables Vercel
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 4000; // Default ke 4000 jika kosong

// 2. Inisialisasi koneksi
$koneksi = mysqli_init();

// 3. Mengatur CA_PATH untuk SSL (Wajib untuk TiDB)
// Path ini adalah lokasi default sertifikat keamanan di server Linux (Vercel)
$ssl_ca = '/etc/ssl/certs/ca-certificates.crt'; 
mysqli_ssl_set($koneksi, NULL, NULL, $ssl_ca, NULL, NULL);

// 4. Melakukan koneksi ke database
mysqli_real_connect($koneksi, $host, $user, $password, $database, $port);

// 5. Memeriksa apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
