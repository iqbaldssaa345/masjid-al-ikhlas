<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}

$id_user = $_SESSION['id'];

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
<title>Laporan Saya</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(180deg,#f8fafc,#eef2f7);
}
.navbar{
    background:linear-gradient(135deg,#198754,#0d6efd);
}
.card{
    border:none;
    border-radius:22px;
    transition:.35s ease;
}
.card:hover{
    transform:translateY(-6px);
    box-shadow:0 25px 50px rgba(0,0,0,.15);
}
.badge-baru{background:#6c757d}
.badge-diproses{background:#ffc107;color:#000}
.badge-selesai{background:#198754}
.laporan-isi{
    max-height:110px;
    overflow:auto;
    line-height:1.7;
}
.modal-content{
    border-radius:24px;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark shadow-sm">
<div class="container-fluid px-4">
    <span class="navbar-brand fw-bold">
        📝 Laporan Jamaah
    </span>
    <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>
</nav>

<div class="container py-5">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Laporan Saya</h4>
        <p class="text-muted mb-0">
            Kelola dan pantau status laporan yang Anda kirimkan
        </p>
    </div>
    <button class="btn btn-success rounded-pill px-4 shadow-sm"
        data-bs-toggle="modal" data-bs-target="#tambah">
        <i class="bi bi-plus-circle"></i> Buat Laporan
    </button>
</div>

<div class="row g-4">

<?php if(mysqli_num_rows($data)>0){ ?>
<?php while($d=mysqli_fetch_assoc($data)){ ?>
<div class="col-md-6 col-lg-4">
<div class="card h-100 p-4">

<div class="d-flex justify-content-between align-items-start mb-2">
    <h6 class="fw-bold mb-0">
        <?= htmlspecialchars($d['judul']); ?>
    </h6>
    <span class="badge 
        <?= $d['status']=='baru'?'badge-baru':
            ($d['status']=='diproses'?'badge-diproses':'badge-selesai'); ?>">
        <i class="bi bi-info-circle"></i>
        <?= strtoupper($d['status']); ?>
    </span>
</div>

<div class="laporan-isi text-muted small mb-3">
<?= nl2br(htmlspecialchars($d['isi'])); ?>
</div>

<div class="d-flex justify-content-between align-items-center mt-auto">
    <small class="text-muted">
        <i class="bi bi-clock"></i>
        <?= date('d M Y H:i',strtotime($d['tanggal'])); ?>
    </small>

    <?php if($d['status']=='baru'){ ?>
    <div>
        <button class="btn btn-warning btn-sm rounded-circle"
            data-bs-toggle="modal"
            data-bs-target="#edit<?= $d['id']; ?>">
            <i class="bi bi-pencil"></i>
        </button>

        <a href="laporan_hapus.php?id=<?= $d['id']; ?>"
        onclick="return confirm('Hapus laporan ini?')"
        class="btn btn-danger btn-sm rounded-circle">
            <i class="bi bi-trash"></i>
        </a>
    </div>
    <?php } ?>
</div>

</div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="edit<?= $d['id']; ?>" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<form action="laporan_update.php" method="post">
<input type="hidden" name="id" value="<?= $d['id']; ?>">

<div class="modal-header">
<h5 class="modal-title fw-bold">✏ Edit Laporan</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<div class="mb-3">
<label class="form-label fw-semibold">Judul</label>
<input type="text" name="judul" class="form-control" required
value="<?= htmlspecialchars($d['judul']); ?>">
</div>

<div class="mb-3">
<label class="form-label fw-semibold">Isi Laporan</label>
<textarea name="isi" class="form-control" rows="5" required><?= htmlspecialchars($d['isi']); ?></textarea>
</div>
</div>

<div class="modal-footer">
<button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
<button class="btn btn-primary rounded-pill px-4">Simpan</button>
</div>
</form>

</div>
</div>
</div>

<?php } ?>
<?php } else { ?>

<div class="col-12">
<div class="alert alert-warning text-center rounded-4 shadow-sm">
    <i class="bi bi-info-circle"></i>
    Anda belum membuat laporan
</div>
</div>

<?php } ?>

</div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="tambah" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<form action="laporan_simpan.php" method="post">
<div class="modal-header">
<h5 class="modal-title fw-bold">📝 Buat Laporan Baru</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<div class="mb-3">
<label class="form-label fw-semibold">Judul</label>
<input type="text" name="judul" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label fw-semibold">Isi Laporan</label>
<textarea name="isi" class="form-control" rows="5" required></textarea>
</div>
</div>

<div class="modal-footer">
<button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
<button class="btn btn-success rounded-pill px-4">Kirim</button>
</div>
</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
