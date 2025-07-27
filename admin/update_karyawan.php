<?php
header('Content-Type: application/json');
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['message' => 'Metode tidak diizinkan.']);
    exit;
}

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['message' => 'ID karyawan tidak ditemukan.']);
    exit;
}

$url = "$api_base_url/karyawan/$id?role=admin&user_id=$id";
$fields = $_POST;

// Tambahkan override method agar Laravel mengenali ini sebagai PUT
$fields['_method'] = 'PUT';

// Tangani file foto
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['foto']['tmp_name'];
    $fileType = mime_content_type($tmpName);
    $fileName = $_FILES['foto']['name'];
    $fields['foto'] = new CURLFile($tmpName, $fileType, $fileName);
}

// Kirim ke Laravel
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true, // <- Ganti dari CUSTOMREQUEST PUT
    CURLOPT_POSTFIELDS => $fields,
    CURLOPT_HTTPHEADER => ['Accept: application/json']
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    echo json_encode(['message' => 'Gagal menghubungi server Laravel.']);
    exit;
}

$result = json_decode($response, true);

// Respons berhasil
if (($httpCode === 200 || $httpCode === 201) && isset($result['message']) && $result['message'] !== 'Validasi gagal') {
    echo json_encode([
        'message' => $result['message'],
        'data' => $result['data'] ?? null
    ]);
    exit;
}

// Validasi gagal
if ($httpCode === 422 && isset($result['errors'])) {
    echo json_encode([
        'message' => 'Validasi gagal',
        'errors' => $result['errors']
    ]);
    exit;
}

// Error lainnya
echo json_encode([
    'message' => $result['message'] ?? 'Terjadi kesalahan.',
    'detail' => $result
]);
exit;
