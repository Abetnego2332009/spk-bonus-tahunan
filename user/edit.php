<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Edit User';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

require_once __DIR__ . '/../auth/auth_pimpinan.php'; 

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<script>alert('ID tidak valid.'); window.location='index.php';</script>";
    exit;
}

$query = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
if (!$data) {
    echo "<script>alert('User tidak ditemukan.'); window.location='index.php';</script>";
    exit;
}

$karyawan_list = mysqli_query($koneksi, "SELECT id, nama_lengkap FROM karyawan WHERE status_karyawan = 'aktif' ORDER BY nama_lengkap");
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit User</h4>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($data['username']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Baru (Kosongkan jika tidak diubah)</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="kepala_toko" <?= $data['role'] == 'kepala_toko' ? 'selected' : '' ?>>Kepala Toko</option>
                                    <option value="pimpinan" <?= $data['role'] == 'pimpinan' ? 'selected' : '' ?>>Pimpinan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hubungkan ke Karyawan</label>
                                <select name="karyawan_id" class="form-control">
                                    <option value="">-- Tidak dihubungkan --</option>
                                    <?php while($k = mysqli_fetch_assoc($karyawan_list)): ?>
                                        <option value="<?= $k['id'] ?>" <?= ($data['karyawan_id'] == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_lengkap']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status Aktif</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" <?= $data['is_active'] == 1 ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= $data['is_active'] == 0 ? 'selected' : '' ?>>Nonaktif</option>
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