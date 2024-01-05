<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
$validate = new validate();
$successMessage = null;
$pageError = null;
$errorMessage = null;
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        if (Input::get('add_results')) {
            $validate = $validate->check($_POST, array());
            if ($validate->passed()) {
                try {
                    $i = 0;
                    foreach (Input::get('id') as $id) {
                        $status = 0;
                        if (Input::get('test_value')[$i]) {
                            $status = 1;
                        }
                        $user->updateRecord('appointment_test_list', array(
                            'test_value' => Input::get('test_value')[$i],
                            'status' => 1,
                            'request_status' => 1,
                        ), $id);
                        $i++;
                    }

                    $user->updateRecord('appointment_list', array(
                        'status' => 1,
                        'request_status' => 1,
                    ), $_GET['appointment_id']);

                    $date = date("Y-m-d\TH:i");

                    $appointment_id = $override->getNews('history_list', 'client_id', $_GET['cid'], 'appointment_id', $_GET['appointment_id']);

                    // var_dump($appointment_id);
                    if ($appointment_id) {
                        $user->updateRecord('history_list', array(
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'date_created' => date("Y-m-d\TH:i", strtotime($date)),
                            'remarks' => Input::get('remarks'),
                        ), $appointment_id[0]['id']);
                    } else {
                        $user->createRecord('history_list', array(
                            'appointment_id' => $_GET['appointment_id'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                            'date_created' => date("Y-m-d\TH:i", strtotime($date)),
                            'remarks' => Input::get('remarks'),
                            'client_id' => $_GET['cid'],
                        ));
                    }

                    Redirect::to('view_appointment_list.php?appointment_id=' . $_GET['appointment_id'] . '&cid=' . $_GET['cid'] . '&status=' . $_GET['status']);
                    $successMessage = 'Lab Results added Successful';
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_test_name')) {
            $user->updateRecord('lab_requests', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Test Deleted Successful';
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
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Results Form</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Results Form</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <?php
            $appointment_list = $override->get('appointment_list', 'client_id', $_GET['cid'])[0];
            $clients = $override->get('clients', 'id', $_GET['cid'])[0];
            $history_list = $override->getNews('history_list', 'client_id', $_GET['cid'], 'appointment_id', $_GET['appointment_id'])[0];


            if ($clients['gender'] = 1) {
                $gender = 'Male';
            } else {
                $gender = 'Male';
            }

            ?>

            <style>
                .img-thumb-path {
                    width: 100px;
                    height: 80px;
                    object-fit: scale-down;
                    object-position: center center;
                }
            </style>

            <!-- Main content -->
            <div class="container">
                <h4>Update Results Form</h4>
                <hr>

                <!-- <form method="post" action="checkbox-db.php"> -->
                <form method="post">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Lab Test</th>
                                <th>value</th>
                                <th>Units</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($override->get('appointment_test_list', 'patient_id', $_GET['cid']) as $value) {
                                $test = $override->get("test_list", "id", $value['test_id'])[0];
                            ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="id[]" value="<?= $value['id'] ?>">
                                        <input type="checkbox" name="status[]" value="1" <?php if ($value['status'] == 1) {
                                                                                                echo 'checked';
                                                                                            } ?>>
                                    </td>
                                    <td><?= $test['name'] ?> </td>

                                    <td><input type="number" name="test_value[]" class="form-control" value="<?php if ($value['test_value']) {
                                                                                                                    print_r($value['test_value']);
                                                                                                                }  ?>" <?php if ($user->data()->position != 1) {
                                                                                                                            echo 'readonly';
                                                                                                                        } ?>>
                                    </td>
                                    <td class="py-1 px-2"><?= $test['units'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="form-group">
                        <small class="mx-2">Remarks</small>
                        <textarea name="remarks" id="remarks" rows="3" class="form-control form-control-sm rounded-0" required><?= $history_list['remarks'] ?></textarea>
                    </div>
                    <div class="text-center">
                        <input type="hidden" name="appointment_id" value="<?= $_GET['appointment_id'] ?>">
                        <input type="submit" name="add_results" class="btn btn-success" value="Submit">
                    </div>
                </form>
            </div>
            <!-- /.content -->

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
        $(document).ready(function() {
            // $('#wait_ds').hide();
            $('#category').change(function() {
                var getUid = $(this).val();
                // $('#wait_ds').show();
                $.ajax({
                    url: "process.php?content=category",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#sub_category').html(data);
                        // console.log(data);
                        // $('#wait_ds').hide();
                    }
                });

            });

            $('#update_category').change(function() {
                var getUid = $(this).val();
                // $('#wait_ds').show();
                $.ajax({
                    url: "process.php?content=category",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#update_sub_category').html(data);
                        // console.log(data);
                        // $('#wait_ds').hide();
                    }
                });

            });
        });



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