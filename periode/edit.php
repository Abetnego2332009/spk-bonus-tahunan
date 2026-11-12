<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Edit Periode';
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

$query = "SELECT * FROM periode_penilaian WHERE id = $id";
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
                        <h4 class="card-title">Edit Periode Penilaian</h4>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Nama Periode</label>
                                <input type="text" name="nama_periode" class="form-control" required value="<?= htmlspecialchars($data['nama_periode']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun</label>
                                <input type="number" name="tahun" class="form-control" required value="<?= $data['tahun'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control" value="<?= $data['tanggal_mulai'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control" value="<?= $data['tanggal_akhir'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status Aktif</label>
                                <select name="is_active" class="form-control">
                                    <option value="0" <?= ($data['is_active'] == 0) ? 'selected' : '' ?>>Tidak Aktif</option>
                                    <option value="1" <?= ($data['is_active'] == 1) ? 'selected' : '' ?>>Aktif</option>
                                </select>
                            </div>
                            <!-- TAMBAHKAN INPUT TARGET PENJUALAN -->
                            <div class="mb-3">
                                <label class="form-label">Target Penjualan Periode (Rp)</label>
                                <input type="number" name="target_penjualan" class="form-control" required min="0" step="1000000" value="<?= $data['target_penjualan'] ?>">
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