<?php
session_start();
include "../config/koneksi.php";

// CEK LOGIN
if(!isset($_SESSION['user_id']) || $_SESSION['role']!="pengunjung"){
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$pesan = "";

/* =======================
   SIMPAN LAPORAN
======================= */
if(isset($_POST['kirim'])){
    $judul = mysqli_real_escape_string($koneksi,$_POST['judul']);
    $isi   = mysqli_real_escape_string($koneksi,$_POST['isi']);

    if($judul=="" || $isi==""){
        $pesan = "Judul dan isi laporan wajib diisi!";
    }else{
        mysqli_query($koneksi,"
            INSERT INTO laporan (id_user, judul, isi, status)
            VALUES ('$id_user','$judul','$isi','baru')
        ");
        header("Location: laporan.php");
        exit;
    }
}

/* =======================
   DATA LAPORAN USER
======================= */
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
    background:linear-gradient(180deg,#f8fafc,#eef2f7);
}
.navbar{
    background:linear-gradient(135deg,#0d6efd,#198754);
}
.card{
    border:none;
    border-radius:22px;
}
.badge{
    border-radius:12px;
    padding:6px 12px;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark shadow">
<div class="container">
    <span class="navbar-brand fw-bold">
        📝 Laporan Pengunjung
    </span>
    <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill">
        <i class="bi bi-house"></i> Beranda
    </a>
</div>
</nav>

<div class="container py-5">

<!-- FORM LAPORAN -->
<div class="card shadow-sm mb-4">
<div class="card-body">

<h5 class="fw-bold mb-3">
    <i class="bi bi-pencil-square"></i> Kirim Laporan
</h5>

<?php if($pesan!=""){ ?>
<div class="alert alert-danger"><?= $pesan ?></div>
<?php } ?>

<form method="post">
<div class="mb-3">
    <label class="form-label">Judul Laporan</label>
    <input type="text" name="judul" class="form-control"
           placeholder="Contoh: Lampu Masjid Mati" required>
</div>

<div class="mb-3">
    <label class="form-label">Isi Laporan</label>
    <textarea name="isi" rows="4" class="form-control"
              placeholder="Jelaskan laporan secara lengkap..." required></textarea>
</div>

<button name="kirim" class="btn btn-success rounded-pill px-4">
    <i class="bi bi-send"></i> Kirim Laporan
</button>
</form>

</div>
</div>

<!-- RIWAYAT LAPORAN -->
<div class="card shadow-sm">
<div class="card-body">

<h5 class="fw-bold mb-3">
    <i class="bi bi-list-check"></i> Riwayat Laporan Saya
</h5>

<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
    <th>Tanggal</th>
    <th>Judul</th>
    <th>Status</th>
</tr>
</thead>
<tbody>

<?php if(mysqli_num_rows($data)>0){ ?>
<?php while($d=mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= date('d-m-Y H:i',strtotime($d['tanggal'])) ?></td>
<td>
    <strong><?= htmlspecialchars($d['judul']) ?></strong><br>
    <small class="text-muted"><?= htmlspecialchars($d['isi']) ?></small>
</td>
<td>
<?php
if($d['status']=="baru"){
    echo '<span class="badge bg-secondary">Baru</span>';
}elseif($d['status']=="diproses"){
    echo '<span class="badge bg-warning text-dark">Diproses</span>';
}else{
    echo '<span class="badge bg-success">Selesai</span>';
}
?>
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
© <?= date('Y') ?> Sistem Laporan Pengunjung
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
