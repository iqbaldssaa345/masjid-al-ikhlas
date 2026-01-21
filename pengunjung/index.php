<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Jamaah | Masjid Al-Ikhlas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(180deg,#f0f4ff,#eef2f7);
}

/* NAVBAR */
.navbar{
    background:linear-gradient(135deg,#198754,#0d6efd);
}

/* HERO */
.hero{
    background:linear-gradient(135deg,#0d6efd,#198754);
    color:white;
    border-radius:32px;
    padding:50px;
    position:relative;
    overflow:hidden;
}
.hero::after{
    content:'';
    position:absolute;
    width:300px;
    height:300px;
    background:rgba(255,255,255,.15);
    border-radius:50%;
    top:-80px;
    right:-80px;
}

/* CARD MENU */
.card-menu{
    border:none;
    border-radius:28px;
    backdrop-filter:blur(10px);
    background:rgba(255,255,255,.9);
    transition:.45s;
}
.card-menu:hover{
    transform:translateY(-16px) scale(1.02);
    box-shadow:0 35px 70px rgba(0,0,0,.2);
}

/* ICON */
.icon{
    width:80px;
    height:80px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:36px;
    margin:0 auto 16px;
    color:white;
}
.icon-display{background:linear-gradient(135deg,#0d6efd,#4f8cff);}
.icon-info{background:linear-gradient(135deg,#198754,#39d98a);}
.icon-laporan{background:linear-gradient(135deg,#ffc107,#ff9f1c);}
.icon-infaq{background:linear-gradient(135deg,#dc3545,#ff6b6b);}

/* FOOTER */
.footer{
    font-size:14px;
    color:#6c757d;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark shadow-sm">
<div class="container-fluid px-4">
    <span class="navbar-brand fw-bold">
        🕌 Masjid Al-Ikhlas
    </span>

    <div class="text-white">
        <i class="bi bi-person-circle"></i>
        <?= htmlspecialchars($_SESSION['nama']); ?>
        <a href="../logout.php" class="btn btn-outline-light btn-sm ms-3">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>
</nav>

<div class="container py-5">

<!-- HERO -->
<div class="hero mb-5 shadow-lg">
<h3 class="fw-bold mb-2">Dashboard Jamaah</h3>
<p class="mb-0 fs-6">
Assalamu’alaikum, <b><?= htmlspecialchars($_SESSION['nama']); ?></b> 👋  
Selamat datang di Sistem Informasi Masjid Al-Ikhlas
</p>
</div>

<!-- MENU -->
<div class="row g-4">

<!-- DISPLAY -->
<div class="col-md-6 col-lg-3">
<a href="../display.php" target="_blank" class="text-decoration-none">
<div class="card card-menu h-100 text-center p-4">
<div class="icon icon-display">
<i class="bi bi-tv"></i>
</div>
<h6 class="fw-bold text-dark">Display Masjid</h6>
<p class="text-muted small">
Jadwal sholat & saldo infaq realtime
</p>
</div>
</a>
</div>

<!-- INFO -->
<div class="col-md-6 col-lg-3">
<a href="info.php" class="text-decoration-none">
<div class="card card-menu h-100 text-center p-4">
<div class="icon icon-info">
<i class="bi bi-megaphone-fill"></i>
</div>
<h6 class="fw-bold text-dark">Informasi Masjid</h6>
<p class="text-muted small">
Profil, agenda & pengumuman
</p>
</div>
</a>
</div>

<!-- LAPORAN -->
<div class="col-md-6 col-lg-3">
<a href="laporan.php" class="text-decoration-none">
<div class="card card-menu h-100 text-center p-4">
<div class="icon icon-laporan">
<i class="bi bi-chat-dots-fill"></i>
</div>
<h6 class="fw-bold text-dark">Laporan Jamaah</h6>
<p class="text-muted small">
Saran & aspirasi jamaah
</p>
</div>
</a>
</div>

<!-- INFAQ -->
<div class="col-md-6 col-lg-3">
<a href="infaq.php" class="text-decoration-none">
<div class="card card-menu h-100 text-center p-4">
<div class="icon icon-infaq">
<i class="bi bi-heart-fill"></i>
</div>
<h6 class="fw-bold text-dark">Infaq Jamaah</h6>
<p class="text-muted small">
Infaq aman & transparan
</p>
</div>
</a>
</div>

</div>
</div>

<footer class="text-center py-3 footer">
© <?= date('Y'); ?> Masjid Al-Ikhlas • Jamaah
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
