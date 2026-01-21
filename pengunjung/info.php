<?php
include "../config/koneksi.php";
$data = mysqli_query($koneksi,"SELECT * FROM info_masjid ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Informasi Masjid | Masjid Al-Ikhlas</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(180deg,#f9fbfd,#eef2f7);
}
.navbar{
    background:linear-gradient(135deg,#198754,#0d6efd);
}
.hero{
    background:linear-gradient(135deg,#198754,#0d6efd);
    color:#fff;
    border-radius:28px;
}
.info-card{
    border:none;
    border-radius:24px;
    transition:.35s ease;
    background:#fff;
}
.info-card:hover{
    transform:translateY(-8px);
    box-shadow:0 30px 60px rgba(0,0,0,.15);
}
.icon-box{
    width:52px;
    height:52px;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#e8f1ff;
    color:#0d6efd;
    font-size:24px;
}
.footer{
    font-size:14px;
    color:#6c757d;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark shadow-sm">
<div class="container-fluid px-4">
    <span class="navbar-brand fw-bold">
        🕌 Masjid Al-Ikhlas
    </span>
    <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
        <i class="bi bi-house"></i> Beranda
    </a>
</div>
</nav>

<div class="container py-5">

<!-- HERO -->
<div class="hero p-5 mb-5 shadow-sm text-center">
    <h3 class="fw-bold mb-2">📢 Informasi Masjid</h3>
    <p class="mb-0 opacity-75">
        Informasi resmi, transparan, dan terpercaya untuk seluruh jamaah
    </p>
</div>

<div class="row g-4">

<?php if(mysqli_num_rows($data)>0){ ?>
<?php while($d=mysqli_fetch_assoc($data)){ ?>
<div class="col-md-6 col-lg-4">
    <div class="card info-card h-100 p-4">

        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="icon-box">
                <i class="bi bi-megaphone-fill"></i>
            </div>
            <h5 class="fw-bold mb-0">
                <?= htmlspecialchars($d['judul']); ?>
            </h5>
        </div>

        <p class="text-muted mb-0" style="line-height:1.8">
            <?= nl2br(htmlspecialchars($d['isi'])); ?>
        </p>

    </div>
</div>
<?php } ?>
<?php } else { ?>

<div class="col-12">
    <div class="alert alert-warning text-center rounded-4 shadow-sm py-4">
        <i class="bi bi-info-circle fs-4"></i><br>
        Belum ada informasi yang tersedia
    </div>
</div>

<?php } ?>

</div>
</div>

<footer class="text-center py-4 footer">
© <?= date('Y'); ?> Masjid Al-Ikhlas • Media Informasi Jamaah
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
