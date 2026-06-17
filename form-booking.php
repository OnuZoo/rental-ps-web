<?php
require 'config.php';

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id_unit = $_GET['id'];

// Ambil data unit
$query = mysqli_query($koneksi, "SELECT * FROM ps_units WHERE id = '$id_unit'");
$unit = mysqli_fetch_assoc($query);
if (!$unit) { header("Location: index.php"); exit; }

$jam_buka = 10;
$jam_tutup = 22;
$tanggal_hari_ini = date('Y-m-d');

// Ambil jadwal terisi HARI INI (Ambil juga kolom 'status'-nya)
$query_booking = mysqli_query($koneksi, "
    SELECT waktu_mulai, durasi_jam, status 
    FROM booking 
    WHERE unit_id = '$id_unit' 
    AND DATE(waktu_mulai) = '$tanggal_hari_ini'
    AND status != 'Rejected'
");

$jadwal_terisi = [];    // Array PHP untuk membedakan warna (Kuning/Merah)
$jadwal_terisi_js = []; // Array murni angka untuk JavaScript (Radar Tabrakan)

while ($b = mysqli_fetch_assoc($query_booking)) {
    $jam_mulai = (int)date('H', strtotime($b['waktu_mulai']));
    $durasi = (int)$b['durasi_jam'];
    $status_booking = $b['status']; // 'Pending' atau 'Confirmed'
    
    // Masukkan jam dan statusnya ke array
    for ($i = 0; $i < $durasi; $i++) {
        $jam_ke = $jam_mulai + $i;
        $jadwal_terisi[$jam_ke] = $status_booking; // Contoh hasil: $jadwal_terisi[10] = 'Pending'
        $jadwal_terisi_js[] = $jam_ke; // Masukkan ke radar JS
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isi Form - Rental PS</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- CSS Eksternal (dikosongkan dulu, akan diisi nanti) -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-7">

                <div class="container-form">
                    <h2 class="text-center mt-0 mb-4">Formulir Pemesanan</h2>

                    <!-- Info Box: Nomor Unit & Harga -->
                    <div class="info-box mb-4">
                        <div>
                            <h3 class="mb-0"><?php echo $unit['nomer_unit']; ?></h3>
                            <span class="text-secondary"><?php echo $unit['tipe']; ?></span>
                        </div>
                        <div class="fw-bold" style="color: #1976d2;">
                            Rp<?php echo number_format($unit['harga_per_jam'], 0, ',', '.'); ?> / jam
                        </div>
                    </div>

                    <form action="proses-booking.php" method="POST">
                        <input type="hidden" name="unit_id" value="<?php echo $unit['id']; ?>">
                        <input type="hidden" name="waktu_mulai" id="waktu_mulai" required>

                        <!-- Nama Lengkap -->
                        <div class="mb-3">
                            <label for="nama_customer" class="form-label">Nama Lengkap:</label>
                            <input type="text" id="nama_customer" name="nama_customer" class="form-control" required>
                        </div>

                        <!-- Nomor WhatsApp -->
                        <div class="mb-3">
                            <label for="no_whatsapp" class="form-label">Nomor WhatsApp Aktif:</label>
                            <input type="text" id="no_whatsapp" name="no_whatsapp" class="form-control" required>
                        </div>

                        <!-- Durasi -->
                        <div class="mb-3">
                            <label for="durasi_jam" class="form-label">Durasi Main (Jam):</label>
                            <input type="number" id="durasi_jam" name="durasi_jam" class="form-control" min="1" value="1" oninput="cekKesesuaian()" required>
                        </div>

                        <!-- Pilih Jam -->
                        <label class="form-label">
                            Pilih Jam Mulai (Hari Ini: <?php echo date('d M Y'); ?>):
                        </label>
                        <p class="text-secondary mb-2" style="font-size: 12px;">
                            *Kotak Kuning = Sedang diproses pelanggan lain.<br>
                            *Kotak Merah = Sudah di-ACC (Penuh).
                        </p>

                        <div class="time-grid mb-3">
                            <?php
                            for ($jam = $jam_buka; $jam <= $jam_tutup; $jam++) {
                                $jam_format = str_pad($jam, 2, "0", STR_PAD_LEFT) . ":00";

                                if (array_key_exists($jam, $jadwal_terisi)) {
                                    $status_jam = $jadwal_terisi[$jam];

                                    if ($status_jam == 'Pending') {
                                        echo "<button type='button' class='btn-time pending' disabled>$jam_format</button>";
                                    } else if ($status_jam == 'Confirmed') {
                                        echo "<button type='button' class='btn-time booked' disabled>$jam_format</button>";
                                    }
                                } else {
                                    echo "<button type='button' class='btn-time tersedia' onclick=\"pilihJam(this, '$jam_format', $jam)\">$jam_format</button>";
                                }
                            }
                            ?>
                        </div>

                        <!-- Summary Box -->
                        <div class="summary mb-3" id="summary_box">
                            Status: Silakan pilih jam mulai.
                        </div>

                        <!-- Tombol Submit -->
                        <button type="submit" name="submit_booking" class="btn-submit w-100" id="btn_submit" disabled>
                            Selesaikan Pemesanan
                        </button>
                    </form>

                    <a href="index.php" class="d-block text-center mt-3 text-secondary">
                        &larr; Kembali Ganti PS
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const tanggalHariIni = "<?php echo $tanggal_hari_ini; ?>";
        const jadwalTerisiJS = <?php echo json_encode($jadwal_terisi_js); ?>;

        let jamTerpilihTeks = null;
        let jamTerpilihAngka = null;

        function pilihJam(elemen, jamTeks, jamAngka) {
            document.querySelectorAll('.btn-time').forEach(function(btn) {
                btn.classList.remove('selected');
            });

            elemen.classList.add('selected');
            jamTerpilihTeks = jamTeks;
            jamTerpilihAngka = jamAngka;

            cekKesesuaian();
        }
        
        const inputTelepon = document.querySelector('input[name="no_whatsapp"]');
        if (inputTelepon) {
            inputTelepon.addEventListener("input", function () {
            this.value = this.value.replace(/[^0-9]/g, "");
            });
        }

        function cekKesesuaian() {
            let inputWaktu = document.getElementById('waktu_mulai');
            let durasi = parseInt(document.getElementById('durasi_jam').value);
            let btnSubmit = document.getElementById('btn_submit');
            let summaryBox = document.getElementById('summary_box');

            if (jamTerpilihTeks === null) { return; }

            if (isNaN(durasi) || durasi < 1) {
                btnSubmit.disabled = true;
                summaryBox.innerHTML = "<span style='color:#d32f2f;'>Status: Masukkan durasi jam yang valid.</span>";
                return;
            }

            let tabrakan = false;
            for (let i = 0; i < durasi; i++) {
                let jamCek = jamTerpilihAngka + i;
                if (jadwalTerisiJS.includes(jamCek)) {
                    tabrakan = true;
                    break;
                }
            }

            if (tabrakan) {
                btnSubmit.disabled = true;
                inputWaktu.value = "";
                summaryBox.innerHTML = "<span style='color:#d32f2f;'>Status: Gagal! Durasi menabrak jadwal (Kuning/Merah) di depannya.</span>";
            } else {
                btnSubmit.disabled = false;
                inputWaktu.value = tanggalHariIni + " " + jamTerpilihTeks + ":00";
                summaryBox.innerHTML = "Status: Aman. Main " + durasi + " Jam (Dimulai pukul " + jamTerpilihTeks + ")";
            }
        }
    </script>

</body>
</html>
