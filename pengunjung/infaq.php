<?php
session_start();
include "../config/koneksi.php";

// ALTER TABLE MIGRATION AMAN
@mysqli_query($koneksi, "ALTER TABLE `keuangan` ADD COLUMN `metode_bayar` varchar(50) NOT NULL DEFAULT 'Cash'");
@mysqli_query($koneksi, "ALTER TABLE `keuangan` ADD COLUMN `bukti_bayar` varchar(255) DEFAULT NULL");
@mysqli_query($koneksi, "ALTER TABLE `keuangan` ADD COLUMN `status` varchar(50) NOT NULL DEFAULT 'Berhasil'");

// DIREKTORI UPLOAD
$uploadDir = "../uploads/bukti/";
if (!file_exists($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

/* ===============================
   SIMPAN INFAQ JAMAAH & UPLOAD BUKTI
================================*/
if(isset($_POST['simpan'])){
    $tanggal      = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $nama_donatur = mysqli_real_escape_string($koneksi, $_POST['nama_donatur']);
    $metode_bayar = mysqli_real_escape_string($koneksi, $_POST['metode_bayar']);
    $keterangan_input = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $jumlah       = (int)$_POST['jumlah'];
    $user_id      = $_SESSION['user_id'] ?? 0;

    $keterangan = $nama_donatur . " - " . $keterangan_input . " (" . $metode_bayar . ")";
    $bukti_bayar = NULL;

    // HANDLE FILE UPLOAD BUKTI BAYAR
    if(isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0){
        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $fileName = "infaq_" . time() . "_" . rand(100, 999) . "." . strtolower($ext);
        $targetFile = $uploadDir . $fileName;

        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        if(in_array(strtolower($ext), $allowed)){
            if(move_uploaded_file($_FILES['bukti']['tmp_name'], $targetFile)){
                $bukti_bayar = $fileName;
            }
        }
    }

    $buktiSql = $bukti_bayar ? "'$bukti_bayar'" : "NULL";

    mysqli_query($koneksi, "
        INSERT INTO keuangan (tanggal, jenis, keterangan, jumlah, user_id, metode_bayar, bukti_bayar, status)
        VALUES ('$tanggal', 'infaq', '$keterangan', '$jumlah', '$user_id', '$metode_bayar', $buktiSql, 'Berhasil')
    ");

    $_SESSION['pesan'] = "Jazakallah Khair! Infaq Anda berhasil dicatat secara transparan.";
    header("Location: infaq.php");
    exit;
}

/* ===============================
   HAPUS INFAQ SAYA
================================*/
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM keuangan WHERE id='$id' AND jenis='infaq'");
    $_SESSION['pesan'] = "Data infaq berhasil dihapus!";
    header("Location: infaq.php");
    exit;
}

/* ===============================
   RINGKASAN DATA INFAQ
================================*/
$data = mysqli_query($koneksi,"
    SELECT * FROM keuangan
    WHERE jenis='infaq'
    ORDER BY tanggal DESC, id DESC
");

$total = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) AS total FROM keuangan WHERE jenis='infaq'")
)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Infaq Bank &amp; QRIS Digital | Jamaah Masjid Al-Ikhlas</title>

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

        .bank-card {
            border: 1.5px solid rgba(25, 135, 84, 0.2);
            border-radius: 20px;
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            padding: 20px;
            transition: all 0.3s ease;
        }

        .bank-card:hover {
            transform: translateY(-4px);
            border-color: #198754;
            box-shadow: 0 12px 30px rgba(25, 135, 84, 0.12);
        }

        .bank-logo-badge {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
        }

        .qris-box {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: #ffffff;
            border-radius: 24px;
            padding: 24px;
            text-align: center;
        }

        .qris-code-img {
            width: 170px;
            height: 170px;
            background: #ffffff;
            padding: 10px;
            border-radius: 18px;
            margin: 12px auto;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm mb-4 py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                💚 Infaq Digital Jamaah | Masjid Al-Ikhlas
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
            <h4 class="fw-bold text-dark m-0">Infaq Digital Transfer Bank &amp; Scan QRIS</h4>
            <p class="text-muted small">Salurkan infaq terbaik Anda dengan aman, cepat, dan terverifikasi otomatis</p>
        </div>

        <!-- NOTIFIKASI SUKSES -->
        <?php if(isset($_SESSION['pesan'])){ ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['pesan']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['pesan']); ?>
        <?php } ?>

        <!-- RINGKASAN SALDO INFAQ -->
        <div class="card card-custom p-4 mb-4 text-center bg-white border border-success border-2">
            <div class="row align-items-center">
                <div class="col-md-8 text-md-start mb-3 mb-md-0">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-bold mb-1">
                        <i class="bi bi-wallet2 me-1"></i> Transparansi Kas Masjid
                    </span>
                    <h5 class="fw-bold text-dark m-0">Total Infaq Jamaah Terkumpul</h5>
                    <small class="text-muted">Diperbarui realtime untuk akuntabilitas operasional &amp; pembangunan DKM</small>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="fs-2 fw-extrabold text-success">
                        Rp <?= number_format($total, 0, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- METODE PEMBAYARAN: BANK & QRIS -->
        <div class="row g-4 mb-4">
            
            <!-- REKENING BANK SYARIAH & NASIONAL -->
            <div class="col-lg-8">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-bank text-success me-2"></i> Rekening Transfer Bank Resmi DKM
                    </h5>
                    
                    <div class="row g-3">
                        <!-- BSI -->
                        <div class="col-md-6">
                            <div class="bank-card">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bank-logo-badge bg-emerald text-white" style="background: #00a39e;">
                                        BSI
                                    </div>
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark">Bank Syariah Indonesia</h6>
                                        <small class="text-muted">Kode Bank: 451</small>
                                    </div>
                                </div>
                                <div class="bg-white p-2 px-3 rounded-3 border d-flex align-items-center justify-content-between">
                                    <span class="fw-extrabold text-dark" id="rekBsi">7123456789</span>
                                    <button onclick="copyToClipboard('7123456789', 'Rekening BSI')" class="btn btn-outline-success btn-sm rounded-pill py-0 px-2 fw-bold" style="font-size: 0.75rem;">
                                        <i class="bi bi-copy me-1"></i> Salin
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">a.n. <strong>DKM Masjid Al-Ikhlas</strong></small>
                            </div>
                        </div>

                        <!-- BCA -->
                        <div class="col-md-6">
                            <div class="bank-card">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bank-logo-badge text-white" style="background: #005caa;">
                                        BCA
                                    </div>
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark">Bank BCA</h6>
                                        <small class="text-muted">Kode Bank: 014</small>
                                    </div>
                                </div>
                                <div class="bg-white p-2 px-3 rounded-3 border d-flex align-items-center justify-content-between">
                                    <span class="fw-extrabold text-dark" id="rekBca">8400123456</span>
                                    <button onclick="copyToClipboard('8400123456', 'Rekening BCA')" class="btn btn-outline-primary btn-sm rounded-pill py-0 px-2 fw-bold" style="font-size: 0.75rem;">
                                        <i class="bi bi-copy me-1"></i> Salin
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">a.n. <strong>DKM Al-Ikhlas Digital</strong></small>
                            </div>
                        </div>

                        <!-- MANDIRI -->
                        <div class="col-md-6">
                            <div class="bank-card">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bank-logo-badge text-white" style="background: #003d79;">
                                        BM
                                    </div>
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark">Bank Mandiri</h6>
                                        <small class="text-muted">Kode Bank: 008</small>
                                    </div>
                                </div>
                                <div class="bg-white p-2 px-3 rounded-3 border d-flex align-items-center justify-content-between">
                                    <span class="fw-extrabold text-dark" id="rekMandiri">1330012345678</span>
                                    <button onclick="copyToClipboard('1330012345678', 'Rekening Mandiri')" class="btn btn-outline-primary btn-sm rounded-pill py-0 px-2 fw-bold" style="font-size: 0.75rem;">
                                        <i class="bi bi-copy me-1"></i> Salin
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">a.n. <strong>Masjid Al-Ikhlas</strong></small>
                            </div>
                        </div>

                        <!-- BRI -->
                        <div class="col-md-6">
                            <div class="bank-card">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bank-logo-badge text-white" style="background: #00529c;">
                                        BRI
                                    </div>
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark">Bank BRI</h6>
                                        <small class="text-muted">Kode Bank: 002</small>
                                    </div>
                                </div>
                                <div class="bg-white p-2 px-3 rounded-3 border d-flex align-items-center justify-content-between">
                                    <span class="fw-extrabold text-dark" id="rekBri">012301004567509</span>
                                    <button onclick="copyToClipboard('012301004567509', 'Rekening BRI')" class="btn btn-outline-primary btn-sm rounded-pill py-0 px-2 fw-bold" style="font-size: 0.75rem;">
                                        <i class="bi bi-copy me-1"></i> Salin
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">a.n. <strong>DKM Al-Ikhlas</strong></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SCAN QRIS DIGITAL MULTI-PAYMENT -->
            <div class="col-lg-4">
                <div class="qris-box h-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold mb-2">
                            <i class="bi bi-qr-code me-1"></i> QRIS Resmi Kemenag &amp; BI
                        </span>
                        <h5 class="fw-bold text-white mb-1">Scan QRIS Instant</h5>
                        <p class="small text-white-50 mb-2">Mendukung GoPay, OVO, DANA, LinkAja, ShopeePay &amp; Semua m-Banking</p>

                        <!-- BARCODE GENERATED QRIS IMAGE -->
                        <div class="qris-code-img">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=00020101021126580016ID.CO.QRIS.WWW01189360091400000000000215MASJID+AL-IKHLAS5204581253033605802ID5916MASJID+AL-IKHLAS6009CIBINONG6105169116304A1B2" alt="QRIS Masjid Al-Ikhlas" class="img-fluid rounded">
                        </div>
                        <small class="fw-bold text-warning d-block">NMID: ID1020045678901</small>
                    </div>

                    <button class="btn btn-light text-primary fw-bold rounded-pill w-100 mt-3" data-bs-toggle="modal" data-bs-target="#modalQris">
                        <i class="bi bi-arrows-fullscreen me-1"></i> Perbesar QRIS Code
                    </button>
                </div>
            </div>

        </div>

        <!-- ROW 3: FORM INPUT INFAQ & UPLOAD BUKTI -->
        <div class="row g-4 mb-4">
            
            <div class="col-lg-5">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-plus-circle-fill text-success me-2"></i> Form Infaq &amp; Upload Bukti Bayar
                    </h5>

                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Tanggal Infaq</label>
                            <input type="date" name="tanggal" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Nama Donatur / Hamba Allah</label>
                            <input type="text" name="nama_donatur" class="form-control rounded-3" value="<?= htmlspecialchars($_SESSION['nama']) ?>" placeholder="Contoh: Hamba Allah / Nama Anda" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Metode Pembayaran</label>
                            <select name="metode_bayar" class="form-select rounded-3 fw-bold text-primary" required>
                                <option value="BSI Transfer">BSI (Bank Syariah Indonesia)</option>
                                <option value="BCA Transfer">Bank BCA</option>
                                <option value="Mandiri Transfer">Bank Mandiri</option>
                                <option value="BRI Transfer">Bank BRI</option>
                                <option value="QRIS Scan">QRIS Digital (GoPay/OVO/DANA/m-Banking)</option>
                                <option value="Tunai Kotak Infaq">Tunai / Kotak Infaq Masjid</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Nominal Infaq (Rp)</label>
                            <input type="number" name="jumlah" class="form-control rounded-3 fw-bold text-success" placeholder="Contoh: 50000" min="1000" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Keterangan / Niat Infaq</label>
                            <input type="text" name="keterangan" class="form-control rounded-3" placeholder="Contoh: Infaq Pembangunan / Sedekah Jumat" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Upload Bukti Transfer / Resi (Opsional)</label>
                            <input type="file" name="bukti" class="form-control rounded-3" accept="image/*,.pdf">
                            <small class="text-muted" style="font-size: 0.75rem;">Format: JPG, PNG, PDF (Maks. 5MB)</small>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">
                            <i class="bi bi-send-fill me-1"></i> Kirim Infaq &amp; Konfirmasi
                        </button>
                    </form>
                </div>
            </div>

            <!-- TABEL RIWAYAT INFAQ JAMAAH -->
            <div class="col-lg-7">
                <div class="card card-custom p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-dark m-0">
                            <i class="bi bi-journal-text text-primary me-2"></i> Catatan Transparansi Infaq Jamaah
                        </h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th width="50" class="text-center">No</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan / Donatur</th>
                                    <th>Metode</th>
                                    <th class="text-end">Jumlah (Rp)</th>
                                    <th width="70" class="text-center">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if(mysqli_num_rows($data) > 0){
                                    while($d = mysqli_fetch_assoc($data)){
                                ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= $no++ ?></td>
                                    <td>
                                        <small class="fw-semibold text-secondary">
                                            <?= date('d/m/Y', strtotime($d['tanggal'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block"><?= htmlspecialchars($d['keterangan']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-bold">
                                            <?= htmlspecialchars($d['metode_bayar'] ?? 'Cash') ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-extrabold text-success">
                                        Rp <?= number_format($d['jumlah'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if(!empty($d['bukti_bayar'])){ ?>
                                            <button class="btn btn-outline-success btn-sm rounded-circle p-1" style="width:32px; height:32px;"
                                                    onclick="viewBukti('../uploads/bukti/<?= $d['bukti_bayar'] ?>')" title="Lihat Bukti">
                                                <i class="bi bi-image"></i>
                                            </button>
                                        <?php } else { ?>
                                            <span class="text-muted small">-</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat infaq jamaah yang tersimpan.</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- MODAL FULLSCREEN QRIS -->
    <div class="modal fade" id="modalQris" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content rounded-4 border-0 p-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark w-100 text-center">
                        <i class="bi bi-qr-code-scan text-primary me-2"></i> QRIS Resmi Masjid Al-Ikhlas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=00020101021126580016ID.CO.QRIS.WWW01189360091400000000000215MASJID+AL-IKHLAS5204581253033605802ID5916MASJID+AL-IKHLAS6009CIBINONG6105169116304A1B2" alt="QRIS Large" class="img-fluid rounded-4 shadow p-2 border mb-3">
                    <h6 class="fw-bold text-dark mb-1">DKM MASJID AL-IKHLAS</h6>
                    <small class="text-muted d-block">NMID: ID1020045678901 &bull; Cibinong, Kab. Bogor</small>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PREVIEW BUKTI TRANSFER -->
    <div class="modal fade" id="modalBukti" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content rounded-4 border-0 p-3">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold text-dark">Bukti Pembayaran / Transfer Infaq</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <img id="imgBukti" src="" alt="Bukti Transfer" class="img-fluid rounded-3 shadow border">
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="text-center py-4 mt-auto text-secondary small">
        © <?= date('Y'); ?> <strong>Masjid Al-Ikhlas</strong> – Transparansi Infaq Digital Jamaah
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS SCRIPT: REALTIME CLOCK, COPY NO REK, PREVIEW BUKTI -->
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

        function copyToClipboard(text, bankName) {
            navigator.clipboard.writeText(text).then(function() {
                alert(`Nomor Rekening ${bankName} (${text}) berhasil disalin!`);
            }, function(err) {
                console.error('Gagal menyalin text: ', err);
            });
        }

        function viewBukti(imgUrl) {
            document.getElementById('imgBukti').src = imgUrl;
            const modalBukti = new bootstrap.Modal(document.getElementById('modalBukti'));
            modalBukti.show();
        }
    </script>
</body>
</html>
