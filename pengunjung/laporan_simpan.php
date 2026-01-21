<?php
session_start();
include "../config/koneksi.php";

$id_user = $_SESSION['id'];
$judul = mysqli_real_escape_string($koneksi,$_POST['judul']);
$isi   = mysqli_real_escape_string($koneksi,$_POST['isi']);

mysqli_query($koneksi,"
INSERT INTO laporan (id_user,judul,isi)
VALUES ('$id_user','$judul','$isi')
");

header("location:laporan.php");
