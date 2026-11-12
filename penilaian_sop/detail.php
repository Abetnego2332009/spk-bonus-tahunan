<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Detail Nilai SOP';
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

// Ambil data penilaian dan karyawan
$query = "SELECT p.id, p.nilai_sop, k.nama_lengkap, j.nama_jabatan 
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

// Ambil detail sub kriteria dan nilai
$detail_query = "SELECT d.nilai, s.nama_sub_kriteria 
                 FROM detail_penilaian_sop d
                 JOIN sub_kriteria s ON d.sub_kriteria_id = s.id
                 WHERE d.penilaian_id = $id
                 ORDER BY s.urutan";
$detail_result = mysqli_query($koneksi, $detail_query);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Detail Penilaian SOP</h4>
                        <p><strong>Nama Karyawan:</strong> <?= htmlspecialchars($data['nama_lengkap']) ?></p>
                        <p><strong>Jabatan:</strong> <?= htmlspecialchars($data['nama_jabatan']) ?></p>
                        <p><strong>Nilai Akhir SOP:</strong> <?= $data['nilai_sop'] . '%' ?></p>
                        <h5>Rincian Sub Kriteria</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>Sub Kriteria</th><th>Nilai</th></tr>
                            </thead>
                            <tbody>
                                <?php while ($det = mysqli_fetch_assoc($detail_result)) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($det['nama_sub_kriteria']) ?></td>
                                    <td><?= $det['nilai'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <a href="index.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once '../template/footer.php'; ?>
</div>