<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$label_nilai = [
    'Sangat Baik' => 100,
    'Baik' => 85,
    'Cukup' => 75,
    'Kurang' => 65
];

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if ($action == 'tambah') {
    $karyawan_id = (int)$_POST['karyawan_id'];
    $periode_id = (int)$_POST['periode_id'];
    $dinilai_oleh = (int)$_SESSION['user_id'];

    if ($karyawan_id <= 0 || $periode_id <= 0) {
        echo "<script>alert('Data tidak valid.'); window.location='tambah.php';</script>";
        exit;
    }

    // Cek apakah sudah ada penilaian SOP untuk karyawan & periode ini
    $cek = mysqli_query($koneksi, "SELECT id FROM penilaian WHERE karyawan_id = $karyawan_id AND periode_id = $periode_id AND nilai_sop IS NOT NULL");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Nilai SOP untuk karyawan ini sudah ada. Silakan edit.'); window.location='tambah.php';</script>";
        exit;
    }

    // Validasi user
    $user = mysqli_query($koneksi, "SELECT id FROM users WHERE id = $dinilai_oleh");
    if (mysqli_num_rows($user) == 0) {
        $alt = mysqli_query($koneksi, "SELECT id FROM users WHERE role = 'kepala_toko' LIMIT 1");
        if ($alt && mysqli_num_rows($alt) > 0) {
            $dinilai_oleh = mysqli_fetch_assoc($alt)['id'];
        } else {
            echo "<script>alert('User tidak valid.'); window.location='tambah.php';</script>";
            exit;
        }
    }

    // Ambil sub kriteria dan hitung rata-rata
    $subs = mysqli_query($koneksi, "SELECT id FROM sub_kriteria WHERE kriteria_id = 1");
    $total = 0;
    $count = 0;
    while ($sub = mysqli_fetch_assoc($subs)) {
        $label = $_POST['sub_' . $sub['id']] ?? '';
        $nilai = $label_nilai[$label] ?? 0;
        if ($nilai > 0) {
            $total += $nilai;
            $count++;
        } else {
            echo "<script>alert('Penilaian tidak lengkap.'); window.location='tambah.php';</script>";
            exit;
        }
    }
    $rata = round($total / $count, 2);

    // Insert ke penilaian
    $query = "INSERT INTO penilaian (karyawan_id, periode_id, dinilai_oleh, nilai_sop) VALUES ($karyawan_id, $periode_id, $dinilai_oleh, $rata)";
    if (mysqli_query($koneksi, $query)) {
        $penilaian_id = mysqli_insert_id($koneksi);
        // Insert detail
        $subs2 = mysqli_query($koneksi, "SELECT id FROM sub_kriteria WHERE kriteria_id = 1");
        while ($sub = mysqli_fetch_assoc($subs2)) {
            $label = $_POST['sub_' . $sub['id']];
            $nilai = $label_nilai[$label];
            mysqli_query($koneksi, "INSERT INTO detail_penilaian_sop (penilaian_id, sub_kriteria_id, nilai) VALUES ($penilaian_id, {$sub['id']}, $nilai)");
        }
        echo "<script>alert('Nilai SOP berhasil ditambahkan.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($koneksi) . "'); window.location='tambah.php';</script>";
    }

} elseif ($action == 'edit') {
    $penilaian_id = (int)$_POST['penilaian_id'];
    $dinilai_oleh = (int)$_SESSION['user_id'];

    if ($penilaian_id <= 0) {
        echo "<script>alert('ID tidak valid.'); window.location='index.php';</script>";
        exit;
    }

    // Ambil sub kriteria dan hitung rata-rata
    $subs = mysqli_query($koneksi, "SELECT id FROM sub_kriteria WHERE kriteria_id = 1");
    $total = 0;
    $count = 0;
    while ($sub = mysqli_fetch_assoc($subs)) {
        $label = $_POST['sub_' . $sub['id']] ?? '';
        $nilai = $label_nilai[$label] ?? 0;
        if ($nilai > 0) {
            $total += $nilai;
            $count++;
        } else {
            echo "<script>alert('Penilaian tidak lengkap.'); window.location='edit.php?id=$penilaian_id';</script>";
            exit;
        }
    }
    $rata = round($total / $count, 2);

    // Update nilai_sop di penilaian
    $query = "UPDATE penilaian SET nilai_sop = $rata, dinilai_oleh = $dinilai_oleh WHERE id = $penilaian_id";
    if (mysqli_query($koneksi, $query)) {
        // Hapus detail lama
        mysqli_query($koneksi, "DELETE FROM detail_penilaian_sop WHERE penilaian_id = $penilaian_id");
        // Insert detail baru
        $subs2 = mysqli_query($koneksi, "SELECT id FROM sub_kriteria WHERE kriteria_id = 1");
        while ($sub = mysqli_fetch_assoc($subs2)) {
            $label = $_POST['sub_' . $sub['id']];
            $nilai = $label_nilai[$label];
            mysqli_query($koneksi, "INSERT INTO detail_penilaian_sop (penilaian_id, sub_kriteria_id, nilai) VALUES ($penilaian_id, {$sub['id']}, $nilai)");
        }
        echo "<script>alert('Nilai SOP berhasil diupdate.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($koneksi) . "'); window.location='edit.php?id=$penilaian_id';</script>";
    }

} elseif ($action == 'hapus') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        echo "<script>alert('ID tidak valid.'); window.location='index.php';</script>";
        exit;
    }
    // Hapus detail dulu (foreign key cascade, tapi lebih aman manual)
    mysqli_query($koneksi, "DELETE FROM detail_penilaian_sop WHERE penilaian_id = $id");
    $query = "DELETE FROM penilaian WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil dihapus.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
} else {
    echo "<script>alert('Aksi tidak dikenal.'); window.location='index.php';</script>";
}
?>