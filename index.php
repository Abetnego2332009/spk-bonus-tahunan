<?php 
session_start();
require_once 'koneksi.php';
// Kalau sudah login, cegah akses ke login
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    echo "<script>
            alert('Anda sudah login sebagai {$_SESSION['nama_lengkap']} ({$_SESSION['role']})');
            window.location = 'dashboard.php';
          </script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="<?= $base_url ?>/assets/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="<?= $base_url ?>/assets/assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="<?= $base_url ?>/assets/assets/images/indomaret.png" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
          <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="brand-logo" style="text-align :center;">
                  <img src="<?= $base_url ?>/assets/assets/images/indomaret.png" alt="logo" style="width: 150px; height: auto;">
                </div>
                <h4 style="text-align: center; font-size: 30px; ">LOGIN</h4>
                
                <form class="pt-3" action="proses_login.php" method="POST">
                  <div class="form-group">
                    <input type="text" class="form-control form-control-lg" name="username" id="exampleInputEmail1" placeholder="Username">
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg" name="password" id="exampleInputPassword1" placeholder="Password">
                  </div>
                  <div class="mt-3 d-grid gap-2">
                    <button class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn" name="login">SIGN IN</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="<?= $base_url ?>/assets/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="<?= $base_url ?>/assets/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="<?= $base_url ?>/assets/assets/js/off-canvas.js"></script>
    <script src="<?= $base_url ?>/assets/assets/js/template.js"></script>
    <script src="<?= $base_url ?>/assets/assets/js/settings.js"></script>
    <script src="<?= $base_url ?>/assets/assets/js/hoverable-collapse.js"></script>
    <script src="<?= $base_url ?>/assets/assets/js/todolist.js"></script>
    <!-- endinject -->
  </body>
</html>