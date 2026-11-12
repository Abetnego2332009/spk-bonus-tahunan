<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Tambah Target Penjualan';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Periode aktif
$periode = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id, nama_periode, tahun, target_penjualan FROM periode_penilaian WHERE is_active = 1 LIMIT 1"));
if (!$periode) {
    echo "<div class='main-panel'><div class='content-wrapper'><div class='alert alert-warning'>Belum ada periode aktif.</div></div></div>";
    require_once '../template/footer.php';
    exit;
}
$target_nominal = $periode['target_penjualan'];

// Karyawan yang belum memiliki nilai target penjualan untuk periode ini
$karyawan_query = "SELECT k.id, k.nama_lengkap, j.nama_jabatan 
                   FROM karyawan k 
                   LEFT JOIN jabatan j ON k.jabatan_id = j.id 
                   WHERE k.status_karyawan = 'aktif'
                   AND NOT EXISTS (SELECT 1 FROM penilaian WHERE karyawan_id = k.id AND periode_id = {$periode['id']} AND nilai_target_penjualan IS NOT NULL)
                   ORDER BY k.nama_lengkap";
$karyawan = mysqli_query($koneksi, $karyawan_query);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah Target Penjualan</h4>
                        <p>Periode aktif: <strong><?= htmlspecialchars($periode['nama_periode'] . ' (' . $periode['tahun'] . ')') ?></strong></p>
                        <p>Target penjualan periode: <strong>Rp <?= number_format($target_nominal, 0, ',', '.') ?></strong></p>
                        <form action="proses.php" method="POST" id="formTarget">
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
                                    <small class="text-danger">Semua karyawan sudah memiliki target penjualan untuk periode ini.</small>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Penjualan Aktual (Rp)</label>
                                <input type="text" name="penjualan_aktual_text" id="penjualan_aktual" class="form-control" required placeholder="Contoh: 28.000.000">
                                
                            </div>
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

<script>
function formatRupiah(angka) {
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    if (ribuan) {
        var separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;
}
var input = document.getElementById('penjualan_aktual');
if (input) {
    input.addEventListener('keyup', function(e) {
        this.value = formatRupiah(this.value);
    });
    document.getElementById('formTarget').addEventListener('submit', function(e) {
        var raw = document.getElementById('penjualan_aktual').value;
        var numeric = raw.replace(/\./g, '').replace(/,/g, '.');
        var final = parseFloat(numeric);
        if (isNaN(final)) {
            alert('Masukkan angka yang valid!');
            e.preventDefault();
            return false;
        }
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'penjualan_aktual';
        hidden.value = final;
        this.appendChild(hidden);
        document.getElementById('penjualan_aktual').disabled = true;
        return true;
    });
}
</script>