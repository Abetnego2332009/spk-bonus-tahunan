      <?php
      date_default_timezone_set('Asia/Jakarta');

      $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
      ];

      $jam = date('H');

      if ($jam >= 5 && $jam < 12) {
        $salam = "Good Morning";
      } elseif ($jam >= 12 && $jam < 15) {
        $salam = "Good Afternoon";
      } elseif ($jam >= 15 && $jam < 18) {
        $salam = "Good Evening";
      } else {
        $salam = "Good Night";
      }

      $tanggal = date('d') . ' ' . $bulan[date('n')] . ' ' . date('Y');
      ?>


      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <a class="navbar-brand brand-logo" href="<?= $base_url ?>/dashboard.php">
              <img src="<?= $base_url ?>/assets/assets/images/indomaret.png" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="<?= $base_url ?>/dashboard.php">
              <img src="<?= $base_url ?>/assets/assets/images/indomaret.png" alt="logo" />
            </a>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-top">
          <ul class="navbar-nav">
            <li class="nav-item fw-semibold d-none d-lg-block ms-0">
              <h1 class="welcome-text">
                <?= $salam ?>,
                <?php $nama = $_SESSION['username'] ?? 'User'; ?>
                <span class="text-black fw-bold"><?= htmlspecialchars($nama) ?></span>
              </h1>
              <h3 class="welcome-sub-text">
                Selamat datang di Sistem Pendukung Keputusan
              </h3>
            </li>
          </ul>
          <ul class="navbar-nav ms-auto">

            <li class="nav-item d-none d-lg-block">
              <div class="input-group">
                <span class="input-group-prepend border-right">
                  <span class="input-group-text">
                    <i class="mdi mdi-calendar"></i>
                  </span>
                </span>
                <span class="form-control bg-white">
                  <?= htmlspecialchars($tanggal) ?>
                </span>
              </div>
            </li>

          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->