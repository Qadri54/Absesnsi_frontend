<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}
include '../config.php';
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$ch = curl_init("$api_base_url/karyawan?role=admin");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$karyawans = $data['data'] ?? [];
// PAGINATION SETUP
$perPage = 10;
$totalData = count($karyawans);
$totalPages = ceil($totalData / $perPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($totalPages, $currentPage));

// Potong array data
$startIndex = ($currentPage - 1) * $perPage;
$paginatedData = array_slice($karyawans, $startIndex, $perPage);

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Karyawan</title>
  <link rel="stylesheet" href="../css/data_admin.css">
  <link rel="stylesheet" href="../css/popup.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


</head>
<body>

<?php include '../sidebar_admin.php'; ?>



<div class="main-content">
  <h2 class="mb-4">📋 Data Karyawan</h2>

  <!-- ALERT Bootstrap -->
  <div class="container px-0">
    <?php if (isset($_SESSION['sukses'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['sukses']; unset($_SESSION['sukses']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
      </div>
    <?php elseif (isset($_SESSION['kesalahan'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['kesalahan']; unset($_SESSION['kesalahan']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
      </div>
    <?php endif; ?>
  </div>

  <button class="btn-tambah" onclick="toggleForm('popupForm')">+ Tambah Karyawan</button>

  <!-- Form Tambah -->
  <div class="popup-form" id="popupForm">
   
<form id="formTambah" enctype="multipart/form-data" class="form-admin">
    <h3>Tambah Karyawan</h3>
<div id="tambah-alert" class="alert d-none" role="alert"></div>

      <label>Nama Lengkap</label>
      <input type="text" name="nama" required>

      <label>NIK SAP</label>
      <input type="text" name="nik_sap" required>

      <label>Username</label>
      <input type="text" name="username" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Jabatan</label>
      <input type="text" name="jabatan" required>

      <label>Status Karyawan</label>
      <select name="status_karyawan">
        <option value="">-- Pilih Status --</option>
        <option value="PKWT">PKWT</option>
        <option value="Pegawai">Pegawai</option>
      </select>

      <label>Afdeling</label>
      <input type="text" name="afdeling" required>

      <label>Tanggal Lahir</label>
      <input type="date" name="tanggal_lahir" required>

      <label>No SPK</label>
      <input type="text" name="no_spk" required>

      <label>Tanggal Kontrak</label>
      <input type="date" name="tgl_kontrak">

      <label>Tanggal Berakhir Kontrak</label>
      <input type="date" name="tgl_berakhir_kontrak">

      <label>Foto</label>
      <input type="file" name="foto" accept="image/*">

      <div class="form-actions">
        <button type="submit">Simpan</button>
        <button type="button" onclick="toggleForm('popupForm')">Batal</button>
      </div>
    </form>
  </div>

  <!-- Form Edit -->
  <div class="popup-form" id="editForm">
  <div id="edit-alert" class="alert alert-danger d-none mt-3"></div>
<form id="formEdit" enctype="multipart/form-data" class="form-admin">
  <h3>Edit Karyawan</h3>
      <input type="hidden" name="id" id="edit_id">

      <label>Nama Lengkap</label>
      <input type="text" name="nama" id="edit_nama" required>

      <label>NIK SAP</label>
      <input type="text" name="nik_sap" id="edit_nik_sap" required>

      <label>Username</label>
      <input type="text" name="username" id="edit_username" required>

      <label>Password (kosongkan jika tidak diubah)</label>
      <input type="password" name="password" id="edit_password" placeholder="(Opsional)">

      <label>Jabatan</label>
      <input type="text" name="jabatan" id="edit_jabatan" required>

      <label>Status Karyawan</label>
      <select name="status_karyawan" id="edit_status_karyawan">
        <option value="PKWT">PKWT</option>
        <option value="Pegawai">Pegawai</option>
      </select>

      <label>Afdeling</label>
      <input type="text" name="afdeling" id="edit_afdeling" required>

      <label>Tanggal Lahir</label>
      <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" required>

      <label>No SPK</label>
      <input type="text" name="no_spk" id="edit_no_spk" required>

      <label>Tanggal Kontrak</label>
      <input type="date" name="tgl_kontrak" id="edit_tgl_kontrak">

      <label>Tanggal Berakhir Kontrak</label>
      <input type="date" name="tgl_berakhir_kontrak" id="edit_tgl_berakhir_kontrak">

      <label>Foto</label>
      <input type="file" name="foto" accept="image/*">

      <div class="form-actions">
        <button type="submit">Update</button>
        <button type="button" onclick="toggleForm('editForm')">Batal</button>
      </div>
    </form>
  </div>

  <!-- Tabel Karyawan -->
  <!-- Tabel Karyawan -->
<table class="table table-bordered table-striped align-middle">
<thead class="custom-green">
    <tr>
      <th>No</th>
      <th>Nama</th>
      <th>NIK SAP</th>
      <th>Jabatan</th>
      <th>Status</th>
      <th>Afdeling</th>
      <th>SPK</th>
      <th>Kontrak</th>
      <th>Akhir</th>
      <th>Foto</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody class="text-center">
    <?php
    $no = 1;
    foreach ($paginatedData as $k) {
      $fotoFile = $k['foto'] ?? '';
      $baseURL = str_replace('/api', '', $api_base_url);
      $fotoPath = $fotoFile ? $baseURL . "/uploads/foto_karyawan/" . $fotoFile : "../img/default.png";
      $statusKaryawan = $k['status_karyawan'] ?? 'PKWT';

      $tgl_kontrak = isset($k['tgl_kontrak']) ? explode('T', $k['tgl_kontrak'])[0] : '-';
      $tgl_berakhir = isset($k['tgl_berakhir_kontrak']) ? explode('T', $k['tgl_berakhir_kontrak'])[0] : '-';

     echo "<tr>
  <td>{$no}</td>
  <td>{$k['nama']}</td>
  <td>{$k['nik_sap']}</td>
  <td>{$k['jabatan']}</td>
  <td>{$statusKaryawan}</td>
  <td>{$k['afdeling']}</td>
  <td>{$k['no_spk']}</td>
  <td>{$tgl_kontrak}</td>
  <td>{$tgl_berakhir}</td>
  <td><img src='{$fotoPath}' width='60' height='60' style='object-fit:cover;border-radius:6px;'></td>
<td>
  <button class=\"btn btn-warning btn-sm mb-1\" title='Edit' onclick='isiFormEdit(" . json_encode($k, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ")'>
    <i class='bi bi-pencil-square'></i>
  </button>
  <a href='hapus_karyawan.php?id={$k['id']}'
     class='btn btn-danger btn-sm mb-1'
     title='Hapus'
     onclick=\"return confirm('Yakin ingin menghapus data karyawan ini?')\">
    <i class='bi bi-trash'></i>
  </a>
</td>

</tr>";
      $no++;
    }
    ?>
  </tbody>
</table>
<!-- Pagination -->
<?php
$range = 2; // Menampilkan 2 halaman sebelum dan sesudah halaman aktif
$start = max(1, $currentPage - $range);
$end = min($totalPages, $currentPage + $range);
?>

<nav class="mt-4">
  <ul class="pagination justify-content-center">
    <?php if ($currentPage > 1): ?>
      <li class="page-item"><a class="page-link" href="?page=<?= $currentPage - 1 ?>">« Prev</a></li>
    <?php endif; ?>

    <?php
    $showPages = 5;
    $half = floor($showPages / 2);
    $start = max(1, $currentPage - $half);
    $end = min($totalPages, $currentPage + $half);

    if ($start > 1) {
      echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
      if ($start > 2) {
        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
      }
    }

    for ($i = $start; $i <= $end; $i++) {
      echo '<li class="page-item ' . ($i == $currentPage ? 'active' : '') . '">';
      echo '<a class="page-link" href="?page=' . $i . '">' . $i . '</a>';
      echo '</li>';
    }

    if ($end < $totalPages) {
      if ($end < $totalPages - 1) {
        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
      }
      echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    ?>

    <?php if ($currentPage < $totalPages): ?>
      <li class="page-item"><a class="page-link" href="?page=<?= $currentPage + 1 ?>">Next »</a></li>
    <?php endif; ?>
  </ul>
</nav>



</div>

<script>
  
  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, ' ');
  }

  function toggleForm(id) {
    const form = document.getElementById(id);
    form.style.display = form.style.display === "block" ? "none" : "block";
  }

  function formatDateISO(str) {
    if (!str) return '';
    return str.includes('T') ? str.split('T')[0] : str;
  }

  function isiFormEdit(data) {
    toggleForm('editForm');
    $('#formEdit')[0].reset();
    $('#edit_id').val(data.id);
    $('#edit_nama').val(data.nama);
    $('#edit_nik_sap').val(data.nik_sap);
    $('#edit_username').val(data.username);
    $('#edit_jabatan').val(data.jabatan);
    $('#edit_status_karyawan').val(data.status_karyawan || 'PKWT');
    $('#edit_afdeling').val(data.afdeling);
    $('#edit_no_spk').val(data.no_spk);
    $('#edit_tanggal_lahir').val(formatDateISO(data.tanggal_lahir));
    $('#edit_tgl_kontrak').val(formatDateISO(data.tgl_kontrak));
    $('#edit_tgl_berakhir_kontrak').val(formatDateISO(data.tgl_berakhir_kontrak));
  }

  function handleError(xhr, $alert, defaultMsg) {
    let msg = `<strong>❌ ${defaultMsg}</strong><ul>`;
    try {
      const res = JSON.parse(xhr.responseText);
      if (res.errors) {
        for (let field in res.errors) {
          res.errors[field].forEach(m => {
            msg += `<li><strong>${capitalize(field)}</strong>: ${m}</li>`;
          });
        }
      } else if (res.message) {
        msg += `<li>${res.message}</li>`;
      }
    } catch (e) {
      msg += `<li>${xhr.responseText}</li>`;
    }
    msg += "</ul>";
    $alert.removeClass('d-none alert-success').addClass('alert alert-danger').html(msg);
  }

  $('#formEdit').on('submit', function (e) {
    e.preventDefault();
    const $alert = $('#edit-alert');
    $alert.removeClass('d-none alert-danger alert-success').empty();

    const formData = new FormData(this);
    formData.append('role', 'admin');
    formData.append('user_id', $('#edit_id').val());

    $.ajax({
      url: 'update_karyawan.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (res) {
        if (res.message && (res.message.includes("berhasil") || res.message === "success")) {
          $alert.addClass('alert-success').html(" " + res.message).removeClass('d-none');
          setTimeout(() => location.reload(), 1500);
        } else if (res.message === 'Validasi gagal' && res.errors) {
          let msg = "<strong> Validasi gagal:</strong><ul>";
          for (let field in res.errors) {
            res.errors[field].forEach(m => {
              msg += `<li><strong>${capitalize(field)}</strong>: ${m}</li>`;
            });
          }
          msg += "</ul>";
          $alert.addClass('alert-danger').html(msg).removeClass('d-none');
        } else {
          $alert.addClass('alert-danger').html("❌ " + (res.message || "Gagal memperbarui data.")).removeClass('d-none');
        }
      },
      error: function (xhr) {
        handleError(xhr, $alert, 'Gagal memperbarui data karyawan:');
      }
    });
  });

  $('#formTambah').on('submit', function (e) {
    e.preventDefault();
    const $alert = $('#tambah-alert');
    $alert.removeClass('d-none alert-danger alert-success').empty();

    const formData = new FormData(this);
    if (!formData.get('status_karyawan')) {
      formData.set('status_karyawan', 'PKWT');
    }
    formData.append('role', 'admin');

    $.ajax({
      url: 'tambah_karyawan.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (res) {
        if (res.message === 'success' || res.message.includes('berhasil')) {
          $alert.addClass('alert-success').html("✅ Karyawan berhasil ditambahkan.").removeClass('d-none');
          setTimeout(() => location.reload(), 1500);
        } else {
          $alert.addClass('alert-danger').html("❌ " + (res.message || "Gagal menambahkan karyawan.")).removeClass('d-none');
        }
      },
      error: function (xhr) {
        handleError(xhr, $alert, 'Gagal menambahkan karyawan:');
      }
    });
  });
</script>

</body>
</html>
