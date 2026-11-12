<?php
session_start();
require_once __DIR__ . '/../koneksi.php';
$title = 'Tambah Kriteria';
require_once '../template/header.php';
require_once '../template/navbar.php';
require_once '../template/sidebar.php';

require_once __DIR__ . '/../auth/auth_pimpinan.php'; 
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah Kriteria Baru</h4>
                        <form action="proses.php" method="POST">
                            <input type="hidden" name="action" value="tambah">
                            
                            <div class="mb-3">
                                <label class="form-label">Kode Kriteria</label>
                                <input type="text" name="kode_kriteria" class="form-control" required placeholder="Contoh: C4" maxlength="10">
                                <small class="text-muted">Harus unik, contoh: C1, C2, C3</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Kriteria</label>
                                <input type="text" name="nama_kriteria" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Bobot (%)</label>
                                <input type="number" step="0.01" name="bobot" class="form-control" required placeholder="Misal: 30.00">
                                <small class="text-muted">Bobot dalam persen. Total bobot semua kriteria sebaiknya 100%.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis</label>
                                <select name="jenis" class="form-control" required>
                                    <option value="benefit">Benefit (semakin besar nilai semakin baik)</option>
                                    <option value="cost">Cost (semakin kecil nilai semakin baik)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"></textarea>
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