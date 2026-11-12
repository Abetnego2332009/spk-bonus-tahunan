<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Daftar Target Penjualan';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil periode aktif
$periode = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id, nama_periode, tahun, target_penjualan FROM periode_penilaian WHERE is_active = 1 LIMIT 1"));
if (!$periode) {
    echo "<div class='main-panel'><div class='content-wrapper'><div class='alert alert-warning'>Belum ada periode penilaian yang aktif.</div></div></div>";
    require_once '../template/footer.php';
    exit;
}
$periode_id = $periode['id'];
$target_nominal = $periode['target_penjualan'];

// Ambil semua penilaian target untuk periode aktif
$query = "SELECT p.id, p.nilai_target_penjualan, p.penjualan_aktual, k.nama_lengkap, j.nama_jabatan 
          FROM penilaian p
          JOIN karyawan k ON p.karyawan_id = k.id
          LEFT JOIN jabatan j ON k.jabatan_id = j.id
          WHERE p.periode_id = $periode_id
          ORDER BY k.nama_lengkap";
$result = mysqli_query($koneksi, $query);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title">Daftar Target Penjualan</h4>
                            <!-- <a href="tambah.php" class="btn btn-primary btn-sm">+ Tambah Target</a> -->
                        </div>
                        <p>Periode aktif: <strong><?= htmlspecialchars($periode['nama_periode'] . ' (' . $periode['tahun'] . ')') ?></strong></p>
                        <p>Target penjualan periode: <strong>Rp <?= number_format($target_nominal, 0, ',', '.') ?></strong></p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Karyawan</th>
                                        <th>Jabatan</th>
                                        <th>Penjualan Aktual</th>
                                        <th>Nilai (%)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) : ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                            <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
                                            <td>
                                                <?= 'Rp ' . number_format($row['penjualan_aktual'] ?? 0, 0, ',', '.') ?>
                                            </td>
                                            <td><?= $row['nilai_target_penjualan'] !== null ? $row['nilai_target_penjualan'] . '%' : '0' .'%' ?></td>
                                            <td>
                                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="proses.php?action=hapus&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin Ingin Menghapus data audit ini? \n\ Data Dari Nilai SOP Dan Hasil Audit Juga Akan Ikut Terhapus')">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($result) == 0) : ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada data target penjualan. Silakan tambah.
                        </div>
                        </td>
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