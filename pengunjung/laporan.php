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
===================== */
if(isset($_POST['kirim'])){
    $judul = trim($_POST['judul']);
    $isi   = trim($_POST['isi']);

    if($judul=="" || $isi==""){
        $alert = "<div class='alert alert-danger'>Judul dan isi wajib diisi.</div>";
    }else{
        mysqli_query($koneksi,"
            INSERT INTO laporan (id_user, judul, isi)
            VALUES ('$id_user',
                    '".mysqli_real_escape_string($koneksi,$judul)."',
                    '".mysqli_real_escape_string($koneksi,$isi)."')
        ");
        $alert = "<div class='alert alert-success'>Laporan berhasil dikirim.</div>";
    }
}

/* =====================
   DATA LAPORAN USER
===================== */
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
<title>Laporan Pengunjung</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:#f4f6f9;
}
.navbar{
    background:linear-gradient(135deg,#0d6efd,#198754);
}
.card{
    border:none;
    border-radius:22px;
}
.status-baru{background:#6c757d;}
.status-diproses{background:#ffc107;color:#000;}
.status-selesai{background:#198754;}
</style>
</head>

<body>

<nav class="navbar navbar-dark shadow">
<div class="container">
    <span class="navbar-brand fw-bold">
        <i class="bi bi-megaphone"></i> Laporan Pengunjung
    </span>
    <a href="index.php" class="btn btn-light btn-sm rounded-pill">
        <i class="bi bi-house"></i> Beranda
    </a>
</div>
</nav>

<div class="container py-5">

<!-- FORM -->
<div class="card shadow-sm mb-4">
<div class="card-body p-4">

<h5 class="fw-bold mb-3">
    <i class="bi bi-pencil-square"></i> Buat Laporan Baru
</h5>

<?= $alert ?>

<form method="post">
<div class="mb-3">
    <label class="form-label fw-semibold">Judul Laporan</label>
    <input type="text" name="judul" class="form-control rounded-4"
           placeholder="Contoh: Lampu Jalan Mati">
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Isi Laporan</label>
    <textarea name="isi" rows="4" class="form-control rounded-4"
              placeholder="Tuliskan laporan secara detail..."></textarea>
</div>

<button name="kirim" class="btn btn-success rounded-pill px-4">
    <i class="bi bi-send"></i> Kirim
</button>
</form>

</div>
</div>

<!-- RIWAYAT -->
<div class="card shadow-sm">
<div class="card-body p-4">

<h5 class="fw-bold mb-3">
    <i class="bi bi-clock-history"></i> Riwayat Laporan Saya
</h5>

<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
    <th>Tanggal</th>
    <th>Laporan</th>
    <th>Status</th>
</tr>
</thead>
<tbody>

<?php if(mysqli_num_rows($data)>0){ ?>
<?php while($d=mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= date('d/m/Y H:i',strtotime($d['tanggal'])) ?></td>
<td>
    <strong><?= htmlspecialchars($d['judul']) ?></strong><br>
    <small class="text-muted"><?= htmlspecialchars($d['isi']) ?></small>
</td>
<td>
<span class="badge rounded-pill
<?=
$d['status']=="baru" ? "status-baru" :
($d['status']=="diproses" ? "status-diproses" : "status-selesai")
?>">
<?= ucfirst($d['status']) ?>
</span>
</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td colspan="3" class="text-center text-muted">
    Belum ada laporan
</td>
</tr>
<?php } ?>

</tbody>
</table>
</div>

</div>
</div>

</div>

<footer class="text-center py-4 text-muted">
© <?= date('Y') ?> Sistem Laporan Kelurahan
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
