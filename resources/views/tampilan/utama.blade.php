<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Workshop Keguruan</title>

  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
    }

    .navbar-brand i {
      font-size: 1.5rem;
    }

    .btn-primary {
      background-color: #2e86de;
      border: none;
    }

    .btn-primary:hover {
      background-color: #1b6ec2;
    }

    .hero-section {
      padding: 100px 20px;
      background: linear-gradient(120deg, #ffffff, #f0f4ff);
      text-align: center;
      animation: fadeIn 1s ease-in;
    }

    .hero-section h1 {
      font-size: 2.8rem;
      font-weight: 700;
      color: #2e86de;
    }

    .hero-section p {
      font-size: 1.2rem;
      max-width: 600px;
      margin: 20px auto;
      color: #6c757d;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    footer {
      font-size: 0.9rem;
      background-color: #fff;
      border-top: 1px solid #eaeaea;
    }

    @media (max-width: 768px) {
      .hero-section h1 {
        font-size: 2rem;
      }

      .hero-section p {
        font-size: 1rem;
      }
    }
  </style>
</head>

<body class="d-flex flex-column min-vh-100">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="#">
        <i class="bi bi-mortarboard-fill me-2"></i>Workshop Keguruan
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
        aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
        <div class="d-flex gap-2 mt-3 mt-lg-0">
          <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-person-plus-fill me-1"></i> Sign Up
          </a>
          <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section flex-grow-1 d-flex align-items-center">
    <div class="container">
      <h1>Selamat Datang di Workshop Keguruan</h1>
      <p>
        Tingkatkan kompetensi dan profesionalitas Anda sebagai pendidik melalui pelatihan, workshop, dan sertifikasi terbaru.
      </p>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-3 mt-auto">
    <div class="container text-center text-muted">
      &copy; 2025 <i class="bi bi-book-fill me-1 text-primary"></i>Workshop Keguruan. Semua hak dilindungi.
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
