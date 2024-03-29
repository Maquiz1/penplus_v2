<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

// $successMessage = null;
// $pageError = null;
// $errorMessage = null;
$numRec = 50;
if ($user->isLoggedIn()) {

    $profile = $override->getNews('clients', 'status', 1, 'id', $_GET['cid'])[0];
    $category = $override->getNews('main_diagnosis', 'status', 1, 'patient_id', $_GET['cid'])[0];

    if (Input::exists('post')) {
        if (Input::get('edit_client')) {
            $validate = $validate->check($_POST, array(
                'clinic_date' => array(
                    'required' => true,
                ),
                'firstname' => array(
                    'required' => true,
                ),
                'lastname' => array(
                    'required' => true,
                ),
                'dob' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $attachment_file = Input::get('image');
                    if (!empty($_FILES['image']["tmp_name"])) {
                        $attach_file = $_FILES['image']['type'];
                        if ($attach_file == "image/jpeg" || $attach_file == "image/jpg" || $attach_file == "image/png" || $attach_file == "image/gif") {
                            $folderName = 'clients/';
                            $attachment_file = $folderName . basename($_FILES['image']['name']);
                            if (@move_uploaded_file($_FILES['image']["tmp_name"], $attachment_file)) {
                                $file = true;
                            } else { {
                                    $errorM = true;
                                    $errorMessage = 'Your profile Picture Not Uploaded ,';
                                }
                            }
                        } else {
                            $errorM = true;
                            $errorMessage = 'None supported file format';
                        } //not supported format
                    } else {
                        $attachment_file = '';
                    }
                    if (!empty($_FILES['image']["tmp_name"])) {
                        $image = $attachment_file;
                    } else {
                        $image = Input::get('client_image');
                    }
                    if ($errorM == false) {
                        $age = $user->dateDiffYears(date('Y-m-d'), Input::get('dob'));
                        $user->updateRecord('clients', array(
                            'hospital_id' => Input::get('hospital_id'),
                            'clinic_date' => Input::get('clinic_date'),
                            'firstname' => Input::get('firstname'),
                            'middlename' => Input::get('middlename'),
                            'lastname' => Input::get('lastname'),
                            'dob' => Input::get('dob'),
                            'age' => $age,
                            'gender' => Input::get('gender'),
                            'site_id' => $user->data()->site_id,
                            'staff_id' => $user->data()->id,
                            'client_image' => $attachment_file,
                            'comments' => Input::get('comments'),
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                        ), Input::get('id'));

                        $successMessage = 'Client Updated Successful';
                    }
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
    <title>Penplus Database | Info</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include 'sidemenu.php'; ?>

        <?php if ($errorMessage) { ?>
            <div class="alert alert-danger">
                <h4>Error!</h4>
                <?= $errorMessage ?>
            </div>
        <?php } elseif ($pageError) { ?>
            <div class="alert alert-danger">
                <h4>Error!</h4>
                <?php foreach ($pageError as $error) {
                    echo $error . ' , ';
                } ?>
            </div>
        <?php } elseif ($successMessage) { ?>
            <div class="alert alert-success">
                <h4>Success!</h4>
                <?= $successMessage ?>
            </div>
        <?php } ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Summary</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                <li class="breadcrumb-item active">Patient Summary</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">

                            <!-- Profile Image -->
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="profile-user-img img-fluid img-circle" src="dist/img/user4-128x128.jpg" alt="User profile picture">
                                    </div>

                                    <h3 class="profile-username text-center">
                                        <?= $profile['firstname'] . ' - ' . $profile['middlename'] . ' - ' . $profile['lastname']; ?>
                                    </h3>

                                    <?php if ($category['cardiac'] == 1) { ?>
                                        <p class="text-muted text-center">Cardiac</p>

                                    <?php } elseif ($category['diabetes'] == 1) { ?>
                                        <p class="text-muted text-center">Diabtes</p>

                                    <?php } elseif ($category['sickle_cell'] == 1) { ?>
                                        <p class="text-muted text-center">Sickle Cell</p>

                                    <?php } else { ?>
                                        <p class="text-muted text-center">Not Diagnosised</p>
                                    <?php
                                    } ?>



                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>PATIENT ID</b> <a class="float-right"><?= $profile['study_id']; ?></a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>AGE</b> <a class="float-right"><?= $profile['age'] . ' '; ?>years</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>SEX</b>
                                            <a class="float-right">
                                                <?php if ($profile['gender'] == 1) { ?>
                                                    <p class="text-muted text-center">Male</p>

                                                <?php } elseif ($profile['gender'] == 2) { ?>
                                                    <p class="text-muted text-center">Female</p>

                                                <?php } ?>
                                            </a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Site</b>
                                            <a class="float-right">
                                                <?php if ($profile['site_id'] == 1) { ?>
                                                    <p class="text-muted text-center">Kondoa</p>

                                                <?php } elseif ($profile['site_id'] == 2) { ?>
                                                    <p class="text-muted text-center">Karatu</p>

                                                <?php } ?>
                                            </a>
                                        </li>
                                    </ul>

                                    <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->

                            <!-- About Me Box -->
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">About Patient</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <strong><i class="fas fa-book mr-1"></i> Education</strong>

                                    <?php if ($profile['education_level'] == 1) { ?>
                                        <p class="text-muted">Not attended school</p>

                                    <?php } elseif ($profile['education_level'] == 2) { ?>
                                        <p class="text-muted">Primary</p>

                                    <?php } elseif ($profile['education_level'] == 3) { ?>
                                        <p class="text-muted">Secondary</p>

                                    <?php } elseif ($profile['education_level'] == 4) { ?>
                                        <p class="text-muted">Certificate</p>

                                    <?php } elseif ($profile['education_level'] == 5) { ?>
                                        <p class="text-muted">Diploma</p>

                                    <?php } elseif ($profile['education_level'] == 6) { ?>
                                        <p class="text-muted">Undergraduate degree</p>

                                    <?php } elseif ($profile['education_level'] == 7) { ?>
                                        <p class="text-muted">Postgraduate degree</p>

                                    <?php } elseif ($profile['education_level'] == 8) { ?>
                                        <p class="text-muted">N / A</p>

                                    <?php } ?>
                                    <hr>

                                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Location / Address</strong>

                                    <p class="text-muted"><?= $profile['physical_address']; ?>, Tanzania</p>

                                    <hr>

                                    <strong><i class="fas fa-pencil-alt mr-1"></i> Employment status</strong>

                                    <?php if ($profile['employment_status'] == 1) { ?>
                                        <p class="text-muted">Employed</p>

                                    <?php } elseif ($profile['employment_status'] == 2) { ?>
                                        <p class="text-muted">Self-employed</p>

                                    <?php } elseif ($profile['employment_status'] == 3) { ?>
                                        <p class="text-muted">Employed but on leave of absence</p>

                                    <?php } elseif ($profile['employment_status'] == 4) { ?>
                                        <p class="text-muted">Unemployed</p>

                                    <?php } elseif ($profile['employment_status'] == 5) { ?>
                                        <p class="text-muted">Student</p>

                                    <?php } ?>

                                    <hr>

                                    <strong><i class="far fa-file-alt mr-1"></i> Notes / Comments</strong>

                                    <p class="text-muted"><?= $profile['comments']; ?></p>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"><a class="nav-link active" href="#history" data-toggle="tab">Patient Hitory & Complication</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#symptoms" data-toggle="tab">Symptoms and Exams</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#timeline3" data-toggle="tab">Test and Results</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#timeline4" data-toggle="tab">Vitals</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#timeline5" data-toggle="tab">Medications</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#timeline6" data-toggle="tab">Hospitalizations</a></li>
                                    </ul>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="history">
                                            <?php
                                            $x = 1;
                                            $historys = $override->getNews('history', 'status', 1, 'patient_id', $_GET['cid']);

                                            foreach ($historys as $history) { ?>
                                                <!-- The timeline -->
                                                <div class="timeline timeline-inverse">
                                                    <!-- timeline time label -->
                                                    <div class="time-label">
                                                        <span class="bg-success">
                                                            <?= $history['visit_date']; ?>
                                                        </span>
                                                    </div>
                                                    <!-- /.timeline-label -->
                                                    <!-- timeline item -->
                                                    <div>
                                                        <i class="fas fa-envelope bg-primary"></i>

                                                        <div class="timeline-item">
                                                            <span class="time timeline-header"><i class="far fa-clock"></i> <?= $history['visit_day']; ?></span>

                                                            <h3 class="timeline-header"><a href="#">List of Patient</a> History and Complications</h3>

                                                            <div class="timeline-body">
                                                                <?php if ($history['hypertension'] == 1) { ?>
                                                                    <p class="text-muted">Hypertension</p>
                                                                <?php } ?>
                                                                <?php if ($history['diabetes'] == 1) { ?>
                                                                    <p class="text-muted">Diabetes</p>
                                                                <?php } ?>
                                                                <?php if ($history['ckd'] == 1) { ?>
                                                                    <p class="text-muted">CKD</p>
                                                                <?php } ?>

                                                                <?php if ($history['depression'] == 1) { ?>
                                                                    <p class="text-muted">Depression</p>
                                                                <?php } ?>

                                                                <?php if ($history['hiv'] == 1) { ?>
                                                                    <p class="text-muted">HIV</p>
                                                                <?php } elseif ($history['hiv'] == 3) { ?>
                                                                    <p class="text-muted">HIV - UNKNOWN</p>

                                                                <?php } ?> <?php if ($history['art'] == 1) { ?>
                                                                    <p class="text-muted">ON ART</p>
                                                                <?php } ?>

                                                                <?php if ($history['tb'] == 1) { ?>
                                                                    <p class="text-muted">TB - Smear Positive</p>
                                                                <?php } elseif ($history['tb'] == 2) { ?>
                                                                    <p class="text-muted">TB - Smear Negative</p>
                                                                <?php } elseif ($history['tb'] == 3) { ?>
                                                                    <p class="text-muted">TB - EPTB</p>
                                                                <?php } elseif ($history['tb'] == 4) { ?>
                                                                    <p class="text-muted">NEVER HAD TB</p>
                                                                <?php } elseif ($history['tb'] == 5) { ?>
                                                                    <p class="text-muted">TB - UNKNOWN</p>
                                                                <?php } ?>


                                                                <?php if ($history['smoking'] == 1) { ?>
                                                                    <p class="text-muted">Smoking</p>
                                                                <?php } elseif ($history['smoking'] == 3) { ?>
                                                                    <p class="text-muted">Smoking - UNKNOWN</p>

                                                                <?php } ?>



                                                                <?php if ($history['hepatitis_test'] == 1) { ?>
                                                                    <p class="text-muted">Hepatitis test - ( Yes ) , <?php if ($history['hepatitis_results']) {
                                                                                                                            echo 'Test Date ' . $history['hepatitis_date'] . ' RESULTS' . $history['hepatitis_results'];
                                                                                                                        } else {
                                                                                                                            echo 'NONE';
                                                                                                                        } ?> </p>

                                                                <?php } ?>


                                                                <?php if ($history['blood_group'] == 1) { ?>
                                                                    <p class="text-muted">ABO Blood Group: A+</p>
                                                                <?php } elseif ($history['blood_group'] == 2) { ?>
                                                                    <p class="text-muted">ABO Blood Group: A-</p>
                                                                <?php } elseif ($history['blood_group'] == 3) { ?>
                                                                    <p class="text-muted">ABO Blood Group: B+</p>
                                                                <?php } elseif ($history['blood_group'] == 4) { ?>
                                                                    <p class="text-muted">ABO Blood Group: B-</p>
                                                                <?php } elseif ($history['blood_group'] == 5) { ?>
                                                                    <p class="text-muted">ABO Blood Group: O+</p>
                                                                <?php } elseif ($history['blood_group'] == 6) { ?>
                                                                    <p class="text-muted">ABO Blood Group: O-</p>
                                                                <?php } elseif ($history['blood_group'] == 7) { ?>
                                                                    <p class="text-muted">ABO Blood Group: AB+</p>
                                                                <?php } elseif ($history['blood_group'] == 8) { ?>
                                                                    <p class="text-muted">ABO Blood Group: AB-</p>
                                                                <?php } ?>


                                                                <?php if ($history['alcohol'] == 1) { ?>
                                                                    <p class="text-muted">Alcohol - Yes , Currently</p>
                                                                <?php } elseif ($history['alcohol'] == 2) { ?>
                                                                    <p class="text-muted">Alcohol - Yes , in the Past</p>
                                                                <?php } elseif ($history['alcohol'] == 3) { ?>
                                                                    <p class="text-muted">Alcohol - Never</p>
                                                                <?php } ?>




                                                                <?php if ($history['cardiovascular'] == 1) { ?>
                                                                    <p class="text-muted">Cardiovascular Diseases</p>
                                                                <?php } ?>
                                                                <?php if ($history['retinopathy'] == 1) { ?>
                                                                    <p class="text-muted">Retinopathy</p>
                                                                <?php } ?>
                                                                <?php if ($history['renal'] == 1) { ?>
                                                                    <p class="text-muted">Renal Disease</p>
                                                                <?php } ?> <?php if ($history['stroke_tia'] == 1) { ?>
                                                                    <p class="text-muted">Stroke / TIA</p>
                                                                <?php } ?>
                                                                <?php if ($history['pvd'] == 1) { ?>
                                                                    <p class="text-muted">PVD</p>
                                                                <?php } ?>
                                                                <?php if ($history['neuropathy'] == 1) { ?>
                                                                    <p class="text-muted">Neuropathy</p>
                                                                <?php } ?>
                                                                <?php if ($history['sexual_dysfunction'] == 1) { ?>
                                                                    <p class="text-muted">Sexual dysfunction</p>
                                                                <?php } ?>
                                                                <?php if ($history['pain_event'] == 1) { ?>
                                                                    <p class="text-muted">Pain Event</p>
                                                                <?php } ?>
                                                                <?php if ($history['stroke'] == 1) { ?>
                                                                    <p class="text-muted">Stroke</p>
                                                                <?php } ?>
                                                                <?php if ($history['pneumonia'] == 1) { ?>
                                                                    <p class="text-muted">Pneumonia</p>
                                                                <?php } ?>
                                                                <?php if ($history['blood_transfusion'] == 1) { ?>
                                                                    <p class="text-muted">Blood Transfusion ( YES )- Since Born :- <?php if ($history['transfusion_born']) {
                                                                                                                                        echo $history['transfusion_born'];
                                                                                                                                    } else {
                                                                                                                                        echo ' 0';
                                                                                                                                    }; ?> - For Past 12 Months :- <?php if ($history['transfusion_12months']) {
                                                                                                                                                                        echo $history['transfusion_12months'];
                                                                                                                                                                    } else {
                                                                                                                                                                        echo ' 0';
                                                                                                                                                                    } ?></p>
                                                                <?php } ?>

                                                                <?php if ($history['acute_chest'] == 1) { ?>
                                                                    <p class="text-muted">Acute chest syndrome</p>
                                                                <?php } ?>

                                                                <?php if ($history['other_complication'] == 1) { ?>
                                                                    <p class="text-muted"> Other Complication :- <?= $history['specify_complication']; ?></p>
                                                                <?php } ?>


                                                                <?php if ($history['cardiac_disease'] == 1) { ?>
                                                                    <p class="text-muted">Family History of cardiac disease ? ( YES )</p>
                                                                <?php } elseif ($history['cardiac_disease'] == 3) { ?>
                                                                    <p class="text-muted">Family History of cardiac disease ? ( UNKNOWN )</p>
                                                                <?php } ?>
                                                                <?php if ($history['cardiac_surgery'] == 1) { ?>
                                                                    <p class="text-muted">History of cardiac surgery ? ( YES )</p>

                                                                    <?php if ($history['cardiac_surgery_type'] == 1) { ?>
                                                                        <p class="text-muted">Valve Surgery</p>
                                                                    <?php } elseif ($history['cardiac_surgery_type'] == 2) { ?>
                                                                        <p class="text-muted">Defect Repair</p>
                                                                    <?php } elseif ($history['cardiac_surgery_type'] == 3) { ?>
                                                                        <p class="text-muted"> Other ( YES ) :-
                                                                            <?php if ($history['surgery_other']) {
                                                                                echo $history['surgery_other'];
                                                                            } else {
                                                                                echo ' NONE ';
                                                                            }; ?>
                                                                        </p>
                                                                    <?php } ?>

                                                                <?php } ?>


                                                                <?php if ($history['diabetic_disease'] == 1) { ?>
                                                                    <p class="text-muted">Family History of Diabetic disease ( YES )</p>
                                                                <?php } elseif ($history['diabetic_disease'] == 3) { ?>
                                                                    <p class="text-muted">Family History of Diabetic disease ( UNKNOWN )</p>
                                                                <?php } ?>

                                                                <?php if ($history['hypertension_disease'] == 1) { ?>
                                                                    <p class="text-muted">Hypertension ( YES )</p>
                                                                <?php } ?>




                                                                <?php if ($history['scd_disease'] == 1) { ?>
                                                                    <p class="text-muted">Family History of SCD ( YES ) - How many siblings do you have? :- <?php if ($history['siblings']) {
                                                                                                                                                                echo $history['siblings'];
                                                                                                                                                            } else {
                                                                                                                                                                echo ' 0';
                                                                                                                                                            }; ?> - How many of them are alive? :- <?php if ($history['sibling_salive']) {
                                                                                                                                                                                                        echo $history['sibling_salive'];
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        echo ' 0';
                                                                                                                                                                                                    } ?>
                                                                    </p>
                                                                <?php } elseif ($history['scd_disease'] == 3) { ?>
                                                                    <p class="text-muted">Family History of SCD ( UNKNOWN )</p>
                                                                <?php } ?>

                                                                <?php if ($history['history_scd'] == 1) { ?>
                                                                    <p class="text-muted">Family History of SCD ( YES ) - How many siblings do you have? :- <?php if ($history['siblings']) {
                                                                                                                                                                echo $history['siblings'];
                                                                                                                                                            } else {
                                                                                                                                                                echo ' 0';
                                                                                                                                                            }; ?> - How many of them are alive? :- <?php if ($history['sibling_salive']) {
                                                                                                                                                                                                        echo $history['sibling_salive'];
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        echo ' 0';
                                                                                                                                                                                                    } ?>
                                                                    </p>


                                                                <?php } elseif ($history['history_scd'] == 3) { ?>
                                                                    <p class="text-muted">Family History of SCD ( UNKNOWN )</p>
                                                                <?php } ?>


                                                                <?php if ($history['vaccine_history'] == 1) { ?>
                                                                    <p class="text-muted"> Vaccine History ( PNEUMOCOCCAL ) :- </p>
                                                                <?php } elseif ($history['vaccine_history'] == 2) { ?>
                                                                    <p class="text-muted"> Vaccine History ( MENINGOCOCCAL ) :- </p>
                                                                <?php } elseif ($history['vaccine_history'] == 3) { ?>
                                                                    <p class="text-muted">Vaccine History:- Haemophilus Influenza Type B History ( Hib ) :- </p>
                                                                <?php } elseif ($history['vaccine_history'] == 4) { ?>
                                                                    <p class="text-muted">Vaccine History:- PPCV 23 :- </p>
                                                                <?php } elseif ($history['vaccine_history'] == 5) { ?>
                                                                    <p class="text-muted">Vaccine History:- UNKNOWN :- </p>
                                                                <?php } ?>





                                                                <?php if ($history['history_other'] == 1) { ?>
                                                                    <p class="text-muted">Other Family History ( YES ) :- <?php if ($history['history_specify']) {
                                                                                                                                echo $history['history_specify'];
                                                                                                                            } else {
                                                                                                                                echo ' NONE ';
                                                                                                                            }; ?>
                                                                    </p>
                                                                <?php } ?>

                                                            </div>
                                                            <div class="timeline-footer">
                                                                <!-- <a href="#" class="btn btn-primary btn-sm">Read more</a> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- END timeline item -->

                                                    <div>
                                                        <i class="far fa-clock bg-gray"></i>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </div>
                                        <!-- /.tab-pane -->
                                        <div class="tab-pane" id="symptoms">
                                            <?php
                                            $x = 1;
                                            $symptoms = $override->getNews('symptoms', 'status', 1, 'patient_id', $_GET['cid']);

                                            foreach ($symptoms as $symptom) { ?> <!-- The timeline -->
                                                <div class="timeline timeline-inverse">
                                                    <!-- timeline time label -->
                                                    <div class="time-label">
                                                        <span class="bg-success">
                                                            <?= $symptom['visit_date']; ?>
                                                        </span>
                                                    </div>
                                                    <!-- /.timeline-label -->
                                                    <!-- timeline item -->
                                                    <div>
                                                        <i class="fas fa-envelope bg-primary"></i>

                                                        <div class="timeline-item">
                                                            <span class="time"><i class="far fa-clock"></i> <?= $symptom['visit_day']; ?></span>

                                                            <h3 class="timeline-header"><a href="#">Symptoms and Exams</a> sent you an email</h3>

                                                            <div class="timeline-body">
                                                                <?php if ($symptoms['chest_pain'] == 2) { ?>
                                                                    <p class="text-muted">Chest Pain</p>
                                                                <?php } ?>
                                                                <?php if ($symptoms['chest_pain'] == 2) { ?>
                                                                    <p class="text-muted">Chest Pain</p>
                                                                <?php } ?>

                                                            </div>
                                                            <div class="timeline-footer">
                                                                <a href="#" class="btn btn-primary btn-sm">Read more</a>
                                                                <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- END timeline item -->

                                                    <div>
                                                        <i class="far fa-clock bg-gray"></i>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </div>
                                        <!-- /.tab-pane -->
                                    </div>
                                    <!-- /.tab-content -->
                                </div><!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
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
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <!-- <script src="dist/js/demo.js"></script> -->
</body>

</html>