<?php
session_start();
include "../config/koneksi.php";

$id = $_GET['id'];

mysqli_query($koneksi,"
DELETE FROM laporan 
WHERE id='$id' AND status='baru'
");

header("location:laporan.php");
