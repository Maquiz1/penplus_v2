<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$successMessage = null;
$pageError = null;
$errorMessage = null;

if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        $validate = new validate();
        if (Input::get('add_test')) {
            $date_requested = date("Y-m-d", strtotime(Input::get('date_requested')));

            $client_request = $override->get('appointment_list', 'client_id', $_GET['cid']);
            if (!$client_request) {
                $i = 0;
                $selected_test = Input::get('test_name');
                $tests = implode(",", $selected_test);
                $date = date("Y-m-d\TH:i");
                $user->createRecord('appointment_list', array(
                    'date_requested' => $date_requested,
                    'schedule' => date("Y-m-d\TH:i", strtotime($date)),
                    'code' => 111,
                    'client_id' => $_GET['cid'],
                    'test_id' => $tests,
                    'prescription_path' => 'N/A',
                    'date_created' => date("Y-m-d\TH:i", strtotime($date)),
                    'date_updated' => date("Y-m-d\TH:i", strtotime($date)),
                    'staff_id' => $user->data()->id,
                    'status' => 0,
                    'request_status' => 0,
                    'site_id' => $user->data()->site_id,
                ));



                $appointment_list = $override->getlastRow('appointment_list', 'client_id', $_GET['cid'], 'id')[0];

                foreach (Input::get('test_name') as $value) {
                    $test = $override->getNews('test_list', 'status', 1, 'id', $value)[0];
                    $user->createRecord('appointment_test_list', array(
                        'appointment_id' => $appointment_list['id'],
                        'date_requested' => $date_requested,
                        'test_id' => $value,
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 0,
                        'request_status' => 0,
                        'site_id' => $user->data()->site_id,
                        'date_created' => date("Y-m-d\TH:i", strtotime($date)),
                        'created_on' => date("Y-m-d"),
                    ));
                }
                $user->createRecord('history_list', array(
                    'appointment_id' => $appointment_list['id'],
                    'staff_id' => $user->data()->id,
                    'status' => 0,
                    'site_id' => $user->data()->site_id,
                    'date_created' => date("Y-m-d\TH:i", strtotime($date)),
                    'remarks' => 'Lab test requested',
                    'client_id' => $_GET['cid'],
                ));

                // Redirect::to('info.php?id=' . $_GET['id'] . '&status=' . $_GET['status']);
                Redirect::to('appointments.php?status=0');
                // Redirect::to('view_appointment_list.php?appointment_id=' . $_GET['appointment_id'] . '&cid=' . $_GET['cid'] . '&status=' . $_GET['status']);

            } else {
                $errorMessage = 'Client already have a request.';
            }
        } elseif (Input::get('update_category')) {
            $validate = $validate->check($_POST, array(
                // 'name' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('category', array(
                        'name' => Input::get('name'),
                        'status' => Input::get('status'),
                        'description' => Input::get('description'),
                    ), Input::get('id'));
                    $successMessage = 'New Test Category Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('deactivate_category')) {
            $user->updateRecord('category', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Category Deleted Successful';
        } elseif (Input::get('activate_category')) {
            $user->updateRecord('category', array(
                'status' => 1,
            ), Input::get('id'));
            $successMessage = 'Category Deleted Successful';
        } elseif (Input::get('delete_category')) {
            $user->deleteRecord('category', 'id', Input::get('id'));
            $successMessage = 'Category Deleted Successful';
        }
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
    <title>PenPlus DataBase | Lab Section</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <!-- BS Stepper -->
    <link rel="stylesheet" href="plugins/bs-stepper/css/bs-stepper.min.css">
    <!-- dropzonejs -->
    <link rel="stylesheet" href="plugins/dropzone/min/dropzone.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
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
            <section class="content-header">
                <div class="container-fluid">
                    <!-- <div class="container-fluid"> -->
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <h1>
                                <?php if ($errorMessage) { ?>
                                    <div class="block">
                                        <div class="alert alert-danger">
                                            <b>Error!</b> <?= $errorMessage ?>
                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        </div>
                                    </div>
                                <?php } elseif ($pageError) { ?>
                                    <div class="block col-md-12">
                                        <div class="alert alert-danger">
                                            <b>Error!</b> <?php foreach ($pageError as $error) {
                                                                echo $error . ' , ';
                                                            } ?>
                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        </div>
                                    </div>
                                <?php } elseif ($successMessage) { ?>
                                    <div class="block">
                                        <div class="alert alert-success">
                                            <b>Success!</b> <?= $successMessage ?>
                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        </div>
                                    </div>
                                <?php } ?>
                            </h1>
                        </div>
                    </div>
                    <!-- </div> -->
                    <!-- /.container-fluid -->
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Request Form</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Request Form</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <style>
                .img-thumb-path {
                    width: 100px;
                    height: 80px;
                    object-fit: scale-down;
                    object-position: center center;
                }
            </style>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Request New Tests</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="post">
                    <div class="card-body">
                        <!-- Date -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date requested:</label>
                                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                        <input type="text" name="date_requested" class="form-control datetimepicker-input" data-target="#reservationdate" value="" />
                                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Test Name</label>
                                    <select class="select2" multiple="multiple" data-placeholder="Select a State" style="width: 100%;" name="test_name[]">
                                        <?php
                                        $tests = $override->get("test_list", "status", 1);
                                        foreach ($tests as $test1) { ?>
                                            <option value="<?= $test1['id'] ?>"><?= $test1['name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <!-- /.form-group -->
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <a href="info.php?id=3&status=1" class="btn btn-danger">Back</a>
                        <input type="submit" name="add_test" value="Add New Test Request" class="btn btn-info">
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.content-wrapper -->
        <?php include 'footer.php'; ?>

        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- InputMask -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap color picker -->
    <script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Bootstrap Switch -->
    <script src="plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <!-- BS-Stepper -->
    <script src="plugins/bs-stepper/js/bs-stepper.min.js"></script>
    <!-- dropzonejs -->
    <script src="plugins/dropzone/min/dropzone.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <!-- <script src="dist/js/demo.js"></script> -->
    <!-- Page specific script -->
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            //Datemask dd/mm/yyyy
            $('#datemask').inputmask('dd/mm/yyyy', {
                'placeholder': 'dd/mm/yyyy'
            })
            //Datemask2 mm/dd/yyyy
            $('#datemask2').inputmask('mm/dd/yyyy', {
                'placeholder': 'mm/dd/yyyy'
            })
            //Money Euro
            $('[data-mask]').inputmask()

            //Date picker
            $('#reservationdate').datetimepicker({
                format: 'L'
            });

            //Date and time picker
            $('#reservationdatetime').datetimepicker({
                icons: {
                    time: 'far fa-clock'
                }
            });

            //Date range picker
            $('#reservation').daterangepicker()
            //Date range picker with time picker
            $('#reservationtime').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                locale: {
                    format: 'MM/DD/YYYY hh:mm A'
                }
            })
            //Date range as a button
            $('#daterange-btn').daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
                }
            )

            //Timepicker
            $('#timepicker').datetimepicker({
                format: 'LT'
            })

            //Bootstrap Duallistbox
            $('.duallistbox').bootstrapDualListbox()

            //Colorpicker
            $('.my-colorpicker1').colorpicker()
            //color picker with addon
            $('.my-colorpicker2').colorpicker()

            $('.my-colorpicker2').on('colorpickerChange', function(event) {
                $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
            })

            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            })

        })
        // BS-Stepper Init
        document.addEventListener('DOMContentLoaded', function() {
            window.stepper = new Stepper(document.querySelector('.bs-stepper'))
        })

        // DropzoneJS Demo Code Start
        Dropzone.autoDiscover = false

        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
        var previewNode = document.querySelector("#template")
        previewNode.id = ""
        var previewTemplate = previewNode.parentNode.innerHTML
        previewNode.parentNode.removeChild(previewNode)

        var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
            url: "/target-url", // Set the url
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate,
            autoQueue: false, // Make sure the files aren't queued until manually added
            previewsContainer: "#previews", // Define the container to display the previews
            clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
        })

        myDropzone.on("addedfile", function(file) {
            // Hookup the start button
            file.previewElement.querySelector(".start").onclick = function() {
                myDropzone.enqueueFile(file)
            }
        })

        // Update the total progress bar
        myDropzone.on("totaluploadprogress", function(progress) {
            document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
        })

        myDropzone.on("sending", function(file) {
            // Show the total progress bar when upload starts
            document.querySelector("#total-progress").style.opacity = "1"
            // And disable the start button
            file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
        })

        // Hide the total progress bar when nothing's uploading anymore
        myDropzone.on("queuecomplete", function(progress) {
            document.querySelector("#total-progress").style.opacity = "0"
        })

        // Setup the buttons for all transfers
        // The "add files" button doesn't need to be setup because the config
        // `clickable` has already been specified.
        document.querySelector("#actions .start").onclick = function() {
            myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
        }
        document.querySelector("#actions .cancel").onclick = function() {
            myDropzone.removeAllFiles(true)
        }
        // DropzoneJS Demo Code End
    </script>
</body>

</html>