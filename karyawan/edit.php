<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Edit Karyawan';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<script>alert('ID tidak valid'); window.location='index.php';</script>";
    exit;
}

// Ambil data karyawan
$query = "SELECT * FROM karyawan WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location='index.php';</script>";
    exit;
}

// Ambil data jabatan
$queryJabatan = "SELECT id, nama_jabatan FROM jabatan ORDER BY nama_jabatan";
$resultJabatan = mysqli_query($koneksi, $queryJabatan);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Karyawan</h4>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">NIK</label>
                                <input type="text" name="nik" class="form-control" required value="<?= htmlspecialchars($data['nik']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" required value="<?= htmlspecialchars($data['nama_lengkap']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jabatan</label>
                                <select name="jabatan_id" class="form-control" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <?php while($jab = mysqli_fetch_assoc($resultJabatan)) : ?>
                                        <option value="<?= $jab['id'] ?>" <?= ($jab['id'] == $data['jabatan_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($jab['nama_jabatan']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="Laki-laki" <?= ($data['jenis_kelamin'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= ($data['jenis_kelamin'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" class="form-control" required value="<?= $data['tanggal_masuk'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status Karyawan</label>
                                <select name="status_karyawan" class="form-control" required>
                                    <option value="aktif" <?= ($data['status_karyawan'] == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                    <option value="nonaktif" <?= ($data['status_karyawan'] == 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once '../template/footer.php'; ?>
</div>