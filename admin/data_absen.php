<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

include '../config.php';
include '../sidebar_admin.php';

// Ambil data absensi dari API Laravel
$ch = curl_init("$api_base_url/absensi?role=admin");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$absensi = $data ?? [];
// Konfigurasi Pagination
$perPage = 10;
$totalData = count($absensi);
$totalPages = ceil($totalData / $perPage);
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$currentPage = min($totalPages, $currentPage);
$startIndex = ($currentPage - 1) * $perPage;
$paginatedData = array_slice($absensi, $startIndex, $perPage);

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Absensi</title>
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/sidebar.css">
  <link rel="stylesheet" href="../css/data_absen.css">
  <link rel="stylesheet" href="../css/popup.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<div class="main-content">
  <div class="header d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-dark">Data Absensi</h2>
    <a href="rekap_absen.php" class="btn btn-success">Rekap Absen</a>
  </div>

  <!-- Notifikasi -->
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
  <?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-bordered table-striped">
    <thead class="custom-green">
        <tr>
          <th>No</th>
          <th>Nama Karyawan</th>
          <th>Tanggal</th>
          <th>Jam Masuk</th>
          <th>Status</th>
          <th>Keterangan</th>
          <th>Bukti</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($absensi) && is_array($absensi)): ?>
      <?php $no = $startIndex + 1; foreach ($paginatedData as $row): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['karyawan']['nama'] ?? '-') ?></td>
              <td><?= htmlspecialchars($row['tanggal']) ?></td>
              <td><?= $row['jam_masuk'] ?? '-' ?></td>
              <td><?= ucfirst($row['status']) ?></td>
              <td><?= $row['keterangan'] ?? '-' ?></td>
              <td>
                <?php if (!empty($row['bukti'])): ?>
                  <?php
                    $baseUrl = str_replace('/api', '', $api_base_url);
                    $fullUrl = $baseUrl . '/storage/' . ltrim($row['bukti'], '/');
                  ?>
                  <a href="<?= $fullUrl ?>" target="_blank" class="btn btn-sm btn-primary" title="Lihat Bukti">
                    <i class="bi bi-eye"></i>
                  </a>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
           <td class="text-center">
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-warning btn-sm" title="Edit" onclick="openEditModal(
              <?= $row['id'] ?>,
              '<?= $row['status'] ?>',
              `<?= htmlspecialchars($row['keterangan'] ?? '', ENT_QUOTES) ?>`
            )">
              <i class="bi bi-pencil-square"></i>
            </button>

            <form action="hapus_absen.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </div>
        </td>

            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8">Data absensi tidak ditemukan atau gagal diambil.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <!-- Pagination -->
<?php if ($totalPages > 1): ?>
<nav class="mt-3">
  <ul class="pagination justify-content-center">

    <?php if ($currentPage > 1): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?= $currentPage - 1 ?>">« Prev</a>
      </li>
    <?php endif; ?>

    <?php
    $range = 2;
    $start = max(1, $currentPage - $range);
    $end = min($totalPages, $currentPage + $range);

    if ($start > 1) {
      echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
      if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }

    for ($i = $start; $i <= $end; $i++) {
      $active = $i == $currentPage ? 'active' : '';
      echo "<li class='page-item $active'><a class='page-link' href='?page=$i'>$i</a></li>";
    }

    if ($end < $totalPages) {
      if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
      echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    ?>

    <?php if ($currentPage < $totalPages): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?= $currentPage + 1 ?>">Next »</a>
      </li>
    <?php endif; ?>

  </ul>
</nav>
<?php endif; ?>

  </div>
</div>

<!-- Modal Edit Absensi -->
<div id="editModal" class="popup-form">
  <span class="close-btn" onclick="closeEditModal()">&times;</span>
  <h3>Edit Absensi</h3>
  <form action="update_absensi.php" method="POST" enctype="multipart/form-data" class="form-admin">
    <input type="hidden" name="id" id="editId">

    <label for="editStatus">Status:</label>
    <select name="status" id="editStatus" class="form-select" required>
      <option value="izin">Izin</option>
      <option value="sakit">Sakit</option>
    </select>

    <label for="editKet">Keterangan:</label>
    <input type="text" name="keterangan" id="editKet" class="form-control">

    <label for="editBukti">Bukti (opsional):</label>
    <input type="file" name="bukti" id="editBukti" class="form-control">

    <div class="form-actions mt-3 d-flex justify-content-between">
      <button type="submit" class="btn btn-success">Simpan</button>
      <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Batal</button>
    </div>
  </form>
</div>

<!-- Bootstrap JS -->
<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
  function openEditModal(id, status, ket) {
    document.getElementById("editId").value = id;
    document.getElementById("editStatus").value = status;
    document.getElementById("editKet").value = ket;
    document.getElementById("editModal").style.display = "block";
  }

  function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
  }

  window.onclick = function(event) {
    const modal = document.getElementById("editModal");
    if (event.target === modal) {
      closeEditModal();
    }
  };
</script>

</body>
</html>
