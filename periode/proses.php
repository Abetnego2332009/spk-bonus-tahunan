<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

function nonaktifkanSemuaPeriode($koneksi) {
    mysqli_query($koneksi, "UPDATE periode_penilaian SET is_active = 0");
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if ($action == 'tambah') {
    $nama = trim(htmlspecialchars($_POST['nama_periode']));
    $tahun = (int)$_POST['tahun'];
    $tgl_mulai = !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : null;
    $tgl_akhir = !empty($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : null;
    $is_active = (int)$_POST['is_active'];
    $target_penjualan = (float)$_POST['target_penjualan'];

    if (empty($nama) || $tahun <= 0 || $target_penjualan <= 0) {
        echo "<script>alert('Data tidak lengkap'); window.location='tambah.php';</script>";
        exit;
    }

    if ($is_active == 1) {
        nonaktifkanSemuaPeriode($koneksi);
    }

    $query = "INSERT INTO periode_penilaian (nama_periode, tahun, tanggal_mulai, tanggal_akhir, is_active, target_penjualan) 
              VALUES ('$nama', $tahun, " . ($tgl_mulai ? "'$tgl_mulai'" : "NULL") . ", " . ($tgl_akhir ? "'$tgl_akhir'" : "NULL") . ", $is_active, $target_penjualan)";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Periode berhasil ditambahkan'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "'); window.location='tambah.php';</script>";
    }

} elseif ($action == 'edit') {
    $id = (int)$_POST['id'];
    $nama = trim(htmlspecialchars($_POST['nama_periode']));
    $tahun = (int)$_POST['tahun'];
    $tgl_mulai = !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : null;
    $tgl_akhir = !empty($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : null;
    $is_active = (int)$_POST['is_active'];
    $target_penjualan = (float)$_POST['target_penjualan'];

    if ($id <= 0 || empty($nama) || $tahun <= 0 || $target_penjualan <= 0) {
        echo "<script>alert('Data tidak lengkap'); window.location='edit.php?id=$id';</script>";
        exit;
    }

    if ($is_active == 1) {
        nonaktifkanSemuaPeriode($koneksi);
    }

    $query = "UPDATE periode_penilaian SET 
              nama_periode = '$nama',
              tahun = $tahun,
              tanggal_mulai = " . ($tgl_mulai ? "'$tgl_mulai'" : "NULL") . ",
              tanggal_akhir = " . ($tgl_akhir ? "'$tgl_akhir'" : "NULL") . ",
              is_active = $is_active,
              target_penjualan = $target_penjualan
              WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Periode berhasil diupdate'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($koneksi) . "'); window.location='edit.php?id=$id';</script>";
    }

} elseif ($action == 'set_aktif') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        echo "<script>alert('ID tidak valid'); window.location='index.php';</script>";
        exit;
    }
    nonaktifkanSemuaPeriode($koneksi);
    mysqli_query($koneksi, "UPDATE periode_penilaian SET is_active = 1 WHERE id = $id");
    echo "<script>alert('Periode berhasil diaktifkan'); window.location='index.php';</script>";

} elseif ($action == 'hapus') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $nama = isset($_GET['nama']) ? htmlspecialchars($_GET['nama']) : 'Periode ini';
    if ($id <= 0) {
        echo "<script>alert('ID tidak valid'); window.location='index.php';</script>";
        exit;
    }

    $cek = mysqli_query($koneksi, "SELECT id FROM penilaian WHERE periode_id = $id LIMIT 1");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Periode \"$nama\" tidak bisa dihapus karena sudah memiliki data penilaian.'); window.location='index.php';</script>";
        exit;
    }

    $query = "DELETE FROM periode_penilaian WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Periode \"$nama\" berhasil dihapus'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
} else {
    echo "<script>alert('Aksi tidak dikenal'); window.location='index.php';</script>";
}
?>