<?php
session_start();
include "../config/koneksi.php";

$id = $_POST['id'];
$judul = mysqli_real_escape_string($koneksi,$_POST['judul']);
$isi   = mysqli_real_escape_string($koneksi,$_POST['isi']);

mysqli_query($koneksi,"
UPDATE laporan 
SET judul='$judul', isi='$isi'
WHERE id='$id' AND status='baru'
");

header("location:laporan.php");
