<?php
include '../config.php'; // berisi $api_base_url

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id           = $_POST['id'] ?? null;
  $nama_lengkap = $_POST['nama_lengkap'] ?? '';
  $username     = strtolower(trim($_POST['username'] ?? ''));
  $password     = $_POST['password'] ?? '';
  $foto_lama    = $_POST['foto_lama'] ?? null;

  if (!$id || !$nama_lengkap || !$username) {
    $msg = "Data tidak lengkap.";
    header("Location: data_admin.php?error=" . urlencode($msg));
    exit;
  }

  // Siapkan data POST ke Laravel
  $postFields = [
    'nama_lengkap' => $nama_lengkap,
    'username'     => $username,
    '_method'      => 'PUT'
  ];

  if (!empty($password)) {
    $postFields['password'] = $password;
  }

  // Sertakan file foto jika ada
  if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $tmpName  = $_FILES['foto']['tmp_name'];
    $fileName = $_FILES['foto']['name'];
    $mime     = mime_content_type($tmpName);

    $postFields['foto'] = new CURLFile($tmpName, $mime, $fileName);
  }

  // Kirim ke Laravel
  $ch = curl_init("$api_base_url/admin/$id");
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields,
    CURLOPT_HTTPHEADER => [
      "Accept: application/json",
      "Content-Type: multipart/form-data"
    ]
  ]);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  // Tangani response Laravel
  if ($httpCode === 200) {
    $msg = "Admin berhasil diperbarui.";
    header("Location: data_admin.php?success=" . urlencode($msg));
  } else {
    // Decode response untuk ambil error detail dari Laravel
    $resDecoded = json_decode($response, true);
    $msg = $resDecoded['message'] ?? "Gagal update admin.";

    if (isset($resDecoded['errors']) && is_array($resDecoded['errors'])) {
      foreach ($resDecoded['errors'] as $fieldErrors) {
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
