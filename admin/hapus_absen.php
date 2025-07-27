<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  include '../config.php';
  $id = $_POST['id'] ?? null;

  if ($id) {
    $ch = curl_init("$api_base_url/absensi/$id?role=admin");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
  }
}

header("Location: data_absen.php");
exit;
