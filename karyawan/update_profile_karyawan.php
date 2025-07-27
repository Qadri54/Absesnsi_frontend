<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profile_karyawan.php");
    exit;
}

$id = $_POST['id'] ?? null;
if (!$id) {
    die("ID tidak ditemukan.");
}

// Ambil data dari form
$nama             = trim($_POST['nama'] ?? '');
$nik_sap          = trim($_POST['nik_sap'] ?? '');
$username         = trim($_POST['username'] ?? '');
$password         = $_POST['password'] ?? '';
$jabatan          = trim($_POST['jabatan'] ?? '');
$status_karyawan  = trim($_POST['status_karyawan'] ?? '');
$afdeling         = trim($_POST['afdeling'] ?? '');
$tanggal_lahir    = $_POST['tanggal_lahir'] ?? '';
$no_spk           = trim($_POST['no_spk'] ?? '');
$tgl_kontrak      = $_POST['tgl_kontrak'] ?? null;
$tgl_berakhir     = $_POST['tgl_berakhir_kontrak'] ?? null;

// Default jika status tidak valid
if (!in_array($status_karyawan, ['PKWT', 'Pegawai'])) {
    $status_karyawan = 'PKWT';
}

// Validasi minimal
$required = ['nama', 'nik_sap', 'username', 'jabatan', 'afdeling', 'tanggal_lahir', 'no_spk'];
$errors = [];
foreach ($required as $field) {
    if (empty($$field)) {
        $errors[] = "Field '$field' wajib diisi.";
    }
}

if (!empty($errors)) {
    echo implode("<br>", $errors);
    echo "<br><a href='profile_karyawan.php'>Kembali</a>";
    exit;
}

// Siapkan payload ke Laravel
$postData = [
    'nama'                 => $nama,
    'nik_sap'              => $nik_sap,
    'username'             => $username,
    'jabatan'              => $jabatan,
    'status_karyawan'      => $status_karyawan,
    'afdeling'             => $afdeling,
    'tanggal_lahir'        => $tanggal_lahir,
    'no_spk'               => $no_spk,
    'tgl_kontrak'          => $tgl_kontrak,
    'tgl_berakhir_kontrak' => $tgl_berakhir,
    'role'                 => 'karyawan',
    'user_id'              => $id,
    '_method'              => 'PUT'
];

// Password hanya dikirim jika diisi
if (!empty($password)) {
    $postData['password'] = $password;
}

// Foto opsional
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['foto']['tmp_name'];
    $type     = mime_content_type($tmp_name);
    $name     = $_FILES['foto']['name'];
    $postData['foto'] = new CURLFile($tmp_name, $type, $name);
}

// Kirim ke Laravel
$ch = curl_init("$api_base_url/karyawan/$id?role=karyawan&user_id=$id");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData,
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Tangani respon
if ($httpCode === 200) {
    $result = json_decode($response, true);

    if (isset($result['data'])) {
        $_SESSION['user'] = $result['data'];
        $_SESSION['user']['role'] = 'karyawan';
    }

    echo "<script>alert('Profil berhasil diperbarui'); window.location='profile_karyawan.php';</script>";
    exit;
} else {
    echo "Gagal memperbarui profil. Status code: $httpCode<br>";
    echo "<pre>$response</pre>";
    echo "<a href='profile_karyawan.php'>Kembali</a>";
}
