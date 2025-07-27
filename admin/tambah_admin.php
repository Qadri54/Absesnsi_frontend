<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_lengkap = $_POST['nama_lengkap'] ?? '';
  $username     = strtolower(trim($_POST['username'] ?? ''));
  $password     = $_POST['password'] ?? '';

  if (!$nama_lengkap || !$username || !$password) {
    $msg = "Semua field wajib diisi.";
    header("Location: data_admin.php?error=" . urlencode($msg));
    exit;
  }

  // Siapkan data POST
  $postFields = [
    'nama_lengkap' => $nama_lengkap,
    'username'     => $username,
    'password'     => $password
  ];

  // Validasi dan lampirkan file foto jika ada
  if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
      $msg = "Format foto tidak diizinkan. Hanya jpg, jpeg, png, webp.";
      header("Location: data_admin.php?error=" . urlencode($msg));
      exit;
    }

    $tmpFile  = $_FILES['foto']['tmp_name'];
    $fileName = $_FILES['foto']['name'];
    $mimeType = mime_content_type($tmpFile);

    $postFields['foto'] = new CURLFile($tmpFile, $mimeType, $fileName);
  }

  // Kirim data ke Laravel
  $ch = curl_init("$api_base_url/admin");
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields,
    CURLOPT_HTTPHEADER => ["Accept: application/json"]
  ]);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  // Tangani response Laravel
  if ($httpCode === 200 || $httpCode === 201) {
    $msg = 'Admin berhasil ditambahkan.';
    header("Location: data_admin.php?success=" . urlencode($msg));
  } else {
    $decoded = json_decode($response, true);
    $msg = $decoded['message'] ?? "Gagal menambah admin.";

    // Tambahkan detail error jika ada
    if (isset($decoded['errors']) && is_array($decoded['errors'])) {
      foreach ($decoded['errors'] as $fieldErrors) {
        foreach ($fieldErrors as $errorText) {
          $msg .= "\\n- " . $errorText;
        }
      }
    }

    header("Location: data_admin.php?error=" . urlencode($msg));
  }

  exit;
} else {
  header("Location: data_admin.php?error=" . urlencode("Metode tidak diizinkan."));
  exit;
}
