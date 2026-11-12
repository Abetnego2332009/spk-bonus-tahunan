<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Data Kriteria';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

require_once __DIR__ . '/../auth/auth_pimpinan.php'; 

// Ambil data kriteria
$query = "SELECT * FROM kriteria ORDER BY kode_kriteria ASC" ;
$result = mysqli_query($koneksi, $query);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title">Data Kriteria & Bobot</h4>
                            <a href="tambah.php" class="btn btn-primary btn-sm">+ Tambah Kriteria</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama Kriteria</th>
                                        <th>Bobot (%)</th>
                                        <th>Jenis</th>
                                        <th>Keterangan</th>
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
                                            <td><?= htmlspecialchars($row['kode_kriteria']) ?></td>
                                            <td><?= htmlspecialchars($row['nama_kriteria']) ?></td>
                                            <td><?= $row['bobot'] ?>%</td>
                                            <td><?= ucfirst($row['jenis']) ?></td>
                                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                            <td>
                                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="proses.php?action=hapus&id=<?= $row['id'] ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus kriteria ini? Jika terkait data lain, sistem akan memberitahu.')">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($result) == 0) : ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada data kriteria</td>
                                        </tr>
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