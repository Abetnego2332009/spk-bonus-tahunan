<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Edit Aturan Bonus';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';
require_once __DIR__ . '/../auth/auth_pimpinan.php'; 


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<script>alert('ID tidak valid.'); window.location='index.php';</script>";
    exit;
}

$query = "SELECT * FROM bonus_aturan WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
if (!$data) {
    echo "<script>alert('Data tidak ditemukan.'); window.location='index.php';</script>";
    exit;
}
$periode_id = $data['periode_id'];
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Aturan Bonus</h4>
                        <form action="proses.php" method="POST" id="formBonus">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="hidden" name="periode_id" value="<?= $periode_id ?>">
                            <div class="mb-3">
                                <label class="form-label">Peringkat ke-</label>
                                <input type="number" name="ranking" class="form-control" required min="1" value="<?= $data['ranking'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Bonus (Rp)</label>
                                <input type="text" name="jumlah_bonus_text" id="jumlah_bonus" class="form-control" required value="<?= number_format($data['jumlah_bonus'], 0, ',', '.') ?>">
                                <small class="text-muted">Gunakan titik sebagai pemisah ribuan.</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="index.php?periode_id=<?= $periode_id ?>" class="btn btn-secondary">Batal</a>
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

var inputBonus = document.getElementById('jumlah_bonus');
if (inputBonus) {
    inputBonus.addEventListener('keyup', function(e) {
        this.value = formatRupiah(this.value);
    });
    document.getElementById('formBonus').addEventListener('submit', function(e) {
        var raw = document.getElementById('jumlah_bonus').value;
        var numeric = raw.replace(/\./g, '').replace(/,/g, '.');
        var final = parseFloat(numeric);
        if (isNaN(final)) {
            alert('Masukkan angka yang valid!');
            e.preventDefault();
            return false;
        }
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'jumlah_bonus';
        hidden.value = final;
        this.appendChild(hidden);
        document.getElementById('jumlah_bonus').disabled = true;
        return true;
    });
}
</script>