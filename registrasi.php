<?php
include "config/koneksi.php";

$pesan = "";
$sukses = "";

if(isset($_POST['daftar'])){
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // VALIDASI
    if(strlen($password) < 5){
        $pesan = "Password minimal 5 karakter!";
    }else{
        // CEK USERNAME
        $cek = mysqli_query($koneksi,"SELECT id FROM users WHERE username='$username'");
        if(mysqli_num_rows($cek) > 0){
            $pesan = "Username sudah digunakan!";
        }else{
            // HASH PASSWORD (LOGIN SUDAH SUPPORT)
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // SIMPAN (DEFAULT: pengunjung)
            $simpan = mysqli_query($koneksi,"
                INSERT INTO users (nama, username, password, role)
                VALUES ('$nama','$username','$hash','pengunjung')
            ");

            if($simpan){
                header("Location: login.php?daftar=success");
                exit;
            }else{
                $pesan = "Gagal menyimpan data!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Registrasi Akun | Masjid Al-Ikhlas</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;
    background:linear-gradient(135deg,#198754,#0d6efd);
    display:flex;
    align-items:center;
    justify-content:center;
}
.card{
    border:none;
    border-radius:26px;
}
.form-control:focus{
    box-shadow:none;
    border-color:#198754;
}
</style>
</head>

<body>

<div class="container">
<div class="row justify-content-center">
<div class="col-md-5 col-sm-11">

<div class="card shadow-lg">
<div class="card-body p-4">

<!-- HEADER -->
<div class="text-center mb-4">
    <i class="bi bi-person-plus-fill text-success" style="font-size:48px"></i>
    <h4 class="fw-bold mt-2">Registrasi Akun</h4>
    <small class="text-muted">Masjid Al-Ikhlas</small>
</div>

<!-- PESAN -->
<?php if($pesan!=""){ ?>
<div class="alert alert-danger text-center py-2">
    <i class="bi bi-exclamation-circle"></i>
    <?= $pesan ?>
</div>
<?php } ?>

<!-- FORM -->
<form method="post">

<div class="mb-3">
    <label class="form-label">Nama Lengkap</label>
    <input type="text" name="nama" class="form-control"
           placeholder="Masukkan nama lengkap" required>
</div>

<div class="mb-3">
    <label class="form-label">Username</label>
    <input type="text" name="username" class="form-control"
           placeholder="Buat username" required>
</div>

<div class="mb-4">
    <label class="form-label">Password</label>
    <input type="password" name="password" class="form-control"
           placeholder="Minimal 5 karakter" required>
</div>

<button name="daftar"
        class="btn btn-success w-100 rounded-pill fw-bold py-2 mb-3">
    <i class="bi bi-check-circle"></i>
    DAFTAR SEKARANG
</button>

</form>

<!-- LINK LOGIN -->
<div class="text-center">
    <small class="text-muted">Sudah punya akun?</small>
    <a href="login.php" class="btn btn-outline-primary w-100 rounded-pill mt-2">
        <i class="bi bi-box-arrow-in-right"></i>
        LOGIN
    </a>
</div>

<!-- FOOTER -->
<div class="text-center mt-4">
    <a href="index.php" class="text-decoration-none small">
        ⬅ Kembali ke Beranda
    </a>
    <div class="text-muted small mt-2">
        © <?= date('Y') ?> Masjid Al-Ikhlas
    </div>
</div>

</div>
</div>

</div>
</div>
</div>

</body>
</html>
