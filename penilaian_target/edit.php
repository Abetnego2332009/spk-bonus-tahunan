<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Edit Target Penjualan';
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

// Ambil data penilaian target
$query = "SELECT p.id, p.karyawan_id, p.penjualan_aktual, p.nilai_target_penjualan, k.nama_lengkap, j.nama_jabatan 
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

// Periode aktif (untuk target)
$periode = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT target_penjualan FROM periode_penilaian WHERE is_active = 1 LIMIT 1"));
$target_nominal = $periode ? $periode['target_penjualan'] : 0;
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Target Penjualan</h4>
                        <p><strong>Karyawan:</strong> <?= htmlspecialchars($data['nama_lengkap'] . ' - ' . $data['nama_jabatan']) ?></p>
                        <p>Target penjualan periode: <strong>Rp <?= number_format($target_nominal, 0, ',', '.') ?></strong></p>
                        <form action="proses.php" method="POST" id="formTarget">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="penilaian_id" value="<?= $id ?>">
                            <div class="mb-3">
                                <label class="form-label">Penjualan Aktual (Rp)</label>
                                <input type="text" name="penjualan_aktual_text" id="penjualan_aktual" class="form-control" required placeholder="<?= 'Rp'. number_format($data['penjualan_aktual'] ?? 0, 0, ',', '.') ?>">
                                
                            </div>
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