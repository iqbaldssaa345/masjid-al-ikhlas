<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}

$dari   = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$jenis  = $_GET['jenis'] ?? '';

$where = "WHERE tanggal BETWEEN '$dari' AND '$sampai'";
if($jenis!='') $where .= " AND jenis='$jenis'";

$q = mysqli_query($koneksi,"
    SELECT keuangan.*, users.nama 
    FROM keuangan 
    LEFT JOIN users ON keuangan.user_id=users.id
    $where
    ORDER BY tanggal ASC
");

$masuk = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis='infaq' AND tanggal BETWEEN '$dari' AND '$sampai'")
)['total'] ?? 0;

$keluar = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis='dkm' AND tanggal BETWEEN '$dari' AND '$sampai'")
)['total'] ?? 0;

$saldo = $masuk - $keluar;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Keuangan Masjid</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{font-family:'Poppins',sans-serif;background:#f4f6f9}
.card{border:none;border-radius:22px}
.stat-card{
    color:#fff;
    border-radius:24px;
    padding:24px;
}
.stat-card i{font-size:34px;opacity:.9}
.gradient-green{background:linear-gradient(135deg,#198754,#28a745)}
.gradient-red{background:linear-gradient(135deg,#dc3545,#fd7e14)}
.gradient-blue{background:linear-gradient(135deg,#0d6efd,#6610f2)}
.table th{text-align:center}
@media print{
    .no-print{display:none}
    body{background:#fff}
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-primary shadow no-print">
<div class="container-fluid px-4">
<span class="navbar-brand fw-bold">📑 Laporan Keuangan Masjid</span>
<a href="index.php" class="btn btn-outline-light btn-sm rounded-pill">
<i class="bi bi-arrow-left"></i> Dashboard
</a>
</div>
</nav>

<div class="container py-4">

<!-- FILTER -->
<div class="card shadow-sm mb-4 no-print">
<div class="card-body">
<form class="row g-3 align-items-end">

<div class="col-md-3">
<label class="fw-semibold">Dari Tanggal</label>
<input type="date" name="dari" value="<?= $dari ?>" class="form-control">
</div>

<div class="col-md-3">
<label class="fw-semibold">Sampai</label>
<input type="date" name="sampai" value="<?= $sampai ?>" class="form-control">
</div>

<div class="col-md-3">
<label class="fw-semibold">Jenis</label>
<select name="jenis" class="form-select">
<option value="">Semua</option>
<option value="infaq" <?= $jenis=='infaq'?'selected':'' ?>>Infaq</option>
<option value="dkm" <?= $jenis=='dkm'?'selected':'' ?>>Pengeluaran (DKM)</option>
</select>
</div>

<div class="col-md-3 d-grid gap-2">
<button class="btn btn-primary">
<i class="bi bi-search"></i> Tampilkan
</button>
<button type="button" onclick="window.print()" class="btn btn-success">
<i class="bi bi-printer"></i> Cetak Laporan
</button>
</div>

</form>
</div>
</div>

<!-- STATISTIK -->
<div class="row g-4 mb-4">

<div class="col-md-4">
<div class="stat-card gradient-green shadow">
<i class="bi bi-arrow-down-circle"></i>
<h6 class="mt-3">Total Pemasukan</h6>
<h4 class="fw-bold">Rp <?= number_format($masuk,0,',','.') ?></h4>
</div>
</div>

<div class="col-md-4">
<div class="stat-card gradient-red shadow">
<i class="bi bi-arrow-up-circle"></i>
<h6 class="mt-3">Total Pengeluaran</h6>
<h4 class="fw-bold">Rp <?= number_format($keluar,0,',','.') ?></h4>
</div>
</div>

<div class="col-md-4">
<div class="stat-card gradient-blue shadow">
<i class="bi bi-wallet2"></i>
<h6 class="mt-3">Saldo Akhir</h6>
<h4 class="fw-bold">Rp <?= number_format($saldo,0,',','.') ?></h4>
</div>
</div>

</div>

<!-- TABEL -->
<div class="card shadow-sm">
<div class="card-body">

<!-- HEADER CETAK -->
<div class="text-center mb-4">
<h5 class="fw-bold mb-1">LAPORAN KEUANGAN MASJID AL-IKHLAS</h5>
<small class="text-muted">
Periode <?= date('d-m-Y',strtotime($dari)) ?> s/d <?= date('d-m-Y',strtotime($sampai)) ?>
</small>
<hr>
</div>

<div class="table-responsive">
<table class="table table-bordered table-striped align-middle">

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
if(mysqli_num_rows($q)==0){
    echo "<tr><td colspan='6' class='text-center text-muted'>Data tidak tersedia</td></tr>";
}
while($d=mysqli_fetch_assoc($q)){
?>
<tr>
<td class="text-center"><?= $no++ ?></td>
<td><?= date('d-m-Y',strtotime($d['tanggal'])) ?></td>
<td><?= htmlspecialchars($d['keterangan']) ?></td>
<td class="text-center">
<span class="badge bg-<?= $d['jenis']=='infaq'?'success':'danger' ?>">
<?= strtoupper($d['jenis']) ?>
</span>
</td>
<td class="text-end fw-semibold">Rp <?= number_format($d['jumlah'],0,',','.') ?></td>
<td><?= htmlspecialchars($d['nama'] ?? '-') ?></td>
</tr>
<?php } ?>
</tbody>

<tfoot class="table-light fw-bold">
<tr>
<td colspan="4" class="text-end">TOTAL PEMASUKAN</td>
<td class="text-end text-success">Rp <?= number_format($masuk,0,',','.') ?></td>
<td></td>
</tr>
<tr>
<td colspan="4" class="text-end">TOTAL PENGELUARAN</td>
<td class="text-end text-danger">Rp <?= number_format($keluar,0,',','.') ?></td>
<td></td>
</tr>
<tr>
<td colspan="4" class="text-end">SALDO AKHIR</td>
<td class="text-end text-primary">Rp <?= number_format($saldo,0,',','.') ?></td>
<td></td>
</tr>
</tfoot>

</table>
</div>

</div>
</div>

</div>

<footer class="text-center py-3 text-muted">
© <?= date('Y') ?> Masjid Al-Ikhlas • Sistem Keuangan Masjid
</footer>

</body>
</html>
