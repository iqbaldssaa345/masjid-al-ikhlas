<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}

/* ========================
   HITUNG DATA
======================== */
$total_laporan = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT COUNT(*) as total FROM laporan")
)['total'];

$total_infaq = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) as total FROM keuangan WHERE jenis='infaq'")
)['total'] ?? 0;

$total_pengeluaran = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) as total FROM keuangan WHERE jenis='dkm'")
)['total'] ?? 0;

$saldo = $total_infaq - $total_pengeluaran;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Petugas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(180deg,#eef2f7,#f8fafc);
}

/* NAVBAR */
.navbar{
    background:linear-gradient(135deg,#0d6efd,#20c997);
}

/* GLASS CARD */
.glass{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(12px);
    border-radius:20px;
}

/* MENU CARD */
.card-menu{
    border:none;
    border-radius:20px;
    transition:.35s;
}
.card-menu:hover{
    transform:translateY(-10px);
    box-shadow:0 20px 45px rgba(0,0,0,.15);
}

/* ICON */
.icon{
    font-size:38px;
}

/* STAT */
.stat{
    border-radius:20px;
    color:white;
}

/* FOOTER */
.footer{
    font-size:14px;
    color:#888;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark shadow">
<div class="container-fluid px-4">
    <span class="navbar-brand fw-bold">
        🕌 Dashboard Petugas
    </span>
    <div>
        <span class="text-white me-3">
            👋 <?= htmlspecialchars($_SESSION['nama']) ?>
        </span>
        <a href="../logout.php" class="btn btn-light btn-sm rounded-pill">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>
</nav>

<div class="container py-5">

<!-- HEADER -->
<div class="mb-4">
    <h3 class="fw-bold">Selamat Datang 👋</h3>
    <p class="text-muted">Kelola sistem masjid dengan cepat & modern</p>
</div>

<!-- STATISTIK -->
<div class="row g-4 mb-4">

<div class="col-md-3">
    <div class="stat p-3 shadow" style="background:#0d6efd">
        <small>Total Laporan</small>
        <h4><?= $total_laporan ?></h4>
    </div>
</div>

<div class="col-md-3">
    <div class="stat p-3 shadow" style="background:#198754">
        <small>Total Infaq</small>
        <h4>Rp <?= number_format($total_infaq,0,',','.') ?></h4>
    </div>
</div>

<div class="col-md-3">
    <div class="stat p-3 shadow" style="background:#dc3545">
        <small>Pengeluaran</small>
        <h4>Rp <?= number_format($total_pengeluaran,0,',','.') ?></h4>
    </div>
</div>

<div class="col-md-3">
    <div class="stat p-3 shadow" style="background:#20c997">
        <small>Saldo</small>
        <h4>Rp <?= number_format($saldo,0,',','.') ?></h4>
    </div>
</div>

</div>

<!-- MENU -->
<div class="row g-4">

<!-- DANA -->
<div class="col-md-6 col-lg-3">
<a href="dana.php" class="text-decoration-none">
<div class="card card-menu text-center p-4 glass">
    <div class="icon text-primary mb-2">
        <i class="bi bi-cash-stack"></i>
    </div>
    <h6 class="fw-bold text-dark">Dana Masjid</h6>
    <small class="text-muted">Kelola pemasukan & pengeluaran</small>
</div>
</a>
</div>

<!-- LAPORAN KEUANGAN -->
<div class="col-md-6 col-lg-3">
<a href="laporan.php" class="text-decoration-none">
<div class="card card-menu text-center p-4 glass">
    <div class="icon text-success mb-2">
        <i class="bi bi-bar-chart-line"></i>
    </div>
    <h6 class="fw-bold text-dark">Laporan Keuangan</h6>
    <small class="text-muted">Rekap & cetak laporan</small>
</div>
</a>
</div>

<!-- LAPORAN JAMAAH -->
<div class="col-md-6 col-lg-3">
<a href="laporan_jamaah.php" class="text-decoration-none">
<div class="card card-menu text-center p-4 glass">
    <div class="icon text-danger mb-2">
        <i class="bi bi-megaphone"></i>
    </div>
    <h6 class="fw-bold text-dark">Laporan Jamaah</h6>
    <small class="text-muted">Kelola laporan warga</small>
</div>
</a>
</div>

<!-- DISPLAY -->
<div class="col-md-6 col-lg-3">
<a href="../display.php" target="_blank" class="text-decoration-none">
<div class="card card-menu text-center p-4 glass">
    <div class="icon text-warning mb-2">
        <i class="bi bi-tv"></i>
    </div>
    <h6 class="fw-bold text-dark">Display Masjid</h6>
    <small class="text-muted">Tampilan layar TV</small>
</div>
</a>
</div>

</div>

</div>

<footer class="text-center py-4 footer">
© <?= date('Y'); ?> Masjid Al-Ikhlas • Sistem Petugas Modern
</footer>

</body>
</html>