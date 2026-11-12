<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Tambah Periode';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$tanggal_hari_ini = date('Y-m-d');
$tanggal_setahun = date('Y-m-d', strtotime('+1 year'));
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah Periode Penilaian</h4>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="tambah">
                            <div class="mb-3">
                                <label class="form-label">Nama Periode</label>
                                <input type="text" name="nama_periode" class="form-control" required placeholder="Contoh: Penilaian Tahun 2024">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun</label>
                                <input type="number" name="tahun" class="form-control" required min="2000" max="2099">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="<?= $tanggal_hari_ini; ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="<?= $tanggal_setahun; ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status Aktif?</label>
                                <select name="is_active" class="form-control">
                                    <option value="0">Tidak Aktif</option>
                                    <option value="1">Aktif</option>
                                </select>
                                <small class="text-muted">Hanya boleh satu periode aktif.</small>
                            </div>
                            <!-- TAMBAHKAN INPUT TARGET PENJUALAN -->
                            <div class="mb-3">
                                <label class="form-label">Target Penjualan Periode (Rp)</label>
                                <input type="number" name="target_penjualan" class="form-control" required min="0" step="1000000" value="30000000">
                                <small class="text-muted">Contoh: 30000000 = 30 juta</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once '../template/footer.php'; ?>
</div>

<script>
const tanggalMulai = document.getElementById('tanggal_mulai');
const tanggalAkhir = document.getElementById('tanggal_akhir');
tanggalMulai.addEventListener('change', function () {
    let mulai = new Date(this.value);
    mulai.setFullYear(mulai.getFullYear() + 1);
    let tahun = mulai.getFullYear();
    let bulan = String(mulai.getMonth() + 1).padStart(2, '0');
    let hari = String(mulai.getDate()).padStart(2, '0');
    tanggalAkhir.value = `${tahun}-${bulan}-${hari}`;
});
</script>