<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Petugas | Masjid Al-Ikhlas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:#f4f6f9;
}
.navbar{
    background:linear-gradient(135deg,#0d6efd,#20c997);
}
.card-menu{
    border:none;
    border-radius:20px;
    transition:.3s;
}
.card-menu:hover{
    transform:translateY(-8px);
    box-shadow:0 15px 35px rgba(0,0,0,.15);
}
.icon{
    font-size:42px;
    margin-bottom:10px;
}
.footer{
    font-size:14px;
    color:#777;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark shadow">
<div class="container-fluid">
    <span class="navbar-brand fw-bold">
        🕌 Masjid Al-Ikhlas | Petugas
    </span>
    <div>
        <span class="text-white me-3">
            👤 <?= htmlspecialchars($_SESSION['nama']) ?>
        </span>
        <a href="../logout.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>
</nav>

<!-- CONTENT -->
<div class="container py-5">

<h4 class="fw-bold mb-4">Dashboard Petugas</h4>

<div class="row g-4">

    <!-- DANA -->
    <div class="col-md-6 col-lg-4">
        <a href="dana.php" class="text-decoration-none">
            <div class="card card-menu h-100 text-center p-4">
                <div class="text-primary icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <h5 class="fw-bold text-dark">Input Dana Masjid</h5>
                <p class="text-muted">
                    Catat pemasukan dan pengeluaran dana masjid.
                </p>
            </div>
        </a>
    </div>

    <!-- LAPORAN -->
    <div class="col-md-6 col-lg-4">
        <a href="laporan.php" class="text-decoration-none">
            <div class="card card-menu h-100 text-center p-4">
                <div class="text-success icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h5 class="fw-bold text-dark">Laporan Keuangan</h5>
                <p class="text-muted">
                    Lihat dan rekap laporan dana masjid.
                </p>
            </div>
        </a>
    </div>

    <!-- DISPLAY -->
    <div class="col-md-6 col-lg-4">
        <a href="../display.php" target="_blank" class="text-decoration-none">
            <div class="card card-menu h-100 text-center p-4">
                <div class="text-warning icon">
                    <i class="bi bi-tv"></i>
                </div>
                <h5 class="fw-bold text-dark">Display Masjid</h5>
                <p class="text-muted">
                    Lihat tampilan layar TV masjid.
                </p>
            </div>
        </a>
    </div>

</div>

</div>

<footer class="text-center py-3 footer">
    © <?= date('Y'); ?> Masjid Al-Ikhlas – Panel Petugas
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
