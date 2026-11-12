<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if ($action == 'tambah') {
    $karyawan_id = (int)$_POST['karyawan_id'];
    $periode_id = (int)$_POST['periode_id'];
    $penjualan_aktual = (float)$_POST['penjualan_aktual'];
    $dinilai_oleh = (int)$_SESSION['user_id'];

    if ($karyawan_id <= 0 || $periode_id <= 0 || $penjualan_aktual < 0) {
        echo "<script>alert('Data tidak valid.'); window.location='tambah.php';</script>";
        exit;
    }

    // Cek apakah sudah ada nilai target penjualan untuk karyawan & periode ini
    $cek = mysqli_query($koneksi, "SELECT id FROM penilaian WHERE karyawan_id = $karyawan_id AND periode_id = $periode_id AND nilai_target_penjualan IS NOT NULL");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Target penjualan untuk karyawan ini sudah ada. Silakan edit.'); window.location='tambah.php';</script>";
        exit;
    }

    // Validasi periode aktif
    $cek_periode = mysqli_query($koneksi, "SELECT target_penjualan FROM periode_penilaian WHERE id = $periode_id AND is_active = 1");
    if (mysqli_num_rows($cek_periode) == 0) {
        echo "<script>alert('Periode tidak aktif.'); window.location='tambah.php';</script>";
        exit;
    }
    $target = mysqli_fetch_assoc($cek_periode)['target_penjualan'];

    $nilai_target = round(($penjualan_aktual / $target) * 100, 2);

    // Validasi user
    $user = mysqli_query($koneksi, "SELECT id FROM users WHERE id = $dinilai_oleh");
    if (mysqli_num_rows($user) == 0) {
        $alt = mysqli_query($koneksi, "SELECT id FROM users WHERE role = 'kepala_toko' LIMIT 1");
        $dinilai_oleh = $alt ? mysqli_fetch_assoc($alt)['id'] : 0;
    }

    // Cek apakah record penilaian sudah ada (misal dari SOP)
    $cek_record = mysqli_query($koneksi, "SELECT id FROM penilaian WHERE karyawan_id = $karyawan_id AND periode_id = $periode_id");
    if (mysqli_num_rows($cek_record) == 0) {
        $query = "INSERT INTO penilaian (karyawan_id, periode_id, dinilai_oleh, penjualan_aktual, nilai_target_penjualan) 
                  VALUES ($karyawan_id, $periode_id, $dinilai_oleh, $penjualan_aktual, $nilai_target)";
    } else {
        $row = mysqli_fetch_assoc($cek_record);
        $pen_id = $row['id'];
        $query = "UPDATE penilaian SET penjualan_aktual = $penjualan_aktual, nilai_target_penjualan = $nilai_target, dinilai_oleh = $dinilai_oleh WHERE id = $pen_id";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Target penjualan berhasil disimpan. Nilai: $nilai_target%'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "'); window.location='tambah.php';</script>";
    }

} elseif ($action == 'edit') {
    $penilaian_id = (int)$_POST['penilaian_id'];
    $penjualan_aktual = (float)$_POST['penjualan_aktual'];
    $dinilai_oleh = (int)$_SESSION['user_id'];

    if ($penilaian_id <= 0 || $penjualan_aktual < 0) {
        echo "<script>alert('Data tidak valid.'); window.location='edit.php?id=$penilaian_id';</script>";
        exit;
    }

    // Validasi periode aktif (ambil target dari periode terkait)
    $cek_pen = mysqli_query($koneksi, "SELECT periode_id FROM penilaian WHERE id = $penilaian_id");
    if (!$cek_pen) {
        echo "<script>alert('Data penilaian tidak ditemukan.'); window.location='index.php';</script>";
        exit;
    }
    $periode_id = mysqli_fetch_assoc($cek_pen)['periode_id'];
    $cek_periode = mysqli_query($koneksi, "SELECT target_penjualan FROM periode_penilaian WHERE id = $periode_id AND is_active = 1");
    if (mysqli_num_rows($cek_periode) == 0) {
        echo "<script>alert('Periode tidak aktif.'); window.location='edit.php?id=$penilaian_id';</script>";
        exit;
    }
    $target = mysqli_fetch_assoc($cek_periode)['target_penjualan'];
    $nilai_target = round(($penjualan_aktual / $target) * 100, 2);

    // Validasi user
    $user = mysqli_query($koneksi, "SELECT id FROM users WHERE id = $dinilai_oleh");
    if (mysqli_num_rows($user) == 0) {
        $alt = mysqli_query($koneksi, "SELECT id FROM users WHERE role = 'kepala_toko' LIMIT 1");
        $dinilai_oleh = $alt ? mysqli_fetch_assoc($alt)['id'] : 0;
    }

    $query = "UPDATE penilaian SET penjualan_aktual = $penjualan_aktual, nilai_target_penjualan = $nilai_target, dinilai_oleh = $dinilai_oleh WHERE id = $penilaian_id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Target penjualan berhasil diupdate. Nilai: $nilai_target%'); window.location='index.php';</script>";
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
        echo "<script>alert('Data target penjualan berhasil dihapus.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
} else {
    echo "<script>alert('Aksi tidak dikenal.'); window.location='index.php';</script>";
}
?>