<?php
session_start();
include "../config/koneksi.php";

// Cek hak akses admin
if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("location:../login.php");
    exit;
}

// OTOMATIS BUAT TABEL JADWAL SHOLAT JIKA BELUM ADA
mysqli_query($koneksi, "
CREATE TABLE IF NOT EXISTS `jadwal_sholat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mode_jadwal` varchar(20) NOT NULL DEFAULT 'manual',
  `kota` varchar(100) NOT NULL DEFAULT 'Kab. Bogor (Cibinong)',
  `jeda_iqomah` int(11) NOT NULL DEFAULT 10,
  `iqomah_subuh` int(11) NOT NULL DEFAULT 7,
  `iqomah_dzuhur` int(11) NOT NULL DEFAULT 4,
  `iqomah_ashar` int(11) NOT NULL DEFAULT 4,
  `iqomah_maghrib` int(11) NOT NULL DEFAULT 6,
  `iqomah_isya` int(11) NOT NULL DEFAULT 5,
  `imsak` varchar(10) NOT NULL DEFAULT '04:36',
  `subuh` varchar(10) NOT NULL DEFAULT '04:46',
  `syuruq` varchar(10) NOT NULL DEFAULT '06:02',
  `dzuhur` varchar(10) NOT NULL DEFAULT '12:03',
  `ashar` varchar(10) NOT NULL DEFAULT '15:24',
  `maghrib` varchar(10) NOT NULL DEFAULT '17:56',
  `isya` varchar(10) NOT NULL DEFAULT '19:09',
  `tanggal` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Migration Kolom Tambahan (Safe Alter)
@mysqli_query($koneksi, "ALTER TABLE `jadwal_sholat` ADD COLUMN `mode_jadwal` varchar(20) NOT NULL DEFAULT 'manual'");
@mysqli_query($koneksi, "ALTER TABLE `jadwal_sholat` ADD COLUMN `kota` varchar(100) NOT NULL DEFAULT 'Kab. Bogor (Cibinong)'");
@mysqli_query($koneksi, "ALTER TABLE `jadwal_sholat` ADD COLUMN `jeda_iqomah` int(11) NOT NULL DEFAULT 10");
@mysqli_query($koneksi, "ALTER TABLE `jadwal_sholat` ADD COLUMN `iqomah_subuh` int(11) NOT NULL DEFAULT 7");
@mysqli_query($koneksi, "ALTER TABLE `jadwal_sholat` ADD COLUMN `iqomah_dzuhur` int(11) NOT NULL DEFAULT 4");
@mysqli_query($koneksi, "ALTER TABLE `jadwal_sholat` ADD COLUMN `iqomah_ashar` int(11) NOT NULL DEFAULT 4");
@mysqli_query($koneksi, "ALTER TABLE `jadwal_sholat` ADD COLUMN `iqomah_maghrib` int(11) NOT NULL DEFAULT 6");
@mysqli_query($koneksi, "ALTER TABLE `jadwal_sholat` ADD COLUMN `iqomah_isya` int(11) NOT NULL DEFAULT 5");
@mysqli_query($koneksi, "ALTER TABLE `jadwal_sholat` ADD COLUMN `syuruq` varchar(10) NOT NULL DEFAULT '06:02'");

// Inisialisasi data default jika kosong
$checkSholat = mysqli_query($koneksi, "SELECT * FROM jadwal_sholat");
if(mysqli_num_rows($checkSholat) == 0){
    mysqli_query($koneksi, "INSERT INTO jadwal_sholat (mode_jadwal, kota, jeda_iqomah, iqomah_subuh, iqomah_dzuhur, iqomah_ashar, iqomah_maghrib, iqomah_isya, imsak, subuh, syuruq, dzuhur, ashar, maghrib, isya, tanggal) VALUES ('manual', 'Kab. Bogor (Cibinong)', 10, 7, 4, 4, 6, 5, '04:36', '04:46', '06:02', '12:03', '15:24', '17:56', '19:09', CURDATE())");
}

// SELALU UPDATE KORIDOR ROW 1 & INFO_MASJID KE WAKTU TERBARU
@mysqli_query($koneksi, "UPDATE jadwal_sholat SET imsak='04:36', subuh='04:46', syuruq='06:02', dzuhur='12:03', ashar='15:24', maghrib='17:56', isya='19:09' WHERE id=1 OR id=(SELECT id FROM (SELECT id FROM jadwal_sholat ORDER BY id DESC LIMIT 1) AS t)");
@mysqli_query($koneksi, "UPDATE info_masjid SET isi='Imsak 04:36 WIB | Subuh 04:46 WIB | Syuruq 06:02 WIB | Dzuhur 12:03 WIB | Ashar 15:24 WIB | Maghrib 17:56 WIB | Isya 19:09 WIB' WHERE id=2 OR judul LIKE '%Jadwal Sholat%'");

// -------------------------------------------------------------
// LIST WILAYAH JABODETABEK RESMI KEMENAG RI
// -------------------------------------------------------------
$citiesJabodetabek = [
    'kab_bogor'   => ['nama' => 'Kab. Bogor (Cibinong)', 'myquran_id' => '1204', 'aladhan' => 'Cibinong'],
    'kota_bogor'  => ['nama' => 'Kota Bogor',            'myquran_id' => '1224', 'aladhan' => 'Bogor'],
    'dki_jakarta' => ['nama' => 'DKI Jakarta',           'myquran_id' => '1301', 'aladhan' => 'Jakarta'],
    'depok'       => ['nama' => 'Kota Depok',            'myquran_id' => '1225', 'aladhan' => 'Depok'],
    'tangerang'   => ['nama' => 'Kota Tangerang',        'myquran_id' => '1401', 'aladhan' => 'Tangerang'],
    'tangsel'     => ['nama' => 'Kota Tangerang Selatan','myquran_id' => '1403', 'aladhan' => 'Tangerang'],
    'kab_tang'    => ['nama' => 'Kab. Tangerang',        'myquran_id' => '1402', 'aladhan' => 'Tangerang'],
    'bekasi'      => ['nama' => 'Kota Bekasi',           'myquran_id' => '1221', 'aladhan' => 'Bekasi'],
    'kab_bekasi'  => ['nama' => 'Kab. Bekasi',           'myquran_id' => '1203', 'aladhan' => 'Bekasi'],
];

// -------------------------------------------------------------
// PROCESS: UPDATE JADWAL SHOLAT (MANUAL & SETTING IQOMAH PER WAKTU)
// -------------------------------------------------------------
if(isset($_POST['update_sholat'])){
    $mode_jadwal   = mysqli_real_escape_string($koneksi, $_POST['mode_jadwal']);
    $kota          = mysqli_real_escape_string($koneksi, $_POST['kota']);
    $iqomah_subuh   = (int)$_POST['iqomah_subuh'];
    $iqomah_dzuhur  = (int)$_POST['iqomah_dzuhur'];
    $iqomah_ashar   = (int)$_POST['iqomah_ashar'];
    $iqomah_maghrib = (int)$_POST['iqomah_maghrib'];
    $iqomah_isya    = (int)$_POST['iqomah_isya'];
    $imsak   = mysqli_real_escape_string($koneksi, $_POST['imsak']);
    $subuh   = mysqli_real_escape_string($koneksi, $_POST['subuh']);
    $syuruq  = mysqli_real_escape_string($koneksi, $_POST['syuruq']);
    $dzuhur  = mysqli_real_escape_string($koneksi, $_POST['dzuhur']);
    $ashar   = mysqli_real_escape_string($koneksi, $_POST['ashar']);
    $maghrib = mysqli_real_escape_string($koneksi, $_POST['maghrib']);
    $isya    = mysqli_real_escape_string($koneksi, $_POST['isya']);

    $qLast = mysqli_query($koneksi, "SELECT id FROM jadwal_sholat ORDER BY id DESC LIMIT 1");
    $dLast = mysqli_fetch_assoc($qLast);
    $sholat_id = $dLast['id'] ?? 1;

    mysqli_query($koneksi, "
        UPDATE jadwal_sholat SET
        mode_jadwal='$mode_jadwal',
        kota='$kota',
        iqomah_subuh='$iqomah_subuh',
        iqomah_dzuhur='$iqomah_dzuhur',
        iqomah_ashar='$iqomah_ashar',
        iqomah_maghrib='$iqomah_maghrib',
        iqomah_isya='$iqomah_isya',
        imsak='$imsak',
        subuh='$subuh',
        syuruq='$syuruq',
        dzuhur='$dzuhur',
        ashar='$ashar',
        maghrib='$maghrib',
        isya='$isya',
        tanggal=CURDATE()
        WHERE id='$sholat_id'
    ");
    $_SESSION['pesan'] = "Pengaturan Jam Sholat & Jeda Iqomah per Waktu berhasil disimpan!";
    header("location:display.php");
    exit;
}

// -------------------------------------------------------------
// PROCESS: SYNC OTOMATIS KEMENAG RI WILAYAH JABODETABEK (1-KLIK)
// -------------------------------------------------------------
if(isset($_POST['sync_kemenag'])){
    $wilayahKey = $_POST['wilayah_jabodetabek'] ?? 'kab_bogor';
    $selectedCity = $citiesJabodetabek[$wilayahKey] ?? $citiesJabodetabek['kab_bogor'];

    $idKotaMyQuran = $selectedCity['myquran_id'];
    $namaWilayah   = $selectedCity['nama'];
    $aladhanCity   = $selectedCity['aladhan'];
    
    $thn = date('Y');
    $bln = date('m');
    $tgl = date('d');

    $fetched = false;
    $imsak = $subuh = $syuruq = $dzuhur = $ashar = $maghrib = $isya = "";

    // METHOD 1: MyQuran Kemenag RI Official API
    $apiUrl1 = "https://api.myquran.com/v2/sholat/jadwal/$idKotaMyQuran/$thn/$bln/$tgl";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res1 = curl_exec($ch);
    curl_close($ch);

    if($res1){
        $json1 = json_decode($res1, true);
        if(isset($json1['status']) && $json1['status'] == true && isset($json1['data']['jadwal'])){
            $j = $json1['data']['jadwal'];
            $imsak   = $j['imsak'] ?? '04:36';
            $subuh   = $j['subuh'] ?? '04:46';
            $syuruq  = $j['terbit'] ?? '06:02';
            $dzuhur  = $j['dzuhur'] ?? '12:03';
            $ashar   = $j['ashar'] ?? '15:24';
            $maghrib = $j['maghrib'] ?? '17:56';
            $isya    = $j['isya'] ?? '19:09';
            $fetched = true;
        }
    }

    // METHOD 2: AlAdhan Kemenag Method 2 Fallback
    if(!$fetched){
        $apiUrl2 = "https://api.aladhan.com/v1/timingsByCity?city=$aladhanCity&country=Indonesia&method=20";
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $apiUrl2);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        $res2 = curl_exec($ch2);
        curl_close($ch2);

        if($res2){
            $json2 = json_decode($res2, true);
            if(isset($json2['code']) && $json2['code'] == 200 && isset($json2['data']['timings'])){
                $t = $json2['data']['timings'];
                $imsak   = substr($t['Imsak'], 0, 5);
                $subuh   = substr($t['Fajr'], 0, 5);
                $syuruq  = substr($t['Sunrise'], 0, 5);
                $dzuhur  = substr($t['Dhuhr'], 0, 5);
                $ashar   = substr($t['Asr'], 0, 5);
                $maghrib = substr($t['Maghrib'], 0, 5);
                $isya    = substr($t['Isha'], 0, 5);
                $fetched = true;
            }
        }
    }

    if($fetched){
        $qLast = mysqli_query($koneksi, "SELECT id FROM jadwal_sholat ORDER BY id DESC LIMIT 1");
        $dLast = mysqli_fetch_assoc($qLast);
        $sholat_id = $dLast['id'] ?? 1;

        mysqli_query($koneksi, "
            UPDATE jadwal_sholat SET
            mode_jadwal='kemenag',
            kota='$namaWilayah',
            imsak='$imsak',
            subuh='$subuh',
            syuruq='$syuruq',
            dzuhur='$dzuhur',
            ashar='$ashar',
            maghrib='$maghrib',
            isya='$isya',
            tanggal=CURDATE()
            WHERE id='$sholat_id'
        ");

        $runningText = "Jadwal Sholat Kemenag RI ($namaWilayah): Imsak $imsak WIB | Subuh $subuh WIB | Syuruq $syuruq WIB | Dzuhur $dzuhur WIB | Ashar $ashar WIB | Maghrib $maghrib WIB | Isya $isya WIB";
        @mysqli_query($koneksi, "UPDATE info_masjid SET isi='$runningText' WHERE id=2 OR judul LIKE '%Jadwal Sholat%'");

        $_SESSION['pesan'] = "Berhasil 1-Klik Sync Jadwal Sholat Kemenag RI untuk wilayah $namaWilayah!";
    } else {
        $_SESSION['pesan_err'] = "Gagal terhubung ke server API Kemenag RI. Silakan gunakan mode input manual.";
    }

    header("location:display.php");
    exit;
}

// -------------------------------------------------------------
// PROCESS: TAMBAH INFO DISPLAY
// -------------------------------------------------------------
if(isset($_POST['simpan_info'])){
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isi   = mysqli_real_escape_string($koneksi, $_POST['isi']);

    mysqli_query($koneksi, "INSERT INTO info_masjid (judul, isi) VALUES ('$judul', '$isi')");
    $_SESSION['pesan'] = "Informasi Display berhasil ditambahkan!";
    header("location:display.php");
    exit;
}

// -------------------------------------------------------------
// PROCESS: HAPUS INFO DISPLAY
// -------------------------------------------------------------
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM info_masjid WHERE id='$id'");
    $_SESSION['pesan'] = "Informasi Display berhasil dihapus!";
    header("location:display.php");
    exit;
}

// -------------------------------------------------------------
// PROCESS: EDIT INFO DISPLAY
// -------------------------------------------------------------
$editInfo = false;
if(isset($_GET['edit'])){
    $editInfo = true;
    $id_edit = (int)$_GET['edit'];
    $qEdit = mysqli_query($koneksi, "SELECT * FROM info_masjid WHERE id='$id_edit'");
    $dEdit = mysqli_fetch_assoc($qEdit);
}

// PROCESS: UPDATE INFO DISPLAY
if(isset($_POST['update_info'])){
    $id    = (int)$_POST['id'];
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isi   = mysqli_real_escape_string($koneksi, $_POST['isi']);

    mysqli_query($koneksi, "UPDATE info_masjid SET judul='$judul', isi='$isi' WHERE id='$id'");
    $_SESSION['pesan'] = "Informasi Display berhasil diperbarui!";
    header("location:display.php");
    exit;
}

// AMBIL DATA JADWAL SHOLAT SAAT INI
$qSholat = mysqli_query($koneksi, "SELECT * FROM jadwal_sholat ORDER BY id DESC LIMIT 1");
$dataSholat = mysqli_fetch_assoc($qSholat);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Display & Jadwal Sholat | Admin</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans & Amiri -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:wght@700&display=swap" rel="stylesheet">

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
            box-shadow: 0 12px 35px rgba(0,0,0,0.05);
            background: #ffffff;
        }

        .input-time-custom {
            font-weight: 700;
            text-align: center;
            font-size: 1.1rem;
            border-radius: 12px;
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

        .btn-sync-kemenag {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: #ffffff;
            border: none;
            font-weight: 700;
            border-radius: 30px;
            padding: 10px 24px;
            transition: all 0.3s ease;
        }

        .btn-sync-kemenag:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4);
            color: #ffffff;
        }

        .iqomah-badge-box {
            background: rgba(13, 110, 253, 0.05);
            border: 1.5px dashed rgba(13, 110, 253, 0.3);
            border-radius: 18px;
            padding: 18px;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm mb-4 py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                📺 Display TV & Jadwal Sholat | Admin Panel
            </span>
            <div class="text-white d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <div class="clock-badge d-flex align-items-center gap-2 me-2">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span id="adminClock" class="text-warning">--:--:-- WIB</span>
                </div>
                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3 text-nowrap">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <a href="../display.php" target="_blank" class="btn btn-warning btn-sm text-dark fw-bold rounded-pill px-3 text-nowrap">
                    <i class="bi bi-tv me-1"></i> Lihat Display TV
                </a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">

        <!-- HEADER TITLE WITH BISMILLAH -->
        <div class="text-center mb-4">
            <div class="bismillah-header">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
            <h4 class="fw-bold text-dark m-0">Pengaturan Jadwal Sholat & Tampilan TV Digital</h4>
            <p class="text-muted small">Sinkronisasi otomatis Kemenag RI, waktu jeda iqomah per sholat, serta materi pengumuman layar</p>
        </div>

        <!-- NOTIFIKASI SUKSES / ERROR -->
        <?php if(isset($_SESSION['pesan'])){ ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['pesan']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['pesan']); ?>
        <?php } ?>

        <?php if(isset($_SESSION['pesan_err'])){ ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $_SESSION['pesan_err']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['pesan_err']); ?>
        <?php } ?>

        <!-- ROW 1: 1-KLIK SYNC RESMI KEMENAG RI WILAYAH JABODETABEK -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-custom p-4 border-success border border-2">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-bold mb-1">
                                <i class="bi bi-patch-check-fill me-1"></i> Resmi Kemenag RI
                            </span>
                            <h5 class="fw-bold text-dark m-0">1-Klik Sync Jadwal Sholat Kemenag RI Wilayah JABODETABEK</h5>
                            <small class="text-muted">Update otomatis presisi tanggal hari ini (<?= date('d F Y') ?>) mencakup Imsak, Subuh, Syuruq, Dzuhur, Ashar, Maghrib, Isya</small>
                        </div>

                        <form method="post" class="d-flex align-items-center gap-2 flex-wrap">
                            <select name="wilayah_jabodetabek" class="form-select rounded-pill px-3 py-2 border-primary fw-bold text-primary" style="min-width: 220px;">
                                <?php foreach($citiesJabodetabek as $key => $c){ ?>
                                    <option value="<?= $key ?>" <?= ($dataSholat['kota'] ?? '') == $c['nama'] ? 'selected' : '' ?>>
                                        <?= $c['nama'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <button type="submit" name="sync_kemenag" class="btn btn-sync-kemenag shadow-sm text-nowrap">
                                <i class="bi bi-cloud-arrow-down-fill me-1"></i> Sync Kemenag RI
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 2: PENGATURAN MANUAL & JEDA IQOMAH PER WAKTU SHOLAT -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-custom p-4">
                    
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 border-bottom pb-3 gap-2">
                        <div>
                            <h5 class="fw-bold text-success m-0 d-flex align-items-center gap-2">
                                <i class="bi bi-sliders fs-4"></i> Pengaturan Jam Sholat & Jeda Iqomah Rawatib Per Waktu
                            </h5>
                            <small class="text-muted">Wilayah Terpasang: <strong><?= htmlspecialchars($dataSholat['kota'] ?? 'Kab. Bogor (Cibinong)') ?></strong></small>
                        </div>

                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold">
                            MODE: <?= strtoupper($dataSholat['mode_jadwal'] ?? 'MANUAL') ?>
                        </span>
                    </div>

                    <form method="post">
                        <div class="row g-3 align-items-center mb-4 bg-light p-3 rounded-4 border">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Sumber / Mode Jadwal Sholat</label>
                                <select name="mode_jadwal" class="form-select rounded-3 fw-bold text-success">
                                    <option value="manual" <?= ($dataSholat['mode_jadwal'] ?? '') == 'manual' ? 'selected' : '' ?>>Input Manual Jam Sholat</option>
                                    <option value="kemenag" <?= ($dataSholat['mode_jadwal'] ?? '') == 'kemenag' ? 'selected' : '' ?>>Otomatis Kemenag RI (JABODETABEK)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Label Wilayah Tampilan Layar TV</label>
                                <input type="text" name="kota" class="form-control rounded-3 fw-bold" value="<?= htmlspecialchars($dataSholat['kota'] ?? 'Kab. Bogor (Cibinong)') ?>" required>
                            </div>
                        </div>

                        <!-- JEDA COUNTDOWN IQOMAH 5 WAKTU SHOLAT -->
                        <div class="iqomah-badge-box mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                <h6 class="fw-bold text-dark m-0">
                                    <i class="bi bi-hourglass-split text-danger me-1"></i> Jeda Countdown Iqomah Rawatib Per Waktu Sholat (Menit)
                                </h6>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.75rem;">
                                    <i class="bi bi-star-fill me-1"></i> Khusus Hari Jumat (Dzuhur/Sholat Jumat): Countdown Iqomah = 0 Menit
                                </span>
                            </div>

                            <div class="row g-3">
                                <div class="col-6 col-md">
                                    <label class="form-label fw-bold text-secondary small">🌅 Subuh</label>
                                    <div class="input-group">
                                        <input type="number" name="iqomah_subuh" class="form-control rounded-start-3 fw-bold text-danger text-center" value="<?= $dataSholat['iqomah_subuh'] ?? 7 ?>" min="0" max="60" required>
                                        <span class="input-group-text bg-white fw-semibold small">Mnt</span>
                                    </div>
                                </div>

                                <div class="col-6 col-md">
                                    <label class="form-label fw-bold text-secondary small">☀️ Dzuhur</label>
                                    <div class="input-group">
                                        <input type="number" name="iqomah_dzuhur" class="form-control rounded-start-3 fw-bold text-danger text-center" value="<?= $dataSholat['iqomah_dzuhur'] ?? 4 ?>" min="0" max="60" required>
                                        <span class="input-group-text bg-white fw-semibold small">Mnt</span>
                                    </div>
                                </div>

                                <div class="col-6 col-md">
                                    <label class="form-label fw-bold text-secondary small">🌤️ Ashar</label>
                                    <div class="input-group">
                                        <input type="number" name="iqomah_ashar" class="form-control rounded-start-3 fw-bold text-danger text-center" value="<?= $dataSholat['iqomah_ashar'] ?? 4 ?>" min="0" max="60" required>
                                        <span class="input-group-text bg-white fw-semibold small">Mnt</span>
                                    </div>
                                </div>

                                <div class="col-6 col-md">
                                    <label class="form-label fw-bold text-secondary small">🌅 Maghrib</label>
                                    <div class="input-group">
                                        <input type="number" name="iqomah_maghrib" class="form-control rounded-start-3 fw-bold text-danger text-center" value="<?= $dataSholat['iqomah_maghrib'] ?? 6 ?>" min="0" max="60" required>
                                        <span class="input-group-text bg-white fw-semibold small">Mnt</span>
                                    </div>
                                </div>

                                <div class="col-6 col-md">
                                    <label class="form-label fw-bold text-secondary small">🌙 Isya</label>
                                    <div class="input-group">
                                        <input type="number" name="iqomah_isya" class="form-control rounded-start-3 fw-bold text-danger text-center" value="<?= $dataSholat['iqomah_isya'] ?? 5 ?>" min="0" max="60" required>
                                        <span class="input-group-text bg-white fw-semibold small">Mnt</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- JAM WAKTU SHOLAT -->
                        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-clock me-1"></i> Jam Waktu Sholat (Format HH:MM)</h6>
                        <div class="row g-2 g-md-3 text-center">
                            <div class="col-4 col-md">
                                <label class="form-label fw-bold text-secondary small">Imsak</label>
                                <input type="text" name="imsak" class="form-control input-time-custom border-primary"
                                       value="<?= htmlspecialchars($dataSholat['imsak'] ?? '04:36') ?>" required>
                            </div>
                            <div class="col-4 col-md">
                                <label class="form-label fw-bold text-secondary small">Subuh</label>
                                <input type="text" name="subuh" class="form-control input-time-custom border-primary"
                                       value="<?= htmlspecialchars($dataSholat['subuh'] ?? '04:46') ?>" required>
                            </div>
                            <div class="col-4 col-md">
                                <label class="form-label fw-bold text-secondary small">Syuruq</label>
                                <input type="text" name="syuruq" class="form-control input-time-custom border-info text-info"
                                       value="<?= htmlspecialchars($dataSholat['syuruq'] ?? '06:02') ?>" required>
                            </div>
                            <div class="col-4 col-md">
                                <label class="form-label fw-bold text-secondary small">Dzuhur</label>
                                <input type="text" name="dzuhur" class="form-control input-time-custom border-success"
                                       value="<?= htmlspecialchars($dataSholat['dzuhur'] ?? '12:03') ?>" required>
                            </div>
                            <div class="col-4 col-md">
                                <label class="form-label fw-bold text-secondary small">Ashar</label>
                                <input type="text" name="ashar" class="form-control input-time-custom border-warning"
                                       value="<?= htmlspecialchars($dataSholat['ashar'] ?? '15:24') ?>" required>
                            </div>
                            <div class="col-4 col-md">
                                <label class="form-label fw-bold text-secondary small">Maghrib</label>
                                <input type="text" name="maghrib" class="form-control input-time-custom border-danger"
                                       value="<?= htmlspecialchars($dataSholat['maghrib'] ?? '17:56') ?>" required>
                            </div>
                            <div class="col-4 col-md">
                                <label class="form-label fw-bold text-secondary small">Isya</label>
                                <input type="text" name="isya" class="form-control input-time-custom border-dark"
                                       value="<?= htmlspecialchars($dataSholat['isya'] ?? '19:09') ?>" required>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" name="update_sholat" class="btn btn-success px-4 rounded-pill fw-bold">
                                <i class="bi bi-save me-1"></i> Simpan Pengaturan Jam & Jeda Iqomah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ROW 3: KELOLA INFO & PENGUMUMAN DISPLAY -->
        <div class="row g-4">
            
            <!-- FORM INPUT / EDIT INFO -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-megaphone me-2 text-warning"></i>
                        <?= $editInfo ? "Edit Pengumuman" : "Tambah Pengumuman" ?>
                    </h5>

                    <form method="post">
                        <?php if($editInfo){ ?>
                            <input type="hidden" name="id" value="<?= $dEdit['id'] ?>">
                        <?php } ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Judul Pengumuman / Info</label>
                            <input type="text" name="judul" class="form-control rounded-3" placeholder="Contoh: Kajian Rutin Subuh" required
                                   value="<?= $editInfo ? htmlspecialchars($dEdit['judul']) : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Isi Pengumuman / Teks Marquee</label>
                            <textarea name="isi" class="form-control rounded-3" rows="5" placeholder="Tuliskan detail pengumuman..." required><?= $editInfo ? htmlspecialchars($dEdit['isi']) : '' ?></textarea>
                        </div>

                        <button type="submit" name="<?= $editInfo ? 'update_info' : 'simpan_info' ?>" class="btn btn-primary w-100 rounded-pill fw-bold">
                            <i class="bi bi-send me-1"></i> <?= $editInfo ? "Update Informasi" : "Simpan Informasi" ?>
                        </button>

                        <?php if($editInfo){ ?>
                            <a href="display.php" class="btn btn-outline-secondary w-100 rounded-pill mt-2">
                                Batal Edit
                            </a>
                        <?php } ?>
                    </form>
                </div>
            </div>

            <!-- TABEL DATA PENGUMUMAN DISPLAY -->
            <div class="col-lg-8">
                <div class="card card-custom p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-dark m-0">
                            <i class="bi bi-list-stars me-2 text-primary"></i> Daftar Pengumuman Display TV
                        </h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th width="60" class="text-center">No</th>
                                    <th>Judul Pengumuman</th>
                                    <th>Isi Teks / Marquee</th>
                                    <th width="120" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $qInfo = mysqli_query($koneksi, "SELECT * FROM info_masjid ORDER BY id DESC");
                                if(mysqli_num_rows($qInfo) > 0) {
                                    while($d = mysqli_fetch_assoc($qInfo)){
                                ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= $no++ ?></td>
                                    <td class="fw-semibold text-primary"><?= htmlspecialchars($d['judul']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($d['isi'])) ?></td>
                                    <td class="text-center">
                                        <a href="?edit=<?= $d['id'] ?>" class="btn btn-warning btn-sm rounded-circle shadow-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?hapus=<?= $d['id'] ?>" onclick="return confirm('Yakin ingin menghapus pengumuman ini?')" class="btn btn-danger btn-sm rounded-circle shadow-sm ms-1" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Belum ada pengumuman display masjid yang tersimpan.
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <footer class="text-center py-4 mt-auto text-secondary small">
        © <?= date('Y'); ?> <strong>Masjid Al-Ikhlas</strong> – Admin Panel
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
