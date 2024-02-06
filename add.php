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
        if (Input::get('add_user')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'firstname' => array(
                    'required' => true,
                ),
                'lastname' => array(
                    'required' => true,
                ),
                'position' => array(
                    'required' => true,
                ),
                'site_id' => array(
                    'required' => true,
                ),
                'username' => array(
                    'required' => true,
                    'unique' => 'user'
                ),
                'phone_number' => array(
                    'required' => true,
                    'unique' => 'user'
                ),
                'email_address' => array(
                    'unique' => 'user'
                ),
            ));
            if ($validate->passed()) {
                $salt = $random->get_rand_alphanumeric(32);
                $password = '12345678';
                switch (Input::get('position')) {
                    case 1:
                        $accessLevel = 1;
                        break;
                    case 2:
                        $accessLevel = 2;
                        break;
                    case 3:
                        $accessLevel = 3;
                        break;
                }
                try {
                    $user->createRecord('user', array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'username' => Input::get('username'),
                        'position' => Input::get('position'),
                        'phone_number' => Input::get('phone_number'),
                        'password' => Hash::make($password, $salt),
                        'salt' => $salt,
                        'create_on' => date('Y-m-d'),
                        'last_login' => '',
                        'status' => 1,
                        'power' => 0,
                        'email_address' => Input::get('email_address'),
                        'accessLevel' => $accessLevel,
                        'user_id' => $user->data()->id,
                        'site_id' => Input::get('site_id'),
                        'count' => 0,
                        'pswd' => 0,
                    ));
                    $successMessage = 'Account Created Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_position')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('position', array(
                        'name' => Input::get('name'),
                    ));
                    $successMessage = 'Position Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_site')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('site', array(
                        'name' => Input::get('name'),
                    ));
                    $successMessage = 'Site Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_visit')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'code' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('schedule', array(
                        'name' => Input::get('name'),
                        'code' => Input::get('code'),
                    ));
                    $successMessage = 'Schedule Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_client')) {
            $validate = new validate();
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
                $errorM = false;
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
                    if ($errorM == false) {
                        $chk = true;
                        $screening_id = $random->get_rand_alphanumeric(8);
                        $check_screening = $override->get('clients', 'participant_id', $screening_id)[0];
                        while ($chk) {
                            $screening_id = strtoupper($random->get_rand_alphanumeric(8));
                            if (!$check_screening = $override->get('clients', 'participant_id', $screening_id)) {
                                $chk = false;
                            }
                        }
                        $age = $user->dateDiffYears(date('Y-m-d'), Input::get('dob'));

                        // $age = $user->dateDiffYears(date('Y-m-d'), Input::get('dob'));
                        $check_clients = $override->countData3('clients', 'firstname', Input::get('firstname'), 'middlename', Input::get('middlename'), 'lastname', Input::get('lastname'));

                        $firstname = Input::get('firstname');
                        $middlename = Input::get('middlename');
                        $lastname = Input::get('lastname');

                        if ($check_clients >= 1) {
                            $errorMessage = 'Participant ' . $firstname . ' -  ' . $middlename . '  -  ' . $lastname . '  -  ' . '  Already Registered';
                        } else {

                            if ($override->get('clients', 'id', $_GET['cid'])) {
                                $user->updateRecord('clients', array(
                                    'hospital_id' => Input::get('hospital_id'),
                                    'clinic_date' => Input::get('clinic_date'),
                                    'firstname' => Input::get('firstname'),
                                    'middlename' => Input::get('middlename'),
                                    'lastname' => Input::get('lastname'),
                                    'dob' => Input::get('dob'),
                                    'age' => $age,
                                    'gender' => Input::get('gender'),
                                    'employment_status' => Input::get('employment_status'),
                                    'education_level' => Input::get('education_level'),
                                    'occupation' => Input::get('occupation'),
                                    'exposure' => Input::get('exposure'),
                                    'phone_number' => Input::get('phone_number'),
                                    'guardian_phone' => Input::get('guardian_phone'),
                                    'guardian_name' => Input::get('guardian_name'),
                                    'relation_patient' => Input::get('relation_patient'),
                                    'physical_address' => Input::get('physical_address'),
                                    'client_image' => $attachment_file,
                                    'comments' => Input::get('comments'),
                                ), $_GET['cid']);
                            } else {
                                $user->createRecord('clients', array(
                                    'participant_id' => $screening_id,
                                    'study_id' => '',
                                    'hospital_id' => Input::get('hospital_id'),
                                    'clinic_date' => Input::get('clinic_date'),
                                    'firstname' => Input::get('firstname'),
                                    'middlename' => Input::get('middlename'),
                                    'lastname' => Input::get('lastname'),
                                    'dob' => Input::get('dob'),
                                    'age' => $age,
                                    'gender' => Input::get('gender'),
                                    'employment_status' => Input::get('employment_status'),
                                    'education_level' => Input::get('education_level'),
                                    'occupation' => Input::get('occupation'),
                                    'exposure' => Input::get('exposure'),
                                    'phone_number' => Input::get('phone_number'),
                                    'guardian_phone' => Input::get('guardian_phone'),
                                    'guardian_name' => Input::get('guardian_name'),
                                    'relation_patient' => Input::get('relation_patient'),
                                    'physical_address' => Input::get('physical_address'),
                                    'site_id' => $user->data()->site_id,
                                    'staff_id' => $user->data()->id,
                                    'client_image' => $attachment_file,
                                    'comments' => Input::get('comments'),
                                    'status' => 1,
                                    'created_on' => date('Y-m-d'),
                                ));

                                $last_row = $override->lastRow('clients', 'id')[0];

                                $user->createRecord('visit', array(
                                    'study_id' => '',
                                    'visit_name' => 'Registration Visit',
                                    'visit_code' => 'RV',
                                    'visit_day' => 'Day -1',
                                    'expected_date' => Input::get('clinic_date'),
                                    'visit_date' => Input::get('clinic_date'),
                                    'visit_window' => 0,
                                    'status' => 1,
                                    'client_id' => $last_row['id'],
                                    'created_on' => date('Y-m-d'),
                                    'seq_no' => -1,
                                    'reasons' => '',
                                    'visit_status' => 1,
                                    'site_id' => $user->data()->site_id,
                                ));
                            }

                            $successMessage = 'Client Added Successful';
                            Redirect::to('info.php?id=3&status=5&site_id=' . $user->data()->site_id);
                        }
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_demographic')) {
            $validate = $validate->check($_POST, array(
                // 'visit_date' => array(
                //     'required' => true,
                // ),
                'next_visit' => array(
                    'required' => true,
                ),
                'referred' => array(
                    'required' => true,
                ),
                // 'chw' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {

                    $demographic = $override->get3('demographic', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    if ($demographic) {
                        $user->updateRecord('demographic', array(
                            'visit_date' => Input::get('visit_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'household_size' => Input::get('household_size'),
                            'grade_age' => Input::get('grade_age'),
                            'school_attendance' => Input::get('school_attendance'),
                            'missed_school' => Input::get('missed_school'),
                            'next_visit' => Input::get('next_visit'),
                            'chw' => Input::get('chw'),
                            'comments' => Input::get('comments'),
                            'referred' => Input::get('referred'),
                            'referred_other' => Input::get('referred_other'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $demographic['id']);
                    } else {
                        $user->createRecord('demographic', array(
                            'visit_date' => Input::get('visit_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'household_size' => Input::get('household_size'),
                            'grade_age' => Input::get('grade_age'),
                            'school_attendance' => Input::get('school_attendance'),
                            'missed_school' => Input::get('missed_school'),
                            'next_visit' => Input::get('next_visit'),
                            'chw' => Input::get('chw'),
                            'comments' => Input::get('comments'),
                            'referred' => Input::get('referred'),
                            'referred_other' => Input::get('referred_other'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Demographic added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_main_diagnosis')) {
            $validate = $validate->check($_POST, array(
                'cardiac' => array(
                    'required' => true,
                ),
                'diabetes' => array(
                    'required' => true,
                ),
                'sickle_cell' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {

                    $main_diagnosis = $override->get3('main_diagnosis', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    // if ((Input::get('cardiac') == 1 && Input::get('diabetes') == 1 && Input::get('sickle_cell') == 1)
                    //     || (Input::get('cardiac') == 1 && Input::get('diabetes') == 1)
                    //     || (Input::get('cardiac') == 1 && Input::get('sickle_cell') == 1)
                    //     || (Input::get('diabetes') == 1 && Input::get('sickle_cell') == 1)
                    // ) {
                    //     $errorMessage = 'Patient Diagnosed with more than one Disease';
                    // } else {

                    if ($main_diagnosis) {

                        $user->updateRecord('main_diagnosis', array(
                            'visit_date' => Input::get('diagnosis_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'cardiac' => Input::get('cardiac'),
                            'diabetes' => Input::get('diabetes'),
                            'sickle_cell' => Input::get('sickle_cell'),
                            'comments' => Input::get('comments'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $main_diagnosis['id']);
                    } else {
                        $user->createRecord('main_diagnosis', array(
                            'visit_date' => Input::get('diagnosis_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'cardiac' => Input::get('cardiac'),
                            'diabetes' => Input::get('diabetes'),
                            'sickle_cell' => Input::get('sickle_cell'),
                            'comments' => Input::get('comments'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }


                    $user->updateRecord('clients', array(
                        'cardiac' => Input::get('cardiac'),
                        'diabetes' => Input::get('diabetes'),
                        'sickle_cell' => Input::get('sickle_cell'),
                    ), $_GET['cid']);


                    $successMessage = 'Diagnosis added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                    // }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_cardiac')) {
            $validate = $validate->check($_POST, array(
                // 'main_diagnosis' => array(
                //     'required' => true,
                // ),


            ));
            if ($validate->passed()) {
                try {

                    $cardiac = $override->get3('cardiac', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    if ($cardiac) {
                        $user->updateRecord('cardiac', array(
                            'diagnosis_date' => Input::get('diagnosis_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'cardiomyopathy' => Input::get('cardiomyopathy'),
                            'sub_cardiomyopathy' => Input::get('sub_cardiomyopathy'),
                            'cardiomyopathy_other' => Input::get('cardiomyopathy_other'),
                            'heumatic' => Input::get('heumatic'),
                            'sub_heumatic' => Input::get('sub_heumatic'),
                            'heumatic_other' => Input::get('heumatic_other'),
                            'congenital' => Input::get('congenital'),
                            'congenital_other' => Input::get('congenital_other'),
                            'sub_congenital' => Input::get('sub_congenital'),
                            'severe_hypertension' => Input::get('severe_hypertension'),
                            'coronary_artery' => Input::get('coronary_artery'),
                            'hypertensive_heart' => Input::get('hypertensive_heart'),
                            'heart_failure' => Input::get('heart_failure'),
                            'sub_heart_failure' => Input::get('sub_heart_failure'),
                            'heart_failure_other' => Input::get('heart_failure_other'),
                            'pericardial' => Input::get('pericardial'),
                            'sub_pericardial' => Input::get('sub_pericardial'),
                            'pericardial_other' => Input::get('pericardial_other'),
                            'arrhythmia' => Input::get('arrhythmia'),
                            'sub_arrhythmia' => Input::get('sub_arrhythmia'),
                            'arrhythmia_other' => Input::get('arrhythmia_other'),
                            'stroke' => Input::get('stroke'),
                            'sub_stroke' => Input::get('sub_stroke'),
                            'thromboembolic' => Input::get('thromboembolic'),
                            'sub_thromboembolic' => Input::get('sub_thromboembolic'),
                            'thromboembolic_other' => Input::get('thromboembolic_other'),
                            'comments' => Input::get('comments'),
                            'diagnosis_other' => Input::get('diagnosis_other'),
                            'diagnosis_specify' => Input::get('diagnosis_specify'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $cardiac['id']);
                    } else {
                        $user->createRecord('cardiac', array(
                            'visit_date' => Input::get('diagnosis_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'diagnosis_date' => Input::get('diagnosis_date'),
                            'cardiomyopathy' => Input::get('cardiomyopathy'),
                            'sub_cardiomyopathy' => Input::get('sub_cardiomyopathy'),
                            'cardiomyopathy_other' => Input::get('cardiomyopathy_other'),
                            'heumatic' => Input::get('heumatic'),
                            'sub_heumatic' => Input::get('sub_heumatic'),
                            'heumatic_other' => Input::get('heumatic_other'),
                            'congenital' => Input::get('congenital'),
                            'congenital_other' => Input::get('congenital_other'),
                            'sub_congenital' => Input::get('sub_congenital'),
                            'severe_hypertension' => Input::get('severe_hypertension'),
                            'coronary_artery' => Input::get('coronary_artery'),
                            'hypertensive_heart' => Input::get('hypertensive_heart'),
                            'heart_failure' => Input::get('heart_failure'),
                            'sub_heart_failure' => Input::get('sub_heart_failure'),
                            'heart_failure_other' => Input::get('heart_failure_other'),
                            'pericardial' => Input::get('pericardial'),
                            'sub_pericardial' => Input::get('sub_pericardial'),
                            'pericardial_other' => Input::get('pericardial_other'),
                            'arrhythmia' => Input::get('arrhythmia'),
                            'sub_arrhythmia' => Input::get('sub_arrhythmia'),
                            'arrhythmia_other' => Input::get('arrhythmia_other'),
                            'stroke' => Input::get('stroke'),
                            'sub_stroke' => Input::get('sub_stroke'),
                            'thromboembolic' => Input::get('thromboembolic'),
                            'sub_thromboembolic' => Input::get('sub_thromboembolic'),
                            'thromboembolic_other' => Input::get('thromboembolic_other'),
                            'comments' => Input::get('comments'),
                            'diagnosis_other' => Input::get('diagnosis_other'),
                            'diagnosis_specify' => Input::get('diagnosis_specify'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }

                    $successMessage = 'Cardiac added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_diabetic')) {
            $validate = $validate->check($_POST, array(
                'diagnosis_date' => array(
                    'required' => true,
                ),


            ));
            if ($validate->passed()) {
                try {

                    $diabetic = $override->get3('diabetic', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    if ($diabetic) {
                        $user->updateRecord('diabetic', array(
                            'visit_date' => Input::get('diagnosis_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'diagnosis' => Input::get('diagnosis'),
                            'diagnosis_other' => Input::get('diagnosis_other'),
                            'hypertension' => Input::get('hypertension'),
                            'hypertension_date' => Input::get('hypertension_date'),
                            'symptoms' => Input::get('symptoms'),
                            'cardiovascular' => Input::get('cardiovascular'),
                            'retinopathy' => Input::get('retinopathy'),
                            'renal_disease' => Input::get('renal_disease'),
                            'stroke' => Input::get('stroke'),
                            'pvd' => Input::get('pvd'),
                            'neuropathy' => Input::get('neuropathy'),
                            'sexual_dysfunction' => Input::get('sexual_dysfunction'),
                            'comments' => Input::get('comments'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $diabetic['id']);
                    } else {
                        $user->createRecord('diabetic', array(
                            'visit_date' => Input::get('diagnosis_date'),
                            'diagnosis' => Input::get('diagnosis'),
                            'diagnosis_other' => Input::get('diagnosis_other'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'hypertension' => Input::get('hypertension'),
                            'hypertension_date' => Input::get('hypertension_date'),
                            'symptoms' => Input::get('symptoms'),
                            'cardiovascular' => Input::get('cardiovascular'),
                            'retinopathy' => Input::get('retinopathy'),
                            'renal_disease' => Input::get('renal_disease'),
                            'stroke' => Input::get('stroke'),
                            'pvd' => Input::get('pvd'),
                            'neuropathy' => Input::get('neuropathy'),
                            'sexual_dysfunction' => Input::get('sexual_dysfunction'),
                            'comments' => Input::get('comments'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Diabetic added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_scd')) {
            $validate = $validate->check($_POST, array(
                'diagnosis' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {

                    $sickle_cell = $override->get3('sickle_cell', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    if ($sickle_cell) {
                        $user->updateRecord('sickle_cell', array(
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'diagnosis' => Input::get('diagnosis'),
                            'diagnosis_other' => Input::get('diagnosis_other'),
                            'visit_date' => Input::get('diagnosis_date'),
                            'comments' => Input::get('comments'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $sickle_cell['id']);
                    } else {
                        $user->createRecord('sickle_cell', array(
                            'diagnosis' => Input::get('diagnosis'),
                            'diagnosis_other' => Input::get('diagnosis_other'),
                            'visit_date' => Input::get('visit_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'comments' => Input::get('comments'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Sickle Cell added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_vital')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {

                    $vital = $override->get3('vital', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    if ($vital) {
                        $user->updateRecord('vital', array(
                            'visit_date' => Input::get('visit_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'height' => Input::get('height'),
                            'weight' => Input::get('weight'),
                            'bmi' => Input::get('bmi'),
                            'muac' => Input::get('muac'),
                            'systolic' => Input::get('systolic'),
                            'dystolic' => Input::get('dystolic'),
                            'pr' => Input::get('pr'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $vital['id']);
                    } else {
                        $user->createRecord('vital', array(
                            'visit_date' => Input::get('visit_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'height' => Input::get('height'),
                            'weight' => Input::get('weight'),
                            'bmi' => Input::get('bmi'),
                            'muac' => Input::get('muac'),
                            'systolic' => Input::get('systolic'),
                            'dystolic' => Input::get('dystolic'),
                            'pr' => Input::get('pr'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Vital added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_history')) {
            $validate = $validate->check($_POST, array(
                // 'visit_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $history = $override->get3('history', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    if ($history) {
                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) {

                            $user->updateRecord('history', array(
                                'visit_date' => date('Y-m-d'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'hypertension' => Input::get('hypertension'),
                                'diabetes' => Input::get('diabetes'),
                                'ckd' => Input::get('ckd'),
                                'depression' => Input::get('depression'),
                                'hiv' => Input::get('hiv'),
                                'hiv_test' => Input::get('hiv_test'),
                                'art' => Input::get('art'),
                                'art_date' => Input::get('art_date'),
                                'tb' => Input::get('tb'),
                                'tb_year' => Input::get('tb_year'),
                                'smoking' => Input::get('smoking'),
                                'packs' => Input::get('packs'),
                                'active_smoker' => Input::get('active_smoker'),
                                'alcohol' => Input::get('alcohol'),
                                'alcohol_type' => Input::get('alcohol_type'),
                                'alcohol_other' => Input::get('alcohol_other'),
                                'quantity' => Input::get('quantity'),
                                'cardiac_disease' => Input::get('cardiac_disease'),
                                'cardiac_surgery' => Input::get('cardiac_surgery'),
                                'cardiac_surgery_type' => Input::get('cardiac_surgery_type'),
                                'surgery_other' => Input::get('surgery_other'),
                                'scd_disease' => Input::get('scd_disease'),
                                'history_other' => Input::get('history_other'),
                                'history_specify' => Input::get('history_specify'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $history['id']);
                        }
                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) {

                            $user->updateRecord('history', array(
                                'visit_date' => date('Y-m-d'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'hiv' => Input::get('hiv'),
                                'hiv_test' => Input::get('hiv_test'),
                                'art' => Input::get('art'),
                                'art_date' => Input::get('art_date'),
                                'tb' => Input::get('tb'),
                                'tb_year' => Input::get('tb_year'),
                                'smoking' => Input::get('smoking'),
                                'packs' => Input::get('packs'),
                                'active_smoker' => Input::get('active_smoker'),
                                'alcohol' => Input::get('alcohol'),
                                'quantity' => Input::get('quantity'),
                                'cardiovascular' => Input::get('cardiovascular'),
                                'cardiovascular_date' => Input::get('cardiovascular_date'),
                                'retinopathy' => Input::get('retinopathy'),
                                'retinopathy_date' => Input::get('retinopathy_date'),
                                'renal' => Input::get('renal'),
                                'renal_date' => Input::get('renal_date'),
                                'stroke_tia' => Input::get('stroke_tia'),
                                'stroke_tia_date' => Input::get('stroke_tia_date'),
                                'pvd' => Input::get('pvd'),
                                'pvd_date' => Input::get('pvd_date'),
                                'neuropathy' => Input::get('neuropathy'),
                                'neuropathy_date' => Input::get('neuropathy_date'),
                                'sexual_dysfunction' => Input::get('sexual_dysfunction'),
                                'sexual_dysfunction_date' => Input::get('sexual_dysfunction_date'),
                                'diabetic_disease' => Input::get('diabetic_disease'),
                                'hypertension_disease' => Input::get('hypertension_disease'),
                                'history_other' => Input::get('history_other'),
                                'history_specify' => Input::get('history_specify'),
                                'scd_disease' => Input::get('scd_disease'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $history['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) {

                            $user->updateRecord('history', array(
                                'visit_date' => date('Y-m-d'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'hiv' => Input::get('hiv'),
                                'hiv_test' => Input::get('hiv_test'),
                                'art' => Input::get('art'),
                                'art_date' => Input::get('art_date'),
                                'tb' => Input::get('tb'),
                                'tb_year' => Input::get('tb_year'),
                                'smoking' => Input::get('smoking'),
                                'packs' => Input::get('packs'),
                                'active_smoker' => Input::get('active_smoker'),
                                'alcohol' => Input::get('alcohol'),
                                'quantity' => Input::get('quantity'),
                                'pain_event' => Input::get('pain_event'),
                                'stroke' => Input::get('stroke'),
                                'pneumonia' => Input::get('pneumonia'),
                                'blood_transfusion' => Input::get('blood_transfusion'),
                                'transfusion_born' => Input::get('transfusion_born'),
                                'transfusion_12months' => Input::get('transfusion_12months'),
                                'acute_chest' => Input::get('acute_chest'),
                                'other_complication' => Input::get('other_complication'),
                                'specify_complication' => Input::get('specify_complication'),
                                'scd_disease' => Input::get('scd_disease'),
                                'history_scd' => Input::get('history_scd'),
                                'hepatitis_test' => Input::get('hepatitis_test'),
                                'hepatitis_date' => Input::get('hepatitis_date'),
                                'hepatitis_results' => Input::get('hepatitis_results'),
                                'vaccine_history' => Input::get('vaccine_history'),
                                'blood_group' => Input::get('blood_group'),
                                'siblings' => Input::get('siblings'),
                                'sibling_salive' => Input::get('sibling_salive'),
                                'history_other' => Input::get('history_other'),
                                'history_specify' => Input::get('history_specify'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $history['id']);
                        }
                    } else {
                        $user->createRecord('history', array(
                            'visit_date' => date('Y-m-d'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'hypertension' => Input::get('hypertension'),
                            'diabetes' => Input::get('diabetes'),
                            'ckd' => Input::get('ckd'),
                            'depression' => Input::get('depression'),
                            'hiv' => Input::get('hiv'),
                            'hiv_test' => Input::get('hiv_test'),
                            'art' => Input::get('art'),
                            'art_date' => Input::get('art_date'),
                            'tb' => Input::get('tb'),
                            'tb_year' => Input::get('tb_year'),
                            'smoking' => Input::get('smoking'),
                            'packs' => Input::get('packs'),
                            'active_smoker' => Input::get('active_smoker'),
                            'alcohol' => Input::get('alcohol'),
                            'alcohol_type' => Input::get('alcohol_type'),
                            'alcohol_other' => Input::get('alcohol_other'),
                            'quantity' => Input::get('quantity'),
                            'cardiovascular' => Input::get('cardiovascular'),
                            'cardiovascular_date' => Input::get('cardiovascular_date'),
                            'retinopathy' => Input::get('retinopathy'),
                            'retinopathy_date' => Input::get('retinopathy_date'),
                            'renal' => Input::get('renal'),
                            'renal_date' => Input::get('renal_date'),
                            'stroke_tia' => Input::get('stroke_tia'),
                            'stroke_tia_date' => Input::get('stroke_tia_date'),
                            'pvd' => Input::get('pvd'),
                            'pvd_date' => Input::get('pvd_date'),
                            'neuropathy' => Input::get('neuropathy'),
                            'neuropathy_date' => Input::get('neuropathy_date'),
                            'sexual_dysfunction' => Input::get('sexual_dysfunction'),
                            'sexual_dysfunction_date' => Input::get('sexual_dysfunction_date'),
                            'pain_event' => Input::get('pain_event'),
                            'stroke' => Input::get('stroke'),
                            'pneumonia' => Input::get('pneumonia'),
                            'blood_transfusion' => Input::get('blood_transfusion'),
                            'transfusion_born' => Input::get('transfusion_born'),
                            'transfusion_12months' => Input::get('transfusion_12months'),
                            'acute_chest' => Input::get('acute_chest'),
                            'other_complication' => Input::get('other_complication'),
                            'specify_complication' => Input::get('specify_complication'),
                            'cardiac_disease' => Input::get('cardiac_disease'),
                            'cardiac_surgery' => Input::get('cardiac_surgery'),
                            'cardiac_surgery_type' => Input::get('cardiac_surgery_type'),
                            'surgery_other' => Input::get('surgery_other'),
                            'diabetic_disease' => Input::get('diabetic_disease'),
                            'hypertension_disease' => Input::get('hypertension_disease'),
                            'history_other' => Input::get('history_other'),
                            'history_specify' => Input::get('history_specify'),
                            'scd_disease' => Input::get('scd_disease'),
                            'history_scd' => Input::get('history_scd'),
                            'hepatitis_test' => Input::get('hepatitis_test'),
                            'hepatitis_date' => Input::get('hepatitis_date'),
                            'hepatitis_results' => Input::get('hepatitis_results'),
                            'vaccine_history' => Input::get('vaccine_history'),
                            'blood_group' => Input::get('blood_group'),
                            'siblings' => Input::get('siblings'),
                            'sibling_salive' => Input::get('sibling_salive'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }

                    $i = 0;
                    foreach (Input::get('age') as $searchValue) {
                        $sickle_cell_status_id = $override->get3('sickle_cell_status_table', 'age', $searchValue, 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq']);
                        if ($sickle_cell_status_id) {
                            $user->updateRecord('sickle_cell_status_table', array(
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'age' => $searchValue,
                                'sex' => Input::get('sex')[$i],
                                'sickle_status' => Input::get('sickle_status')[$i],
                            ), $sickle_cell_status_id[0]['id']);
                        } else {
                            $user->createRecord('sickle_cell_status_table', array(
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'age' => $searchValue,
                                'sex' => Input::get('sex')[$i],
                                'sickle_status' => Input::get('sickle_status')[$i],
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'created_on' => date('Y-m-d'),
                                'site_id' => $user->data()->site_id,
                            ));
                        }
                        $i++;
                    }
                    $successMessage = 'History added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_symptoms')) {
            $validate = $validate->check($_POST, array(
                // 'visit_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $symptoms = $override->get3('symptoms', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    if ($symptoms) {
                        $user->updateRecord('symptoms', array(
                            'visit_date' => Input::get('visit_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $symptoms['id']);

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1) || $override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) {

                            $user->updateRecord('symptoms', array(
                                'chest_pain' => Input::get('chest_pain'),
                                'score_chest_pain' => Input::get('score_chest_pain'),
                            ), $symptoms['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1) || $override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) {

                            $user->updateRecord('symptoms', array(
                                'abnorminal_pain' => Input::get('abnorminal_pain'),
                                'score_abnorminal_pain' => Input::get('score_abnorminal_pain'),
                            ), $symptoms['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) {
                            $user->updateRecord('symptoms', array(
                                'dyspnea' => Input::get('dyspnea'),
                                'orthopnea' => Input::get('orthopnea'),
                                'paroxysmal' => Input::get('paroxysmal'),
                                'cough' => Input::get('cough'),
                                'edema' => Input::get('edema'),
                                'lungs' => Input::get('lungs'),
                                'lungs_other' => Input::get('lungs_other'),
                                'jvp' => Input::get('jvp'),
                                'volume' => Input::get('volume'),
                                'murmur' => Input::get('murmur'),
                            ), $symptoms['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) {
                            $user->updateRecord('symptoms', array(
                                'thirst' => Input::get('thirst'),
                                'urination' => Input::get('urination'),
                                'vision' => Input::get('vision'),
                                'vomiting' => Input::get('vomiting'),
                                'weight_loss' => Input::get('weight_loss'),
                                'foot_exam' => Input::get('foot_exam'),
                                'foot_exam_finding' => Input::get('foot_exam_finding'),
                                'foot_exam_other' => Input::get('foot_exam_other'),
                                'fasting' => Input::get('fasting'),
                                'random_fs' => Input::get('random_fs'),
                                'hba1c' => Input::get('hba1c'),
                                'hypoglycemia_symptoms' => Input::get('hypoglycemia_symptoms'),
                                'hypoglycemia_severe' => Input::get('hypoglycemia_severe'),
                                'hypoglycemia__number' => Input::get('hypoglycemia__number'),
                            ), $symptoms['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) {
                            $user->updateRecord('symptoms', array(
                                'breathing' => Input::get('breathing'),
                                'other_sickle' => Input::get('other_sickle'),
                                'sickle_specify' => Input::get('sickle_specify'),
                                'malnutrition' => Input::get('malnutrition'),
                                'pallor' => Input::get('pallor'),
                                'jaundice' => Input::get('jaundice'),
                                'splenomegaly' => Input::get('splenomegaly'),
                                'anemia' => Input::get('anemia'),
                                'hb' => Input::get('hb'),
                                'wbc' => Input::get('wbc'),
                                'plt' => Input::get('plt'),
                                'labs_other' => Input::get('labs_other'),
                                'headache' => Input::get('headache'),
                                'score_headache' => Input::get('score_headache'),
                                'upper_arms' => Input::get('upper_arms'),
                                'score_upper_arms' => Input::get('score_upper_arms'),
                                'lower_arms' => Input::get('lower_arms'),
                                'score_lower_arms' => Input::get('score_lower_arms'),
                                'waist' => Input::get('waist'),
                                'score_waist' => Input::get('score_waist'),
                                'joints' => Input::get('joints'),
                                'spescify_joints' => Input::get('spescify_joints'),
                                'score_joints' => Input::get('score_joints'),
                                'other_pain' => Input::get('other_pain'),
                                'spescify_other_pain' => Input::get('spescify_other_pain'),
                                'score_other_pain' => Input::get('score_other_pain'),
                            ), $symptoms['id']);
                        }
                    } else {
                        $user->createRecord('symptoms', array(
                            'visit_date' => Input::get('visit_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'dyspnea' => Input::get('dyspnea'),
                            'orthopnea' => Input::get('orthopnea'),
                            'paroxysmal' => Input::get('paroxysmal'),
                            'headache' => Input::get('headache'),
                            'score_headache' => Input::get('score_headache'),
                            'chest_pain' => Input::get('chest_pain'),
                            'score_chest_pain' => Input::get('score_chest_pain'),
                            'abnorminal_pain' => Input::get('abnorminal_pain'),
                            'score_abnorminal_pain' => Input::get('score_abnorminal_pain'),
                            'upper_arms' => Input::get('upper_arms'),
                            'score_upper_arms' => Input::get('score_upper_arms'),
                            'lower_arms' => Input::get('lower_arms'),
                            'score_lower_arms' => Input::get('score_lower_arms'),
                            'waist' => Input::get('waist'),
                            'score_waist' => Input::get('score_waist'),
                            'joints' => Input::get('joints'),
                            'spescify_joints' => Input::get('spescify_joints'),
                            'score_joints' => Input::get('score_joints'),
                            'other_pain' => Input::get('other_pain'),
                            'spescify_other_pain' => Input::get('spescify_other_pain'),
                            'score_other_pain' => Input::get('score_other_pain'),
                            'cough' => Input::get('cough'),
                            'thirst' => Input::get('thirst'),
                            'urination' => Input::get('urination'),
                            'vision' => Input::get('vision'),
                            'vomiting' => Input::get('vomiting'),
                            'weight_loss' => Input::get('weight_loss'),
                            'breathing' => Input::get('breathing'),
                            'other_sickle' => Input::get('other_sickle'),
                            'sickle_specify' => Input::get('sickle_specify'),
                            'edema' => Input::get('edema'),
                            'lungs' => Input::get('lungs'),
                            'lungs_other' => Input::get('lungs_other'),
                            'jvp' => Input::get('jvp'),
                            'volume' => Input::get('volume'),
                            'murmur' => Input::get('murmur'),
                            'foot_exam' => Input::get('foot_exam'),
                            'foot_exam_finding' => Input::get('foot_exam_finding'),
                            'foot_exam_other' => Input::get('foot_exam_other'),
                            'malnutrition' => Input::get('malnutrition'),
                            'pallor' => Input::get('pallor'),
                            'jaundice' => Input::get('jaundice'),
                            'splenomegaly' => Input::get('splenomegaly'),
                            'anemia' => Input::get('anemia'),
                            'fasting' => Input::get('fasting'),
                            'random_fs' => Input::get('random_fs'),
                            'hba1c' => Input::get('hba1c'),
                            'hypoglycemia_symptoms' => Input::get('hypoglycemia_symptoms'),
                            'hypoglycemia_severe' => Input::get('hypoglycemia_severe'),
                            'hypoglycemia__number' => Input::get('hypoglycemia__number'),
                            'hb' => Input::get('hb'),
                            'wbc' => Input::get('wbc'),
                            'plt' => Input::get('plt'),
                            'labs_other' => Input::get('labs_other'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Symptoms added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_diagnosis')) {
            $validate = $validate->check($_POST, array(
                'diagnosis_date' => array(
                    'required' => true,
                ),


            ));
            if ($validate->passed()) {
                try {
                    $diagnosis = $override->get3('diagnosis', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];


                    // Counting number of checked checkboxes.
                    $checked_count = count(Input::get('cardiac'));

                    $i = 0;
                    foreach (Input::get('cardiac') as $selected) {

                        if ($diagnosis) {
                            $user->updateRecord('diagnosis', array(
                                'visit_date' => Input::get('diagnosis_date'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'diagnosis_date' => Input::get('diagnosis_date'),
                                'cardiac' => Input::get('cardiac')[$i],
                                'diagnosis_date' => Input::get('diagnosis_date'),
                                'cardiomyopathy' => Input::get('cardiomyopathy'),
                                'heumatic' => Input::get('heumatic'),
                                'congenital' => Input::get('congenital'),
                                'heart_failure' => Input::get('heart_failure'),
                                'pericardial' => Input::get('pericardial'),
                                'arrhythmia' => Input::get('arrhythmia'),
                                'stroke' => Input::get('stroke'),
                                'thromboembolic' => Input::get('thromboembolic'),
                                'diagnosis_other' => Input::get('diagnosis_other'),
                                'comments' => Input::get('comments'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $diagnosis['id']);
                        } else {
                            $user->createRecord('diagnosis', array(
                                'visit_date' => Input::get('diagnosis_date'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'cardiac' => Input::get('cardiac')[$i],
                                'diagnosis_date' => Input::get('diagnosis_date'),
                                'cardiomyopathy' => Input::get('cardiomyopathy'),
                                'heumatic' => Input::get('heumatic'),
                                'congenital' => Input::get('congenital'),
                                'heart_failure' => Input::get('heart_failure'),
                                'pericardial' => Input::get('pericardial'),
                                'arrhythmia' => Input::get('arrhythmia'),
                                'stroke' => Input::get('stroke'),
                                'thromboembolic' => Input::get('thromboembolic'),
                                'diagnosis_other' => Input::get('diagnosis_other'),
                                'comments' => Input::get('comments'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'created_on' => date('Y-m-d'),
                                'site_id' => $user->data()->site_id,
                            ));
                        }
                    }
                    $successMessage = 'Diagnosis added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_results')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $results = $override->get3('results', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];
                    if ($results) {
                        $user->updateRecord('results', array(
                            'visit_date' => Input::get('visit_date'),
                            'ecg_performed' => Input::get('ecg_performed'),
                            'echo_performed' => Input::get('echo_performed'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $results['id']);

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) {
                            $user->updateRecord('results', array(
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'ecg_date' => Input::get('ecg_date'),
                                'ecg' => Input::get('ecg'),
                                'ecg_other' => Input::get('ecg_other'),
                                'echo_date' => Input::get('echo_date'),
                                'echo' => Input::get('echo'),
                                'echo_other' => Input::get('echo_other'),
                                'echo_specify' => Input::get('echo_specify'),
                                'echo_other2' => Input::get('echo_other2'),
                                'lv' => Input::get('lv'),
                                'mitral' => Input::get('mitral'),
                                'rv' => Input::get('rv'),
                                'pericardial' => Input::get('pericardial'),
                                'ivc' => Input::get('ivc'),
                                'congenital_defect' => Input::get('congenital_defect'),
                                'thrombus' => Input::get('thrombus'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $results['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) {
                            $user->updateRecord('results', array(
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'ecg_date' => Input::get('ecg_date'),
                                'ecg' => Input::get('ecg'),
                                'ecg_other' => Input::get('ecg_other'),
                                'echo_date' => Input::get('echo_date'),
                                'echo' => Input::get('echo'),
                                'echo_other' => Input::get('echo_other'),
                                'echo_specify' => Input::get('echo_specify'),
                                'echo_other2' => Input::get('echo_other2'),
                                'lv' => Input::get('lv'),
                                'mitral' => Input::get('mitral'),
                                'rv' => Input::get('rv'),
                                'pericardial' => Input::get('pericardial'),
                                'ivc' => Input::get('ivc'),
                                'scd_test' => Input::get('scd_test'),
                                'scd_test_other' => Input::get('scd_test_other'),
                                'confirmatory_test' => Input::get('confirmatory_test'),
                                'confirmatory_test_type' => Input::get('confirmatory_test_type'),
                                'scd_done' => Input::get('scd_done'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $results['id']);
                        }
                    } else {
                        $user->createRecord('results', array(
                            'visit_date' => Input::get('visit_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'ecg_performed' => Input::get('ecg_performed'),
                            'ecg_date' => Input::get('ecg_date'),
                            'ecg' => Input::get('ecg'),
                            'ecg_other' => Input::get('ecg_other'),
                            'echo_performed' => Input::get('echo_performed'),
                            'echo_date' => Input::get('echo_date'),
                            'echo' => Input::get('echo'),
                            'lv' => Input::get('lv'),
                            'mitral' => Input::get('mitral'),
                            'rv' => Input::get('rv'),
                            'pericardial' => Input::get('pericardial'),
                            'ivc' => Input::get('ivc'),
                            'congenital_defect' => Input::get('congenital_defect'),
                            'thrombus' => Input::get('thrombus'),
                            'echo_other' => Input::get('echo_other'),
                            'echo_specify' => Input::get('echo_specify'),
                            'echo_other2' => Input::get('echo_other2'),
                            'scd_test' => Input::get('scd_test'),
                            'scd_done' => Input::get('scd_done'),
                            'scd_test_other' => Input::get('scd_test_other'),
                            'confirmatory_test' => Input::get('confirmatory_test'),
                            'confirmatory_test_type' => Input::get('confirmatory_test_type'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Results added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_hospitalizaion')) {
            $validate = $validate->check($_POST, array(
                'hospitalizations' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {

                    $hospitalization = $override->get3('hospitalization', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    if ($hospitalization) {
                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) {
                            $user->updateRecord('hospitalization', array(
                                'visit_date' => Input::get('hospitalization_date'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'hospitalization_date' => Input::get('hospitalization_date'),
                                'hospitalizations' => Input::get('hospitalizations'),
                                'ncd_hospitalizations' => Input::get('ncd_hospitalizations'),
                                'hospitalization_number' => Input::get('hospitalization_number'),
                                'missed_days' => Input::get('missed_days'),
                                'school_days' => Input::get('school_days'),
                                'fluid' => Input::get('fluid'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $hospitalization['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) {
                            $user->updateRecord('hospitalization', array(
                                'visit_date' => Input::get('hospitalization_date'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'hospitalization_date' => Input::get('hospitalization_date'),
                                'hospitalizations' => Input::get('hospitalizations'),
                                'ncd_hospitalizations' => Input::get('ncd_hospitalizations'),
                                'hospitalization_number' => Input::get('hospitalization_number'),
                                'missed_days' => Input::get('missed_days'),
                                'school_days' => Input::get('school_days'),
                                'fluid' => Input::get('fluid'),
                                'bg_measurement' => Input::get('bg_measurement'),
                                'bg_result180' => Input::get('bg_result180'),
                                'bg_result70_180' => Input::get('bg_result70_180'),
                                'bg_result70' => Input::get('bg_result70'),
                                'basal' => Input::get('basal'),
                                'prandial' => Input::get('prandial'),
                                'basal_insulin' => Input::get('basal_insulin'),
                                'basal_am' => Input::get('basal_am'),
                                'basal_pm' => Input::get('basal_pm'),
                                'prandial_insulin' => Input::get('prandial_insulin'),
                                'prandial_am' => Input::get('prandial_am'),
                                'prandial_lunch' => Input::get('prandial_lunch'),
                                'prandial_pm' => Input::get('prandial_pm'),
                                'total_insulin_dose' => Input::get('total_insulin_dose'),
                                'home_insulin_dose' => Input::get('home_insulin_dose'),
                                'issue_injection' => Input::get('issue_injection'),
                                'issue_injection_yes' => Input::get('issue_injection_yes'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $hospitalization['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) {
                            $user->updateRecord('hospitalization', array(
                                'visit_date' => Input::get('hospitalization_date'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'hospitalization_date' => Input::get('hospitalization_date'),
                                'hospitalizations' => Input::get('hospitalizations'),
                                'ncd_hospitalizations' => Input::get('ncd_hospitalizations'),
                                'hospitalization_number' => Input::get('hospitalization_number'),
                                'missed_days' => Input::get('missed_days'),
                                'school_days' => Input::get('school_days'),
                                'transfusion' => Input::get('transfusion'),
                                'fluid' => Input::get('fluid'),
                                'prophylaxis' => Input::get('prophylaxis'),
                                'insecticide' => Input::get('insecticide'),
                                'folic_acid' => Input::get('folic_acid'),
                                'penicillin' => Input::get('penicillin'),
                                'pneumococcal' => Input::get('pneumococcal'),
                                'opioid' => Input::get('opioid'),
                                'opioid_type' => Input::get('opioid_type'),
                                'opioid_dose' => Input::get('opioid_dose'),
                                'hydroxyurea' => Input::get('hydroxyurea'),
                                'hydroxyurea_date' => Input::get('hydroxyurea_date'),
                                'hydroxyurea_dose' => Input::get('hydroxyurea_dose'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $hospitalization['id']);
                        }
                    } else {
                        $user->createRecord('hospitalization', array(
                            'visit_date' => Input::get('hospitalization_date'),
                            'hospitalization_date' => Input::get('hospitalization_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'hospitalizations' => Input::get('hospitalizations'),
                            'ncd_hospitalizations' => Input::get('ncd_hospitalizations'),
                            'hospitalization_number' => Input::get('hospitalization_number'),
                            'missed_days' => Input::get('missed_days'),
                            'school_days' => Input::get('school_days'),
                            'transfusion' => Input::get('transfusion'),
                            'fluid' => Input::get('fluid'),
                            'bg_measurement' => Input::get('bg_measurement'),
                            'bg_result180' => Input::get('bg_result180'),
                            'bg_result70_180' => Input::get('bg_result70_180'),
                            'bg_result70' => Input::get('bg_result70'),
                            'basal' => Input::get('basal'),
                            'prandial' => Input::get('prandial'),
                            'prophylaxis' => Input::get('prophylaxis'),
                            'insecticide' => Input::get('insecticide'),
                            'folic_acid' => Input::get('folic_acid'),
                            'penicillin' => Input::get('penicillin'),
                            'pneumococcal' => Input::get('pneumococcal'),
                            'opioid' => Input::get('opioid'),
                            'opioid_type' => Input::get('opioid_type'),
                            'opioid_dose' => Input::get('opioid_dose'),
                            'hydroxyurea' => Input::get('hydroxyurea'),
                            'hydroxyurea_date' => Input::get('hydroxyurea_date'),
                            'hydroxyurea_dose' => Input::get('hydroxyurea_dose'),
                            'basal_insulin' => Input::get('basal_insulin'),
                            'basal_am' => Input::get('basal_am'),
                            'basal_pm' => Input::get('basal_pm'),
                            'prandial_insulin' => Input::get('prandial_insulin'),
                            'prandial_am' => Input::get('prandial_am'),
                            'prandial_lunch' => Input::get('prandial_lunch'),
                            'prandial_pm' => Input::get('prandial_pm'),
                            'total_insulin_dose' => Input::get('total_insulin_dose'),
                            'home_insulin_dose' => Input::get('home_insulin_dose'),
                            'issue_injection' => Input::get('issue_injection'),
                            'issue_injection_yes' => Input::get('issue_injection_yes'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Hospitalization added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_lab_details')) {
            $validate = $validate->check($_POST, array(
                'lab_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {

                    $lab_details = $override->get3('lab_details', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    if ($lab_details) {
                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) {

                            $user->updateRecord('lab_details', array(
                                'visit_date' => Input::get('lab_date'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'lab_date' => Input::get('lab_date'),
                                'ncd_coping' => Input::get('ncd_coping'),
                                'family_planning' => Input::get('family_planning'),
                                'chemistry_test' => Input::get('chemistry_test'),
                                'na' => Input::get('na'),
                                'k' => Input::get('k'),
                                'bun' => Input::get('bun'),
                                'cre' => Input::get('cre'),
                                'bnp' => Input::get('bnp'),
                                'inr' => Input::get('inr'),
                                'lab_Other' => Input::get('lab_Other'),
                                'lab_specify' => Input::get('lab_specify'),
                                'lab_ecg' => Input::get('lab_ecg'),
                                'lab_ecg_other' => Input::get('lab_ecg_other'),
                                'cardiac_surgery' => Input::get('cardiac_surgery'),
                                'cardiac_surgery_type' => Input::get('cardiac_surgery_type'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $lab_details['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) {

                            $user->updateRecord('lab_details', array(
                                'visit_date' => Input::get('lab_date'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'lab_date' => Input::get('lab_date'),
                                'ncd_coping' => Input::get('ncd_coping'),
                                'family_planning' => Input::get('family_planning'),
                                'dka_number' => Input::get('dka_number'),
                                'eyes_examined' => Input::get('eyes_examined'),
                                'cataracts' => Input::get('cataracts'),
                                'retinopathy_screening' => Input::get('retinopathy_screening'),
                                'foot_exam_diabetes' => Input::get('foot_exam_diabetes'),
                                'na_diabetes' => Input::get('na_diabetes'),
                                'k_diabetes' => Input::get('k_diabetes'),
                                'cre_diabetes' => Input::get('cre_diabetes'),
                                'chemistry_test2' => Input::get('chemistry_test2'),
                                'proteinuria' => Input::get('proteinuria'),
                                'lipid_panel' => Input::get('lipid_panel'),
                                'other_lab_diabetes' => Input::get('other_lab_diabetes'),
                                'specify_lab_diabetes' => Input::get('specify_lab_diabetes'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $lab_details['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) {

                            $user->updateRecord('lab_details', array(
                                'visit_date' => Input::get('lab_date'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'lab_date' => Input::get('lab_date'),
                                'ncd_coping' => Input::get('ncd_coping'),
                                'family_planning' => Input::get('family_planning'),
                                'lab_transfusion_sickle' => Input::get('lab_transfusion_sickle'),
                                'transcranial_doppler' => Input::get('transcranial_doppler'),
                                'wbc' => Input::get('wbc'),
                                'hb' => Input::get('hb'),
                                'mcv' => Input::get('mcv'),
                                'plt' => Input::get('plt'),
                                'fe_studies' => Input::get('fe_studies'),
                                'lfts' => Input::get('lfts'),
                                'hematology_test' => Input::get('hematology_test'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $lab_details['id']);
                        }
                    } else {
                        $user->createRecord('lab_details', array(
                            'visit_date' => Input::get('lab_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'lab_date' => Input::get('lab_date'),
                            'ncd_coping' => Input::get('ncd_coping'),
                            'family_planning' => Input::get('family_planning'),
                            'na' => Input::get('na'),
                            'k' => Input::get('k'),
                            'bun' => Input::get('bun'),
                            'cre' => Input::get('cre'),
                            'bnp' => Input::get('bnp'),
                            'inr' => Input::get('inr'),
                            'lab_Other' => Input::get('lab_Other'),
                            'lab_specify' => Input::get('lab_specify'),
                            'lab_ecg' => Input::get('lab_ecg'),
                            'lab_ecg_other' => Input::get('lab_ecg_other'),
                            'cardiac_surgery' => Input::get('cardiac_surgery'),
                            'cardiac_surgery_type' => Input::get('cardiac_surgery_type'),
                            'dka_number' => Input::get('dka_number'),
                            'eyes_examined' => Input::get('eyes_examined'),
                            'cataracts' => Input::get('cataracts'),
                            'retinopathy_screening' => Input::get('retinopathy_screening'),
                            'foot_exam_diabetes' => Input::get('foot_exam_diabetes'),
                            'chemistry_test' => Input::get('chemistry_test'),
                            'chemistry_test2' => Input::get('chemistry_test2'),
                            'na_diabetes' => Input::get('na_diabetes'),
                            'k_diabetes' => Input::get('k_diabetes'),
                            'cre_diabetes' => Input::get('cre_diabetes'),
                            'proteinuria' => Input::get('proteinuria'),
                            'lipid_panel' => Input::get('lipid_panel'),
                            'other_lab_diabetes' => Input::get('other_lab_diabetes'),
                            'specify_lab_diabetes' => Input::get('specify_lab_diabetes'),
                            'lab_transfusion_sickle' => Input::get('lab_transfusion_sickle'),
                            'transcranial_doppler' => Input::get('transcranial_doppler'),
                            'wbc' => Input::get('wbc'),
                            'hb' => Input::get('hb'),
                            'mcv' => Input::get('mcv'),
                            'plt' => Input::get('plt'),
                            'fe_studies' => Input::get('fe_studies'),
                            'lfts' => Input::get('lfts'),
                            'hematology_test' => Input::get('hematology_test'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Lab details added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_hospitalization_details')) {
            $validate = $validate->check($_POST, array(
                'hospitalization_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {

                    $hospitalization_details = $override->get3('hospitalization_details', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];
                    if ($hospitalization_details) {
                        $user->updateRecord('hospitalization_details', array(
                            'visit_date' => Input::get('hospitalization_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'hospitalization_date' => Input::get('hospitalization_date'),
                            'hospitalization_ncd' => Input::get('hospitalization_ncd'),
                            'hospitalization_year' => Input::get('hospitalization_year'),
                            'hospitalization_day' => Input::get('hospitalization_day'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $hospitalization_details['id']);
                    } else {
                        $user->createRecord('hospitalization_details', array(
                            'visit_date' => Input::get('hospitalization_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'hospitalization_date' => Input::get('hospitalization_date'),
                            'hospitalization_ncd' => Input::get('hospitalization_ncd'),
                            'hospitalization_year' => Input::get('hospitalization_year'),
                            'hospitalization_day' => Input::get('hospitalization_day'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }

                    // $multiArray = $override->get('hospitalization_table', 'patient_id', $_GET['cid']);
                    $i = 0;
                    foreach (Input::get('admission_date') as $searchValue) {
                        $hospitalization_id = $override->getNews('hospitalization_table', 'admission_date', $searchValue, 'patient_id', $_GET['cid']);
                        if ($hospitalization_id) {
                            // if ($user->isValueInMultiArrays($searchValue, $multiArray)) {

                            // echo "The value '{$searchValue}' exists in the multi-dimensional array.";
                            // $user->isValueInMultiArrays($searchValue, $multiArray);
                            // $id = $override->getNews('hospitalization_table', 'admission_date', $searchValue,'patient_id',$_GET['cid']);
                            $user->updateRecord('hospitalization_table', array(
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'admission_date' => $searchValue,
                                'admission_reason' => Input::get('admission_reason')[$i],
                                'discharge_diagnosis' => Input::get('discharge_diagnosis')[$i],
                            ), $hospitalization_id[0]['id']);
                        } else {
                            // echo "The value '{$searchValue}' does not exist in the multi-dimensional array.";
                            // $user->createRecord('card_test', array(
                            //     'cardiac' => $searchValue,
                            // ));vehicle11
                            $user->createRecord('hospitalization_table', array(
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'admission_date' => $searchValue,
                                'admission_reason' => Input::get('admission_reason')[$i],
                                'discharge_diagnosis' => Input::get('discharge_diagnosis')[$i],
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'created_on' => date('Y-m-d'),
                                'site_id' => $user->data()->site_id,
                            ));
                        }
                        $i++;
                    }
                    $successMessage = 'Hospitalization details added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_hospitalization_list')) {
            $validate = $validate->check($_POST, array(
                // 'hospitalization_date' => array(
                //     'required' => true,
                // ),

            ));
            if ($validate->passed()) {

                try {

                    $multiArray = $override->get('hospitalization_table', 'patient_id', $_GET['cid']);
                    $i = 0;

                    print_r($_POST);
                    foreach (Input::get('admission_date') as $searchValue) {
                        // if ($user->isValueInMultiArrays($searchValue, $multiArray)) {
                        //     // echo "The value '{$searchValue}' exists in the multi-dimensional array.";
                        //     // $user->isValueInMultiArrays($searchValue, $multiArray);
                        //     // $id = $override->get('card_test', 'cardiac', $searchValue);
                        //     // $user->updateRecord('card_test', array(
                        //     //     'cardiac' => $searchValue,
                        //     // ), $id['id']);
                        // } else {
                        //     // echo "The value '{$searchValue}' does not exist in the multi-dimensional array.";
                        //     // $user->createRecord('card_test', array(
                        //     //     'cardiac' => $searchValue,
                        //     // ));vehicle11

                        // print_r('Hi');
                        $user->createRecord('hospitalization_table', array(
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'admission_date' => $searchValue,
                            'admission_reason' => Input::get('admission_reason')[$i],
                            'discharge_diagnosis' => Input::get('discharge_diagnosis')[$i],
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                        // }
                        $i++;
                    }


                    $successMessage = 'Hospitalization details added Successful';
                    // Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_risks')) {
            $validate = $validate->check($_POST, array(
                'risk_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {

                    $risks = $override->get3('risks', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];
                    if ($risks) {
                        $user->updateRecord('risks', array(
                            'visit_date' => Input::get('risk_date'),
                            'risk_date' => Input::get('risk_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'risk_tobacco' => Input::get('risk_tobacco'),
                            'risk_alcohol' => Input::get('risk_alcohol'),
                            'risk_employment' => Input::get('risk_employment'),
                            'ncd_limiting' => Input::get('ncd_limiting'),
                            'social_economic' => Input::get('social_economic'),
                            'risk_hiv_date' => Input::get('risk_hiv_date'),
                            'risk_hiv' => Input::get('risk_hiv'),
                            'risk_art' => Input::get('risk_art'),
                            'risk_art_date' => Input::get('risk_art_date'),
                            'risk_tb_date' => Input::get('risk_tb_date'),
                            'risk_tb' => Input::get('risk_tb'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $risks['id']);
                    } else {
                        $user->createRecord('risks', array(
                            'visit_date' => Input::get('risk_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'risk_date' => Input::get('risk_date'),
                            'risk_tobacco' => Input::get('risk_tobacco'),
                            'risk_alcohol' => Input::get('risk_alcohol'),
                            'risk_employment' => Input::get('risk_employment'),
                            'ncd_limiting' => Input::get('ncd_limiting'),
                            'social_economic' => Input::get('social_economic'),
                            'risk_hiv_date' => Input::get('risk_hiv_date'),
                            'risk_hiv' => Input::get('risk_hiv'),
                            'risk_art' => Input::get('risk_art'),
                            'risk_art_date' => Input::get('risk_art_date'),
                            'risk_tb_date' => Input::get('risk_tb_date'),
                            'risk_tb' => Input::get('risk_tb'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Risks details added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_dgns_complctns_comorbdts')) {
            $validate = $validate->check($_POST, array(
                'diagns_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $dgns_complctns_comorbdts = $override->get3('dgns_complctns_comorbdts', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    if ($dgns_complctns_comorbdts) {
                        $user->updateRecord('dgns_complctns_comorbdts', array(
                            'visit_date' => Input::get('diagns_date'),
                            'diagns_date' => Input::get('diagns_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'diagns_changed' => Input::get('diagns_changed'),
                            'diagns_cardiac' => Input::get('diagns_cardiac'),
                            'diagns_specify' => Input::get('diagns_specify'),
                            'new_diagns' => Input::get('new_diagns'),
                            'new_diagns_specify' => Input::get('new_diagns_specify'),
                            'diagns_complication' => Input::get('diagns_complication'),
                            'cmplctn_other' => Input::get('cmplctn_other'),
                            'complication_specify' => Input::get('complication_specify'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $dgns_complctns_comorbdts['id']);

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1) || $override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) {

                            $user->updateRecord('dgns_complctns_comorbdts', array(
                                'cmplctn_pain_event' => Input::get('cmplctn_pain_event'),
                                'cmplctn_stroke' => Input::get('cmplctn_stroke'),
                            ), $dgns_complctns_comorbdts['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) {

                            $user->updateRecord('dgns_complctns_comorbdts', array(
                                'diagns_cardiac' => Input::get('diagns_cardiac'),
                                'cmplctn_ckd' => Input::get('cmplctn_ckd'),
                                'cmplctn_depression' => Input::get('cmplctn_depression'),
                            ), $dgns_complctns_comorbdts['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) {

                            $user->updateRecord('dgns_complctns_comorbdts', array(
                                'diagns_diabetes' => Input::get('diagns_diabetes'),
                                'cmplctn_cardiovascular' => Input::get('cmplctn_cardiovascular'),
                                'cmplctn_neuropathy' => Input::get('cmplctn_neuropathy'),
                                'cmplctn_dysfunction' => Input::get('cmplctn_dysfunction'),
                                'cmplctn_pvd' => Input::get('cmplctn_pvd'),
                                'cmplctn_retinopathy' => Input::get('cmplctn_retinopathy'),
                                'cmplctn_renal' => Input::get('cmplctn_renal'),
                            ), $dgns_complctns_comorbdts['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) {

                            $user->updateRecord('dgns_complctns_comorbdts', array(
                                'diagns_sickle' => Input::get('diagns_sickle'),
                                'cmplctn_pneumonia' => Input::get('cmplctn_pneumonia'),
                                'cmplctn_priapism' => Input::get('cmplctn_priapism'),
                                'cmplctn_avn' => Input::get('cmplctn_avn'),
                                'cmplctn_hypersplenism' => Input::get('cmplctn_hypersplenism'),
                                'cmplctn_ulcer' => Input::get('cmplctn_ulcer'),
                                'cmplctn_visual_loss' => Input::get('cmplctn_visual_loss'),
                                'cmplctn_transfusion' => Input::get('cmplctn_transfusion'),
                                'cmplctn_syndrome' => Input::get('cmplctn_syndrome'),
                            ), $dgns_complctns_comorbdts['id']);
                        }
                    } else {
                        $user->createRecord('dgns_complctns_comorbdts', array(
                            'visit_date' => Input::get('diagns_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'diagns_date' => Input::get('diagns_date'),
                            'diagns_changed' => Input::get('diagns_changed'),
                            'diagns_cardiac' => Input::get('diagns_cardiac'),
                            'diagns_diabetes' => Input::get('diagns_diabetes'),
                            'diagns_sickle' => Input::get('diagns_sickle'),
                            'diagns_specify' => Input::get('diagns_specify'),
                            'new_diagns' => Input::get('new_diagns'),
                            'new_diagns_specify' => Input::get('new_diagns_specify'),
                            'diagns_complication' => Input::get('diagns_complication'),
                            'cmplctn_ckd' => Input::get('cmplctn_ckd'),
                            'cmplctn_depression' => Input::get('cmplctn_depression'),
                            'cmplctn_cardiovascular' => Input::get('cmplctn_cardiovascular'),
                            'cmplctn_neuropathy' => Input::get('cmplctn_neuropathy'),
                            'cmplctn_dysfunction' => Input::get('cmplctn_dysfunction'),
                            'cmplctn_pvd' => Input::get('cmplctn_pvd'),
                            'cmplctn_retinopathy' => Input::get('cmplctn_retinopathy'),
                            'cmplctn_renal' => Input::get('cmplctn_renal'),
                            'cmplctn_pain_event' => Input::get('cmplctn_pain_event'),
                            'cmplctn_stroke' => Input::get('cmplctn_stroke'),
                            'cmplctn_pneumonia' => Input::get('cmplctn_pneumonia'),
                            'cmplctn_priapism' => Input::get('cmplctn_priapism'),
                            'cmplctn_avn' => Input::get('cmplctn_avn'),
                            'cmplctn_hypersplenism' => Input::get('cmplctn_hypersplenism'),
                            'cmplctn_ulcer' => Input::get('cmplctn_ulcer'),
                            'cmplctn_visual_loss' => Input::get('cmplctn_visual_loss'),
                            'cmplctn_transfusion' => Input::get('cmplctn_transfusion'),
                            'cmplctn_syndrome' => Input::get('cmplctn_syndrome'),
                            'cmplctn_other' => Input::get('cmplctn_other'),
                            'complication_specify' => Input::get('complication_specify'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Diagnosis details added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_summary')) {
            $validate = $validate->check($_POST, array(
                'summary_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {

                    $summary = $override->get3('summary', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];
                    if ($summary) {
                        $user->updateRecord('summary', array(
                            'visit_date' => Input::get('summary_date'),
                            'summary_date' => Input::get('summary_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'comments' => Input::get('comments'),
                            'diagnosis' => Input::get('diagnosis'),
                            'diagnosis_other' => Input::get('diagnosis_other'),
                            'outcome' => Input::get('outcome'),
                            'transfer_out' => Input::get('transfer_out'),
                            'cause_death' => Input::get('cause_death'),
                            'next_appointment_notes' => Input::get('next_appointment_notes'),
                            'next_appointment' => Input::get('next_appointment'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $summary['id']);
                    } else {
                        $user->createRecord('summary', array(
                            'visit_date' => Input::get('summary_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'summary_date' => Input::get('summary_date'),
                            'comments' => Input::get('comments'),
                            'diagnosis' => Input::get('diagnosis'),
                            'diagnosis_other' => Input::get('diagnosis_other'),
                            'outcome' => Input::get('outcome'),
                            'transfer_out' => Input::get('transfer_out'),
                            'cause_death' => Input::get('cause_death'),
                            'next_appointment_notes' => Input::get('next_appointment_notes'),
                            'next_appointment' => Input::get('next_appointment'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Visit Summary  details added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_treatment_plan')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {

                    $treatment_plan = $override->get3('treatment_plan', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                    if ($treatment_plan) {
                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) {
                            $user->updateRecord('treatment_plan', array(
                                'visit_date' => Input::get('visit_date'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'salt' => Input::get('salt'),
                                'fluid' => Input::get('fluid'),
                                'restriction_other' => Input::get('restriction_other'),
                                'restriction_specify' => Input::get('restriction_specify'),
                                'social_support' => Input::get('social_support'),
                                'social_support_type' => Input::get('social_support_type'),
                                'cardiology' => Input::get('cardiology'),
                                'completed' => Input::get('completed'),
                                'cardiology_reason' => Input::get('cardiology_reason'),
                                'cardiology_date' => Input::get('cardiology_date'),
                                'awaiting_surgery' => Input::get('awaiting_surgery'),
                                'new_referrals' => Input::get('new_referrals'),
                                'new_referrals_type' => Input::get('new_referrals_type'),
                                'medication_notes' => Input::get('medication_notes'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $treatment_plan['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) {

                            $user->updateRecord('treatment_plan', array(
                                'visit_date' => Input::get('visit_date'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'basal_changed' => Input::get('basal_changed'),
                                'basal_am2' => Input::get('basal_am2'),
                                'basal_pm2' => Input::get('basal_pm2'),
                                'prandial_changed' => Input::get('prandial_changed'),
                                'prandial_am2' => Input::get('prandial_am2'),
                                'prandial_lunch2' => Input::get('prandial_lunch2'),
                                'prandial_pm2' => Input::get('prandial_pm2'),
                                'salt' => Input::get('salt'),
                                'fluid' => Input::get('fluid'),
                                'restriction_other' => Input::get('restriction_other'),
                                'social_support' => Input::get('social_support'),
                                'social_support_type' => Input::get('social_support_type'),
                                'cardiology' => Input::get('cardiology'),
                                'completed' => Input::get('completed'),
                                'cardiology_reason' => Input::get('cardiology_reason'),
                                'cardiology_date' => Input::get('cardiology_date'),
                                'awaiting_surgery' => Input::get('awaiting_surgery'),
                                'new_referrals' => Input::get('new_referrals'),
                                'new_referrals_type' => Input::get('new_referrals_type'),
                                'medication_notes' => Input::get('medication_notes'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $treatment_plan['id']);
                        }

                        if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) {

                            $user->updateRecord('treatment_plan', array(
                                'visit_date' => Input::get('visit_date'),
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'salt' => Input::get('salt'),
                                'fluid' => Input::get('fluid'),
                                'restriction_other' => Input::get('restriction_other'),
                                'vaccination' => Input::get('vaccination'),
                                'vaccination_specify' => Input::get('vaccination_specify'),
                                'transfusion_needed' => Input::get('transfusion_needed'),
                                'transfusion_units' => Input::get('transfusion_units'),
                                'diet' => Input::get('diet'),
                                'hydration' => Input::get('hydration'),
                                'acute_symptoms' => Input::get('acute_symptoms'),
                                'fever' => Input::get('fever'),
                                'other_support' => Input::get('other_support'),
                                'support_specify' => Input::get('support_specify'),
                                'social_support' => Input::get('social_support'),
                                'social_support_type' => Input::get('social_support_type'),
                                'cardiology' => Input::get('cardiology'),
                                'completed' => Input::get('completed'),
                                'cardiology_reason' => Input::get('cardiology_reason'),
                                'cardiology_date' => Input::get('cardiology_date'),
                                'awaiting_surgery' => Input::get('awaiting_surgery'),
                                'new_referrals' => Input::get('new_referrals'),
                                'new_referrals_type' => Input::get('new_referrals_type'),
                                'misconception' => Input::get('misconception'),
                                'life_style' => Input::get('life_style'),
                                'medication_notes' => Input::get('medication_notes'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'site_id' => $user->data()->site_id,
                            ), $treatment_plan['id']);
                        }
                    } else {
                        $user->createRecord('treatment_plan', array(
                            'visit_date' => Input::get('visit_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'basal_changed' => Input::get('basal_changed'),
                            'basal_am2' => Input::get('basal_am2'),
                            'basal_pm2' => Input::get('basal_pm2'),
                            'prandial_changed' => Input::get('prandial_changed'),
                            'prandial_am2' => Input::get('prandial_am2'),
                            'prandial_lunch2' => Input::get('prandial_lunch2'),
                            'prandial_pm2' => Input::get('prandial_pm2'),
                            'salt' => Input::get('salt'),
                            'fluid' => Input::get('fluid'),
                            'restriction_other' => Input::get('restriction_other'),
                            'restriction_specify' => Input::get('restriction_specify'),
                            'vaccination' => Input::get('vaccination'),
                            'vaccination_specify' => Input::get('vaccination_specify'),
                            'transfusion_needed' => Input::get('transfusion_needed'),
                            'transfusion_units' => Input::get('transfusion_units'),
                            'diet' => Input::get('diet'),
                            'hydration' => Input::get('hydration'),
                            'acute_symptoms' => Input::get('acute_symptoms'),
                            'fever' => Input::get('fever'),
                            'other_support' => Input::get('other_support'),
                            'support_specify' => Input::get('support_specify'),
                            'social_support' => Input::get('social_support'),
                            'social_support_type' => Input::get('social_support_type'),
                            'cardiology' => Input::get('cardiology'),
                            'completed' => Input::get('completed'),
                            'cardiology_reason' => Input::get('cardiology_reason'),
                            'cardiology_date' => Input::get('cardiology_date'),
                            'awaiting_surgery' => Input::get('awaiting_surgery'),
                            'new_referrals' => Input::get('new_referrals'),
                            'new_referrals_type' => Input::get('new_referrals_type'),
                            'misconception' => Input::get('misconception'),
                            'life_style' => Input::get('life_style'),
                            'medication_notes' => Input::get('medication_notes'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }

                    // $multiArray = $override->get('medication_treatments', 'patient_id', $_GET['cid']);

                    $i = 0;
                    foreach (Input::get('medication_type') as $searchValue) {
                        $medication_id = $override->getNews('medication_treatments', 'medication_type', $searchValue, 'patient_id', $_GET['cid']);
                        if ($medication_id) {
                            // if ($user->isValueInMultiArrays($searchValue, $multiArray)) {
                            // if ($user->isValueInMultiArrays($searchValue, $multiArray)) {
                            // echo "The value '{$searchValue}' exists in the multi-dimensional array.";
                            // $user->isValueInMultiArrays($searchValue, $multiArray);
                            // $id = $override->get('card_test', 'cardiac', $searchValue);
                            $user->updateRecord('medication_treatments', array(
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'medication_type' => $searchValue,
                                'medication_action' => Input::get('medication_action')[$i],
                                'medication_dose' => Input::get('medication_dose')[$i],
                            ), $medication_id[0]['id']);
                        } else {
                            // echo "The value '{$searchValue}' does not exist in the multi-dimensional array.";
                            // $user->createRecord('card_test', array(
                            //     'cardiac' => $searchValue,
                            // ));vehicle11
                            $user->createRecord('medication_treatments', array(
                                'study_id' => $_GET['sid'],
                                'visit_code' => $_GET['vcode'],
                                'visit_day' => $_GET['vday'],
                                'seq_no' => $_GET['seq'],
                                'vid' => $_GET['vid'],
                                'medication_type' => $searchValue,
                                'medication_action' => Input::get('medication_action')[$i],
                                'medication_dose' => Input::get('medication_dose')[$i],
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'created_on' => date('Y-m-d'),
                                'site_id' => $user->data()->site_id,
                            ));
                        }
                        $i++;
                    }

                    $successMessage = 'Treatment plan added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_social_economic')) {
            $validate = $validate->check($_POST, array(
                'social_economic_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {

                    $social_economic = $override->get3('social_economic', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];
                    if ($social_economic) {
                        $user->updateRecord('social_economic', array(
                            'visit_date' => Input::get('social_economic_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'social_economic_date' => Input::get('social_economic_date'),
                            'distance_km' => Input::get('distance_km'),
                            'distance_minutes' => Input::get('distance_minutes'),
                            'transport_mode' => Input::get('transport_mode'),
                            'transport_mode_other' => Input::get('transport_mode_other'),
                            'transportation_cost' => Input::get('transportation_cost'),
                            'household_head' => Input::get('household_head'),
                            'household_head_other' => Input::get('household_head_other'),
                            'household_years' => Input::get('household_years'),
                            'household_people' => Input::get('household_people'),
                            'primary_income_earner' => Input::get('primary_income_earner'),
                            'primary_income_earner_other' => Input::get('primary_income_earner_other'),
                            'formally_employed' => Input::get('formally_employed'),
                            'formally_employed_other' => Input::get('formally_employed_other'),
                            'main_income_based_other' => Input::get('main_income_based_other'),
                            'main_income_based' => Input::get('main_income_based'),
                            'reason_not_working' => Input::get('reason_not_working'),
                            'last_working' => Input::get('last_working'),
                            'stopped_working' => Input::get('stopped_working'),
                            'stopped_duration' => Input::get('stopped_duration'),
                            'someone_take_care' => Input::get('someone_take_care'),
                            'take_care_duration' => Input::get('take_care_duration'),
                            'quit_job' => Input::get('quit_job'),
                            'affect_social' => Input::get('affect_social'),
                            'affect_social_how' => Input::get('affect_social_how'),
                            'financial_burden' => Input::get('financial_burden'),
                            'affect_social_other' => Input::get('affect_social_other'),
                            'wealth_rate' => Input::get('wealth_rate'),
                            'contributer_occupation' => Input::get('contributer_occupation'),
                            'contributer_occupation_other' => Input::get('contributer_occupation_other'),
                            'main_occupation' => Input::get('main_occupation'),
                            'main_occupation_other' => Input::get('main_occupation_other'),
                            'main_icome_based' => Input::get('main_icome_based'),
                            'main_icome_other' => Input::get('main_icome_other'),
                            'earn_individual' => Input::get('earn_individual'),
                            'earn_household' => Input::get('earn_household'),
                            'main_transport' => Input::get('main_transport'),
                            'time_from_home' => Input::get('time_from_home'),
                            'leave_children' => Input::get('leave_children'),
                            'looking_children' => Input::get('looking_children'),
                            'looking_children_other' => Input::get('looking_children_other'),
                            'occupation_looking_child' => Input::get('occupation_looking_child'),
                            'occupation_looking_child_other' => Input::get('occupation_looking_child_other'),
                            'accompany' => Input::get('accompany'),
                            'accompany_occupation' => Input::get('accompany_occupation'),
                            'accompany_occupation_other' => Input::get('accompany_occupation_other'),
                            'accompany_transport' => Input::get('accompany_transport'),
                            'accompany_expenses' => Input::get('accompany_expenses'),
                            'activities_disrupted' => Input::get('activities_disrupted'),
                            'material_floor' => Input::get('material_floor'),
                            'material_floor_other' => Input::get('material_floor_other'),
                            'material_roof' => Input::get('material_roof'),
                            'material_roof_other' => Input::get('material_roof_other'),
                            'cooking_fuel' => Input::get('cooking_fuel'),
                            'cooking_fuel_other' => Input::get('cooking_fuel_other'),
                            'water_access' => Input::get('water_access'),
                            'water_source' => Input::get('water_source'),
                            'water_source_other' => Input::get('water_source_other'),
                            'toilet_access' => Input::get('toilet_access'),
                            'toilet_facility' => Input::get('toilet_facility'),
                            'toilet_access_other' => Input::get('toilet_access_other'),
                            'television' => Input::get('television'),
                            'refrigerator' => Input::get('refrigerator'),
                            'sofa' => Input::get('sofa'),
                            'clock' => Input::get('clock'),
                            'fan' => Input::get('fan'),
                            'vcr_dvd' => Input::get('vcr_dvd'),
                            'bank_account' => Input::get('bank_account'),
                            'no_food' => Input::get('no_food'),
                            'sleep_hungry' => Input::get('sleep_hungry'),
                            'day_hungry' => Input::get('day_hungry'),
                            'patient_education' => Input::get('patient_education'),
                            'patient_education_other' => Input::get('patient_education_other'),
                            'primary_earner_edctn' => Input::get('primary_earner_edctn'),
                            'household_education' => Input::get('household_education'),
                            'household_education_other' => Input::get('household_education_other'),
                            'earner_edctn_other' => Input::get('earner_edctn_other'),
                            'spouse_edctn' => Input::get('spouse_edctn'),
                            'spouse_edctn_other' => Input::get('spouse_edctn_other'),
                            'socioeconomic_notes' => Input::get('socioeconomic_notes'),
                            'socioeconomic_notes' => Input::get('socioeconomic_notes'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), $social_economic['id']);
                    } else {
                        $user->createRecord('social_economic', array(
                            'visit_date' => Input::get('social_economic_date'),
                            'study_id' => $_GET['sid'],
                            'visit_code' => $_GET['vcode'],
                            'visit_day' => $_GET['vday'],
                            'seq_no' => $_GET['seq'],
                            'vid' => $_GET['vid'],
                            'social_economic_date' => Input::get('social_economic_date'),
                            'distance_km' => Input::get('distance_km'),
                            'distance_minutes' => Input::get('distance_minutes'),
                            'transport_mode' => Input::get('transport_mode'),
                            'transport_mode_other' => Input::get('transport_mode_other'),
                            'transportation_cost' => Input::get('transportation_cost'),
                            'household_head' => Input::get('household_head'),
                            'household_head_other' => Input::get('household_head_other'),
                            'household_years' => Input::get('household_years'),
                            'household_people' => Input::get('household_people'),
                            'primary_income_earner' => Input::get('primary_income_earner'),
                            'primary_income_earner_other' => Input::get('primary_income_earner_other'),
                            'formally_employed' => Input::get('formally_employed'),
                            'formally_employed_other' => Input::get('formally_employed_other'),
                            'main_income_based_other' => Input::get('main_income_based_other'),
                            'main_income_based' => Input::get('main_income_based'),
                            'reason_not_working' => Input::get('reason_not_working'),
                            'last_working' => Input::get('last_working'),
                            'stopped_working' => Input::get('stopped_working'),
                            'stopped_duration' => Input::get('stopped_duration'),
                            'someone_take_care' => Input::get('someone_take_care'),
                            'take_care_duration' => Input::get('take_care_duration'),
                            'quit_job' => Input::get('quit_job'),
                            'affect_social' => Input::get('affect_social'),
                            'affect_social_how' => Input::get('affect_social_how'),
                            'affect_social_other' => Input::get('affect_social_other'),
                            'financial_burden' => Input::get('financial_burden'),
                            'wealth_rate' => Input::get('wealth_rate'),
                            'contributer_occupation' => Input::get('contributer_occupation'),
                            'contributer_occupation_other' => Input::get('contributer_occupation_other'),
                            'main_occupation' => Input::get('main_occupation'),
                            'main_occupation_other' => Input::get('main_occupation_other'),
                            'main_icome_based' => Input::get('main_icome_based'),
                            'main_icome_other' => Input::get('main_icome_other'),
                            'earn_individual' => Input::get('earn_individual'),
                            'earn_household' => Input::get('earn_household'),
                            'main_transport' => Input::get('main_transport'),
                            'time_from_home' => Input::get('time_from_home'),
                            'leave_children' => Input::get('leave_children'),
                            'looking_children' => Input::get('looking_children'),
                            'looking_children_other' => Input::get('looking_children_other'),
                            'occupation_looking_child' => Input::get('occupation_looking_child'),
                            'occupation_looking_child_other' => Input::get('occupation_looking_child_other'),
                            'accompany' => Input::get('accompany'),
                            'accompany_occupation' => Input::get('accompany_occupation'),
                            'accompany_occupation_other' => Input::get('accompany_occupation_other'),
                            'accompany_transport' => Input::get('accompany_transport'),
                            'accompany_expenses' => Input::get('accompany_expenses'),
                            'activities_disrupted' => Input::get('activities_disrupted'),
                            'material_floor' => Input::get('material_floor'),
                            'material_floor_other' => Input::get('material_floor_other'),
                            'material_roof' => Input::get('material_roof'),
                            'material_roof_other' => Input::get('material_roof_other'),
                            'cooking_fuel' => Input::get('cooking_fuel'),
                            'cooking_fuel_other' => Input::get('cooking_fuel_other'),
                            'water_access' => Input::get('water_access'),
                            'water_source' => Input::get('water_source'),
                            'water_source_other' => Input::get('water_source_other'),
                            'toilet_access' => Input::get('toilet_access'),
                            'toilet_facility' => Input::get('toilet_facility'),
                            'toilet_access_other' => Input::get('toilet_access_other'),
                            'television' => Input::get('television'),
                            'refrigerator' => Input::get('refrigerator'),
                            'sofa' => Input::get('sofa'),
                            'clock' => Input::get('clock'),
                            'fan' => Input::get('fan'),
                            'vcr_dvd' => Input::get('vcr_dvd'),
                            'bank_account' => Input::get('bank_account'),
                            'no_food' => Input::get('no_food'),
                            'sleep_hungry' => Input::get('sleep_hungry'),
                            'day_hungry' => Input::get('day_hungry'),
                            'patient_education' => Input::get('patient_education'),
                            'patient_education_other' => Input::get('patient_education_other'),
                            'primary_earner_edctn' => Input::get('primary_earner_edctn'),
                            'household_education' => Input::get('household_education'),
                            'household_education_other' => Input::get('household_education_other'),
                            'earner_edctn_other' => Input::get('earner_edctn_other'),
                            'spouse_edctn' => Input::get('spouse_edctn'),
                            'spouse_edctn_other' => Input::get('spouse_edctn_other'),
                            'socioeconomic_notes' => Input::get('socioeconomic_notes'),
                            'patient_id' => $_GET['cid'],
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                            'site_id' => $user->data()->site_id,
                        ));
                    }
                    $successMessage = 'Social economic  details added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_card_test')) {
            $validate = $validate->check($_POST, array(
                // 'cardiac' => array(
                //     'required' => true,
                // ),


            ));
            if ($validate->passed()) {
                try {
                    $multiArray = $override->get('card_test', 'status', 1);
                    foreach (Input::get('cardiac') as $searchValue) {
                        if ($user->isValueInMultiArrays($searchValue, $multiArray)) {
                            // echo "The value '{$searchValue}' exists in the multi-dimensional array.";
                            // $user->isValueInMultiArrays($searchValue, $multiArray);
                            // $id = $override->get('card_test', 'cardiac', $searchValue);
                            // $user->updateRecord('card_test', array(
                            //     'cardiac' => $searchValue,
                            // ), $id['id']);
                        } else {
                            // echo "The value '{$searchValue}' does not exist in the multi-dimensional array.";
                            $user->createRecord('card_test', array(
                                'cardiac' => $searchValue,
                            ));
                        }
                    }

                    $successMessage = 'Diagnosis added Successful';
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq'] . '&sid=' . $_GET['sid'] . '&vday=' . $_GET['vday']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_med')) {
            $user->updateRecord('medication_treatments', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'User Medication Successful Deleted';
        } elseif (Input::get('delete_admin')) {
            $user->updateRecord('hospitalization_table', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Hospitalization details Successful Deleted';
        } elseif (Input::get('delete_sickle')) {
            $user->updateRecord('sickle_cell_status_table', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'sickle cell status details Successful Deleted';
        } elseif (Input::get('add_medications')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'cardiac' => array(
                    'required' => true,
                ),
                'diabetes' => array(
                    'required' => true,
                ),
                'sickle_cell' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    if ($_GET['btn'] == 'Update') {
                        $user->updateRecord('medications', array(
                            'name' => Input::get('name'),
                            'use_group' => Input::get('groups'),
                            'forms' => Input::get('forms'),
                            'maintainance' => Input::get('maintainance'),
                            'cardiac' => Input::get('cardiac'),
                            'diabetes' => Input::get('diabetes'),
                            'sickle_cell' => Input::get('sickle_cell'),
                            'status' => 1,
                        ), Input::get('id'));
                        $successMessage = 'Medications Successful Updated';
                    } elseif ($_GET['btn'] == 'Add') {
                        $medications = $override->get('medications', 'name', Input::get('name'));

                        if ($medications) {
                            $errorMessage = 'Medications Already  Available Please Update instead!';
                        } else {
                            $user->createRecord('medications', array(
                                'name' => Input::get('name'),
                                'use_group' => Input::get('groups'),
                                'forms' => Input::get('forms'),
                                'maintainance' => Input::get('maintainance'),
                                'cardiac' => Input::get('cardiac'),
                                'diabetes' => Input::get('diabetes'),
                                'sickle_cell' => Input::get('sickle_cell'),
                                'status' => 1,
                            ));
                            $successMessage = 'Medications Successful Added';
                        }
                    }

                    Redirect::to('info.php?id=8');
                    // Redirect::to('info.php?id=8&status=' . $_GET['status']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_batch')) {
            $validate = $validate->check($_POST, array(
                'serial_name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    if ($_GET['btn'] == 'Update') {
                        $user->updateRecord('batch', array(
                            'medication_id' => Input::get('medication_id'),
                            'serial_name' => Input::get('serial_name'),
                            'amount' => Input::get('amount'),
                            'expire_date' => Input::get('expire_date'),
                            'remarks' => Input::get('remarks'),
                            'price' => Input::get('price'),
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                        ), Input::get('id'));
                        $successMessage = 'Medications Successful Updated';
                    } elseif ($_GET['btn'] == 'Add') {

                        $batches = $override->get('batch', 'serial_name', Input::get('serial_name'));
                        if ($batches) {
                            $errorMessage = 'Batch / Serial Name Already  Available Please Update instead!';
                        } else {
                            $user->createRecord('batch', array(
                                'medication_id' => Input::get('medication_id'),
                                'serial_name' => Input::get('serial_name'),
                                'amount' => Input::get('amount'),
                                'expire_date' => Input::get('expire_date'),
                                'remarks' => Input::get('remarks'),
                                'price' => Input::get('price'),
                                'status' => 1,
                                'create_on' => date('Y-m-d H:i:s'),
                                'site_id' =>  $user->data()->site_id,
                                'staff_id' => $user->data()->id,
                                'update_on' => date('Y-m-d H:i:s'),
                                'update_id' => $user->data()->id,
                            ));

                            $successMessage = 'Medications Successful Added';
                        }
                    }

                    // Redirect::to('info.php?id=9');

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
    <title>Penplus Database | Add Page</title>

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

    <style>
        #medication_table {
            border-collapse: collapse;
        }

        #medication_table th,
        #medication_table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #medication_table th {
            text-align: left;
            background-color: #f2f2f2;
        }

        #medication_table {
            border-collapse: collapse;
        }

        #medication_list th,
        #medication_list td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #medication_list th {
            text-align: left;
            background-color: #f2f2f2;
        }

        .remove-row {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
        }

        .remove-row:hover {
            background-color: #da190b;
        }

        .edit-row {
            background-color: #3FF22F;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
        }

        .edit-row:hover {
            background-color: #da190b;
        }

        #hospitalization_details_table {
            border-collapse: collapse;
        }

        #hospitalization_details_table th,
        #hospitalization_details_table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #hospitalization_details_table th,
        #hospitalization_details_table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #hospitalization_details_table th {
            text-align: left;
            background-color: #f2f2f2;
        }

        #sickle_cell_table {
            border-collapse: collapse;
        }

        #sickle_cell_table th,
        #sickle_cell_table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #sickle_cell_table th,
        #sickle_cell_table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #sickle_cell_table th {
            text-align: left;
            background-color: #f2f2f2;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include 'sidemenu.php'; ?>

        <?php if ($errorMessage) { ?>
            <div class="alert alert-danger text-center">
                <h4>Error!</h4>
                <?= $errorMessage ?>
            </div>
        <?php } elseif ($pageError) { ?>
            <div class="alert alert-danger text-center">
                <h4>Error!</h4>
                <?php foreach ($pageError as $error) {
                    echo $error . ' , ';
                } ?>
            </div>
        <?php } elseif ($successMessage) { ?>
            <div class="alert alert-success text-center">
                <h4>Success!</h4>
                <?= $successMessage ?>
            </div>
        <?php } ?>

        <?php if ($_GET['id'] == 1 && ($user->data()->position == 1 || $user->data()->position == 2)) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>General Form</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">General Form</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">General Elements</h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <form>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <!-- text input -->
                                                    <div class="form-group">
                                                        <label>Text</label>
                                                        <input type="text" class="form-control" placeholder="Enter ...">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Text Disabled</label>
                                                        <input type="text" class="form-control" placeholder="Enter ..." disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <!-- textarea -->
                                                    <div class="form-group">
                                                        <label>Textarea</label>
                                                        <textarea class="form-control" rows="3" placeholder="Enter ..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Textarea Disabled</label>
                                                        <textarea class="form-control" rows="3" placeholder="Enter ..." disabled></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 2 && $user->data()->position == 1) { ?>
        <?php } elseif ($_GET['id'] == 3 && $user->data()->position == 1) { ?>
        <?php } elseif ($_GET['id'] == 4) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>General Form</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">General Form</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">

                            <?php $client = $override->get('clients', 'id', $_GET['cid'])[0]; ?>

                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">

                                        <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>
                                            <h3 class="card-title">Add Client</h3>
                                        <?php } ?>

                                        <?php if ($user->data()->position == 2) { ?>
                                            <h3 class="card-title">View clients info</h3>
                                        <?php } ?>
                                    </div>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- Date -->
                                                        <div class="form-group">
                                                            <label>Registration Date:</label>
                                                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                                                <input type="text" name="clinic_date" id="clinic_date" class="validate[required,custom[date]] form-control datetimepicker-input" data-target="#reservationdate" value="<?php if ($client['clinic_date']) {
                                                                                                                                                                                                                                            print_r($client['clinic_date']);
                                                                                                                                                                                                                                        }  ?>" />
                                                                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>First Name</label>
                                                            <input class="form-control" type="text" name="firstname" id="firstname" placeholder="Type firstname..." onkeyup="fetchData()" value="<?php if ($client['firstname']) {
                                                                                                                                                                                                        print_r($client['firstname']);
                                                                                                                                                                                                    }  ?>" required />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>Middle Name</label>
                                                            <input class="validate[required] form-control" type="text" name="middlename" id="middlename" placeholder="Type middlename..." onkeyup="fetchData()" value="<?php if ($client['middlename']) {
                                                                                                                                                                                                                            print_r($client['middlename']);
                                                                                                                                                                                                                        }  ?>" required />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>Last Name</label>
                                                            <input class="validate[required] form-control" type="text" name="lastname" id="lastname" placeholder="Type lastname..." onkeyup="fetchData()" value="<?php if ($client['lastname']) {
                                                                                                                                                                                                                        print_r($client['lastname']);
                                                                                                                                                                                                                    }  ?>" required />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- Date -->
                                                        <div class="form-group">
                                                            <label>Date of Birth:</label>
                                                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                                                <input type="text" name="dob" id="dob" class="validate[required,custom[date]] form-control datetimepicker-input" data-target="#reservationdate" value="<?php if ($client['dob']) {
                                                                                                                                                                                                                            print_r($client['dob']);
                                                                                                                                                                                                                        }  ?>" />
                                                                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>Gender</label>
                                                            <select class="form-control" name="gender" style="width: 100%;" required>
                                                                <option value="<?= $client['gender'] ?>"><?php if ($client) {
                                                                                                                if ($client['gender'] == 1) {
                                                                                                                    echo 'Male';
                                                                                                                } elseif ($client['gender'] == 2) {
                                                                                                                    echo 'Female';
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Select';
                                                                                                            } ?></option>
                                                                <option value="1">Male</option>
                                                                <option value="2">Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>Education Level</label>
                                                            <select class="form-control" name="education_level" style="width: 100%;" required>
                                                                <option value="<?= $client['education_level'] ?>"><?php if ($client) {
                                                                                                                        if ($client['education_level'] == 1) {
                                                                                                                            echo 'Not attended school';
                                                                                                                        } elseif ($client['education_level'] == 2) {
                                                                                                                            echo 'Primary';
                                                                                                                        } elseif ($client['education_level'] == 3) {
                                                                                                                            echo 'Secondary';
                                                                                                                        } elseif ($client['education_level'] == 4) {
                                                                                                                            echo 'Certificate';
                                                                                                                        } elseif ($client['education_level'] == 5) {
                                                                                                                            echo 'Diploma';
                                                                                                                        } elseif ($client['education_level'] == 6) {
                                                                                                                            echo 'Undergraduate degree';
                                                                                                                        } elseif ($client['education_level'] == 7) {
                                                                                                                            echo 'Postgraduate degree';
                                                                                                                        } elseif ($client['education_level'] == 8) {
                                                                                                                            echo 'N / A';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                <option value="1">Not attended school</option>
                                                                <option value="2">Primary</option>
                                                                <option value="3">Secondary</option>
                                                                <option value="4">Certificate</option>
                                                                <option value="5">Diploma</option>
                                                                <option value="6">Undergraduate degree</option>
                                                                <option value="7">Postgraduate degree</option>
                                                                <option value="8">N / A</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Hospital ID (Patient Hospital ID Number )</label>
                                                            <input class="form-control" type="text" name="hospital_id" id="hospital_id" value="<?php if ($client['hospital_id']) {
                                                                                                                                                    print_r($client['hospital_id']);
                                                                                                                                                }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>


                                            <?php
                                            //  if ($override->get4('clients', 'id', $_GET['cid'], 'age')) {
                                            ?>
                                            <div id="adult">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Employment status</label>
                                                                <select class="form-control" name="employment_status" style="width: 100%;" required>
                                                                    <option value="<?= $client['employment_status'] ?>"><?php if ($client) {
                                                                                                                            if ($client['employment_status'] == 1) {
                                                                                                                                echo 'Employed';
                                                                                                                            } elseif ($client['employment_status'] == 2) {
                                                                                                                                echo 'Self-employed';
                                                                                                                            } elseif ($client['employment_status'] == 3) {
                                                                                                                                echo 'Employed but on leave of absence';
                                                                                                                            } elseif ($client['employment_status'] == 4) {
                                                                                                                                echo 'Unemployed';
                                                                                                                            } elseif ($client['employment_status'] == 5) {
                                                                                                                                echo 'Student';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            echo 'Select';
                                                                                                                        } ?></option>
                                                                    <option value="1">Employed</option>
                                                                    <option value="2">Self-employed</option>
                                                                    <option value="3">Employed but on leave of absence</option>
                                                                    <option value="4">Unemployed</option>
                                                                    <option value="5">Student</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Occupational Exposures</label>
                                                                <select class="form-control" name="occupation" id="occupation" style="width: 100%;" onchange="checkQuestionValue1('occupation','exposure')" required>
                                                                    <option value="<?= $client['occupation'] ?>"><?php if ($client) {
                                                                                                                        if ($client['occupation'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($client['occupation'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($client['occupation'] == 3) {
                                                                                                                            echo 'Unknown';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unknown</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!-- <input type="text" id="occupation" onkeyup="checkQuestionValue('occupation','exposure')"> -->

                                                    </div>
                                                    <div class="col-sm-6 hidden" id="exposure">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>If yes, list exposure:</label>
                                                                <textarea class="form-control" name="exposure" rows="4"><?php if ($client['exposure']) {
                                                                                                                            print_r($client['exposure']);
                                                                                                                        }  ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php
                                            // }
                                            ?>

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Patient Phone Number</label>
                                                            <input class="form-control" type="text" name="phone_number" id="phone_number" value="<?php if ($client['phone_number']) {
                                                                                                                                                        print_r($client['phone_number']);
                                                                                                                                                    }  ?>" /> <span>Example: 0700 000 111</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Guardian Phone Number</label>
                                                            <input class="form-control" type="text" name="guardian_phone" id="guardian_phone" value="<?php if ($client['guardian_phone']) {
                                                                                                                                                            print_r($client['guardian_phone']);
                                                                                                                                                        }  ?>" /> <span>Example: 0700 000 111</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Guardian Name</label>
                                                            <input class="form-control" type="text" name="guardian_name" id="guardian_name" value="<?php if ($client['guardian_name']) {
                                                                                                                                                        print_r($client['guardian_name']);
                                                                                                                                                    }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Relation to patient</label>
                                                            <input class="form-control" type="text" name="relation_patient" id="relation_patient" value="<?php if ($client['relation_patient']) {
                                                                                                                                                                print_r($client['relation_patient']);
                                                                                                                                                            }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Physical Address</label>
                                                            <input class="form-control" type="text" name="physical_address" id="physical_address" value="<?php if ($client['physical_address']) {
                                                                                                                                                                print_r($client['physical_address']);
                                                                                                                                                            }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Comments / Remarks:</label>
                                                            <textarea class="form-control" name="comments" rows="4"><?php if ($client['comments']) {
                                                                                                                        print_r($client['comments']);
                                                                                                                    }  ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <a href='index1.php' class="btn btn-default">Back</a>
                                            <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>

                                                <input type="submit" name="add_client" value="Submit" class="btn btn-primary">
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 5) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Medication Form</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Medication Form</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">

                            <?php
                            // $client = $override->get('clients', 'id', $_GET['cid'])[0];
                            $medication = $override->getNews('medications', 'status', 1, 'id', $_GET['medication_id'])['0'];
                            $forms = $override->getNews('forms', 'status', 1, 'id', $_GET['forms']);
                            $use_group = $override->getNews('use_group', 'status', 1, 'id', $_GET['use_group']);
                            $maintainances = $override->getNews('maintainance', 'status', 1, 'id', $_GET['maintainance']);
                            ?>

                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">

                                        <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>
                                            <h3 class="card-title">Add / Update Medication</h3>
                                        <?php } ?>

                                        <?php if ($user->data()->position == 2) { ?>
                                            <h3 class="card-title">Add / Update Medication</h3>
                                        <?php } ?>
                                    </div>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
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


                                                <div class="col-sm-2">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label for="forms">Form</label>
                                                            <select name="forms" class="form-control" style="width: 100%;" required>
                                                                <?php if (!$forms) { ?>
                                                                    <option value="">Select Form / Units</option>
                                                                <?php } else { ?>
                                                                    <option value="<?= $forms[0]['id'] ?>"><?= $forms[0]['name'] ?></option>
                                                                <?php } ?>
                                                                <?php foreach ($override->get('forms', 'status', 1) as $form) { ?>
                                                                    <option value="<?= $form['id'] ?>"><?= $form['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label for="forms">Category</label>
                                                            <select name="groups" class="form-control" style="width: 100%;" required>
                                                                <?php if (!$use_group) { ?>
                                                                    <option value="">Select Category / Groups</option>
                                                                <?php } else { ?>
                                                                    <option value="<?= $use_group[0]['id'] ?>"><?= $use_group[0]['name'] ?></option>
                                                                <?php } ?>
                                                                <?php foreach ($override->get('use_group', 'status', 1) as $group) { ?>
                                                                    <option value="<?= $group['id'] ?>"><?= $group['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label for="forms">Maintainance Type</label>
                                                            <select name="maintainance" class="form-control" style="width: 100%;" required>
                                                                <?php if (!$use_group) { ?>
                                                                    <option value="">Select Maintainance Type</option>
                                                                <?php } else { ?>
                                                                    <option value="<?= $maintainances[0]['id'] ?>"><?= $maintainances[0]['name'] ?></option>
                                                                <?php } ?>
                                                                <?php foreach ($override->get('maintainance', 'status', 1) as $maintainance) { ?>
                                                                    <option value="<?= $maintainance['id'] ?>"><?= $maintainance['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">Cardiac</label>
                                                            <select name="cardiac" class="form-control" style="width: 100%;" required>
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
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">Diabetes</label>
                                                            <select name="diabetes" class="form-control" style="width: 100%;" required>
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
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">Sickle Cell</label>
                                                            <select name="sickle_cell" class="form-control" style="width: 100%;" required>
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
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <a href='index1.php' class="btn btn-default">Back</a>
                                            <input type="hidden" name="id" value="<?= $_GET['medication_id'] ?>">
                                            <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>

                                                <input type="submit" name="add_medications" value="Submit" class="btn btn-primary">
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 6) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Medication Batch Form</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Medication Batch Form</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">

                            <?php
                            // $client = $override->get('clients', 'id', $_GET['cid'])[0];
                            $medications = $override->getNews('medications', 'status', 1, 'id', $_GET['medication_id']);
                            $batches = $override->getNews('batch', 'status', 1, 'id', $_GET['batch_id'])[0];
                            ?>

                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">

                                        <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>
                                            <h3 class="card-title">Add / Update Medication Batch / Serial</h3>
                                        <?php } ?>

                                        <?php if ($user->data()->position == 2) { ?>
                                            <h3 class="card-title">Add / Update Medication Batch / Serial</h3>
                                        <?php } ?>
                                    </div>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label for="medication_id">Medication Name</label>
                                                            <select name="medication_id" class="form-control" style="width: 100%;" required>
                                                                <?php if (!$medications) { ?>
                                                                    <option value="">Select Medication Name</option>
                                                                <?php } else { ?>
                                                                    <option value="<?= $medications[0]['id'] ?>"><?= $medications[0]['name'] ?></option>
                                                                <?php } ?>
                                                                <?php foreach ($override->get('medications', 'status', 1) as $medication) { ?>
                                                                    <option value="<?= $medication['id'] ?>"><?= $medication['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Batch / Serial Number:</label>
                                                            <input class="form-control" type="text" name="serial_name" id="serial_name" value="<?php if ($batches['serial_name']) {
                                                                                                                                                    print_r($batches['serial_name']);
                                                                                                                                                }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Amount</label>
                                                            <input class="form-control" type="number" min="0" max="10000" name="amount" id="amount" value="<?php if ($batches['amount']) {
                                                                                                                                                                print_r($batches['amount']);
                                                                                                                                                            }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Expire Date:</label>
                                                            <input class="form-control" type="date" name="expire_date" id="expire_date" value="<?php if ($batches['expire_date']) {
                                                                                                                                                    print_r($batches['expire_date']);
                                                                                                                                                }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>



                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Price</label>
                                                            <input class="form-control" type="number" min="0" max="10000000000000" name="price" id="price" value="<?php if ($batches['price']) {
                                                                                                                                                                        print_r($batches['price']);
                                                                                                                                                                    }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Remarks</label>
                                                            <input class="form-control" type="text" name="remarks" id="remarks" value="<?php if ($batches['remarks']) {
                                                                                                                                            print_r($batches['remarks']);
                                                                                                                                        }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <a href='index1.php' class="btn btn-default">Back</a>
                                            <input type="hidden" name="id" value="<?= $_GET['batch_id'] ?>">
                                            <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>

                                                <input type="submit" name="add_batch" value="Submit" class="btn btn-primary">
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 7) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Demographic Form</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Demographic Form</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php $demographic = $override->get3('demographic', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                            $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                            $category = $override->get('main_diagnosis', 'patient_id', $_GET['cid'])[0];
                            $cat = '';

                            if ($category['cardiac'] == 1) {
                                $cat = 'Cardiac';
                            } elseif ($category['diabetes'] == 1) {
                                $cat = 'Diabetes';
                            } elseif ($category['sickle_cell'] == 1) {
                                $cat = 'Sickle cell';
                            } else {
                                $cat = 'Not Diagnosed';
                            }


                            if ($patient['gender'] == 1) {
                                $gender = 'Male';
                            } elseif ($patient['gender'] == 2) {
                                $gender = 'Female';
                            }



                            $name = 'Patient ID: ' . $patient['study_id'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <!-- <h3 class="card-title">
                                            <strong style="font-size: larger">
                                                <?= $name ?>
                                            </strong>
                                        </h3> -->
                                    </div>
                                    <!-- Content Header (Page header) -->
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Patient ID:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['study_id']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Age:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['age']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Gender</a></li>
                                                        <li class="breadcrumb-item active"><?= $gender; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Type</a></li>
                                                        <li class="breadcrumb-item active"><?= $cat; ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Date of Visit:</label>
                                                            <input class="validate[required,custom[date]] form-control" type="date" name="visit_date" id="visit_date" value="<?php if ($demographic['visit_date']) {
                                                                                                                                                                                    print_r($demographic['visit_date']);
                                                                                                                                                                                }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php
                                            if (!$override->get4('clients', 'id', $_GET['cid'], 'age')) {
                                            ?>

                                                <div class="row">

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Appropriate grade for age:</label>
                                                                <select class="form-control" name="grade_age" style="width: 100%;">
                                                                    <option value="<?= $demographic['grade_age'] ?>"><?php if ($demographic) {
                                                                                                                            if ($demographic['grade_age'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($demographic['grade_age'] == 2) {
                                                                                                                                echo 'No';
                                                                                                                            } elseif ($demographic['grade_age'] == 3) {
                                                                                                                                echo 'N/A';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            echo 'Select';
                                                                                                                        } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">N/A</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>NCD limiting school attendance::</label>
                                                                <select class="form-control" name="school_attendance" style="width: 100%;">
                                                                    <option value="<?= $demographic['school_attendance'] ?>"><?php if ($demographic) {
                                                                                                                                    if ($demographic['school_attendance'] == 1) {
                                                                                                                                        echo 'Yes';
                                                                                                                                    } elseif ($demographic['school_attendance'] == 2) {
                                                                                                                                        echo 'No';
                                                                                                                                    } elseif ($demographic['school_attendance'] == 3) {
                                                                                                                                        echo 'N/A';
                                                                                                                                    }
                                                                                                                                } else {
                                                                                                                                    echo 'Select';
                                                                                                                                } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">N/A</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>

                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Days of missed school in past month:</label>
                                                            <input class="form-control" type="number" min="0" max="100" name="missed_school" id="missed_school" value="<?php if ($demographic['missed_school']) {
                                                                                                                                                                            print_r($demographic['missed_school']);
                                                                                                                                                                        }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Household Size:</label>
                                                            <input class="form-control" type="number" min="0" max="100" name="household_size" id="household_size" value="<?php if ($demographic['household_size']) {
                                                                                                                                                                                print_r($demographic['household_size']);
                                                                                                                                                                            }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Patient referred from:</label>
                                                            <select class="form-control" id="referred" name="referred" style="width: 100%;" onchange="checkQuestionValue96('referred','referred_other')">
                                                                <option value=" <?= $demographic['referred'] ?>"><?php if ($demographic) {
                                                                                                                        if ($demographic['referred'] == 1) {
                                                                                                                            echo 'Inpatient / hospital stay';
                                                                                                                        } elseif ($demographic['referred'] == 2) {
                                                                                                                            echo 'Primary care clinic';
                                                                                                                        } elseif ($demographic['referred'] == 3) {
                                                                                                                            echo 'Other outpatient clinic';
                                                                                                                        } elseif ($demographic['referred'] == 4) {
                                                                                                                            echo 'Maternal health';
                                                                                                                        } elseif ($demographic['referred'] == 5) {
                                                                                                                            echo 'Community';
                                                                                                                        } elseif ($demographic['referred'] == 6) {
                                                                                                                            echo 'Self';
                                                                                                                        } elseif ($demographic['referred'] == 96) {
                                                                                                                            echo 'Other';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                <option value="1">Inpatient / hospital stay</option>
                                                                <option value="2">Primary care clinic</option>
                                                                <option value="3">Other outpatient clinic</option>
                                                                <option value="4">Maternal health</option>
                                                                <option value="5">Community</option>
                                                                <option value="6">Self</option>
                                                                <option value="96">Other</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3" id="referred_other">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Other Specify:</label>
                                                            <input class="form-control" type="text" name="referred_other" value="<?php if ($demographic['referred_other']) {
                                                                                                                                        print_r($demographic['referred_other']);
                                                                                                                                    }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Agrees to home visits:</label>
                                                            <select class="form-control" name="next_visit" style="width: 100%;" required>
                                                                <option value="<?= $demographic['next_visit'] ?>"><?php if ($demographic) {
                                                                                                                        if ($demographic['next_visit'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($demographic['next_visit'] == 2) {
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
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>CHW name:</label>
                                                            <input class="form-control" type=" text" name="chw" id="chw" value="<?php if ($demographic['chw']) {
                                                                                                                                    print_r($demographic['chw']);
                                                                                                                                }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Comments:</label>
                                                            <textarea class="form-control" name="comments" rows="4"><?php if ($demographic['comments']) {
                                                                                                                        print_r($demographic['comments']);
                                                                                                                    }  ?> </textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->

                                        <div class="card-footer">
                                            <a href='info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>' class="btn btn-default">Back</a>
                                            <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>
                                                <input type="hidden" name="sid" value="<?= $_GET['sid'] ?>">
                                                <input type="hidden" name="vday" value="<?= $_GET['vday'] ?>">
                                                <input type="submit" name="add_demographic" value="Submit" class="btn btn-primary">
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 8) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Vital Signs Form</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Vital Signs Form</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php
                            $vital = $override->get3('vital', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];
                            if ($override->get5('clients', 'id', $_GET['cid'], 'age', 25)) {
                                $height = $override->get3('vital', 'patient_id', $_GET['cid'], 'seq_no', 1, 'visit_code', 'EV')[0]['height'];
                            }

                            $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                            $category = $override->get('main_diagnosis', 'patient_id', $_GET['cid'])[0];
                            $cat = '';

                            if ($category['cardiac'] == 1) {
                                $cat = 'Cardiac';
                            } elseif ($category['diabetes'] == 1) {
                                $cat = 'Diabetes';
                            } elseif ($category['sickle_cell'] == 1) {
                                $cat = 'Sickle cell';
                            } else {
                                $cat = 'Not Diagnosed';
                            }


                            if ($patient['gender'] == 1) {
                                $gender = 'Male';
                            } elseif ($patient['gender'] == 2) {
                                $gender = 'Female';
                            }

                            $name = 'Patient ID: ' . $patient['study_id'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <!-- <h3 class="card-title">
                                            <strong style="font-size: larger">
                                                <?= $name ?>
                                            </strong>
                                        </h3> -->
                                    </div>
                                    <!-- Content Header (Page header) -->
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Patient ID:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['study_id']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Age:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['age']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Gender</a></li>
                                                        <li class="breadcrumb-item active"><?= $gender; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Type</a></li>
                                                        <li class="breadcrumb-item active"><?= $cat; ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->

                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Vital Signs Date</label>
                                                                <input class="validate[required,custom[date]] form-control" type="date" name="visit_date" id="visit_date" value="<?php if ($vital['visit_date']) {
                                                                                                                                                                                        print_r($vital['visit_date']);
                                                                                                                                                                                    }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Ht (cm)</label>
                                                                <input class="form-control" type="text" name="height" id="height" value="<?php if ($vital['height']) {
                                                                                                                                                print_r($vital['height']);
                                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Wt (kg):</label>
                                                                <input class="form-control" type="text" name="weight" id="weight" value="<?php if ($vital['weight']) {
                                                                                                                                                print_r($vital['weight']);
                                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>BMI</label><span>&nbsp;&nbsp; ( kg/m2 )</span>
                                                                <input class="form-control" name="bmi" id="bmi" value="<?php if ($vital['bmi']) {
                                                                                                                            print_r($vital['bmi']);
                                                                                                                        }  ?>" readonly placeholder="bmi value here">

                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>MUAC (cm)</label>
                                                                <input class="form-control" type="text" name="muac" id="muac" value="<?php if ($vital['muac']) {
                                                                                                                                            print_r($vital['muac']);
                                                                                                                                        }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Systolic</label>
                                                                <input class="form-control" type="text" name="systolic" id="systolic" value="<?php if ($vital['systolic']) {
                                                                                                                                                    print_r($vital['systolic']);
                                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Dystolic</label>
                                                                <input class="form-control" type="text" name="dystolic" id="dystolic" value="<?php if ($vital['dystolic']) {
                                                                                                                                                    print_r($vital['dystolic']);
                                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>PR</label>
                                                                <input class="form-control" type="text" name="pr" id="pr" value="<?php if ($vital['pr']) {
                                                                                                                                        print_r($vital['pr']);
                                                                                                                                    }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->

                                        <div class="card-footer">
                                            <a href='info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>' class="btn btn-default">Back</a>
                                            <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>
                                                <input type="submit" name="add_vital" value="Submit" class="btn btn-primary">
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 9) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Pateint Category Form</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Pateint Category Form</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php
                            $main_diagnosis = $override->get3('main_diagnosis', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];
                            $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                            $category = $override->get('main_diagnosis', 'patient_id', $_GET['cid'])[0];
                            $cat = '';

                            if ($category['cardiac'] == 1) {
                                $cat = 'Cardiac';
                            } elseif ($category['diabetes'] == 1) {
                                $cat = 'Diabetes';
                            } elseif ($category['sickle_cell'] == 1) {
                                $cat = 'Sickle cell';
                            } else {
                                $cat = 'Not Diagnosed';
                            }


                            if ($patient['gender'] == 1) {
                                $gender = 'Male';
                            } elseif ($patient['gender'] == 2) {
                                $gender = 'Female';
                            }


                            $name = 'Patient ID: ' . $patient['study_id'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <!-- <h3 class="card-title">
                                            <strong style="font-size: larger">
                                                <?= $name ?>
                                            </strong>
                                        </h3> -->
                                    </div>
                                    <!-- Content Header (Page header) -->
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Patient ID:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['study_id']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Age:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['age']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Gender</a></li>
                                                        <li class="breadcrumb-item active"><?= $gender; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Type</a></li>
                                                        <li class="breadcrumb-item active"><?= $cat; ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <!-- <div class="row"> -->
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Date of Diagnosis:</label>
                                                            <input class="validate[required,custom[date]] form-control" type="text" name="diagnosis_date" id="diagnosis_date" value="<?php if ($main_diagnosis['visit_date']) {
                                                                                                                                                                                            print_r($main_diagnosis['visit_date']);
                                                                                                                                                                                        }  ?>" required />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Patient for Cardiac:</label>
                                                            <select class="form-control" name="cardiac" id="cardiac" style="width: 100%;" required>
                                                                <option value="<?= $main_diagnosis['cardiac'] ?>"><?php if ($main_diagnosis) {
                                                                                                                        if ($main_diagnosis['cardiac'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($main_diagnosis['cardiac'] == 2) {
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
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Patient for Diabetes:</label>
                                                            <select class="form-control" name="diabetes" style="width: 100%;" required>
                                                                <option value="<?= $main_diagnosis['diabetes'] ?>"><?php if ($main_diagnosis) {
                                                                                                                        if ($main_diagnosis['diabetes'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($main_diagnosis['diabetes'] == 2) {
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
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Patient for Sickle cell:</label>
                                                            <select class="form-control" name="sickle_cell" style="width: 100%;" required>
                                                                <option value="<?= $main_diagnosis['sickle_cell'] ?>"><?php if ($main_diagnosis) {
                                                                                                                            if ($main_diagnosis['sickle_cell'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($main_diagnosis['sickle_cell'] == 2) {
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

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Reamrks / Comments / Notes:</label>
                                                            <textarea class="form-control" name="comments" id="comments" cols="30" rows="4">
                                                                    <?php if ($main_diagnosis['comments']) {
                                                                        print_r($main_diagnosis['comments']);
                                                                    }  ?>
                                                                </textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- </div> -->
                                        </div>
                                        <!-- /.card-body -->

                                        <div class="card-footer">
                                            <a href='info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>' class="btn btn-default">Back</a>
                                            <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>
                                                <input type="submit" name="add_main_diagnosis" value="Submit" class="btn btn-primary">
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 10) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Patient Hitory & Complication</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Patient Hitory & Complication</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php $history = $override->get3('history', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                            $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                            $category = $override->get('main_diagnosis', 'patient_id', $_GET['cid'])[0];
                            $cat = '';

                            if ($category['cardiac'] == 1) {
                                $cat = 'Cardiac';
                            } elseif ($category['diabetes'] == 1) {
                                $cat = 'Diabetes';
                            } elseif ($category['sickle_cell'] == 1) {
                                $cat = 'Sickle cell';
                            } else {
                                $cat = 'Not Diagnosed';
                            }


                            if ($patient['gender'] == 1) {
                                $gender = 'Male';
                            } elseif ($patient['gender'] == 2) {
                                $gender = 'Female';
                            }

                            $name = 'Patient ID: ' . $patient['study_id'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">

                                    <div class="card-header">
                                        <h3 class="card-title">Diseases History</h3>

                                        <!-- <h3 class="card-title">
                                            <strong style="font-size: larger">
                                                <?= $name ?>
                                            </strong>
                                        </h3> -->
                                    </div>
                                    <!-- Content Header (Page header) -->
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Patient ID:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['study_id']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Age:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['age']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Gender</a></li>
                                                        <li class="breadcrumb-item active"><?= $gender; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Type</a></li>
                                                        <li class="breadcrumb-item active"><?= $cat; ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">

                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) { ?>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card card-warning">
                                                            <div class="card-header">
                                                                <h3 class="card-title">cardiac</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Hypertension</label>
                                                                <select name="hypertension" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['hypertension'] ?>"><?php if ($history) {
                                                                                                                        if ($history['hypertension'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($history['hypertension'] == 2) {
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

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Diabetes</label>
                                                                <select name="diabetes" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['diabetes'] ?>"><?php if ($history) {
                                                                                                                    if ($history['diabetes'] == 1) {
                                                                                                                        echo 'Yes';
                                                                                                                    } elseif ($history['diabetes'] == 2) {
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

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>CKD</label>
                                                                <select name="ckd" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['ckd'] ?>"><?php if ($history) {
                                                                                                                if ($history['ckd'] == 1) {
                                                                                                                    echo 'Yes';
                                                                                                                } elseif ($history['ckd'] == 2) {
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

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Depression</label>
                                                                <select name="depression" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['depression'] ?>"><?php if ($history) {
                                                                                                                        if ($history['depression'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($history['depression'] == 2) {
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
                                                </div>


                                            <?php } ?>


                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) { ?>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card card-warning">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Diabetic</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Cardiovascular Diseases</label>
                                                                <select name="cardiovascular" id="cardiovascular" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['cardiovascular'] ?>"><?php if ($history) {
                                                                                                                            if ($history['cardiovascular'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($history['cardiovascular'] == 2) {
                                                                                                                                echo 'No';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            echo 'Select';
                                                                                                                        } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                                <span> (eg. Heart attack, ischemic heart disease, CCF)</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="cardiovascular_date">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Date of Cardiovascular</label>
                                                                <input type="date" name="cardiovascular_date" class="form-control" value="<?php if ($history['cardiovascular_date']) {
                                                                                                                                                print_r($history['cardiovascular_date']);
                                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Retinopathy</label>
                                                                <select name="retinopathy" id="retinopathy" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['retinopathy'] ?>"><?php if ($history) {
                                                                                                                        if ($history['retinopathy'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($history['retinopathy'] == 2) {
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

                                                    <div class="col-sm-3 hidden" id="retinopathy_date">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Date of Retinopathy</label>
                                                                <input type="date" name="retinopathy_date" class="form-control" id="retinopathy_date" value="<?php if ($history['retinopathy_date']) {
                                                                                                                                                                    print_r($history['retinopathy_date']);
                                                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>

                                                <div class="row">

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Renal Disease </label>
                                                                <select name="renal" id="renal" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['renal'] ?>"><?php if ($history) {
                                                                                                                    if ($history['renal'] == 1) {
                                                                                                                        echo 'Yes';
                                                                                                                    } elseif ($history['renal'] == 2) {
                                                                                                                        echo 'No';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                                <span> (e.g elevated creatinine)</span>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="renal_date">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Date of Renal</label>
                                                                <input type="date" name="renal_date" class="form-control" value="<?php if ($history['renal_date']) {
                                                                                                                                        print_r($history['renal_date']);
                                                                                                                                    }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Stroke / TIA</label>
                                                                <select name="stroke_tia" id="stroke_tia" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['stroke_tia'] ?>"><?php if ($history) {
                                                                                                                        if ($history['stroke_tia'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($history['stroke_tia'] == 2) {
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

                                                    <div class="col-sm-3" id="stroke_tia_date">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Date of Stroke / TIA</label>
                                                                <input type="date" name="stroke_tia_date" class="form-control" value="<?php if ($history['stroke_tia_date']) {
                                                                                                                                            print_r($history['stroke_tia_date']);
                                                                                                                                        }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>PVD </label>
                                                                <select name="pvd" id="pvd" style="width: 100%;" class="form-control">
                                                                    <option value="<?= $history['pvd'] ?>"><?php if ($history) {
                                                                                                                if ($history['pvd'] == 1) {
                                                                                                                    echo 'Yes';
                                                                                                                } elseif ($history['pvd'] == 2) {
                                                                                                                    echo 'No';
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Select';
                                                                                                            } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                                <span> (e.g ulcers, gangrene)</span>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3 hidden" id="pvd_date">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Date of PVD</label>
                                                                <input type="date" name="pvd_date" id="pvd_date" class="form-control" value="<?php if ($history['pvd_date']) {
                                                                                                                                                    print_r($history['pvd_date']);
                                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Neuropathy</label>
                                                                <select name="neuropathy" id="neuropathy" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['neuropathy'] ?>"><?php if ($history) {
                                                                                                                        if ($history['neuropathy'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($history['neuropathy'] == 2) {
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

                                                    <div class="col-sm-3 hidden" id="neuropathy_date">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Date of Neuropathy</label>
                                                                <input type="date" name="neuropathy_date" class="form-control" id="neuropathy_date" value="<?php if ($history['neuropathy_date']) {
                                                                                                                                                                print_r($history['neuropathy_date']);
                                                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Sexual dysfunction</label>
                                                                <select name="sexual_dysfunction" id="sexual_dysfunction" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['sexual_dysfunction'] ?>"><?php if ($history) {
                                                                                                                                if ($history['sexual_dysfunction'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($history['sexual_dysfunction'] == 2) {
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

                                                    <div class="col-sm-6" id="sexual_dysfunction_date">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Date of Sexual dysfunction</label>
                                                                <input type="date" name="sexual_dysfunction_date" class="form-control" id="sexual_dysfunction_date" value="<?php if ($history['sexual_dysfunction_date']) {
                                                                                                                                                                                print_r($history['sexual_dysfunction_date']);
                                                                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            <?php } ?>


                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card card-warning">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Sickle Cell ( Previous complications at intake ) </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Pain Event</label>
                                                                <select name="pain_event" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['pain_event'] ?>"><?php if ($history) {
                                                                                                                        if ($history['pain_event'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($history['pain_event'] == 2) {
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

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Stroke</label>
                                                                <select name="stroke" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['stroke'] ?>"><?php if ($history) {
                                                                                                                    if ($history['stroke'] == 1) {
                                                                                                                        echo 'Yes';
                                                                                                                    } elseif ($history['stroke'] == 2) {
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

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Pneumonia </label>
                                                                <select name="pneumonia" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['pneumonia'] ?>"><?php if ($history) {
                                                                                                                        if ($history['pneumonia'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($history['pneumonia'] == 2) {
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
                                                </div>



                                                <div class="row">

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Blood Transfusion</label>
                                                                <select name="blood_transfusion" class="form-control" id="blood_transfusion" style="width: 100%;" onchange="checkQuestionValue3('blood_transfusion','transfusion_born','transfusion_12months')">
                                                                    <option value="<?= $history['blood_transfusion'] ?>"><?php if ($history) {
                                                                                                                                if ($history['blood_transfusion'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($history['blood_transfusion'] == 2) {
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

                                                    <div class="col-sm-4" id="transfusion_born">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>how many times since you were born ? </label>
                                                                <input type="text" name="transfusion_born" class="form-control" value="<?php if ($history['transfusion_born']) {
                                                                                                                                            print_r($history['transfusion_born']);
                                                                                                                                        }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4" id="transfusion_12months">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>how many times for the past twelve months ?</label>
                                                                <input type="text" name="transfusion_12months" class="form-control" value="<?php if ($history['transfusion_12months']) {
                                                                                                                                                print_r($history['transfusion_12months']);
                                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>



                                                <div class="row">

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Acute chest syndrome </label>
                                                                <select name="acute_chest" style="width: 100%;" class="form-control" required>
                                                                    <option value="<?= $history['acute_chest'] ?>"><?php if ($history) {
                                                                                                                        if ($history['acute_chest'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($history['acute_chest'] == 2) {
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

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Any Other Complication?</label>
                                                                <select name="other_complication" id="other_complication" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('other_complication','specify_complication')">
                                                                    <option value="<?= $history['other_complication'] ?>"><?php if ($history) {
                                                                                                                                if ($history['other_complication'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($history['other_complication'] == 2) {
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

                                                    <div class="col-sm-6" id="specify_complication">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Other Specify</label>
                                                                <input type="text" name="specify_complication" class="form-control" value="<?php if ($history['specify_complication']) {
                                                                                                                                                print_r($history['specify_complication']);
                                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php } ?>



                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>HIV</label>
                                                            <select name="hiv" id="hiv" style="width: 100%;" class="form-control">
                                                                <option value="<?= $history['hiv'] ?>"><?php if ($history) {
                                                                                                            if ($history['hiv'] == 1) {
                                                                                                                echo 'R';
                                                                                                            } elseif ($history['hiv'] == 2) {
                                                                                                                echo 'NR';
                                                                                                            } elseif ($history['hiv'] == 3) {
                                                                                                                echo 'Unknown';
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo 'Select';
                                                                                                        } ?></option>
                                                                <option value="1">R</option>
                                                                <option value="2">NR</option>
                                                                <option value="3">Unknown</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3" id="hiv_test">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Date Of Test</label>
                                                            <input type="date" name="hiv_test" class="form-control" value="<?php if ($history['hiv_test']) {
                                                                                                                                print_r($history['hiv_test']);
                                                                                                                            }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3" id="art1">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>On ART ?</label>
                                                            <select name="art" id="art" style="width: 100%;" class="form-control">
                                                                <option value="<?= $history['art'] ?>"><?php if ($history) {
                                                                                                            if ($history['art'] == 1) {
                                                                                                                echo 'Yes';
                                                                                                            } elseif ($history['art'] == 2) {
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


                                                <div class="col-sm-3" id="art_date">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>ART Start Date</label>
                                                            <input type="date" name="art_date" class="form-control" value="<?php if ($history['art_date']) {
                                                                                                                                print_r($history['art_date']);
                                                                                                                            }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>TB</label>
                                                            <select name="tb" id="tb" style="width: 100%;" class="form-control" onchange="checkNotQuestionValue5('tb','tb_year')">
                                                                <option value="<?= $history['tb'] ?>"><?php if ($history) {
                                                                                                            if ($history['tb'] == 1) {
                                                                                                                echo 'Smear pos';
                                                                                                            } elseif ($history['tb'] == 2) {
                                                                                                                echo 'Smear neg';
                                                                                                            } elseif ($history['tb'] == 3) {
                                                                                                                echo 'EPTB';
                                                                                                            } elseif ($history['tb'] == 4) {
                                                                                                                echo 'never had TB';
                                                                                                            } elseif ($history['tb'] == 5) {
                                                                                                                echo 'Unknown';
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo 'Select';
                                                                                                        } ?></option>
                                                                <option value="1">Smear pos</option>
                                                                <option value="2">Smear neg</option>
                                                                <option value="3">EPTB</option>
                                                                <option value="4">never had TB</option>
                                                                <option value="5">Unknown</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6" id="tb_year">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Year TB tested</label>
                                                            <input type="number" name="tb_year" min="1970" max="2024" class="form-control" value="<?php if ($history['tb_year']) {
                                                                                                                                                        print_r($history['tb_year']);
                                                                                                                                                    }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>History of smoking</label>
                                                            <select name="smoking" id="smoking" class="form-control" style="width: 100%;">
                                                                <option value="<?= $history['smoking'] ?>"><?php if ($history) {
                                                                                                                if ($history['smoking'] == 1) {
                                                                                                                    echo 'Yes';
                                                                                                                } elseif ($history['smoking'] == 2) {
                                                                                                                    echo 'No';
                                                                                                                } elseif ($history['smoking'] == 3) {
                                                                                                                    echo 'Unknown';
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Select';
                                                                                                            } ?></option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                                <option value="3">Unknown</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4" id="packs">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Number of pack years</label>
                                                            <input type="text" name="packs" min="0" max="100000" class="form-control" value="<?php if ($history['packs']) {
                                                                                                                                                    print_r($history['packs']);
                                                                                                                                                }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="active_smoker">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>Active smoker</label>
                                                            <select name="active_smoker" class="form-control" style="width: 100%;">
                                                                <option value="<?= $history['active_smoker'] ?>"><?php if ($history) {
                                                                                                                        if ($history['active_smoker'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($history['active_smoker'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($history['active_smoker'] == 3) {
                                                                                                                            echo 'Unknown';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                                <option value="3">Unknown</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>




                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Hepatitis test?:</label>
                                                                <select name="hepatitis_test" id="hepatitis_test" class="form-control" style="width: 100%;" onchange="checkQuestionValue3('hepatitis_test','hepatitis_date','hepatitis_results')">
                                                                    <option value="<?= $history['hepatitis_test'] ?>"><?php if ($history) {
                                                                                                                            if ($history['hepatitis_test'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($history['hepatitis_test'] == 2) {
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

                                                    <div class="col-sm-3" id="hepatitis_date">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Date Test</label>
                                                                <input type="date" name="hepatitis_date" class="form-control" value="<?php if ($history['hepatitis_date']) {
                                                                                                                                            print_r($history['hepatitis_date']);
                                                                                                                                        }  ?>" />

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="hepatitis_results">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Test Results</label>
                                                                <input type="text" name="hepatitis_results" class="form-control" value="<?php if ($history['hepatitis_results']) {
                                                                                                                                            print_r($history['hepatitis_results']);
                                                                                                                                        }  ?>" />

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>ABO Blood Group:</label>
                                                                <select name="blood_group" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['blood_group'] ?>"><?php if ($history) {
                                                                                                                        if ($history['blood_group'] == 1) {
                                                                                                                            echo 'A+';
                                                                                                                        } elseif ($history['blood_group'] == 2) {
                                                                                                                            echo 'A-';
                                                                                                                        } elseif ($history['blood_group'] == 3) {
                                                                                                                            echo 'B+';
                                                                                                                        } elseif ($history['blood_group'] == 4) {
                                                                                                                            echo 'B-';
                                                                                                                        } elseif ($history['blood_group'] == 5) {
                                                                                                                            echo 'O+';
                                                                                                                        } elseif ($history['blood_group'] == 6) {
                                                                                                                            echo 'O-';
                                                                                                                        } elseif ($history['blood_group'] == 7) {
                                                                                                                            echo 'AB+';
                                                                                                                        } elseif ($history['blood_group'] == 8) {
                                                                                                                            echo 'AB-';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?>
                                                                    </option>
                                                                    <option value="1">A+</option>
                                                                    <option value="2">A-</option>
                                                                    <option value="3">B+</option>
                                                                    <option value="4">B-</option>
                                                                    <option value="5">O+</option>
                                                                    <option value="6">O-</option>
                                                                    <option value="7">AB+</option>
                                                                    <option value="8">AB</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>


                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>Alcohol consumption</label>
                                                            <select name="alcohol" id="alcohol" class="form-control" style="width: 100%;" onchange="checkNotQuestionValue3('alcohol','alcohol_hides')">
                                                                <option value="<?= $history['alcohol'] ?>"><?php if ($history) {
                                                                                                                if ($history['alcohol'] == 1) {
                                                                                                                    echo 'Yes, currently';
                                                                                                                } elseif ($history['alcohol'] == 2) {
                                                                                                                    echo 'Yes, in the past';
                                                                                                                } elseif ($history['alcohol'] == 3) {
                                                                                                                    echo 'never';
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Select';
                                                                                                            } ?></option>
                                                                <option value="1">Yes, currently</option>
                                                                <option value="2">Yes, in the past</option>
                                                                <option value="3">never</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3" id="alcohol_type1">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>Type of Alcohol consumption</label>
                                                            <select name="alcohol_type" id="alcohol_type" class="form-control" style="width: 100%;" onchange="checkQuestionValue96('alcohol_type','alcohol_other')">
                                                                <option value="<?= $history['alcohol_type'] ?>"><?php if ($history) {
                                                                                                                    if ($history['alcohol_type'] == 1) {
                                                                                                                        echo 'Regular beer';
                                                                                                                    } elseif ($history['alcohol_type'] == 2) {
                                                                                                                        echo 'Spirits';
                                                                                                                    } elseif ($history['alcohol_type'] == 3) {
                                                                                                                        echo 'Wine';
                                                                                                                    } elseif ($history['alcohol_type'] == 96) {
                                                                                                                        echo 'Other';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                <option value="1">Regular beer</option>
                                                                <option value="2">Spirits</option>
                                                                <option value="3">Wine</option>
                                                                <option value="96">Other</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3" id="alcohol_other">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Other Type of Alcohol</label>
                                                            <input type="text" name="alcohol_other" class="form-control" value="<?php if ($history['alcohol_other']) {
                                                                                                                                    print_r($history['alcohol_other']);
                                                                                                                                }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3" id="quantity">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Quantity (number of bottle)</label>
                                                            <input type="number" name="quantity" min="0" max="10000" class="form-control" value="<?php if ($history['quantity']) {
                                                                                                                                                        print_r($history['quantity']);
                                                                                                                                                    }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) { ?>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Family History of cardiac disease?</label>
                                                                <select name="cardiac_disease" id="cardiac_disease" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('cardiac_disease','cardiac_surgery1')">
                                                                    <option value="<?= $history['cardiac_disease'] ?>"><?php if ($history) {
                                                                                                                            if ($history['cardiac_disease'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($history['cardiac_disease'] == 2) {
                                                                                                                                echo 'No';
                                                                                                                            } elseif ($history['cardiac_disease'] == 3) {
                                                                                                                                echo 'Unknown';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            echo 'Select';
                                                                                                                        } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unknown</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>History of cardiac surgery?</label>
                                                                <select name="cardiac_surgery" id="cardiac_surgery" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('cardiac_surgery','cardiac_surgery_type1')">
                                                                    <option value="<?= $history['cardiac_surgery'] ?>"><?php if ($history) {
                                                                                                                            if ($history['cardiac_surgery'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($history['cardiac_surgery'] == 2) {
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

                                                    <div class="col-sm-3" id="cardiac_surgery_type1">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Type of cardiac surgery</label>
                                                                <select name="cardiac_surgery_type" id="cardiac_surgery_type" class="form-control" style="width: 100%;" onchange="checkQuestionValue96('cardiac_surgery_type','surgery_other1')">
                                                                    <option value=" <?= $history['cardiac_surgery_type'] ?>"><?php if ($history) {
                                                                                                                                    if ($history['cardiac_surgery_type'] == 1) {
                                                                                                                                        echo 'Valve Surgery';
                                                                                                                                    } elseif ($history['cardiac_surgery_type'] == 2) {
                                                                                                                                        echo 'Defect repair';
                                                                                                                                    } elseif ($history['cardiac_surgery_type'] == 96) {
                                                                                                                                        echo 'Other specify';
                                                                                                                                    }
                                                                                                                                } else {
                                                                                                                                    echo 'Select';
                                                                                                                                } ?></option>
                                                                    <option value="1">Valve Surgery</option>
                                                                    <option value="2">Defect repair</option>
                                                                    <option value="96">Other specify</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="surgery_other1">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Specify surgery</label>
                                                                <input type="text" name="surgery_other" class="form-control" value="<?php if ($history['surgery_other']) {
                                                                                                                                        print_r($history['surgery_other']);
                                                                                                                                    }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php } ?>

                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) { ?>

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Family History of Diabetic disease?</label>
                                                                <select name="diabetic_disease" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['diabetic_disease'] ?>"><?php if ($history) {
                                                                                                                            if ($history['diabetic_disease'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($history['diabetic_disease'] == 2) {
                                                                                                                                echo 'No';
                                                                                                                            } elseif ($history['diabetic_disease'] == 3) {
                                                                                                                                echo 'Unknown';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            echo 'Select';
                                                                                                                        } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unknown</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Hypertension ?</label>
                                                                <select name="hypertension_disease" class="form-control" id="hypertension_disease" style="width: 100%;">
                                                                    <option value="<?= $history['hypertension_disease'] ?>"><?php if ($history) {
                                                                                                                                if ($history['hypertension_disease'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($history['hypertension_disease'] == 2) {
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
                                                </div>

                                            <?php } ?>

                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Family History of SCD?</label>
                                                                <select name="history_scd" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['history_scd'] ?>"><?php if ($history) {
                                                                                                                        if ($history['history_scd'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($history['history_scd'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($history['history_scd'] == 3) {
                                                                                                                            echo 'Unknown';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unknown</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Vaccine History</label>
                                                                <select name="vaccine_history" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $history['vaccine_history'] ?>"><?php if ($history) {
                                                                                                                            if ($history['vaccine_history'] == 1) {
                                                                                                                                echo 'Pneumococcal';
                                                                                                                            } elseif ($history['vaccine_history'] == 2) {
                                                                                                                                echo 'Meningococcal';
                                                                                                                            } elseif ($history['vaccine_history'] == 3) {
                                                                                                                                echo 'Haemophilus Influenza type B (Hib)';
                                                                                                                            } elseif ($history['vaccine_history'] == 4) {
                                                                                                                                echo 'PPCV 23';
                                                                                                                            } elseif ($history['vaccine_history'] == 99) {
                                                                                                                                echo 'Unknown';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            echo 'Select';
                                                                                                                        } ?>
                                                                    </option>
                                                                    <option value="1">Pneumococcal </option>
                                                                    <option value="2">Meningococcal</option>
                                                                    <option value="3">Haemophilus Influenza type B (Hib)</option>
                                                                    <option value="4">PPCV 23</option>
                                                                    <option value="99">Unknown</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>How many siblings do you have?</label>
                                                                <input type="number" name="siblings" class="form-control" value="<?php if ($history['siblings']) {
                                                                                                                                        print_r($history['siblings']);
                                                                                                                                    }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>How many of them are alive?</label>
                                                                <input type="number" name="sibling_salive" class="form-control" value="<?php if ($history['sibling_salive']) {
                                                                                                                                            print_r($history['sibling_salive']);
                                                                                                                                        }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                                <div class="row-form clearfix">

                                                    <table id="myTable3" class="table order-list">
                                                        <thead>
                                                            <tr>
                                                                <th> age </th>
                                                                <th> sex </th>
                                                                <th> sickle cell disease status </th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($override->getNews('sickle_cell_status_table', 'patient_id', $_GET['cid'], 'status', 1) as $sickle_cell_status_table) { ?>

                                                                <tr>
                                                                    <td>
                                                                        <input type="text" class="form-control" name="age[]" id="age[]" placeholder="Type age..." value="<?php if ($sickle_cell_status_table['age']) {
                                                                                                                                                                                print_r($sickle_cell_status_table['age']);
                                                                                                                                                                            }  ?>">
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control" name="sex[]" style="width: 100%;">
                                                                            <option value="<?= $sickle_cell_status_table['sex'] ?>"><?php if ($sickle_cell_status_table) {
                                                                                                                                        if ($sickle_cell_status_table['sex'] == 1) {
                                                                                                                                            echo 'Male';
                                                                                                                                        } elseif ($sickle_cell_status_table['sex'] == 2) {
                                                                                                                                            echo 'Female';
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        echo 'Select';
                                                                                                                                    } ?></option>
                                                                            <option value="">Select</option>
                                                                            <option value="1">Male</option>
                                                                            <option value="2">Female</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control" name="sickle_status[]" value='<?php if ($sickle_cell_status_table['sickle_status']) {
                                                                                                                                                    print_r($sickle_cell_status_table['sickle_status']);
                                                                                                                                                }  ?>'>
                                                                    </td>
                                                                    <td>
                                                                        <input type="button" class="ibtnDel3 btn btn-md btn-warning" value="Remove">
                                                                        <a href="#delete_sickle<?= $sickle_cell_status_table['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                                    </td>
                                                                </tr>
                                                                <div class="modal fade" id="delete_sickle<?= $sickle_cell_status_table['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <form method="post">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                                    <h4>Delete this ickle cell status details </h4>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <strong style="font-weight: bold;color: red">
                                                                                        <p>Are you sure you want to delete this sickle cell status details ?</p>
                                                                                    </strong>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <input type="hidden" name="id" value="<?= $sickle_cell_status_table['id'] ?>">
                                                                                    <input type="submit" name="delete_sickle" value="Delete" class="btn btn-danger">
                                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>

                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                    <input type="button" class="btn btn-lg btn-block " id="addrow3" value="Add Row" />
                                                </div>

                                            <?php } ?>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Any Other Family History ?</label>
                                                            <select name="history_other" id="history_other" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('history_other','history_specify')">
                                                                <option value="<?= $history['history_other'] ?>"><?php if ($history) {
                                                                                                                        if ($history['history_other'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($history['history_other'] == 2) {
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

                                                <div class="col-sm-6" id="history_specify">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Specify Other</label>
                                                            <input type="text" name="history_specify" class="form-control" value="<?php if ($history['history_specify']) {
                                                                                                                                        print_r($history['history_specify']);
                                                                                                                                    }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <a href='info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>' class="btn btn-default">Back</a>
                                            <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>

                                                <input type="submit" name="add_history" value="Submit" class="btn btn-primary">
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 11) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>History, Symtom & Exam</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">History, Symtom & Exam</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php
                            $symptoms = $override->get3('symptoms', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                            $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                            $category = $override->get('main_diagnosis', 'patient_id', $_GET['cid'])[0];
                            $cat = '';

                            if ($category['cardiac'] == 1) {
                                $cat = 'Cardiac';
                            } elseif ($category['diabetes'] == 1) {
                                $cat = 'Diabetes';
                            } elseif ($category['sickle_cell'] == 1) {
                                $cat = 'Sickle cell';
                            } else {
                                $cat = 'Not Diagnosed';
                            }


                            if ($patient['gender'] == 1) {
                                $gender = 'Male';
                            } elseif ($patient['gender'] == 2) {
                                $gender = 'Female';
                            }

                            $name = 'Patient ID: ' . $patient['study_id'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">

                                    <div class="card-header">
                                        <h3 class="card-title">History, Symtom & Exam</h3>

                                        <!-- <h3 class="card-title">
                                            <strong style="font-size: larger">
                                                <?= $name ?>
                                            </strong>
                                        </h3> -->
                                    </div>
                                    <!-- Content Header (Page header) -->
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Patient ID:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['study_id']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Age:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['age']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Gender</a></li>
                                                        <li class="breadcrumb-item active"><?= $gender; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Type</a></li>
                                                        <li class="breadcrumb-item active"><?= $cat; ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Visit Date</label>
                                                            <input class="validate[required,custom[date]] form-control" type="date" name="visit_date" id="visit_date" value="<?php if ($symptoms['visit_date']) {
                                                                                                                                                                                    print_r($symptoms['visit_date']);
                                                                                                                                                                                }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) { ?>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Symptoms ( Cardiac )</h3>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Orthopnea</label>
                                                                <select name="orthopnea" id="orthopnea" class="form-control" required>
                                                                    <option value="<?= $symptoms['orthopnea'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['orthopnea'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['orthopnea'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['orthopnea'] == 3) {
                                                                                                                            echo 'Unsure';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unsure</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Cough</label>
                                                                <select name="cough" id="cough" class="form-control" style="width: 100%;" required>
                                                                    <option value="<?= $symptoms['cough'] ?>"><?php if ($symptoms) {
                                                                                                                    if ($symptoms['cough'] == 1) {
                                                                                                                        echo 'Yes';
                                                                                                                    } elseif ($symptoms['cough'] == 2) {
                                                                                                                        echo 'No';
                                                                                                                    } elseif ($symptoms['cough'] == 3) {
                                                                                                                        echo 'Unsure';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unsure</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">

                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Paroxysmal nocturnal dyspnea</label>
                                                                <select name="paroxysmal" id="paroxysmal" class="form-control" style="width: 100%;" required>
                                                                    <option value="<?= $symptoms['paroxysmal'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['paroxysmal'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['paroxysmal'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['paroxysmal'] == 3) {
                                                                                                                            echo 'Unsure';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unsure</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Dyspnea on exertion: NYHA Classification</label>
                                                                <select name="dyspnea" style="width: 100%;" class="form-control" required>
                                                                    <option value="<?= $symptoms['dyspnea'] ?>"><?php if ($symptoms) {
                                                                                                                    if ($symptoms['dyspnea'] == 1) {
                                                                                                                        echo 'I';
                                                                                                                    } elseif ($symptoms['dyspnea'] == 2) {
                                                                                                                        echo 'II';
                                                                                                                    } elseif ($symptoms['dyspnea'] == 3) {
                                                                                                                        echo 'III';
                                                                                                                    } elseif ($symptoms['dyspnea'] == 4) {
                                                                                                                        echo 'IV';
                                                                                                                    } elseif ($symptoms['dyspnea'] == 5) {
                                                                                                                        echo 'cannot determine';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">I</option>
                                                                    <option value="2">II</option>
                                                                    <option value="3">III</option>
                                                                    <option value="4">IV</option>
                                                                    <option value="5">cannot determine</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>

                                            <?php } ?>

                                            <div class="card card-warning">
                                                <div class="card-header">
                                                    <h3 class="card-title">Pains ( Symptoms )</h3>
                                                </div>
                                            </div>

                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1) || $override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>


                                                <div class="row">

                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Chest Pain</label>
                                                                <select name="chest_pain" id="chest_pain" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['chest_pain'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['chest_pain'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['chest_pain'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['chest_pain'] == 3) {
                                                                                                                            echo 'Unsure';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unsure</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6" id="score_chest_pain">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Pain Score Today( Chest Pain ):</label>
                                                                <input type="text" name="score_chest_pain" class="form-control" value="<?php if ($symptoms['score_chest_pain']) {
                                                                                                                                            print_r($symptoms['score_chest_pain']);
                                                                                                                                        }  ?>" />
                                                            </div>
                                                            <span> ( 1 - 10 )</span>
                                                        </div>
                                                    </div>

                                                </div>

                                            <?php } ?>

                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>


                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Headache Pains </label>
                                                                <select name="headache" id="headache" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['headache'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['headache'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['headache'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['headache'] == 3) {
                                                                                                                            echo 'Unsure';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unsure</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6" id="score_headache">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Pain Score Today( Headache ):</label>
                                                                <input type="text" name="score_headache" min="0" max="10" class="form-control" value="<?php if ($symptoms['score_headache']) {
                                                                                                                                                            print_r($symptoms['score_headache']);
                                                                                                                                                        }  ?>" />
                                                            </div>
                                                            <span> ( 1 - 10 )</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1) || $override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>

                                                <div class="row">

                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Abnorminal Pain</label>
                                                                <select name="abnorminal_pain" id="abnorminal_pain" class="form-control" style="width: 100%;" onload="checkQuestionValue1('abnorminal_pain','score_abnorminal_pain')">
                                                                    <option value="<?= $symptoms['abnorminal_pain'] ?>"><?php if ($symptoms) {
                                                                                                                            if ($symptoms['abnorminal_pain'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($symptoms['abnorminal_pain'] == 2) {
                                                                                                                                echo 'No';
                                                                                                                            } elseif ($symptoms['abnorminal_pain'] == 3) {
                                                                                                                                echo 'Unk';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            echo 'Select';
                                                                                                                        } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6" id="score_abnorminal_pain">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Pain Score Today( Abnorminal Pain ):</label>
                                                                <input type="number" name="score_abnorminal_pain" min="1" max="10" class="form-control" value="<?php if ($symptoms['score_abnorminal_pain']) {
                                                                                                                                                                    print_r($symptoms['score_abnorminal_pain']);
                                                                                                                                                                }  ?>" />
                                                            </div>
                                                            <span> ( 1 - 10 )</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>

                                                <div class="row">

                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Upper arms Pains </label>
                                                                <select name="upper_arms" id="upper_arms" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('upper_arms','score_upper_arms')">
                                                                    <option value="<?= $symptoms['upper_arms'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['upper_arms'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['upper_arms'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['upper_arms'] == 3) {
                                                                                                                            echo 'Unk';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6" id="score_upper_arms">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Pain Score Today( Upper arms ):</label>
                                                                <input type="text" name="score_upper_arms" class="form-control" min="0" max="10" value="<?php if ($symptoms['score_upper_arms']) {
                                                                                                                                                            print_r($symptoms['score_upper_arms']);
                                                                                                                                                        }  ?>" />
                                                            </div>
                                                            <span> ( 1 - 10 )</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">


                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Lower arms Pains </label>
                                                                <select name="lower_arms" id="lower_arms" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('lower_arms','score_lower_arms')">
                                                                    <option value="<?= $symptoms['lower_arms'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['lower_arms'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['lower_arms'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['lower_arms'] == 3) {
                                                                                                                            echo 'Unk';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6" id="score_lower_arms">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Pain Score Today( Lower arms ):</label>
                                                                <input type="text" name="score_lower_arms" class="form-control" min="0" max="10" value="<?php if ($symptoms['score_lower_arms']) {
                                                                                                                                                            print_r($symptoms['score_lower_arms']);
                                                                                                                                                        }  ?>" />
                                                            </div>
                                                            <span> ( 1 - 10 )</span>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">

                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Waist Pains </label>
                                                                <select name="waist" id="waist" style="width: 100%;" class="form-control" onchange="checkQuestionValue1('waist','score_waist')">
                                                                    <option value="<?= $symptoms['waist'] ?>"><?php if ($symptoms) {
                                                                                                                    if ($symptoms['waist'] == 1) {
                                                                                                                        echo 'Yes';
                                                                                                                    } elseif ($symptoms['waist'] == 2) {
                                                                                                                        echo 'No';
                                                                                                                    } elseif ($symptoms['waist'] == 3) {
                                                                                                                        echo 'Unk';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6" id="score_waist">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Pain Score Today( Waist ):</label>
                                                                <input type="text" name="score_waist" class="form-control" min="0" max="10" value="<?php if ($symptoms['score_waist']) {
                                                                                                                                                        print_r($symptoms['score_waist']);
                                                                                                                                                    }  ?>" />
                                                            </div>
                                                            <span> ( 1 - 10 )</span>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Joints Pains</label>
                                                                <select name="joints" id="joints" style="width: 100%;" class="form-control" onchange="checkQuestionValue1('joints','joints_hides')">
                                                                    <option value="<?= $symptoms['joints'] ?>"><?php if ($symptoms) {
                                                                                                                    if ($symptoms['joints'] == 1) {
                                                                                                                        echo 'Yes';
                                                                                                                    } elseif ($symptoms['joints'] == 2) {
                                                                                                                        echo 'No';
                                                                                                                    } elseif ($symptoms['joints'] == 3) {
                                                                                                                        echo 'Unk';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4" id="joints_hides1">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Joints( Specify )</label>
                                                                <input type="text" name="spescify_joints" class="form-control" value="<?php if ($symptoms['spescify_joints']) {
                                                                                                                                            print_r($symptoms['spescify_joints']);
                                                                                                                                        }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4" id="joints_hides2">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Pain Score Today ( Joints ):</label>
                                                                <input type="number" name="score_joints" class="form-control" min="0" max="10" value="<?php if ($symptoms['score_joints']) {
                                                                                                                                                            print_r($symptoms['score_joints']);
                                                                                                                                                        }  ?>" />
                                                            </div>
                                                            <span> ( 1 - 10 )</span>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Any Other Pain?</label>
                                                                <select name="other_pain" id="other_pain" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['other_pain'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['other_pain'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['other_pain'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['other_pain'] == 3) {
                                                                                                                            echo 'Unk';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4" id="spescify_other_pain">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Other Pain ( Specify )</label>
                                                                <input type="text" name="spescify_other_pain" class="form-control" value="<?php if ($symptoms['spescify_other_pain']) {
                                                                                                                                                print_r($symptoms['spescify_other_pain']);
                                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4" id="score_other_pain">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Pain Score Today ( Other ):</label>
                                                                <input type="number" name="score_other_pain" class="form-control" min="0" max="10" value="<?php if ($symptoms['score_other_pain']) {
                                                                                                                                                                print_r($symptoms['score_other_pain']);
                                                                                                                                                            }  ?>" />
                                                            </div>
                                                            <span> ( 1 - 10 )</span>
                                                        </div>
                                                    </div>
                                                </div>


                                            <?php } ?>
                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) { ?>


                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Glucose Monitoring ( Diabetic )</h3>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Fasting FS:</label>
                                                                <input type="text" name="fasting" class="form-control" value="<?php if ($symptoms['fasting']) {
                                                                                                                                    print_r($symptoms['fasting']);
                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Random FS:</label>
                                                                <input type="text" name="random_fs" class="form-control" value="<?php if ($symptoms['random_fs']) {
                                                                                                                                    print_r($symptoms['random_fs']);
                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php if ($_GET['seq'] == 1) { ?>
                                                                    <label>HbA1C:( During enrollment )</label>
                                                                <?php } else { ?>
                                                                    <label>HbA1C:( During Follow up )</label>
                                                                <?php } ?>
                                                                <input type="text" name="hba1c" class="form-control" value="<?php if ($symptoms['hba1c']) {
                                                                                                                                print_r($symptoms['hba1c']);
                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Hypoglycemia ( Diabetic )</h3>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Symptoms of hypoglycemia?:</label>
                                                                <select name="hypoglycemia_symptoms" id="hypoglycemia_symptoms" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['hypoglycemia_symptoms'] ?>"><?php if ($symptoms) {
                                                                                                                                    if ($symptoms['hypoglycemia_symptoms'] == 1) {
                                                                                                                                        echo 'Yes';
                                                                                                                                    } elseif ($symptoms['hypoglycemia_symptoms'] == 2) {
                                                                                                                                        echo 'No';
                                                                                                                                    } elseif ($symptoms['hypoglycemia_symptoms'] == 3) {
                                                                                                                                        echo 'Unsure';
                                                                                                                                    }
                                                                                                                                } else {
                                                                                                                                    echo 'Select';
                                                                                                                                } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unsure</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php if ($_GET['seq'] == 1) { ?>
                                                                    <label>Severe hypoglycemia in last month?:</label>
                                                                <?php } else { ?>
                                                                    <label>Severe hypoglycemia since last visit?:</label>
                                                                <?php } ?>
                                                                <select name="hypoglycemia_severe" id="hypoglycemia_severe" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('hypoglycemia_severe','hypoglycemia__number')">
                                                                    <option value="<?= $symptoms['hypoglycemia_severe'] ?>"><?php if ($symptoms) {
                                                                                                                                if ($symptoms['hypoglycemia_severe'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($symptoms['hypoglycemia_severe'] == 2) {
                                                                                                                                    echo 'No';
                                                                                                                                } elseif ($symptoms['hypoglycemia_severe'] == 3) {
                                                                                                                                    echo 'Unsure';
                                                                                                                                }
                                                                                                                            } else {
                                                                                                                                echo 'Select';
                                                                                                                            } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unsure</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-6" id="hypoglycemia__number">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>If Yes, how many episodes of severe hypoglycemia</label>
                                                                <input type="number" name="hypoglycemia__number" min="0" max="100" class="form-control" value="<?php if ($symptoms['hypoglycemia__number']) {
                                                                                                                                                                    print_r($symptoms['hypoglycemia__number']);
                                                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Symptoms ( Diabetic )</h3>
                                                    </div>
                                                </div>

                                                <div class="row">

                                                    <div class="col-sm-2">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Increased thirst</label>
                                                                <select name="thirst" id="thirst" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['thirst'] ?>"><?php if ($symptoms) {
                                                                                                                    if ($symptoms['thirst'] == 1) {
                                                                                                                        echo 'Yes';
                                                                                                                    } elseif ($symptoms['thirst'] == 2) {
                                                                                                                        echo 'No';
                                                                                                                    } elseif ($symptoms['thirst'] == 3) {
                                                                                                                        echo 'Unk';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Increased Urination</label>
                                                                <select name="urination" id="urination" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['urination'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['urination'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['urination'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['urination'] == 3) {
                                                                                                                            echo 'Unk';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Vision Changes</label>
                                                                <select name="vision" id="vision" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['vision'] ?>"><?php if ($symptoms) {
                                                                                                                    if ($symptoms['vision'] == 1) {
                                                                                                                        echo 'Yes';
                                                                                                                    } elseif ($symptoms['vision'] == 2) {
                                                                                                                        echo 'No';
                                                                                                                    } elseif ($symptoms['vision'] == 3) {
                                                                                                                        echo 'Unk';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Vomiting</label>
                                                                <select name="vomiting" id="vomiting" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['vomiting'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['vomiting'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['vomiting'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['vomiting'] == 3) {
                                                                                                                            echo 'Unk';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Weight Loss</label>
                                                                <select name="weight_loss" id="weight_loss" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['weight_loss'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['weight_loss'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['weight_loss'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['weight_loss'] == 3) {
                                                                                                                            echo 'Unk';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php } ?>

                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Symptoms ( Sickle Cell )</h3>
                                                    </div>
                                                </div>

                                                <div class="row">

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Difficulty Breathing</label>
                                                                <select name="breathing" id="breathing" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['breathing'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['breathing'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['breathing'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['breathing'] == 3) {
                                                                                                                            echo 'Unk';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Any Other (Symptoms) ?</label>
                                                                <select name="other_sickle" id="other_sickle" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('other_sickle','sickle_specify')">
                                                                    <option value="<?= $symptoms['other_sickle'] ?>"><?php if ($symptoms) {
                                                                                                                            if ($symptoms['other_sickle'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($symptoms['other_sickle'] == 2) {
                                                                                                                                echo 'No';
                                                                                                                            } elseif ($symptoms['other_sickle'] == 3) {
                                                                                                                                echo 'Unk';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            echo 'Select';
                                                                                                                        } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="3">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>



                                                    <div class="col-sm-6" id="sickle_specify">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Other Symptoms (Specify)</label>
                                                                <input type="text" name="sickle_specify" class="form-control" value="<?php if ($symptoms['sickle_specify']) {
                                                                                                                                            print_r($symptoms['sickle_specify']);
                                                                                                                                        }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            <?php } ?>


                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) { ?>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Exam ( Cardiac )</h3>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Edema</label>
                                                                <select name="edema" id="edema" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['edema'] ?>"><?php if ($symptoms) {
                                                                                                                    if ($symptoms['edema'] == 1) {
                                                                                                                        echo 'None';
                                                                                                                    } elseif ($symptoms['edema'] == 2) {
                                                                                                                        echo 'Trace';
                                                                                                                    } elseif ($symptoms['edema'] == 3) {
                                                                                                                        echo '1+';
                                                                                                                    } elseif ($symptoms['edema'] == 4) {
                                                                                                                        echo '2+';
                                                                                                                    } elseif ($symptoms['edema'] == 5) {
                                                                                                                        echo '3+';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">None</option>
                                                                    <option value="2">Trace</option>
                                                                    <option value="3">1+</option>
                                                                    <option value="4">2+</option>
                                                                    <option value="5">3+</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Lungs</label>
                                                                <select name="lungs" id="lungs" class="form-control" style="width: 100%;" onchange="checkQuestionValue96('chest_pain','lungs_other')">
                                                                    <option value="<?= $symptoms['lungs'] ?>"><?php if ($symptoms) {
                                                                                                                    if ($symptoms['lungs'] == 1) {
                                                                                                                        echo 'Clear';
                                                                                                                    } elseif ($symptoms['lungs'] == 2) {
                                                                                                                        echo 'Bibasilar';
                                                                                                                    } elseif ($symptoms['lungs'] == 3) {
                                                                                                                        echo 'Crackles';
                                                                                                                    } elseif ($symptoms['lungs'] == 4) {
                                                                                                                        echo 'Wheeze';
                                                                                                                    } elseif ($symptoms['lungs'] == 96) {
                                                                                                                        echo 'Other';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">Clear</option>
                                                                    <option value="2">Bibasilar</option>
                                                                    <option value="3">Crackles</option>
                                                                    <option value="4">Wheeze</option>
                                                                    <option value="96">Other</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6" id="lungs_other">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Other Lung specify:</label>
                                                                <input type="text" name="lungs_other" class="form-control" value="<?php if ($symptoms['lungs_other']) {
                                                                                                                                        print_r($symptoms['lungs_other']);
                                                                                                                                    }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>JVP</label>
                                                                <select name="jvp" id="jvp" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['jvp'] ?>"><?php if ($symptoms) {
                                                                                                                if ($symptoms['jvp'] == 1) {
                                                                                                                    echo 'Elevated';
                                                                                                                } elseif ($symptoms['jvp'] == 2) {
                                                                                                                    echo 'Normal';
                                                                                                                } elseif ($symptoms['jvp'] == 3) {
                                                                                                                    echo 'Unable to determine';
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Select';
                                                                                                            } ?></option>
                                                                    <option value="1">Elevated</option>
                                                                    <option value="2">Normal</option>
                                                                    <option value="3">Unable to determine</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Volume status</label>
                                                                <select name="volume" id="volume" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['volume'] ?>"><?php if ($symptoms) {
                                                                                                                    if ($symptoms['volume'] == 1) {
                                                                                                                        echo 'Hyper';
                                                                                                                    } elseif ($symptoms['volume'] == 2) {
                                                                                                                        echo 'Hypo';
                                                                                                                    } elseif ($symptoms['volume'] == 3) {
                                                                                                                        echo 'Euvolemic';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">Hyper</option>
                                                                    <option value="2">Hypo</option>
                                                                    <option value="3">Euvolemic</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Loud Murmur?</label>
                                                                <select name="murmur" id="murmur" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['murmur'] ?>"><?php if ($symptoms) {
                                                                                                                    if ($symptoms['murmur'] == 1) {
                                                                                                                        echo 'Present';
                                                                                                                    } elseif ($symptoms['murmur'] == 2) {
                                                                                                                        echo 'Absent';
                                                                                                                    } elseif ($symptoms['murmur'] == 3) {
                                                                                                                        echo 'Unknown';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">Present</option>
                                                                    <option value="2">Absent</option>
                                                                    <option value="3">Unknown</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php } ?>


                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) { ?>


                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Foot Exam ( diabetes )</h3>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Foot Exam</label>
                                                                <select name="foot_exam" id="foot_exam" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('foot_exam','foot_exam_finding1')">
                                                                    <option value="<?= $symptoms['foot_exam'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['foot_exam'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['foot_exam'] == 2) {
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

                                                    <div class="col-sm-3" id="foot_exam_finding1">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Foot Exam Category</label>
                                                                <select name="foot_exam_finding" id="foot_exam_finding" class="form-control" style="width: 100%;" onchange="checkQuestionValue21('foot_exam_finding','foot_exam_other')">
                                                                    <option value="<?= $symptoms['foot_exam_finding'] ?>"><?php if ($symptoms) {
                                                                                                                                if ($symptoms['foot_exam_finding'] == 1) {
                                                                                                                                    echo 'Normal';
                                                                                                                                } elseif ($symptoms['foot_exam_finding'] == 2) {
                                                                                                                                    echo 'Abnormal';
                                                                                                                                }
                                                                                                                            } else {
                                                                                                                                echo 'Select';
                                                                                                                            } ?></option>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6" id="foot_exam_other">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Foot exam Finding Other:</label>
                                                                <input type="text" name="foot_exam_other" class="form-control" value="<?php if ($symptoms['foot_exam_other']) {
                                                                                                                                            print_r($symptoms['foot_exam_other']);
                                                                                                                                        }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php } ?>

                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Exam ( Sickle Cell )</h3>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Malnutrition</label>
                                                                <select name="malnutrition" id="malnutrition" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['malnutrition'] ?>"><?php if ($symptoms) {
                                                                                                                            if ($symptoms['malnutrition'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($symptoms['malnutrition'] == 2) {
                                                                                                                                echo 'No';
                                                                                                                            } elseif ($symptoms['malnutrition'] == 3) {
                                                                                                                                echo 'Unk';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            echo 'Select';
                                                                                                                        } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="2">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Pallor</label>
                                                                <select name="pallor" id="pallor" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['pallor'] ?>"><?php if ($symptoms) {
                                                                                                                    if ($symptoms['pallor'] == 1) {
                                                                                                                        echo 'Yes';
                                                                                                                    } elseif ($symptoms['pallor'] == 2) {
                                                                                                                        echo 'No';
                                                                                                                    } elseif ($symptoms['pallor'] == 3) {
                                                                                                                        echo 'Unk';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo 'Select';
                                                                                                                } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="2">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Jaundice</label>
                                                                <select name="jaundice" id="jaundice" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['jaundice'] ?>"><?php if ($symptoms) {
                                                                                                                        if ($symptoms['jaundice'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($symptoms['jaundice'] == 2) {
                                                                                                                            echo 'No';
                                                                                                                        } elseif ($symptoms['jaundice'] == 3) {
                                                                                                                            echo 'Unk';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 'Select';
                                                                                                                    } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="2">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Splenomegaly</label>
                                                                <select name="splenomegaly" id="splenomegaly" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $symptoms['splenomegaly'] ?>"><?php if ($symptoms) {
                                                                                                                            if ($symptoms['splenomegaly'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($symptoms['splenomegaly'] == 2) {
                                                                                                                                echo 'No';
                                                                                                                            } elseif ($symptoms['splenomegaly'] == 3) {
                                                                                                                                echo 'Unk';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            echo 'Select';
                                                                                                                        } ?></option>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                    <option value="2">Unk</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Labs ( Sickle Cell )</h3>
                                                    </div>
                                                </div>
                                                <div class="row">

                                                    <div class="col-sm-3" id="hb">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Hb:</label>
                                                                <input type="text" name="hb" class="form-control" value="<?php if ($symptoms['hb']) {
                                                                                                                                print_r($symptoms['hb']);
                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="wbc">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>WBC:</label>
                                                                <input type="text" name="wbc" class="form-control" value="<?php if ($symptoms['wbc']) {
                                                                                                                                print_r($symptoms['wbc']);
                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="plt">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Plt:</label>
                                                                <input type="text" name="plt" class="form-control" value="<?php if ($symptoms['plt']) {
                                                                                                                                print_r($symptoms['plt']);
                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="labs_other">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Other:</label>
                                                                <input type="text" name="labs_other" class="form-control" value="<?php if ($symptoms['labs_other']) {
                                                                                                                                        print_r($symptoms['labs_other']);
                                                                                                                                    }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            <?php } ?>

                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <a href='info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>' class="btn btn-default">Back</a>
                                            <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>

                                                <input type="submit" name="add_symptoms" value="Submit" class="btn btn-primary">
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 12) { ?>

        <?php } elseif ($_GET['id'] == 13) { ?>
        <?php } elseif ($_GET['id'] == 14) { ?>

        <?php } elseif ($_GET['id'] == 15) { ?>
        <?php } elseif ($_GET['id'] == 16) { ?>
        <?php } elseif ($_GET['id'] == 17) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Hospitalizazions Details</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Hospitalizazions Details</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php
                            $hospitalization_details = $override->get3('hospitalization_details', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];
                            $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                            $category = $override->get('main_diagnosis', 'patient_id', $_GET['cid'])[0];
                            $cat = '';

                            if ($category['cardiac'] == 1) {
                                $cat = 'Cardiac';
                            } elseif ($category['diabetes'] == 1) {
                                $cat = 'Diabetes';
                            } elseif ($category['sickle_cell'] == 1) {
                                $cat = 'Sickle cell';
                            } else {
                                $cat = 'Not Diagnosed';
                            }


                            if ($patient['gender'] == 1) {
                                $gender = 'Male';
                            } elseif ($patient['gender'] == 2) {
                                $gender = 'Female';
                            }

                            $name = 'Patient ID: ' . $patient['study_id'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Hospitalizazions Details</h3>
                                        <!-- <h3 class="card-title">
                                            <strong style="font-size: larger">
                                                <?= $name ?>
                                            </strong>
                                        </h3> -->
                                    </div>
                                    <!-- Content Header (Page header) -->
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Patient ID:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['study_id']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Age:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['age']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Gender</a></li>
                                                        <li class="breadcrumb-item active"><?= $gender; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Type</a></li>
                                                        <li class="breadcrumb-item active"><?= $cat; ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Visit date:</label>
                                                            <input class="form-control" type="date" name="hospitalization_date" id="hospitalization_date" value="<?php if ($hospitalization_details['hospitalization_date']) {
                                                                                                                                                                        print_r($hospitalization_details['hospitalization_date']);
                                                                                                                                                                    }  ?>" required />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <?php if ($_GET['seq'] == 1) { ?>
                                                                <label>Hospitalized in the past 12 Months for this NCD?:</label>
                                                            <?php } else { ?>
                                                                <label>Hospitalized since the last visit for this NCD?:</label>
                                                            <?php } ?>
                                                            <select name="hospitalization_ncd" id="hospitalization_ncd" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('hospitalization_ncd','hospitalization_ncd_hides')" required>
                                                                <option value="<?= $hospitalization_details['hospitalization_ncd'] ?>"><?php if ($hospitalization_details) {
                                                                                                                                            if ($hospitalization_details['hospitalization_ncd'] == 1) {
                                                                                                                                                echo 'Yes';
                                                                                                                                            } elseif ($hospitalization_details['hospitalization_ncd'] == 2) {
                                                                                                                                                echo 'No';
                                                                                                                                            } elseif ($hospitalization_details['hospitalization_ncd'] == 3) {
                                                                                                                                                echo 'Unknown';
                                                                                                                                            }
                                                                                                                                        } else {
                                                                                                                                            echo 'Select';
                                                                                                                                        } ?>
                                                                </option>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                                <option value="3">Unknown</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" id="hospitalization_ncd_hides">
                                                <div class="row" id="hospitalization_year">
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <?php if ($_GET['seq'] == 1) { ?>
                                                                    <label>If yes , Number of hospitalizations in past 12 Months:</label>
                                                                <?php } else { ?>
                                                                    <label>If yes , Number of hospitalizations since last Visit:</label>
                                                                <?php } ?>
                                                                <input type="number" name="hospitalization_year" min="0" max="1000" class="form-control" value="<?php if ($hospitalization_details['hospitalization_year']) {
                                                                                                                                                                    print_r($hospitalization_details['hospitalization_year']);
                                                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <?php if ($_GET['seq'] == 1) { ?>
                                                                    <label>If yes , Number of hospital days in past 12 Months:</label>
                                                                <?php } else { ?>
                                                                    <label>If yes , Number of hospital days since last visit:</label>
                                                                <?php } ?>
                                                                <input type="number" name="hospitalization_day" min="0" max="1000" class="form-control" value="<?php if ($hospitalization_details['hospitalization_day']) {
                                                                                                                                                                    print_r($hospitalization_details['hospitalization_day']);
                                                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row-form clearfix">

                                                    <table id="myTable" class=" table order-list">
                                                        <thead>
                                                            <tr>
                                                                <th> Admission Date </th>
                                                                <th> Admission Reason </th>
                                                                <th> Discharge Diagnosis </th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($override->getNews('hospitalization_table', 'patient_id', $_GET['cid'], 'status', 1) as $hospitalization_table) { ?>

                                                                <tr>
                                                                    <td class="col-sm-4">
                                                                        <input class="form-control" name="admission_date[]" id="admission_date[]" value="<?php if ($hospitalization_table['admission_date']) {
                                                                                                                                                                print_r($hospitalization_table['admission_date']);
                                                                                                                                                            }  ?>">
                                                                    </td>
                                                                    <td class="col-sm-4">
                                                                        <input class="form-control" name="admission_reason[]" id="admission_reason[]" value="<?php if ($hospitalization_table['admission_reason']) {
                                                                                                                                                                    print_r($hospitalization_table['admission_reason']);
                                                                                                                                                                }  ?>">
                                                                    </td>
                                                                    <td class="col-sm-3">
                                                                        <input class="form-control" name="discharge_diagnosis[]" value='<?php if ($hospitalization_table['discharge_diagnosis']) {
                                                                                                                                            print_r($hospitalization_table['discharge_diagnosis']);
                                                                                                                                        }  ?>'>
                                                                    </td>
                                                                    <td>
                                                                        <input type="button" class="ibtnDel btn btn-md btn-warning" value="Remove">
                                                                    </td>
                                                                    <td>
                                                                        <a href="#delete_admission<?= $hospitalization_table['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                                    </td>
                                                                </tr>

                                                                <div class="modal fade" id="delete_admission<?= $hospitalization_table['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <form method="post">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                                    <h4>Delete this ospitalization details </h4>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <strong style="font-weight: bold;color: red">
                                                                                        <p>Are you sure you want to delete this hospitalization details ?</p>
                                                                                    </strong>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <input type="hidden" name="id" value="<?= $hospitalization_table['id'] ?>">
                                                                                    <input type="submit" name="delete_admin" value="Delete" class="btn btn-danger">
                                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                    <input type="button" class="btn btn-lg btn-block " id="addrow" value="Add Row" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <a href='info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>' class="btn btn-default">Back</a>
                                            <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>

                                                <input type="submit" name="add_hospitalization_details" value="Submit" class="btn btn-primary">
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container-fluid -->
                </section>
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 18) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>TREATMMENT PLAN</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">TREATMMENT PLAN</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php
                            $treatment_plan = $override->get3('treatment_plan', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];
                            $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                            $category = $override->get('main_diagnosis', 'patient_id', $_GET['cid'])[0];
                            $cat = '';

                            if ($category['cardiac'] == 1) {
                                $cat = 'Cardiac';
                            } elseif ($category['diabetes'] == 1) {
                                $cat = 'Diabetes';
                            } elseif ($category['sickle_cell'] == 1) {
                                $cat = 'Sickle cell';
                            } else {
                                $cat = 'Not Diagnosed';
                            }


                            if ($patient['gender'] == 1) {
                                $gender = 'Male';
                            } elseif ($patient['gender'] == 2) {
                                $gender = 'Female';
                            }

                            $name = 'Patient ID: ' . $patient['study_id'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) { ?>
                                            <h3 class="card-title">Medications ( Cardiac )</h3>
                                        <?php } ?>

                                        <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) { ?>
                                            <h3 class="card-title">Medications ( Diabetes )</h3>
                                        <?php } ?>

                                        <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>
                                            <h3 class="card-title">Medications ( Sickle Cell )</h3>
                                        <?php } ?>
                                        <!-- <h3 class="card-title">
                                            <strong style="font-size: larger">
                                                <?= $name ?>
                                            </strong>
                                        </h3> -->
                                    </div>
                                    <!-- Content Header (Page header) -->
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Patient ID:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['study_id']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Age:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['age']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Gender</a></li>
                                                        <li class="breadcrumb-item active"><?= $gender; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Type</a></li>
                                                        <li class="breadcrumb-item active"><?= $cat; ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Date:</label>
                                                            <input class="form-control" type="date" name="visit_date" id="visit_date" value="<?php if ($treatment_plan['visit_date']) {
                                                                                                                                                    print_r($treatment_plan['visit_date']);
                                                                                                                                                }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="row-form clearfix">

                                                <table id="medication_list" class="table order-list">
                                                    <thead>
                                                        <tr>
                                                            <th> Medication name </th>
                                                            <th> Action </th>
                                                            <th width="10%"> Dose </th>
                                                            <th width="10%"> Units </th>
                                                            <th width="20%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($override->getNews('medication_treatments', 'patient_id', $_GET['cid'], 'status', 1) as $treatment) { ?>

                                                            <tr>
                                                                <td>
                                                                    <input type="text" class="form-control" name="medication_type[]" id="medication_type[]" placeholder="Type medications name..." value="<?php if ($treatment['medication_type']) {
                                                                                                                                                                                                                print_r($treatment['medication_type']);
                                                                                                                                                                                                            }  ?>">
                                                                </td>
                                                                <td>
                                                                    <select name="medication_action[]" class="form-control" id="medication_action[]" style="width: 80%;" required>
                                                                        <option value="<?= $treatment['medication_action'] ?>"><?php if ($treatment) {
                                                                                                                                    if ($treatment['medication_action'] == 1) {
                                                                                                                                        echo 'Continue';
                                                                                                                                    } elseif ($treatment['medication_action'] == 2) {
                                                                                                                                        echo 'Start';
                                                                                                                                    } elseif ($treatment['medication_action'] == 3) {
                                                                                                                                        echo 'Stop';
                                                                                                                                    } elseif ($treatment['medication_action'] == 4) {
                                                                                                                                        echo 'Not Eligible';
                                                                                                                                    }
                                                                                                                                } else {
                                                                                                                                    echo 'Select';
                                                                                                                                } ?>
                                                                        </option>
                                                                        <option value="1">Continue</option>
                                                                        <option value="2">Start</option>
                                                                        <option value="3">Stop</option>
                                                                        <option value="4">Not Eligible</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" name="medication_dose[]" style="width: 50%;" value='<?php if ($treatment['medication_dose']) {
                                                                                                                                                                    print_r($treatment['medication_dose']);
                                                                                                                                                                }  ?>'>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" name="medication_units[]" style="width: 50%;" value='<?php if ($treatment['medication_units']) {
                                                                                                                                                                        print_r($treatment['medication_units']);
                                                                                                                                                                    }  ?>'>
                                                                </td>
                                                                <td>
                                                                    <input type="button" class="ibtnDel2 btn btn-md btn-warning" value="Remove">
                                                                    <a href="#delete_med<?= $treatment['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                                </td>
                                                            </tr>
                                                            <div class="modal fade" id="delete_med<?= $treatment['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <form method="post">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                                <h4>Delete this Medication</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <strong style="font-weight: bold;color: red">
                                                                                    <p>Are you sure you want to delete this Medication ?</p>
                                                                                </strong>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <input type="hidden" name="id" value="<?= $treatment['id'] ?>">
                                                                                <input type="submit" name="delete_med" value="Delete" class="btn btn-danger">
                                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <input type="button" class="btn btn-lg btn-block " id="addrow2" value="Add Row" />
                                            </div>


                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) { ?>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card card-warning">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Basal Insulin</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Changed?</label>
                                                                <select name="basal_changed" class="form-control" id="basal_changed" style="width: 100%;" onchange="checkQuestionValue1('basal_changed','basal_changed_hides')">
                                                                    <option value="<?= $treatment_plan['basal_changed'] ?>"><?php if ($treatment_plan) {
                                                                                                                                if ($treatment_plan['basal_changed'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($treatment_plan['basal_changed'] == 2) {
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

                                                    <div class="col-sm-8" id="basal_changed_hides">
                                                        <div class="col-sm-4">
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <label>Units in am:</label>
                                                                    <input type="text" class="form-control" name="basal_am2" id="basal_am2" value="<?php if ($treatment_plan['basal_am2']) {
                                                                                                                                                        print_r($treatment_plan['basal_am2']);
                                                                                                                                                    }  ?>" />

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <label>Units in pm:</label>
                                                                    <input type="text" class="form-control" name="basal_pm2" id="basal_pm2" value="<?php if ($treatment_plan['basal_pm2']) {
                                                                                                                                                        print_r($treatment_plan['basal_pm2']);
                                                                                                                                                    }  ?>" />

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card card-warning">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Prandial Insulin</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Changed?</label>
                                                                <select name="prandial_changed" class="form-control" id="prandial_changed" style="width: 100%;" onchange="checkQuestionValue1('prandial_changed','prandial_changed_hides')">
                                                                    <option value="<?= $treatment_plan['prandial_changed'] ?>"><?php if ($treatment_plan) {
                                                                                                                                    if ($treatment_plan['prandial_changed'] == 1) {
                                                                                                                                        echo 'Yes';
                                                                                                                                    } elseif ($treatment_plan['prandial_changed'] == 2) {
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

                                                    <div class="col-sm-8" id="prandial_changed_hides">

                                                        <div class="col-sm-3">
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <label>Units in am :</label>
                                                                    <input type="text" name="prandial_am2" class="form-control" id="prandial_am2" value="<?php if ($treatment_plan['prandial_am2']) {
                                                                                                                                                                print_r($treatment_plan['prandial_am2']);
                                                                                                                                                            }  ?>" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <label>Units at lunch :</label>
                                                                    <input type="text" name="prandial_lunch2" class="form-control" id="prandial_lunch2" value="<?php if ($treatment_plan['prandial_lunch2']) {
                                                                                                                                                                    print_r($treatment_plan['prandial_lunch2']);
                                                                                                                                                                }  ?>" />

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3">
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <label>Units in pm :</label>
                                                                    <input type="text" name="prandial_pm2" class="form-control" id="prandial_pm2" value="<?php if ($treatment_plan['prandial_pm2']) {
                                                                                                                                                                print_r($treatment_plan['prandial_pm2']);
                                                                                                                                                            }  ?>" />

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            <?php } ?>


                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) { ?>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card card-warning">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Diet and Fluid restriction</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Salt:</label>
                                                                <select name="salt" id="salt" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $treatment_plan['salt'] ?>"><?php if ($treatment_plan) {
                                                                                                                        if ($treatment_plan['salt'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($treatment_plan['salt'] == 2) {
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

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Fluid:</label>
                                                                <select name="fluid" id="fluid" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $treatment_plan['salt'] ?>"><?php if ($treatment_plan) {
                                                                                                                        if ($treatment_plan['salt'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($treatment_plan['salt'] == 2) {
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

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Other Restriction ?:</label>
                                                                <select name="restriction_other" id="restriction_other" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('restriction_other','restriction_specify')">
                                                                    <option value="<?= $treatment_plan['restriction_other'] ?>"><?php if ($treatment_plan) {
                                                                                                                                    if ($treatment_plan['restriction_other'] == 1) {
                                                                                                                                        echo 'Yes';
                                                                                                                                    } elseif ($treatment_plan['restriction_other'] == 2) {
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

                                                    <div class="col-sm-3 hidden" id="restriction_specify">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Other Restriction specify:</label>
                                                                <input type="text" name="restriction_specify" class="form-control" value="<?php if ($treatment_plan['restriction_specify']) {
                                                                                                                                                print_r($treatment_plan['restriction_specify']);
                                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            <?php } ?>


                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card card-warning">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Vaccination</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Vaccination needed?:</label>
                                                                <select name="vaccination" id="vaccination" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('vaccination','vaccination_specify')">
                                                                    <option value="<?= $treatment_plan['vaccination'] ?>"><?php if ($treatment_plan) {
                                                                                                                                if ($treatment_plan['vaccination'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($treatment_plan['vaccination'] == 2) {
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

                                                    <div class="col-sm-8 hidden" id="vaccination_specify">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Which::</label>
                                                                <input style="width: 100%;" type="text" name="vaccination_specify" class="form-control" value="<?php if ($treatment_plan['vaccination_specify']) {
                                                                                                                                                                    print_r($treatment_plan['vaccination_specify']);
                                                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card card-warning">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Transfusions</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Transfusion needed today?</label>
                                                                <select name="transfusion_needed" id="transfusion_needed" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('transfusion_needed','transfusion_units')">
                                                                    <option value="<?= $treatment_plan['transfusion_needed'] ?>"><?php if ($treatment_plan) {
                                                                                                                                        if ($treatment_plan['transfusion_needed'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($treatment_plan['transfusion_needed'] == 2) {
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

                                                    <div class="col-sm-8 hidden" id="transfusion_units">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label># units::</label>
                                                                <input style="width: 100%;" type="text" name="transfusion_units" class="form-control" value="<?php if ($treatment_plan['transfusion_units']) {
                                                                                                                                                                    print_r($treatment_plan['transfusion_units']);
                                                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card card-warning">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Family Education and counselling</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Diet?:</label>
                                                                <select name="diet" id="diet" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $treatment_plan['diet'] ?>"><?php if ($treatment_plan) {
                                                                                                                        if ($treatment_plan['diet'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($treatment_plan['diet'] == 2) {
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

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Hydration?:</label>
                                                                <select name="hydration" id="hydration" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $treatment_plan['hydration'] ?>"><?php if ($treatment_plan) {
                                                                                                                            if ($treatment_plan['hydration'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($treatment_plan['hydration'] == 2) {
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

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Acute Symptoms?:</label>
                                                                <select name="acute_symptoms" id="acute_symptoms" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $treatment_plan['acute_symptoms'] ?>"><?php if ($treatment_plan) {
                                                                                                                                    if ($treatment_plan['acute_symptoms'] == 1) {
                                                                                                                                        echo 'Yes';
                                                                                                                                    } elseif ($treatment_plan['acute_symptoms'] == 2) {
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

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Fever ?:</label>
                                                                <select name="fever" id="fever" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $treatment_plan['fever'] ?>"><?php if ($treatment_plan) {
                                                                                                                        if ($treatment_plan['fever'] == 1) {
                                                                                                                            echo 'Yes';
                                                                                                                        } elseif ($treatment_plan['fever'] == 2) {
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


                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Life style :</label>
                                                                <select name="life_style" id="life_style" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $treatment_plan['life_style'] ?>"><?php if ($treatment_plan) {
                                                                                                                                if ($treatment_plan['life_style'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($treatment_plan['life_style'] == 2) {
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

                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Misconception:</label>
                                                                <select name="misconception" id="misconception" class="form-control" style="width: 100%;">
                                                                    <option value="<?= $treatment_plan['misconception'] ?>"><?php if ($treatment_plan) {
                                                                                                                                if ($treatment_plan['misconception'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($treatment_plan['misconception'] == 2) {
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


                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Any Other Support?:</label>
                                                                <select name="other_support" id="other_support" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('other_support','support_specify')">
                                                                    <option value="<?= $treatment_plan['other_support'] ?>"><?php if ($treatment_plan) {
                                                                                                                                if ($treatment_plan['other_support'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($treatment_plan['other_support'] == 2) {
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

                                                    <div class="col-sm-9 hidden" id="support_specify">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Specify:</label>
                                                                <input style="width: 100%;" type="text" name="support_specify" class="form-control" value="<?php if ($treatment_plan['support_specify']) {
                                                                                                                                                                print_r($treatment_plan['support_specify']);
                                                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php } ?>


                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card card-warning">
                                                        <div class="card-header">
                                                            <h3 class="card-title">Support</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Social support provided?:</label>

                                                            <select name="social_support" id="social_support" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('social_support','social_support_type')" required>
                                                                <option value="<?= $treatment_plan['social_support'] ?>"><?php if ($treatment_plan) {
                                                                                                                                if ($treatment_plan['social_support'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($treatment_plan['social_support'] == 2) {
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

                                                <div class="col-sm-6 hidden" id="social_support_type">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Type:</label>
                                                            <input style="width: 100%;" type="text" class="form-control" name="social_support_type" value="<?php if ($treatment_plan['social_support_type']) {
                                                                                                                                                                print_r($treatment_plan['social_support_type']);
                                                                                                                                                            }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) { ?>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Cardiology referral:</label>
                                                                <select name="cardiology" id="cardiology" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('cardiology','completed1')" required>
                                                                    <option value="<?= $treatment_plan['cardiology'] ?>"><?php if ($treatment_plan) {
                                                                                                                                if ($treatment_plan['cardiology'] == 1) {
                                                                                                                                    echo 'Yes';
                                                                                                                                } elseif ($treatment_plan['cardiology'] == 2) {
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

                                                    <div class="col-sm-3" id="completed_hidden">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Completed ?:</label>
                                                                <select name="completed" id="completed" class="form-control" style="width: 100%;" onchange="checkQuestionValue2('completed','cardiology_date','cardiology_reason')">
                                                                    <option value="<?= $treatment_plan['completed'] ?>"><?php if ($treatment_plan) {
                                                                                                                            if ($treatment_plan['completed'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($treatment_plan['completed'] == 2) {
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

                                                    <div class="col-sm-3" id="cardiology_date">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Date Completed:</label>
                                                                <input style="width: 100%;" type="text" name="cardiology_date" class="form-control" value="<?php if ($treatment_plan['cardiology_date']) {
                                                                                                                                                                print_r($treatment_plan['cardiology_date']);
                                                                                                                                                            }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-3" id="cardiology_reason">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>If no, why ?:</label>
                                                                <input style="width: 100%;" type="text" class="form-control" name="cardiology_reason" value="<?php if ($treatment_plan['cardiology_reason']) {
                                                                                                                                                                    print_r($treatment_plan['cardiology_reason']);
                                                                                                                                                                }  ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row">

                                                    <div class="col-sm-12">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Awaiting surgery:</label>
                                                                <select name="awaiting_surgery" class="form-control" id="awaiting_surgery" style="width: 100%;" required>
                                                                    <option value="<?= $treatment_plan['awaiting_surgery'] ?>"><?php if ($treatment_plan) {
                                                                                                                                    if ($treatment_plan['awaiting_surgery'] == 1) {
                                                                                                                                        echo 'Yes';
                                                                                                                                    } elseif ($treatment_plan['awaiting_surgery'] == 2) {
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


                                            <?php } ?>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Any new referrals provided?:</label>
                                                            <select name="new_referrals" id="new_referrals" class="form-control" style="width: 100%;" onchange="checkQuestionValue1('new_referrals','new_referrals_type')" required>
                                                                <option value="<?= $treatment_plan['new_referrals'] ?>"><?php if ($treatment_plan) {
                                                                                                                            if ($treatment_plan['new_referrals'] == 1) {
                                                                                                                                echo 'Yes';
                                                                                                                            } elseif ($treatment_plan['new_referrals'] == 2) {
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

                                                <div class="col-sm-6" id="new_referrals_type">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Type:</label>
                                                            <input style="width: 100%;" type="text" class="form-control" name="new_referrals_type" value="<?php if ($treatment_plan['new_referrals_type']) {
                                                                                                                                                                print_r($treatment_plan['new_referrals_type']);
                                                                                                                                                            }  ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>




                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Notes:</label>
                                                            <textarea name="medication_notes" class="form-control" rows="4">
                                                        <?php if ($treatment_plan['medication_notes']) {
                                                            print_r($treatment_plan['medication_notes']);
                                                        }  ?>
                                                    </textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <a href='info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>' class="btn btn-default">Back</a>
                                            <?php if ($user->data()->position == 1 || $user->data()->position == 3 || $user->data()->position == 4 || $user->data()->position == 5) { ?>

                                                <input type="submit" name="add_treatment_plan" value="Submit" class="btn btn-primary">
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container-fluid -->
                </section>
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 19) { ?>

        <?php } elseif ($_GET['id'] == 20) { ?>
        <?php } elseif ($_GET['id'] == 21) { ?>
        <?php } elseif ($_GET['id'] == 22) { ?>
        <?php } elseif ($_GET['id'] == 23) { ?>
        <?php } elseif ($_GET['id'] == 24) { ?>
        <?php } elseif ($_GET['id'] == 25) { ?>
        <?php } elseif ($_GET['id'] == 26) { ?>
        <?php } elseif ($_GET['id'] == 27) { ?>
        <?php } elseif ($_GET['id'] == 28 && $user->data()->position == 1) { ?>

        <?php } ?>

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
    <!-- <script src="../../dist/js/demo.js"></script> -->
    <!-- Page specific script -->

    <!-- Vital Signs Js -->
    <script src="myjs/add/clients.js"></script>

    <!-- demographic Js -->
    <script src="myjs/add/demographic.js"></script>


    <!-- Vital Signs Js -->
    <script src="myjs/add/vital.js"></script>

    <!-- Medications Js -->
    <script src="myjs/add/medications/basal_changed.js"></script>
    <script src="myjs/add/medications/prandial_changed.js"></script>
    <script src="myjs/add/medications/fluid_restriction.js"></script>
    <script src="myjs/add/medications/support.js"></script>
    <script src="myjs/add/medications/cardiology.js"></script>
    <script src="myjs/add/medications/referrals.js"></script>
    <script src="myjs/add/medications/social_support.js"></script>
    <script src="myjs/add/medications/transfusion.js"></script>
    <script src="myjs/add/medications/vaccination.js"></script>
    <script src="myjs/add/medications/completed.js"></script>
    <script src="myjs/add/medications/medication.js"></script>


    <!-- History Js -->

    <script src="myjs/add/history/cardiovascular.js"></script>
    <script src="myjs/add/history/retinopathy.js"></script>
    <script src="myjs/add/history/alcohol.js"></script>
    <script src="myjs/add/history/alcohol_type.js"></script>
    <script src="myjs/add/history/art.js"></script>
    <script src="myjs/add/history/blood_transfusion.js"></script>
    <script src="myjs/add/history/hepatitis.js"></script>
    <script src="myjs/add/history/history_other.js"></script>
    <script src="myjs/add/history/hiv.js"></script>
    <script src="myjs/add/history/neuropathy.js"></script>
    <script src="myjs/add/history/other_complication.js"></script>
    <script src="myjs/add/history/pvd.js"></script>
    <script src="myjs/add/history/renal.js"></script>
    <script src="myjs/add/history/sexual_dysfunction.js"></script>
    <script src="myjs/add/history/smoking.js"></script>
    <script src="myjs/add/history/stroke_tia.js"></script>
    <script src="myjs/add/history/surgery.js"></script>
    <script src="myjs/add/history/surgery_type.js"></script>
    <script src="myjs/add/history/tb.js"></script>


    <!-- Symptoms Js -->


    <script src="myjs/add/symptoms/abnorminal_pain.js"></script>
    <script src="myjs/add/symptoms/chest_pain.js"></script>
    <script src="myjs/add/symptoms/foot_exam.js"></script>
    <script src="myjs/add/symptoms/foot_exam_finding.js"></script>
    <script src="myjs/add/symptoms/headache.js"></script>
    <script src="myjs/add/symptoms/hypoglycemia_severe.js"></script>
    <script src="myjs/add/symptoms/joints.js"></script>
    <script src="myjs/add/symptoms/lower_arms.js"></script>
    <script src="myjs/add/symptoms/lungs.js"></script>
    <script src="myjs/add/symptoms/other_pain.js"></script>
    <script src="myjs/add/symptoms/other_symptoms.js"></script>
    <script src="myjs/add/symptoms/upper_arms.js"></script>
    <script src="myjs/add/symptoms/waist.js"></script>

    <!-- hospitalization_details Js -->

    <script src="myjs/add/hospitalization_details/hospitalization_ncd.js"></script>
    <script src="myjs/add/symptoms"></script>
    <script src="myjs/add/symptoms"></script>
    <script src="myjs/add/symptoms"></script>
    <script src="myjs/add/symptoms"></script>
    <script src="myjs/add/symptoms"></script>
    <script src="myjs/add/symptoms"></script>
    <script src="myjs/add/symptoms"></script>
    <script src="myjs/add/symptoms"></script>





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