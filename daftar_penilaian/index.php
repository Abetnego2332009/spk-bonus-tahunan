<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Hasil Ranking & Bonus';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Periode aktif atau pilihan
$periode_aktif = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id, nama_periode, tahun FROM periode_penilaian WHERE is_active = 1 LIMIT 1"));
$periode_id = $periode_aktif ? $periode_aktif['id'] : 0;
$selected_periode = isset($_GET['periode_id']) ? (int)$_GET['periode_id'] : $periode_id;

// Ambil data periode yang dipilih untuk ditampilkan di cetak
$periode_info = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_periode, tahun FROM periode_penilaian WHERE id = $selected_periode"));
$periode_text = $periode_info ? $periode_info['nama_periode'] . ' (' . $periode_info['tahun'] . ')' : 'Periode tidak diketahui';

// Query dari view v_ranking_bonus (sudah termasuk bonus)
$query = "SELECT * FROM v_ranking_bonus WHERE periode_id = $selected_periode ORDER BY ranking";
$result = mysqli_query($koneksi, $query);

// Dropdown periode
$periode_list = mysqli_query($koneksi, "SELECT id, nama_periode, tahun FROM periode_penilaian ORDER BY tahun DESC");
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h4 class="card-title">Hasil Ranking & Bonus</h4>
                            <button onclick="window.print();" class="btn btn-secondary" style="font-size: 1rem; padding: 0.4rem 1rem;">🖨️ Cetak</button>
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

                        <?php if (mysqli_num_rows($result) == 0): ?>
                            <div class="alert alert-warning">Belum ada data ranking untuk periode ini. Silakan proses SAW terlebih dahulu.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <!-- Header cetak (logo, nama toko, alamat, periode) -->
                                <div class="print-header" style="display: none;">
                                    <img src="../assets/assets/images/indomaret.png" alt="Logo Indomaret" onerror="this.style.display='none'">
                                    <p><strong>Indomaret MH Sitorus</strong></p>
                                    <p>Jl. MH Sitorus, Kota Pematang Siantar</p>
                                   
                                </div>
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Ranking</th>
                                            <th>Nama Karyawan</th>
                                            <th>Jabatan</th>
                                            <th>Nilai Preferensi</th>
                                            <th>Bonus (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?= $row['ranking'] ?></td>
                                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                            <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
                                            <td><?= $row['nilai_preferensi'] ?></td>
                                            <td>Rp <?= number_format($row['jumlah_bonus'] ?? 0, 0, ',', '.') ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <br>
                                 <p><strong><?= htmlspecialchars($periode_text) ?></strong></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once '../template/footer.php'; ?>
</div>

<style media="print">
    /* Sembunyikan elemen navigasi dan tombol saat cetak */
    .navbar, .sidebar, .btn, form, .header, .footer, .form-control, label, .card-title {
        display: none !important;
    }
    .main-panel, .content-wrapper, .card, .card-body {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    /* Header cetak: logo, nama toko, alamat, periode */
    .print-header {
        text-align: center;
        margin-bottom: 20px;
        display: block !important;
    }
    .print-header img {
        max-width: 100px;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    .print-header p {
        margin: 5px 0;
        font-size: 11pt;
        color: #000 !important;
        font-weight: normal;
    }
    .print-header p strong {
        color: #000 !important;
    }
    /* Tabel: semua teks hitam pekat, header tidak gelap */
    table, th, td {
        color: #000 !important;
        background-color: #fff !important;
    }
    th {
        background-color: #f0f0f0 !important;
        font-weight: bold !important;
        color: #000 !important;
    }
    th, td {
        border: 1px solid #000 !important;
        padding: 6px !important;
    }
    /* Pastikan tidak ada warna lain */
    * {
        color: #000 !important;
    }
</style>