<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Data Karyawan';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

// Cek login (opsional, sesuai auth Anda)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data karyawan dengan join jabatan
$query = "SELECT k.*, j.nama_jabatan 
          FROM karyawan k 
          LEFT JOIN jabatan j ON k.jabatan_id = j.id 
          ORDER BY k.id DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title">Data Karyawan</h4>
                            <a href="tambah.php" class="btn btn-primary btn-sm">+ Tambah Karyawan</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jabatan</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Status</th>
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
                                        <td><?= htmlspecialchars($row['nik']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
                                        <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                                        <td><?= date('d-m-Y', strtotime($row['tanggal_masuk'])) ?></td>
                                        <td>
                                            <?php if ($row['status_karyawan'] == 'aktif') : ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else : ?>
                                                <span class="badge bg-danger">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="proses.php?action=hapus&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($result) == 0) : ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada data karyawan</td>
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