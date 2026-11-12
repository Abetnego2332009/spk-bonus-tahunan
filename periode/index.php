<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Periode Penilaian';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$query = "SELECT * FROM periode_penilaian ORDER BY tahun DESC, id DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title">Periode Penilaian</h4>
                            <a href="tambah.php" class="btn btn-primary btn-sm">+ Tambah Periode</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Periode</th>
                                        <th>Tahun</th>
                                        <th>Target Penjualan</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Akhir</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama_periode']) ?></td>
                                        <td><?= $row['tahun'] ?></td>
                                        <td>Rp <?= number_format($row['target_penjualan'], 0, ',', '.') ?></td>
                                        <td><?= $row['tanggal_mulai'] ? date('d-m-Y', strtotime($row['tanggal_mulai'])) : '-' ?></td>
                                        <td><?= $row['tanggal_akhir'] ? date('d-m-Y', strtotime($row['tanggal_akhir'])) : '-' ?></td>
                                        <td>
                                            <?php if ($row['is_active'] == 1) : ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else : ?>
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['is_active'] != 1) : ?>
                                                <a href="proses.php?action=set_aktif&id=<?= $row['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Set periode ini sebagai aktif?')">Set Aktif</a>
                                            <?php endif; ?>
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="proses.php?action=hapus&id=<?= $row['id'] ?>&nama=<?= urlencode($row['nama_periode']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($result) == 0) : ?>
                                    <tr><td colspan="8" class="text-center">Belum ada periode penilaian</td><tr>
                                    <?php endif; ?>
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