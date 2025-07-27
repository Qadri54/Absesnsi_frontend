<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
  $ch = curl_init("$api_base_url/karyawan/$id?role=admin&user_id=$id");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close($ch);

  $result = json_decode($response, true);
  
  if ($result && isset($result['message']) && stripos($result['message'], 'berhasil') !== false) {
    $_SESSION['sukses'] = "✅ Data karyawan berhasil dihapus.";
  } else {
    $_SESSION['kesalahan'] = "❌ Gagal menghapus data karyawan.";
  }
} else {
  $_SESSION['kesalahan'] = "❌ ID karyawan tidak ditemukan.";
}

header("Location: data_karyawan.php");
exit;
