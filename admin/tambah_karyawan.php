<?php
header('Content-Type: application/json');
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'message' => 'Metode tidak diizinkan.'
    ]);
    exit;
}

$url = "$api_base_url/karyawan?role=admin";
$fields = $_POST;

// Tangani file foto jika ada
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['foto']['tmp_name'];
    $fileType = mime_content_type($tmpName);
    $fileName = $_FILES['foto']['name'];
    $fields['foto'] = new CURLFile($tmpName, $fileType, $fileName);
}

// Kirim ke Laravel API
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $fields,
    CURLOPT_HTTPHEADER => ['Accept: application/json']
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Tangani jika gagal koneksi
if ($response === false) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Gagal menghubungi server Laravel.'
    ]);
    exit;
}

$result = json_decode($response, true) ?? [];

// ✅ Berhasil
if ($httpCode === 201 || ($httpCode === 200 && ($result['message'] ?? '') === 'success')) {
    echo json_encode(['message' => 'success']);
    exit;
}

// ❌ Validasi Gagal
if ($httpCode === 422 && isset($result['errors'])) {
    http_response_code(422);
    echo json_encode([
        'message' => 'Validasi gagal',
        'errors' => $result['errors']
    ]);
    exit;
}

// ❌ Error umum lainnya
http_response_code($httpCode);
echo json_encode([
    'message' => $result['message'] ?? 'Terjadi kesalahan.',
    'errors' => $result['errors'] ?? null,
    'detail' => $result
]);
exit;
