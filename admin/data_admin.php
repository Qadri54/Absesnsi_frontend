<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

include '../config.php';

$ch = curl_init("$api_base_url/admin?role=admin");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
$admins = $data ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Admin</title>

  <!-- Bootstrap Lokal -->
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/data_admin.css">
  <link rel="stylesheet" href="../css/popup.css">
</head>
<body>

<?php include '../sidebar_admin.php'; ?>

<div class="main-content">
  <h2 class="mb-4">👥 Data Admin</h2>

  <!-- ALERT Bootstrap -->
  <div class="container px-0">
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= urldecode($_GET['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
      </div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= nl2br(urldecode($_GET['error'])) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
      </div>
    <?php endif; ?>
  </div>

  <button class="btn-tambah mb-3" onclick="toggleForm()">+ Tambah Admin</button>

  <!-- FORM TAMBAH -->
  <div class="popup-form" id="popupForm">
    <form action="tambah_admin.php" method="POST" enctype="multipart/form-data" class="form-admin">
      <h3>Tambah Admin</h3>

      <label for="nama">Nama Lengkap</label>
      <input type="text" name="nama_lengkap" id="nama" required>

      <label for="username">Username</label>
      <input type="text" name="username" id="username" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>

      <label for="foto">Foto</label>
      <input type="file" name="foto" id="foto" accept="image/*" required>

      <div class="form-actions">
        <button type="submit">Simpan</button>
        <button type="button" onclick="toggleForm()">Batal</button>
      </div>
    </form>
  </div>

  <!-- FORM EDIT -->
  <div class="popup-form" id="popupEditForm" style="display: none;">
    <form action="update_admin.php" method="POST" enctype="multipart/form-data" class="form-admin">
      <h3>Edit Admin</h3>

      <input type="hidden" name="id" id="edit_id">
      <input type="hidden" name="foto_lama" id="edit_foto_lama">

      <label for="edit_nama">Nama Lengkap</label>
      <input type="text" name="nama_lengkap" id="edit_nama" required>

      <label for="edit_username">Username</label>
      <input type="text" name="username" id="edit_username" required>

      <label for="edit_password">Password (kosongkan jika tidak diganti)</label>
      <input type="password" name="password" id="edit_password">

      <label for="edit_foto">Foto Baru (opsional)</label>
      <input type="file" name="foto" id="edit_foto" accept="image/*">

      <div class="form-actions">
        <button type="submit">Simpan Perubahan</button>
        <button type="button" onclick="toggleEditForm()">Batal</button>
      </div>
    </form>
  </div>

  <!-- TABEL DATA -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped text-center align-middle">
    <thead class="text-center">
        <tr>
          <th>No</th>
          <th>Foto</th>
          <th>Nama Lengkap</th>
          <th>Username</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (!empty($admins)) {
          $no = 1;
          foreach ($admins as $admin) {
            $fotoFile = $admin['foto'] ?? '';
            $fotoPath = "https://via.placeholder.com/50";
            if (!empty($fotoFile)) {
              $baseURL = str_replace('/api', '', $api_base_url);
              $fotoPath = $baseURL . "/uploads/foto_admin/" . $fotoFile;
            }

            $adminJson = htmlspecialchars(json_encode($admin), ENT_QUOTES, 'UTF-8');

            echo "<tr>
              <td>{$no}</td>
              <td><img src='{$fotoPath}' alt='Foto Admin' class='rounded-circle' width='45' height='45'></td>
              <td>{$admin['nama_lengkap']}</td>
              <td>{$admin['username']}</td>
           <td class='text-center'>
            <a href='#' onclick='showEditForm({$adminJson})' class='btn btn-warning btn-sm d-inline-block me-1' title='Edit'>
              <i class='bi bi-pencil-square'></i>
            </a>
            <a href='hapus_admin.php?id={$admin['id']}' class='btn btn-danger btn-sm d-inline-block' title='Hapus' onclick=\"return confirm('Yakin ingin menghapus admin ini?')\">
              <i class='bi bi-trash'></i>
            </a>
          </td>

            </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='5' class='text-center'>Data tidak ditemukan</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<!-- JavaScript -->
<script>
  function toggleForm() {
    const form = document.getElementById("popupForm");
    form.style.display = (form.style.display === "block") ? "none" : "block";
  }

  function toggleEditForm() {
    document.getElementById("popupEditForm").style.display = "none";
  }

  function showEditForm(admin) {
    document.getElementById("edit_id").value = admin.id;
    document.getElementById("edit_foto_lama").value = admin.foto || '';
    document.getElementById("edit_nama").value = admin.nama_lengkap;
    document.getElementById("edit_username").value = admin.username;
    document.getElementById("edit_password").value = '';
    document.getElementById("popupEditForm").style.display = "block";
  }
</script>

<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
