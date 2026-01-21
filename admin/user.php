<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("location:../login.php");
    exit;
}

/* TAMBAH USER */
if(isset($_POST['tambah'])){
    $pass = md5($_POST['password']);
    mysqli_query($koneksi,"INSERT INTO users (nama,username,password,role) VALUES (
        '$_POST[nama]',
        '$_POST[username]',
        '$pass',
        '$_POST[role]'
    )");
    header("location:user.php");
}

/* EDIT USER */
if(isset($_POST['edit'])){
    if($_POST['password']==""){
        mysqli_query($koneksi,"UPDATE users SET
            nama='$_POST[nama]',
            username='$_POST[username]',
            role='$_POST[role]'
            WHERE id='$_POST[id]'
        ");
    }else{
        $pass = md5($_POST['password']);
        mysqli_query($koneksi,"UPDATE users SET
            nama='$_POST[nama]',
            username='$_POST[username]',
            password='$pass',
            role='$_POST[role]'
            WHERE id='$_POST[id]'
        ");
    }
    header("location:user.php");
}

/* HAPUS USER */
if(isset($_GET['hapus'])){
    mysqli_query($koneksi,"DELETE FROM users WHERE id='$_GET[hapus]'");
    header("location:user.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Manajemen User</title>

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
    <h4 class="fw-bold">👥 Manajemen User</h4>
    <div>
        <a href="index.php" class="btn btn-secondary btn-sm">⬅ Dashboard</a>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#tambah">
            <i class="bi bi-plus-circle"></i> Tambah User
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
    <th>Nama</th>
    <th>Username</th>
    <th>Role</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$no=1;
$q=mysqli_query($koneksi,"SELECT * FROM users ORDER BY role ASC");
while($u=mysqli_fetch_assoc($q)){
?>
<tr>
    <td class="text-center"><?= $no++ ?></td>
    <td><?= htmlspecialchars($u['nama']) ?></td>
    <td><?= htmlspecialchars($u['username']) ?></td>
    <td class="text-center">
        <?php if($u['role']=="admin"){ ?>
            <span class="badge bg-danger">ADMIN</span>
        <?php }elseif($u['role']=="petugas"){ ?>
            <span class="badge bg-warning text-dark">PETUGAS</span>
        <?php }else{ ?>
            <span class="badge bg-success">PENGUNJUNG</span>
        <?php } ?>
    </td>
    <td class="text-center">
        <button class="btn btn-warning btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#edit<?= $u['id'] ?>">
            <i class="bi bi-pencil"></i>
        </button>
        <?php if($u['role']!="admin"){ ?>
        <a href="?hapus=<?= $u['id'] ?>"
           onclick="return confirm('Hapus user ini?')"
           class="btn btn-danger btn-sm">
            <i class="bi bi-trash"></i>
        </a>
        <?php } ?>
    </td>
</tr>

<!-- MODAL EDIT -->
<div class="modal fade" id="edit<?= $u['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">
<form method="post">

<div class="modal-header bg-warning">
    <h5 class="modal-title">Edit User</h5>
</div>

<div class="modal-body">
    <input type="hidden" name="id" value="<?= $u['id'] ?>">

    <div class="mb-2">
        <label>Nama</label>
        <input type="text" name="nama" class="form-control" value="<?= $u['nama'] ?>" required>
    </div>

    <div class="mb-2">
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="<?= $u['username'] ?>" required>
    </div>

    <div class="mb-2">
        <label>Password (kosongkan jika tidak diubah)</label>
        <input type="password" name="password" class="form-control">
    </div>

    <div class="mb-2">
        <label>Role</label>
        <select name="role" class="form-control">
            <option value="admin" <?= $u['role']=="admin"?'selected':'' ?>>Admin</option>
            <option value="petugas" <?= $u['role']=="petugas"?'selected':'' ?>>Petugas</option>
            <option value="pengunjung" <?= $u['role']=="pengunjung"?'selected':'' ?>>Pengunjung</option>
        </select>
    </div>
</div>

<div class="modal-footer">
    <button name="edit" class="btn btn-warning w-100">Simpan</button>
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
    <h5 class="modal-title">Tambah User</h5>
</div>

<div class="modal-body">

    <div class="mb-2">
        <label>Nama</label>
        <input type="text" name="nama" class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Role</label>
        <select name="role" class="form-control">
            <option value="admin">Admin</option>
            <option value="petugas">Petugas</option>
            <option value="pengunjung">Pengunjung</option>
        </select>
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
