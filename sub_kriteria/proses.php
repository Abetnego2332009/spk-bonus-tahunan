<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if ($action == 'tambah') {
    $kriteria_id = (int)$_POST['kriteria_id'];
    $nama = trim(htmlspecialchars($_POST['nama_sub_kriteria']));
    $urutan = isset($_POST['urutan']) && $_POST['urutan'] !== '' ? (int)$_POST['urutan'] : null;

    if ($kriteria_id <= 0 || empty($nama)) {
        echo "<script>alert('Data tidak lengkap!'); window.location='tambah.php';</script>";
        exit;
    }

    $query = "INSERT INTO sub_kriteria (kriteria_id, nama_sub_kriteria, urutan) 
              VALUES ($kriteria_id, '$nama', " . ($urutan ?? 'NULL') . ")";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Sub kriteria berhasil ditambahkan'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "'); window.location='tambah.php';</script>";
    }

} elseif ($action == 'edit') {
    $id = (int)$_POST['id'];
    $kriteria_id = (int)$_POST['kriteria_id'];
    $nama = trim(htmlspecialchars($_POST['nama_sub_kriteria']));
    $urutan = isset($_POST['urutan']) && $_POST['urutan'] !== '' ? (int)$_POST['urutan'] : null;

    if ($id <= 0 || $kriteria_id <= 0 || empty($nama)) {
        echo "<script>alert('Data tidak lengkap!'); window.location='edit.php?id=$id';</script>";
        exit;
    }

    $query = "UPDATE sub_kriteria SET 
              kriteria_id = $kriteria_id,
              nama_sub_kriteria = '$nama',
              urutan = " . ($urutan ?? 'NULL') . "
              WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Sub kriteria berhasil diupdate'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($koneksi) . "'); window.location='edit.php?id=$id';</script>";
    }

} elseif ($action == 'hapus') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $nama = isset($_GET['nama']) ? htmlspecialchars($_GET['nama']) : 'Sub kriteria';
    if ($id <= 0) {
        echo "<script>alert('ID tidak valid'); window.location='index.php';</script>";
        exit;
    }

    // Cek apakah sudah digunakan di detail_penilaian_sop
    $cek = mysqli_query($koneksi, "SELECT id FROM detail_penilaian_sop WHERE sub_kriteria_id = $id LIMIT 1");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Sub kriteria \"$nama\" tidak bisa dihapus karena sudah dipakai dalam penilaian.'); window.location='index.php';</script>";
        exit;
    }

    $query = "DELETE FROM sub_kriteria WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Sub kriteria \"$nama\" berhasil dihapus'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
} else {
    echo "<script>alert('Aksi tidak dikenal'); window.location='index.php';</script>";
}
?>