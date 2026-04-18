<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}

/* FILTER */
$cari   = $_GET['cari'] ?? '';
$status = $_GET['status'] ?? '';

$where = "WHERE 1=1";
if($cari!=''){
    $where .= " AND (judul LIKE '%$cari%' OR isi LIKE '%$cari%')";
}
if($status!=''){
    $where .= " AND status='$status'";
}

/* CRUD */
if(isset($_POST['tambah'])){
    mysqli_query($koneksi,"INSERT INTO laporan (id_user,judul,isi,status)
    VALUES ('{$_POST['id_user']}','{$_POST['judul']}','{$_POST['isi']}','baru')");
    header("Location: laporan_jamaah.php"); exit;
}

if(isset($_POST['update'])){
    mysqli_query($koneksi,"UPDATE laporan SET 
    judul='{$_POST['judul']}',
    isi='{$_POST['isi']}',
    status='{$_POST['status']}'
    WHERE id='{$_POST['id']}'");
    header("Location: laporan_jamaah.php"); exit;
}

if(isset($_GET['hapus'])){
    mysqli_query($koneksi,"DELETE FROM laporan WHERE id='{$_GET['hapus']}'");
    header("Location: laporan_jamaah.php"); exit;
}

/* DATA */
$data = mysqli_query($koneksi,"
SELECT laporan.*, users.nama 
FROM laporan
LEFT JOIN users ON laporan.id_user=users.id
$where
ORDER BY tanggal DESC
");

/* STAT */
$total = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) t FROM laporan"))['t'];
$baru  = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) t FROM laporan WHERE status='baru'"))['t'];
$proses= mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) t FROM laporan WHERE status='diproses'"))['t'];
$selesai= mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) t FROM laporan WHERE status='selesai'"))['t'];

$users = mysqli_query($koneksi,"SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Jamaah</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:linear-gradient(135deg,#eef2f7,#f8fafc);
    font-family:Poppins;
}

/* HEADER */
.header-box{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

/* CARD */
.card{
    border:none;
    border-radius:22px;
}

/* GLASS */
.glass{
    background:rgba(255,255,255,0.8);
    backdrop-filter:blur(10px);
}

/* STAT */
.stat{
    border-radius:18px;
    color:#fff;
    padding:18px;
    text-align:center;
    font-weight:600;
    transition:.3s;
}
.stat:hover{
    transform:translateY(-5px);
}

/* BADGE */
.badge{
    padding:6px 12px;
    border-radius:12px;
}

/* TABLE */
.table tr:hover{
    background:#f1f5ff;
    transition:.2s;
}

/* BUTTON */
.btn{
    border-radius:12px;
}
</style>
</head>

<body>

<div class="container py-4">

<!-- HEADER -->
<div class="header-box">
    <h4 class="fw-bold mb-0">📢 Laporan Jamaah</h4>

    <div class="d-flex gap-2">
        <!-- TOMBOL KEMBALI -->
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>

        <button class="btn btn-success"
            data-bs-toggle="modal" data-bs-target="#tambah">
            <i class="bi bi-plus"></i> Tambah
        </button>
    </div>
</div>

<!-- STAT -->
<div class="row g-3 mb-4">
<div class="col-md-3"><div class="stat bg-primary">Total<br><h4><?= $total ?></h4></div></div>
<div class="col-md-3"><div class="stat bg-secondary">Baru<br><h4><?= $baru ?></h4></div></div>
<div class="col-md-3"><div class="stat bg-warning text-dark">Diproses<br><h4><?= $proses ?></h4></div></div>
<div class="col-md-3"><div class="stat bg-success">Selesai<br><h4><?= $selesai ?></h4></div></div>
</div>

<!-- FILTER -->
<div class="card glass p-3 mb-3 shadow-sm">
<form class="row g-2">
<div class="col-md-4">
<input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>" class="form-control" placeholder="Cari laporan...">
</div>

<div class="col-md-3">
<select name="status" class="form-select">
<option value="">Semua Status</option>
<option value="baru" <?= $status=='baru'?'selected':'' ?>>Baru</option>
<option value="diproses" <?= $status=='diproses'?'selected':'' ?>>Diproses</option>
<option value="selesai" <?= $status=='selesai'?'selected':'' ?>>Selesai</option>
</select>
</div>

<div class="col-md-3">
<button class="btn btn-primary w-100">
<i class="bi bi-search"></i> Filter
</button>
</div>

<div class="col-md-2">
<a href="laporan_jamaah.php" class="btn btn-outline-secondary w-100">
Reset
</a>
</div>

</form>
</div>

<!-- TABEL -->
<div class="card shadow-sm">
<div class="card-body table-responsive">

<table class="table align-middle">
<thead>
<tr>
<th>No</th>
<th>Judul</th>
<th>User</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; while($d=mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= $no++ ?></td>

<td>
<b><?= htmlspecialchars($d['judul']) ?></b><br>
<small class="text-muted"><?= htmlspecialchars($d['isi']) ?></small>
</td>

<td><?= htmlspecialchars($d['nama'] ?? '-') ?></td>

<td>
<span class="badge bg-<?=
$d['status']=='baru'?'secondary':
($d['status']=='diproses'?'warning text-dark':'success')
?>">
<?= strtoupper($d['status']) ?>
</span>
</td>

<td>
<button class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#edit<?= $d['id'] ?>">
<i class="bi bi-pencil"></i>
</button>

<a href="?hapus=<?= $d['id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Yakin hapus?')">
<i class="bi bi-trash"></i>
</a>
</td>

</tr>

<!-- MODAL EDIT -->
<div class="modal fade" id="edit<?= $d['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">
<form method="post">

<input type="hidden" name="id" value="<?= $d['id'] ?>">

<div class="modal-body">
<input name="judul" class="form-control mb-2"
value="<?= htmlspecialchars($d['judul']) ?>">

<textarea name="isi" class="form-control mb-2"><?= htmlspecialchars($d['isi']) ?></textarea>

<select name="status" class="form-select">
<option value="baru" <?= $d['status']=='baru'?'selected':'' ?>>Baru</option>
<option value="diproses" <?= $d['status']=='diproses'?'selected':'' ?>>Diproses</option>
<option value="selesai" <?= $d['status']=='selesai'?'selected':'' ?>>Selesai</option>
</select>
</div>

<div class="modal-footer">
<button class="btn btn-primary" name="update">Simpan</button>
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

<div class="modal-body">

<select name="id_user" class="form-select mb-2" required>
<option value="">Pilih User</option>
<?php while($u=mysqli_fetch_assoc($users)){ ?>
<option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama']) ?></option>
<?php } ?>
</select>

<input name="judul" class="form-control mb-2" placeholder="Judul" required>
<textarea name="isi" class="form-control" placeholder="Isi laporan" required></textarea>

</div>

<div class="modal-footer">
<button class="btn btn-success" name="tambah">Simpan</button>
</div>

</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>