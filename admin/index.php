<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("location:../login.php");
    exit;
}

// QUERY STATISTIK RINGKASAN
$qDana = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total FROM keuangan WHERE jenis='infaq'");
$dDana = mysqli_fetch_assoc($qDana);
$totalInfaq = $dDana['total'] ?? 0;

$qUser = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users");
$dUser = mysqli_fetch_assoc($qUser);
$totalUser = $dUser['total'] ?? 0;

$qInfo = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM info_masjid");
$dInfo = mysqli_fetch_assoc($qInfo);
$totalInfo = $dInfo['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Masjid Al-Ikhlas</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans & Amiri -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Amiri:wght@700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f6f9;
            color: #212529;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background: linear-gradient(135deg, #198754, #20c997);
        }

        .hero-banner {
            background: linear-gradient(135deg, #198754, #20c997);
            color: #ffffff;
            border-radius: 28px;
            padding: 40px 35px;
            box-shadow: 0 15px 35px rgba(25, 135, 84, 0.25);
            position: relative;
            overflow: hidden;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50%;
            top: -60px;
            right: -60px;
            pointer-events: none;
        }

        .bismillah-text {
            font-family: 'Amiri', serif;
            font-size: 2.1rem;
            line-height: 1.8;
            color: #ffffff;
            padding-top: 6px;
            margin-bottom: 12px;
            opacity: 0.98;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            display: block;
        }

        .stat-card {
            border: none;
            border-radius: 22px;
            background: #ffffff;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .card-menu {
            border: none;
            border-radius: 24px;
            background: #ffffff;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            text-decoration: none !important;
            color: inherit;
        }

        .card-menu:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.12);
        }

        .menu-icon-box {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 18px;
            transition: transform 0.3s ease;
        }

        .card-menu:hover .menu-icon-box {
            transform: scale(1.1) rotate(5deg);
        }

        .footer {
            font-size: 14px;
            color: #6c757d;
            margin-top: auto;
        }

        .clock-badge {
            background: rgba(0, 0, 0, 0.22);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 30px;
            padding: 6px 16px;
            font-weight: 700;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                🕌 Masjid Al-Ikhlas | Admin Panel
            </span>

            <div class="text-white d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <!-- JAM DIGITAL REAL-TIME -->
                <div class="clock-badge d-flex align-items-center gap-2 me-2">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span id="adminClock" class="text-warning">--:--:-- WIB</span>
                </div>

                <div class="d-none d-md-flex align-items-center gap-2 me-2">
                    <i class="bi bi-person-circle fs-5"></i>
                    <span class="fw-semibold"><?= htmlspecialchars($_SESSION['nama']); ?></span>
                    <span class="badge bg-light text-success rounded-pill px-3 py-1.5 fw-bold">Admin</span>
                </div>

                <a href="../logout.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="container py-4">

        <!-- HERO BANNER -->
        <div class="hero-banner mb-4">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-3 mb-lg-0">
                    <div class="bismillah-text">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
                    <div class="mb-3">
                        <span class="badge bg-white text-success rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size: 0.85rem;">
                            <i class="bi bi-shield-check me-1"></i> Panel Kontrol Utama Administrator
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1">Selamat Datang, <?= htmlspecialchars($_SESSION['nama']); ?>! 👋</h3>
                    <p class="mb-0 opacity-90">
                        Kelola seluruh data keuangan, pengguna, informasi pengumuman, dan display TV digital Masjid Al-Ikhlas secara terpusat.
                    </p>
                </div>
                <div class="col-lg-5 text-lg-end mt-2 mt-lg-0">
                    <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                        <a href="../index.php" target="_blank" class="btn btn-light text-success fw-bold rounded-pill px-3 py-2 shadow-sm text-nowrap">
                            <i class="bi bi-globe me-1"></i> Beranda Utama
                        </a>
                        <a href="../display.php" target="_blank" class="btn btn-warning text-dark fw-bold rounded-pill px-3 py-2 shadow-sm text-nowrap">
                            <i class="bi bi-tv me-1"></i> Display TV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATISTIK RINGKASAN -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">TOTAL SALDO INFAQ</div>
                        <div class="fs-4 fw-extrabold text-success">
                            Rp <?= number_format($totalInfaq, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">TOTAL PENGGUNA</div>
                        <div class="fs-4 fw-extrabold text-primary">
                            <?= number_format($totalUser, 0, ',', '.') ?> Account
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-broadcast"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">INFO & RUNNING TEXT</div>
                        <div class="fs-4 fw-extrabold text-warning">
                            <?= number_format($totalInfo, 0, ',', '.') ?> Data Aktif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MENU NAVIGASI ADMIN -->
        <h5 class="fw-bold mb-3 text-dark">
            <i class="bi bi-grid-fill text-success me-2"></i> Menu Pengelolaan Sistem
        </h5>

        <div class="row g-4">

            <!-- DANA -->
            <div class="col-md-6 col-lg-3">
                <a href="dana.php" class="card-menu h-100 text-center p-4 d-block">
                    <div class="menu-icon-box bg-success-subtle text-success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Data Dana Masjid</h5>
                    <p class="text-muted small mb-0">
                        Kelola pencatatan pemasukan dan pengeluaran dana infaq masjid.
                    </p>
                </a>
            </div>

            <!-- USER -->
            <div class="col-md-6 col-lg-3">
                <a href="user.php" class="card-menu h-100 text-center p-4 d-block">
                    <div class="menu-icon-box bg-primary-subtle text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Manajemen User</h5>
                    <p class="text-muted small mb-0">
                        Kelola data pengguna, hak akses Admin, Petugas, dan Jamaah.
                    </p>
                </a>
            </div>

            <!-- INFO MASJID -->
            <div class="col-md-6 col-lg-3">
                <a href="info.php" class="card-menu h-100 text-center p-4 d-block">
                    <div class="menu-icon-box bg-info-subtle text-info">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Info & Running Text</h5>
                    <p class="text-muted small mb-0">
                        Kelola pengumuman, profil, dan running text beranda & TV.
                    </p>
                </a>
            </div>

            <!-- DISPLAY & JADWAL SHOLAT -->
            <div class="col-md-6 col-lg-3">
                <a href="display.php" class="card-menu h-100 text-center p-4 d-block">
                    <div class="menu-icon-box bg-warning-subtle text-warning">
                        <i class="bi bi-tv"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Display & Jadwal</h5>
                    <p class="text-muted small mb-0">
                        Pengaturan jam sholat dan informasi tampilan layar TV.
                    </p>
                </a>
            </div>

        </div>

    </div>

    <!-- FOOTER -->
    <footer class="text-center py-4 footer">
        <div class="container">
            <hr class="mb-3">
            <p class="mb-0">© <?= date('Y'); ?> <strong>Masjid Al-Ikhlas</strong> – Admin Panel Digital</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- REALTIME CLOCK SCRIPT -->
    <script>
        function updateAdminClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockElem = document.getElementById("adminClock");
            if(clockElem) {
                clockElem.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updateAdminClock, 1000);
        updateAdminClock();
    </script>
</body>
</html>
