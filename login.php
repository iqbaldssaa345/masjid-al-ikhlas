<?php
session_start();
include "config/koneksi.php";

$error = "";

if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $q = mysqli_query($koneksi,"
        SELECT * FROM users 
        WHERE username='$username'
        LIMIT 1
    ");

    if(mysqli_num_rows($q) === 1){
        $d = mysqli_fetch_assoc($q);

        // VALIDASI PASSWORD (MD5 & PASSWORD_HASH)
        if(
            password_verify($password, $d['password']) ||
            md5($password) === $d['password']
        ){
            $_SESSION['user_id'] = $d['id'];
            $_SESSION['nama']    = $d['nama'];
            $_SESSION['role']    = $d['role'];

            if($d['role'] == "admin"){
                header("Location: admin/");
            }elseif($d['role'] == "petugas"){
                header("Location: petugas/");
            }else{
                header("Location: pengunjung/");
            }
            exit;
        }else{
            $error = "Username atau password salah!";
        }
    }else{
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login | Masjid Al-Ikhlas</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700;800&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;
    background:linear-gradient(135deg,#0d6efd,#198754);
}
.login-card{
    border-radius:26px;
    border:none;
}
.form-control:focus{
    box-shadow:none;
    border-color:#198754;
}
</style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="container">
<div class="row justify-content-center">
<div class="col-md-4 col-sm-10">

<div class="card shadow-lg login-card">
<div class="card-body p-4">

<!-- HEADER -->
<div class="text-center mb-4">
    <i class="bi bi-moon-stars-fill text-success" style="font-size:48px"></i>
    <h4 class="fw-bold mt-2">Masjid Al-Ikhlas</h4>
    <small class="text-muted">Sistem Informasi & Pelayanan Masjid</small>
</div>

<!-- ERROR -->
<?php if($error!=""){ ?>
<div class="alert alert-danger text-center py-2 rounded-3">
    <i class="bi bi-exclamation-circle"></i>
    <?= $error ?>
</div>
<?php } ?>

<!-- FORM -->
<form method="post">

<div class="mb-3">
    <label class="form-label">Username</label>
    <div class="input-group">
        <span class="input-group-text bg-light">
            <i class="bi bi-person"></i>
        </span>
        <input type="text" name="username" class="form-control"
               placeholder="Masukkan username" required autofocus>
    </div>
</div>

<div class="mb-4">
    <label class="form-label">Password</label>
    <div class="input-group">
        <span class="input-group-text bg-light">
            <i class="bi bi-lock"></i>
        </span>
        <input type="password" name="password" class="form-control"
               placeholder="Masukkan password" required>
    </div>
</div>

<button name="login"
        class="btn btn-success w-100 rounded-pill fw-bold py-2 mb-3">
    <i class="bi bi-box-arrow-in-right"></i>
    LOGIN
</button>

</form>

<!-- REGISTER -->
<div class="text-center">
    <small class="text-muted">Belum punya akun?</small>
    <a href="registrasi.php"
       class="btn btn-outline-primary w-100 rounded-pill mt-2">
        <i class="bi bi-person-plus"></i>
        DAFTAR AKUN
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
