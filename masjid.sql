-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 18, 2026 at 06:12 AM
-- Server version: 8.0.17
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `masjid`
--

-- --------------------------------------------------------

--
-- Table structure for table `info_masjid`
--

CREATE TABLE `info_masjid` (
  `id` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `isi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `info_masjid`
--

INSERT INTO `info_masjid` (`id`, `judul`, `isi`) VALUES
(1, 'Profil Masjid', 'Masjid Al-Ikhlas berdiri sejak tahun 1994 diresmikan oleh bupati bogor dan menjadi pusat kegiatan ibadah serta sosial masyarakat sekitar '),
(2, 'Jadwal Sholat', 'Subuh 04:35 WIB | Dzuhur 12:00 WIB | Ashar 15:15 WIB | Maghrib 18:10 WIB | Isya 19:20 WIB'),
(3, 'Pengumuman', 'setiap jumat '),
(4, 'Laporan Keuangan', 'Laporan keuangan masjid diperbarui secara berkala dan dapat dilihat pada display TV masjid.');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan`
--

CREATE TABLE `keuangan` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('infaq','dkm') NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `keuangan`
--

INSERT INTO `keuangan` (`id`, `tanggal`, `jenis`, `keterangan`, `jumlah`, `user_id`) VALUES
(2, '2026-01-02', 'infaq', 'Infaq Sholat Subuh', 350000, 2),
(3, '2026-01-03', 'infaq', 'Infaq Sholat Jumat', 1250000, 2),
(4, '2026-01-04', 'dkm', 'Dana Operasional Masjid', 200000, 1),
(5, '2026-01-05', 'infaq', 'Infaq Jamaah Ahmad', 230000000, 3),
(6, '2026-01-05', 'infaq', 'Infaq Jamaah Budi', 250000, 4),
(7, '2026-01-20', 'infaq', 'renovasi ruang wudhu', 100000000, 0),
(8, '2026-01-20', 'infaq', 'Infaq Jamaah Ahmad', 100000, 2),
(9, '2026-01-20', 'infaq', 'renovasi ruang wudhu', 89999000, 0),
(10, '2026-01-20', 'infaq', 'renovasi ruang audio', 1200000, 0),
(11, '2026-01-20', 'dkm', 'renovasi ruang wudhu', 88999, 2),
(12, '2026-01-20', 'infaq', 'renovasi ruang audio', 8887700, 2),
(13, '2026-01-20', 'dkm', 'renovasi ruang dkm', 1200000, 2),
(14, '2026-01-20', 'infaq', 'renovasi ruang dkm', 2000000, 2),
(15, '2026-01-20', 'dkm', 'renovasi ruang dkm', 12200000, 2),
(16, '2026-01-20', 'infaq', 'renovasi ruang wudhu', 89999000, 2),
(17, '2026-01-20', 'infaq', 'renovasi lantai 2 ', 40000000, 2),
(18, '2026-01-20', 'infaq', 'renovasi lantai 2 ', 50000, 2),
(19, '2026-01-21', 'dkm', 'pembangunan menara masjid gantikan toa di atap masjid ', 100000000, 2),
(20, '2026-01-15', 'infaq', 'penambahan audio ', 100000000, 0),
(22, '2026-01-21', 'dkm', 'berbaikan pra sarana', 112000000, 0),
(24, '2026-01-22', 'dkm', ' tk al ikhlas renovasi  ', 200000000, 5),
(25, '2026-01-22', 'infaq', ' masjid al ikhlas biar berkah', 300000000, 6),
(27, '2026-01-22', 'infaq', ' masjid al ikhlas biar berkah dan bagus', 30000000, 4),
(28, '2026-01-22', 'infaq', ' masjid al ikhlas biar berkah dan bagus', 430000000, 4),
(29, '2026-01-22', 'dkm', ' masjid al ikhlas biar berkah dan bagus dah ', 500000000, 6),
(30, '2026-04-18', 'infaq', 'pembangunan menara masjid ', 90000000, 4),
(31, '2026-04-18', 'dkm', 'pembangunan menara masjid ', 200000000, 4),
(32, '2026-04-18', 'infaq', 'buat anak yatim', 50000000, 4);

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `isi` text NOT NULL,
  `status` enum('baru','diproses','selesai') DEFAULT 'baru',
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id`, `id_user`, `judul`, `isi`, `status`, `tanggal`) VALUES
(1, 1, 'Lampu Jalan Mati', 'Lampu jalan di RT 03 RW 05 mati sejak 3 hari lalu.', 'baru', '2026-01-21 14:11:32'),
(2, 2, 'Pelayanan KTP Lambat', 'Proses pembuatan KTP sudah 2 minggu belum selesai.', 'diproses', '2026-01-21 14:11:32'),
(3, 3, 'Sampah Menumpuk', 'Sampah di selokan depan masjid belum diangkut.', 'selesai', '2026-01-21 14:11:32'),
(4, 3, 'listrik mati ', 'woi berbaiki\r\n', 'baru', '2026-01-21 14:27:39'),
(5, 5, 'lampu mati', 'lampu mati', 'baru', '2026-01-21 18:58:48'),
(6, 4, 'berbaiki', 'audio burik', 'diproses', '2026-04-18 07:54:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas','pengunjung') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Administrator Masjid', 'admin', '202cb962ac59075b964b07152d234b70', 'admin'),
(2, 'Petugas Keuangan', 'petugas', '202cb962ac59075b964b07152d234b70', 'petugas'),
(3, 'Ahmad Jamaah', 'ahmad', '8de13959395270bf9d6819f818ab1a00', 'pengunjung'),
(4, 'Budi Jamaah', 'budi', '9c5fa085ce256c7c598f6710584ab25d', 'pengunjung'),
(5, 'ustadz el putra', 'putra', '$2y$10$tX/P9OngdPhaQBUj8Qtjz..LBtCfcBXzmNVcS9pgXa71slrdbwrna', 'pengunjung'),
(6, 'ustadz ali tamam', 'ali tamam', '$2y$10$4kwe389Xku9zwv.Vfc11kOTJkAQAooDED0Mz2t75XvusnOQBnM1d6', 'pengunjung'),
(7, 'rudi gunawan', 'rudi', '$2y$10$yoVynnnD.rR4Qp6y0BrIs.YTrR2GmndtfZdZqPItipZQLR6s99yf2', 'pengunjung'),
(8, 'haji zulkarnaen', 'zulkanaen', '289dff07669d7a23de0ef88d2f7129e7', 'petugas'),
(9, 'hendra', 'rudi@gmail.com', '$2y$10$dsn8.5ZDIQPaRNl27.xa4uGhURE7Yv2cENq67Z.8a8NV8LQveu7eW', 'pengunjung');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `info_masjid`
--
ALTER TABLE `info_masjid`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keuangan`
--
ALTER TABLE `keuangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `info_masjid`
--
ALTER TABLE `info_masjid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `keuangan`
--
ALTER TABLE `keuangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
