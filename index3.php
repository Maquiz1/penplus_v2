<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$users = $override->getData('user');
if ($user->isLoggedIn()) {

  $site_data = $override->getData('site');
  $Total = $override->getCount('clients', 'status', 1);
  $data_enrolled = $override->getCount1('clients', 'status', 1, 'enrolled', 1);

  if ($user->data()->power == 1) {
    $screened = $override->countData('clients', 'status', 1, 'screened', 1);
    $eligible = $override->countData('clients', 'status', 1, 'eligible', 1);
    $enrolled = $override->countData('clients', 'status', 1, 'enrolled', 1);
    $end = $override->countData('clients', 'status', 1, 'end_study', 1);
  } else {

    $screened = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $user->data()->site_id);
    $eligible = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $user->data()->site_id);
    $enrolled = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $user->data()->site_id);
    $end = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $user->data()->site_id);
  }
} else {
  Redirect::to('index.php');
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Penplus Database | Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- IonIcons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<!--
`body` tag options:

  Apply one or more of the following classes to to the body tag
  to get the desired effect

  * sidebar-collapse
  * sidebar-mini
-->

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include 'sidemenu.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Dashboard v3</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard v3</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-6">
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">Screening of Participants</h3>
                    <a href="javascript:void(0);">View Report</a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="d-flex">
                    <p class="d-flex flex-column">
                      <span class="text-bold text-lg"><?= $screened ?></span>
                      <span>Screening Over Time</span>
                    </p>
                    <p class="ml-auto d-flex flex-column text-right">
                      <span class="text-success">
                        <i class="fas fa-arrow-up"></i> 12.5%
                      </span>
                      <span class="text-muted">Since last week</span>
                    </p>
                  </div>
                  <!-- /.d-flex -->

                  <div class="position-relative mb-4">
                    <canvas id="visitors-chart" height="200"></canvas>
                  </div>

                  <div class="d-flex flex-row justify-content-end">
                    <span class="mr-2">
                      <i class="fas fa-square text-primary"></i> This Week
                    </span>

                    <span>
                      <i class="fas fa-square text-gray"></i> Last Week
                    </span>
                  </div>
                </div>
              </div>
              <!-- /.card -->

              <div class="card">
                <div class="card-header border-0">
                  <h3 class="card-title">Dieseases</h3>
                  <div class="card-tools">
                    <a href="#" class="btn btn-tool btn-sm">
                      <i class="fas fa-download"></i>
                    </a>
                    <a href="#" class="btn btn-tool btn-sm">
                      <i class="fas fa-bars"></i>
                    </a>
                  </div>
                </div>
                <div class="card-body table-responsive p-0">
                  <table class="table table-striped table-valign-middle">
                    <thead>
                      <tr>
                        <th>Site</th>
                        <th>Cardiac</th>
                        <th>Diabetic</th>
                        <th>Sickle Cell</th>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $x = 1;
                      foreach ($site_data as $row) {
                        $site_count = $override->countData('clients', 'status', 1, 'site_id', $row['id']);
                        $registered_Total = $override->getCount('clients', 'status', 1);
                        $screened = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $row['id']);
                        $screened_Total = $override->countData('clients', 'status', 1, 'screened', 1);
                        $cardiac = $override->countData2('clients', 'status', 1, 'cardiac', 1, 'site_id', $row['id']);
                        $cardiac_Total = $override->countData('clients', 'status', 1, 'cardiac', 1);
                        $diabetes = $override->countData2('clients', 'status', 1, 'diabetes', 1, 'site_id', $row['id']);
                        $diabetes_Total = $override->countData('clients', 'status', 1, 'diabetes', 1);
                        $sickle_cell = $override->countData2('clients', 'status', 1, 'sickle_cell', 1, 'site_id', $row['id']);
                        $sickle_cell_Total = $override->countData('clients', 'status', 1, 'sickle_cell', 1);

                        $all_disease = $override->countData4('clients', 'status', 1, 'cardiac', 1, 'diabetes', 1, 'sickle_cell', 1, 'site_id', $row['id']);
                        $all_disease_Total = $override->countData5('clients', 'status', 1, 'cardiac', 1, 'diabetes', 1, 'sickle_cell', 1);


                      ?>
                        <tr>
                          <td>
                            <img src="dist/img/default-150x150.png" alt="Product 1" class="img-circle img-size-32 mr-2">
                            <?= $row['name']; ?>
                          </td>
                          <td><?= $cardiac ?></td>

                          <td><?= $diabetes ?></td>

                          <td><?= $sickle_cell ?></td>

                          <td><?= $all_disease ?></td>
                        </tr>


                      <?php
                        $x += 1;
                      }
                      ?>
                      <tr>
                        <th>Total</th>
                        <td><?= $cardiac_Total ?></td>

                        <td><?= $diabetes_Total ?></td>

                        <td><?= $sickle_cell_Total ?></td>

                        <td><?= $all_disease_Total ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col-md-6 -->
            <div class="col-lg-6">
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">Tests</h3>
                    <a href="javascript:void(0);">View Report</a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="d-flex">
                    <p class="d-flex flex-column">
                      <span class="text-bold text-lg">$18,230.00</span>
                      <span>Tests Over Time</span>
                    </p>
                    <p class="ml-auto d-flex flex-column text-right">
                      <span class="text-success">
                        <i class="fas fa-arrow-up"></i> 33.1%
                      </span>
                      <span class="text-muted">Since last month</span>
                    </p>
                  </div>
                  <!-- /.d-flex -->

                  <div class="position-relative mb-4">
                    <canvas id="sales-chart" height="200"></canvas>
                  </div>

                  <div class="d-flex flex-row justify-content-end">
                    <span class="mr-2">
                      <i class="fas fa-square text-primary"></i> This year
                    </span>

                    <span>
                      <i class="fas fa-square text-gray"></i> Last year
                    </span>
                  </div>
                </div>
              </div>
              <!-- /.card -->

              <div class="card">
                <div class="card-header border-0">
                  <h3 class="card-title">Online Store Overview</h3>
                  <div class="card-tools">
                    <a href="#" class="btn btn-sm btn-tool">
                      <i class="fas fa-download"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-tool">
                      <i class="fas fa-bars"></i>
                    </a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                    <p class="text-success text-xl">
                      <i class="ion ion-ios-refresh-empty"></i>
                    </p>
                    <p class="d-flex flex-column text-right">
                      <span class="font-weight-bold">
                        <i class="ion ion-android-arrow-up text-success"></i> 12%
                      </span>
                      <span class="text-muted">ENROLLMENT RATE</span>
                    </p>
                  </div>
                  <!-- /.d-flex -->
                  <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                    <p class="text-warning text-xl">
                      <i class="ion ion-ios-cart-outline"></i>
                    </p>
                    <p class="d-flex flex-column text-right">
                      <span class="font-weight-bold">
                        <i class="ion ion-android-arrow-up text-warning"></i> 0.8%
                      </span>
                      <span class="text-muted">SCREENING RATE</span>
                    </p>
                  </div>
                  <!-- /.d-flex -->
                  <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="text-danger text-xl">
                      <i class="ion ion-ios-people-outline"></i>
                    </p>
                    <p class="d-flex flex-column text-right">
                      <span class="font-weight-bold">
                        <i class="ion ion-android-arrow-down text-danger"></i> 1%
                      </span>
                      <span class="text-muted">REGISTRATION RATE</span>
                    </p>
                  </div>
                  <!-- /.d-flex -->
                </div>
              </div>
            </div>
            <!-- /.col-md-6 -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <?php include 'footer.php'; ?>

    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE -->
    <script src="dist/js/adminlte.js"></script>

    <!-- OPTIONAL SCRIPTS -->
    <script src="plugins/chart.js/Chart.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>

    <!-- AdminLTE for demo purposes -->
    <!-- <script src="dist/js/demo.js"></script> -->
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="dist/js/pages/dashboard3.js"></script>
</body>

</html>