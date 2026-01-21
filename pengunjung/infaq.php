<?php
session_start();
include "../config/koneksi.php";

/* ===============================
   SIMPAN INFAQ
================================*/
if(isset($_POST['simpan'])){
    $tanggal    = $_POST['tanggal'];
    $keterangan = mysqli_real_escape_string($koneksi,$_POST['keterangan']);
    $jumlah     = (int)$_POST['jumlah'];
    $user_id    = $_SESSION['user_id'] ?? 0;

    mysqli_query($koneksi,"
        INSERT INTO keuangan (tanggal,jenis,keterangan,jumlah,user_id)
        VALUES ('$tanggal','infaq','$keterangan','$jumlah','$user_id')
    ");

    header("Location: infaq.php");
    exit;
}

/* ===============================
   HAPUS INFAQ
================================*/
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM keuangan WHERE id='$id' AND jenis='infaq'");
    header("Location: infaq.php");
    exit;
}

/* ===============================
   DATA & RINGKASAN
================================*/
$data = mysqli_query($koneksi,"
    SELECT * FROM keuangan
    WHERE jenis='infaq'
    ORDER BY tanggal DESC
");

$total = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) AS total FROM keuangan WHERE jenis='infaq'")
)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Infaq Masjid</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:Poppins,sans-serif;
    background:linear-gradient(180deg,#f8fafc,#eef2f7);
}
.navbar{
    background:linear-gradient(135deg,#198754,#0d6efd);
}
.card{
    border:none;
    border-radius:22px;
}
.money{
    font-size:26px;
    font-weight:700;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark shadow">
<div class="container">
    <span class="navbar-brand fw-bold">
        💚 Infaq Jamaah
    </span>
    <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill">
        <i class="bi bi-house"></i> Beranda
    </a>
</div>
</nav>

<div class="container py-5">

<!-- RINGKASAN -->
<div class="card shadow-sm mb-4">
<div class="card-body text-center">
    <small class="text-muted">Total Infaq Terkumpul</small>
    <div class="money text-success">
        Rp <?= number_format($total,0,',','.'); ?>
    </div>
</div>
</div>

<!-- FORM INPUT -->
<div class="card shadow-sm mb-4">
<div class="card-body">
<h6 class="fw-bold mb-3">
    <i class="bi bi-plus-circle"></i> Tambah Infaq
</h6>

<form method="post">
<div class="row g-3">
    <div class="col-md-3">
        <input type="date" name="tanggal" class="form-control" required>
    </div>
    <div class="col-md-5">
        <input type="text" name="keterangan" class="form-control" placeholder="Keterangan infaq" required>
    </div>
    <div class="col-md-3">
        <input type="number" name="jumlah" class="form-control" placeholder="Nominal (Rp)" required>
    </div>
    <div class="col-md-1 d-grid">
        <button name="simpan" class="btn btn-success">
            <i class="bi bi-save"></i>
        </button>
    </div>
</div>
</form>
</div>
</div>

<!-- TABEL -->
<div class="card shadow-sm">
<div class="card-body">

<h6 class="fw-bold mb-3">
    <i class="bi bi-cash-stack"></i> Riwayat Infaq
</h6>

<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
    <th>Tanggal</th>
    <th>Keterangan</th>
    <th class="text-end">Jumlah</th>
    <th class="text-center">Aksi</th>
</tr>
</thead>
<tbody>

<?php if(mysqli_num_rows($data)>0){ ?>
<?php while($d=mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= date('d-m-Y',strtotime($d['tanggal'])); ?></td>
<td><?= htmlspecialchars($d['keterangan']); ?></td>
<td class="text-end text-success fw-semibold">
    Rp <?= number_format($d['jumlah'],0,',','.'); ?>
</td>
<td class="text-center">
    <a href="?hapus=<?= $d['id']; ?>"
       onclick="return confirm('Hapus data infaq?')"
       class="btn btn-danger btn-sm rounded-pill">
       <i class="bi bi-trash"></i>
    </a>
</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td colspan="4" class="text-center text-muted">
    Belum ada data infaq
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
© <?= date('Y'); ?> Masjid • Transparansi Infaq Jamaah
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
