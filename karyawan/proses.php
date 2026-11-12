<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if ($action == 'tambah') {
    // Proses tambah data
    $nik = trim(htmlspecialchars($_POST['nik']));
    $nama_lengkap = trim(htmlspecialchars($_POST['nama_lengkap']));
    $jabatan_id = (int)$_POST['jabatan_id'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $status_karyawan = $_POST['status_karyawan'];

    // Validasi sederhana
    if (empty($nik) || empty($nama_lengkap) || $jabatan_id <= 0) {
        echo "<script>alert('Data tidak lengkap'); window.location='tambah.php';</script>";
        exit;
    }

    // Cek NIK unik
    $cek = mysqli_query($koneksi, "SELECT id FROM karyawan WHERE nik = '$nik'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIK sudah terdaftar!'); window.location='tambah.php';</script>";
        exit;
    }

    $query = "INSERT INTO karyawan (nik, nama_lengkap, jabatan_id, jenis_kelamin, tanggal_masuk, status_karyawan) 
              VALUES ('$nik', '$nama_lengkap', $jabatan_id, '$jenis_kelamin', '$tanggal_masuk', '$status_karyawan')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil ditambahkan'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data: " . mysqli_error($koneksi) . "'); window.location='tambah.php';</script>";
    }

} elseif ($action == 'edit') {
    // Proses edit data
    $id = (int)$_POST['id'];
    $nik = trim(htmlspecialchars($_POST['nik']));
    $nama_lengkap = trim(htmlspecialchars($_POST['nama_lengkap']));
    $jabatan_id = (int)$_POST['jabatan_id'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $status_karyawan = $_POST['status_karyawan'];

    if ($id <= 0) {
        echo "<script>alert('ID tidak valid'); window.location='index.php';</script>";
        exit;
    }

    // Cek NIK unik kecuali dirinya sendiri
    $cek = mysqli_query($koneksi, "SELECT id FROM karyawan WHERE nik = '$nik' AND id != $id");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIK sudah digunakan karyawan lain!'); window.location='edit.php?id=$id';</script>";
        exit;
    }

    $query = "UPDATE karyawan SET 
              nik = '$nik',
              nama_lengkap = '$nama_lengkap',
              jabatan_id = $jabatan_id,
              jenis_kelamin = '$jenis_kelamin',
              tanggal_masuk = '$tanggal_masuk',
              status_karyawan = '$status_karyawan'
              WHERE id = $id";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil diupdate'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($koneksi) . "'); window.location='edit.php?id=$id';</script>";
    }

} elseif ($action == 'hapus') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        echo "<script>alert('ID tidak valid'); window.location='index.php';</script>";
        exit;
    }

    // Cek apakah karyawan memiliki relasi di tabel penilaian, bonus, atau users
    $cekPenilaian = mysqli_query($koneksi, "SELECT id FROM penilaian WHERE karyawan_id = $id LIMIT 1");
    $cekBonus = mysqli_query($koneksi, "SELECT id FROM bonus WHERE karyawan_id = $id LIMIT 1");
    $cekUsers = mysqli_query($koneksi, "SELECT id FROM users WHERE karyawan_id = $id LIMIT 1");
    
    if (mysqli_num_rows($cekPenilaian) > 0 || mysqli_num_rows($cekBonus) > 0 || mysqli_num_rows($cekUsers) > 0) {
        echo "<script>alert('Tidak bisa menghapus karyawan karena sudah memiliki data penilaian, bonus, atau user terkait!'); window.location='index.php';</script>";
        exit;
    }

    $query = "DELETE FROM karyawan WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil dihapus'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
} else {
    echo "<script>alert('Aksi tidak dikenal'); window.location='index.php';</script>";
}
?>