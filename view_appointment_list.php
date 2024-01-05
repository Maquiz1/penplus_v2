<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        $validate = new validate();
        if (Input::get('confirm_screening')) {
            $validate = $validate->check($_POST, array(
                // 'date_confirmed' => array(
                //     'required' => true,
                // ),
                'dm' => array(
                    'required' => true,
                ),
                'scd' => array(
                    'required' => true,
                ),
                'rhd' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $eligibility = 0;
                    if ((Input::get('dm') == 1 || Input::get('scd') == 1 || Input::get('rhd') == 1)) {
                        $eligibility = 1;
                    }

                    $doctor_confirm = 0;
                    if ((Input::get('dm') == 1 || Input::get('dm') == 2) && (Input::get('scd') == 1 || Input::get('scd') == 2) && (Input::get('rhd') == 1 || Input::get('rhd') == 2)) {
                        $doctor_confirm = 1;
                    }

                    $screening_id = $override->get('screening', 'patient_id', $_GET['cid'])[0];

                    if ($override->get('screening', 'patient_id', $_GET['cid'])) {
                        $user->updateRecord('screening', array(
                            'date_confirmed' => Input::get('date_confirmed'),
                            'dm' => Input::get('dm'),
                            'scd' => Input::get('scd'),
                            'rhd' => Input::get('rhd'),
                            'staff_id' => $user->data()->id,
                            'eligibility' => $eligibility,
                            'remarks' => Input::get('remarks'),
                            'doctor_confirm' => $doctor_confirm,
                        ), $screening_id['id']);

                        // $visit = $override->getNews('visit', 'client_id', $_GET['cid'], 'seq_no', 0, 'visit_name', 'Screening')[0];

                        // $user->updateRecord('visit', array(
                        //     'expected_date' => Input::get('screening_date'),
                        //     'visit_date' => Input::get('screening_date'),
                        // ), $visit['id']);
                    }

                    $user->updateRecord('clients', array(
                        'eligible' => $eligibility,
                        // 'enrolled' => $eligibility,
                        // 'screened' => 1,
                    ), $_GET['cid']);

                    $successMessage = 'Patient Successful Confirmed';

                    // if ($eligibility) {
                    //     Redirect::to('info.php?id=3&status=2');
                    // } else {
                    //     Redirect::to('info.php?id=3&status=' . $_GET['status']);
                    // }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
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
    <title>PenPlus DataBase | Vie Appointments List</title>

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
                            <h1>Test Results Form</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Test Results Form</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <?php
            $appointment_list = $override->get('appointment_list', 'client_id', $_GET['cid'])[0];
            $clients = $override->get('clients', 'id', $_GET['cid'])[0];

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
            <div class="content py-5">
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h4 class="card-title"><b>Test Results Details</b></h4>
                        <div class="card-tools">
                            <?php
                            if ($user->data()->position == 1) :
                            ?>

                                <a class="btn btn-default bg-gradient-navy btn-flat btn-sm" href="update_results.php?appointment_id=<?= $appointment_list['id'] ?>&cid=<?= $_GET['cid'] ?>&status=<?= $_GET['status'] ?>"> Update Results</a>
                            <?php endif; ?>
                            <a class="btn btn-primary border btn-flat btn-sm" href="pending_doctor_confirmation.php?status=1"><i class="fa fa-angle-right"></i> Back To Pending</a>

                            <a class="btn btn-default border btn-flat btn-sm" href="appointments.php?status=<?= $_GET['status'] ?>"><i class="fa fa-angle-left"></i> Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="container-fluid" id="outprint">
                            <div class="row">
                                <div class="col-2 border bg-gradient-primary text-light">Appointment Code</div>
                                <div class="col-4 border"><?= $appointment_list['code']; ?></div>
                                <div class="col-2 border bg-gradient-primary text-light">Date Requested</div>
                                <div class="col-4 border"><?= $appointment_list['date_requested']; ?></div>
                                <div class="col-2 border bg-gradient-primary text-light">Patient Name</div>
                                <div class="col-10 border"><?= $clients['firstname'] . '-' . $clients['lastname']; ?></div>
                                <div class="col-1 border bg-gradient-primary text-light">Gender</div>
                                <div class="col-3 border"><?= $gender ?></div>
                                <div class="col-1 border bg-gradient-primary text-light">Contact #</div>
                                <div class="col-3 border"><?= $clients['phone_number'] ?></div>
                                <div class="col-1 border bg-gradient-primary text-light">Email</div>
                                <div class="col-3 border"><?= $clients['phone_number'] ?></div>
                                <div class="col-2 border bg-gradient-primary text-light">Address</div>
                                <div class="col-10 border"><?= $clients['physical_address'] ?></div>
                                <div class="col-2 border bg-gradient-primary text-light">Status</div>
                                <div class="col-4 border ">
                                    <?php

                                    switch ($appointment_list['status']) {
                                        case 0:
                                            echo '<span class="">Pending</span>';
                                            break;
                                        case 1:
                                            echo '<span class">Done</span>';

                                            break;
                                        case 2:
                                            echo '<span class">Sample Collected</span>';
                                            break;
                                        case 3:
                                            echo '<span class="rounde">Delivered to Lab</span>';
                                            break;
                                        case 4:
                                            echo '<span class">Approved</span>';
                                            break;
                                        case 5:
                                            echo '<span class">Cancelled</span>';
                                            break;
                                        case 6:
                                            echo '<span class">Report Uploaded</span>';
                                            break;
                                    }
                                    ?>
                                </div>
                                <div class="col-2 border bg-gradient-primary text-light">Prescription</div>
                                <div class="col-4 border ">
                                    <?php

                                    echo "N/A";

                                    ?>
                                </div>
                                <div class="col-2 border bg-gradient-primary text-light">Uploaded Report</div>
                                <div class="col-10 border ">
                                    N/A
                                </div>
                            </div>
                            <hr>
                            <fieldset>
                                <legend class="text-muted">List of Tests</legend>
                                <table class="table table-striped table-bordered">
                                    <colgroup>
                                        <col width="10%">
                                        <col width="45%">
                                        <col width="45%">
                                    </colgroup>
                                    <thead>
                                        <tr class="bg-gradient-primary text-light">
                                            <th class="text-center">#</th>
                                            <th>Name</th>
                                            <th>Value</th>
                                            <th>Units</th>
                                            <th>Ranges</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $appointment_test_list = $override->get('appointment_test_list', 'patient_id', $_GET['cid']);
                                        if ($appointment_test_list) {
                                            foreach ($override->get('appointment_test_list', 'patient_id', $_GET['cid']) as $value) {
                                                $test = $override->get("test_list", "id", $value['test_id'])[0];
                                        ?>
                                                <tr>
                                                    <td class="py-1 px-2 text-center"><?= $i++; ?></td>
                                                    <td class="py-1 px-2"><?= $test['name'] ?></td>
                                                    <td class="py-1 px-2"><?= $value['test_value'] ?></td>
                                                    <td class="py-1 px-2"><?= $test['units'] ?></td>
                                                    <td class="py-1 px-2"><?= $test['minimum'] . ' -' . $test['maximum']  ?></td>
                                                    <td class="py-1 px-2 text-right"><?= number_format($test['cost'], 2) ?></td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <th class="py-1 text-center" colspan="4">No Data</th>
                                            </tr>
                                        <?php
                                        } ?>
                                    </tbody>
                                </table>
                            </fieldset>

                            <?php
                            $screening = $override->get('screening', 'patient_id', $_GET['cid'])[0];

                            // if (!$screening['eligibility'] & $appointment_list['status'] == 1) {
                            if ($appointment_list['status'] == 1) {
                            ?>

                                <hr>
                                <fieldset>
                                    <legend class="text-muted">Doctor Confirmation</legend>
                                    <!-- general form elements -->
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Doctor Confirmation</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <!-- form start -->
                                        <form method="post">
                                            <!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Date Confirmed:</label>
                                                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                                                <input type="text" name="date_confirmed" class="form-control datetimepicker-input" data-target="#reservationdate" value="<?= $screening['date_confirmed'] ?>" />
                                                                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Confirmed for Diabetes ?</label>
                                                            <select name="dm" class="form-control select2" style="width: 100%;" required>
                                                                <!-- <option selected="selected">Alabama</option> -->
                                                                <option value="<?= $screening['dm'] ?>"><?php if ($screening) {
                                                                                                            if ($screening['dm'] == 1) {
                                                                                                                echo 'Yes';
                                                                                                            } elseif ($screening['dm'] == 2) {
                                                                                                                echo 'No';
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo 'Select';
                                                                                                        } ?></option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Confirmed for RHD ?</label>
                                                            <select name="rhd" class="form-control select2" style="width: 100%;" required>
                                                                <option value="<?= $screening['rhd'] ?>"><?php if ($screening) {
                                                                                                                if ($screening['rhd'] == 1) {
                                                                                                                    echo 'Yes';
                                                                                                                } elseif ($screening['rhd'] == 2) {
                                                                                                                    echo 'No';
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Select';
                                                                                                            } ?></option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Confirmed for SCD ?</label>
                                                            <select name="scd" class="form-control select2" style="width: 100%;" required>
                                                                <option value="<?= $screening['scd'] ?>"><?php if ($screening) {
                                                                                                                if ($screening['scd'] == 1) {
                                                                                                                    echo 'Yes';
                                                                                                                } elseif ($screening['scd'] == 2) {
                                                                                                                    echo 'No';
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Select';
                                                                                                            } ?></option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="col-sm-12">
                                                    <!-- textarea -->
                                                    <div class="form-group">
                                                        <label>Remarks By Doctor</label>
                                                        <textarea class="form-control" rows="3" name="remarks" placeholder="Enter ..."><?= $screening['remarks'] ?></textarea>
                                                    </div>
                                                </div>
                                                <hr>

                                            </div>
                                            <!-- /.card-body -->

                                            <div class="card-footer">
                                                <input type="submit" name="confirm_screening" value="Confirm" class="btn btn-info">
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.card -->
                                </fieldset>


                            <?php } ?>

                            <hr>
                            <fieldset>
                                <legend class="text-muted">Update History</legend>
                                <table class="table table-striped table-bordered">
                                    <colgroup>
                                        <col width="10%">
                                        <col width="20%">
                                        <col width="40%">
                                        <col width="30%">
                                    </colgroup>
                                    <thead>
                                        <tr class="bg-gradient-primary text-light">
                                            <th class="text-center">#</th>
                                            <th>Date</th>
                                            <th>Remarks</th>
                                            <th>New Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $history = $override->get('history_list', 'client_id', $_GET['cid']);
                                        if ($history) {
                                            foreach ($override->get('history_list', 'client_id', $_GET['cid']) as $value) {
                                        ?>
                                                <tr>
                                                    <td class="py-1 px-2 text-center"><?= $i++; ?></td>
                                                    <td class="py-1 px-2"><?= date("M d, Y H:i", strtotime($value['date_created'])) ?></td>
                                                    <td class="py-1 px-2"><?= $value['remarks'] ?></td>
                                                    <td class="py-1 px-2">
                                                        <?php
                                                        switch ($value['status']) {
                                                            case 0:
                                                                echo '<span class="">Pending</span>';
                                                                break;
                                                            case 1:
                                                                echo '<span class">Done</span>';
                                                                break;
                                                            case 2:
                                                                echo '<span class">Sample Collected</span>';
                                                                break;
                                                            case 3:
                                                                echo '<span class="rounde">Delivered to Lab</span>';
                                                                break;
                                                            case 4:
                                                                echo '<span class">Approved</span>';
                                                                break;
                                                            case 5:
                                                                echo '<span class">Cancelled</span>';
                                                                break;
                                                            case 6:
                                                                echo '<span class">Report Uploaded</span>';
                                                                break;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <th class="py-1 text-center" colspan="4">No Data</th>
                                            </tr>
                                        <?php
                                        } ?>

                                    </tbody>
                                </table>
                            </fieldset>
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