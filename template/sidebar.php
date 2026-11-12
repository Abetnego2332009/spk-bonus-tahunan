
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>


<div class="container-fluid page-body-wrapper">
  <!-- partial:partials/_sidebar.html -->
  <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      <!-- Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/dashboard.php">
          <i class="mdi mdi-grid-large menu-icon"></i>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>

      <!-- Master Data -->
      <li class="nav-item nav-category">Master Data</li>
      
    <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/karyawan/">
           <i class="menu-icon mdi mdi-account-group"></i>
          <span class="menu-title">Data Karyawan</span>
        </a>
      </li>



      <!-- <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#master-karyawan" aria-expanded="false" aria-controls="master-karyawan">
          <i class="menu-icon mdi mdi-account-group"></i>
          <span class="menu-title">Karyawan</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="master-karyawan">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/karyawan/">Data Karyawan</a></li>
          </ul>
        </div>
      </li> -->
     <?php if (
                $_SESSION['role'] != 'kepala_toko'
            ): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#master-kriteria" aria-expanded="false" aria-controls="master-kriteria">
          <i class="menu-icon mdi mdi-chart-areaspline"></i>
          <span class="menu-title">Kriteria & Bobot</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="master-kriteria">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/kriteria/">Data Kriteria</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/sub_kriteria/">Sub Kriteria (SOP)</a></li>
          </ul>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/periode/">
          <i class="menu-icon mdi mdi-calendar-month"></i>
          <span class="menu-title">Periode Penilaian</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/bonus/">
          <i class="menu-icon mdi mdi-gift"></i>
          <span class="menu-title">Tetapkan Bonus</span>
        </a>
      </li>
      <?php endif;?>
      <!-- Penilaian (Kepala Toko) -->
      <li class="nav-item nav-category">Penilaian</li>

      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#penilaian-input" aria-expanded="false" aria-controls="penilaian-input">
          <i class="menu-icon mdi mdi-clipboard-list"></i>
          <span class="menu-title">Input Nilai Karyawan</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="penilaian-input">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/penilaian_sop/">Nilai SOP</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/penilaian_target/">Target Penjualan</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/input_audit/">Hasil Audit</a></li>
          </ul>
        </div>
      </li>

      <!-- <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/daftar_penilaian/">
          <i class="menu-icon mdi mdi-table-search"></i>
          <span class="menu-title">Daftar Penilaian Kriteria</span>
        </a>
      </li> -->

      <!-- SPK - SAW -->
      <li class="nav-item nav-category">SPK - SAW</li>

      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/saw/">
          <i class="menu-icon mdi mdi-calculator"></i>
          <span class="menu-title">Hitung Nilai</span>
        </a>
      </li>

      <!-- <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/daftar_Penilaian/">
          <i class="menu-icon mdi mdi-trophy"></i>
          <span class="menu-title">Lihat Ranking</span>
        </a>
      </li> -->

      <!-- Bonus & Keputusan (Pimpinan) -->
      <li class="nav-item nav-category">Bonus & Keputusan</li>

      

      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/daftar_Penilaian/">
          <i class="menu-icon mdi mdi-cash-multiple"></i>
          <span class="menu-title">Hasil Ranking & Bonus</span>
        </a>
      </li>
  <?php if (
                $_SESSION['role'] != 'kepala_toko'
            ): ?>
      <!-- Laporan & Pengaturan -->
      <li class="nav-item nav-category">Pengaturan</li>

      <!-- <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/modules/laporan/laporan_penilaian.php">
          <i class="menu-icon mdi mdi-file-chart"></i>
          <span class="menu-title">Cetak Laporan</span>
        </a>
      </li> -->

      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/user/">
           <i class="menu-icon mdi mdi-account-key"></i>
          <span class="menu-title">Manajemen User</span>
        </a>
      </li>
    <?php endif;?>
      <!-- <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#user-management" aria-expanded="false" aria-controls="user-management">
          <i class="menu-icon mdi mdi-account-key"></i>
          <span class="menu-title">Manajemen User</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="user-management">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/modules/users/data_users.php">Data User</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/modules/users/ganti_password.php">Ganti Password</a></li>
          </ul>
        </div>
      </li> -->
      <li class="nav-item">
    <a class="nav-link" href="<?= $base_url ?>/logout.php">
        <i class="menu-icon mdi mdi-logout"></i>
        <span class="menu-title">Logout</span>
    </a>
</li>
    </ul>
  </nav>
  <!-- partial -->