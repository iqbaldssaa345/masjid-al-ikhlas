<?php
$koneksi = mysqli_connect("localhost","root","12345678","masjid");
if(!$koneksi){
    die("Koneksi gagal");
}
session_start();
?>
