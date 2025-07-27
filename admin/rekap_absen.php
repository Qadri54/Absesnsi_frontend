<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

include '../config.php';
include '../sidebar_admin.php';

$bulanOptions = [
  '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
  '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
  '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

$selectedBulan = $_GET['bulan'] ?? '';
$selectedTahun = $_GET['tahun'] ?? '';
$rekapData = [];
$errorMsg = '';

if ($selectedBulan && $selectedTahun) {
  $url = "$api_base_url/rekap/bulanan?bulan=" . (int)$selectedBulan . "&tahun=" . (int)$selectedTahun;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [ "role: admin" ]);
  $response = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curlError = curl_error($ch);
  curl_close($ch);

  if ($response && $httpcode === 200) {
    $decoded = json_decode($response, true);
    $rekapData = is_array($decoded['data'] ?? null) ? $decoded['data'] : [];
    // PAGINATION
      $perPage = 10;
      $totalData = count($rekapData);
      $totalPages = ceil($totalData / $perPage);
      $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
      $currentPage = min($totalPages, $currentPage);
      $startIndex = ($currentPage - 1) * $perPage;
      $paginatedData = array_slice($rekapData, $startIndex, $perPage);

  } else {
    $errorMsg = $curlError ?: "Gagal mengambil data dari server (HTTP $httpcode)";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rekap Absensi</title>
  <link rel="stylesheet" href="../css/sidebar.css">
  <link rel="stylesheet" href="../css/rekap_absen.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    .only-excel { display: none; }
    .only-web { display: inline; }
    @media print {
      .only-web { display: none !important; }
      .only-excel { display: inline !important; }
    }
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
<div class="main-content">
  <div class="header">
    <h2>Rekap Absensi</h2>
  </div>

  <form method="GET" style="margin-bottom: 20px;">
    <label for="bulan">Bulan:</label>
    <select name="bulan" id="bulan" required>
      <option value="">-- Pilih --</option>
      <?php foreach ($bulanOptions as $value => $label): ?>
        <option value="<?= $value ?>" <?= ($selectedBulan == $value) ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>

    <label for="tahun">Tahun:</label>
    <select name="tahun" id="tahun" required>
      <option value="">-- Pilih --</option>
      <?php
      $now = date('Y');
      for ($i = $now; $i >= $now - 5; $i--) {
        $selected = ($selectedTahun == $i) ? 'selected' : '';
        echo "<option value='$i' $selected>$i</option>";
      }
      ?>
    </select>

    <button type="submit" class="btn-rekap">Tampilkan</button>
    <?php if ($selectedBulan && $selectedTahun && !empty($rekapData)): ?>
      <button type="button" onclick="exportToExcel()" class="btn-rekap">Export ke Excel</button>
      <button type="button" onclick="exportToPDF()" class="btn-rekap btn-danger">Export ke PDF</button>
    <?php endif; ?>
  </form>

  <?php if ($errorMsg): ?>
    <div style="color: red; margin-bottom: 10px;"><strong> <?= $errorMsg ?></strong></div>
  <?php endif; ?>

<table id="rekapTable" class="table table-bordered table-striped table-hover text-center align-middle">
<thead class="custom-green">
    <tr>
      <th>No</th>
      <th>Nama Karyawan</th>
      <th>Tanggal</th>
      <th>Jam Masuk</th>
      <th>Status</th>
      <th>Keterangan</th>
      <th>Bukti</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($selectedBulan && $selectedTahun): ?>
      <?php if (!empty($rekapData)): ?>
        <?php $no = $startIndex + 1; foreach ($paginatedData as $item): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($item['karyawan']['nama'] ?? '-') ?></td>
            <td><?= htmlspecialchars($item['tanggal']) ?></td>
            <td><?= $item['jam_masuk'] ?? '-' ?></td>
            <td><?= ucfirst($item['status']) ?></td>
            <td><?= $item['keterangan'] ?? '-' ?></td>
            <td>
              <?php if (!empty($item['bukti'])): ?>
                <span class="only-web">
                <a href="<?= str_replace('/api', '', $api_base_url) . '/storage/' . $item['bukti'] ?>" target="_blank" class="btn btn-sm btn-primary" title="Lihat Bukti">
                  <i class="bi bi-eye"></i>
                </a>
                </span>
                <span class="only-excel"><?= htmlspecialchars(basename($item['bukti'])) ?></span>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7">Data tidak ditemukan untuk bulan/tahun tersebut.</td></tr>
      <?php endif; ?>
    <?php else: ?>
      <tr><td colspan="7">Silakan pilih bulan dan tahun untuk melihat data.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
<?php if ($selectedBulan && $selectedTahun && isset($totalPages) && $totalPages > 1): ?>
<nav class="mt-3">
  <ul class="pagination justify-content-center">
    <?php if ($currentPage > 1): ?>
      <li class="page-item">
        <a class="page-link" href="?bulan=<?= $selectedBulan ?>&tahun=<?= $selectedTahun ?>&page=<?= $currentPage - 1 ?>">« Prev</a>
      </li>
    <?php endif; ?>

    <?php
    $range = 2;
    $start = max(1, $currentPage - $range);
    $end = min($totalPages, $currentPage + $range);

    if ($start > 1) {
      echo '<li class="page-item"><a class="page-link" href="?bulan=' . $selectedBulan . '&tahun=' . $selectedTahun . '&page=1">1</a></li>';
      if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }

    for ($i = $start; $i <= $end; $i++) {
      $active = $i == $currentPage ? 'active' : '';
      echo "<li class='page-item $active'><a class='page-link' href='?bulan=$selectedBulan&tahun=$selectedTahun&page=$i'>$i</a></li>";
    }

    if ($end < $totalPages) {
      if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
      echo '<li class="page-item"><a class="page-link" href="?bulan=' . $selectedBulan . '&tahun=' . $selectedTahun . '&page=' . $totalPages . "'>$totalPages</a></li>";
    }
    ?>

    <?php if ($currentPage < $totalPages): ?>
      <li class="page-item">
        <a class="page-link" href="?bulan=<?= $selectedBulan ?>&tahun=<?= $selectedTahun ?>&page=<?= $currentPage + 1 ?>">Next »</a>
      </li>
    <?php endif; ?>
  </ul>
</nav>
<?php endif; ?>

</div>

<script>
function exportToExcel() {
  // Tampilkan kolom nama file, sembunyikan kolom "Lihat"
  document.querySelectorAll(".only-excel").forEach(e => e.style.display = "inline");
  document.querySelectorAll(".only-web").forEach(e => e.style.display = "none");

  const table = document.getElementById("rekapTable");
  const wb = XLSX.utils.table_to_book(table, { sheet: "Rekap Absensi" });
  XLSX.writeFile(wb, `rekap_absensi_<?= $selectedBulan . "_" . $selectedTahun ?>.xlsx`);

  // Balikkan ke tampilan semula
  document.querySelectorAll(".only-excel").forEach(e => e.style.display = "none");
  document.querySelectorAll(".only-web").forEach(e => e.style.display = "inline");
}

function exportToPDF() {
  // Tampilkan kolom nama file, sembunyikan kolom "Lihat"
  document.querySelectorAll(".only-excel").forEach(e => e.style.display = "inline");
  document.querySelectorAll(".only-web").forEach(e => e.style.display = "none");

  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('l', 'pt', 'a4');
  doc.text("Rekap Absensi Bulan <?= $bulanOptions[$selectedBulan] ?? '' ?> Tahun <?= $selectedTahun ?>", 40, 30);

  doc.autoTable({
    html: '#rekapTable',
    startY: 50,
    styles: { fontSize: 8, halign: 'center' },
    headStyles: { fillColor: [40, 167, 69] }
  });

  doc.save(`rekap_absensi_<?= $selectedBulan . "_" . $selectedTahun ?>.pdf`);

  // Balikkan ke tampilan semula
  document.querySelectorAll(".only-excel").forEach(e => e.style.display = "none");
  document.querySelectorAll(".only-web").forEach(e => e.style.display = "inline");
}
</script>