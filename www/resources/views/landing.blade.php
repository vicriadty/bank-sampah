<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #28a745;
            /* Green */
            --secondary-color: #f8f9fa;
            /* Light Gray */
            --dark-color: #343a40;
            /* Dark Gray */
            --light-color: #ffffff;
            /* White */
            --gold-color: #febd14
                /* Gold */
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background-color: var(--light-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color);
        }

        .nav-link {
            color: var(--dark-color);
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .btn-login {
            background-color: var(--primary-color);
            color: var(--light-color);
            border: none;
            border-radius: 20px;
            padding: 8px 20px;
        }

        .btn-login:hover {
            background-color: #218838;
            color: var(--light-color);
        }

        .hero-section {
            padding: 150px 0;
            background-color: var(--secondary-color);
        }

        .hero-text h1 {
            font-size: 3rem;
            font-weight: bold;
            color: var(--dark-color);
        }

        .hero-text p {
            font-size: 1.2rem;
            color: var(--dark-color);
        }

        .btn-cta {
            background-color: var(--primary-color);
            color: var(--light-color);
            padding: 15px 30px;
            font-size: 1.2rem;
            border-radius: 30px;
            text-transform: uppercase;
        }

        .btn-cta:hover {
            background-color: #218838;
        }

        .hero-image {
            text-align: center;
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
            border-radius: 20px;
        }

        .navbar .dropdown-menu {
            border-radius: 10px;
        }

        .navbar .dropdown-item {
            color: var(--dark-color);
        }

        .navbar .dropdown-item:hover {
            background-color: var(--primary-color);
            color: var(--light-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        @media (max-width: 768px) {
            .hero-text h1 {
                font-size: 2.5rem;
            }

            .hero-section .row {
                flex-direction: column-reverse;
            }

            .hero-image {
                margin-top: 40px;
            }
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Bank Sampah</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#informasi">Informasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#berita">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang-kami">Tentang Kami</a>
                    </li>
                </ul>
                @auth
                    <div class="m-3">
                        <span>Selamat Datang, {{ Auth::user()->username }}</span>
                    </div>
                    <div class="nav-item dropdown">
                        <a class="btn btn-sm btn-login dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">

                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item"
                                    href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('nasabah.dashboard') }}">Dashboard</a>
                            </li>
                            <!-- <li><a class="dropdown-item" href="{{ route('settings.edit') }}">Settings</a></li> -->
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>

                    <div class="topbar-divider d-none d-sm-block"></div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-login">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 hero-text">
                    <h1>Ubah <span class="text-success">Sampah</span> Jadi <span style="color: #febd14">Emas</span>,
                        Bangun
                        Masa Depanmu!</h1>
                    <p class="my-4">Jangan biarkan sampah terbuang sia-sia. Mulai kebiasaan memilah dan setorkan
                        sampahmu ke Bank Sampah. Kumpulkan saldo rupiahnya, konversikan langsung menjadi tabungan emas
                        murni, dan mulailah investasi jangka panjangmu hari ini!</p>
                    <a href="{{ route('register') }}" class="btn btn-cta">Daftar Nasabah</a>
                </div>
                <div class="col-md-6 hero-image">
                    <img src="/images/hero-image2.png" alt="Ilustrasi Lingkungan" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Informasi Section -->
    <section id="informasi" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <h2>Informasi</h2>
                    <p>Disini Anda dapat menemukan berbagai informasi mengenai jenis sampah yang diterima, jadwal
                        setoran, dan tips memilah sampah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Berita Section -->
    <section id="berita" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <h2>Berita</h2>
                    <p>Ikuti berita terbaru dari kami, termasuk kegiatan, promo, dan informasi penting lainnya seputar
                        bank sampah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Kami Section -->
    <section id="tentang-kami" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <h2>Tentang Kami</h2>
                    <p>Kami adalah Bank Sampah yang berdedikasi untuk menciptakan lingkungan yang lebih bersih dan
                        sehat, sekaligus memberikan manfaat ekonomi bagi masyarakat.</p>
                </div>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
