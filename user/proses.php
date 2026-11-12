<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pimpinan') {
    header("Location: ../login.php");
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if ($action == 'tambah') {
    $username = trim(htmlspecialchars($_POST['username']));
    $password = $_POST['password'];
    $role = $_POST['role'];
    $karyawan_id = !empty($_POST['karyawan_id']) ? (int)$_POST['karyawan_id'] : null;
    $is_active = (int)$_POST['is_active'];

    if (empty($username) || empty($password)) {
        echo "<script>alert('Username dan password harus diisi.'); window.location='tambah.php';</script>";
        exit;
    }

    // Cek username unik
    $cek = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah digunakan.'); window.location='tambah.php';</script>";
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, password, role, karyawan_id, is_active) VALUES ('$username', '$hashed', '$role', " . ($karyawan_id ?? 'NULL') . ", $is_active)";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('User berhasil ditambahkan.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "'); window.location='tambah.php';</script>";
    }
} elseif ($action == 'edit') {
    $id = (int)$_POST['id'];
    $username = trim(htmlspecialchars($_POST['username']));
    $password = $_POST['password'];
    $role = $_POST['role'];
    $karyawan_id = !empty($_POST['karyawan_id']) ? (int)$_POST['karyawan_id'] : null;
    $is_active = (int)$_POST['is_active'];

    if (empty($username)) {
        echo "<script>alert('Username harus diisi.'); window.location='edit.php?id=$id';</script>";
        exit;
    }

    // Cek username unik kecuali dirinya sendiri
    $cek = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username' AND id != $id");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah digunakan user lain.'); window.location='edit.php?id=$id';</script>";
        exit;
    }

    $set_password = '';
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $set_password = ", password = '$hashed'";
    }

    $query = "UPDATE users SET username = '$username', role = '$role', karyawan_id = " . ($karyawan_id ?? 'NULL') . ", is_active = $is_active $set_password WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('User berhasil diupdate.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($koneksi) . "'); window.location='edit.php?id=$id';</script>";
    }
} elseif ($action == 'hapus') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        echo "<script>alert('ID tidak valid.'); window.location='index.php';</script>";
        exit;
    }
    // Cek apakah user ini memiliki relasi di tabel penilaian (dinilai_oleh) atau bonus (ditentukan_oleh)
    $cek_penilaian = mysqli_query($koneksi, "SELECT id FROM penilaian WHERE dinilai_oleh = $id LIMIT 1");
    $cek_bonus = mysqli_query($koneksi, "SELECT id FROM bonus WHERE ditentukan_oleh = $id LIMIT 1");
    if (mysqli_num_rows($cek_penilaian) > 0 || mysqli_num_rows($cek_bonus) > 0) {
        echo "<script>alert('User tidak bisa dihapus karena sudah pernah melakukan penilaian atau penetapan bonus. Nonaktifkan saja.'); window.location='index.php';</script>";
        exit;
    }
    $query = "DELETE FROM users WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('User berhasil dihapus.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
} else {
    echo "<script>alert('Aksi tidak dikenal.'); window.location='index.php';</script>";
}
?>