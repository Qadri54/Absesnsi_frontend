<?php
session_start();
include '../config.php';

if (!isset($_GET['id'])) {
  header("Location: data_admin.php?error=" . urlencode("ID admin tidak ditemukan."));
  exit;
}

$id = $_GET['id'];

// Cegah hapus akun sendiri
if (isset($_SESSION['user']['id']) && $_SESSION['user']['id'] == $id) {
  header("Location: data_admin.php?error=" . urlencode("Tidak bisa menghapus akun sendiri."));
  exit;
}

// Kirim DELETE request ke Laravel
$url = "$api_base_url/admin/$id";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Coba ambil pesan dari respons
$res = json_decode($response, true);
$message = $res['message'] ?? 'Terjadi kesalahan.';

// Redirect dengan pesan
if ($httpCode === 200) {
  header("Location: data_admin.php?success=" . urlencode($message));
} else {
  header("Location: data_admin.php?error=" . urlencode("Gagal menghapus admin: $message"));
}
exit;
