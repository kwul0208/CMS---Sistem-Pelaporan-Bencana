<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Work for Us - Pencatatan Kinerja</title>
  <style>
    /* ====== GLOBAL STYLE ====== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      background: #f9fbfd;
      color: #333;
      line-height: 1.6;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    /* ====== HEADER ====== */
    header {
      background: linear-gradient(90deg, #007bff, #00c6ff);
      color: #fff;
      padding: 1.2rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    header h1 {
      font-size: 1.5rem;
      font-weight: 600;
    }

    nav a {
      margin-left: 1.5rem;
      font-weight: 500;
      transition: 0.3s;
    }

    nav a:hover {
      color: #f9f9f9;
      text-decoration: underline;
    }

    /* ====== HERO SECTION ====== */
    .hero {
      text-align: center;
      padding: 5rem 2rem;
      background: linear-gradient(to bottom right, #e3f2fd, #ffffff);
    }

    .hero img {
      width: 120px;
      height: 120px;
      margin-bottom: 1rem;
    }

    .hero h2 {
      font-size: 2.5rem;
      color: #222;
    }

    .hero p {
      margin-top: 1rem;
      color: #555;
      font-size: 1.1rem;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    .cta-btn {
      display: inline-block;
      margin-top: 2rem;
      background: #007bff;
      color: #fff;
      padding: 0.9rem 1.8rem;
      border-radius: 8px;
      font-size: 1.1rem;
      transition: 0.3s;
    }

    .cta-btn:hover {
      background: #005fcc;
    }

    /* ====== FEATURES SECTION ====== */
    .features {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      padding: 4rem 2rem;
      background: #fff;
    }

    .feature-card {
      background: #f5f9ff;
      border-radius: 12px;
      padding: 2rem;
      text-align: center;
      transition: 0.3s;
    }

    .feature-card:hover {
      background: #eaf3ff;
      transform: translateY(-5px);
    }

    .feature-card h3 {
      color: #007bff;
      margin-bottom: 1rem;
    }

    /* ====== FOOTER ====== */
    footer {
      background: #0a0a23;
      color: #fff;
      text-align: center;
      padding: 2rem;
    }

    footer a {
      color: #00c6ff;
      text-decoration: underline;
    }

    /* ====== PRIVACY POLICY SECTION ====== */
    .privacy {
      padding: 4rem 2rem;
      background: #fafafa;
      max-width: 900px;
      margin: 0 auto;
    }

    .privacy h2 {
      color: #007bff;
      margin-bottom: 1rem;
    }

    .privacy p {
      margin-bottom: 1rem;
      color: #444;
    }

    .contact-box {
      margin-top: 2rem;
      background: #e8f0fe;
      padding: 1.5rem;
      border-radius: 10px;
    }

    .contact-box p {
      margin: 0.3rem 0;
    }
  </style>
</head>
<body>
  <header>
    <h1>Work for Us</h1>
    <nav>
      <a href="#features">Fitur</a>
      <a href="#privacy">Kebijakan Privasi</a>
      <a href="#download">Unduh</a>
    </nav>
  </header>

  <section class="hero">
    <img src="A_flat_design_logo_for_a_mobile_application_named_.png" alt="Work for Us Logo">
    <h2>Pencatatan Kinerja yang Mudah & Efisien</h2>
    <p>“Work for Us” membantu tim Anda mencatat, memantau, dan menganalisis kinerja harian dengan cepat dan akurat — semua dalam satu aplikasi.</p>
    <a href="#download" class="cta-btn">Mulai Sekarang</a>
  </section>

  <section id="features" class="features">
    <div class="feature-card">
      <h3>📊 Laporan Otomatis</h3>
      <p>Hasil kinerja tersaji dalam bentuk grafik dan metrik yang mudah dipahami.</p>
    </div>
    <div class="feature-card">
      <h3>📅 Manajemen Harian</h3>
      <p>Catat aktivitas, target, dan capaian setiap anggota tim dengan cepat.</p>
    </div>
    <div class="feature-card">
      <h3>🔔 Notifikasi Cerdas</h3>
      <p>Dapatkan pengingat untuk aktivitas penting dan deadline kinerja.</p>
    </div>
  </section>

  <section id="privacy" class="privacy">
    <h2>Kebijakan Privasi</h2>
    <p>
      Aplikasi <strong>Work for Us</strong> menghargai privasi pengguna. Kami tidak akan membagikan data pribadi Anda kepada pihak ketiga tanpa izin.
      Semua data aktivitas dan laporan hanya digunakan untuk keperluan internal aplikasi.
    </p>

    <p>
      Kami dapat mengumpulkan informasi seperti nama, email, dan data penggunaan aplikasi untuk meningkatkan pengalaman pengguna.
    </p>

    <p>
      Dengan menggunakan aplikasi ini, Anda menyetujui kebijakan privasi yang berlaku.
      Jika ada perubahan, kami akan memberitahukan pengguna melalui pembaruan aplikasi.
    </p>

    <div class="contact-box">
      <h3>📩 Kontak Kami</h3>
      <p>Email: support@workforus.app</p>
      <p>Telepon: +62 812 3456 7890</p>
      <p>Alamat: Jakarta, Indonesia</p>
    </div>
  </section>

  <footer id="download">
    <p>© 2025 Work for Us. Semua Hak Dilindungi.</p>
    <p><a href="#privacy">Kebijakan Privasi</a> | <a href="#features">Tentang Kami</a></p>
  </footer>
</body>
</html>
