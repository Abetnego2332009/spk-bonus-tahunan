<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Edit Nilai Audit';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<script>alert('ID tidak valid.'); window.location='index.php';</script>";
    exit;
}

// Ambil data penilaian
$query = "SELECT p.id, p.karyawan_id, p.nilai_audit, k.nama_lengkap, j.nama_jabatan 
          FROM penilaian p
          JOIN karyawan k ON p.karyawan_id = k.id
          LEFT JOIN jabatan j ON k.jabatan_id = j.id
          WHERE p.id = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
if (!$data) {
    echo "<script>alert('Data tidak ditemukan.'); window.location='index.php';</script>";
    exit;
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Nilai Audit</h4>
                        <p><strong>Karyawan:</strong> <?= htmlspecialchars($data['nama_lengkap'] . ' - ' . $data['nama_jabatan']) ?></p>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="penilaian_id" value="<?= $id ?>">
                            <div class="mb-3">
                                <label class="form-label">Nilai Audit (0-100)</label>
                                <input type="number" name="nilai_audit" class="form-control" required min="0" max="100" step="1" value="<?= htmlspecialchars($data['nilai_audit'] ?? '') ?>">
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Update</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once '../template/footer.php'; ?>
</div>