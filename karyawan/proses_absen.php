<?php
session_start();
include '../config.php'; // berisi $api_base_url
include '../helpers/hitung_jarak.php'; // fungsi hitungJarak(lat1, lon1, lat2, lon2)

// Cek sesi login karyawan
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'karyawan') {
  echo "<script>alert('Akses ditolak. Silakan login ulang.'); window.location='../login.php';</script>";
  exit;
}

$id_karyawan = $_POST['id_karyawan'] ?? $_SESSION['user']['id'] ?? null;
if (!$id_karyawan) {
  echo "<script>alert('ID karyawan tidak ditemukan.'); window.location='dashboard_karyawan.php';</script>";
  exit;
}

// // Ambil lokasi GPS dari user
// $lat_user = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
// $lon_user = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;

// // Koordinat kantor (ganti sesuai lokasi sebenarnya)
// $lat_kantor = 3.5376317;
// $lon_kantor = 98.65087273418932;

// // Validasi keberadaan GPS
// if (!$lat_user || !$lon_user) {
//   echo "<script>alert('Lokasi GPS tidak tersedia. Aktifkan GPS Anda dan izinkan akses lokasi.'); window.location='dashboard_karyawan.php';</script>";
//   exit;
// }

// // Hitung jarak dari user ke kantor
// $jarak = hitungJarak($lat_user, $lon_user, $lat_kantor, $lon_kantor);
// if ($jarak > 100) {
//   echo "<script>alert('Anda berada di luar radius kantor. Jarak Anda: " . round($jarak) . " meter.'); window.location='dashboard_karyawan.php';</script>";
//   exit;
// }

// Atur zona waktu dan tanggal sekarang
date_default_timezone_set('Asia/Jakarta');
$tanggal = date('Y-m-d');

// Cek apakah sudah absen hari ini (gunakan GET ke API Laravel)
$cek_url = "$api_base_url/absensi/{$id_karyawan}?role=karyawan&user_id={$id_karyawan}&karyawan_id={$id_karyawan}";
$cek = @file_get_contents($cek_url);
$absen_hari_ini = false;

$data_cek = json_decode($cek, true);
if (is_array($data_cek)) {
  foreach ($data_cek as $absen) {
    if (isset($absen['tanggal']) && $absen['tanggal'] === $tanggal) {
      $absen_hari_ini = true;
      break;
    }
  }
}

if ($absen_hari_ini) {
  echo "<script>alert(' Anda sudah melakukan absensi hari ini.'); window.location='dashboard_karyawan.php';</script>";
  exit;
}

// Kirim absensi ke Laravel API (/api/absensi/now)
$data = [
  'karyawan_id' => $id_karyawan
];

$ch = curl_init("$api_base_url/absensi/now");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Accept: application/json',
  'role: karyawan'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
  echo "<script>alert('Gagal terhubung ke server. Silakan cek koneksi atau hubungi admin.'); window.location='dashboard_karyawan.php';</script>";
  exit;
}

$result = json_decode($response, true);

// Evaluasi hasil
if ($httpCode === 200 && isset($result['data'])) {
  echo "<script>alert(' Absensi berhasil.'); window.location='dashboard_karyawan.php';</script>";
  exit;
} else {
  $pesan = $result['message'] ?? 'Gagal melakukan absensi. Silakan coba lagi.';
  echo "<script>alert('$pesan'); window.location='dashboard_karyawan.php';</script>";
  exit;
}
?>
