<?php include "../config/koneksi.php"; ?>
<form method="post">
<input type="date" name="tanggal">
<select name="jenis">
  <option value="infaq">Infaq</option>
  <option value="dkm">DKM</option>
</select>
<input name="keterangan">
<input name="jumlah">
<button name="simpan">Simpan</button>
</form>

<?php
if(isset($_POST['simpan'])){
 mysqli_query($koneksi,"INSERT INTO keuangan VALUES(
 NULL,
 '$_POST[tanggal]',
 '$_POST[jenis]',
 '$_POST[keterangan]',
 '$_POST[jumlah]',
 '$_SESSION[id]'
 )");
 echo "Data tersimpan";
}
?>
