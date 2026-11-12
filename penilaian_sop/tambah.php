<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Tambah Nilai SOP';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Label nilai statis
$label_nilai = [
    'Sangat Baik' => 100,
    'Baik' => 85,
    'Cukup' => 75,
    'Kurang' => 65
];

// Periode aktif
$periode = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id, nama_periode, tahun FROM periode_penilaian WHERE is_active = 1 LIMIT 1"));
if (!$periode) {
    echo "<div class='main-panel'><div class='content-wrapper'><div class='alert alert-warning'>Belum ada periode aktif.</div></div></div>";
    require_once '../template/footer.php';
    exit;
}

// Karyawan aktif (belum punya nilai SOP untuk periode ini)
$karyawan_query = "SELECT k.id, k.nama_lengkap, j.nama_jabatan 
                   FROM karyawan k 
                   LEFT JOIN jabatan j ON k.jabatan_id = j.id 
                   WHERE k.status_karyawan = 'aktif'
                   AND NOT EXISTS (SELECT 1 FROM penilaian WHERE karyawan_id = k.id AND periode_id = {$periode['id']} AND nilai_sop IS NOT NULL)
                   ORDER BY k.nama_lengkap";
$karyawan = mysqli_query($koneksi, $karyawan_query);

// Sub kriteria SOP
$sub_list = [];
$res = mysqli_query($koneksi, "SELECT id, nama_sub_kriteria FROM sub_kriteria WHERE kriteria_id = 1 ORDER BY urutan");
while ($row = mysqli_fetch_assoc($res)) $sub_list[] = $row;
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah Nilai SOP</h4>
                        <p>Periode aktif: <strong><?= htmlspecialchars($periode['nama_periode'] . ' (' . $periode['tahun'] . ')') ?></strong></p>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="tambah">
                            <input type="hidden" name="periode_id" value="<?= $periode['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Pilih Karyawan</label>
                                <select name="karyawan_id" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <?php while($k = mysqli_fetch_assoc($karyawan)): ?>
                                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_lengkap'] . ' - ' . $k['nama_jabatan']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <?php if (mysqli_num_rows($karyawan) == 0): ?>
                                    <small class="text-danger">Semua karyawan sudah memiliki nilai SOP untuk periode ini.</small>
                                <?php endif; ?>
                            </div>
                            <table class="table table-bordered">
                                <thead class="table-light"><tr><th>Sub Kriteria</th><th>Penilaian</th></tr></thead>
                                <tbody>
                                    <?php foreach ($sub_list as $sub): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($sub['nama_sub_kriteria']) ?></td>
                                        <td>
                                            <select name="sub_<?= $sub['id'] ?>" class="form-control" required>
                                                <option value="">-- Pilih --</option>
                                                <?php foreach ($label_nilai as $label => $nilai): ?>
                                                    <option value="<?= $label ?>"><?= $label . ' (' . $nilai . ')' ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once '../template/footer.php'; ?>
</div>