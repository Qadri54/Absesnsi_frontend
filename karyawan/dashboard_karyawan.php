<?php 
session_start();

// Cek login karyawan
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'karyawan') {
  header("Location: ../login.php");
  exit;
}

include '../config.php';

$id_karyawan = $_SESSION['user']['id'];
$nama = $_SESSION['user']['nama'];
$tanggal = date('Y-m-d');

// Ambil data absensi hari ini dari API Laravel
$absenHariIni = null;
$api_url = "$api_base_url/absensi/$id_karyawan?role=karyawan&user_id=$id_karyawan";
$response = @file_get_contents($api_url);
if ($response !== false) {
  $result = json_decode($response, true);
  if (isset($result['data']) && is_array($result['data'])) {
    foreach ($result['data'] as $row) {
      if (isset($row['tanggal']) && $row['tanggal'] === $tanggal) {
        $absenHariIni = $row;
        break;
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Karyawan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
  <!-- Sidebar CSS -->
  <link rel="stylesheet" href="../css/sidebar_karyawan.css">

 <link rel="stylesheet" href="../css/dashboard_karyawan.css">

</head>
<body>

<?php include 'sidebar_karyawan.php'; ?>

<!-- Konten Utama -->
<div class="container mt-4">
  <div class="card mx-auto shadow-sm" style="max-width: 500px;">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0">Halo, <?= htmlspecialchars($nama) ?></h5>
    </div>
    <div class="card-body">
      <p><strong>Tanggal:</strong> <?= date('l, d F Y') ?></p>

      <?php if ($absenHariIni): ?>
        <div class="alert alert-success">
          <p class="mb-2"><strong>Kamu sudah absen hari ini</strong></p>
          <ul class="mb-0">
            <li><strong>Status:</strong> <?= ucfirst(htmlspecialchars($absenHariIni['status'])) ?></li>
            <li><strong>Jam Masuk:</strong> <?= htmlspecialchars($absenHariIni['jam_masuk'] ?? '-') ?></li>
            <li><strong>Keterangan:</strong> <?= htmlspecialchars($absenHariIni['keterangan'] ?? '-') ?></li>
            <li><strong>Bukti:</strong>
              <?php
                if (!empty($absenHariIni['bukti'])) {
                  $baseUrl = str_replace('/api', '', $api_base_url);
                  $buktiUrl = rtrim($baseUrl, '/') . '/storage/' . ltrim($absenHariIni['bukti'], '/');
                  echo '<a href="' . $buktiUrl . '" class="btn btn-sm btn-outline-info" target="_blank">Lihat</a>';
                } else {
                  echo '-';
                }
              ?>
            </li>
          </ul>
        </div>
      <?php else: ?>
        <!-- Form Absen Masuk -->
        <form action="proses_absen.php" method="POST" class="mb-3" onsubmit="return ambilLokasiGPS();">
          <input type="hidden" name="id_karyawan" value="<?= $id_karyawan ?>">
          <input type="hidden" id="latitude" name="latitude">
          <input type="hidden" id="longitude" name="longitude">
          <button type="submit" class="btn btn-green w-100 mb-2">Absen Masuk Sekarang</button>
        </form>

        <!-- Tombol Izin -->
        <button class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#popupIzin">Tidak Hadir (Izin/Sakit)</button>
      <?php endif; ?>

      <a href="data_absensi.php" class="btn btn-outline-primary w-100 mt-3">Lihat Riwayat Absensi</a>
    </div>
  </div>
</div>

<!-- Modal Izin/Sakit -->
<div class="modal fade" id="popupIzin" tabindex="-1" aria-labelledby="popupIzinLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="proses_izin.php" method="POST" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title" id="popupIzinLabel">Form Tidak Hadir</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_karyawan" value="<?= $id_karyawan ?>">

        <div class="mb-3">
          <label class="form-label text-success">Status</label>
          <select name="status" class="form-select" required>
            <option value="izin">Izin</option>
            <option value="sakit">Sakit</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label text-success">Keterangan</label>
          <textarea name="keterangan" class="form-control" placeholder="Tulis alasan..." required></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label text-success">Upload Bukti (opsional)</label>
          <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-green">Kirim</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Script Lokasi GPS -->
<!-- <script>
function ambilLokasiGPS() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
      document.getElementById('latitude').value = pos.coords.latitude;
      document.getElementById('longitude').value = pos.coords.longitude;
      document.forms[0].submit();
    }, function(error) {
      alert('Gagal mengambil lokasi. Aktifkan GPS dan izinkan akses lokasi.');
    });
    return false;
  } else {
    alert('Browser tidak mendukung GPS.');
    return false;
  }
}
</script> -->

<!-- Bootstrap JS -->
<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
