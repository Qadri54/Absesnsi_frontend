<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include '../config.php';

$fotoPath = 'https://via.placeholder.com/90'; // default
$namaKaryawan = 'Karyawan';

// Cek session login
if (isset($_SESSION['user']['id']) && $_SESSION['user']['role'] === 'karyawan') {
  $id = $_SESSION['user']['id'];

  // Ambil data karyawan dari API (endpoint show)
  $apiUrl = "$api_base_url/karyawan/$id?role=karyawan&user_id=$id";
  $ch = curl_init($apiUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close($ch);

  $result = json_decode($response, true);

  if (isset($result['data'])) {
    $karyawan = $result['data'];
    $namaKaryawan = $karyawan['nama'] ?? 'Karyawan';

    if (!empty($karyawan['foto'])) {
      $baseUrl = str_replace('/api', '', $api_base_url);
      $fotoPath = rtrim($baseUrl, '/') . "/uploads/foto_karyawan/" . ltrim($karyawan['foto'], '/');
    }
  }
}

$tanggal = date('d-m-Y');
?>

<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>

<!-- Navbar -->
<nav class="navbar shadow-sm sticky-top" style="background-color: #2d742f;">
  <div class="container-fluid">
    <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarKaryawan" aria-controls="sidebarKaryawan">
      <i class="fas fa-bars"></i>
    </button>
    <span class="navbar-brand mb-0 h6 text-white">Sistem Absensi</span>
  </div>
</nav>



<!-- Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start sidebar-green" sidebar-karyawan" tabindex="-1" id="sidebarKaryawan" aria-labelledby="sidebarLabel">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="sidebarLabel">Karyawan</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column justify-content-between">
    <div>
     <div class="text-center mb-4">
  <p class="mb-1 text-muted"><?= $tanggal ?></p>
  <img src="<?= $fotoPath ?>" alt="Foto" class="rounded-circle mb-2 border" width="80" height="80" style="object-fit: cover;">
  
  <!-- Nama karyawan dijadikan link -->
  <h6 class="mb-1">
    <a href="profile_karyawan.php" class="text-decoration-none text-dark fw-semibold">
      <?= htmlspecialchars($namaKaryawan) ?>
    </a>
  </h6>
  
<a href="../logout.php" class="btn logout-btn">Logout</a>
  <div class="text-muted mt-1" id="jamNow">00:00:00</div>
</div>


      <hr>

      <ul class="nav flex-column">
        <li class="nav-item mb-2">
          <a href="dashboard_karyawan.php" class="nav-link text-dark">
            <i class="fas fa-gauge me-2"></i> Dashboard
          </a>
        </li>
        <li class="nav-item mb-2">
          <a href="data_absensi.php" class="nav-link text-dark">
            <i class="fas fa-calendar-check me-2"></i> Riwayat Absensi
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>

<!-- Jam real-time -->
<script>
  function updateClock() {
    const now = new Date();
    document.getElementById("jamNow").textContent = now.toLocaleTimeString('en-GB');
  }
  setInterval(updateClock, 1000);
  updateClock();
</script>
