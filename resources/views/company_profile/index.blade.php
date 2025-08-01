<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Company Profile - E-Lapor</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      line-height: 1.6;
      background: #f9f9f9;
    }
    header {
      background: linear-gradient(to right, #8e2de2, #4a00e0);
      color: white;
      padding: 60px 20px;
      text-align: center;
    }
    header h1 {
      font-size: 36px;
      margin: 0;
    }
    header p {
      font-size: 18px;
      margin-top: 10px;
    }
    .section {
      padding: 40px 20px;
      max-width: 1000px;
      margin: auto;
    }
    .section h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #4a00e0;
    }
    .features {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 20px;
      text-align: center;
    }
    .feature-box {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .feature-box img {
      width: 40px;
      margin-bottom: 10px;
    }
    .screenshots {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .screenshots img {
      max-width: 300px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    iframe {
      width: 100%;
      height: 400px;
      border: none;
      border-radius: 10px;
      margin-top: 20px;
    }
    footer {
      background: #4a00e0;
      color: white;
      text-align: center;
      padding: 30px 20px;
    }
  </style>
</head>
<body>

<header>
  <h1>E-Lapor</h1>
  <p>Sistem Informasi Pemantauan dan Laporan Saluran Drainase & Banjir</p>
</header>

<section class="section">
  <h2>Tentang Aplikasi</h2>
  <p>
    Aplikasi ini merupakan platform digital yang dikembangkan untuk mendukung kegiatan monitoring saluran drainase dan penanggulangan banjir. Didesain untuk digunakan oleh admin dan petugas lapangan, aplikasi ini memungkinkan pencatatan data saluran, pelaporan tanggap darurat, serta pemetaan lokasi pekerjaan melalui integrasi dengan Google Maps.
  </p>
</section>

<section class="section">
  <h2>Fitur Unggulan</h2>
  <div class="features">
    <div class="feature-box">
      <img src="https://img.icons8.com/emoji/48/000000/warning-emoji.png" />
      <p>Tanggap Darurat</p>
    </div>
    <div class="feature-box">
      <img src="https://img.icons8.com/color/48/000000/task.png" />
      <p>Laporan Rutin</p>
    </div>
    <div class="feature-box">
      <img src="https://img.icons8.com/color/48/000000/open-book--v2.png" />
      <p>Laporan Swakelola</p>
    </div>
    <div class="feature-box">
      <img src="https://img.icons8.com/color/48/000000/water.png" />
      <p>Data Saluran</p>
    </div>
    <div class="feature-box">
      <img src="https://img.icons8.com/color/48/000000/org-structure.png" />
      <p>Struktur Organisasi</p>
    </div>
    <div class="feature-box">
      <img src="https://img.icons8.com/color/48/000000/facebook-like--v1.png" />
      <p>Social Media</p>
    </div>
  </div>
</section>

{{-- <section class="section">
  <h2>Tampilan Aplikasi</h2>
  <div class="screenshots">
    <img src="screenshot1.jpg" alt="Beranda">
    <img src="screenshot2.jpg" alt="Detail Laporan">
    <img src="screenshot3.jpg" alt="Daftar Laporan">
  </div>
</section> --}}

{{-- <section class="section">
  <h2>Peta Monitoring Saluran</h2>
  <iframe src="https://www.google.com/maps/d/u/0/embed?mid=1mRnhYUTyWzMQrjAXW_0Ut_7IYgXZVgM" allowfullscreen></iframe>
</section> --}}

<footer>
  <p>© 2025 E-Lapor - Dikembangkan oleh elapor</p>
  <p>Email: elapor@gmail.com | Telepon: (021) 123-4567</p>
</footer>

</body>
</html>
