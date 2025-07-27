<?php
include '../config.php';
header('Content-Type: text/html');

// Validasi metode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: data_absen.php?error=Metode tidak valid");
  exit;
}

$id         = $_POST['id'] ?? null;
$status     = $_POST['status'] ?? null;
$keterangan = $_POST['keterangan'] ?? '';
$bukti      = $_FILES['bukti'] ?? null;

if (!$id || !$status) {
  header("Location: data_absen.php?error=ID dan status wajib diisi");
  exit;
}

// Siapkan data form
$postFields = [
  'status'     => $status,
  'keterangan' => $keterangan,
  '_method'    => 'PUT'
];

// Validasi dan proses file bukti jika ada
if ($bukti && is_uploaded_file($bukti['tmp_name'])) {
  $mimeType = mime_content_type($bukti['tmp_name']);

  // Hanya izinkan PDF saja
  if ($mimeType !== 'application/pdf') {
    header("Location: data_absen.php?error=Tipe file tidak valid. Hanya file PDF yang diperbolehkan.");
    exit;
  }

  // Gunakan curl_file_create untuk kirim file
  $cfile = curl_file_create($bukti['tmp_name'], $mimeType, $bukti['name']);
  $postFields['bukti'] = $cfile;
}

// Kirim request ke API Laravel
$url = "$api_base_url/absensi/$id";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "role: admin"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

// Jika error dari cURL
if ($error) {
  header("Location: data_absen.php?error=" . urlencode("CURL Error: $error"));
  exit;
}

// Decode respons JSON
$result = json_decode($response, true);

// Redirect dengan pesan sesuai hasil
if ($httpCode === 200) {
  header("Location: data_absen.php?success=" . urlencode($result['message'] ?? 'Update berhasil'));
} else {
  header("Location: data_absen.php?error=" . urlencode($result['message'] ?? "Gagal update (HTTP $httpCode)"));
}
exit;
?>
