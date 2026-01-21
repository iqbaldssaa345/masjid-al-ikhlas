<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("location:../login.php");
    exit;
}

/* TAMBAH DATA */
if(isset($_POST['tambah'])){
    mysqli_query($koneksi,"INSERT INTO keuangan 
        (tanggal,jenis,keterangan,jumlah,user_id) VALUES (
        '$_POST[tanggal]',
        '$_POST[jenis]',
        '$_POST[keterangan]',
        '$_POST[jumlah]',
        '$_SESSION[id]'
    )");
    header("location:dana.php");
}

/* EDIT DATA */
if(isset($_POST['edit'])){
    mysqli_query($koneksi,"UPDATE keuangan SET
        tanggal='$_POST[tanggal]',
        jenis='$_POST[jenis]',
        keterangan='$_POST[keterangan]',
        jumlah='$_POST[jumlah]'
        WHERE id='$_POST[id]'
    ");
    header("location:dana.php");
}

/* HAPUS DATA */
if(isset($_GET['hapus'])){
    mysqli_query($koneksi,"DELETE FROM keuangan WHERE id='$_GET[hapus]'");
    header("location:dana.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Dana Masjid</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
.card{border-radius:18px}
</style>
</head>

<body class="bg-light">

<div class="container py-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">💰 Data Dana Masjid</h4>
    <div>
        <a href="index.php" class="btn btn-secondary btn-sm">
            ⬅ Dashboard
        </a>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#tambah">
            <i class="bi bi-plus-circle"></i> Tambah
        </button>
    </div>
</div>

<!-- TABEL -->
<div class="card shadow">
<div class="card-body">

<table class="table table-bordered table-hover align-middle">
<thead class="table-success text-center">
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Jenis</th>
    <th>Keterangan</th>
    <th>Jumlah</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$no=1;
$q = mysqli_query($koneksi,"SELECT * FROM keuangan ORDER BY tanggal DESC");
while($d=mysqli_fetch_assoc($q)){
?>
<tr>
    <td class="text-center"><?= $no++ ?></td>
    <td><?= $d['tanggal'] ?></td>
    <td>
        <span class="badge <?= $d['jenis']=='infaq'?'bg-success':'bg-primary' ?>">
            <?= strtoupper($d['jenis']) ?>
        </span>
    </td>
    <td><?= htmlspecialchars($d['keterangan']) ?></td>
    <td class="text-end fw-bold">
        Rp <?= number_format($d['jumlah'],0,',','.') ?>
    </td>
    <td class="text-center">
        <button class="btn btn-warning btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#edit<?= $d['id'] ?>">
            <i class="bi bi-pencil"></i>
        </button>
        <a href="?hapus=<?= $d['id'] ?>"
           onclick="return confirm('Hapus data ini?')"
           class="btn btn-danger btn-sm">
            <i class="bi bi-trash"></i>
        </a>
    </td>
</tr>

<!-- MODAL EDIT -->
<div class="modal fade" id="edit<?= $d['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">
<form method="post">

<div class="modal-header bg-warning">
    <h5 class="modal-title">Edit Dana</h5>
</div>

<div class="modal-body">
    <input type="hidden" name="id" value="<?= $d['id'] ?>">

    <div class="mb-2">
        <label>Tanggal</label>
        <input type="date" name="tanggal" class="form-control" value="<?= $d['tanggal'] ?>" required>
    </div>

    <div class="mb-2">
        <label>Jenis</label>
        <select name="jenis" class="form-control">
            <option value="infaq" <?= $d['jenis']=='infaq'?'selected':'' ?>>Infaq</option>
            <option value="dkm" <?= $d['jenis']=='dkm'?'selected':'' ?>>DKM</option>
        </select>
    </div>

    <div class="mb-2">
        <label>Keterangan</label>
        <input type="text" name="keterangan" class="form-control" value="<?= $d['keterangan'] ?>" required>
    </div>

    <div class="mb-2">
        <label>Jumlah</label>
        <input type="number" name="jumlah" class="form-control" value="<?= $d['jumlah'] ?>" required>
    </div>
</div>

<div class="modal-footer">
    <button name="edit" class="btn btn-warning w-100">Simpan Perubahan</button>
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

<!-- MODAL TAMBAH -->
<div class="modal fade" id="tambah">
<div class="modal-dialog">
<div class="modal-content">
<form method="post">

<div class="modal-header bg-success text-white">
    <h5 class="modal-title">Tambah Dana</h5>
</div>

<div class="modal-body">

    <div class="mb-2">
        <label>Tanggal</label>
        <input type="date" name="tanggal" class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Jenis</label>
        <select name="jenis" class="form-control">
            <option value="infaq">Infaq</option>
            <option value="dkm">DKM</option>
        </select>
    </div>

    <div class="mb-2">
        <label>Keterangan</label>
        <input type="text" name="keterangan" class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Jumlah</label>
        <input type="number" name="jumlah" class="form-control" required>
    </div>

</div>

<div class="modal-footer">
    <button name="tambah" class="btn btn-success w-100">Simpan</button>
</div>

</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
