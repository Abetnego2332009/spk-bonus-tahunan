<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Daftar Penilaian & Ranking SAW';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil periode aktif default
$periode_aktif = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id, nama_periode FROM periode_penilaian WHERE is_active = 1 LIMIT 1"));
$periode_id = $periode_aktif ? $periode_aktif['id'] : 0;
$selected_periode = isset($_GET['periode_id']) ? (int)$_GET['periode_id'] : $periode_id;

// Ambil data hasil_saw untuk periode yang dipilih
$query = "SELECT hs.*, k.nama_lengkap, j.nama_jabatan 
          FROM hasil_saw hs
          JOIN penilaian p ON hs.penilaian_id = p.id
          JOIN karyawan k ON p.karyawan_id = k.id
          LEFT JOIN jabatan j ON k.jabatan_id = j.id
          WHERE hs.periode_id = $selected_periode
          ORDER BY hs.ranking ASC";
$result = mysqli_query($koneksi, $query);
$data_hasil = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data_hasil[] = $row;
}

// Ambil semua periode untuk dropdown filter
$periode_list = mysqli_query($koneksi, "SELECT id, nama_periode FROM periode_penilaian ORDER BY tahun DESC");
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daftar Penilaian & Ranking SAW</h4>
                        <form method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Pilih Periode</label>
                                    <select name="periode_id" class="form-control" onchange="this.form.submit()">
                                        <?php while($row = mysqli_fetch_assoc($periode_list)): ?>
                                            <option value="<?= $row['id'] ?>" <?= $selected_periode == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars($row['nama_periode']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <a href="proses.php?periode_id=<?= $selected_periode ?>" class="btn btn-primary form-control">Proses SAW</a>
                                </div>
                            </div>
                        </form>

                        <?php if (empty($data_hasil)): ?>
                            <div class="alert alert-info">Belum ada hasil SAW untuk periode ini. Klik "Proses SAW" untuk menghitung dan menyimpan ranking.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nama Karyawan</th>
                                            <th>Jabatan</th>
                                            <th>Nilai SOP</th>
                                            <th>Target (%)</th>
                                            <th>Nilai Audit</th>
                                            <th>Nilai Akhir</th>
                                            <th>Ranking</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data_hasil as $row): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                                <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
                                                <td><?= $row['nilai_c1'] ?>%  </td>
                                                <td><?= $row['nilai_c2'] ?>%</td>
                                                <td><?= $row['nilai_c3'] ?>% </td>
                                                <td><?= $row['nilai_preferensi'] ?></td>
                                                <td class = "text-center"><?= $row['ranking'] ?></td>
                                                <td><a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Detail</a></td>
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