<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'karyawan') {
  header("Location: ../login.php");
  exit;
}

include '../config.php';
$karyawan = $_SESSION['user'];
$baseURL = str_replace('/api', '', $api_base_url);
$fotoPath = $karyawan['foto']
  ? $baseURL . "/uploads/foto_karyawan/" . $karyawan['foto']
  : "../img/default.png";

// Helper untuk tanggal kosong
function formatTanggal($tgl) {
  if (!$tgl || $tgl === '0000-00-00') return '-';
  return explode('T', $tgl)[0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Saya</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Sidebar -->
  <link rel="stylesheet" href="../css/sidebar_karyawan.css">
</head>
<body>
<?php include 'sidebar_karyawan.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <img src="<?= $fotoPath ?>" width="120" height="120" class="rounded-circle shadow mb-3" style="object-fit: cover;">
          <h4 class="mb-1"><?= htmlspecialchars($karyawan['nama']) ?></h4>
          <p class="text-muted mb-0"><?= htmlspecialchars($karyawan['jabatan']) ?> - <?= htmlspecialchars($karyawan['afdeling']) ?></p>
          <p class="text-muted small"><?= htmlspecialchars($karyawan['nik_sap']) ?></p>
          <p class="badge bg-secondary">Status: <?= htmlspecialchars($karyawan['status_karyawan'] ?? 'PKWT') ?></p>
          <button class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#editModal">
            <i class="bi bi-pencil-square me-1"></i> Edit Profil
          </button>
        </div>
        <ul class="list-group list-group-flush text-start">
          <li class="list-group-item"><strong>Username:</strong> <?= htmlspecialchars($karyawan['username']) ?></li>
          <li class="list-group-item"><strong>Status Karyawan:</strong> <?= htmlspecialchars($karyawan['status_karyawan'] ?? 'PKWT') ?></li>
          <li class="list-group-item"><strong>Tanggal Lahir:</strong> <?= formatTanggal($karyawan['tanggal_lahir']) ?></li>
          <li class="list-group-item"><strong>No SPK:</strong> <?= htmlspecialchars($karyawan['no_spk']) ?></li>
          <li class="list-group-item"><strong>Kontrak:</strong> <?= formatTanggal($karyawan['tgl_kontrak']) ?> s/d <?= formatTanggal($karyawan['tgl_berakhir_kontrak']) ?></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form action="update_profile_karyawan.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Profil Karyawan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <input type="hidden" name="id" value="<?= $karyawan['id'] ?>">

          <div class="col-md-6">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($karyawan['nama']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">NIK SAP</label>
            <input type="text" name="nik_sap" class="form-control" value="<?= htmlspecialchars($karyawan['nik_sap']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($karyawan['username']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
            <input type="password" name="password" class="form-control" placeholder="(Opsional)">
          </div>

          <div class="col-md-6">
            <label class="form-label">Jabatan</label>
            <input type="text" name="jabatan" class="form-control" value="<?= htmlspecialchars($karyawan['jabatan']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Status Karyawan</label>
            <select name="status_karyawan" class="form-select">
              <option value="PKWT" <?= ($karyawan['status_karyawan'] ?? 'PKWT') === 'PKWT' ? 'selected' : '' ?>>PKWT</option>
              <option value="Pegawai" <?= ($karyawan['status_karyawan'] ?? '') === 'Pegawai' ? 'selected' : '' ?>>Pegawai</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Afdeling</label>
            <input type="text" name="afdeling" class="form-control" value="<?= htmlspecialchars($karyawan['afdeling']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control" value="<?= formatTanggal($karyawan['tanggal_lahir']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">No SPK</label>
            <input type="text" name="no_spk" class="form-control" value="<?= htmlspecialchars($karyawan['no_spk']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Tanggal Kontrak</label>
            <input type="date" name="tgl_kontrak" class="form-control" value="<?= formatTanggal($karyawan['tgl_kontrak']) ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Tanggal Berakhir Kontrak</label>
            <input type="date" name="tgl_berakhir_kontrak" class="form-control" value="<?= formatTanggal($karyawan['tgl_berakhir_kontrak']) ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Foto (Opsional)</label>
            <input type="file" name="foto" class="form-control" accept="image/*">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
