<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Aturan Bonus per Peringkat';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

require_once __DIR__ . '/../auth/auth_pimpinan.php'; 

// Ambil semua periode untuk dropdown filter
$periode_list = mysqli_query($koneksi, "SELECT id, nama_periode, tahun FROM periode_penilaian ORDER BY tahun ASC");

$selected_periode = isset($_GET['periode_id']) ? (int)$_GET['periode_id'] : 0;
if ($selected_periode <= 0 && mysqli_num_rows($periode_list) > 0) {
    $first = mysqli_fetch_assoc($periode_list);
    $selected_periode = $first['id'];
    mysqli_data_seek($periode_list, 0); // reset pointer
}

// Ambil aturan bonus untuk periode yang dipilih
$aturan = [];
if ($selected_periode) {
    $query = "SELECT * FROM bonus_aturan WHERE periode_id = $selected_periode ORDER BY ranking";
    $res = mysqli_query($koneksi, $query);
    while ($row = mysqli_fetch_assoc($res)) {
        $aturan[] = $row;
    }
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title">Aturan Bonus per Peringkat</h4>
                            <a href="tambah.php?periode_id=<?= $selected_periode ?>" class="btn btn-primary btn-sm">+ Tambah Aturan</a>
                        </div>
                        <form method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Pilih Periode</label>
                                    <select name="periode_id" class="form-control" onchange="this.form.submit()">
                                        <?php while($p = mysqli_fetch_assoc($periode_list)): ?>
                                            <option value="<?= $p['id'] ?>" <?= $selected_periode == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nama_periode'] . ' (' . $p['tahun'] . ')') ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </form>

                        <?php if (empty($aturan)): ?>
                            <div class="alert alert-info">Belum ada aturan bonus untuk periode ini. Silakan tambah aturan.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Peringkat ke-</th>
                                            <th>Jumlah Bonus (Rp)</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($aturan as $a): ?>
                                        <tr>
                                            <td><?= $a['ranking'] ?></td>
                                            <td><?= number_format($a['jumlah_bonus'], 0, ',', '.') ?></td>
                                            <td>
                                                <a href="edit.php?id=<?= $a['id'] ?>&periode_id=<?= $selected_periode ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="proses.php?action=hapus&id=<?= $a['id'] ?>&periode_id=<?= $selected_periode ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus aturan ini?')">Hapus</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once '../template/footer.php'; ?>
</div>