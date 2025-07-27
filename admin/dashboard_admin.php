<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include '../config.php';

function fetchApiData($endpoint, $params = []) {
  global $api_base_url;
  $query = http_build_query($params);
  $url = "$api_base_url/$endpoint" . ($query ? "?$query" : "");

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'User-Agent: PHP-Request'
  ]);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($httpCode === 200 && $response !== false) {
    return json_decode($response, true);
  }

  return ['data' => []];
}

$dataAdmin    = fetchApiData("admin", ['role' => 'admin']);
$dataKaryawan = fetchApiData("karyawan", ['role' => 'admin']);
$dataAbsen    = fetchApiData("absensi", ['role' => 'admin']);

$admins    = $dataAdmin['data'] ?? $dataAdmin;
$karyawans = $dataKaryawan['data'] ?? $dataKaryawan;
$absens    = $dataAbsen['data'] ?? $dataAbsen;

$jumlahAdmin    = count($admins);
$jumlahKaryawan = count($karyawans);
$tanggalHariIni = date('Y-m-d');
$tanggal = new DateTime();

$absenHariIni = array_filter($absens, function ($item) use ($tanggal) {
  if (!isset($item['tanggal'])) return false;
  return json_encode(explode("T", $item["tanggal"])[0]) === json_encode($tanggal->format('Y-m-d'));
});

$jumlahAbsenHariIni = count($absenHariIni);
$jumlahTidakHadir = count($karyawans) - count($absenHariIni);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../css/dashboard_admin.css">
</head>
<body>
<div class="d-flex">
  <?php include '../sidebar_admin.php'; ?>

  <main class="main-content flex-grow-1 p-4">
    <h2 class="mb-4">Dashboard Admin</h2>
    <div class="row g-4">
      <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-start border-success border-5">
          <div class="card-body text-center">
            <h5 class="card-title text-success">Jumlah Admin</h5>
            <p class="display-6 fw-bold text-dark"><?= $jumlahAdmin ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-start border-success border-5">
          <div class="card-body text-center">
            <h5 class="card-title text-success">Jumlah Karyawan</h5>
            <p class="display-6 fw-bold text-dark"><?= $jumlahKaryawan ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-start border-success border-5">
          <div class="card-body text-center">
            <h5 class="card-title text-success">Jumlah Hadir Hari Ini</h5>
            <p class="display-6 fw-bold text-dark"><?= $jumlahAbsenHariIni ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-start border-success border-5">
          <div class="card-body text-center">
            <h5 class="card-title text-success">Jumlah Tidak Hadir</h5>
            <p class="display-6 fw-bold text-dark"><?= $jumlahTidakHadir ?></p>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
