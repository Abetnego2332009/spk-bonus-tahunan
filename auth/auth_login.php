<?php
// authentication/auth_login.php
if (session_status() === PHP_SESSION_NONE) session_start();

// koneksi & base_url
require_once __DIR__ . '/../koneksi.php';

// cek login
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    echo "<script>
            alert('Anda harus login dulu!');
            window.location = '" . $base_url . "/index.php';
          </script>";
    exit;
}
?>
