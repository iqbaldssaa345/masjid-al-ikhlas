<?php
session_start();
include "config/koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Display Masjid Al-Ikhlas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Scheherazade+New:wght@700&display=swap" rel="stylesheet">

<meta http-equiv="refresh" content="180">

<style>
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background:#000;
    color:#fff;
}
.header{
    background:linear-gradient(90deg,#0f2027,#203a43,#2c5364);
    padding:15px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.jam{
    font-size:56px;
    font-weight:800;
    color:#ffeb3b;
}
.hero{
    background:linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)),
    url('https://images.unsplash.com/photo-1587614382346-4ec70e388b28');
    background-size:cover;
    background-position:center;
    text-align:center;
    padding:60px 20px;
}
.arab{
    font-family:'Scheherazade New',serif;
    font-size:74px;
}
.panel-sholat{
    display:flex;
    text-align:center;
}
.panel{
    flex:1;
    padding:18px;
    font-weight:700;
}
.panel span{
    display:block;
    font-size:26px;
}
.imsak{background:#6a1b9a;}
.subuh{background:#1565c0;}
.dzuhur{background:#2e7d32;}
.ashar{background:#ef6c00;}
.maghrib{background:#c62828;}
.isya{background:#37474f;}

.saldo-box{
    background:#fff;
    color:#1b5e20;
    text-align:center;
    padding:25px;
    font-weight:800;
    font-size:38px;
}

.card{
    border-radius:18px;
}

.marquee{
    background:#0d6efd;
    padding:14px;
    font-size:18px;
    font-weight:600;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div>
        <h3 class="fw-bold mb-0">🕌 MASJID AL-IKHLAS</h3>
        <small class="opacity-75">Sistem Display & Informasi Masjid</small>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="jam" id="jam"></div>

        <?php if(isset($_SESSION['role']) && $_SESSION['role']=="admin"){ ?>
            <a href="admin/" class="btn btn-warning btn-sm fw-bold">
                ⚙ ADMIN
            </a>
        <?php } ?>
    </div>
</div>

<!-- AYAT -->
<div class="hero">
    <div class="arab">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
    <div class="opacity-75 mt-2 fs-5">
        Dengan nama Allah Yang Maha Pengasih, Maha Penyayang
    </div>

    <a href="index.php" class="btn btn-outline-light mt-4 px-4">
        ⬅ Kembali ke Beranda
    </a>
</div>

<!-- JADWAL SHOLAT -->
<div class="panel-sholat">
    <div class="panel imsak">Imsak<span>04:25</span></div>
    <div class="panel subuh">Subuh<span>04:35</span></div>
    <div class="panel dzuhur">Dzuhur<span>11:45</span></div>
    <div class="panel ashar">Ashar<span>15:07</span></div>
    <div class="panel maghrib">Maghrib<span>18.16</span></div>
    <div class="panel isya">Isya<span>19:20</span></div>
</div>

<!-- SALDO INFAQ -->
<?php
$q = mysqli_query($koneksi,"SELECT SUM(jumlah) AS total FROM keuangan WHERE jenis='infaq'");
$d = mysqli_fetch_assoc($q);
$saldo = $d['total'] ?? 0;
?>
<div class="saldo-box">
    💰 SALDO INFAQ SAAT INI<br>
    Rp <?= number_format($saldo,0,',','.') ?>
</div>

<!-- INFO MASJID -->
<div class="container my-4">
    <div class="row g-4">
        <?php
        $info = mysqli_query($koneksi,"SELECT * FROM info_masjid ORDER BY id DESC");
        while($i=mysqli_fetch_assoc($info)){
        ?>
        <div class="col-md-6">
            <div class="card bg-dark text-white h-100 shadow">
                <div class="card-body">
                    <h5 class="card-title text-warning fw-bold">
                        <?= htmlspecialchars($i['judul']) ?>
                    </h5>
                    <p class="card-text mt-2">
                        <?= nl2br(htmlspecialchars($i['isi'])) ?>
                    </p>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<!-- MARQUEE -->
<div class="marquee">
<marquee scrollamount="7">
<?php
$mq = mysqli_query($koneksi,"SELECT isi FROM info_masjid");
while($m=mysqli_fetch_assoc($mq)){
    echo htmlspecialchars($m['isi'])." • ";
}
?>
</marquee>
</div>

<script>
function updateWaktu(){
    document.getElementById("jam").innerHTML =
        new Date().toLocaleTimeString("id-ID");
}
setInterval(updateWaktu,1000);
updateWaktu();
</script>

</body>
</html>
