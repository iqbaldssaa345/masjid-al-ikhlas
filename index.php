<?php include "config/koneksi.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masjid Al-Ikhlas - Sistem Informasi Digital</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts: Plus Jakarta Sans & Amiri -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #0d6efd;
            --success-green: #198754;
            --warning-gold: #ffc107;
            --emerald-grad: linear-gradient(135deg, #198754, #20c997);
            --gold-grad: linear-gradient(135deg, #ffc107, #ff9800);
            --blue-grad: linear-gradient(135deg, #0d6efd, #0b5ed7);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: 
                linear-gradient(135deg, rgba(0, 0, 0, 0.75), rgba(10, 25, 18, 0.82)),
                url('https://images.unsplash.com/photo-1609358905581-e53c6a1db1dd');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px 15px;
            overflow-x: hidden;
        }

        /* Top Bar Clock & Greeting */
        .top-info-bar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 10px 24px;
            display: inline-flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: fadeInDown 0.8s ease-out;
        }

        .pulse-dot {
            width: 9px;
            height: 9px;
            background-color: #20c997;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 10px #20c997;
            animation: pulse 1.8s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(32, 201, 151, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(32, 201, 151, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(32, 201, 151, 0); }
        }

        /* Main Glass Card Container */
        .glass-container {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 36px;
            padding: 55px 45px;
            box-shadow: 0 35px 70px rgba(0, 0, 0, 0.55),
                        inset 0 1px 0 rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.18);
            position: relative;
            max-width: 1140px;
            width: 100%;
            animation: fadeInUp 0.9s ease-out;
        }

        /* Arabic Subtitle */
        .arabic-title {
            font-family: 'Amiri', serif;
            font-size: 2.5rem;
            color: #ffc107;
            text-shadow: 0 2px 10px rgba(255, 193, 7, 0.35);
            margin-top: 10px;
            margin-bottom: 14px;
            line-height: 1.8;
            display: block;
        }

        /* Custom Badge */
        .badge-custom {
            background: var(--emerald-grad);
            color: #ffffff;
            padding: 10px 24px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 6px 20px rgba(25, 135, 84, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Feature Cards */
        .card-feature {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 28px;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            overflow: hidden;
            color: #212529;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .card-feature::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            transition: height 0.3s ease;
        }

        .card-login::before { background: var(--primary-blue); }
        .card-display::before { background: var(--success-green); }
        .card-infaq::before { background: var(--warning-gold); }

        .card-feature:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 28px 55px rgba(0, 0, 0, 0.4);
        }

        .card-login:hover { border-color: rgba(13, 110, 253, 0.5); }
        .card-display:hover { border-color: rgba(25, 135, 84, 0.5); }
        .card-infaq:hover { border-color: rgba(255, 193, 7, 0.5); }

        /* Icon Box */
        .icon-box {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            margin: 0 auto 22px;
            color: #ffffff;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        .card-feature:hover .icon-box {
            transform: scale(1.1) rotate(4deg);
        }

        .icon-login {
            background: var(--blue-grad);
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.4);
        }

        .icon-display {
            background: var(--emerald-grad);
            box-shadow: 0 10px 25px rgba(25, 135, 84, 0.4);
        }

        .icon-infaq {
            background: var(--gold-grad);
            color: #212529;
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.4);
        }

        /* Buttons */
        .btn-custom {
            border-radius: 30px;
            padding: 12px 28px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary.btn-custom {
            background: var(--blue-grad);
            border: none;
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.35);
        }

        .btn-primary.btn-custom:hover {
            box-shadow: 0 12px 28px rgba(13, 110, 253, 0.55);
            transform: translateY(-2px);
        }

        .btn-success.btn-custom {
            background: var(--emerald-grad);
            border: none;
            box-shadow: 0 8px 20px rgba(25, 135, 84, 0.35);
        }

        .btn-success.btn-custom:hover {
            box-shadow: 0 12px 28px rgba(25, 135, 84, 0.55);
            transform: translateY(-2px);
        }

        .btn-warning.btn-custom {
            background: var(--gold-grad);
            border: none;
            color: #212529 !important;
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.35);
        }

        .btn-warning.btn-custom:hover {
            box-shadow: 0 12px 28px rgba(255, 193, 7, 0.55);
            transform: translateY(-2px);
        }

        /* Ticker Bar */
        .ticker-wrapper {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 12px 20px;
            margin-top: 35px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Footer */
        footer {
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.75);
            margin-top: 30px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .glass-container {
                padding: 35px 22px;
                border-radius: 26px;
            }
            .arabic-title {
                font-size: 1.8rem;
            }
            .top-info-bar {
                flex-direction: column;
                gap: 6px;
                border-radius: 20px;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <!-- Top Info Bar (Live Clock & Greeting) -->
    <div class="top-info-bar">
        <div class="d-flex align-items-center gap-2">
            <span class="pulse-dot"></span>
            <span id="greetingText" class="fw-semibold">Selamat Datang</span>
        </div>
        <div class="text-white-50 d-none d-md-inline">•</div>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-calendar3 text-warning"></i>
            <span id="liveDate" class="small"></span>
        </div>
        <div class="text-white-50 d-none d-md-inline">•</div>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-info"></i>
            <span id="liveClock" class="fw-bold tracking-wide text-warning"></span>
        </div>
    </div>

    <!-- Main Glass Container -->
    <div class="glass-container text-center">

        <!-- Badge -->
        <div class="mb-2">
            <span class="badge badge-custom">
                <i class="bi bi-stars"></i> Sistem Informasi Masjid Digital
            </span>
        </div>

        <!-- Arabic Subtitle -->
        <div class="arabic-title">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>

        <h1 class="fw-extrabold display-4 mb-3 tracking-tight">
            MASJID AL-IKHLAS
        </h1>

        <p class="lead mb-5 mx-auto text-white-50" style="max-width: 680px; font-size: 1.1rem; font-weight: 400;">
            Transparansi Infaq &bull; Display TV Masjid &bull; Pelayanan Jamaah Terpadu
        </p>

        <!-- Feature Cards Grid -->
        <div class="row g-4 justify-content-center">

            <!-- LOGIN -->
            <div class="col-md-4">
                <div class="card card-feature card-login h-100">
                    <div class="card-body p-4 p-xl-5 d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box icon-login">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Login Sistem</h4>
                            <p class="text-secondary small mb-4">
                                Akses khusus untuk Admin, Petugas, dan Jamaah terdaftar.
                            </p>
                        </div>
                        <a href="login.php" class="btn btn-primary btn-custom w-100">
                            Masuk Sistem <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- DISPLAY -->
            <div class="col-md-4">
                <div class="card card-feature card-display h-100">
                    <div class="card-body p-4 p-xl-5 d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box icon-display">
                                <i class="bi bi-tv-fill"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Display Masjid</h4>
                            <p class="text-secondary small mb-4">
                                Informasi jadwal sholat, rekapan infaq, dan pengumuman realtime.
                            </p>
                        </div>
                        <a href="display.php" class="btn btn-success btn-custom w-100">
                            Tampilkan Display <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- INFAQ -->
            <div class="col-md-4">
                <div class="card card-feature card-infaq h-100">
                    <div class="card-body p-4 p-xl-5 d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box icon-infaq">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Infaq Jamaah</h4>
                            <p class="text-secondary small mb-4">
                                Layanan infaq dan sedekah digital yang transparan dan amanah.
                            </p>
                        </div>
                        <a href="login.php" class="btn btn-warning btn-custom w-100">
                            Infaq Sekarang <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Ticker Announcement Bar (Running Text Dynamic) -->
        <div class="ticker-wrapper mt-5 align-items-center">
            <div class="d-flex align-items-center gap-2 me-2 text-warning fw-bold text-nowrap">
                <i class="bi bi-megaphone-fill"></i>
                <span class="d-none d-sm-inline">INFO:</span>
            </div>
            <div class="w-100 overflow-hidden">
                <marquee scrollamount="6" onmouseover="this.stop();" onmouseout="this.start();">
                <?php
                $qInfoTicker = mysqli_query($koneksi, "SELECT * FROM info_masjid ORDER BY id DESC");
                $hasTicker = false;
                if($qInfoTicker && mysqli_num_rows($qInfoTicker) > 0){
                    while($rowTicker = mysqli_fetch_assoc($qInfoTicker)){
                        echo "✨ <strong class='text-warning'>" . htmlspecialchars($rowTicker['judul']) . "</strong>: " . htmlspecialchars($rowTicker['isi']) . " &nbsp;&bull;&nbsp; ";
                        $hasTicker = true;
                    }
                }
                if(!$hasTicker){
                    echo "Mari bersama memakmurkan Masjid Al-Ikhlas &bull; Transparansi & Kemudahan Layanan Jamaah";
                }
                ?>
                </marquee>
            </div>
        </div>

        <!-- Footer -->
        <footer class="pt-3">
            <hr style="border-color: rgba(255, 255, 255, 0.15);">
            <p class="mb-0 small">
                © <?= date('Y'); ?> <strong>Masjid Al-Ikhlas</strong>. All rights reserved.<br>
                <span class="text-white-50">Sistem Informasi & Display Digital Masjid</span>
            </p>
        </footer>

    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Real-time Clock & Greeting Script -->
    <script>
        function updateClockAndGreeting() {
            const now = new Date();
            
            // Format Jam (HH:mm:ss WIB)
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('liveClock').textContent = `${hours}:${minutes}:${seconds} WIB`;

            // Format Tanggal
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('liveDate').textContent = now.toLocaleDateString('id-ID', options);

            // Dynamic Islamic Greeting based on hour
            const hourNum = now.getHours();
            let greeting = 'Assalamu\'alaikum';
            if (hourNum >= 3 && hourNum < 11) {
                greeting = 'Assalamu\'alaikum, Selamat Pagi';
            } else if (hourNum >= 11 && hourNum < 15) {
                greeting = 'Assalamu\'alaikum, Selamat Siang';
            } else if (hourNum >= 15 && hourNum < 18) {
                greeting = 'Assalamu\'alaikum, Selamat Sore';
            } else {
                greeting = 'Assalamu\'alaikum, Selamat Malam';
            }
            document.getElementById('greetingText').textContent = greeting;
        }

        setInterval(updateClockAndGreeting, 1000);
        updateClockAndGreeting();
    </script>
</body>
</html>
