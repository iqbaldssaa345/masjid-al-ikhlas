<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}

/* ================= FILTER ================= */
$dari   = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$jenis  = $_GET['jenis'] ?? '';

$where = "WHERE tanggal BETWEEN '$dari' AND '$sampai'";
if($jenis!=''){
    $where .= " AND jenis='$jenis'";
}

/* ================= DATA ================= */
$q = mysqli_query($koneksi,"
    SELECT keuangan.*, users.nama 
    FROM keuangan 
    LEFT JOIN users ON keuangan.user_id=users.id
    $where
    ORDER BY tanggal ASC
");

/* ================= TOTAL ================= */
$masuk = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis='infaq' AND tanggal BETWEEN '$dari' AND '$sampai'")
)['total'] ?? 0;

$keluar = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT SUM(jumlah) total FROM keuangan WHERE jenis IN ('dkm','pengeluaran') AND tanggal BETWEEN '$dari' AND '$sampai'")
)['total'] ?? 0;

$saldo = $masuk - $keluar;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Masjid | Petugas</title>

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
            border-radius: 18px;
            border: none;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03);
            text-align: center;
        }

        .table-custom th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 14px;
        }

        .table-custom td {
            padding: 12px 14px;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .card-custom {
                border: none !important;
                box-shadow: none !important;
            }
            .container {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm py-3 no-print">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-5 d-flex align-items-center gap-2">
                📑 Laporan Keuangan Masjid | Petugas
            </span>

            <div class="text-white d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <div class="clock-badge d-flex align-items-center gap-2 me-2">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span id="petugasClock" class="text-warning">--:--:-- WIB</span>
                </div>

                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">

        <!-- FILTER CARD -->
        <div class="card card-custom p-3 mb-4 no-print">
            <div class="card-body p-2">
                <form class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-secondary">DARI TANGGAL</label>
                        <input type="date" name="dari" value="<?= $dari ?>" class="form-control rounded-3">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-secondary">SAMPAI TANGGAL</label>
                        <input type="date" name="sampai" value="<?= $sampai ?>" class="form-control rounded-3">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-secondary">JENIS TRANSAKSI</label>
                        <select name="jenis" class="form-select rounded-3">
                            <option value="">Semua Transaksi</option>
                            <option value="infaq" <?= $jenis=='infaq'?'selected':'' ?>>Infaq (Pemasukan)</option>
                            <option value="dkm" <?= $jenis=='dkm'?'selected':'' ?>>Pengeluaran (DKM)</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                        <button type="button" onclick="window.print()" class="btn btn-success w-100 fw-bold rounded-3">
                            <i class="bi bi-printer me-1"></i> Cetak
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- STATS SUMMARY -->
        <div class="row g-3 mb-4 no-print">
            <div class="col-md-4">
                <div class="stat-card">
                    <small class="text-secondary fw-semibold">TOTAL PEMASUKAN</small>
                    <h4 class="fw-bold text-success mt-1 mb-0">Rp <?= number_format($masuk,0,',','.') ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <small class="text-secondary fw-semibold">TOTAL PENGELUARAN</small>
                    <h4 class="fw-bold text-danger mt-1 mb-0">Rp <?= number_format($keluar,0,',','.') ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <small class="text-secondary fw-semibold">SALDO AKHIR PERIODE</small>
                    <h4 class="fw-bold text-primary mt-1 mb-0">Rp <?= number_format($saldo,0,',','.') ?></h4>
                </div>
            </div>
        </div>

        <!-- PRINTABLE REPORT CONTENT -->
        <div class="card card-custom">
            <div class="card-body p-4">

                <!-- PRINT HEADER -->
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-dark mb-1">LAPORAN KEUANGAN MASJID AL-IKHLAS</h4>
                    <p class="text-secondary small mb-0">
                        Periode: <b><?= date('d/m/Y',strtotime($dari)) ?></b> s/d <b><?= date('d/m/Y',strtotime($sampai)) ?></b>
                    </p>
                    <hr class="my-3">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-custom align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">NO</th>
                                <th>TANGGAL</th>
                                <th>KETERANGAN TRANSAKSI</th>
                                <th class="text-center">JENIS</th>
                                <th class="text-end">JUMLAH (RP)</th>
                                <th>PETUGAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no=1;
                            if(mysqli_num_rows($q)==0){
                                echo "<tr><td colspan='6' class='text-center text-muted py-4'>Tidak ada data transaksi pada periode ini</td></tr>";
                            }
                            while($d=mysqli_fetch_assoc($q)){
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary"><?= $no++ ?></td>
                                <td><?= date('d/m/Y',strtotime($d['tanggal'])) ?></td>
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
                                <td><?= htmlspecialchars($d['nama'] ?? 'Petugas') ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>

                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end text-uppercase">Total Pemasukan (Infaq)</td>
                                <td class="text-end text-success fs-6">Rp <?= number_format($masuk,0,',','.') ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end text-uppercase">Total Pengeluaran (DKM)</td>
                                <td class="text-end text-danger fs-6">Rp <?= number_format($keluar,0,',','.') ?></td>
                                <td></td>
                            </tr>
                            <tr class="table-secondary">
                                <td colspan="4" class="text-end text-uppercase fw-extrabold">Saldo Akhir Kas</td>
                                <td class="text-end text-primary fs-6 fw-extrabold">Rp <?= number_format($saldo,0,',','.') ?></td>
                                <td></td>
                            </tr>
                        </tfoot>

                    </table>
                </div>

            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <footer class="text-center py-4 text-muted small no-print">
        © <?= date('Y') ?> Masjid Al-Ikhlas • Cetak & Rekapitulasi Laporan Keuangan
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
