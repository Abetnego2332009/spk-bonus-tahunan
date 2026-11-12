<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if ($action == 'edit') {
    $penilaian_id = (int)$_POST['penilaian_id'];
    $nilai_audit = (int)$_POST['nilai_audit'];
    $dinilai_oleh = (int)$_SESSION['user_id'];

    if ($penilaian_id <= 0 || $nilai_audit < 0 || $nilai_audit > 100) {
        echo "<script>alert('Data tidak valid. Nilai harus antara 0-100.'); window.location='edit.php?id=$penilaian_id';</script>";
        exit;
    }

    // Validasi user
    $user = mysqli_query($koneksi, "SELECT id FROM users WHERE id = $dinilai_oleh");
    if (mysqli_num_rows($user) == 0) {
        $alt = mysqli_query($koneksi, "SELECT id FROM users WHERE role = 'kepala_toko' LIMIT 1");
        $dinilai_oleh = $alt ? mysqli_fetch_assoc($alt)['id'] : 0;
    }

    $query = "UPDATE penilaian SET nilai_audit = $nilai_audit, dinilai_oleh = $dinilai_oleh WHERE id = $penilaian_id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Nilai audit berhasil diupdate.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($koneksi) . "'); window.location='edit.php?id=$penilaian_id';</script>";
    }
} elseif ($action == 'hapus') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        echo "<script>alert('ID tidak valid.'); window.location='index.php';</script>";
        exit;
    }
    $query = "DELETE FROM penilaian WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data audit berhasil dihapus.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
} else {
    echo "<script>alert('Aksi tidak dikenal.'); window.location='index.php';</script>";
}
?>