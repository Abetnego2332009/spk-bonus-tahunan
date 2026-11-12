<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Manajemen User';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

require_once __DIR__ . '/../auth/auth_pimpinan.php'; 

$query = "SELECT u.*, k.nama_lengkap FROM users u LEFT JOIN karyawan k ON u.karyawan_id = k.id ORDER BY u.id DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title">Manajemen User</h4>
                            <a href="tambah.php" class="btn btn-primary btn-sm">+ Tambah User</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Nama Karyawan</th>
                                        <th>Status</th>
                                        <th>Terakhir Login</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['username']) ?></td>
                                        <td><?= $row['role'] == 'kepala_toko' ? 'Kepala Toko' : 'Pimpinan' ?></td>
                                        <td><?= $row['nama_lengkap'] ? htmlspecialchars($row['nama_lengkap']) : '-' ?></td>
                                        <td>
                                            <?php if ($row['is_active'] == 1): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $row['last_login'] ? date('d-m-Y H:i', strtotime($row['last_login'])) : '-' ?></td>
                                        <td>
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="proses.php?action=hapus&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini? Data terkait (penilaian, bonus) mungkin akan bermasalah.')">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if(mysqli_num_rows($result) == 0): ?>
                                    <tr><td colspan="7" class="text-center">Belum ada user.<?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once '../template/footer.php'; ?>
</div>