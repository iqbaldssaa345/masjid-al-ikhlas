<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("location:../login.php");
    exit;
}

// TAMBAH DATA
if(isset($_POST['simpan'])){
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isi   = mysqli_real_escape_string($koneksi, $_POST['isi']);

    mysqli_query($koneksi, "
        INSERT INTO info_masjid (judul, isi)
        VALUES ('$judul','$isi')
    ");
    $_SESSION['pesan'] = "Info & Running Text berhasil ditambahkan!";
    header("location:info.php");
    exit;
}

// HAPUS DATA
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM info_masjid WHERE id='$id'");
    $_SESSION['pesan'] = "Info & Running Text berhasil dihapus!";
    header("location:info.php");
    exit;
}

// EDIT DATA
$edit = false;
if(isset($_GET['edit'])){
    $edit = true;
    $id = (int)$_GET['edit'];
    $qEdit = mysqli_query($koneksi, "SELECT * FROM info_masjid WHERE id='$id'");
    $dEdit = mysqli_fetch_assoc($qEdit);
}

// UPDATE DATA
if(isset($_POST['update'])){
    $id    = (int)$_POST['id'];
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isi   = mysqli_real_escape_string($koneksi, $_POST['isi']);

    mysqli_query($koneksi, "
        UPDATE info_masjid SET
        judul='$judul',
        isi='$isi'
        WHERE id='$id'
    ");
    $_SESSION['pesan'] = "Info & Running Text berhasil diperbarui!";
    header("location:info.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Info & Running Text | Admin Panel</title>

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
            font-size: 1.6rem;
            color: #198754;
            margin-bottom: 2px;
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
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm mb-4 py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                📢 Info & Running Text | Admin Panel
            </span>
            <div class="text-white d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <div class="clock-badge d-flex align-items-center gap-2 me-2">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span id="adminClock" class="text-warning">--:--:-- WIB</span>
                </div>
                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">

        <!-- HEADER TITLE WITH BISMILLAH -->
        <div class="text-center mb-4">
            <div class="bismillah-header">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
            <h4 class="fw-bold text-dark m-0">Pengumuman & Running Text Digital</h4>
            <p class="text-muted small">Kelola informasi publik yang tampil di beranda dan layar TV digital Masjid Al-Ikhlas</p>
        </div>

        <!-- NOTIFIKASI -->
        <?php if(isset($_SESSION['pesan'])){ ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['pesan']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['pesan']); ?>
        <?php } ?>

        <div class="row g-4">

            <!-- FORM INPUT -->
            <div class="col-lg-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <?= $edit ? '<i class="bi bi-pencil-square text-warning me-2"></i>Edit Informasi' : '<i class="bi bi-plus-circle-fill text-success me-2"></i>Tambah Informasi Baru' ?>
                    </h5>

                    <form method="post">
                        <?php if($edit){ ?>
                            <input type="hidden" name="id" value="<?= $dEdit['id'] ?>">
                        <?php } ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">JUDUL KATEGORI / TOPIK</label>
                            <input type="text" name="judul" class="form-control rounded-3"
                                   placeholder="Contoh: Jadwal Kajian / Sholat Jumat"
                                   value="<?= $edit ? htmlspecialchars($dEdit['judul']) : '' ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary">ISI PENGUMUMAN / RUNNING TEXT</label>
                            <textarea name="isi" class="form-control rounded-3" rows="5"
                                      placeholder="Tuliskan isi pesan pengumuman..." required><?= $edit ? htmlspecialchars($dEdit['isi']) : '' ?></textarea>
                        </div>

                        <?php if($edit){ ?>
                            <button type="submit" name="update" class="btn btn-warning w-100 fw-bold rounded-pill py-2 shadow-sm text-dark mb-2">
                                <i class="bi bi-check-lg me-1"></i> SIMPAN PERUBAHAN
                            </button>
                            <a href="info.php" class="btn btn-outline-secondary w-100 fw-semibold rounded-pill py-2">
                                Batal Edit
                            </a>
                        <?php }else{ ?>
                            <button type="submit" name="simpan" class="btn btn-success w-100 fw-bold rounded-pill py-2 shadow-sm">
                                <i class="bi bi-save me-1"></i> SIMPAN INFORMASI
                            </button>
                        <?php } ?>
                    </form>
                </div>
            </div>

            <!-- TABEL INFO -->
            <div class="col-lg-8">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-list-stars text-success me-2"></i>Daftar Informasi &amp; Running Text
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th width="50" class="text-center">No</th>
                                    <th width="200">Judul / Topik</th>
                                    <th>Isi Pengumuman / Running Text</th>
                                    <th width="100" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q = mysqli_query($koneksi, "SELECT * FROM info_masjid ORDER BY id DESC");
                                if(mysqli_num_rows($q) > 0){
                                    while($d = mysqli_fetch_assoc($q)){
                                ?>
                                <tr>
                                    <td class="text-center fw-bold text-secondary"><?= $no++ ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($d['judul']) ?></td>
                                    <td class="text-secondary small"><?= htmlspecialchars($d['isi']) ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="?edit=<?= $d['id'] ?>" class="btn btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?hapus=<?= $d['id'] ?>" onclick="return confirm('Apakah Anda yakin menghapus informasi ini?')" class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                }else{
                                ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data informasi masjid tersimpan</td>
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
    <footer class="text-center py-4 mt-auto text-muted small">
        © <?= date('Y'); ?> <strong>Masjid Al-Ikhlas</strong> – Admin Panel Digital
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