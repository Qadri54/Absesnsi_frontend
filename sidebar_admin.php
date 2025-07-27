<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include 'config.php';

$fotoPath = 'https://via.placeholder.com/90';
$namaAdmin = 'Admin';

if (isset($_SESSION['user']['id']) && $_SESSION['user']['role'] === 'admin') {
  $id = $_SESSION['user']['id'];

  $apiUrl = "$api_base_url/admin?role=admin";
  $ch = curl_init($apiUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close($ch);

  $admins = json_decode($response, true);

  foreach ($admins as $admin) {
    if ($admin['id'] == $id) {
      $namaAdmin = $admin['nama_lengkap'];
      if (!empty($admin['foto'])) {
        $baseUrl = str_replace('/api', '', $api_base_url);
        $fotoPath = $baseUrl . "/uploads/foto_admin/" . $admin['foto'];
      }
      break;
    }
  }
}
?>

<!-- Bootstrap Lokal -->
<link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
<!-- Custom CSS Sidebar -->
<link rel="stylesheet" href="../css/sidebar.css">

<!-- Sidebar -->
<div class="sidebar d-flex flex-column align-items-center text-white">
  <div class="profile text-center">
    <img src="<?= $fotoPath ?>" alt="Foto Admin" class="rounded-circle border border-white" width="90" height="90" style="object-fit: cover;">
    <h3 class="mt-2"><?= htmlspecialchars($namaAdmin) ?></h3>
    <a href="../logout.php" class="logout-btn btn btn-danger btn-sm mt-2">Logout</a>
  </div>

  <ul class="menu nav flex-column mt-4 w-100 px-2">
    <li class="nav-item mb-2">
      <a href="../admin/dashboard_admin.php" class="nav-link text-white d-flex align-items-center">
        📊 <span class="ms-2">Dashboard</span>
      </a>
    </li>
    <li class="nav-item mb-2">
      <a href="../admin/data_admin.php" class="nav-link text-white d-flex align-items-center">
        👥 <span class="ms-2">Data Admin</span>
      </a>
    </li>
    <li class="nav-item mb-2">
      <a href="../admin/data_karyawan.php" class="nav-link text-white d-flex align-items-center">
        🧑‍🌾 <span class="ms-2">Data Karyawan</span>
      </a>
    </li>
    <li class="nav-item mb-2">
      <a href="../admin/data_absen.php" class="nav-link text-white d-flex align-items-center">
        🕒 <span class="ms-2">Data Absen</span>
      </a>
    </li>
  </ul>
</div>

<!-- Bootstrap JS Lokal (jika dibutuhkan) -->
<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
