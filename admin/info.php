<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("location:../login.php");
    exit;
}

// TAMBAH DATA
if(isset($_POST['simpan'])){
    $judul = mysqli_real_escape_string($koneksi,$_POST['judul']);
    $isi   = mysqli_real_escape_string($koneksi,$_POST['isi']);

    mysqli_query($koneksi,"
        INSERT INTO info_masjid (judul, isi)
        VALUES ('$judul','$isi')
    ");
    header("location:info.php");
}

// HAPUS DATA
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM info_masjid WHERE id='$id'");
    header("location:info.php");
}

// EDIT DATA
$edit = false;
if(isset($_GET['edit'])){
    $edit = true;
    $id = (int)$_GET['edit'];
    $qEdit = mysqli_query($koneksi,"SELECT * FROM info_masjid WHERE id='$id'");
    $dEdit = mysqli_fetch_assoc($qEdit);
}

// UPDATE DATA
if(isset($_POST['update'])){
    $id    = (int)$_POST['id'];
    $judul = mysqli_real_escape_string($koneksi,$_POST['judul']);
    $isi   = mysqli_real_escape_string($koneksi,$_POST['isi']);

    mysqli_query($koneksi,"
        UPDATE info_masjid SET
        judul='$judul',
        isi='$isi'
        WHERE id='$id'
    ");
    header("location:info.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Info Masjid | Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{background:#f4f6f9}
.card{border-radius:15px}
</style>
</head>

<body>

<div class="container py-4">

<a href="index.php" class="btn btn-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Kembali
</a>

<div class="row g-4">

<!-- FORM -->
<div class="col-md-4">
<div class="card shadow">
<div class="card-body">
<h5 class="fw-bold mb-3">
    <?= $edit ? "Edit Info Masjid" : "Tambah Info Masjid" ?>
</h5>

<form method="post">
<?php if($edit){ ?>
    <input type="hidden" name="id" value="<?= $dEdit['id'] ?>">
<?php } ?>

<div class="mb-3">
    <label class="form-label">Judul</label>
    <input type="text" name="judul" class="form-control" required
           value="<?= $edit ? htmlspecialchars($dEdit['judul']) : '' ?>">
</div>

<div class="mb-3">
    <label class="form-label">Isi</label>
    <textarea name="isi" class="form-control" rows="5" required><?= $edit ? htmlspecialchars($dEdit['isi']) : '' ?></textarea>
</div>

<button class="btn btn-success w-100">
    <i class="bi bi-save"></i>
    <?= $edit ? "Update" : "Simpan" ?>
</button>

<?php if($edit){ ?>
<a href="info.php" class="btn btn-outline-secondary w-100 mt-2">
    Batal
</a>
<?php } ?>

<?php if(!$edit){ ?>
<input type="hidden" name="simpan">
<?php }else{ ?>
<input type="hidden" name="update">
<?php } ?>
</form>

</div>
</div>
</div>

<!-- TABEL -->
<div class="col-md-8">
<div class="card shadow">
<div class="card-body">

<h5 class="fw-bold mb-3">Data Info Masjid</h5>

<table class="table table-bordered table-hover align-middle">
<thead class="table-success">
<tr>
    <th width="50">No</th>
    <th>Judul</th>
    <th>Isi</th>
    <th width="120">Aksi</th>
</tr>
</thead>
<tbody>
<?php
$no=1;
$q = mysqli_query($koneksi,"SELECT * FROM info_masjid ORDER BY id ASC");
while($d=mysqli_fetch_assoc($q)){
?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($d['judul']) ?></td>
    <td><?= nl2br(htmlspecialchars($d['isi'])) ?></td>
    <td>
        <a href="?edit=<?= $d['id'] ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i>
        </a>
        <a href="?hapus=<?= $d['id'] ?>"
           onclick="return confirm('Hapus info ini?')"
           class="btn btn-danger btn-sm">
            <i class="bi bi-trash"></i>
        </a>
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
