<?php
session_start();
include "config/koneksi.php";

// AMBIL DATA JADWAL SHOLAT DARI SQL DATABASE
$qSholat = @mysqli_query($koneksi, "SELECT * FROM jadwal_sholat ORDER BY id DESC LIMIT 1");
$dSholat = ($qSholat) ? mysqli_fetch_assoc($qSholat) : null;
$kotaJadwal = $dSholat['kota'] ?? 'Kab. Bogor (Cibinong)';

$iqSubuh   = (int)($dSholat['iqomah_subuh'] ?? 7);
$iqDzuhur  = (int)($dSholat['iqomah_dzuhur'] ?? 4);
$iqAshar   = (int)($dSholat['iqomah_ashar'] ?? 4);
$iqMaghrib = (int)($dSholat['iqomah_maghrib'] ?? 6);
$iqIsya    = (int)($dSholat['iqomah_isya'] ?? 5);

$imsakVal   = $dSholat['imsak']   ?? '04:36';
$subuhVal   = $dSholat['subuh']   ?? '04:46';
$syuruqVal  = $dSholat['syuruq']  ?? '06:02';
$dzuhurVal  = $dSholat['dzuhur']  ?? '12:03';
$asharVal   = $dSholat['ashar']   ?? '15:24';
$maghribVal = $dSholat['maghrib'] ?? '17:56';
$isyaVal    = $dSholat['isya']    ?? '19:09';

// AMBIL HINGGA 5 DATA INFORMASI MASJID
$qInfo = mysqli_query($koneksi, "SELECT * FROM info_masjid ORDER BY id DESC LIMIT 5");
$infoList = [];
if ($qInfo) {
    while ($row = mysqli_fetch_assoc($qInfo)) {
        $infoList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Display Masjid Al-Ikhlas - Digital TV Board</title>

<!-- Bootstrap 5.3 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<!-- Google Fonts: Poppins, Orbitron & Scheherazade New -->
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800;900&family=Poppins:wght@400;500;600;700;800&family=Scheherazade+New:wght@700&display=swap" rel="stylesheet">

<meta http-equiv="refresh" content="180">

<style>
* {
    box-sizing: border-box;
}

html, body {
    height: 100vh;
    width: 100vw;
    margin: 0;
    padding: 0;
    overflow: hidden;
    font-family: 'Poppins', sans-serif;
    background: #050b14;
    color: #ffffff;
}

body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: radial-gradient(circle at center, #0f1b29 0%, #050b14 100%);
}

/* HEADER BAR TV DIGITAL STYLE */
.header {
    height: 56px;
    background: linear-gradient(90deg, #0f2027, #203a43, #2c5364);
    padding: 6px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2.5px solid rgba(255, 235, 59, 0.5);
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.7);
    flex-shrink: 0;
}

.header-title {
    font-size: 1.25rem;
    font-weight: 800;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
    letter-spacing: 0.5px;
}

.header-subtitle {
    font-size: 0.78rem;
    opacity: 0.85;
}

.jam-box {
    background: rgba(0, 0, 0, 0.55);
    border: 1.5px solid rgba(255, 235, 59, 0.4);
    padding: 2px 14px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(255, 235, 59, 0.2);
}

.jam {
    font-family: 'Orbitron', 'Poppins', monospace;
    font-size: 30px;
    font-weight: 800;
    color: #ffeb3b;
    font-variant-numeric: tabular-nums;
    text-shadow: 0 0 16px rgba(255, 235, 59, 0.8);
    line-height: 1.1;
    letter-spacing: 1px;
}

/* MAIN DISPLAY CONTAINER - FIT 100VH */
.display-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    max-width: 1400px;
    width: 100%;
    margin: 0 auto;
    padding: 6px 16px;
    gap: 6px;
    overflow: hidden;
}

/* HERO BANNER BERKELAS & LEBIH LUAS GRAND STYLE */
.hero {
    background: linear-gradient(rgba(15, 32, 39, 0.45), rgba(32, 58, 67, 0.45)),
    url('https://images.unsplash.com/photo-1564769625905-50e93615e769'),
    linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    background-size: cover;
    background-position: center 35%;
    background-repeat: no-repeat;
    text-align: center;
    padding: 18px 24px;
    min-height: 135px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.65);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

.arab {
    font-family: 'Scheherazade New', serif;
    font-size: 42px;
    color: #ffffff;
    text-shadow: 0 4px 15px rgba(0, 0, 0, 0.95), 0 0 12px rgba(0, 0, 0, 0.9);
    line-height: 1.35;
    margin: 0;
    display: block;
}

.hero-sub {
    opacity: 0.95;
    font-size: 0.95rem;
    font-weight: 500;
    margin: 0;
    display: block;
    color: #ffffff;
    letter-spacing: 0.5px;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.95);
}

.btn-beranda {
    position: absolute;
    top: 12px;
    left: 16px;
    padding: 4px 14px;
    font-size: 0.8rem;
    border-radius: 30px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    z-index: 5;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(6px);
}

/* COUNTDOWN CARD JEDA IQOMAH RAWATIB */
.countdown-box {
    background: rgba(15, 25, 35, 0.88);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 2px solid rgba(255, 235, 59, 0.5);
    border-radius: 14px;
    padding: 6px 16px;
    margin: 0 auto;
    width: 100%;
    max-width: 920px;
    text-align: center;
    box-shadow: 0 8px 30px rgba(0,0,0,0.6);
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
    transition: all 0.5s ease;
}

.countdown-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #1565c0, #2e7d32, #ffeb3b, #ef6c00);
    transition: all 0.5s ease;
}

.countdown-box.mode-iqomah {
    border-color: rgba(255, 71, 87, 0.85);
    box-shadow: 0 0 35px rgba(255, 71, 87, 0.45);
}

.countdown-box.mode-iqomah::before {
    background: linear-gradient(90deg, #ff4757, #ffbe76, #ff4757);
}

.countdown-box.mode-sholat {
    border-color: rgba(0, 230, 118, 0.85);
    box-shadow: 0 0 40px rgba(0, 230, 118, 0.45);
    background: rgba(8, 32, 22, 0.92);
    animation: sholatPulse 3s infinite ease-in-out;
}

.countdown-box.mode-sholat::before {
    background: linear-gradient(90deg, #00e676, #1de9b6, #00e676);
}

@keyframes sholatPulse {
    0% { box-shadow: 0 0 25px rgba(0, 230, 118, 0.3); }
    50% { box-shadow: 0 0 45px rgba(0, 230, 118, 0.65); }
    100% { box-shadow: 0 0 25px rgba(0, 230, 118, 0.3); }
}

.next-prayer-heading {
    font-size: 0.98rem;
    font-weight: 700;
    margin: 2px 0 1px 0;
}

.countdown-timer {
    font-family: 'Orbitron', 'Poppins', monospace;
    font-size: 34px;
    font-weight: 800;
    color: #ffeb3b;
    text-shadow: 0 0 18px rgba(255,235,59,0.7);
    line-height: 1.05;
    margin: 1px 0;
    font-variant-numeric: tabular-nums;
    letter-spacing: 1px;
}

.timer-iqomah {
    color: #ff4757 !important;
    text-shadow: 0 0 25px rgba(255,71,87,0.9) !important;
}

.timer-sholat {
    color: #00e676 !important;
    text-shadow: 0 0 25px rgba(0, 230, 118, 0.9) !important;
}

.iqomah-info-note {
    font-size: 0.74rem;
    color: rgba(255, 255, 255, 0.75);
    margin-top: 1px;
}

/* PANEL SHOLAT GRID DISPLAY TV WARNA PRESISI & MEWAH */
.panel-sholat-wrapper {
    width: 100%;
    flex-shrink: 0;
}

.panel-sholat {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    text-align: center;
}

.panel {
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.35s ease;
    box-shadow: 0 6px 18px rgba(0,0,0,0.5);
    border: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    flex-direction: column;
}

.panel-header {
    padding: 4px 4px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: rgba(0, 0, 0, 0.25);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.panel-body {
    padding: 5px 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.panel span {
    font-family: 'Orbitron', 'Poppins', monospace;
    display: block;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 0.5px;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
}

.panel:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.7);
}

/* WARNA PRESERVED EXACT MATCHA */
.imsak   { background:#6a1b9a; color: #ffffff; }
.subuh   { background:#1565c0; color: #ffffff; }
.syuruq  { background:#00838f; color: #ffffff; }
.dzuhur  { background:#2e7d32; color: #ffffff; }
.ashar   { background:#ef6c00; color: #ffffff; }
.maghrib { background:#c62828; color: #ffffff; }
.isya    { background:#37474f; color: #ffffff; }

.panel.active-prayer {
    outline: 3px solid #ffeb3b !important;
    outline-offset: -3px;
    box-shadow: 0 0 25px rgba(255,235,59,0.9);
    transform: scale(1.03);
    z-index: 2;
}

/* BOTTOM ROW: SALDO & AUTO-SLIDING 5 INFORMASI DISPLAY */
.bottom-grid {
    display: grid;
    grid-template-columns: 310px 1fr;
    gap: 10px;
    align-items: stretch;
    flex: 1;
    min-height: 0;
}

.saldo-box {
    background: #ffffff;
    color: #1b5e20;
    text-align: center;
    padding: 8px 16px;
    font-weight: 800;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 2px;
    height: 100%;
}

.saldo-label {
    font-size: 0.86rem;
    font-weight: 700;
    color: #2e7d32;
    display: flex;
    align-items: center;
    gap: 6px;
}

.saldo-amount {
    font-family: 'Orbitron', 'Poppins', monospace;
    font-size: 1.65rem;
    font-weight: 800;
    color: #1b5e20;
    line-height: 1.1;
    letter-spacing: 0.5px;
}

/* CAROUSEL CONTAINER UNTUK 5 INFORMASI MASJID */
.info-section {
    position: relative;
    height: 100%;
    width: 100%;
    overflow: hidden;
}

.info-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.6s ease-in-out, visibility 0.6s ease-in-out;
}

.info-slide.active {
    opacity: 1;
    visibility: visible;
    position: relative;
}

.card {
    border-radius: 12px;
    background: rgba(20, 30, 40, 0.88);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #ffffff;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    height: 100%;
}

.card-body {
    padding: 8px 14px;
}

.toa-icon-badge {
    background: rgba(255, 235, 59, 0.2);
    border: 1px solid rgba(255, 235, 59, 0.4);
    color: #ffeb3b;
    border-radius: 50%;
    width: 26px;
    height: 26px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    box-shadow: 0 0 10px rgba(255, 235, 59, 0.3);
    flex-shrink: 0;
}

.card-title {
    color: #ffeb3b;
    font-weight: 700;
    font-size: 0.88rem;
    margin-bottom: 2px !important;
}

.card-text {
    font-size: 0.78rem;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* SLIDE DOT INDICATORS */
.slide-indicators {
    position: absolute;
    bottom: 4px;
    right: 12px;
    display: flex;
    gap: 6px;
    z-index: 10;
}

.slide-indicators .dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    cursor: pointer;
    transition: all 0.3s ease;
}

.slide-indicators .dot.active {
    background: #ffeb3b;
    width: 18px;
    border-radius: 10px;
}

/* MARQUEE FOOTER */
.marquee {
    height: 38px;
    background: #0d6efd;
    padding: 0 18px;
    font-size: 15px;
    font-weight: 600;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 -6px 20px rgba(0, 0, 0, 0.5);
    flex-shrink: 0;
}

.marquee-badge {
    background: rgba(255, 255, 255, 0.22);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.35);
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 800;
    padding: 3px 12px;
    border-radius: 30px;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

marquee {
    font-size: 0.92rem;
    font-weight: 600;
}
</style>
</head>

<body>

<!-- HEADER BAR -->
<div class="header">
    <div>
        <h3 class="header-title">🕌 MASJID AL-IKHLAS</h3>
        <span class="header-subtitle">Sistem Display &amp; Informasi Digital Masjid</span>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="jam-box">
            <div class="jam" id="jam">--:--:--</div>
        </div>

        <?php if(isset($_SESSION['role']) && $_SESSION['role']=="admin"){ ?>
            <a href="admin/" class="btn btn-warning btn-sm fw-bold px-3 rounded-pill shadow-sm">
                ⚙ ADMIN
            </a>
        <?php } ?>
    </div>
</div>

<!-- MAIN DISPLAY CONTAINER - FULLSCREEN FIT 100VH -->
<div class="display-container">

    <!-- HERO BANNER BERKELAS & LEBIH LUAS -->
    <div class="hero">
        <a href="index.php" class="btn btn-outline-light btn-beranda">
            ⬅ Beranda
        </a>
        <div class="arab">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
        <div class="hero-sub">
            Dengan nama Allah Yang Maha Pengasih, Maha Penyayang
        </div>
    </div>

    <!-- COUNTDOWN IQOMAH & MENUJU SHOLAT -->
    <div class="countdown-box" id="countdownBox">
        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill" id="countdownBadge">
            ⏰ SHOLAT BERIKUTNYA
        </span>
        <h4 class="next-prayer-heading">
            <span id="nextPrayerLabel">Menuju Sholat</span> <strong id="nextPrayerName" class="text-warning">...</strong>
            <small id="nextPrayerTimeText" class="opacity-75 fs-6 ms-1"></small>
        </h4>
        <div class="countdown-timer" id="countdownTimer">00:00:00</div>
        <div class="iqomah-info-note" id="iqomahInfoNote">
            📍 <strong><?= htmlspecialchars($kotaJadwal) ?></strong> &bull; Jeda Iqomah: Subuh <?= $iqSubuh ?>m, Dzuhur <?= $iqDzuhur ?>m, Ashar <?= $iqAshar ?>m, Maghrib <?= $iqMaghrib ?>m, Isya <?= $iqIsya ?>m (Jumat: 0m)
        </div>
    </div>

    <!-- PANEL JADWAL SHOLAT DIGITAL TV CARDS -->
    <div class="panel-sholat-wrapper">
        <div class="panel-sholat">
            <div class="panel imsak" id="panel-imsak">
                <div class="panel-header"><i class="bi bi-moon-stars me-1"></i> Imsak</div>
                <div class="panel-body"><span><?= htmlspecialchars($imsakVal) ?></span></div>
            </div>
            <div class="panel subuh" id="panel-subuh">
                <div class="panel-header"><i class="bi bi-sunrise me-1"></i> Subuh</div>
                <div class="panel-body"><span><?= htmlspecialchars($subuhVal) ?></span></div>
            </div>
            <div class="panel syuruq" id="panel-syuruq">
                <div class="panel-header"><i class="bi bi-sun me-1"></i> Syuruq</div>
                <div class="panel-body"><span><?= htmlspecialchars($syuruqVal) ?></span></div>
            </div>
            <div class="panel dzuhur" id="panel-dzuhur">
                <div class="panel-header"><i class="bi bi-brightness-high me-1"></i> Dzuhur</div>
                <div class="panel-body"><span><?= htmlspecialchars($dzuhurVal) ?></span></div>
            </div>
            <div class="panel ashar" id="panel-ashar">
                <div class="panel-header"><i class="bi bi-cloud-sun me-1"></i> Ashar</div>
                <div class="panel-body"><span><?= htmlspecialchars($asharVal) ?></span></div>
            </div>
            <div class="panel maghrib" id="panel-maghrib">
                <div class="panel-header"><i class="bi bi-sunset me-1"></i> Maghrib</div>
                <div class="panel-body"><span><?= htmlspecialchars($maghribVal) ?></span></div>
            </div>
            <div class="panel isya" id="panel-isya">
                <div class="panel-header"><i class="bi bi-moon-stars-fill me-1"></i> Isya</div>
                <div class="panel-body"><span><?= htmlspecialchars($isyaVal) ?></span></div>
            </div>
        </div>
    </div>

    <!-- BOTTOM GRID: SALDO INFAQ & AUTO-SLIDING 5 INFORMASI -->
    <div class="bottom-grid">
        <?php
        $q = mysqli_query($koneksi,"SELECT SUM(jumlah) AS total FROM keuangan WHERE jenis='infaq'");
        $d = mysqli_fetch_assoc($q);
        $saldo = $d['total'] ?? 0;
        ?>
        <div class="saldo-box">
            <div class="saldo-label">
                💰 SALDO INFAQ SAAT INI
            </div>
            <div class="saldo-amount">
                Rp <?= number_format($saldo,0,',','.') ?>
            </div>
        </div>

        <div class="info-section">
            <?php if(count($infoList) > 0) { 
                $chunks = array_chunk($infoList, 2);
                foreach($chunks as $slideIndex => $slideItems) {
                ?>
                <div class="info-slide <?= $slideIndex === 0 ? 'active' : '' ?>" data-slide="<?= $slideIndex ?>">
                    <div class="row g-2 h-100">
                        <?php foreach($slideItems as $item) { ?>
                        <div class="<?= count($slideItems) === 1 ? 'col-12' : 'col-6' ?> h-100">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <h5 class="card-title d-flex align-items-center gap-2">
                                        <span class="toa-icon-badge"><i class="bi bi-megaphone-fill"></i></span>
                                        <span class="text-warning">📢 <?= htmlspecialchars($item['judul']) ?></span>
                                    </h5>
                                    <p class="card-text text-white-50 mt-1 mb-0">
                                        <?= nl2br(htmlspecialchars($item['isi'])) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

                <?php if(count($chunks) > 1) { ?>
                <div class="slide-indicators">
                    <?php foreach($chunks as $idx => $chk) { ?>
                        <span class="dot <?= $idx === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $idx ?>)"></span>
                    <?php } ?>
                </div>
                <?php } ?>
            <?php } else { ?>
                <div class="card h-100">
                    <div class="card-body text-center text-white-50 py-2 d-flex align-items-center justify-content-center">
                        Belum ada pengumuman masjid saat ini.
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

</div>

<!-- MARQUEE FOOTER -->
<div class="marquee">
    <div class="marquee-badge">
        <i class="bi bi-megaphone-fill text-warning fs-6"></i> 📢 INFORMASI MASJID
    </div>
    <div class="w-100 overflow-hidden">
        <marquee scrollamount="7">
        <?php
        $mq = mysqli_query($koneksi,"SELECT isi FROM info_masjid");
        $hasInfo = false;
        while($m=mysqli_fetch_assoc($mq)){
            echo htmlspecialchars($m['isi'])." &bull; ";
            $hasInfo = true;
        }
        if(!$hasInfo){
            echo "Selamat Datang di Masjid Al-Ikhlas &bull; Jagalah Kebersihan & Kekhusyukan Beribadah";
        }
        ?>
        </marquee>
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Real-time Clock, Prayer Countdown & Iqomah Engine -->
<script>
const PRAYER_TIMES = {
    imsak: "<?= $imsakVal ?>",
    subuh: "<?= $subuhVal ?>",
    syuruq: "<?= $syuruqVal ?>",
    dzuhur: "<?= $dzuhurVal ?>",
    ashar: "<?= $asharVal ?>",
    maghrib: "<?= $maghribVal ?>",
    isya: "<?= $isyaVal ?>"
};

const JEDA_IQOMAH_MAP = {
    subuh: <?= $iqSubuh ?>,
    dzuhur: <?= $iqDzuhur ?>,
    ashar: <?= $iqAshar ?>,
    maghrib: <?= $iqMaghrib ?>,
    isya: <?= $iqIsya ?>
};

function updateWaktu(){
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById("jam").textContent = `${hours}:${minutes}:${seconds}`;

    updatePrayerCountdown(now);
}

function timeToDateObj(timeStr, baseDate) {
    const [h, m] = timeStr.split(':').map(Number);
    const d = new Date(baseDate);
    d.setHours(h, m, 0, 0);
    return d;
}

const DURASI_SHOLAT_MINS = 15; // Estimasi durasi waktu sholat berjamaah (menit)

function updatePrayerCountdown(now) {
    const isFriday = (now.getDay() === 5); // 5 = Hari Jumat

    const list = [
        { name: 'Subuh', key: 'subuh', timeStr: PRAYER_TIMES.subuh },
        { name: isFriday ? 'Sholat Jumat' : 'Dzuhur', key: 'dzuhur', timeStr: PRAYER_TIMES.dzuhur },
        { name: 'Ashar', key: 'ashar', timeStr: PRAYER_TIMES.ashar },
        { name: 'Maghrib', key: 'maghrib', timeStr: PRAYER_TIMES.maghrib },
        { name: 'Isya', key: 'isya', timeStr: PRAYER_TIMES.isya }
    ];

    let targetPrayer = null;
    let mode = 'normal'; // 'normal', 'iqomah', 'sholat'
    let activeKey = '';

    for (let i = 0; i < list.length; i++) {
        let iqomahMins = JEDA_IQOMAH_MAP[list[i].key] || 5;

        // Khusus Hari Jumat Dzuhur (Sholat Jumat) => Countdown Iqomah 0 Menit
        if (isFriday && list[i].key === 'dzuhur') {
            iqomahMins = 0;
        }

        const pDate = timeToDateObj(list[i].timeStr, now);
        const iqomahEndDate = new Date(pDate.getTime() + iqomahMins * 60 * 1000);
        const sholatEndDate = new Date(iqomahEndDate.getTime() + DURASI_SHOLAT_MINS * 60 * 1000);

        // 1. Cek jika saat ini masuk dalam rentang jeda iqomah (Rawatib -> Iqomah)
        if (iqomahMins > 0 && now >= pDate && now < iqomahEndDate) {
            targetPrayer = list[i];
            mode = 'iqomah';
            activeKey = list[i].key;
            targetPrayer.targetTime = iqomahEndDate;
            break;
        }

        // 2. Cek jika waktu iqomah habis & sholat berjamaah sedang berlangsung
        if (now >= iqomahEndDate && now < sholatEndDate) {
            targetPrayer = list[i];
            mode = 'sholat';
            activeKey = list[i].key;
            targetPrayer.targetTime = sholatEndDate;
            break;
        }

        // 3. Cek sholat berikutnya hari ini
        if (now < pDate) {
            targetPrayer = list[i];
            mode = 'normal';
            activeKey = list[i].key;
            targetPrayer.targetTime = pDate;
            break;
        }
    }

    if (!targetPrayer) {
        targetPrayer = list[0]; // Subuh Besok
        mode = 'normal';
        activeKey = 'subuh';
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        targetPrayer.targetTime = timeToDateObj(PRAYER_TIMES.subuh, tomorrow);
    }

    // Highlight Active Panel
    document.querySelectorAll('.panel-sholat .panel').forEach(el => el.classList.remove('active-prayer'));
    const activePanel = document.getElementById(`panel-${activeKey}`);
    if (activePanel) {
        activePanel.classList.add('active-prayer');
    }

    // Render Timer UI
    const boxEl = document.getElementById('countdownBox');
    const badgeEl = document.getElementById('countdownBadge');
    const labelEl = document.getElementById('nextPrayerLabel');
    const nameEl = document.getElementById('nextPrayerName');
    const timeTextEl = document.getElementById('nextPrayerTimeText');
    const timerEl = document.getElementById('countdownTimer');
    const noteEl = document.getElementById('iqomahInfoNote');

    const diffMs = targetPrayer.targetTime - now;
    const diffSec = Math.max(0, Math.floor(diffMs / 1000));

    const hrs = String(Math.floor(diffSec / 3600)).padStart(2, '0');
    const mins = String(Math.floor((diffSec % 3600) / 60)).padStart(2, '0');
    const secs = String(diffSec % 60).padStart(2, '0');

    boxEl.classList.remove('mode-iqomah', 'mode-sholat');

    if (mode === 'iqomah') {
        boxEl.classList.add('mode-iqomah');
        badgeEl.className = "badge bg-danger text-white fw-bold px-3 py-1 rounded-pill";
        badgeEl.innerHTML = `🔔 JEDA RAWATIB & COUNTDOWN IQOMAH`;
        labelEl.textContent = "Hitung Mundur Iqomah Sholat";
        nameEl.textContent = targetPrayer.name.toUpperCase();
        timeTextEl.textContent = "";
        timerEl.className = "countdown-timer timer-iqomah";
        timerEl.textContent = `${mins}:${secs}`;
        if (noteEl) {
            noteEl.innerHTML = `✨ <strong>Sholat Sunnah Rawatib &amp; Persiapan Iqomah</strong> &bull; Harap Rapatkan Shaf`;
        }
    } else if (mode === 'sholat') {
        boxEl.classList.add('mode-sholat');
        badgeEl.className = "badge bg-success text-white fw-bold px-3 py-1 rounded-pill";
        badgeEl.innerHTML = `🕌 SHOLAT BERJAMAAH SEDANG BERLANGSUNG`;
        labelEl.textContent = "Waktu Sholat";
        nameEl.textContent = targetPrayer.name.toUpperCase();
        timeTextEl.textContent = "";
        timerEl.className = "countdown-timer timer-sholat";
        timerEl.textContent = `${mins}:${secs}`;
        if (noteEl) {
            noteEl.innerHTML = `🔇 <strong>Mohon Matikan HP / Nada Dering</strong> &bull; Luruskan & Rapatkan Shaf`;
        }
    } else {
        badgeEl.className = "badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill";
        badgeEl.innerHTML = `⏰ SHOLAT BERIKUTNYA`;
        labelEl.textContent = "Menuju Sholat";
        nameEl.textContent = targetPrayer.name;
        timeTextEl.textContent = `(${targetPrayer.timeStr} WIB)`;
        timerEl.className = "countdown-timer";
        timerEl.textContent = `-${hrs}:${mins}:${secs}`;
        if (noteEl) {
            noteEl.innerHTML = `📍 <strong><?= htmlspecialchars($kotaJadwal) ?></strong> &bull; Jeda Iqomah: Subuh <?= $iqSubuh ?>m, Dzuhur <?= $iqDzuhur ?>m, Ashar <?= $iqAshar ?>m, Maghrib <?= $iqMaghrib ?>m, Isya <?= $iqIsya ?>m (Jumat: 0m)`;
        }
    }
}

// AUTO SLIDER LOGIC UNTUK 5 INFORMASI MASJID
let currentSlide = 0;
const slides = document.querySelectorAll('.info-slide');
const dots = document.querySelectorAll('.slide-indicators .dot');

function goToSlide(index) {
    if (slides.length <= 1) return;
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
        if (dots[i]) dots[i].classList.remove('active');
    });
    currentSlide = index % slides.length;
    slides[currentSlide].classList.add('active');
    if (dots[currentSlide]) dots[currentSlide].classList.add('active');
}

if (slides.length > 1) {
    setInterval(() => {
        goToSlide(currentSlide + 1);
    }, 6000);
}

setInterval(updateWaktu, 1000);
updateWaktu();
</script>

</body>
</html>