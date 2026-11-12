<?php
session_start();
require_once 'koneksi.php';

if (isset($_POST['login'])) {

    $username = trim(htmlspecialchars($_POST['username']));
    $password = trim(htmlspecialchars($_POST['password']));

    if ($username === '' || $password === '') {
        echo "<script>alert('Username dan password wajib diisi!'); window.location='index.php';</script>";
        exit;
    }

    // Query ambil data user + join ke karyawan untuk mendapatkan nama_lengkap
    $query = "SELECT u.id, u.karyawan_id, u.username, u.password, u.role, u.is_active,
                     k.nama_lengkap
              FROM users u
              LEFT JOIN karyawan k ON u.karyawan_id = k.id
              WHERE u.username = ?";

    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        if (password_verify($password, $row['password'])) {

            if ($row['is_active'] == 1) {

                // --- LOGIN BERHASIL ---
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['karyawan_id'] = $row['karyawan_id'];
                $_SESSION['logged_in'] = true;
                $_SESSION['nama_lengkap'] = $row['nama_lengkap'] ?? $row['username']; // fallback jika null

                // Update last_login
                $update_query = "UPDATE users SET last_login = NOW() WHERE id = ?";
                $update_stmt = mysqli_prepare($koneksi, $update_query);
                mysqli_stmt_bind_param($update_stmt, "i", $row['id']);
                mysqli_stmt_execute($update_stmt);
                mysqli_stmt_close($update_stmt);

                // ========== PERUBAHAN DI SINI ==========
                // Array tujuan berdasarkan role
                $halaman_tujuan = 'dashboard.php';

                // Nama untuk sambutan (sudah ada di session, tapi kita ambil dari $row)
                $nama_sambutan = htmlspecialchars($row['nama_lengkap'] ?? $row['username'], ENT_QUOTES, 'UTF-8');

                // Tampilkan alert dan redirect
                echo "<script>
                    alert('Selamat datang, {$nama_sambutan}');
                    window.location='{$halaman_tujuan}';
                </script>";
                exit;
                // ========================================

            } else {
                echo "<script>alert('Akun Anda tidak aktif. Silakan hubungi administrator.'); window.location='index.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Password yang Anda masukkan salah!'); window.location='index.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Username tidak terdaftar!'); window.location='index.php';</script>";
        exit;
    }

    mysqli_stmt_close($stmt);
} else {
    header("Location: index.php");
    exit;
}
