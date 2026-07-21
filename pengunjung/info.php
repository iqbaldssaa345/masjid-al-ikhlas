<?php
session_start();
include "../config/koneksi.php";

$data = mysqli_query($koneksi,"SELECT * FROM info_masjid ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informasi &amp; Pengumuman | Jamaah Masjid Al-Ikhlas</title>

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

        .bismillah-header {
            font-family: 'Amiri', serif;
            font-size: 1.85rem;
            line-height: 1.8;
            color: #198754;
            padding-top: 4px;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(25, 135, 84, 0.1);
        }

        .card-custom {
            border: none;
            border-radius: 24px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.1);
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

        .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
            font-size: 24px;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm mb-4 py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                📢 Informasi Jamaah | Masjid Al-Ikhlas
            </span>
            <div class="text-white d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <div class="clock-badge d-flex align-items-center gap-2 me-2">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span id="jamaahClock" class="text-warning">--:--:-- WIB</span>
                </div>
                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3 text-nowrap">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">

        <!-- HEADER TITLE WITH BISMILLAH -->
        <div class="text-center mb-4">
            <div class="bismillah-header">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
            <h4 class="fw-bold text-dark m-0">Agenda &amp; Pengumuman Resmi Masjid</h4>
            <p class="text-muted small">Informasi kegiatan kajian, agenda DKM, dan laporan transparansi jamaah</p>
        </div>

        <div class="row g-4">
            <?php if(mysqli_num_rows($data) > 0){ ?>
                <?php while($d = mysqli_fetch_assoc($data)){ ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card-custom h-100 p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box">
                                <i class="bi bi-megaphone-fill"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">
                                <?= htmlspecialchars($d['judul']); ?>
                            </h5>
                        </div>

                        <p class="text-secondary mb-0 leading-relaxed">
                            <?= nl2br(htmlspecialchars($d['isi'])); ?>
                        </p>
                    </div>
                </div>
                <?php } ?>
            <?php } else { ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center rounded-4 shadow-sm py-4">
                        <i class="bi bi-info-circle fs-3 mb-2 d-block"></i>
                        Belum ada pengumuman masjid yang tersedia saat ini.
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>

    <footer class="text-center py-4 mt-auto text-secondary small">
        © <?= date('Y'); ?> <strong>Masjid Al-Ikhlas</strong> – Media Informasi Jamaah
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- REAL-TIME CLOCK SCRIPT -->
    <script>
        function updateJamaahClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockElem = document.getElementById("jamaahClock");
            if(clockElem) {
                clockElem.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updateJamaahClock, 1000);
        updateJamaahClock();
    </script>
</body>
</html>
