<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Edit Kriteria';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

require_once __DIR__ . '/../auth/auth_pimpinan.php'; 

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<script>alert('ID tidak valid'); window.location='index.php';</script>";
    exit;
}

// Ambil data kriteria
$query = "SELECT * FROM kriteria WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location='index.php';</script>";
    exit;
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Kriteria</h4>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Kode Kriteria</label>
                                <input type="text" name="kode_kriteria" class="form-control" required value="<?= htmlspecialchars($data['kode_kriteria']) ?>" maxlength="10">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Kriteria</label>
                                <input type="text" name="nama_kriteria" class="form-control" required value="<?= htmlspecialchars($data['nama_kriteria']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Bobot (%)</label>
                                <input type="number" step="0.01" name="bobot" class="form-control" required value="<?= $data['bobot'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis</label>
                                <select name="jenis" class="form-control" required>
                                    <option value="benefit" <?= ($data['jenis'] == 'benefit') ? 'selected' : '' ?>>Benefit</option>
                                    <option value="cost" <?= ($data['jenis'] == 'cost') ? 'selected' : '' ?>>Cost</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($data['keterangan']) ?></textarea>
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