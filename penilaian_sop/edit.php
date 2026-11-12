<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Edit Nilai SOP';
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

// Ambil data penilaian
$query = "SELECT p.id, p.karyawan_id, p.nilai_sop, k.nama_lengkap, j.nama_jabatan 
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

// Ambil detail sub kriteria dan nilai yang sudah ada
$detail_query = "SELECT sub_kriteria_id, nilai FROM detail_penilaian_sop WHERE penilaian_id = $id";
$detail_result = mysqli_query($koneksi, $detail_query);
$existing = [];
while ($det = mysqli_fetch_assoc($detail_result)) {
    $existing[$det['sub_kriteria_id']] = $det['nilai'];
}

// Sub kriteria SOP
$sub_list = [];
$res = mysqli_query($koneksi, "SELECT id, nama_sub_kriteria FROM sub_kriteria WHERE kriteria_id = 1 ORDER BY urutan");
while ($row = mysqli_fetch_assoc($res)) $sub_list[] = $row;

$label_nilai = [
    'Sangat Baik' => 100,
    'Baik' => 85,
    'Cukup' => 75,
    'Kurang' => 65
];
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Nilai SOP</h4>
                        <p><strong>Karyawan:</strong> <?= htmlspecialchars($data['nama_lengkap']) . ' - ' . htmlspecialchars($data['nama_jabatan']) ?></p>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="penilaian_id" value="<?= $id ?>">
                            <table class="table table-bordered">
                                <thead class="table-light"><tr><th>Sub Kriteria</th><th>Penilaian</th></tr></thead>
                                <tbody>
                                    <?php foreach ($sub_list as $sub): 
                                        $nilai_exist = $existing[$sub['id']] ?? null;
                                        $selected_label = '';
                                        foreach ($label_nilai as $label => $val) {
                                            if ($val == $nilai_exist) { $selected_label = $label; break; }
                                        }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($sub['nama_sub_kriteria']) ?></td>
                                        <td>
                                            <select name="sub_<?= $sub['id'] ?>" class="form-control" required>
                                                <option value="">-- Pilih --</option>
                                                <?php foreach ($label_nilai as $label => $val): ?>
                                                    <option value="<?= $label ?>" <?= $selected_label == $label ? 'selected' : '' ?>><?= $label . ' (' . $val . ')' ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <button type="submit" name="submit" class="btn btn-primary">Update</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once '../template/footer.php'; ?>
</div>