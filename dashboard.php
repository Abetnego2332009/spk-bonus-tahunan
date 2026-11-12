<?php
session_start();
require_once __DIR__ . '/koneksi.php';
$title = 'Dashboard SPK';
require_once 'template/header.php';
require_once 'template/navbar.php';
require_once 'template/sidebar.php';

require_once __DIR__ . '/auth/auth_login.php'; 

// Ambil periode aktif
$periode_aktif = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id, nama_periode, tahun FROM periode_penilaian WHERE is_active = 1 LIMIT 1"));
$periode_id = $periode_aktif ? $periode_aktif['id'] : 0;
$periode_nama = $periode_aktif ? $periode_aktif['nama_periode'] . ' (' . $periode_aktif['tahun'] . ')' : 'Tidak ada periode aktif';

// Jumlah karyawan aktif
$query = "SELECT COUNT(*) as total FROM karyawan WHERE status_karyawan = 'aktif'";
$res = mysqli_query($koneksi, $query);
$total_karyawan = mysqli_fetch_assoc($res)['total'];



// Jumlah penilaian lengkap untuk periode aktif
$query = "SELECT COUNT(*) as total FROM penilaian WHERE periode_id = $periode_id AND nilai_sop IS NOT NULL AND nilai_target_penjualan IS NOT NULL AND nilai_audit IS NOT NULL";
$res = mysqli_query($koneksi, $query);
$penilaian_lengkap = mysqli_fetch_assoc($res)['total'];

// Top 5 ranking dari hasil_saw untuk periode aktif (jika ada)
$top_ranking = [];
if ($periode_id > 0) {
  $query = "SELECT hs.ranking, k.nama_lengkap, j.nama_jabatan, hs.nilai_preferensi
              FROM hasil_saw hs
              JOIN penilaian p ON hs.penilaian_id = p.id
              JOIN karyawan k ON p.karyawan_id = k.id
              LEFT JOIN jabatan j ON k.jabatan_id = j.id
              WHERE hs.periode_id = $periode_id
              ORDER BY hs.ranking ASC
              LIMIT 5";
  $result = mysqli_query($koneksi, $query);
  while ($row = mysqli_fetch_assoc($result)) {
    $top_ranking[] = $row;
  }
}

// Total bonus yang sudah ditetapkan (dari tabel bonus) untuk periode aktif
$total_bonus = 0;
if ($periode_id > 0) {
  $query = "SELECT SUM(jumlah_bonus) as total FROM bonus_aturan WHERE periode_id = $periode_id";
  $res = mysqli_query($koneksi, $query);
  $total_bonus = mysqli_fetch_assoc($res)['total'] ?? 0;
}

// 3. Bobot kriteria (C1, C2, C3)
$bobot = [];
$query = "SELECT kode_kriteria, bobot FROM kriteria WHERE id IN (1,2,3) ORDER BY id";
$res = mysqli_query($koneksi, $query);
while ($row = mysqli_fetch_assoc($res)) {
    $bobot[$row['kode_kriteria']] = $row['bobot'];
}
// Top 3 ranking (ambil dari hasil_saw untuk periode aktif)
$top_ranking = [];
if ($periode_id) {
    $query = "SELECT hs.ranking, k.nama_lengkap, j.nama_jabatan, hs.nilai_preferensi
              FROM hasil_saw hs
              JOIN penilaian p ON hs.penilaian_id = p.id
              JOIN karyawan k ON p.karyawan_id = k.id
              LEFT JOIN jabatan j ON k.jabatan_id = j.id
              WHERE hs.periode_id = $periode_id
              ORDER BY hs.ranking ASC
              LIMIT 3";
    $result = mysqli_query($koneksi, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $top_ranking[] = $row;
    }
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-sm-12">
        <div class="home-tab">

        <div class="row">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5>Karyawan Aktif</h5>
                            <i class="mdi mdi-account-group" style="font-size: 2rem;"></i>
                        </div>
                        <h2 class="mt-3"><?= $total_karyawan ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5>Total Bonus (Aturan)</h5>
                            <i class="mdi mdi-cash-multiple" style="font-size: 2rem;"></i>
                        </div>
                        <h2 class="mt-3">Rp <?= number_format($total_bonus, 0, ',', '.') ?></h2>
                        <small>Periode aktif</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5>Bobot Kriteria</h5>
                            <i class="mdi mdi-chart-areaspline" style="font-size: 2rem;"></i>
                        </div>
                        <ul class="mt-2" style="list-style: none; padding-left: 0;">
                            <li><?= isset($bobot['C1']) ? "C1 (SOP) : {$bobot['C1']}%" : '-' ?></li>
                            <li><?= isset($bobot['C2']) ? "C2 (Target) : {$bobot['C2']}%" : '-' ?></li>
                            <li><?= isset($bobot['C3']) ? "C3 (Audit) : {$bobot['C3']}%" : '-' ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tabel Top 3 Ranking -->
          <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Top 3 Ranking Karyawan (Periode Aktif)</h4>
                        <?php if (empty($top_ranking)): ?>
                            <div class="alert alert-info">Belum ada data ranking. Silakan proses SAW terlebih dahulu.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Ranking</th>
                                            <th>Nama Karyawan</th>
                                            <th>Jabatan</th>
                                            <th>Nilai Preferensi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($top_ranking as $row): ?>
                                        <tr>
                                            <td><?= $row['ranking'] ?></td>
                                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                            <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
                                            <td><?= $row['nilai_preferensi'] ?></td>
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
      </div>
    </div>
  </div>
  <!-- content-wrapper ends -->
  <?php require 'template/footer.php'; ?>