<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Tambah User';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

require_once __DIR__ . '/../auth/auth_pimpinan.php'; 

// Ambil karyawan aktif untuk dihubungkan ke user (opsional)
$karyawan_list = mysqli_query($koneksi, "SELECT id, nama_lengkap FROM karyawan WHERE status_karyawan = 'aktif' ORDER BY nama_lengkap");
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah User</h4>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="tambah">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="kepala_toko">Kepala Toko</option>
                                    <option value="pimpinan">Pimpinan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hubungkan ke Karyawan (Opsional)</label>
                                <select name="karyawan_id" class="form-control">
                                    <option value="">-- Tidak dihubungkan --</option>
                                    <?php while($k = mysqli_fetch_assoc($karyawan_list)): ?>
                                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_lengkap']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <small class="text-muted">Jika user adalah karyawan, pilih namanya.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status Aktif</label>
                                <select name="is_active" class="form-control">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
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