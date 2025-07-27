<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'karyawan') {
  header("Location: ../login.php");
  exit;
}

$id_karyawan = $_SESSION['user']['id'];
$nama = $_SESSION['user']['nama'];

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
  $url = "$api_base_url/absensi/$id_karyawan?role=karyawan&karyawan_id=$id_karyawan&bulan=$selectedBulan&tahun=$selectedTahun";
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  $response = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curlError = curl_error($ch);
  curl_close($ch);

  if ($response && $httpcode === 200) {
    $decoded = json_decode($response, true);
    $rekapData = is_array($decoded) ? $decoded : [];
  } else {
    $errorMsg = $curlError ?: "Gagal mengambil data dari server (HTTP $httpcode)";
  }
}

$baseUrl = str_replace('/api', '', $api_base_url);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rekap Absensi Saya</title>
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/rekap_absen.css">
  <style>
    .only-excel { display: none; }
    .only-web { display: inline; }
    @media print {
      .only-web { display: none !important; }
      .only-excel { display: inline !important; }
    }
  </style>
</head>
<body class="bg-light">
<link rel="stylesheet" href="../css/sidebar_karyawan.css">

<?php include 'sidebar_karyawan.php'; ?>

<div class="container mt-4">
  <div class="card shadow">
    <div class="card-header text-white" style="background-color: #2d742f;">
      <h4 class="mb-0">Rekap Absensi - <?= htmlspecialchars($nama) ?></h4>
    </div>

    <div class="card-body">
      <form method="GET" class="row g-2 align-items-center mb-3">
        <input type="hidden" name="karyawan_id" value="<?= $id_karyawan ?>">
        <div class="col-md-3">
          <select name="bulan" class="form-select" required>
            <option value="">-- Pilih Bulan --</option>
            <?php foreach ($bulanOptions as $value => $label): ?>
              <option value="<?= $value ?>" <?= ($selectedBulan == $value) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <select name="tahun" class="form-select" required>
            <option value="">-- Pilih Tahun --</option>
            <?php
            $now = date('Y');
            for ($i = $now; $i >= $now - 5; $i--) {
              $selected = ($selectedTahun == $i) ? 'selected' : '';
              echo "<option value='$i' $selected>$i</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-auto">
          <button type="submit" class="btn btn-success">Tampilkan</button>
        </div>
        <?php if ($selectedBulan && $selectedTahun && !empty($rekapData)): ?>
          <div class="col-md-auto">
            <button type="button" onclick="exportToPDF()" class="btn btn-outline-success">
              <i class="fas fa-file-pdf"></i> Export ke PDF
            </button>
          </div>
        <?php endif; ?>
      </form>

      <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?= $errorMsg ?></div>
      <?php endif; ?>

      <div class="table-responsive">
        <table id="rekapTable" class="table table-bordered table-hover text-center align-middle">
          <thead class="custom-green-header">
            <tr>
              <th>No</th>
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
                <?php $no = 1; foreach ($rekapData as $item): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($item['tanggal'] ?? '-') ?></td>
                    <td><?= $item['jam_masuk'] ?? '-' ?></td>
                    <td><?= ucfirst($item['status'] ?? '-') ?></td>
                    <td><?= $item['keterangan'] ?? '-' ?></td>
                    <td>
                      <?php if (!empty($item['bukti'])): ?>
                        <span class="only-web">
                        <a href="<?= $baseUrl ?>/storage/<?= ltrim($item['bukti'], '/') ?>" target="_blank" class="btn btn-sm btn-success">
                          <i class="fas fa-eye"></i>
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
                <tr><td colspan="6">Tidak ada data absensi untuk bulan/tahun ini.</td></tr>
              <?php endif; ?>
            <?php else: ?>
              <tr><td colspan="6">Silakan pilih bulan dan tahun terlebih dahulu.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <a href="dashboard_karyawan.php" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
      </a>
    </div>
  </div>
</div>

<!-- Tambahkan library jsPDF dan html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function exportToPDF() {
  const table = document.getElementById("rekapTable");
  html2canvas(table).then(canvas => {
    const imgData = canvas.toDataURL('image/png');
    const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
    const pageWidth = pdf.internal.pageSize.getWidth();
    const imgProps = pdf.getImageProperties(imgData);
    const pdfWidth = pageWidth - 20;
    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
    let y = 20;

    pdf.text("Rekap Absensi - <?= htmlspecialchars($nama) ?>", 10, 15);
    pdf.addImage(imgData, 'PNG', 10, y, pdfWidth, pdfHeight);
    pdf.save(`rekap_absensi_karyawan_<?= $selectedBulan . "_" . $selectedTahun ?>.pdf`);
  });
}
</script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>