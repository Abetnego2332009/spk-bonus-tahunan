<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$periode_id = isset($_GET['periode_id']) ? (int)$_GET['periode_id'] : 0;
if ($periode_id <= 0) {
    echo "<script>alert('Periode tidak valid.'); window.location='index.php';</script>";
    exit;
}

// Ambil data penilaian untuk periode ini (hanya yang lengkap)
$query = "SELECT p.id, p.nilai_sop, p.nilai_target_penjualan, p.nilai_audit
          FROM penilaian p
          WHERE p.periode_id = $periode_id
          AND p.nilai_sop IS NOT NULL
          AND p.nilai_target_penjualan IS NOT NULL
          AND p.nilai_audit IS NOT NULL";
$result = mysqli_query($koneksi, $query);
$data_penilaian = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data_penilaian[] = $row;
}

if (empty($data_penilaian)) {
    echo "<script>alert('Tidak ada data penilaian lengkap untuk periode ini.'); window.location='index.php?periode_id=$periode_id';</script>";
    exit;
}

// Hitung max tiap kriteria
$max_c1 = max(array_column($data_penilaian, 'nilai_sop'));
$max_c2 = max(array_column($data_penilaian, 'nilai_target_penjualan'));
$max_c3 = max(array_column($data_penilaian, 'nilai_audit'));

// Ambil bobot kriteria
$bobot = [];
$bobot_query = mysqli_query($koneksi, "SELECT id, bobot FROM kriteria WHERE id IN (1,2,3)");
while ($b = mysqli_fetch_assoc($bobot_query)) {
    $bobot[$b['id']] = $b['bobot'] / 100;
}
$w1 = $bobot[1] ?? 0.30;
$w2 = $bobot[2] ?? 0.30;
$w3 = $bobot[3] ?? 0.40;

// Hitung normalisasi dan preferensi
$ranking_data = [];
foreach ($data_penilaian as $item) {
    $norm_c1 = round($item['nilai_sop'] / $max_c1, 6);
    $norm_c2 = round($item['nilai_target_penjualan'] / $max_c2, 6);
    $norm_c3 = round($item['nilai_audit'] / $max_c3, 6);
    $preferensi = round(($w1 * $norm_c1) + ($w2 * $norm_c2) + ($w3 * $norm_c3), 6);
    $ranking_data[] = [
        'penilaian_id' => $item['id'],
        'nilai_c1' => $item['nilai_sop'],
        'nilai_c2' => $item['nilai_target_penjualan'],
        'nilai_c3' => $item['nilai_audit'],
        'norm_c1' => $norm_c1,
        'norm_c2' => $norm_c2,
        'norm_c3' => $norm_c3,
        'preferensi' => $preferensi
    ];
}
// Urutkan berdasarkan preferensi tertinggi
usort($ranking_data, function($a, $b) {
    return $b['preferensi'] <=> $a['preferensi'];
});

// Hapus data lama di hasil_saw untuk periode ini
mysqli_query($koneksi, "DELETE FROM hasil_saw WHERE periode_id = $periode_id");

// Simpan hasil baru
$ranking = 1;
foreach ($ranking_data as $rd) {
    $query_insert = "INSERT INTO hasil_saw (penilaian_id, periode_id, nilai_c1, nilai_c2, nilai_c3, 
                    max_c1, max_c2, max_c3, nilai_normalisasi_c1, nilai_normalisasi_c2, nilai_normalisasi_c3, 
                    nilai_preferensi, ranking) 
                    VALUES ({$rd['penilaian_id']}, $periode_id, {$rd['nilai_c1']}, {$rd['nilai_c2']}, {$rd['nilai_c3']}, 
                    $max_c1, $max_c2, $max_c3, {$rd['norm_c1']}, {$rd['norm_c2']}, {$rd['norm_c3']}, 
                    {$rd['preferensi']}, $ranking)";
    mysqli_query($koneksi, $query_insert);
    $ranking++;
}

echo "<script>alert('Perhitungan SAW selesai. Data berhasil disimpan.'); window.location='index.php?periode_id=$periode_id';</script>";
exit;
?>