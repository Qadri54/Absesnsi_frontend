<?php
session_start();
include '../config.php'; // $api_base_url

// Cek login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'karyawan') {
  header("Location: ../login.php");
  exit;
}

// Ambil data input
$id_karyawan = $_POST['id_karyawan'] ?? $_SESSION['user']['id'] ?? null;
$status      = $_POST['status'] ?? null; // 'izin' atau 'sakit'
$keterangan  = trim($_POST['keterangan'] ?? '');
$tanggal     = date('Y-m-d');
$hari        = strtolower(date('l')); // sunday, monday, etc

// ❌ Validasi data wajib
if (!$id_karyawan || !$status || !$keterangan) {
  echo "<script>alert('Data tidak lengkap. Harap isi semua kolom.'); window.location='dashboard_karyawan.php';</script>";
  exit;
}

// ❌ Tidak boleh izin di hari Minggu
if ($hari === 'sunday') {
  echo "<script>alert('⛔ Pengajuan izin tidak diperbolehkan pada hari Minggu.'); window.location='dashboard_karyawan.php';</script>";
  exit;
}

// ✅ Siapkan data untuk dikirim
$postFields = [
  'karyawan_id' => $id_karyawan,
  'tanggal'     => $tanggal,
  'status'      => $status,
  'keterangan'  => $keterangan
];

// ✅ Proses file bukti jika ada
if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
  $tmpName  = $_FILES['bukti']['tmp_name'];
  $filename = $_FILES['bukti']['name'];
  $mimeType = mime_content_type($tmpName);
  $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  $fileSize = $_FILES['bukti']['size'];

  // ❌ Validasi hanya PDF
  if ($ext !== 'pdf' || $mimeType !== 'application/pdf') {
    echo "<script>alert('Hanya file PDF yang diperbolehkan sebagai bukti.'); window.location='dashboard_karyawan.php';</script>";
    exit;
  }

  // ❌ Validasi ukuran maksimal 2MB
  if ($fileSize > 2 * 1024 * 1024) {
    echo "<script>alert('Ukuran file terlalu besar. Maksimal 2MB.'); window.location='dashboard_karyawan.php';</script>";
    exit;
  }

  // ✅ Jika valid, tambahkan file ke postFields
  $postFields['bukti'] = curl_file_create($tmpName, $mimeType, $filename);
}

// ✅ Kirim ke Laravel API
$ch = curl_init("$api_base_url/absensi/izin");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Accept: application/json',
  'role: karyawan'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Parse hasil
$result = json_decode($response, true);

// Jika berhasil
if ($httpCode === 200 && isset($result['data'])) {
  echo "<script>alert('Pengajuan izin berhasil dikirim.'); window.location='dashboard_karyawan.php';</script>";
  exit;
}

// Jika gagal
$pesan = $result['message'] ?? 'Gagal mengirim izin. Silakan coba lagi.';
echo "<script>alert('$pesan'); window.location='dashboard_karyawan.php';</script>";
exit;
?>
