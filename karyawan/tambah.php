<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Tambah Karyawan';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data jabatan untuk dropdown
$queryJabatan = "SELECT id, nama_jabatan FROM jabatan ORDER BY nama_jabatan";
$resultJabatan = mysqli_query($koneksi, $queryJabatan);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah Karyawan Baru</h4>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="tambah">
                            
                            <div class="mb-3">
                                <label class="form-label">NIK</label>
                                <input type="text" name="nik" class="form-control" required maxlength="20">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jabatan</label>
                                <select name="jabatan_id" class="form-control" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <?php while($jab = mysqli_fetch_assoc($resultJabatan)) : ?>
                                        <option value="<?= $jab['id'] ?>"><?= htmlspecialchars($jab['nama_jabatan']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status Karyawan</label>
                                <select name="status_karyawan" class="form-control" required>
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once '../template/footer.php'; ?>
</div>