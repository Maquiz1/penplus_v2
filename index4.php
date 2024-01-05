<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$users = $override->getData('user');
if ($user->isLoggedIn()) {
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
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
    </div>

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
              <h1 class="m-0">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard v1</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?= $screened ?></h3>

                  <p>Screened</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
                <a href="info.php?id=3&status=1" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?= $eligible ?><sup style="font-size: 20px">%</sup></h3>

                  <p>Eligible</p>
                </div>
                <div class="icon">
                  <i class="ion ion-stats-bars"></i>
                </div>
                <a href="info.php?id=3&status=2" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><?= $enrolled ?></h3>

                  <p>Enrolled</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-add"></i>
                </div>
                <a href="info.php?id=3&status=3" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?= $end ?></h3>

                  <p>End of study</p>
                </div>
                <div class="icon">
                  <i class="ion ion-pie-graph"></i>
                </div>
                <a href="info.php?id=3&status=4" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
          </div>
          <!-- /.row -->

          <!-- Main row -->
          <div class="row">
            <!-- Left col -->
            <section class="col-lg-7 connectedSortable">
              <!-- Custom tabs (Charts with tabs)-->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Sales
                  </h3>
                  <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                      <li class="nav-item">
                        <a class="nav-link active" href="#revenue-chart" data-toggle="tab">Area</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="#sales-chart" data-toggle="tab">Donut</a>
                      </li>
                    </ul>
                  </div>
                </div><!-- /.card-header -->
                <div class="card-body">
                  <div class="tab-content p-0">
                    <!-- Morris chart - Sales -->
                    <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
                      <canvas id="revenue-chart-canvas" height="300" style="height: 300px;"></canvas>
                    </div>
                    <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
                      <canvas id="sales-chart-canvas" height="300" style="height: 300px;"></canvas>
                    </div>
                  </div>
                </div><!-- /.card-body -->
              </div>
              <!-- /.card -->

              <!-- TO DO List -->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="ion ion-clipboard mr-1"></i>
                    List of Medications
                  </h3>

                  <div class="card-tools">
                    <ul class="pagination pagination-sm">
                      <li class="page-item"><a href="#" class="page-link">&laquo;</a></li>
                      <li class="page-item"><a href="#" class="page-link">1</a></li>
                      <li class="page-item"><a href="#" class="page-link">2</a></li>
                      <li class="page-item"><a href="#" class="page-link">3</a></li>
                      <li class="page-item"><a href="#" class="page-link">&raquo;</a></li>
                    </ul>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <ul class="todo-list" data-widget="todo-list">
                    <?php
                    $x = 1;
                    foreach ($override->get('medications', 'status', 1) as $medication) { ?>
                      <li>
                        <!-- drag handle -->
                        <span class="handle">
                          <i class="fas fa-ellipsis-v"></i>
                          <i class="fas fa-ellipsis-v"></i>
                        </span>
                        <!-- checkbox -->
                        <div class="icheck-primary d-inline ml-2">
                          <input type="checkbox" value="" name="todo1" id="todoCheck1">
                          <label for="todoCheck1"></label>
                        </div>
                        <!-- todo text -->
                        <span class="text"><?= $medication['name'] ?></span>
                        <!-- Emphasis label -->
                        <small class="badge badge-success"><i class="far fa-clock"></i> <?= $medication['amount'] ?></small>
                        <!-- General tools such as edit or delete-->
                        <div class="tools">
                          <i class="fas fa-edit"></i>
                          <i class="fas fa-trash-o"></i>
                        </div>
                      </li>
                    <?php } ?>
                  </ul>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                  <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#medication_modal<?= $medication['id'] ?>"><i class="fas fa-plus"></i> Add New Medication</button>
                </div>
                <div class="modal fade" id="medication_modal<?= $medication['id'] ?>">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title">Medication Form</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-sm-4">
                            <div class="row-form clearfix">
                              <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" placeholder="Enter email" id="name" name="name" value="<?php if ($medication['name']) {
                                                                                                                                  print_r($medication['name']);
                                                                                                                                }  ?>" required />
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-4">
                            <div class="row-form clearfix">
                              <div class="form-group">
                                <label for="exampleInputEmail1">Batch / Serial </label>
                                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-4">
                            <div class="row-form clearfix">
                              <div class="form-group">
                                <label for="exampleInputEmail1">Amount</label>
                                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-sm-4">
                            <div class="row-form clearfix">
                              <div class="form-group">
                                <label for="exampleInputEmail1">Form</label>
                                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-4">
                            <div class="row-form clearfix">
                              <div class="form-group">
                                <label for="exampleInputEmail1">Expire Date </label>
                                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-4">
                            <div class="row-form clearfix">
                              <div class="form-group">
                                <label for="exampleInputEmail1">Category</label>
                                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-sm-4">
                            <div class="row-form clearfix">
                              <!-- select -->
                              <div class="form-group">
                                <label>Cardiac:</label>
                                <select name="cardiac" style="width: 100%;" required>
                                  <option value="<?= $medication['cardiac'] ?>"><?php if ($medication) {
                                                                                  if ($medication['cardiac'] == 1) {
                                                                                    echo 'Yes';
                                                                                  } elseif ($medication['cardiac'] == 2) {
                                                                                    echo 'No';
                                                                                  }
                                                                                } else {
                                                                                  echo 'Select';
                                                                                } ?>
                                  </option>
                                  <option value="1">Yes</option>
                                  <option value="2">No</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="row-form clearfix">
                              <!-- select -->
                              <div class="form-group">
                                <label>Diabetes:</label>
                                <select name="diabetes" style="width: 100%;" required>
                                  <option value="<?= $medication['diabetes'] ?>"><?php if ($medication) {
                                                                                    if ($medication['diabetes'] == 1) {
                                                                                      echo 'Yes';
                                                                                    } elseif ($medication['diabetes'] == 2) {
                                                                                      echo 'No';
                                                                                    }
                                                                                  } else {
                                                                                    echo 'Select';
                                                                                  } ?>
                                  </option>
                                  <option value="1">Yes</option>
                                  <option value="2">No</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="row-form clearfix">
                              <!-- select -->
                              <div class="form-group">
                                <label>Sickle Cell:</label>
                                <select name="sickle_cell" style="width: 100%;" required>
                                  <option value="<?= $medication['sickle_cell'] ?>"><?php if ($medication) {
                                                                                      if ($medication['sickle_cell'] == 1) {
                                                                                        echo 'Yes';
                                                                                      } elseif ($medication['sickle_cell'] == 2) {
                                                                                        echo 'No';
                                                                                      }
                                                                                    } else {
                                                                                      echo 'Select';
                                                                                    } ?>
                                  </option>
                                  <option value="1">Yes</option>
                                  <option value="2">No</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <input type="hidden" name="id" value="<?= $medication['id'] ?>">
                        <input type="hidden" name="action" value="edit">
                        <input type="submit" name="add_medications" value="Submit" class="btn btn-default">
                      </div>
                    </div>
                  </div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
              <!-- /.card -->
            </section>
            <!-- /.Left col -->
      </section>
      <!-- /.Left col -->


      <!-- right col (We are only adding the ID to make the widgets sortable)-->
      <section class="col-lg-5 connectedSortable">
        <!-- Map card -->
        <div class="card bg-gradient-primary">
          <div class="card-header border-0">
            <h3 class="card-title">
              <i class="fas fa-map-marker-alt mr-1"></i>
              Visitors
            </h3>
            <!-- card tools -->
            <div class="card-tools">
              <button type="button" class="btn btn-primary btn-sm daterange" title="Date range">
                <i class="far fa-calendar-alt"></i>
              </button>
              <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
            <!-- /.card-tools -->
          </div>

          <!-- /.card-body-->
          <div class="card-footer bg-transparent">
            <div class="row">
              <div class="col-4 text-center">
                <div id="sparkline-1"></div>
                <div class="text-white">Visitors</div>
              </div>
              <!-- ./col -->
              <div class="col-4 text-center">
                <div id="sparkline-2"></div>
                <div class="text-white">Online</div>
              </div>
              <!-- ./col -->
              <div class="col-4 text-center">
                <div id="sparkline-3"></div>
                <div class="text-white">Sales</div>
              </div>
              <!-- ./col -->
            </div>
            <!-- /.row -->
          </div>
        </div>
        <!-- /.card -->

        <!-- Calendar -->
        <div class="card bg-gradient-success">
          <div class="card-header border-0">

            <h3 class="card-title">
              <i class="far fa-calendar-alt"></i>
              Calendar
            </h3>
            <!-- tools card -->
            <div class="card-tools">
              <!-- button with a dropdown -->
              <div class="btn-group">
                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">
                  <i class="fas fa-bars"></i>
                </button>
                <div class="dropdown-menu" role="menu">
                  <a href="#" class="dropdown-item">Add new event</a>
                  <a href="#" class="dropdown-item">Clear events</a>
                  <div class="dropdown-divider"></div>
                  <a href="#" class="dropdown-item">View calendar</a>
                </div>
              </div>
              <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
            <!-- /. tools -->
          </div>
          <!-- /.card-header -->
          <div class="card-body pt-0">
            <!--The calendar -->
            <div id="calendar" style="width: 100%"></div>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </section>
      <!-- right col -->
    </div>
    <!-- /.row (main row) -->
  </div><!-- /.container-fluid -->
  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include 'footer.php'; ?>


  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="plugins/moment/moment.min.js"></script>
  <script src="plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.js"></script>
  <!-- AdminLTE for demo purposes -->
  <!-- <script src="dist/js/demo.js"></script> -->
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="dist/js/pages/dashboard.js"></script>
</body>

</html>