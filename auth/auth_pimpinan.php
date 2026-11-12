<?php
// hanya admin
require_once __DIR__ . '/auth_login.php'; // ini sudah session_start()


if ($_SESSION['role'] !== 'pimpinan') {
    echo "<script>
            alert('Akses ditolak! Halaman ini hanya untuk Pimpinan.');
            window.location = '" . $base_url . "/dashboard.php/';
          </script>";
    exit;
}
?>
