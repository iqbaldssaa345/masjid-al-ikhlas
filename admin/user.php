<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("location:../login.php");
    exit;
}

/* TAMBAH USER */
if(isset($_POST['tambah'])){
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $user = mysqli_real_escape_string($koneksi, $_POST['username']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    mysqli_query($koneksi,"INSERT INTO users (nama,username,password,role) VALUES (
        '$nama',
        '$user',
        '$pass',
        '$role'
    )");
    $_SESSION['pesan'] = "User baru berhasil ditambahkan!";
    header("location:user.php");
    exit;
}

/* EDIT USER */
if(isset($_POST['edit'])){
    $id   = (int)$_POST['id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $user = mysqli_real_escape_string($koneksi, $_POST['username']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    if($_POST['password']==""){
        mysqli_query($koneksi,"UPDATE users SET
            nama='$nama',
            username='$user',
            role='$role'
            WHERE id='$id'
        ");
    }else{
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($koneksi,"UPDATE users SET
            nama='$nama',
            username='$user',
            password='$pass',
            role='$role'
            WHERE id='$id'
        ");
    }
    $_SESSION['pesan'] = "Data user berhasil diperbarui!";
    header("location:user.php");
    exit;
}

/* HAPUS USER */
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM users WHERE id='$id'");
    $_SESSION['pesan'] = "User berhasil dihapus!";
    header("location:user.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User | Admin Panel</title>

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

        .modal-content-custom {
            border-radius: 24px;
            border: none;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm mb-4 py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                👥 Manajemen User | Admin Panel
            </span>
            <div class="text-white d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <div class="clock-badge d-flex align-items-center gap-2 me-2">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span id="adminClock" class="text-warning">--:--:-- WIB</span>
                </div>
                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <button class="btn btn-warning btn-sm text-dark fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#tambah">
                    <i class="bi bi-person-plus me-1"></i> Tambah User
                </button>
            </div>
        </div>
    </nav>

    <div class="container pb-5">

        <!-- HEADER TITLE WITH BISMILLAH -->
        <div class="text-center mb-4">
            <div class="bismillah-header">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
            <h4 class="fw-bold text-dark m-0">Pengelolaan Akun Pengguna System</h4>
            <p class="text-muted small">Atur hak akses akun Administrator, Petugas Operasional, dan Jamaah Pengunjung</p>
        </div>

        <!-- NOTIFIKASI -->
        <?php if(isset($_SESSION['pesan'])){ ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['pesan']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['pesan']); ?>
        <?php } ?>

        <!-- TABEL USER -->
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h5 class="fw-bold text-dark m-0">
                        <i class="bi bi-people-fill text-success me-2"></i> Daftar Pengguna Terdaftar
                    </h5>
                    <p class="text-muted small mb-0">Kelola kredensial akun dan peranan di sistem masjid</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th width="60" class="text-center">No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th class="text-center">Role / Hak Akses</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $q = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role ASC, id DESC");
                        if(mysqli_num_rows($q) > 0){
                            while($d = mysqli_fetch_assoc($q)){
                        ?>
                        <tr>
                            <td class="text-center fw-bold text-secondary"><?= $no++ ?></td>
                            <td class="fw-bold text-dark">
                                <i class="bi bi-person-circle text-success me-2"></i><?= htmlspecialchars($d['nama']) ?>
                            </td>
                            <td class="fw-semibold text-secondary">@<?= htmlspecialchars($d['username']) ?></td>
                            <td class="text-center">
                                <?php if($d['role']=='admin'){ ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1.5 fw-bold">ADMINISTRATOR</span>
                                <?php }elseif($d['role']=='petugas'){ ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold">PETUGAS</span>
                                <?php }else{ ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-bold">JAMAAH / PENGUNJUNG</span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm rounded-circle shadow-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#edit<?= $d['id'] ?>" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="?hapus=<?= $d['id'] ?>"
                                   onclick="return confirm('Apakah Anda yakin menghapus pengguna ini?')"
                                   class="btn btn-danger btn-sm rounded-circle shadow-sm ms-1" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- MODAL EDIT USER -->
                        <div class="modal fade" id="edit<?= $d['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content modal-content-custom">
                                    <form method="post">
                                        <div class="modal-header bg-warning text-dark">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-pencil-square me-1"></i> Edit Data User
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-secondary">Nama Lengkap</label>
                                                <input type="text" name="nama" class="form-control rounded-3" value="<?= htmlspecialchars($d['nama']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-secondary">Username</label>
                                                <input type="text" name="username" class="form-control rounded-3" value="<?= htmlspecialchars($d['username']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-secondary">Password Baru (Kosongkan jika tidak diubah)</label>
                                                <input type="password" name="password" class="form-control rounded-3" placeholder="Ganti password jika perlu">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-secondary">Role / Hak Akses</label>
                                                <select name="role" class="form-select rounded-3">
                                                    <option value="admin" <?= $d['role']=='admin'?'selected':'' ?>>Admin</option>
                                                    <option value="petugas" <?= $d['role']=='petugas'?'selected':'' ?>>Petugas</option>
                                                    <option value="pengunjung" <?= $d['role']=='pengunjung'?'selected':'' ?>>Jamaah / Pengunjung</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="edit" class="btn btn-warning rounded-pill px-4 fw-bold">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <?php 
                            }
                        }else{
                        ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada user tersimpan.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- MODAL TAMBAH USER -->
    <div class="modal fade" id="tambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <form method="post">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-person-plus me-1"></i> Tambah User Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control rounded-3" placeholder="Masukkan nama pengguna" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Username</label>
                            <input type="text" name="username" class="form-control rounded-3" placeholder="Masukkan username login" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Password</label>
                            <input type="password" name="password" class="form-control rounded-3" placeholder="Minimal 5 karakter" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Role / Hak Akses</label>
                            <select name="role" class="form-select rounded-3">
                                <option value="pengunjung">Jamaah / Pengunjung</option>
                                <option value="petugas">Petugas Operasional</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-success rounded-pill px-4 fw-bold">Simpan User</button>
                    </div>
                </form>
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
