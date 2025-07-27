<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Absensi Karyawan</title>

  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"/>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/index.css"/>
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet"/>

  <style>
    html {
      scroll-behavior: smooth;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<header class="navbar">
  <div class="container">
    <div class="logo-wrapper d-flex align-items-center">
      <div class="logo-box">
        <img src="img/ptpn4.png" alt="Logo" />
      </div>
      <h1 class="logo m-0 ms-2">ABSENSI KARYAWAN</h1>
    </div>

    <nav>
      <ul>
        <li><a href="#home">Home</a></li>
        <li><a href="#tentang">Tentang</a></li>
        <li><a href="#kontak">Kontak</a></li>
      </ul>
    </nav>
  </div>
</header>

<!-- HERO SECTION -->
<section id="home" class="hero position-relative">
  <div class="slideshow-container">
    <div class="slide active" style="background-image: url('img/kebun1.jpeg');"></div>
    <div class="slide" style="background-image: url('img/sawit.jpeg');"></div>
    <div class="slide" style="background-image: url('img/sawit3.jpeg');"></div>

    <a class="prev" onclick="plusSlides(-1)">❮</a>
    <a class="next" onclick="plusSlides(1)">❯</a>
  </div>
  <div class="hero-content text-center">
    <h1 class="fw-bold text-white">Selamat Datang</h1>
    <p class="text-white">Website ini berfungsi sebagai absensi karyawan dan sistem informasi karyawan..</p>
    <a href="login.php" class="btn btn-light fw-bold px-4 py-2 mt-3">Login</a>
  </div>
</section>

<!-- TENTANG (Versi Dua Kolom) -->
<!-- TENTANG (Versi Dua Kolom) -->
<section id="tentang" class="py-5 bg-light">
  <div class="container">
    <div class="row align-items-start">
      <!-- KIRI: PENJELASAN TENTANG -->
      <div class="col-md-7" data-aos="fade-right">
        <h2 class="fw-bold text-success">TENTANG KAMI</h2>
        <h5 class="mb-3">Profil PT Perkebunan Nusantara IV</h5>
        <p><strong>Pendirian</strong><br>
        PT Perkebunan Nusantara IV pasca aksi restrukturisasi atau yang sering disebut PalmCo merupakan 
        Subholding PT Perkebunan Nusantara III (Persero) dengan portofolio komoditi utama kelapa sawit dan 
        dibentuk melalui penggabungan PTPN V, VI dan XIII ke dalam PTPN IV sebagai entitas bertahan, serta 
        pemisahan tidak murni PTPN III (Persero) ke dalam PTPN IV. Secara efektif tergabung pada tanggal 1 
        Desember 2023 sebagaimana tertuang di dalam Akta Penggabungan Nomor 01 tanggal 01 Desember 2023 yang 
        dibuat dihadapan Nanda Fauz Iwan, S.H., M.Kn., Notaris di Jakarta Selatan dan telah mendapatkan bukti 
        penerimaan pemberitahuan penggabungan Perseroan berdasarkan Surat Menteri Hukum dan Hak Asasi Manusia 
        Nomor AHU-AH.01.03-0149887 tanggal 01 Desember 2023 perihal Penerimaan Pemberitahuan Penggabungan 
        Perseroan PT Perkebunan Nusantara IV.  Adapun perubahan anggaran dasar Perseroan telah dinyatakan 
        dalam Akta Nomor 02 tanggal 01 Desember 2023 yang dibuat dihadapan Nanda Fauz Iwan, S.H., M.Kn., 
        Notaris di Jakarta Selatan dan telah mendapat persetujuan dari Menteri Hukum dan Hak Asasi Manusia 
        berdasarkan Keputusan Nomor AHU-0074926.AH.01.02.Tahun 2023 tanggal 01 Desember 2023 tentang 
        Persetujuan Perubahan Anggaran Dasar Perseroan Terbatas PT Perkebunan Nusantara IV serta 
        pemberitahuannya telah diterima oleh Menteri Hukum dan Hak Asasi Manusia berdasarkan Surat Nomor 
        AHU-AH.01.03-0149887 tanggal 01 Desember 2023 perihal Penerimaan Pemberitahuan Perubahan Anggaran 
        Dasar PT Perkebunan Nusantara IV; dan Akta Nomor 08 tanggal 01 Desember 2023 yang dibuat dihadapan 
        Nanda Fauz Iwan, S.H., M.Kn., Notaris di Jakarta Selatan dan telah mendapat persetujuan dari Menteri 
        Hukum dan Hak Asasi Manusia berdasarkan Keputusan Nomor AHU-0076469.AH.01.02. ahun 2023 tanggal 07 
        Desember 2023 tentang Persetujuan Perubahan Anggaran Dasar Perseroan Terbatas  PT Perkebunan Nusantara 
        IV.         
      </p>
      </div>

      <!-- KANAN: GAMBAR UNTUK PENDIRIAN -->
      <div class="col-md-5 mb-4" data-aos="fade-left">
        <div class="image-box text-center">
          <img src="img/sawit4.jpeg" alt="Sejarah Sawit" class="img-fluid rounded shadow" />
        </div>
      </div>

      <!-- KOMODITAS: TEKS KIRI, GAMBAR KANAN -->
      <div class="col-md-7 order-md-1 mt-4" data-aos="fade-right">
        <p><strong>Komoditas dan Bidang Usaha</strong><br>
        Dari sisi komoditas, PalmCo mengelola areal kelapa sawit tertanam seluas 434.597 Ha sebagai komoditas 
        terbesar. Selanjutnya berturut-turut karet seluas 46.534 Ha, teh 6.255 Ha dan Kopi 501 Ha. Untuk 
        Pabrik Pengolahan, PalmCo mengolah bahan bakunya di fasilitas milik sendiri yang terdiri dari 54 
        Fasilitas Pabrik Kelapa Sawit, 12 Pabrik Karet, 4 Pabrik Teh. Adapun Unit kerja/usaha PalmCo tersebut 
        tersebar di 8 provinsi, dengan jumlah karyawan berkisar 62,7 ribu orang. Bidang usaha PalmCo 
        sebagaimana tertuang di dalam Anggaran Dasar meliputi agro bisnis, agro industri dan lain sebagainya.
        </p>
      </div>
      <div class="col-md-5 order-md-2 mt-4" data-aos="fade-left">
        <div class="image-box text-center">
          <img src="img/visimisi.png" alt="Visi dan misi" class="img-fluid rounded shadow" />
        </div>
      </div>

      <!-- VISI MISI: GAMBAR KIRI, TEKS KANAN -->
      <div class="col-md-5 mt-4 order-md-1" data-aos="fade-right">
        <div class="image-box text-center">
          <img src="img/komoditas.jpeg" alt="Komoditas" class="img-fluid rounded shadow" />
        </div>
      </div>
      <div class="col-md-7 mt-4 order-md-2" data-aos="fade-left">
        <p><strong>Visi:</strong><br>
          "Menjadi perusahaan produsen minyak kelapa sawit berkelanjutan terbesar di dunia, yang turut memastikan 
          penghidupan bagi masyarakat dan menciptakan nilai bagi pemegang saham."
        </p>
        <p><strong>Misi:</strong></p>
        <ul>
          <li>Menjadi produsen minyak kelapa sawit terbesar di dunia dengan praktik operasional perkebunan terbaik yang berbasis digital dan industri hilir yang terintegrasi</li>
          <li>Mendukung ketahanan pangan nasional, meningkatkan kesejahteraan petani plasma dan masyarakat lokal</li>
          <li>Meningkatkan kapabilitas dan potensi karyawan melalui program pengembangan yang berkelanjutan</li>
          <li>Memelihara lingkungan hidup dan sosial dengan mengimplentasikan praktik keberlanjutan</li>
          <li>Menjunjung standar etika yang tinggi melalui implementasi dari tata kelola perusahaan yang baik</li>
        </ul>
      </div>
    </div>
  </div>
</section>



<!-- KONTAK -->
<section id="kontak" class="py-5 bg-light">
  <div class="container d-flex justify-content-center">
    <div class="card p-4 shadow-sm text-center" style="max-width: 600px; width: 100%;">
      <h2 class="fw-bold">Kontak</h2>
      <p><strong>PT Perkebunan Nusantara IV</strong><br>
        Gedung Agro Plaza Lt. 8, Jl. HR. Rasuna Said, Jakarta Selatan</p>
      <p><strong>Telepon:</strong> +62 21 31119000</p>
      <p><strong>Email:</strong><br>
        corsecpalmcon4@gmail.com<br>
        ptpnusantara4@ptpn4.co.id</p>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer-green text-white text-center py-3">
  &copy; 2025 Absensi Karyawan. All rights reserved.
</footer>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init();</script>

<!-- SLIDESHOW SCRIPT -->
<script>
  let slideIndex = 0;
  const slides = document.querySelectorAll(".slide");

  function showSlide(n) {
    slides.forEach(slide => slide.classList.remove("active"));
    slideIndex = (n + slides.length) % slides.length;
    slides[slideIndex].classList.add("active");
  }

  function plusSlides(n) {
    showSlide(slideIndex + n);
  }

  setInterval(() => plusSlides(1), 5000);
</script>
</body>
</html>
