<?php include "config/koneksi.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Masjid Al-Ikhlas</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700;800&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:
        linear-gradient(rgba(0,0,0,.7),rgba(0,0,0,.7)),
        url('https://images.unsplash.com/photo-1609358905581-e53c6a1db1dd');
    background-size:cover;
    background-position:center;
    min-height:100vh;
    color:#fff;
}

.hero{
    min-height:100vh;
    display:flex;
    align-items:center;
}

.glass{
    background:rgba(255,255,255,.12);
    backdrop-filter:blur(14px);
    border-radius:32px;
    padding:60px 50px;
    box-shadow:0 30px 60px rgba(0,0,0,.45);
    border:1px solid rgba(255,255,255,.25);
}

.badge-custom{
    background:linear-gradient(135deg,#198754,#20c997);
    padding:10px 22px;
    border-radius:30px;
    font-size:14px;
    letter-spacing:.5px;
}

.card-feature{
    background:#fff;
    border:none;
    border-radius:26px;
    transition:.4s;
}

.card-feature:hover{
    transform:translateY(-14px) scale(1.02);
    box-shadow:0 25px 50px rgba(0,0,0,.35);
}

.icon-box{
    width:70px;
    height:70px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:32px;
    margin:0 auto 20px;
    color:#fff;
}

.icon-login{background:#0d6efd;}
.icon-display{background:#198754;}
.icon-infaq{background:#ffc107;color:#000;}

.btn-custom{
    border-radius:30px;
    padding:12px 28px;
    font-weight:600;
}

footer{
    font-size:14px;
    opacity:.85;
}
</style>
</head>

<body>

<div class="container hero">
<div class="row justify-content-center w-100">
<div class="col-lg-11 col-xl-10 text-center">

<div class="glass">

    <span class="badge badge-custom mb-4 d-inline-block">
        Sistem Informasi Masjid Digital
    </span>

    <h1 class="fw-bold display-4 mb-3">
        🕌 MASJID AL-IKHLAS
    </h1>

    <p class="lead mb-5">
        Transparansi Infaq • Display TV Masjid • Pelayanan Jamaah Terpadu
    </p>

    <div class="row g-4">

        <!-- LOGIN -->
        <div class="col-md-4">
            <div class="card card-feature h-100">
                <div class="card-body p-4">
                    <div class="icon-box icon-login">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Login Sistem</h5>
                    <p class="text-muted">
                        Admin, Petugas, dan Jamaah dengan hak akses masing-masing.
                    </p>
                    <a href="login.php" class="btn btn-primary btn-custom w-100">
                        Masuk Sistem
                    </a>
                </div>
            </div>
        </div>

        <!-- DISPLAY -->
        <div class="col-md-4">
            <div class="card card-feature h-100">
                <div class="card-body p-4">
                    <div class="icon-box icon-display">
                        <i class="bi bi-tv"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Display Masjid</h5>
                    <p class="text-muted">
                        Jadwal sholat, saldo infaq, dan pengumuman realtime.
                    </p>
                    <a href="display.php" class="btn btn-success btn-custom w-100">
                        Tampilkan Display
                    </a>
                </div>
            </div>
        </div>

        <!-- INFAQ -->
        <div class="col-md-4">
            <div class="card card-feature h-100">
                <div class="card-body p-4">
                    <div class="icon-box icon-infaq">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Infaq Jamaah</h5>
                    <p class="text-muted">
                        Transparan, aman, dan mudah untuk seluruh jamaah.
                    </p>
                    <a href="login.php" class="btn btn-warning btn-custom w-100 text-dark">
                        Infaq Sekarang
                    </a>
                </div>
            </div>
        </div>

    </div>

    <footer class="mt-5">
        <hr class="border-light">
        <p class="mb-0">
            © <?= date('Y'); ?> Masjid Al-Ikhlas<br>
            Sistem Informasi & Display Digital Masjid
        </p>
    </footer>

</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
