<?php
session_start();
include "../config/koneksi.php";

// REKAPITULASI DANA & RINGKASAN PETUGAS
$total_infaq = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) as total FROM keuangan WHERE jenis='infaq'")
)['total'] ?? 0;

$total_pengeluaran = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) as total FROM keuangan WHERE jenis IN ('dkm', 'pengeluaran')")
)['total'] ?? 0;

$saldo = $total_infaq - $total_pengeluaran;

$total_info = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT COUNT(*) as total FROM info_masjid")
)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petugas Dashboard | Masjid Al-Ikhlas</title>

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

        .clock-badge {
            background: rgba(0, 0, 0, 0.22);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 30px;
            padding: 6px 16px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .hero-banner {
            background: linear-gradient(135deg, #198754, #20c997);
            color: #ffffff;
            border-radius: 24px;
            padding: 35px 30px;
            box-shadow: 0 12px 30px rgba(25, 135, 84, 0.2);
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50%;
            top: -50px;
            right: -50px;
            pointer-events: none;
        }

        .bismillah-text {
            font-family: 'Amiri', serif;
            font-size: 2rem;
            line-height: 1.7;
            color: #ffffff;
            margin-bottom: 8px;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            display: block;
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            padding: 22px 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .section-header {
            margin-top: 15px;
            margin-bottom: 20px;
            padding-top: 10px;
        }

        .card-menu {
            border: none;
            border-radius: 22px;
            background: #ffffff;
            transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
            text-decoration: none !important;
            color: inherit;
            height: 100%;
            display: block;
            position: relative;
            overflow: hidden;
        }

        .card-menu:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .menu-icon-box {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 16px;
            transition: transform 0.3s ease;
        }

        .card-menu:hover .menu-icon-box {
            transform: scale(1.1) rotate(4deg);
        }

        .footer {
            font-size: 14px;
            color: #6c757d;
            margin-top: auto;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                🕌 Masjid Al-Ikhlas | Petugas Panel
            </span>

            <div class="text-white d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <!-- JAM DIGITAL REAL-TIME -->
                <div class="clock-badge d-flex align-items-center gap-2 me-2">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span id="petugasClock" class="text-warning">--:--:-- WIB</span>
                </div>

                <div class="d-none d-md-flex align-items-center gap-2 me-2">
                    <i class="bi bi-person-badge fs-5"></i>
                    <span class="fw-semibold"><?= htmlspecialchars($_SESSION['nama'] ?? 'Petugas'); ?></span>
                    <span class="badge bg-light text-success rounded-pill px-3 py-1.5 fw-bold">Petugas</span>
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
        <div class="hero-banner">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <div class="bismillah-text">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
                    <div class="mb-2">
                        <span class="badge bg-white text-success rounded-pill px-3 py-1.5 fw-bold shadow-sm" style="font-size: 0.85rem;">
                            <i class="bi bi-shield-check me-1"></i> Dashboard Pengelolaan Petugas DKM
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1">Assalamu'alaikum, <?= htmlspecialchars($_SESSION['nama'] ?? 'Petugas'); ?>! 👋</h3>
                    <p class="mb-0 opacity-90">
                        Selamat bertugas di Sistem Manajemen Operasional Masjid Al-Ikhlas. Kelola keuangan infaq, laporan kas, dan informasi pengumuman jamaah secara rapi dan transparan.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-2 mt-lg-0">
                    <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                        <a href="../display.php" target="_blank" class="btn btn-warning text-dark fw-bold rounded-pill px-3 py-2 shadow-sm text-nowrap">
                            <i class="bi bi-tv me-1"></i> Display TV
                        </a>
                        <a href="../index.php" target="_blank" class="btn btn-light text-success fw-bold rounded-pill px-3 py-2 shadow-sm text-nowrap">
                            <i class="bi bi-globe me-1"></i> Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATISTIK RINGKASAN OPERASIONAL -->
        <div class="row g-3 mb-4">

            <!-- TOTAL INFAQ -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted fw-bold d-block mb-1 text-uppercase" style="font-size: 0.75rem;">PEMASUKAN INFAQ</small>
                            <h5 class="fw-bold text-success m-0" style="font-size: 1.25rem;">Rp <?= number_format($total_infaq,0,',','.'); ?></h5>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-arrow-down-left-circle-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PENGELUARAN -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted fw-bold d-block mb-1 text-uppercase" style="font-size: 0.75rem;">PENGELUARAN DKM</small>
                            <h5 class="fw-bold text-danger m-0" style="font-size: 1.25rem;">Rp <?= number_format($total_pengeluaran,0,',','.'); ?></h5>
                        </div>
                        <div class="stat-icon bg-danger-subtle text-danger">
                            <i class="bi bi-arrow-up-right-circle-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SALDO KAS -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted fw-bold d-block mb-1 text-uppercase" style="font-size: 0.75rem;">SALDO KAS MASJID</small>
                            <h5 class="fw-bold text-primary m-0" style="font-size: 1.25rem;">Rp <?= number_format($saldo,0,',','.'); ?></h5>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PENGUMUMAN -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted fw-bold d-block mb-1 text-uppercase" style="font-size: 0.75rem;">INFORMASI AKTIF</small>
                            <h5 class="fw-bold text-info m-0" style="font-size: 1.25rem;"><?= number_format($total_info,0,',','.'); ?> Berita</h5>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="bi bi-megaphone-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- MENU MODUL PETUGAS HEADER -->
        <div class="section-header">
            <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <i class="bi bi-grid-fill text-success fs-5"></i> Menu Utama Petugas
            </h5>
        </div>

        <!-- ROW MENU CARDS -->
        <div class="row g-4 mb-4">

            <!-- DANA MASJID -->
            <div class="col-md-6 col-lg-3">
                <a href="dana.php" class="card-menu p-4 text-center">
                    <div class="card-body p-0">
                        <div class="menu-icon-box bg-success-subtle text-success">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Dana Masjid</h5>
                        <p class="text-muted small m-0">Input &amp; kelola transaksi pemasukan serta pengeluaran operasional</p>
                    </div>
                </a>
            </div>

            <!-- LAPORAN KEUANGAN -->
            <div class="col-md-6 col-lg-3">
                <a href="laporan.php" class="card-menu p-4 text-center">
                    <div class="card-body p-0">
                        <div class="menu-icon-box bg-primary-subtle text-primary">
                            <i class="bi bi-bar-chart-line-fill"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Laporan Keuangan</h5>
                        <p class="text-muted small m-0">Rekapitulasi, filter periode, dan cetak laporan kas masjid</p>
                    </div>
                </a>
            </div>

            <!-- INFORMASI MASJID -->
            <div class="col-md-6 col-lg-3">
                <a href="info.php" class="card-menu p-4 text-center">
                    <div class="card-body p-0">
                        <div class="menu-icon-box bg-info-subtle text-info">
                            <i class="bi bi-megaphone-fill"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Informasi Masjid</h5>
                        <p class="text-muted small m-0">Kelola pengumuman, berita DKM, &amp; informasi running text</p>
                    </div>
                </a>
            </div>

            <!-- DISPLAY MASJID -->
            <div class="col-md-6 col-lg-3">
                <a href="../display.php" target="_blank" class="card-menu p-4 text-center">
                    <div class="card-body p-0">
                        <div class="menu-icon-box bg-warning-subtle text-warning">
                            <i class="bi bi-tv-fill"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Display TV Board</h5>
                        <p class="text-muted small m-0">Buka halaman display layar TV masjid real-time</p>
                    </div>
                </a>
            </div>

        </div>

    </div>

    <!-- FOOTER -->
    <footer class="footer text-center py-4">
        <div class="container">
            <hr class="mb-3">
            <p class="mb-0">© <?= date('Y'); ?> <strong>Masjid Al-Ikhlas</strong> • Petugas Panel Management</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- REAL-TIME CLOCK SCRIPT -->
    <script>
        function updatePetugasClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockElem = document.getElementById('petugasClock');
            if (clockElem) {
                clockElem.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updatePetugasClock, 1000);
        updatePetugasClock();
    </script>
</body>
</html>