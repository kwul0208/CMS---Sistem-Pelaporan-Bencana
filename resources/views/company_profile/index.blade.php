<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pepor Apps - Pencatatan Penduduk Digital</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background: #f7f9fc; color: #333; }

    header {
      background: #0078D7;
      color: white;
      padding: 20px 60px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header h1 { font-size: 1.5rem; }
    header nav a {
      color: white;
      margin-left: 20px;
      text-decoration: none;
      font-weight: 500;
    }
    header nav a:hover { text-decoration: underline; }

    .hero {
      text-align: center;
      padding: 100px 20px;
      background: linear-gradient(135deg, #0078D7, #00A3FF);
      color: white;
    }
    .hero h2 { font-size: 2.5rem; margin-bottom: 20px; }
    .hero p { font-size: 1.1rem; margin-bottom: 30px; }
    .hero a {
      background: white;
      color: #0078D7;
      padding: 12px 30px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
    }
    .hero a:hover { background: #eaf4ff; }

    .features {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      padding: 80px 40px;
      gap: 40px;
    }
    .feature {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      width: 300px;
      padding: 30px;
      text-align: center;
    }
    .feature h3 { color: #0078D7; margin-bottom: 10px; }

    footer {
      background: #003A75;
      color: white;
      text-align: center;
      padding: 20px;
      margin-top: 60px;
    }
    footer a { color: #90c9ff; text-decoration: none; }
  </style>
</head>
<body>

  <header>
    <h1>Pepor Apps</h1>
    <nav>
      <a href="#features">Fitur</a>
      <a href="#download">Download</a>
    </nav>
  </header>

  <section class="hero">
    <h2>Pencatatan Penduduk Lebih Mudah & Cepat</h2>
    <p>Pepor Apps membantu petugas dan masyarakat dalam mencatat, memperbarui, dan mengelola data kependudukan secara digital dan aman.</p>
    <a href="#download">Mulai Sekarang</a>
  </section>

  <section id="features" class="features">
    <div class="feature">
      <h3>📋 Pendataan Mudah</h3>
      <p>Input data penduduk hanya dalam hitungan detik. Semua tersimpan otomatis di cloud.</p>
    </div>
    <div class="feature">
      <h3>🔒 Data Aman</h3>
      <p>Seluruh data dienkripsi dan hanya dapat diakses oleh petugas yang berwenang.</p>
    </div>
    <div class="feature">
      <h3>📱 Akses Mobile</h3>
      <p>Gunakan aplikasi di perangkat Android atau iOS untuk kemudahan di lapangan.</p>
    </div>
  </section>

  <footer>
    <p>© 2025 Pepor Apps. All rights reserved. | <a href="privacy.html">Kebijakan Privasi</a></p>
  </footer>

</body>
</html>
