<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}

/* =====================
   SIMPAN DATA
===================== */
if(isset($_POST['simpan'])){
    mysqli_query($koneksi,"
        INSERT INTO keuangan
        (tanggal,jenis,keterangan,jumlah,user_id)
        VALUES (
            '$_POST[tanggal]',
            '$_POST[jenis]',
            '".mysqli_real_escape_string($koneksi,$_POST['keterangan'])."',
            ".(int)$_POST['jumlah'].",
            ".$_SESSION['id']."
        )
    ");
    header("location:dana.php");
}

/* =====================
   UPDATE DATA
===================== */
if(isset($_POST['update'])){
    mysqli_query($koneksi,"
        UPDATE keuangan SET
        tanggal='$_POST[tanggal]',
        jenis='$_POST[jenis]',
        keterangan='".mysqli_real_escape_string($koneksi,$_POST['keterangan'])."',
        jumlah=".(int)$_POST['jumlah']."
        WHERE id=".(int)$_POST['id']
    );
    header("location:dana.php");
}

/* =====================
   HAPUS
===================== */
if(isset($_GET['hapus'])){
    mysqli_query($koneksi,"DELETE FROM keuangan WHERE id=".(int)$_GET['hapus']);
    header("location:dana.php");
}

/* =====================
   EDIT
===================== */
$edit=false;
if(isset($_GET['edit'])){
    $edit=true;
    $dEdit=mysqli_fetch_assoc(
        mysqli_query($koneksi,"SELECT * FROM keuangan WHERE id=".(int)$_GET['edit'])
    );
}

/* =====================
   HITUNG SALDO (AKURAT)
===================== */
$masuk = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis='infaq'")
)['total'] ?? 0;

$keluar = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis='dkm'")
)['total'] ?? 0;

$saldo = $masuk - $keluar;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dana Masjid | Petugas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{background:#f4f6f9;font-family:'Poppins',sans-serif}
.card{border-radius:18px}
</style>
</head>

<body>

<nav class="navbar navbar-dark bg-success shadow">
<div class="container-fluid">
<span class="navbar-brand fw-bold">💰 Dana Masjid | Petugas</span>
<a href="../logout.php" class="btn btn-outline-light btn-sm">
<i class="bi bi-box-arrow-right"></i> Logout
</a>
</div>
</nav>

<div class="container py-4">

<a href="index.php" class="btn btn-secondary mb-3">
<i class="bi bi-arrow-left"></i> Dashboard
</a>

<div class="alert alert-success text-center fw-bold fs-5">
SALDO KAS MASJID : Rp <?= number_format($saldo,0,',','.') ?>
</div>

<div class="row g-4">

<!-- FORM -->
<div class="col-md-4">
<div class="card shadow">
<div class="card-body">

<h5 class="fw-bold mb-3"><?= $edit?'✏ Edit Dana':'➕ Tambah Dana' ?></h5>

<form method="post">
<?php if($edit){ ?>
<input type="hidden" name="id" value="<?= $dEdit['id'] ?>">
<?php } ?>

<label>Tanggal</label>
<input type="date" name="tanggal" class="form-control mb-2"
value="<?= $edit?$dEdit['tanggal']:date('Y-m-d') ?>" required>

<label>Keterangan</label>
<input type="text" name="keterangan" class="form-control mb-2"
value="<?= $edit?htmlspecialchars($dEdit['keterangan']):'' ?>" required>

<label>Jenis</label>
<select name="jenis" class="form-select mb-2">
<option value="infaq" <?= $edit && $dEdit['jenis']=='infaq'?'selected':'' ?>>Pemasukan (Infaq)</option>
<option value="dkm" <?= $edit && $dEdit['jenis']=='dkm'?'selected':'' ?>>Pengeluaran (DKM)</option>
</select>

<label>Jumlah</label>
<input type="number" name="jumlah" class="form-control mb-3"
value="<?= $edit?$dEdit['jumlah']:'' ?>" required>

<?php if($edit){ ?>
<button name="update" class="btn btn-warning w-100">Update</button>
<a href="dana.php" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
<?php }else{ ?>
<button name="simpan" class="btn btn-success w-100">Simpan</button>
<?php } ?>
</form>

</div>
</div>
</div>

<!-- TABEL -->
<div class="col-md-8">
<div class="card shadow">
<div class="card-body">

<h5 class="fw-bold mb-3">📊 Riwayat Keuangan</h5>

<table class="table table-bordered table-hover">
<thead class="table-success">
<tr>
<th>No</th>
<th>Tanggal</th>
<th>Keterangan</th>
<th>Jenis</th>
<th>Jumlah</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$no=1;
$q=mysqli_query($koneksi,"SELECT * FROM keuangan ORDER BY tanggal DESC");
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
<td>
<a href="?edit=<?= $d['id'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
<a href="?hapus=<?= $d['id'] ?>" onclick="return confirm('Hapus data?')" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
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

</body>
</html>
