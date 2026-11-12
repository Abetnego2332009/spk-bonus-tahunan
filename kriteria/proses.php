<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fungsi menghitung total bobot semua kriteria (opsional exclude ID)
function getTotalBobot($koneksi, $excludeId = null) {
    $sql = "SELECT SUM(bobot) as total FROM kriteria";
    if ($excludeId !== null) {
        $sql .= " WHERE id != " . (int)$excludeId;
    }
    $result = mysqli_query($koneksi, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ? (float)$row['total'] : 0;
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if ($action == 'tambah') {
    $kode = trim(htmlspecialchars($_POST['kode_kriteria']));
    $nama = trim(htmlspecialchars($_POST['nama_kriteria']));
    $bobot = (float)$_POST['bobot'];
    $jenis = $_POST['jenis'];
    $keterangan = trim(htmlspecialchars($_POST['keterangan']));

    if (empty($kode) || empty($nama) || $bobot < 0) {
        echo "<script>alert('Data tidak lengkap atau bobot negatif!'); window.location='tambah.php';</script>";
        exit;
    }

    // Cek kode unik
    $cek = mysqli_query($koneksi, "SELECT id FROM kriteria WHERE kode_kriteria = '$kode'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Kode kriteria sudah digunakan!'); window.location='tambah.php';</script>";
        exit;
    }

    // Hitung total bobot saat ini + bobot baru
    $totalSaatIni = getTotalBobot($koneksi);
    $totalBaru = $totalSaatIni + $bobot;

    if ($totalBaru != 100) {
        $selisih = 100 - $totalSaatIni;
        echo "<script>
            alert('Total bobot kriteria harus 100%!\\nSaat ini total bobot: {$totalSaatIni}%\\nBobot baru: {$bobot}%\\nTotal menjadi: {$totalBaru}%\\n\\nSilakan atur bobot kriteria yang sudah ada terlebih dahulu (gunakan menu Edit).\\nSetelah total kurang dari 100%, baru tambah kriteria baru dengan bobot yang sesuai.');
            window.location='tambah.php';
        </script>";
        exit;
    }

    $query = "INSERT INTO kriteria (kode_kriteria, nama_kriteria, bobot, jenis, keterangan) 
              VALUES ('$kode', '$nama', $bobot, '$jenis', '$keterangan')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil ditambahkan'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan: " . mysqli_error($koneksi) . "'); window.location='tambah.php';</script>";
    }

} elseif ($action == 'edit') {
    $id = (int)$_POST['id'];
    $kode = trim(htmlspecialchars($_POST['kode_kriteria']));
    $nama = trim(htmlspecialchars($_POST['nama_kriteria']));
    $bobot = (float)$_POST['bobot'];
    $jenis = $_POST['jenis'];
    $keterangan = trim(htmlspecialchars($_POST['keterangan']));

    if ($id <= 0) {
        echo "<script>alert('ID tidak valid'); window.location='index.php';</script>";
        exit;
    }
    if ($bobot < 0) {
        echo "<script>alert('Bobot tidak boleh negatif!'); window.location='edit.php?id=$id';</script>";
        exit;
    }

    // Cek kode unik kecuali diri sendiri
    $cek = mysqli_query($koneksi, "SELECT id FROM kriteria WHERE kode_kriteria = '$kode' AND id != $id");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Kode kriteria sudah digunakan kriteria lain!'); window.location='edit.php?id=$id';</script>";
        exit;
    }

    // Update data terlebih dahulu
    $query = "UPDATE kriteria SET 
              kode_kriteria = '$kode',
              nama_kriteria = '$nama',
              bobot = $bobot,
              jenis = '$jenis',
              keterangan = '$keterangan'
              WHERE id = $id";
    
    if (mysqli_query($koneksi, $query)) {
        // Setelah update, hitung total semua bobot
        $totalSemua = getTotalBobot($koneksi);
        if ($totalSemua != 100) {
            echo "<script>
                alert('Data berhasil diupdate, tetapi PERHATIAN: Total bobot semua kriteria sekarang menjadi {$totalSemua}% (bukan 100%).\\nSilakan edit kriteria lain atau tambah kriteria baru agar total kembali 100%.');
                window.location='index.php';
            </script>";
        } else {
            echo "<script>alert('Data berhasil diupdate (total bobot 100%).'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($koneksi) . "'); window.location='edit.php?id=$id';</script>";
    }

} elseif ($action == 'hapus') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        echo "<script>alert('ID tidak valid.'); window.location='index.php';</script>";
        exit;
    }

    // Ambil nama kriteria untuk pesan yang lebih jelas
    $queryNama = "SELECT nama_kriteria FROM kriteria WHERE id = $id";
    $resultNama = mysqli_query($koneksi, $queryNama);
    $namaKriteria = "kriteria ini";
    if ($row = mysqli_fetch_assoc($resultNama)) {
        $namaKriteria = $row['nama_kriteria'];
    }

    // Cek apakah masih ada rincian (sub kriteria)
    $cekSub = mysqli_query($koneksi, "SELECT id FROM sub_kriteria WHERE kriteria_id = $id LIMIT 1");
    if (mysqli_num_rows($cekSub) > 0) {
        echo "<script>alert('Kriteria \"$namaKriteria\" tidak bisa dihapus karena masih memiliki data rincian penilaian.\\n\\nLangkah: Hapus dulu rincian melalui menu \"Sub Kriteria (SOP)\", lalu hapus kriteria ini.'); window.location='index.php';</script>";
        exit;
    }

    $totalSetelahHapus = getTotalBobot($koneksi, $id);
    $query = "DELETE FROM kriteria WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        if ($totalSetelahHapus != 100) {
            echo "<script>alert('Kriteria \"$namaKriteria\" berhasil dihapus. Sekarang total bobot menjadi {$totalSetelahHapus}%. Silakan edit kriteria lain agar total 100%.'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Kriteria \"$namaKriteria\" berhasil dihapus.'); window.location='index.php';</script>";
        }
    } else {
        // Cek apakah gagal karena sudah terpakai di penilaian
        $error = mysqli_error($koneksi);
        if (strpos($error, 'foreign key') !== false) {
            echo "<script>alert('Kriteria \"$namaKriteria\" tidak bisa dihapus karena sudah pernah digunakan dalam penilaian karyawan.\\n\\nSolusi: Tidak perlu dihapus. Cukup ubah bobotnya menjadi 0 atau nonaktifkan melalui menu Edit.'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Gagal hapus: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
        }
    }
} else {
    echo "<script>alert('Aksi tidak dikenal'); window.location='index.php';</script>";
}
?>