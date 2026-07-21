<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || !in_array($_SESSION['role'],['admin','petugas'])){
    header("location:../login.php");
    exit;
}

/* =====================
   RINGKASAN KEUANGAN
===================== */
$infaq = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis='infaq'")
)['total'] ?? 0;

$keluar = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis IN ('dkm', 'pengeluaran')")
)['total'] ?? 0;

$saldo = $infaq - $keluar;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan Masjid | Petugas</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f6f9;
            min-height: 100vh;
            color: #212529;
        }

        .navbar-custom {
            background: linear-gradient(135deg, #198754, #20c997);
        }

        .clock-badge {
            background: rgba(0, 0, 0, 0.22);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 30px;
            padding: 6px 16px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .card-custom {
            background: #ffffff;
            border-radius: 24px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .stat-card {
            background: #ffffff;
            border-radius: 22px;
            border: none;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 12px;
        }

        .icon-pemasukan {
            background: rgba(25, 135, 84, 0.12);
            color: #198754;
        }

        .icon-pengeluaran {
            background: rgba(220, 53, 69, 0.12);
            color: #dc3545;
        }

        .icon-saldo {
            background: rgba(13, 110, 253, 0.12);
            color: #0d6efd;
        }

        .table-custom th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px 16px;
        }

        .table-custom td {
            padding: 14px 16px;
            font-size: 0.9rem;
            vertical-align: middle;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                📊 Ringkasan Keuangan Masjid | Petugas
            </span>

            <div class="text-white d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <div class="clock-badge d-flex align-items-center gap-2 me-2">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span id="petugasClock" class="text-warning">--:--:-- WIB</span>
                </div>

                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <a href="../logout.php" class="btn btn-light text-success btn-sm rounded-pill px-3 fw-bold">
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">

        <!-- ACTION NAVIGATION -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="index.php" class="btn btn-outline-secondary fw-semibold rounded-pill px-3">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>

            <a href="laporan_jamaah.php" class="btn btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm">
                <i class="bi bi-chat-left-quote-fill me-1"></i> Laporan Jamaah
            </a>
        </div>

        <!-- RINGKASAN KEUANGAN -->
        <div class="row g-4 mb-4">

            <!-- TOTAL PEMASUKAN -->
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon icon-pemasukan">
                        <i class="bi bi-arrow-down-circle-fill"></i>
                    </div>
                    <small class="text-secondary font-weight-600 d-block">TOTAL PEMASUKAN (INFAQ)</small>
                    <h3 class="fw-bold text-success mt-1 mb-0">Rp <?= number_format($infaq,0,',','.') ?></h3>
                </div>
            </div>

            <!-- TOTAL PENGELUARAN -->
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon icon-pengeluaran">
                        <i class="bi bi-arrow-up-circle-fill"></i>
                    </div>
                    <small class="text-secondary font-weight-600 d-block">TOTAL PENGELUARAN (DKM)</small>
                    <h3 class="fw-bold text-danger mt-1 mb-0">Rp <?= number_format($keluar,0,',','.') ?></h3>
                </div>
            </div>

            <!-- SALDO AKHIR -->
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon icon-saldo">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <small class="text-secondary font-weight-600 d-block">SALDO AKHIR KAS</small>
                    <h3 class="fw-bold text-primary mt-1 mb-0">Rp <?= number_format($saldo,0,',','.') ?></h3>
                </div>
            </div>

        </div>

        <!-- TABEL TRANSAKSI -->
        <div class="card card-custom">
            <div class="card-body p-4">

                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-journal-text text-primary me-2"></i>Detail Transaksi Keuangan
                </h5>

                <div class="table-responsive">
                    <table class="table table-hover table-custom align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">NO</th>
                                <th>TANGGAL</th>
                                <th>KETERANGAN</th>
                                <th class="text-center">JENIS</th>
                                <th class="text-end">JUMLAH</th>
                                <th>PETUGAS INPUT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no=1;
                            $q=mysqli_query($koneksi,"
                                SELECT keuangan.*, users.nama
                                FROM keuangan
                                LEFT JOIN users ON keuangan.user_id = users.id
                                ORDER BY tanggal DESC
                            ");

                            if(mysqli_num_rows($q) == 0){
                                echo '<tr><td colspan="6" class="text-center text-muted py-4">Belum ada transaksi recorded</td></tr>';
                            }

                            while($d=mysqli_fetch_assoc($q)){
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary"><?= $no++ ?></td>
                                <td class="fw-semibold text-dark"><?= date('d/m/Y',strtotime($d['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($d['keterangan']) ?></td>
                                <td class="text-center">
                                    <?php if($d['jenis']=='infaq'){ ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-bold d-inline-flex align-items-center gap-1"><i class="bi bi-arrow-down-left"></i> PEMASUKAN</span>
                                    <?php }else{ ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-bold d-inline-flex align-items-center gap-1"><i class="bi bi-arrow-up-right"></i> PENGELUARAN</span>
                                    <?php } ?>
                                </td>
                                <td class="text-end fw-bold <?= $d['jenis']=='infaq'?'text-success':'text-danger' ?>">
                                    Rp <?= number_format($d['jumlah'],0,',','.') ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="bi bi-person me-1"></i><?= htmlspecialchars($d['nama'] ?? 'Sistem') ?>
                                    </span>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <footer class="text-center py-4 text-muted small">
        © <?= date('Y') ?> Masjid Al-Ikhlas • Sistem Ringkasan Keuangan
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- REAL-TIME CLOCK SCRIPT -->
    <script>
        function updatePetugasClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockElem = document.getElementById('petugasClock');
            if (clockElem) {
                clockElem.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updatePetugasClock, 1000);
        updatePetugasClock();
    </script>
</body>
</html>
