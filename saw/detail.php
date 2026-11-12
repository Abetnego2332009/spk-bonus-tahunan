<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Detail Perhitungan SAW';
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

// Ambil data detail dari hasil_saw beserta informasi karyawan, periode, dan penilaian
$query = "SELECT hs.*, k.nama_lengkap, j.nama_jabatan, pe.nama_periode, pe.tahun
          FROM hasil_saw hs
          JOIN penilaian p ON hs.penilaian_id = p.id
          JOIN karyawan k ON p.karyawan_id = k.id
          LEFT JOIN jabatan j ON k.jabatan_id = j.id
          JOIN periode_penilaian pe ON hs.periode_id = pe.id
          WHERE hs.id = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
if (!$data) {
    echo "<script>alert('Data tidak ditemukan.'); window.location='index.php';</script>";
    exit;
}

// Ambil bobot kriteria untuk ditampilkan
$bobot = [];
$bobot_query = mysqli_query($koneksi, "SELECT kode_kriteria, bobot FROM kriteria WHERE id IN (1,2,3)");
while ($b = mysqli_fetch_assoc($bobot_query)) {
    $bobot[$b['kode_kriteria']] = $b['bobot'];
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Detail Perhitungan SAW</h4>
                        <p><strong>Periode:</strong> <?= htmlspecialchars($data['nama_periode'] . ' (' . $data['tahun'] . ')') ?></p>
                        <p><strong>Karyawan:</strong> <?= htmlspecialchars($data['nama_lengkap']) ?> - <?= htmlspecialchars($data['nama_jabatan']) ?></p>
                        <p><strong>Ranking:</strong> <?= $data['ranking'] ?></p>
                        <hr>
                        <h5>Nilai Kriteria Asli</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>Kriteria</th><th>Nilai</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>SOP (C1)</td><td><?= $data['nilai_c1'] ?>%</td></tr>
                                <tr><td>Target Penjualan (C2)</td><td><?= $data['nilai_c2'] ?>%</td></tr>
                                <tr><td>Audit (C3)</td><td><?= $data['nilai_c3'] ?>%</td></tr>
                            </tbody>
                        </table>
                        <br>
                        <h5>Nilai Max Periode</h5>
                        <table class="table table-bordered">
                            <thead class="table-light"><tr><th>Kriteria</th><th>Max</th></tr></thead>
                            <tbody>
                                <tr><td>SOP (C1)</td><td><?= $data['max_c1'] ?>%</td></tr>
                                <tr><td>Target Penjualan (C2)</td><td><?= $data['max_c2'] ?>%</td></tr>
                                <tr><td>Audit (C3)</td><td><?= $data['max_c3'] ?>%</td></tr>
                            </tbody>
                        </table>
                            <br>
                        <h5>Normalisasi</h5>
                        <table class="table table-bordered">
                            <thead class="table-light"><tr><th>Kriteria</th><th>Rumus</th><th>Hasil Normalisasi</th></tr></thead>
                            <tbody>
                                <tr><td>SOP (C1)</td><td><?= $data['nilai_c1'] ?> / <?= $data['max_c1'] ?></td><td><?= $data['nilai_normalisasi_c1'] ?></td></tr>
                                <tr><td>Target Penjualan (C2)</td><td><?= $data['nilai_c2'] ?> / <?= $data['max_c2'] ?></td><td><?= $data['nilai_normalisasi_c2'] ?></td></tr>
                                <tr><td>Audit (C3)</td><td><?= $data['nilai_c3'] ?> / <?= $data['max_c3'] ?></td><td><?= $data['nilai_normalisasi_c3'] ?></td></tr>
                            </tbody>
                        </table>
                        <br>
                        <h5>Bobot Kriteria</h5>
                        <table class="table table-bordered">
                            <thead class="table-light"><tr><th>Kriteria</th><th>Bobot (%)</th></tr></thead>
                            <tbody>
                                <tr><td>SOP (C1)</td><td><?= $bobot['C1'] ?? '30' ?>%</td></tr>
                                <tr><td>Target Penjualan (C2)</td><td><?= $bobot['C2'] ?? '30' ?>%</td></tr>
                                <tr><td>Audit (C3)</td><td><?= $bobot['C3'] ?? '40' ?>%</td></tr>
                            </tbody>
                        </table>
                        <br><br>
                        <h5>Perhitungan Nilai Preferensi</h5>
                        <hr>
                        <p class="formula">
                            <strong>Rumus:</strong> (Bobot_SOP × Normalisasi_SOP) + (Bobot_Target × Normalisasi_Target) + (Bobot_Audit × Normalisasi_Audit)
                        </p>
                        <p>
                            = (<?= ($bobot['C1'] ?? 30) / 100 ?> × <?= $data['nilai_normalisasi_c1'] ?>) +
                              (<?= ($bobot['C2'] ?? 30) / 100 ?> × <?= $data['nilai_normalisasi_c2'] ?>) +
                              (<?= ($bobot['C3'] ?? 40) / 100 ?> × <?= $data['nilai_normalisasi_c3'] ?>)
                        </p>
                        <p>
                            = <?= round(($bobot['C1'] ?? 30) / 100 * $data['nilai_normalisasi_c1'], 6) ?> +
                              <?= round(($bobot['C2'] ?? 30) / 100 * $data['nilai_normalisasi_c2'], 6) ?> +
                              <?= round(($bobot['C3'] ?? 40) / 100 * $data['nilai_normalisasi_c3'], 6) ?>
                        </p>
                        <p><strong>Nilai Preferensi Akhir = <?= $data['nilai_preferensi'] ?></strong></p>
                        <p><strong>Ranking:</strong> <?= $data['ranking'] ?></p>
                        <a href="index.php?periode_id=<?= $data['periode_id'] ?>" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once '../template/footer.php'; ?>
</div>