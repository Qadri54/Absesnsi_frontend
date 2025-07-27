<?php
session_start();
include 'config.php'; // berisi $api_base_url = "http://127.0.0.1:8000/api"

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $postData = http_build_query([
            'username' => $username,
            'password' => $password
        ]);

        $ch = curl_init("$api_base_url/login");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $error = "❌ Tidak dapat terhubung ke server. Error: $curlError";
        } else {
            $data = json_decode($response, true);

            if ($httpCode === 200 && $data && $data['status'] === 'success') {
                $id = $data['data']['id'] ?? null;
                $role = $data['role'] ?? 'karyawan';

                if ($role === 'karyawan' && $id) {
                    $ch = curl_init("$api_base_url/karyawan/$id?role=karyawan&user_id=$id");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $detailResponse = curl_exec($ch);
                    curl_close($ch);

                    $detailData = json_decode($detailResponse, true);
                    if (isset($detailData['data'])) {
                        $_SESSION['user'] = $detailData['data'];
                        $_SESSION['user']['role'] = 'karyawan';
                    } else {
                        $error = "❌ Gagal mengambil data profil karyawan.";
                    }
                } elseif ($role === 'admin') {
                    $_SESSION['user'] = $data['data'] ?? [];
                    $_SESSION['user']['role'] = 'admin';
                }

                // Redirect jika berhasil login
                if (!empty($_SESSION['user'])) {
                    if ($_SESSION['user']['role'] === 'admin') {
                        header("Location: admin/dashboard_admin.php");
                    } else {
                        header("Location: karyawan/dashboard_karyawan.php");
                    }
                    exit;
                }
            } else {
                // Ambil pesan error dari API (jika ada)
                $apiMessage = $data['message'] ?? 'Login gagal. Username atau password salah.';
                $error = "❌ $apiMessage";
            }
        }
    } else {
        $error = "⚠️ Username dan password wajib diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Login - Absensi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="css/login.css" />
</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow p-4 login-box text-center w-100" style="max-width: 400px;">
    <h2 class="mb-4">Login</h2>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger py-2" role="alert">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required />
      </div>
        <div class="mb-3 position-relative">
        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required />
        <span class="toggle-password position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;">
          <i class="bi bi-eye-slash fs-5"></i>
        </span>
      </div>

      <button type="submit" class="btn btn-success w-100 fw-semibold">LOGIN</button>
    </form>

    <p class="mt-3"><a href="index.php" class="text-decoration-none text-secondary">← Kembali ke Beranda</a></p>
  </div>
</div>
<script>
  const togglePassword = document.querySelector('.toggle-password');
  const passwordInput = document.getElementById('password');

  togglePassword.addEventListener('click', function () {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    this.innerHTML = isPassword ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
