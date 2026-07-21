<?php 
session_start();
include "../config/koneksi.php"; 

$pesan = "";
if(isset($_POST['simpan'])){
    $user_id = $_SESSION['user_id'] ?? 1;
    $simpan = mysqli_query($koneksi,"INSERT INTO keuangan (tanggal, jenis, keterangan, jumlah, user_id) VALUES(
        '$_POST[tanggal]',
        '$_POST[jenis]',
        '".mysqli_real_escape_string($koneksi, $_POST['keterangan'])."',
        ".(int)$_POST['jumlah'].",
        '$user_id'
    )");
    if($simpan){
        $pesan = "Data keuangan berhasil disimpan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Input Keuangan | Petugas</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f6fb; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card-custom { border-radius: 24px; border: 1px solid rgba(226, 232, 240, 0.8); box-shadow: 0 15px 35px rgba(0,0,0,0.06); background: #ffffff; max-width: 450px; width: 100%; }
    </style>
</head>
<body>

<div class="card card-custom p-4">
    <div class="card-body">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-plus-circle text-success me-2"></i>Quick Input Keuangan</h5>
        
        <?php if($pesan != ""){ ?>
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <i class="bi bi-check-circle me-1"></i> <?= $pesan ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">TANGGAL</label>
                <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="form-control rounded-3" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">JENIS</label>
                <select name="jenis" class="form-select rounded-3">
                    <option value="infaq">Infaq (Pemasukan)</option>
                    <option value="dkm">DKM (Pengeluaran)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">KETERANGAN</label>
                <input type="text" name="keterangan" class="form-control rounded-3" placeholder="Masukan keterangan" required>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">JUMLAH (RP)</label>
                <input type="number" name="jumlah" class="form-control rounded-3" placeholder="0" required>
            </div>

            <button name="simpan" type="submit" class="btn btn-success w-100 fw-bold rounded-3 py-2 mb-2">Simpan Data</button>
            <a href="dana.php" class="btn btn-outline-secondary w-100 fw-semibold rounded-3">Kembali ke Dana Masjid</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
