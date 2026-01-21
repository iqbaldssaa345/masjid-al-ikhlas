<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("location:../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | Masjid Al-Ikhlas</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700;800&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:#f4f6f9;
}

.navbar{
    background:linear-gradient(135deg,#198754,#20c997);
}

.card-menu{
    border:none;
    border-radius:20px;
    transition:.3s;
}

.card-menu:hover{
    transform:translateY(-10px);
    box-shadow:0 20px 40px rgba(0,0,0,.15);
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
        🕌 Masjid Al-Ikhlas | Admin
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

<!-- CONTENT -->
<div class="container py-5">

<h4 class="fw-bold mb-1">Dashboard Admin</h4>
<p class="text-muted mb-4">
    Kelola seluruh sistem Masjid Al-Ikhlas
</p>

<div class="row g-4">

    <!-- DANA -->
    <div class="col-md-6 col-lg-4">
        <a href="dana.php" class="text-decoration-none">
            <div class="card card-menu h-100 text-center p-4">
                <div class="text-success icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <h5 class="fw-bold text-dark">Data Dana Masjid</h5>
                <p class="text-muted">
                    Kelola pemasukan dan pengeluaran dana infaq.
                </p>
            </div>
        </a>
    </div>

    <!-- USER -->
    <div class="col-md-6 col-lg-4">
        <a href="user.php" class="text-decoration-none">
            <div class="card card-menu h-100 text-center p-4">
                <div class="text-primary icon">
                    <i class="bi bi-people"></i>
                </div>
                <h5 class="fw-bold text-dark">Manajemen User</h5>
                <p class="text-muted">
                    Kelola admin, petugas, dan jamaah.
                </p>
            </div>
        </a>
    </div>

    <!-- INFO MASJID -->
    <div class="col-md-6 col-lg-4">
        <a href="info.php" class="text-decoration-none">
            <div class="card card-menu h-100 text-center p-4">
                <div class="text-info icon">
                    <i class="bi bi-megaphone"></i>
                </div>
                <h5 class="fw-bold text-dark">Info Masjid</h5>
                <p class="text-muted">
                    Kelola profil masjid, agenda, dan pengumuman.
                </p>
            </div>
        </a>
    </div>

    <!-- DISPLAY -->
    <div class="col-md-6 col-lg-4">
        <a href="../display.php" class="text-decoration-none" target="_blank">
            <div class="card card-menu h-100 text-center p-4">
                <div class="text-warning icon">
                    <i class="bi bi-tv"></i>
                </div>
                <h5 class="fw-bold text-dark">Display Masjid</h5>
                <p class="text-muted">
                    Lihat tampilan layar TV masjid secara realtime.
                </p>
            </div>
        </a>
    </div>

</div>

</div>

<footer class="text-center py-3 footer">
    © <?= date('Y'); ?> Masjid Al-Ikhlas – Admin Panel
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
