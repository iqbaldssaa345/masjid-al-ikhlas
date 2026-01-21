<?php
session_start();

/* Hapus semua session */
$_SESSION = [];
session_unset();
session_destroy();

/* Redirect ke halaman index */
header("Location: index.php");
exit;
?>
