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
        if (Input::get('delete_appointment1')) {
            $user->updateRecord('appointment_list', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Test Deleted Successful';
        } elseif (Input::get('delete_appointment')) {
            $user->deleteRecord('appointment_list', 'id', Input::get('id'));
            $successMessage = 'Request Test Deleted Successful';
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
    <title>PenPlus DataBase | Appointments</title>

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
                            <h1>List of Test Requests</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">List of Test Requests</li>
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
            <div class="card card-outline card-primary rounded-0 shadow">
                <div class="card-header">
                    <h3 class="card-title">List of Test Requests</h3>
                    <div class="card-tools">
                        <a class="btn btn-default border btn-flat btn-sm" href="dashboard.php"><i class="fa fa-angle-left"></i> Back</a>
                        <!-- <a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-sm btn-primary"><span class="fas fa-plus"></span> Book New Test Request</a> -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="container-fluid">
                            <table class="table table-bordered table-hover table-striped">
                                <colgroup>
                                    <col width="5%">
                                    <col width="20%">
                                    <col width="15%">
                                    <col width="30%">
                                    <col width="15%">
                                    <col width="15%">
                                </colgroup>
                                <thead>
                                    <tr class="bg-gradient-primary text-light">
                                        <th>#</th>
                                        <th>Date Requested</th>
                                        <th>Client Name</th>
                                        <th>Test</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if ($_GET['status'] == 1) {
                                        $appointment_list = $override->get("appointment_list", 'status', 1);
                                    } else {
                                        $appointment_list = $override->get("appointment_list", 'status', 0);
                                    }
                                    foreach ($appointment_list as $value) {
                                        $test_name = $override->get("test_list", "id", $value['test_id']);
                                        $client_name = $override->get("clients", "id", $value['client_id'])[0];
                                        $test_list = $override->SelectTests('test_list', 'id', 'test_id', 'appointment_test_list', 'appointment_id', $value['id']);
                                        $test_arr = array_column($test_list, 'name');
                                        $test = implode(", ", $test_arr);

                                        $test_arr_id = array_column($test_list, 'id');
                                        $test_ids = implode(", ", $test_arr_id);
                                        // print_r($test_ids)
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i++; ?></td>
                                            <td class=""><?php echo date("Y-m-d H:i", strtotime($value['date_created'])) ?></td>
                                            <td class=""><?= $client_name['firstname'] . ' - ' . $client_name['lastname'] ?></td>
                                            <td class="">
                                                <p class="m-0 truncate-1"><?= $test ?></p>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                switch ($value['status']) {
                                                    case 0:
                                                        echo '<span class="rounded-pill badge badge-secondary ">Pending</span>';
                                                        break;
                                                    case 1:
                                                        echo '<span class="rounded-pill badge badge-primary ">Done</span>';
                                                        break;
                                                    case 2:
                                                        echo '<span class="rounded-pill badge badge-warning ">Sample Collected</span>';
                                                        break;
                                                    case 3:
                                                        echo '<span class="rounded-pill badge badge-primary bg-teal ">Delivered to Lab</span>';
                                                        break;
                                                    case 4:
                                                        echo '<span class="rounded-pill badge badge-success ">Approved</span>';

                                                        break;
                                                    case 5:
                                                        echo '<span class="rounded-pill badge badge-danger ">Cancelled</span>';
                                                        break;
                                                    case 6:
                                                        echo '<span class="rounded-pill badge-light badge border text-dark ">Report Uploaded</span>';
                                                        break;
                                                }
                                                ?>
                                            </td>
                                            <td align="center">
                                                <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                                    Action
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu" role="menu">
                                                    <a class="dropdown-item" href="view_appointment_list.php?appointment_id=<?= $value['id'] ?>&cid=<?= $client_name['id'] ?>&status=<?= $_GET['status'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
                                                    <?php if ($value['status'] < 1) :
                                                        if ($user->data()->position == 1) :

                                                    ?>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $value['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
                                                            <div class="dropdown-divider"></div>
                                                            <?php
                                                            if ($user->data()->power == 1) :

                                                            ?>
                                                                <a class="dropdown-item" href="#delete<?= $value['id'] ?>" role="button" data-toggle="modal"><span class="fa fa-trash text-danger"></span> Delete</a>

                                                    <?php endif;
                                                        endif;
                                                    endif;
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="delete<?= $value['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form method="post">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4>Delete Test Request</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <strong style="font-weight: bold;color: red">
                                                                <p>Are you sure you want to delete this Test Request</p>
                                                            </strong>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="id" value="<?= $value['id'] ?>">
                                                            <input type="submit" name="delete_appointment" value="Delete" class="btn btn-danger">
                                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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