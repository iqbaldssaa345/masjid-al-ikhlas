<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}

/* FILTER */
$cari   = $_GET['cari'] ?? '';
$status = $_GET['status'] ?? '';

$where = "WHERE 1=1";
if($cari!=''){
    $where .= " AND (judul LIKE '%$cari%' OR isi LIKE '%$cari%')";
}
if($status!=''){
    $where .= " AND status='$status'";
}

/* CRUD */
if(isset($_POST['tambah'])){
    mysqli_query($koneksi,"INSERT INTO laporan (id_user,judul,isi,status)
    VALUES ('{$_POST['id_user']}','".mysqli_real_escape_string($koneksi,$_POST['judul'])."','".mysqli_real_escape_string($koneksi,$_POST['isi'])."','baru')");
    header("Location: laporan_jamaah.php"); exit;
}

if(isset($_POST['update'])){
    mysqli_query($koneksi,"UPDATE laporan SET 
    judul='".mysqli_real_escape_string($koneksi,$_POST['judul'])."',
    isi='".mysqli_real_escape_string($koneksi,$_POST['isi'])."',
    status='{$_POST['status']}'
    WHERE id='{$_POST['id']}'");
    header("Location: laporan_jamaah.php"); exit;
}

if(isset($_GET['hapus'])){
    mysqli_query($koneksi,"DELETE FROM laporan WHERE id='{$_GET['hapus']}'");
    header("Location: laporan_jamaah.php"); exit;
}

/* DATA */
$data = mysqli_query($koneksi,"
SELECT laporan.*, users.nama 
FROM laporan
LEFT JOIN users ON laporan.id_user=users.id
$where
ORDER BY tanggal DESC
");

/* STAT */
$total  = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) t FROM laporan"))['t'];
$baru   = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) t FROM laporan WHERE status='baru'"))['t'];
$proses = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) t FROM laporan WHERE status='diproses'"))['t'];
$selesai= mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) t FROM laporan WHERE status='selesai'"))['t'];

$users = mysqli_query($koneksi,"SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jamaah | Petugas Al-Ikhlas</title>

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
            border-radius: 24px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .stat-card {
            border-radius: 20px;
            padding: 20px;
            color: #ffffff;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            transition: transform 0.25s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-blue { background: linear-gradient(135deg, #0d6efd, #0b5ed7); }
        .stat-slate { background: linear-gradient(135deg, #64748b, #475569); }
        .stat-amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-green { background: linear-gradient(135deg, #198754, #157347); }

        .table-custom th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.85rem;
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

        .badge-status-baru {
            background: rgba(100, 116, 139, 0.12);
            color: #475569;
            border: 1px solid rgba(100, 116, 139, 0.25);
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .badge-status-diproses {
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.25);
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .badge-status-selesai {
            background: rgba(25, 135, 84, 0.12);
            color: #198754;
            border: 1px solid rgba(25, 135, 84, 0.25);
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .modal-content-custom {
            border-radius: 24px;
            border: none;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .modal-header-custom {
            background: linear-gradient(135deg, #198754, #20c997);
            color: #ffffff;
            padding: 18px 24px;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                📢 Laporan Jamaah | Petugas Panel
            </span>

            <div class="text-white d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <div class="clock-badge d-flex align-items-center gap-2 me-2">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span id="petugasClock" class="text-warning">--:--:-- WIB</span>
                </div>

                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <button class="btn btn-light text-success btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#tambah">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Laporan
                </button>
            </div>
        </div>
    </nav>

    <div class="container py-4">

        <!-- STAT CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card stat-blue">
                    <small class="text-white-50 fw-bold">TOTAL LAPORAN</small>
                    <h3 class="fw-bold m-0 mt-1"><?= number_format($total) ?></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card stat-slate">
                    <small class="text-white-50 fw-bold">BARU MASUK</small>
                    <h3 class="fw-bold m-0 mt-1"><?= number_format($baru) ?></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card stat-amber">
                    <small class="text-white-50 fw-bold">SEDANG DIPROSES</small>
                    <h3 class="fw-bold m-0 mt-1"><?= number_format($proses) ?></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card stat-green">
                    <small class="text-white-50 fw-bold">SELESAI</small>
                    <h3 class="fw-bold m-0 mt-1"><?= number_format($selesai) ?></h3>
                </div>
            </div>
        </div>

        <!-- FILTER CARD -->
        <div class="card card-custom p-3 mb-4">
            <div class="card-body p-2">
                <form class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-search"></i></span>
                            <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>" class="form-control border-start-0" placeholder="Cari judul / isi laporan jamaah...">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="baru" <?= $status=='baru'?'selected':'' ?>>Baru</option>
                            <option value="diproses" <?= $status=='diproses'?'selected':'' ?>>Diproses</option>
                            <option value="selesai" <?= $status=='selesai'?'selected':'' ?>>Selesai</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3">
                            Filter
                        </button>
                    </div>

                    <div class="col-md-2">
                        <a href="laporan_jamaah.php" class="btn btn-outline-secondary w-100 rounded-3">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABEL LAPORAN -->
        <div class="card card-custom">
            <div class="card-body p-4">

                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-list-stars text-primary me-2"></i>Daftar Laporan Jamaah
                </h5>

                <div class="table-responsive">
                    <table class="table table-hover table-custom align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">NO</th>
                                <th>JUDUL &amp; RINCIAN LAPORAN</th>
                                <th>JAMAAH (PELAPOR)</th>
                                <th class="text-center">STATUS</th>
                                <th class="text-center" style="width: 100px;">AKSI</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php 
                            $no=1; 
                            if(mysqli_num_rows($data)==0){
                                echo '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada data laporan ditemukan</td></tr>';
                            }
                            while($d=mysqli_fetch_assoc($data)){ 
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;"><?= htmlspecialchars($d['judul']) ?></div>
                                    <div class="text-secondary small line-clamp-2"><?= htmlspecialchars($d['isi']) ?></div>
                                    <small class="text-muted opacity-75 mt-1 d-block"><i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($d['tanggal'])) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-semibold">
                                        <i class="bi bi-person-circle text-primary me-1"></i><?= htmlspecialchars($d['nama'] ?? 'Anonim') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if($d['status']=='baru'){ ?>
                                        <span class="badge-status-baru">BARU</span>
                                    <?php }elseif($d['status']=='diproses'){ ?>
                                        <span class="badge-status-diproses">DIPROSES</span>
                                    <?php }else{ ?>
                                        <span class="badge-status-selesai">SELESAI</span>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>" title="Tanggapi / Edit Status">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="?hapus=<?= $d['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin menghapus laporan ini?')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL EDIT LAPORAN -->
                            <div class="modal fade" id="edit<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content modal-content-custom">
                                        <div class="modal-header modal-header-custom">
                                            <h6 class="modal-title fw-bold m-0"><i class="bi bi-pencil-square me-2"></i>Update Status Laporan</h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="post">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">

                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small text-secondary">JUDUL LAPORAN</label>
                                                    <input name="judul" class="form-control rounded-3 fw-semibold" value="<?= htmlspecialchars($d['judul']) ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small text-secondary">ISI / DETAIL LAPORAN</label>
                                                    <textarea name="isi" class="form-control rounded-3" rows="4" required><?= htmlspecialchars($d['isi']) ?></textarea>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label fw-bold small text-secondary">STATUS PROSES</label>
                                                    <select name="status" class="form-select rounded-3 fw-bold text-dark">
                                                        <option value="baru" <?= $d['status']=='baru'?'selected':'' ?>>Baru (Belum Ditindaklanjuti)</option>
                                                        <option value="diproses" <?= $d['status']=='diproses'?'selected':'' ?>>Diproses (Sedang Ditangani)</option>
                                                        <option value="selesai" <?= $d['status']=='selesai'?'selected':'' ?>>Selesai (Telah Ditindaklanjuti)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="modal-footer bg-light p-3">
                                                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="update" class="btn btn-success rounded-pill px-4 fw-bold">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    <!-- MODAL TAMBAH LAPORAN -->
    <div class="modal fade" id="tambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h6 class="modal-title fw-bold m-0"><i class="bi bi-plus-circle me-2"></i>Tambah Laporan Jamaah</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">PILIH JAMAAH / USER</label>
                            <select name="id_user" class="form-select rounded-3" required>
                                <option value="">-- Pilih Nama Jamaah --</option>
                                <?php 
                                mysqli_data_seek($users, 0);
                                while($u=mysqli_fetch_assoc($users)){ 
                                ?>
                                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama']) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">JUDUL LAPORAN</label>
                            <input name="judul" class="form-control rounded-3" placeholder="Masukkan judul masukan/laporan" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold small text-secondary">ISI LAPORAN</label>
                            <textarea name="isi" class="form-control rounded-3" rows="4" placeholder="Tuliskan isi laporan secara rinci" required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-success rounded-pill px-4 fw-bold">Simpan Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="text-center py-4 text-muted small">
        © <?= date('Y') ?> Masjid Al-Ikhlas • Manajamen Laporan Jamaah
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