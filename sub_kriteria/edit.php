<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Edit Sub Kriteria';
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

$query = "SELECT * FROM sub_kriteria WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location='index.php';</script>";
    exit;
}

$queryKriteria = "SELECT id, kode_kriteria, nama_kriteria FROM kriteria ORDER BY kode_kriteria";
$resultKriteria = mysqli_query($koneksi, $queryKriteria);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Sub Kriteria</h4>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Kriteria Induk</label>
                                <select name="kriteria_id" class="form-control" required>
                                    <?php while($k = mysqli_fetch_assoc($resultKriteria)) : ?>
                                        <option value="<?= $k['id'] ?>" <?= ($k['id'] == $data['kriteria_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($k['kode_kriteria'] . ' - ' . $k['nama_kriteria']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Sub Kriteria</label>
                                <input type="text" name="nama_sub_kriteria" class="form-control" required value="<?= htmlspecialchars($data['nama_sub_kriteria']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Urutan Tampil</label>
                                <input type="number" name="urutan" class="form-control" value="<?= $data['urutan'] ?>">
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