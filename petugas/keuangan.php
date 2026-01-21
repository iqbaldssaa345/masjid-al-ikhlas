<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || !in_array($_SESSION['role'],['admin','petugas'])){
    header("location:../login.php");
    exit;
}

/* =====================
   RINGKASAN KEUANGAN
===================== */
$infaq = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis='infaq'")
)['total'] ?? 0;

$keluar = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis='pengeluaran'")
)['total'] ?? 0;

$saldo = $infaq - $keluar;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Keuangan Masjid</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{background:#f4f6f9;font-family:'Poppins',sans-serif}
.card{border-radius:18px}
.stat i{font-size:34px}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-primary shadow">
<div class="container-fluid">
<span class="navbar-brand fw-bold">📊 Keuangan Masjid Al-Ikhlas</span>
<a href="../logout.php" class="btn btn-outline-light btn-sm">
<i class="bi bi-box-arrow-right"></i> Logout
</a>
</div>
</nav>

<div class="container py-4">

<!-- NAVIGATION -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="index.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>

    <a href="laporan.php" class="btn btn-warning fw-bold">
        <i class="bi bi-chat-dots"></i> Laporan Jamaah
    </a>
</div>

<!-- ================= RINGKASAN ================= -->
<div class="row g-4 mb-4">

<div class="col-md-4">
<div class="card shadow stat text-center p-4">
<i class="bi bi-arrow-down-circle text-success"></i>
<h6 class="mt-2">Total Pemasukan</h6>
<h5 class="fw-bold text-success">
Rp <?= number_format($infaq,0,',','.') ?>
</h5>
</div>
</div>

<div class="col-md-4">
<div class="card shadow stat text-center p-4">
<i class="bi bi-arrow-up-circle text-danger"></i>
<h6 class="mt-2">Total Pengeluaran</h6>
<h5 class="fw-bold text-danger">
Rp <?= number_format($keluar,0,',','.') ?>
</h5>
</div>
</div>

<div class="col-md-4">
<div class="card shadow stat text-center p-4">
<i class="bi bi-wallet2 text-primary"></i>
<h6 class="mt-2">Saldo Akhir</h6>
<h5 class="fw-bold text-primary">
Rp <?= number_format($saldo,0,',','.') ?>
</h5>
</div>
</div>

</div>

<!-- ================= TABEL ================= -->
<div class="card shadow">
<div class="card-body">

<h5 class="fw-bold mb-3">📑 Detail Transaksi Keuangan</h5>

<table class="table table-bordered table-hover align-middle">
<thead class="table-primary">
<tr>
<th>No</th>
<th>Tanggal</th>
<th>Keterangan</th>
<th>Jenis</th>
<th>Jumlah</th>
<th>Petugas</th>
</tr>
</thead>
<tbody>

<?php
$no=1;
$q=mysqli_query($koneksi,"
    SELECT keuangan.*, users.nama
    FROM keuangan
    LEFT JOIN users ON keuangan.user_id = users.id
    ORDER BY tanggal DESC
");

while($d=mysqli_fetch_assoc($q)){
?>
<tr>
<td><?= $no++ ?></td>
<td><?= date('d-m-Y',strtotime($d['tanggal'])) ?></td>
<td><?= htmlspecialchars($d['keterangan']) ?></td>
<td>
<span class="badge bg-<?= $d['jenis']=='infaq'?'success':'danger' ?>">
<?= strtoupper($d['jenis']) ?>
</span>
</td>
<td>Rp <?= number_format($d['jumlah'],0,',','.') ?></td>
<td><?= htmlspecialchars($d['nama'] ?? '-') ?></td>
</tr>
<?php } ?>

</tbody>
</table>

</div>
</div>

</div>

<footer class="text-center py-3 text-muted">
© <?= date('Y') ?> Masjid Al-Ikhlas | Sistem Keuangan
</footer>

</body>
</html>
