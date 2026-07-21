<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}

// MIGRATION ALTER TABLE
@mysqli_query($koneksi, "ALTER TABLE `keuangan` ADD COLUMN `metode_bayar` varchar(50) NOT NULL DEFAULT 'Cash'");
@mysqli_query($koneksi, "ALTER TABLE `keuangan` ADD COLUMN `bukti_bayar` varchar(255) DEFAULT NULL");

// DIREKTORI UPLOAD
$uploadDir = "../uploads/bukti/";
if (!file_exists($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

/* =====================
   SIMPAN DATA
==================== */
if(isset($_POST['simpan'])){
    $tanggal      = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $jenis        = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $keterangan   = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $metode_bayar = mysqli_real_escape_string($koneksi, $_POST['metode_bayar']);
    $jumlah       = (int)$_POST['jumlah'];
    $user_id      = $_SESSION['user_id'];

    $bukti_bayar = NULL;
    if(isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0){
        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $fileName = "dana_" . time() . "_" . rand(100, 999) . "." . strtolower($ext);
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        if(in_array(strtolower($ext), $allowed)){
            if(move_uploaded_file($_FILES['bukti']['tmp_name'], $uploadDir . $fileName)){
                $bukti_bayar = $fileName;
            }
        }
    }

    $buktiSql = $bukti_bayar ? "'$bukti_bayar'" : "NULL";

    mysqli_query($koneksi, "
        INSERT INTO keuangan (tanggal, jenis, keterangan, jumlah, user_id, metode_bayar, bukti_bayar)
        VALUES ('$tanggal', '$jenis', '$keterangan', '$jumlah', '$user_id', '$metode_bayar', $buktiSql)
    ");
    $_SESSION['pesan'] = "Transaksi kas berhasil disimpan!";
    header("location:dana.php");
    exit;
}

/* =====================
   UPDATE DATA
==================== */
if(isset($_POST['update'])){
    $id           = (int)$_POST['id'];
    $tanggal      = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $jenis        = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $keterangan   = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $metode_bayar = mysqli_real_escape_string($koneksi, $_POST['metode_bayar']);
    $jumlah       = (int)$_POST['jumlah'];

    $queryBukti = "";
    if(isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0){
        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $fileName = "dana_" . time() . "_" . rand(100, 999) . "." . strtolower($ext);
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        if(in_array(strtolower($ext), $allowed)){
            if(move_uploaded_file($_FILES['bukti']['tmp_name'], $uploadDir . $fileName)){
                $queryBukti = ", bukti_bayar='$fileName'";
            }
        }
    }

    mysqli_query($koneksi, "
        UPDATE keuangan SET
        tanggal='$tanggal',
        jenis='$jenis',
        keterangan='$keterangan',
        metode_bayar='$metode_bayar',
        jumlah='$jumlah'
        $queryBukti
        WHERE id='$id'
    ");
    $_SESSION['pesan'] = "Transaksi kas berhasil diperbarui!";
    header("location:dana.php");
    exit;
}

/* =====================
   HAPUS
==================== */
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM keuangan WHERE id='$id'");
    $_SESSION['pesan'] = "Data transaksi kas berhasil dihapus!";
    header("location:dana.php");
    exit;
}

/* =====================
   EDIT LOAD
==================== */
$edit = false;
if(isset($_GET['edit'])){
    $edit = true;
    $dEdit = mysqli_fetch_assoc(
        mysqli_query($koneksi,"SELECT * FROM keuangan WHERE id=".(int)$_GET['edit'])
    );
}

/* =====================
   HITUNG SALDO
==================== */
$masuk = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis='infaq'")
)['total'] ?? 0;

$keluar = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis IN ('dkm', 'pengeluaran')")
)['total'] ?? 0;

$saldo = $masuk - $keluar;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dana Kas Masjid | Petugas Al-Ikhlas</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f6f9;
            min-height: 100vh;
            color: #212529;
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

        .card-custom {
            background: #ffffff;
            border-radius: 22px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .saldo-banner {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            border-radius: 22px;
            color: #ffffff;
            padding: 26px 28px;
            box-shadow: 0 12px 30px rgba(25, 135, 84, 0.2);
            position: relative;
            overflow: hidden;
        }

        .form-label-custom {
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-control-custom, .form-select-custom {
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            padding: 10px 14px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15);
        }

        .table-custom th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px 16px;
        }

        .table-custom td {
            padding: 14px 16px;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .badge-infaq {
            background: rgba(25, 135, 84, 0.12);
            color: #198754;
            border: 1px solid rgba(25, 135, 84, 0.3);
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-size: 0.8rem;
            line-height: 1;
            white-space: nowrap;
        }

        .badge-dkm {
            background: rgba(220, 53, 69, 0.12);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-size: 0.8rem;
            line-height: 1;
            white-space: nowrap;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                💰 Kelola Dana Kas Masjid | Petugas DKM
            </span>
            <div class="text-white d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <div class="clock-badge d-flex align-items-center gap-2 me-2">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span id="petugasClock" class="text-warning">--:--:-- WIB</span>
                </div>
                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTAINER MAIN -->
    <div class="container py-4">

        <!-- NOTIFIKASI SUKSES -->
        <?php if(isset($_SESSION['pesan'])){ ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['pesan']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['pesan']); ?>
        <?php } ?>

        <!-- BANNER AKUMULASI SALDO KAS -->
        <div class="saldo-banner mb-4">
            <div class="row align-items-center">
                <div class="col-md-4 mb-3 mb-md-0">
                    <small class="opacity-75 text-uppercase fw-bold" style="letter-spacing:0.5px;">TOTAL PEMASUKAN INFAQ</small>
                    <h3 class="fw-bold m-0">Rp <?= number_format($masuk,0,',','.') ?></h3>
                </div>
                <div class="col-md-4 mb-3 mb-md-0 border-start border-white border-opacity-25 ps-md-4">
                    <small class="opacity-75 text-uppercase fw-bold" style="letter-spacing:0.5px;">TOTAL PENGELUARAN DKM</small>
                    <h3 class="fw-bold m-0">Rp <?= number_format($keluar,0,',','.') ?></h3>
                </div>
                <div class="col-md-4 border-start border-white border-opacity-25 ps-md-4">
                    <small class="text-warning text-uppercase fw-bold" style="letter-spacing:0.5px;">SALDO BUKUKAS SAAT INI</small>
                    <h2 class="fw-extrabold text-warning m-0">Rp <?= number_format($saldo,0,',','.') ?></h2>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <!-- FORM INPUT & EDIT DANA WITH BUKTI BAYAR -->
            <div class="col-lg-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-<?= $edit?'pencil-square text-warning':'plus-circle-fill text-success' ?> me-2"></i>
                        <?= $edit ? 'Edit Data Keuangan' : 'Catat Transaksi Kas' ?>
                    </h5>

                    <form method="post" enctype="multipart/form-data">
                        <?php if($edit){ ?>
                            <input type="hidden" name="id" value="<?= $dEdit['id'] ?>">
                        <?php } ?>

                        <div class="mb-3">
                            <label class="form-label-custom">Tanggal Transaksi</label>
                            <input type="date" name="tanggal" class="form-control form-control-custom" value="<?= $edit ? $dEdit['tanggal'] : date('Y-m-d') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Jenis Transaksi</label>
                            <select name="jenis" class="form-select form-select-custom fw-bold" required>
                                <option value="infaq" <?= $edit && $dEdit['jenis']=='infaq' ? 'selected':'' ?>>💚 Pemasukan (Infaq Jamaah)</option>
                                <option value="dkm" <?= $edit && ($dEdit['jenis']=='dkm'||$dEdit['jenis']=='pengeluaran') ? 'selected':'' ?>>🔴 Pengeluaran (Operasional DKM)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Metode Pembayaran</label>
                            <select name="metode_bayar" class="form-select form-select-custom fw-bold text-primary" required>
                                <option value="Tunai" <?= $edit && ($dEdit['metode_bayar']??'')=='Tunai' ? 'selected':'' ?>>Tunai / Cash</option>
                                <option value="BSI Transfer" <?= $edit && ($dEdit['metode_bayar']??'')=='BSI Transfer' ? 'selected':'' ?>>BSI (Bank Syariah Indonesia)</option>
                                <option value="BCA Transfer" <?= $edit && ($dEdit['metode_bayar']??'')=='BCA Transfer' ? 'selected':'' ?>>Bank BCA</option>
                                <option value="Mandiri Transfer" <?= $edit && ($dEdit['metode_bayar']??'')=='Mandiri Transfer' ? 'selected':'' ?>>Bank Mandiri</option>
                                <option value="BRI Transfer" <?= $edit && ($dEdit['metode_bayar']??'')=='BRI Transfer' ? 'selected':'' ?>>Bank BRI</option>
                                <option value="QRIS Scan" <?= $edit && ($dEdit['metode_bayar']??'')=='QRIS Scan' ? 'selected':'' ?>>QRIS Scan Multi-Payment</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Keterangan / Deskripsi</label>
                            <input type="text" name="keterangan" class="form-control form-control-custom" value="<?= $edit ? htmlspecialchars($dEdit['keterangan']) : '' ?>" placeholder="Contoh: Infaq Jumat / Listrik Masjid" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Nominal (Rp)</label>
                            <input type="number" name="jumlah" class="form-control form-control-custom fw-bold text-success" value="<?= $edit ? $dEdit['jumlah'] : '' ?>" placeholder="Contoh: 150000" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Upload Bukti Transaksi / Resi (Opsional)</label>
                            <input type="file" name="bukti" class="form-control form-control-custom" accept="image/*,.pdf">
                            <small class="text-muted" style="font-size:0.75rem;">Format: JPG, PNG, PDF (Maks 5MB)</small>
                        </div>

                        <div class="d-grid gap-2">
                            <?php if($edit){ ?>
                                <button type="submit" name="update" class="btn btn-warning fw-bold py-2 rounded-pill shadow-sm text-dark">
                                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                                </button>
                                <a href="dana.php" class="btn btn-outline-secondary rounded-pill py-2 text-center">Batal Edit</a>
                            <?php }else{ ?>
                                <button type="submit" name="simpan" class="btn btn-success fw-bold py-2 rounded-pill shadow-sm">
                                    <i class="bi bi-save-fill me-1"></i> Simpan Transaksi Kas
                                </button>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABEL KAS DANA & BUKTI BAYAR -->
            <div class="col-lg-8">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-list-columns-reverse text-primary me-2"></i> Riwayat Transaksi Dana Kas Masjid
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">NO</th>
                                    <th>TANGGAL</th>
                                    <th>KETERANGAN</th>
                                    <th class="text-center">JENIS</th>
                                    <th>METODE</th>
                                    <th class="text-end">JUMLAH</th>
                                    <th class="text-center">BUKTI</th>
                                    <th class="text-center" style="width: 90px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q = mysqli_query($koneksi,"SELECT * FROM keuangan ORDER BY tanggal DESC, id DESC");
                                if(mysqli_num_rows($q) == 0){
                                    echo '<tr><td colspan="8" class="text-center text-muted py-4">Belum ada catatan transaksi kas</td></tr>';
                                }
                                while($d = mysqli_fetch_assoc($q)){
                                ?>
                                <tr>
                                    <td class="text-center fw-bold text-secondary"><?= $no++ ?></td>
                                    <td class="fw-semibold text-dark"><?= date('d/m/Y',strtotime($d['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($d['keterangan']) ?></td>
                                    <td class="text-center">
                                        <?php if($d['jenis']=='infaq'){ ?>
                                            <span class="badge-infaq"><i class="bi bi-arrow-down-left"></i> PEMASUKAN</span>
                                        <?php }else{ ?>
                                            <span class="badge-dkm"><i class="bi bi-arrow-up-right"></i> PENGELUARAN</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1 small fw-bold">
                                            <?= htmlspecialchars($d['metode_bayar'] ?? 'Cash') ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold <?= $d['jenis']=='infaq'?'text-success':'text-danger' ?>">
                                        Rp <?= number_format($d['jumlah'],0,',','.') ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if(!empty($d['bukti_bayar'])){ ?>
                                            <button class="btn btn-outline-success btn-sm rounded-circle p-1" style="width:32px; height:32px;"
                                                    onclick="viewBukti('../uploads/bukti/<?= $d['bukti_bayar'] ?>')" title="Lihat Resi / Bukti Transfer">
                                                <i class="bi bi-file-earmark-image"></i>
                                            </button>
                                        <?php } else { ?>
                                            <span class="text-muted small">-</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="?edit=<?= $d['id'] ?>" class="btn btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?hapus=<?= $d['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data transaksi ini?')" class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
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

    <!-- MODAL PREVIEW BUKTI PEMBAYARAN -->
    <div class="modal fade" id="modalBukti" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content rounded-4 border-0 p-3">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold text-dark">Resi / Bukti Pembayaran Transaksi Kas</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <img id="imgBukti" src="" alt="Bukti Pembayaran" class="img-fluid rounded-3 shadow border">
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="text-center py-4 mt-auto text-secondary small">
        © <?= date('Y'); ?> <strong>Masjid Al-Ikhlas</strong> – Sistem Pengelolaan Kas &amp; Transparansi Dana Petugas
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- REAL-TIME CLOCK & BUKTI MODAL SCRIPT -->
    <script>
        function updatePetugasClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockElem = document.getElementById("petugasClock");
            if (clockElem) {
                clockElem.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updatePetugasClock, 1000);
        updatePetugasClock();

        function viewBukti(imgUrl) {
            document.getElementById('imgBukti').src = imgUrl;
            const modalBukti = new bootstrap.Modal(document.getElementById('modalBukti'));
            modalBukti.show();
        }
    </script>
</body>
</html>
