<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "pengunjung"){
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$alert = "";

/* =====================
   SIMPAN LAPORAN BARU
==================== */
if(isset($_POST['kirim'])){
    $judul = trim($_POST['judul']);
    $isi   = trim($_POST['isi']);

    if($judul=="" || $isi==""){
        $alert = "<div class='alert alert-danger alert-dismissible fade show rounded-4 mb-3' role='alert'><i class='bi bi-exclamation-triangle-fill me-2'></i> Judul dan isi laporan wajib diisi.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }else{
        mysqli_query($koneksi,"
            INSERT INTO laporan (id_user, judul, isi)
            VALUES ('$id_user',
                    '".mysqli_real_escape_string($koneksi,$judul)."',
                    '".mysqli_real_escape_string($koneksi,$isi)."')
        ");
        $alert = "<div class='alert alert-success alert-dismissible fade show rounded-4 mb-3' role='alert'><i class='bi bi-check-circle-fill me-2'></i> Jazakallah! Laporan &amp; aspirasi Anda berhasil dikirim ke DKM.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

/* =====================
   DATA LAPORAN USER
==================== */
$data = mysqli_query($koneksi,"
    SELECT * FROM laporan
    WHERE id_user='$id_user'
    ORDER BY tanggal DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan &amp; Aspirasi Jamaah | Masjid Al-Ikhlas</title>

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

        .status-baru{ background:#6c757d; color:#fff; }
        .status-diproses{ background:#ffc107; color:#000; }
        .status-selesai{ background:#198754; color:#fff; }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm mb-4 py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                💬 Laporan &amp; Aspirasi Jamaah | Masjid Al-Ikhlas
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
            <h4 class="fw-bold text-dark m-0">Pusat Aspirasi &amp; Laporan Jamaah</h4>
            <p class="text-muted small">Sampaikan masukan perbaikan fasilitas, kebersihan, atau saran kegiatan ke DKM</p>
        </div>

        <div class="row g-4">
            
            <!-- FORM BUAT LAPORAN -->
            <div class="col-lg-5">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-pencil-square text-success me-2"></i> Buat Laporan Baru
                    </h5>

                    <?= $alert ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Judul Laporan / Topik</label>
                            <input type="text" name="judul" class="form-control rounded-3" placeholder="Contoh: Perbaikan AC Ruang Utama" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Rincian Laporan / Aspirasi</label>
                            <textarea name="isi" rows="5" class="form-control rounded-3" placeholder="Tuliskan masukan atau rincian laporan secara detail..." required></textarea>
                        </div>

                        <button name="kirim" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">
                            <i class="bi bi-send-fill me-1"></i> Kirim Laporan ke DKM
                        </button>
                    </form>
                </div>
            </div>

            <!-- TABEL RIWAYAT LAPORAN SAYA -->
            <div class="col-lg-7">
                <div class="card card-custom p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-dark m-0">
                            <i class="bi bi-clock-history text-primary me-2"></i> Riwayat Laporan Saya
                        </h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th width="140">Tanggal</th>
                                    <th>Judul &amp; Detail Laporan</th>
                                    <th width="100" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($data) > 0){ ?>
                                    <?php while($d = mysqli_fetch_assoc($data)){ ?>
                                    <tr>
                                        <td>
                                            <small class="fw-semibold text-secondary">
                                                <?= date('d/m/Y H:i', strtotime($d['tanggal'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <strong class="text-dark d-block mb-1"><?= htmlspecialchars($d['judul']) ?></strong>
                                            <small class="text-muted"><?= htmlspecialchars($d['isi']) ?></small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill px-3 py-1.5 fw-bold <?= $d['status']=="baru" ? "status-baru" : ($d['status']=="diproses" ? "status-diproses" : "status-selesai") ?>">
                                                <?= ucfirst($d['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Belum ada riwayat laporan tersimpan.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- FOOTER -->
    <footer class="text-center py-4 mt-auto text-secondary small">
        © <?= date('Y'); ?> <strong>Masjid Al-Ikhlas</strong> – Laporan &amp; Aspirasi Jamaah
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
