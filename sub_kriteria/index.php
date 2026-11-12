<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Data Sub Kriteria';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$query = "SELECT sk.*, k.nama_kriteria, k.kode_kriteria 
          FROM sub_kriteria sk 
          LEFT JOIN kriteria k ON sk.kriteria_id = k.id 
          ORDER BY sk.kriteria_id, sk.urutan, sk.id";
$result = mysqli_query($koneksi, $query);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title">Data Sub Kriteria</h4>
                            <a href="tambah.php" class="btn btn-primary btn-sm">+ Tambah Sub Kriteria</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Kriteria</th>
                                        <th>Nama Sub Kriteria</th>
                                        <th>Urutan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) :
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['kode_kriteria'] . ' - ' . $row['nama_kriteria']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_sub_kriteria']) ?></td>
                                        <td><?= $row['urutan'] ?></td>
                                        <td>
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="proses.php?action=hapus&id=<?= $row['id'] ?>&nama=<?= urlencode($row['nama_sub_kriteria']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus sub kriteria ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($result) == 0) : ?>
                                    <tr><td colspan="5" class="text-center">Belum ada data sub kriteria</td></tr>
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