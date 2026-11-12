<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$periode_id = isset($_POST['periode_id']) ? (int)$_POST['periode_id'] : (isset($_GET['periode_id']) ? (int)$_GET['periode_id'] : 0);

if ($action == 'tambah') {
    $periode_id = (int)$_POST['periode_id'];
    $ranking = (int)$_POST['ranking'];
    $jumlah_bonus = (float)$_POST['jumlah_bonus'];

    if ($periode_id <= 0 || $ranking <= 0 || $jumlah_bonus < 0) {
        echo "<script>alert('Data tidak valid.'); window.location='tambah.php?periode_id=$periode_id';</script>";
        exit;
    }

    // Cek duplikat ranking untuk periode yang sama
    $cek = mysqli_query($koneksi, "SELECT id FROM bonus_aturan WHERE periode_id = $periode_id AND ranking = $ranking");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Aturan untuk peringkat $ranking sudah ada.'); window.location='tambah.php?periode_id=$periode_id';</script>";
        exit;
    }

    $query = "INSERT INTO bonus_aturan (periode_id, ranking, jumlah_bonus) VALUES ($periode_id, $ranking, $jumlah_bonus)";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Aturan bonus berhasil ditambahkan.'); window.location='index.php?periode_id=$periode_id';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "'); window.location='tambah.php?periode_id=$periode_id';</script>";
    }
} elseif ($action == 'edit') {
    $id = (int)$_POST['id'];
    $periode_id = (int)$_POST['periode_id'];
    $ranking = (int)$_POST['ranking'];
    $jumlah_bonus = (float)$_POST['jumlah_bonus'];

    if ($id <= 0 || $periode_id <= 0 || $ranking <= 0 || $jumlah_bonus < 0) {
        echo "<script>alert('Data tidak valid.'); window.location='edit.php?id=$id';</script>";
        exit;
    }

    // Cek duplikat kecuali dirinya sendiri
    $cek = mysqli_query($koneksi, "SELECT id FROM bonus_aturan WHERE periode_id = $periode_id AND ranking = $ranking AND id != $id");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Aturan untuk peringkat $ranking sudah ada.'); window.location='edit.php?id=$id';</script>";
        exit;
    }

    $query = "UPDATE bonus_aturan SET ranking = $ranking, jumlah_bonus = $jumlah_bonus WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Aturan bonus berhasil diupdate.'); window.location='index.php?periode_id=$periode_id';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($koneksi) . "'); window.location='edit.php?id=$id';</script>";
    }
} elseif ($action == 'hapus') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $periode_id = isset($_GET['periode_id']) ? (int)$_GET['periode_id'] : 0;
    if ($id <= 0) {
        echo "<script>alert('ID tidak valid.'); window.location='index.php';</script>";
        exit;
    }
    $query = "DELETE FROM bonus_aturan WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Aturan bonus berhasil dihapus.'); window.location='index.php?periode_id=$periode_id';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
} else {
    echo "<script>alert('Aksi tidak dikenal.'); window.location='index.php';</script>";
}
?>