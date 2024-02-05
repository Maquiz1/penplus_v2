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
    if (Input::exists('post')) {
        $validate = new validate();
        if (Input::get('edit_position')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('position', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Position Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_staff')) {
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
                'phone_number' => array(
                    'required' => true,
                ),
                'email_address' => array(),
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
                    $user->updateRecord('user', array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'position' => Input::get('position'),
                        'phone_number' => Input::get('phone_number'),
                        'email_address' => Input::get('email_address'),
                        'accessLevel' => $accessLevel,
                        'power' => Input::get('power'),
                        'user_id' => $user->data()->id,
                    ), Input::get('id'));

                    $successMessage = 'Account Updated Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('reset_pass')) {
            $salt = $random->get_rand_alphanumeric(32);
            $password = '12345678';
            $user->updateRecord('user', array(
                'password' => Hash::make($password, $salt),
                'salt' => $salt,
            ), Input::get('id'));
            $successMessage = 'Password Reset Successful';
        } elseif (Input::get('unlock_account')) {
            $user->updateRecord('user', array(
                'count' => 0,
            ), Input::get('id'));
            $successMessage = 'Account Unlock Successful';
        } elseif (Input::get('delete_staff')) {
            $user->updateRecord('user', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'User Deleted Successful';
        } elseif (Input::get('delete_client')) {
            $user->updateRecord('clients', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'User Deleted Successful';
        } elseif (Input::get('edit_study')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'code' => array(
                    'required' => true,
                ),
                'sample_size' => array(
                    'required' => true,
                ),
                'start_date' => array(
                    'required' => true,
                ),
                'end_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('study', array(
                        'name' => Input::get('name'),
                        'code' => Input::get('code'),
                        'sample_size' => Input::get('sample_size'),
                        'start_date' => Input::get('start_date'),
                        'end_date' => Input::get('end_date'),
                    ), Input::get('id'));
                    $successMessage = 'Study Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_site')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('site', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Site Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_client')) {
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
        } elseif (Input::get('add_screening')) {
            $validate = $validate->check($_POST, array(
                'screening_date' => array(
                    'required' => true,
                ),
                // 'conset_date' => array(
                //     'required' => true,
                // ),
                'ncd' => array(
                    'required' => true,
                ),
                'consent' => array(
                    'required' => true,
                ),
                'residence' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $eligibility = 0;
                    if ((Input::get('consent') == 1 && Input::get('residence') == 1) && (Input::get('ncd') == 1)) {
                        $eligibility = 1;
                    }

                    $doctor_confirm = 0;
                    if ((Input::get('consent') == 1 && Input::get('residence') == 1)) {
                        if (Input::get('ncd') == 1) {
                            $doctor_confirm = 2;
                        }
                    }

                    if ($override->get('screening', 'patient_id', Input::get('id'))) {
                        $user->updateRecord('screening', array(
                            'screening_date' => Input::get('screening_date'),
                            'conset_date' => Input::get('conset_date'),
                            'ncd' => Input::get('ncd'),
                            'consent' => Input::get('consent'),
                            'residence' => Input::get('residence'),
                            'created_on' => date('Y-m-d'),
                            'patient_id' => Input::get('id'),
                            'staff_id' => $user->data()->id,
                            'eligibility' => $eligibility,
                            'doctor_confirm' => $doctor_confirm,
                            'status' => 1,
                        ), Input::get('scrrening_id'));

                        $visit = $override->getNews('visit', 'client_id', Input::get('id'), 'seq_no', 0, 'visit_name', 'Screening')[0];

                        $user->updateRecord('visit', array(
                            'expected_date' => Input::get('screening_date'),
                            'visit_date' => Input::get('screening_date'),
                        ), $visit['id']);
                    } else {
                        $user->createRecord('screening', array(
                            'screening_date' => Input::get('screening_date'),
                            'conset_date' => Input::get('conset_date'),
                            'consent' => Input::get('consent'),
                            'ncd' => Input::get('ncd'),
                            'study_id' => '',
                            'residence' => Input::get('residence'),
                            'created_on' => date('Y-m-d'),
                            'patient_id' => Input::get('id'),
                            'staff_id' => $user->data()->id,
                            'eligibility' => $eligibility,
                            'status' => 1,
                            'doctor_confirm' => $doctor_confirm,
                            'site_id' => $user->data()->site_id,
                        ));

                        $user->createRecord('visit', array(
                            'study_id' => '',
                            'visit_name' => 'Screening',
                            'visit_code' => 'SV',
                            'visit_day' => 'Day 0',
                            'expected_date' => Input::get('screening_date'),
                            'visit_date' => Input::get('screening_date'),
                            'visit_window' => 0,
                            'status' => 1,
                            'seq_no' => 0,
                            'client_id' => Input::get('id'),
                            'created_on' => date('Y-m-d'),
                            'reasons' => '',
                            'visit_status' => 1,
                            'site_id' => $user->data()->site_id,
                        ));
                    }

                    $user->updateRecord('clients', array(
                        'eligible' => $eligibility,
                        // 'enrolled' => $eligibility,
                        'screened' => 1,
                    ), Input::get('id'));

                    $successMessage = 'Patient Successful Screened';

                    if ($eligibility) {
                        Redirect::to('info.php?id=3&status=2');
                    } else {
                        // Redirect::to('info.php?id=3&status=' . $_GET['status']);
                        Redirect::to('add_lab.php?cid=' . Input::get('id') . '&status=1');
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_Enrollment')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $client_study = $override->getNews('clients', 'id', Input::get('id'), 'status', 1)[0];
                $std_id = $override->getNews('study_id', 'site_id', $user->data()->site_id, 'status', 0)[0];
                $screening_id = $override->getNews('screening', 'patient_id', Input::get('id'), 'status', 1)[0];
                $visit_id = $override->get('visit', 'client_id', Input::get('id'))[0];
                $last_visit = $override->getlastRow('visit', 'client_id', Input::get('id'), 'id')[0];
                $visit = $override->get3('visit', 'client_id', Input::get('id'), 'seq_no', 1, 'visit_name', Input::get('visit_name'));
                $visit_id = $override->get3('visit', 'client_id', Input::get('id'), 'seq_no', 1, 'visit_name', Input::get('visit_name'))[0];

                if (!$client_study['study_id']) {
                    $study_id = $std_id['study_id'];
                } else {
                    $study_id = $client_study['study_id'];
                }

                if (Input::get('visit_name') == 'Registration Visit') {
                    $visit_code = 'RV';
                } elseif (Input::get('visit_name') == 'Screening Visit') {
                    $visit_code = 'SV';
                } elseif (Input::get('visit_name') == 'Enrollment Visit') {
                    $visit_code = 'EV';
                } elseif (Input::get('visit_name') == 'Follow Up Visit') {
                    $visit_code = 'FV';
                } elseif (Input::get('visit_name') == 'Study Termination Visit') {
                    $visit_code = 'TV';
                } elseif (Input::get('visit_name') == 'Unschedule Visit') {
                    $visit_code = 'UV';
                }

                if ($visit) {
                    $user->updateRecord('visit', array('expected_date' => Input::get('visit_date'), 'reasons' => Input::get('reasons')), $visit_id['id']);

                    // $errorMessage = 'Visit with the same Date ana Name already exists for this Client';
                } else {
                    $user->createRecord('visit', array(
                        'study_id' => $study_id,
                        'visit_name' => Input::get('visit_name'),
                        'visit_code' => $visit_code,
                        'visit_day' => 'Day 1',
                        'expected_date' => Input::get('visit_date'),
                        'visit_date' => '',
                        'visit_window' => 0,
                        'status' => 0,
                        'client_id' => Input::get('id'),
                        'created_on' => date('Y-m-d'),
                        'seq_no' => 1,
                        'reasons' => Input::get('reasons'),
                        'visit_status' => 1,
                        'site_id' => $user->data()->site_id,
                    ));

                    if (!$client_study['study_id']) {
                        $user->updateRecord('screening', array('study_id' => $std_id['study_id']), $screening_id['id']);
                        $user->updateRecord('clients', array('study_id' => $std_id['study_id'], 'enrolled' => 1), Input::get('id'));
                        $user->updateRecord('study_id', array('status' => 1, 'client_id' => Input::get('id')), $std_id['id']);
                    } else {
                        $user->updateRecord('screening', array('study_id' => $client_study['study_id']), $screening_id['id']);
                        $user->updateRecord('clients', array('study_id' => $client_study['study_id'], 'enrolled' => 1), Input::get('id'));
                    }

                    $successMessage = 'Enrollment  Added Successful';
                    Redirect::to('info.php?id=3&status=3');
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_Schedule')) {
            $validate = $validate->check($_POST, array(
                'expected_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $client_study = $override->getNews('clients', 'id', $_GET['cid'], 'status', 1)[0];
                $std_id = $override->getNews('study_id', 'site_id', $user->data()->site_id, 'status', 0)[0];
                $screening_id = $override->getNews('screening', 'patient_id', $_GET['cid'], 'status', 1)[0];
                $visit_id = $override->get('visit', 'client_id', $_GET['cid'])[0];
                $last_visit = $override->getlastRow('visit', 'client_id', $_GET['cid'], 'id')[0];
                $expected_date = $override->getNews('visit', 'expected_date', Input::get('expected_date'), 'client_id', $_GET['cid'])[0];

                $sq = $last_visit['seq_no'] + 1;
                $visit_day = 'Day ' . $sq;

                if (!$client_study['study_id']) {
                    $study_id = $std_id['study_id'];
                } else {
                    $study_id = $client_study['study_id'];
                }

                if (Input::get('visit_name') == 'Registration Visit') {
                    $visit_code = 'RV';
                } elseif (Input::get('visit_name') == 'Screening Visit') {
                    $visit_code = 'SV';
                } elseif (Input::get('visit_name') == 'Enrollment Visit') {
                    $visit_code = 'EV';
                } elseif (Input::get('visit_name') == 'Follow Up Visit') {
                    $visit_code = 'FV';
                } elseif (Input::get('visit_name') == 'Study Termination Visit') {
                    $visit_code = 'TV';
                } elseif (Input::get('visit_name') == 'Unschedule Visit') {
                    $visit_code = 'UV';
                }

                $summary = $override->getNews('visit', 'client_id', $_GET['cid'], 'seq_no', Input::get('seq_no'));

                if (Input::get('summary')) {
                    $user->updateRecord('visit', array(
                        // 'visit_name' => Input::get('visit_name'),
                        // 'visit_code' => $visit_code,
                        // 'visit_day' => $visit_day,
                        'expected_date' => Input::get('expected_date'),
                        'visit_date' => Input::get('summary_date'),
                        'summary_date' => Input::get('summary_date'),
                        'comments' => Input::get('comments'),
                        'diagnosis' => Input::get('diagnosis'),
                        'diagnosis_other' => Input::get('diagnosis_other'),
                        'outcome' => Input::get('outcome'),
                        'transfer_out' => Input::get('transfer_out'),
                        'transfer_other' => Input::get('transfer_other'),
                        'cause_death' => Input::get('cause_death'),
                        'death_other' => Input::get('death_other'),
                        'next_notes' => Input::get('next_notes'),
                        'reasons' => Input::get('reasons'),
                    ), Input::get('id'));

                    $successMessage = 'Schedule Summary  Updated Successful';
                } else {
                    $user->createRecord('visit', array(
                        'study_id' => $study_id,
                        'visit_name' => Input::get('visit_name'),
                        'visit_code' => $visit_code,
                        'visit_day' => $visit_day,
                        'expected_date' => Input::get('expected_date'),
                        'visit_date' => '',

                        'summary_date' => Input::get('summary_date'),
                        'comments' => Input::get('comments'),
                        'diagnosis' => Input::get('diagnosis'),
                        'diagnosis_other' => Input::get('diagnosis_other'),
                        'outcome' => Input::get('outcome'),
                        'transfer_out' => Input::get('transfer_out'),
                        'transfer_other' => Input::get('transfer_other'),
                        'cause_death' => Input::get('cause_death'),
                        'death_other' => Input::get('death_other'),
                        'next_notes' => Input::get('next_notes'),

                        'visit_window' => 0,
                        'status' => 0,
                        'client_id' => $_GET['cid'],
                        'created_on' => date('Y-m-d'),
                        'seq_no' => $sq,
                        'reasons' => '',
                        'visit_status' => 0,
                        'site_id' => $user->data()->site_id,
                    ));

                    $successMessage = 'Schedule Summary  Added Successful';
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_summary2')) {
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
                        ), $summary['id']);
                    } else {
                        $user->createRecord('summary', array(
                            'visit_date' => Input::get('summary_date'),
                            'study_id' => Input::get('sid'),
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
                    Redirect::to('info.php?id=7&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&seq=' . $_GET['seq']);
                    die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_visit')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('visit', array(
                        'visit_date' => Input::get('visit_date'),
                        'status' => 1,
                        'visit_status' => 0,
                        'reasons' => Input::get('reasons'),
                    ), Input::get('id'));

                    $client_id = $override->getNews('clients', 'id', Input::get('cid'), 'status', 1)[0];


                    if (Input::get('visit_name') == 'Study Termination Visit') {
                        $user->updateRecord('clients', array(
                            'end_study' => 1,
                        ), Input::get('cid'));
                    } else {
                        $user->updateRecord('clients', array(
                            'end_study' => 0,
                        ), Input::get('cid'));
                    }

                    $successMessage = 'Visit  Added Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('search_by_site')) {

            $validate = $validate->check($_POST, array(
                'site' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $url = 'info.php?id=3&sid=' . Input::get('site');
                Redirect::to($url);
                $pageError = $validate->errors();
            }
        } elseif (Input::get('clear_data')) {

            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    if (Input::get('name')) {
                        if (Input::get('name') == 'user' || Input::get('name') == 'sub_category' || Input::get('name') == 'test_list' || Input::get('name') == 'category' || Input::get('name') == 'medications' || Input::get('name') == 'site' || Input::get('name') == 'schedule' || Input::get('name') == 'study_id') {
                            $errorMessage = 'Table ' . '"' . Input::get('name') . '"' . '  can not be Cleared';
                        } else {
                            $clearData = $override->clearDataTable(Input::get('name'));
                        }
                        $successMessage = 'Table ' . '"' . Input::get('name') . '"' . ' Cleared Successfull';
                    } else {
                        $errorMessage = 'Table ' . '"' . Input::get('name') . '"' . '  can not be Found!';
                    }
                    // die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('setSiteId')) {

            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $setSiteId = $override->setSiteId('visit', 'site_id', Input::get('name'), 1);
                    $successMessage = 'Site ID Successfull';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_data')) {

            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'site' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    if (Input::get('name')) {
                        $site_id = '';
                        if (Input::get('site') == '1') {
                            $site_id = 'KNODOA';
                        } elseif (Input::get('site') == '2') {
                            $site_id = 'KARATU';
                        }

                        if (Input::get('name') == 'user' || Input::get('name') == 'sub_category' || Input::get('name') == 'test_list' || Input::get('name') == 'category' || Input::get('name') == 'medications' || Input::get('name') == 'site' || Input::get('name') == 'schedule' || Input::get('name') == 'study_id') {
                            $errorMessage = 'Table ' . '"' . Input::get('name') . '"' . '  can not be Deleted';
                        } else {
                            // $clearData = $override->deleteDataTable(Input::get('name'), Input::get('site'));
                            $deleteData = $user->deleteRecord(Input::get('name'), 'site_id', Input::get('site'));
                            $successMessage = 'Data on Table ' . '"' . Input::get('name') . 'On site "' . '"' . $site_id . '"'  . ' Deleted Successfull';
                        }
                    } else {
                        $errorMessage = 'Data on Table ' . '"' . Input::get('name') . '"' . '  can not be Found!';
                    }
                    // die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_data2')) {

            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'date2' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    if (Input::get('name')) {
                        $site_id = '';
                        if (Input::get('site') == '1') {
                            $site_id = 'KNODOA';
                        } elseif (Input::get('site') == '2') {
                            $site_id = 'KARATU';
                        }

                        if (Input::get('name') == 'user' || Input::get('name') == 'sub_category' || Input::get('name') == 'test_list' || Input::get('name') == 'category' || Input::get('name') == 'medications' || Input::get('name') == 'site' || Input::get('name') == 'schedule' || Input::get('name') == 'study_id') {
                            $errorMessage = 'Table ' . '"' . Input::get('name') . '"' . '  can not be Deleted';
                        } else {
                            // $clearData = $override->deleteDataTable(Input::get('name'), Input::get('site'));
                            $deleteData = $user->deleteRecord(Input::get('name'), 'created_on', Input::get('date2'));
                            $successMessage = 'Data on Table ' . '"' . Input::get('name') . ' On site "' . '"' . $site_id . '"'  . ' On date "' . '"' .  Input::get('date2') . '"'  . ' Deleted Successfull';
                        }
                    } else {
                        $errorMessage = 'Data on Table ' . '"' . Input::get('name') . '"' . '  can not be Found!';
                    }
                    // die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
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
                    if (Input::get('action') == 'edit') {
                        $user->updateRecord('medications', array(
                            'name' => Input::get('name'),
                            'cardiac' => Input::get('cardiac'),
                            'diabetes' => Input::get('diabetes'),
                            'sickle_cell' => Input::get('sickle_cell'),
                            'status' => 1,
                        ), Input::get('id'));
                        $successMessage = 'Medications Successful Updated';
                    } elseif (Input::get('action') == 'add') {

                        $medications = $override->get('medications', 'name', Input::get('name'));
                        if ($medications) {
                            $errorMessage = 'Medications Already  Available Please Update instead!';
                        } else {
                            $user->createRecord('medications', array(
                                'name' => Input::get('name'),
                                'cardiac' => Input::get('cardiac'),
                                'diabetes' => Input::get('diabetes'),
                                'sickle_cell' => Input::get('sickle_cell'),
                                'status' => 1,
                            ));
                            $successMessage = 'Medications Successful Added';
                        }
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('DoctorConfirm')) {

            $validate = $validate->check($_POST, array(
                // 'name' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $setSiteId = $override->DoctorConfirm('screening', 'doctor_confirm', Input::get('name'), 1);
                    $successMessage = 'Doctor Confirm Successfull Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_medication')) {
            $user->updateRecord('medications', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = Input::get('name') . ' - ' . 'Medications Deleted Successful';
        } elseif (Input::get('delete_batch')) {
            $user->updateRecord('batch', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = Input::get('name') . ' - ' . 'Medications Deleted Successful';
        }



        if ($_GET['id'] == 6) {
            $data = null;
            $filename = null;
            if (Input::get('clients')) {
                $data = $override->get('clients', 'status', 1);
                $filename = 'Registartion Data';
            } elseif (Input::get('screening')) {
                $data = $override->get('screening', 'status', 1);
                $filename = 'screening Data';
            } elseif (Input::get('demographic')) {
                $data = $override->get('demographic', 'status', 1);
                $filename = 'Demographic Data';
            } elseif (Input::get('vital')) {
                $data = $override->getData('vital');
                $filename = 'Vitals Sign Data';
            } elseif (Input::get('main_diagnosis')) {
                $data = $override->getData('main_diagnosis');
                $filename = 'Pateint Category Data';
            } elseif (Input::get('history')) {
                $data = $override->getData('history');
                $filename = 'Patient & Family History & Complication';
            } elseif (Input::get('symptoms')) {
                $data = $override->getData('symptoms');
                $filename = 'Symtom & Exam';
            } elseif (Input::get('diagnosis')) {
                $data = $override->getData('cardiac');
                $filename = 'Main diagnosis 3 ( Cardiac )';
            } elseif (Input::get('diabetic')) {
                $data = $override->getData('diabetic');
                $filename = 'Main diagnosis 3 ( Diabetic )';
            } elseif (Input::get('sickle_cell_list')) {
                $data = $override->get('sickle_cell_status_table', 'status', 1);
                $filename = 'sickle cell status Data';
            } elseif (Input::get('sickle_cell')) {
                $data = $override->getData('sickle_cell');
                $filename = 'Main diagnosis 3 ( Sickle Cell )';
            } elseif (Input::get('results')) {
                $data = $override->getData('results');
                $filename = 'Results Data';
            } elseif (Input::get('hospitalization')) {
                $data = $override->getData('hospitalization');
                $filename = 'Hospitalization Data';
            } elseif (Input::get('treatment_plan')) {
                $data = $override->getData('treatment_plan');
                $filename = 'Treatment Plan Data';
            } elseif (Input::get('treatment_list')) {
                $data = $override->get('medication_treatments', 'status', 1);
                $filename = 'Treatment Medication List Data';
            } elseif (Input::get('dgns_complctns_comorbdts')) {
                $data = $override->getData('dgns_complctns_comorbdts');
                $filename = 'Diagnosis, Complications, & Comorbidities Data';
            } elseif (Input::get('risks')) {
                $data = $override->getData('risks');
                $filename = 'RISK Data';
            } elseif (Input::get('hospitalization_details')) {
                $data = $override->getData('hospitalization_details');
                $filename = 'Hospitalization Details Data';
            } elseif (Input::get('hospitalization_list')) {
                $data = $override->get('hospitalization_table', 'status', 1);
                $filename = 'hospitalization List Data';
            } elseif (Input::get('lab_details')) {
                $data = $override->getData('lab_details');
                $filename = 'Lab Details Data';
            } elseif (Input::get('social_economic')) {
                $data = $override->getData('social_economic');
                $filename = 'Socioeconomic Status Data';
            } elseif (Input::get('visit')) {
                $data = $override->getData('visit');
                $filename = 'visit Schedule Data';
            } elseif (Input::get('study_id')) {
                $data = $override->getData('study_id');
                $filename = 'study_id Status Data';
            } elseif (Input::get('site')) {
                $data = $override->getData('site');
                $filename = 'Site List';
            }

            $user->exportData($data, $filename);
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

        <?php } elseif ($_GET['id'] == 3) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    if ($_GET['status'] == 1) {
                                        echo $title = 'Screening';
                                    ?>
                                    <?php
                                    } elseif ($_GET['status'] == 2) {
                                        echo $title = 'Eligibility';
                                    ?>
                                    <?php
                                    } elseif ($_GET['status'] == 3) {
                                        echo  $title = 'Enrollment';
                                    ?>
                                    <?php
                                    } elseif ($_GET['status'] == 4) {
                                        echo $title = 'Termination';
                                    ?>
                                    <?php
                                    } elseif ($_GET['status'] == 5) {
                                        echo  $title = 'Registration'; ?>
                                    <?php
                                    } ?>
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active"><?= $title; ?></li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <?php
                                if ($user->data()->power == 1) {
                                    if ($_GET['sid'] != null) {
                                        $pagNum = 0;
                                        if ($_GET['status'] == 1) {
                                            $pagNum = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $_GET['sid']);
                                        } elseif ($_GET['status'] == 2) {
                                            $pagNum = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $_GET['sid']);
                                        } elseif ($_GET['status'] == 3) {
                                            $pagNum = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $_GET['sid']);
                                        } elseif ($_GET['status'] == 4) {
                                            $pagNum = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $_GET['sid']);
                                        } elseif ($_GET['status'] == 5) {
                                            $pagNum = $override->countData('clients', 'status', 1, 'site_id', $_GET['site_id']);
                                        } elseif ($_GET['status'] == 6) {
                                            $pagNum = $override->countData2('clients', 'status', 1, 'screened', 0, 'site_id', $_GET['sid']);
                                        } elseif ($_GET['status'] == 7) {
                                            $pagNum = $override->getCount('clients', 'site_id', $_GET['sid']);
                                        } elseif ($_GET['status'] == 8) {
                                            $pagNum = $override->countData('clients', 'status', 0, 'site_id', $_GET['sid']);
                                        }
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        if ($_GET['status'] == 1) {
                                            $clients = $override->getWithLimit3('clients', 'status', 1, 'screened', 1, 'site_id', $_GET['sid'], $page, $numRec);
                                        } elseif ($_GET['status'] == 2) {
                                            $clients = $override->getWithLimit3('clients', 'status', 1, 'eligible', 1, 'site_id', $_GET['sid'], $page, $numRec);
                                        } elseif ($_GET['status'] == 3) {
                                            $clients = $override->getWithLimit3('clients', 'status', 1, 'enrolled', 1, 'site_id', $_GET['sid'], $page, $numRec);
                                        } elseif ($_GET['status'] == 4) {
                                            $clients = $override->getWithLimit3('clients', 'status', 1, 'end_study', 1, 'site_id', $_GET['sid'], $page, $numRec);
                                        } elseif ($_GET['status'] == 5) {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'site_id', $_GET['site_id'], $page, $numRec);
                                        } elseif ($_GET['status'] == 6) {
                                            $clients = $override->getWithLimit3('clients', 'status', 1, 'screened', 0, 'site_id', $_GET['sid'], $page, $numRec);
                                        } elseif ($_GET['status'] == 7) {
                                            $clients = $override->getWithLimit('clients', 'site_id', $_GET['sid'], $page, $numRec);
                                        } elseif ($_GET['status'] == 8) {
                                            $clients = $override->getWithLimit1('clients', 'status', 0, 'site_id', $_GET['sid'], $page, $numRec);
                                        }
                                    } else {
                                        $pagNum = 0;
                                        if ($_GET['status'] == 1) {
                                            $pagNum = $override->getCount1('clients', 'status', 1, 'screened', 1);
                                        } elseif ($_GET['status'] == 2) {
                                            $pagNum = $override->getCount1('clients', 'status', 1, 'eligible', 1);
                                        } elseif ($_GET['status'] == 3) {
                                            $pagNum = $override->getCount1('clients', 'status', 1, 'enrolled', 1);
                                        } elseif ($_GET['status'] == 4) {
                                            $pagNum = $override->getCount1('clients', 'status', 1, 'end_study', 1);
                                        } elseif ($_GET['status'] == 5) {
                                            $pagNum = $override->getCount('clients', 'status', 1);
                                        } elseif ($_GET['status'] == 6) {
                                            $pagNum = $override->getCount1('clients', 'status', 1, 'screened', 0);
                                        } elseif ($_GET['status'] == 7) {
                                            $clients = $override->getNo('clients');
                                        } elseif ($_GET['status'] == 8) {
                                            $pagNum = $override->getCount('clients', 'status', 0);
                                        }
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        if ($_GET['status'] == 1) {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'screened', 1, $page, $numRec);
                                        } elseif ($_GET['status'] == 2) {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'eligible', 1, $page, $numRec);
                                        } elseif ($_GET['status'] == 3) {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'enrolled', 1, $page, $numRec);
                                        } elseif ($_GET['status'] == 4) {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'end_study', 1, $page, $numRec);
                                        } elseif ($_GET['status'] == 5) {
                                            $clients = $override->getWithLimit('clients', 'status', 1, $page, $numRec);
                                        } elseif ($_GET['status'] == 6) {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'screened', 0, $page, $numRec);
                                        } elseif ($_GET['status'] == 7) {
                                            $clients = $override->getDataLimit('clients', $page, $numRec);
                                        } elseif ($_GET['status'] == 8) {
                                            $clients = $override->getWithLimit('clients', 'status', 0, $page, $numRec);
                                        }
                                    }
                                } else {

                                    $pagNum = 0;
                                    if ($_GET['status'] == 1) {
                                        $pagNum = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $user->data()->site_id);
                                    } elseif ($_GET['status'] == 2) {
                                        $pagNum = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $user->data()->site_id);
                                    } elseif ($_GET['status'] == 3) {
                                        $pagNum = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $user->data()->site_id);
                                    } elseif ($_GET['status'] == 4) {
                                        $pagNum = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $user->data()->site_id);
                                    } elseif ($_GET['status'] == 5) {
                                        $pagNum = $override->countData('clients', 'status', 1, 'site_id', $_GET['site_id']);
                                    } elseif ($_GET['status'] == 6) {
                                        $pagNum = $override->countData2('clients', 'status', 1, 'screened', 0, 'site_id', $user->data()->site_id);
                                    } elseif ($_GET['status'] == 7) {
                                        $pagNum = $override->getCount('clients', 'site_id', $user->data()->site_id);
                                    } elseif ($_GET['status'] == 8) {
                                        $pagNum = $override->countData('clients', 'status', 0, 'site_id', $user->data()->site_id);
                                    }
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    if ($_GET['status'] == 1) {
                                        $clients = $override->getWithLimit3('clients', 'status', 1, 'screened', 1, 'site_id', $user->data()->site_id, $page, $numRec);
                                    } elseif ($_GET['status'] == 2) {
                                        $clients = $override->getWithLimit3('clients', 'status', 1, 'eligible', 1, 'site_id', $user->data()->site_id, $page, $numRec);
                                    } elseif ($_GET['status'] == 3) {
                                        $clients = $override->getWithLimit3('clients', 'status', 1, 'enrolled', 1, 'site_id', $user->data()->site_id, $page, $numRec);
                                    } elseif ($_GET['status'] == 4) {
                                        $clients = $override->getWithLimit3('clients', 'status', 1, 'end_study', 1, 'site_id', $user->data()->site_id, $page, $numRec);
                                    } elseif ($_GET['status'] == 5) {
                                        $clients = $override->getWithLimit1('clients', 'status', 1, 'site_id', $_GET['site_id'], $page, $numRec);
                                    } elseif ($_GET['status'] == 6) {
                                        $clients = $override->getWithLimit3('clients', 'status', 1, 'screened', 0, 'site_id', $user->data()->site_id, $page, $numRec);
                                    } elseif ($_GET['status'] == 7) {
                                        $clients = $override->getWithLimit('clients', 'site_id', $user->data()->site_id, $page, $numRec);
                                    } elseif ($_GET['status'] == 8) {
                                        $clients = $override->getWithLimit1('clients', 'status', 0, 'site_id', $user->data()->site_id, $page, $numRec);
                                    }
                                } ?>
                                <div class="card">
                                    <div class="card-header">
                                        <?php
                                        if ($_GET['status'] == 1) { ?>
                                            <h3 class="card-title">List of Screened Clients</h3>
                                        <?php
                                        } elseif ($_GET['status'] == 2) { ?>
                                            <h3 class="card-title">List of Eligible Clients</h3>
                                        <?php
                                        } elseif ($_GET['status'] == 3) { ?>
                                            <h3 class="card-title">List of Enrolled Clients</h3>
                                        <?php
                                        } elseif ($_GET['status'] == 4) { ?>
                                            <h3 class="card-title">List of Terminated Clients</h3>
                                        <?php
                                        } elseif ($_GET['status'] == 5) { ?>
                                            <h3 class="card-title">List of Registered Clients</h3>
                                        <?php
                                        } ?>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Patient ID</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                    <th>Summary</th>

                                                    <?php
                                                    if ($_GET['status'] == 1) { ?>
                                                        <th>Screening / Enrollments / Forms</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $x = 1;
                                                foreach ($clients as $client) {
                                                    $screening = $override->get('screening', 'patient_id', $client['id'])[0];
                                                    $visit = $override->getCount('visit', 'client_id', $client['id']);
                                                    $visits = $override->get('visit', 'client_id', $client['id'])[0];
                                                    $end_study = $override->getNews('screening', 'status', 1, 'patient_id', $client['id'])[0];
                                                    $termination = $override->firstRow1('visit', 'outcome', 'id', 'client_id', $client['id'], 'visit_code', 'TV')[0]['outcome'];
                                                    $type = $override->get('main_diagnosis', 'patient_id', $client['id'])[0];

                                                    $enrollment_date = $override->firstRow2('visit', 'visit_date', 'id', 'client_id', $client['id'], 'seq_no', 1, 'visit_code', 'EV')[0]['visit_date'];



                                                    // print_r($termination);

                                                    $screened = 0;
                                                    $eligibility = 0;
                                                    $enrollment = 0;

                                                    if ($client) {
                                                        if ($client['screened']) {
                                                            $screened = 1;
                                                        }
                                                    }

                                                    if ($client) {
                                                        if ($client['eligible']) {
                                                            $eligibility = 1;
                                                        }
                                                    }

                                                    if ($client) {
                                                        if ($client['enrolled']) {
                                                            $enrollment = 1;
                                                        }
                                                    }


                                                ?>
                                                    <tr>
                                                        <td><?= $client['study_id'] ?></td>
                                                        <?php if ($type['cardiac'] == 1) { ?>
                                                            <td>
                                                                <a href="#" class="btn btn-default">Cardiac</a>
                                                            </td>
                                                        <?php } elseif ($type['diabetes'] == 1) { ?>
                                                            <td>
                                                                <a href="#" class="btn btn-info">Diabtes</a>
                                                            </td>
                                                        <?php } elseif ($type['sickle_cell'] == 1) { ?>
                                                            <td>
                                                                <a href="#" class="btn btn-success">Sickle Cell</a>
                                                            </td>
                                                        <?php } else { ?>
                                                            <td>
                                                                <a href="#" class="btn btn-warning">Not Diagnosised</a>
                                                            </td>
                                                        <?php
                                                        } ?>

                                                        <?php if ($_GET['status'] == 1) { ?>

                                                            <?php if ($client['eligible'] == 1) { ?>
                                                                <td>
                                                                    <a href="#" class="btn btn-success">Eligible</a>
                                                                </td>
                                                            <?php } else { ?>
                                                                <td>
                                                                    <a href="#" class="btn btn-danger">Not Eligible</a>
                                                                </td>
                                                        <?php }
                                                        } ?>

                                                        <?php if ($_GET['status'] == 2) { ?>

                                                            <?php if ($client['enrolled'] == 1) { ?>
                                                                <td>
                                                                    <a href="#" class="btn btn-success">Enrolled</a>
                                                                </td>
                                                            <?php } else { ?>
                                                                <td>
                                                                    <a href="#" class="btn btn-danger">Not Enrolled</a>
                                                                </td>
                                                        <?php }
                                                        } ?>

                                                        <?php if ($_GET['status'] == 3) { ?>

                                                            <?php if ($client['enrolled'] == 1) { ?>
                                                                <td>
                                                                    <a href="#" class="btn btn-success">Enrolled</a>
                                                                </td>
                                                            <?php } else { ?>
                                                                <td>
                                                                    <a href="#" class="btn btn-danger">Not Enrolled</a>
                                                                </td>
                                                        <?php }
                                                        } ?>



                                                        <?php if ($_GET['status'] == 4) { ?>
                                                            <td>
                                                                <?php if ($client['end_study'] == 1) { ?>
                                                                    <a href="#" class="btn btn-danger">END</a>
                                                            </td>

                                                            <td>
                                                                <?php if ($termination == 1) { ?>
                                                                    <a href="#" class="btn btn-info">On Treatment</a>
                                                                <?php } elseif ($termination == '2') { ?>

                                                                    <a href="#" class="btn btn-info">Default</a>
                                                                <?php
                                                                    } elseif ($termination == 3) { ?>
                                                                    <a href="#" class="btn btn-info">Stop treatment</a>
                                                                <?php
                                                                    } elseif ($termination == 4) { ?>
                                                                    <a href="#" class="btn btn-info">Trnasfer Out</a>
                                                                <?php
                                                                    } elseif ($termination == 5) { ?>
                                                                    <a href="#" class="btn btn-info">Death</a>
                                                                <?php
                                                                    } else { ?>
                                                                    <a href="#" class="btn btn-info">Other</a>
                                                                <?php
                                                                    } ?>
                                                            </td>


                                                        <?php } else { ?>
                                                            <td>
                                                                <a href="#" class="btn btn-success">ACTIVE</a>
                                                            </td>
                                                    <?php }
                                                            } ?>


                                                    <?php if ($_GET['status'] == 5 || $_GET['status'] == 6 || $_GET['status'] == 7 || $_GET['status'] == 8) { ?>

                                                        <?php if ($client['screened'] == 1) { ?>
                                                            <td>
                                                                <a href="#" class="btn btn-success">SCREENED</a>
                                                            </td>
                                                        <?php } else { ?>
                                                            <td>
                                                                <a href="#" class="btn btn-danger">NOT SCREENED</a>
                                                            </td>
                                                    <?php }
                                                    } ?>

                                                    <td>
                                                        <?php if ($_GET['status'] == 1 || $_GET['status'] == 5 || $_GET['status'] == 6 || $_GET['status'] == 7 || $_GET['status'] == 8) { ?>
                                                            <a href="add.php?id=4&cid=<?= $client['id'] ?>" role="button" class="btn btn-default">View / Update</a>
                                                            <?php if ($user->data()->power == 1) { ?>
                                                                <a href="#delete<?= $client['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                            <?php } ?>
                                                    </td>

                                                    <td>


                                                        <?php if ($screened) { ?>
                                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addScreening<?= $client['id'] ?>">
                                                                Edit Screening </button>
                                                            <!-- <a href="#addScreening<?= $client['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit Screening</a> -->
                                                        <?php } else {  ?>
                                                            <!-- <a href="#addScreening<?= $client['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Add Screening</a> -->
                                                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#addScreening<?= $client['id'] ?>">
                                                                Add Screening </button>
                                                    <?php }
                                                        } ?>

                                                    <?php if ($_GET['status'] == 2) { ?>
                                                        <?php if ($enrollment == 1) { ?>
                                                            <a href="#addEnrollment<?= $client['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit Enrollment</a>
                                                        <?php } else {  ?>
                                                            <a href="#addEnrollment<?= $client['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Add Enrollment</a>

                                                        <?php }
                                                        ?>
                                                    <?php } ?>
                                                    <?php if ($_GET['status'] == 3) { ?>
                                                        <?php if ($enrollment == 1) { ?>
                                                            <a href="info.php?id=4&cid=<?= $client['id'] ?>" role="button" class="btn btn-warning">Study Crf</a>

                                                    <?php }
                                                    } ?>
                                                    </td>

                                                    <td>
                                                        <?php if ($_GET['status'] == 3) { ?>
                                                            <?php if ($enrollment == 1) { ?>
                                                                <a href="summary.php?cid=<?= $client['id'] ?>" role="button" class="btn btn-primary">Patient Summary</a>

                                                        <?php }
                                                        } ?>
                                                    </td>

                                                    </tr>

                                                    <div class="modal fade" id="addScreening<?= $client['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Default Modal</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <?php $screening = $override->get('screening', 'patient_id', $client['id'])[0];
                                                                    ?>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-6">
                                                                                <div class="row-form clearfix">
                                                                                    <div class="form-group">
                                                                                        <label>Date of Screening</label>
                                                                                        <input class="validate[required,custom[date]] form-control" type="text" name="screening_date" id="screening_date" value="<?php if ($screening['screening_date']) {
                                                                                                                                                                                                                        print_r($screening['screening_date']);
                                                                                                                                                                                                                    }  ?>" required />
                                                                                        <span>Example: 2010-12-01</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="row-form clearfix">
                                                                                    <div class="form-group">
                                                                                        <label>Consenting individuals?</label>
                                                                                        <select class="form-control" name="consent" id="consent" style="width: 100%;" onchange="checkQuestionValue1('consent','conset_date')" required>
                                                                                            <option value="<?= $screening['consent'] ?>"><?php if ($screening) {
                                                                                                                                                if ($screening['consent'] == 1) {
                                                                                                                                                    echo 'Yes';
                                                                                                                                                } elseif ($screening['consent'] == 2) {
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
                                                                            <div class="col-sm-4" id="conset_date">
                                                                                <div class="row-form clearfix">
                                                                                    <div class="form-group">
                                                                                        <label>Date of Conset</label>
                                                                                        <input class="form-control" type="text" name="conset_date" value="<?php if ($screening['conset_date']) {
                                                                                                                                                                print_r($screening['conset_date']);
                                                                                                                                                            }  ?>" required />
                                                                                        <span>Example: 2010-12-01</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <div class="form-group">
                                                                                        <label>Permanent resident?</label>
                                                                                        <select class="form-control" name="residence" style="width: 100%;" required>
                                                                                            <option value="<?= $screening['residence'] ?>"><?php if ($screening) {
                                                                                                                                                if ($screening['residence'] == 1) {
                                                                                                                                                    echo 'Yes';
                                                                                                                                                } elseif ($screening['residence'] == 2) {
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
                                                                                        <label>Known NCD?</label>
                                                                                        <select class="form-control" name="ncd" style="width: 100%;" required>
                                                                                            <option value="<?= $screening['scd'] ?>"><?php if ($screening) {
                                                                                                                                            if ($screening['ncd'] == 1) {
                                                                                                                                                echo 'Yes';
                                                                                                                                            } elseif ($screening['ncd'] == 2) {
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
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                        <input type="hidden" name="screening_id" value="<?= $screening['id'] ?>">
                                                                        <input type="hidden" name="gender" value="<?= $client['gender'] ?>">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                        <input type="submit" name="add_screening" class="btn btn-primary" value="Save changes">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->

                                                    <div class="modal fade" id="addEnrollment<?= $client['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Visit</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <?php
                                                                    $visits_date = $override->firstRow1('visit', 'visit_date', 'id', 'client_id', $client['id'], 'visit_code', 'EV')[0];
                                                                    $visits_reason = $override->firstRow1('visit', 'reasons', 'id', 'client_id', $client['id'], 'visit_code', 'EV')[0];

                                                                    // $visits = $override->getlastRow('visit', 'client_id', $client['id'], 'id')[0];
                                                                    ?>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <div class="form-group">
                                                                                        <label>Date of Enrollment</label>
                                                                                        <input class="validate[required,custom[date]] form-control" type="text" name="visit_date" id="visit_date" value="<?php if ($visits_date['visit_date']) {
                                                                                                                                                                                                                print_r($visits_date['visit_date']);
                                                                                                                                                                                                            }  ?>" required />
                                                                                        <span>Example: 2010-12-01</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-8">
                                                                                <div class="row-form clearfix">
                                                                                    <div class="form-group">
                                                                                        <label>Notes / Remarks / Comments</label>
                                                                                        <textarea class="form-control" name="reasons" rows="4">
                                                                                 <?php
                                                                                    if ($visits_reason['reasons']) {
                                                                                        print_r($visits_reason['reasons']);
                                                                                    } ?>
                                                                                </textarea>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="dr"><span></span></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                        <input type="hidden" name="visit_name" value="Enrollment Visit">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                        <input type="submit" name="add_Enrollment" class="btn btn-primary" value="Save changes">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->
                                                <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Patient ID</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                    <th>Summary</th>
                                                    <?php
                                                    if ($_GET['status'] == 1) { ?>
                                                        <th>Screening / Enrollments / Forms</th>
                                                    <?php } ?>
                                                </tr>
                                            </tfoot>
                                        </table>
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
        <?php } elseif ($_GET['id'] == 4) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>DataTables</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">DataTables</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <?php
                                        $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                                        $visits_status = $override->firstRow1('visit', 'status', 'id', 'client_id', $_GET['cid'], 'visit_code', 'EV')[0]['status'];

                                        // $patient = $override->get('clients', 'id', $_GET['cid'])[0];
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

                                        $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;

                                        ?>

                                        <h1>Schedule for Study ID: <?= $patient['study_id'] ?><h4><?= $name ?></h4>

                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Patient ID</th>
                                                    <th>Visit Day</th>
                                                    <th>Expected Date</th>
                                                    <th>Visit Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                    <th>Next Appointment Summary</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $x = 1;
                                                foreach ($override->get('visit', 'client_id', $_GET['cid']) as $visit) {
                                                    // print_r($visit['visit_code']);
                                                    $clnt = $override->get('clients', 'id', $_GET['cid'])[0];
                                                    $cntV = $override->getCount('visit', 'client_id', $visit['client_id']);

                                                    $demographic = $override->get3('demographic', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $vital = $override->get3('vital', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $history = $override->get3('history', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $symptoms = $override->get3('symptoms', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $diagnosis = $override->get3('main_diagnosis', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $results = $override->get3('results', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $hospitalization = $override->get3('hospitalization', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $treatment_plan = $override->get3('treatment_plan', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $dgns_complctns_comorbdts = $override->get3('dgns_complctns_comorbdts', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $risks = $override->get3('risks', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $hospitalization_details = $override->get3('hospitalization_details', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $lab_details = $override->get3('lab_details', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $summary = $override->get3('summary', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $social_economic = $override->get3('social_economic', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);


                                                    // print_r($treatment_plan);
                                                    if ($visit['status'] == 0) {
                                                        $btnV = 'Add';
                                                    } elseif ($visit['status'] == 1) {
                                                        $btnV = 'Edit';
                                                    }

                                                    $visit_name = $visit['visit_name'];

                                                ?>
                                                    <tr>
                                                        <td><?= $patient['study_id'] ?></td>
                                                        <td> <?= $visit['visit_day'] ?></td>
                                                        <td> <?= $visit['expected_date'] ?></td>
                                                        <td> <?= $visit['visit_date'] ?> </td>
                                                        <td>
                                                            <?php if ($visit['status'] == 1) { ?>
                                                                <a href="#editVisit<?= $visit['id'] ?>" role="button" class="btn btn-success" data-toggle="modal">Done</a>
                                                            <?php } elseif ($visit['status'] == 0) { ?>
                                                                <a href="#editVisit<?= $visit['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Pending</a>
                                                            <?php } ?>
                                                        </td>

                                                        <td>
                                                            <?php if ($visit['visit_code'] == 'EV') { ?>

                                                                <?php if ($visit['status'] == 1 && ($visit['visit_code'] == 'EV' || $visit['visit_code'] == 'FV' || $visit['visit_code'] == 'TV' || $visit['visit_code'] == 'UV')) { ?>

                                                                    <?php if ($demographic && $vital && $history && $symptoms && $diagnosis && $results && $hospitalization && $treatment_plan && $dgns_complctns_comorbdts && $risks && $hospitalization_details  && $lab_details && $social_economic) { ?>

                                                                        <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>" role="button" class="btn btn-info"> Edit Study Forms </a>


                                                                    <?php } else { ?>
                                                                        <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>" role="button" class="btn btn-warning"> Fill Study Forms </a>

                                                            <?php }
                                                                }
                                                            } ?>


                                                            <?php if (($visit['visit_code'] == 'FV' || $visit['visit_code'] == 'TV' || $visit['visit_code'] == 'UV')) { ?>

                                                                <?php if ($visit['status'] == 1 && ($visit['visit_code'] == 'EV' || $visit['visit_code'] == 'FV' || $visit['visit_code'] == 'TV' || $visit['visit_code'] == 'UV')) { ?>

                                                                    <?php if ($vital && $symptoms && $results && $hospitalization && $treatment_plan && $dgns_complctns_comorbdts && $risks && $hospitalization_details  && $lab_details) { ?>

                                                                        <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>" role="button" class="btn btn-info"> Edit Study Forms </a>


                                                                    <?php } else { ?>
                                                                        <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>" role="button" class="btn btn-warning"> Fill Study Forms </a>

                                                            <?php }
                                                                }
                                                            } ?>

                                                        </td>
                                                        <td>
                                                            <?php if ($visit['seq_no'] >= 1) {
                                                                $summary = '';
                                                            ?>
                                                                <?php if ($visit['visit_status']) {
                                                                ?>
                                                                    <a href="#addSchedule<?= $visit['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Update</a>
                                                                <?php } else {
                                                                    $summary = 1;
                                                                ?>
                                                                    <a href="#addSchedule<?= $visit['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Add</a>
                                                            <?php }
                                                            } ?>

                                                        </td>
                                                    </tr>

                                                    <div class="modal fade" id="editVisit<?= $visit['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Default Modal</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <?php $screening = $override->get('screening', 'patient_id', $client['id'])[0];
                                                                    ?>
                                                                    <div class="modal-body">
                                                                        <div class="row">

                                                                            <div class="row">
                                                                                <div class="col-sm-6">
                                                                                    <div class="row-form clearfix">
                                                                                        <!-- select -->
                                                                                        <div class="form-group">
                                                                                            <label>Visit Date</label>
                                                                                            <input value="<?php if ($visit['status'] != 0) {
                                                                                                                echo $visit['visit_date'];
                                                                                                            } ?>" class="validate[required,custom[date]]" type="text" name="visit_date" id="visit_date" />
                                                                                            <span>Example: 2010-12-01</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- <div class="col-sm-3">
                                                                                    <div class="row-form clearfix"> -->
                                                                                <!-- select -->
                                                                                <!-- <div class="form-group">
                                                                                            <label>Visit Name</label>
                                                                                            <select name="visit_name" style="width: 100%;" required>
                                                                                                <option value="">Select</option>
                                                                                                <?php foreach ($override->getData('schedule') as $study) { ?>
                                                                                                    <option value="<?= $study['name'] ?>"><?= $study['name'] ?></option>
                                                                                                <?php } ?>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div> -->

                                                                                <div class="col-sm-6">
                                                                                    <div class="row-form clearfix">
                                                                                        <!-- select -->
                                                                                        <div class="form-group">
                                                                                            <label>Notes / Remarks /Comments</label>
                                                                                            <textarea name="reasons" rows="4"><?php if ($visit['status'] != 0) {
                                                                                                                                    echo $visit['reasons'];
                                                                                                                                } ?></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="dr"><span></span></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id" value="<?= $visit['id'] ?>">
                                                                        <input type="hidden" name="vc" value="<?= $visit['visit_code'] ?>">
                                                                        <input type="hidden" name="visit_name" value="<?= $visit['visit_name'] ?>">
                                                                        <input type="hidden" name="cid" value="<?= $visit['client_id'] ?>">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                        <input type="submit" name="edit_visit" class="btn btn-primary" value="Save changes">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->

                                                    <div class="modal fade" id="addSchedule<?= $visit['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Visit Summary</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <?php

                                                                    $visits_date = $override->firstRow('visit', 'visit_date', 'id', 'client_id', $client['id'])[0];
                                                                    $visits = $override->getlastRow('visit', 'client_id', $client['id'], 'id')[0];
                                                                    $summary = $override->get3('visit', 'client_id', $client['id'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])[0];

                                                                    ?>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Visit Name</label>
                                                                                        <select class="form-control" name="visit_name" style="width: 100%;" required>
                                                                                            <option value="">Select</option>
                                                                                            <?php foreach ($override->getData2('schedule', 'status', 4) as $study) { ?>
                                                                                                <option value="<?= $study['name'] ?>"><?= $study['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Summary Date</label>
                                                                                        <input class="validate[required,custom[date]] form-control" type="text" name="summary_date" id="summary_date" value="<?php if ($visit['summary_date']) {
                                                                                                                                                                                                                    print_r($visit['summary_date']);
                                                                                                                                                                                                                }  ?>" required />
                                                                                        <span>Example: 2010-12-01</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Type of diagnosis</label>
                                                                                        <select class="form-control" name="diagnosis" id="diagnosis" style="width: 100%;" onchange="checkQuestionValue96('diagnosis','diagnosis_other')" required>
                                                                                            <option value="<?= $visit['diagnosis'] ?>"><?php if ($visit) {
                                                                                                                                            if ($visit['diagnosis'] == 1) {
                                                                                                                                                echo 'Type 1 Diabetes';
                                                                                                                                            } elseif ($visit['diagnosis'] == 2) {
                                                                                                                                                echo 'Type 2 Diabetes';
                                                                                                                                            } elseif ($visit['diagnosis'] == 3) {
                                                                                                                                                echo 'Cardiac';
                                                                                                                                            } elseif ($visit['diagnosis'] == 4) {
                                                                                                                                                echo 'Sickle Cell Disease ';
                                                                                                                                            } elseif ($visit['diagnosis'] == 5) {
                                                                                                                                                echo 'Respiratory';
                                                                                                                                            } elseif ($visit['diagnosis'] == 6) {
                                                                                                                                                echo 'Liver';
                                                                                                                                            } elseif ($visit['diagnosis'] == 7) {
                                                                                                                                                echo 'Kidney';
                                                                                                                                            } elseif ($visit['diagnosis'] == 96) {
                                                                                                                                                echo 'Other';
                                                                                                                                            }
                                                                                                                                        } else {
                                                                                                                                            echo 'Select';
                                                                                                                                        } ?>
                                                                                            </option>
                                                                                            <option value="1">Type 1 Diabetes</option>
                                                                                            <option value="2">Type 2 Diabetes </option>
                                                                                            <option value="3">Cardiac</option>
                                                                                            <option value="4">Sickle Cell Disease </option>
                                                                                            <option value="5">Respiratory</option>
                                                                                            <option value="6">Liver</option>
                                                                                            <option value="7">Kidney</option>
                                                                                            <option value="96">Other</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">

                                                                            <div class="row hidden" id="diagnosis_other">

                                                                                <div class="col-sm-6">
                                                                                    <div class="row-form clearfix">
                                                                                        <!-- select -->
                                                                                        <div class="form-group">
                                                                                            <label>If other, Specify</label>
                                                                                            <input class="form-control" type="text" name="diagnosis_other" value="<?php if ($visit['diagnosis_other']) {
                                                                                                                                                                        print_r($visit['diagnosis_other']);
                                                                                                                                                                    }  ?>" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-6">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Comments</label>
                                                                                        <textarea class="form-control" name="comments" rows="4">
                                                                                    <?php if ($visit['comments']) {
                                                                                        print_r($visit['comments']);
                                                                                    }  ?>
                                                                                </textarea>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>




                                                                        <div class="row">

                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Outcome</label>
                                                                                        <select class="form-control" name="outcome" id="outcome" style="width: 100%;" onchange="checkQuestionValue45('outcome','transfer_out1','cause_death1')" required>
                                                                                            <option value="<?= $visit['outcome'] ?>"><?php if ($visit) {
                                                                                                                                            if ($visit['outcome'] == 1) {
                                                                                                                                                echo 'On treatment';
                                                                                                                                            } elseif ($visit['outcome'] == 2) {
                                                                                                                                                echo 'Default';
                                                                                                                                            } elseif ($visit['outcome'] == 3) {
                                                                                                                                                echo 'Stop Treatment';
                                                                                                                                            } elseif ($visit['outcome'] == 4) {
                                                                                                                                                echo 'Transfer Out';
                                                                                                                                            } elseif ($visit['outcome'] == 5) {
                                                                                                                                                echo 'Death';
                                                                                                                                            }
                                                                                                                                        } else {
                                                                                                                                            echo 'Select';
                                                                                                                                        } ?>
                                                                                            </option>
                                                                                            <option value="1">On treatment</option>
                                                                                            <option value="2">Default</option>
                                                                                            <option value="3">Stop Treatment</option>
                                                                                            <option value="4">Transfer Out</option>
                                                                                            <option value="5">Death</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>


                                                                            <div class="col-sm-4 hidden" id="transfer_out1">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Transfer Out To</label>
                                                                                        <select class="form-control" name="transfer_out" id="transfer_out" style="width: 100%;" onchange="checkQuestionValue96('transfer_out','transfer_other')">
                                                                                            <option value="<?= $visit['transfer_out'] ?>"><?php if ($visit) {
                                                                                                                                                if ($visit['transfer_out'] == 1) {
                                                                                                                                                    echo 'Other NCD clinic';
                                                                                                                                                } elseif ($visit['transfer_out'] == 2) {
                                                                                                                                                    echo 'Referral hospital';
                                                                                                                                                } elseif ($visit['transfer_out'] == 96) {
                                                                                                                                                    echo 'Other';
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                echo 'Select';
                                                                                                                                            } ?>
                                                                                            </option>
                                                                                            <option value="1">Other NCD clinic</option>
                                                                                            <option value="2">Referral hospital</option>
                                                                                            <option value="96">Other</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-4 hidden" id="transfer_other">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>If other reason, Specify</label>
                                                                                        <input class="form-control" type="text" name="transfer_other" value="<?php if ($visit['transfer_other']) {
                                                                                                                                                                    print_r($visit['transfer_other']);
                                                                                                                                                                }  ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-4 hidden" id="cause_death1">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Cause of Death</label>
                                                                                        <select class="form-control" name="cause_death" id="cause_death" style="width: 100%;" onchange="checkQuestionValue96('cause_death','death_other')">
                                                                                            <option value="<?= $visit['cause_death'] ?>"><?php if ($visit) {
                                                                                                                                                if ($visit['cause_death'] == 1) {
                                                                                                                                                    echo 'NCD';
                                                                                                                                                } elseif ($visit['cause_death'] == 2) {
                                                                                                                                                    echo 'Unknown';
                                                                                                                                                } elseif ($visit['cause_death'] == 96) {
                                                                                                                                                    echo 'Other';
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                echo 'Select';
                                                                                                                                            } ?>
                                                                                            </option>
                                                                                            <option value="1">NCD</option>
                                                                                            <option value="2">Unknown</option>
                                                                                            <option value="96">Other</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-4 hidden" id="death_other">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>If other cause, Specify</label>
                                                                                        <input class="form-control" type="text" name="death_other" value="<?php if ($visit['death_other']) {
                                                                                                                                                                print_r($visit['death_other']);
                                                                                                                                                            }  ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                        <div class="row">

                                                                            <div class="col-sm-6">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Notes for Next Appointment</label>
                                                                                        <input class="form-control" type="text" name="next_notes" id="next_notes" value="<?php if ($visit['next_notes']) {
                                                                                                                                                                                print_r($visit['next_notes']);
                                                                                                                                                                            }  ?>" required />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-6" id="diagnosis_other">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Next Appointment Date</label>
                                                                                        <input class="validate[required,custom[date]] form-control" type="text" name="expected_date" id="expected_date" value="<?php if ($visit['expected_date']) {
                                                                                                                                                                                                                    print_r($visit['expected_date']);
                                                                                                                                                                                                                }  ?>" required />
                                                                                        <span>Example: 2023-01-01</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id" value="<?= $visit['id'] ?>">
                                                                        <input type="hidden" name="seq_no" value="<?= $visit['seq_no'] ?>">
                                                                        <input type="hidden" name="summary" value="<?= $summary ?>">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                        <input type="submit" name="add_Schedule" class="btn btn-primary" value="Save changes">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->
                                                <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Patient ID</th>
                                                    <th>Visit Day</th>
                                                    <th>Expected Date</th>
                                                    <th>Visit Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                    <th>Next Appointment Summary</th>
                                                </tr>
                                            </tfoot>
                                        </table>
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
        <?php } elseif ($_GET['id'] == 5) { ?>
        <?php } elseif ($_GET['id'] == 6) { ?>
        <?php } elseif ($_GET['id'] == 7) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>DataTables</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">DataTables</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <?php
                                        $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                                        $visits_status = $override->firstRow1('visit', 'status', 'id', 'client_id', $_GET['cid'], 'visit_code', 'EV')[0]['status'];

                                        // $patient = $override->get('clients', 'id', $_GET['cid'])[0];
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

                                        $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;

                                        ?>

                                        <h1>Study CRF (Enrollment) for ID: <?= $patient['study_id'] ?><h4><?= $name ?></h4>

                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>CRF</th>
                                                    <th>Records</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <tr>
                                                    <?php if ($_GET['seq'] == 1) { ?>

                                                <tr>
                                                    <td>1</td>
                                                    <td>Demographic</td>
                                                    <td>
                                                        <!-- <i class="nav-icon fas fa-th"></i> -->
                                                        <span class="badge badge-info right">
                                                            <?= $override->countData('demographic', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                        </span>
                                                    </td>
                                                    <?php if ($override->get3('demographic', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>
                                                        <td><a href="add.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                    <?php } else { ?>
                                                        <td><a href="add.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                    <?php } ?>
                                                </tr>

                                            <?php }  ?>

                                            <tr>
                                                <td>2</td>
                                                <td>Vitals</td>
                                                <td>
                                                    <!-- <i class="nav-icon fas fa-th"></i> -->
                                                    <span class="badge badge-info right">
                                                        <?= $override->countData('vital', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                    </span>
                                                </td>
                                                <?php if ($override->get3('vital', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>
                                                    <td><a href="add.php?id=8&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                <?php } else { ?>
                                                    <td><a href="add.php?id=8&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                <?php } ?>
                                            </tr>

                                            <?php if ($_GET['seq'] == 1) { ?>

                                                <tr>
                                                    <td>2</td>
                                                    <td>Pateint Category</td>
                                                    <td>
                                                        <!-- <i class="nav-icon fas fa-th"></i> -->
                                                        <span class="badge badge-info right">
                                                            <?= $override->countData('main_diagnosis', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                        </span>
                                                    </td>
                                                    <?php if ($override->get3('main_diagnosis', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>
                                                        <td><a href="add.php?id=9&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                    <?php } else { ?>
                                                        <td><a href="add.php?id=9&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                    <?php } ?>
                                                </tr>

                                            <?php }  ?>


                                            <?php if ($_GET['seq'] == 1) { ?>

                                                <tr>
                                                    <td>3</td>
                                                    <td>Patient Hitory & Family History & Complication</td>
                                                    <td>
                                                        <!-- <i class="nav-icon fas fa-th"></i> -->
                                                        <span class="badge badge-info right">
                                                            <?= $override->countData('history', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                        </span>
                                                    </td>
                                                    <?php if ($override->get3('history', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                        <td><a href="add.php?id=10&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                    <?php } else { ?>
                                                        <td><a href="add.php?id=10&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                    <?php } ?>
                                                </tr>

                                            <?php }  ?>


                                            <tr>
                                                <td>4</td>
                                                <td>History, Symtom & Exam</td>
                                                <td>
                                                    <!-- <i class="nav-icon fas fa-th"></i> -->
                                                    <span class="badge badge-info right">
                                                        <?= $override->countData('symptoms', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                    </span>
                                                </td>
                                                <?php if ($override->get3('symptoms', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                    <td><a href="add.php?id=10&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                <?php } else { ?>
                                                    <td><a href="add.php?id=10&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                <?php } ?>
                                            </tr>


                                            <?php if ($_GET['seq'] == 1) { ?>

                                                <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1)) { ?>

                                                    <tr>
                                                        <td>5</td>
                                                        <td>Main diagnosis 1 ( Cardiac )</td>
                                                        <td>
                                                            <!-- <i class="nav-icon fas fa-th"></i> -->
                                                            <span class="badge badge-info right">
                                                                <?= $override->countData('cardiac', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                            </span>
                                                        </td>
                                                        <?php if ($override->get3('cardiac', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                            <td><a href="add.php?id=11&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                        <?php } else { ?>
                                                            <td><a href="add.php?id=11&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>


                                                <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'diabetes', 1)) { ?>

                                                    <tr>
                                                        <td>5</td>
                                                        <td>Main diagnosis 2 ( Diabetes )</td>
                                                        <td>
                                                            <!-- <i class="nav-icon fas fa-th"></i> -->
                                                            <span class="badge badge-info right">
                                                                <?= $override->countData('diabetic', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                            </span>
                                                        </td>
                                                        <?php if ($override->get3('diabetic', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                            <td><a href="add.php?id=21&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                        <?php } else { ?>
                                                            <td><a href="add.php?id=21&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>


                                                <?php if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { ?>


                                                    <tr>
                                                        <td>5</td>
                                                        <td>Main diagnosis 3 ( Sickle Cell )</td>
                                                        <td>
                                                            <!-- <i class="nav-icon fas fa-th"></i> -->
                                                            <span class="badge badge-info right">
                                                                <?= $override->countData('sickle_cell', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                            </span>
                                                        </td>
                                                        <?php if ($override->get3('sickle_cell', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                            <td><a href="add.php?id=22&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                        <?php } else { ?>
                                                            <td><a href="add.php?id=22&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                        <?php } ?>
                                                    </tr>

                                                <?php } ?>

                                            <?php }  ?>


                                            <?php
                                            //  if ($override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'cardiac', 1) || $override->get2('main_diagnosis', 'patient_id', $_GET['cid'], 'sickle_cell', 1)) { 
                                            ?>
                                            <tr>
                                                <td>6</td>
                                                <?php if ($_GET['seq'] == 1) { ?>
                                                    <td>Results at enrollment</td>
                                                <?php } else { ?>
                                                    <td>Results at Follow Up</td>
                                                <?php } ?>
                                                <td>
                                                    <!-- <i class="nav-icon fas fa-th"></i> -->
                                                    <span class="badge badge-info right">
                                                        <?= $override->countData('results', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                    </span>
                                                </td>

                                                <?php if ($override->get3('results', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                    <td><a href="add.php?id=12&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                <?php } else { ?>
                                                    <td><a href="add.php?id=12&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                <?php } ?>

                                            </tr>

                                            <?php
                                            //  }
                                            ?>

                                            <tr>
                                                <td>7</td>
                                                <td>Hospitalization</td>
                                                <td>
                                                    <!-- <i class="nav-icon fas fa-th"></i> -->
                                                    <span class="badge badge-info right">
                                                        <?= $override->countData('hospitalization', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                    </span>
                                                </td>
                                                <?php if ($override->get3('hospitalization', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                    <td><a href="add.php?id=13&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                <?php } else { ?>
                                                    <td><a href="add.php?id=13&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                <?php } ?>

                                            </tr>
                                            <tr>
                                                <td>8</td>
                                                <td>Hospitalization Details</td>
                                                <td>
                                                    <!-- <i class="nav-icon fas fa-th"></i> -->
                                                    <span class="badge badge-info right">
                                                        <?= $override->countData('hospitalization_details', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                    </span>
                                                </td>
                                                <?php if ($override->get3('hospitalization_details', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                    <td><a href="add.php?id=17&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                <?php } else { ?>
                                                    <td><a href="add.php?id=17&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                <?php } ?>

                                            </tr>

                                            <tr>
                                                <td>9</td>
                                                <td>Treatment Plan</td>
                                                <td>
                                                    <!-- <i class="nav-icon fas fa-th"></i> -->
                                                    <span class="badge badge-info right">
                                                        <?= $override->countData('treatment_plan', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                    </span>
                                                </td>
                                                <?php if ($override->get3('treatment_plan', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                    <td><a href="add.php?id=14&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                <?php } else { ?>
                                                    <td><a href="add.php?id=14&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                <?php } ?>

                                            </tr>

                                            <tr>
                                                <td>10</td>
                                                <td>Diagnosis, Complications, & Comorbidities</td>
                                                <td>
                                                    <!-- <i class="nav-icon fas fa-th"></i> -->
                                                    <span class="badge badge-info right">
                                                        <?= $override->countData('dgns_complctns_comorbdts', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                    </span>
                                                </td>
                                                <?php if ($override->get3('dgns_complctns_comorbdts', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                    <td><a href="add.php?id=15&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                <?php } else { ?>
                                                    <td><a href="add.php?id=15&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                <?php } ?>

                                            </tr>

                                            <tr>
                                                <td>11</td>
                                                <td>RISK</td>
                                                <td>
                                                    <!-- <i class="nav-icon fas fa-th"></i> -->
                                                    <span class="badge badge-info right">
                                                        <?= $override->countData('risks', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                    </span>
                                                </td>
                                                <?php if ($override->get3('risks', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                    <td><a href="add.php?id=16&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                <?php } else { ?>
                                                    <td><a href="add.php?id=16&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                <?php } ?>

                                            </tr>



                                            <tr>
                                                <td>12</td>
                                                <td>Lab Details</td>
                                                <td>
                                                    <!-- <i class="nav-icon fas fa-th"></i> -->
                                                    <span class="badge badge-info right">
                                                        <?= $override->countData('lab_details', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                    </span>
                                                </td>
                                                <?php if ($override->get3('lab_details', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                    <td><a href="add.php?id=18&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                <?php } else { ?>
                                                    <td><a href="add.php?id=18&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                <?php } ?>

                                            </tr>
                                            <?php if ($_GET['seq'] == 1) { ?>

                                                <tr>
                                                    <td>13</td>
                                                    <td>Socioeconomic Status</td>
                                                    <td>
                                                        <!-- <i class="nav-icon fas fa-th"></i> -->
                                                        <span class="badge badge-info right">
                                                            <?= $override->countData('social_economic', 'status', 1, 'site_id', $user->data()->site_id) ?>
                                                        </span>
                                                    </td>
                                                    <?php if ($override->get3('social_economic', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                        <td><a href="add.php?id=20&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                    <?php } else { ?>
                                                        <td><a href="add.php?id=20&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                    <?php } ?>

                                                </tr>
                                            <?php } ?>

                                            <!-- <tr>
                                                                                    <td>14</td>
                                                                                    <td>Lab Requests</td>
                                                                                    <?php if ($override->get3('lab_requests', 'patient_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'visit_code', $_GET['vcode'])) { ?>

                                                                                        <td><a href="add_lab.php?cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-success"> Change </a> </td>
                                                                                    <?php } else { ?>
                                                                                        <td><a href="add_lab.php?cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>" class="btn btn-warning"> Add </a> </td>
                                                                                    <?php } ?>

                                                                                    </tr> -->
                                            </tr>

                                            </tbody>
                                            <tfoot>
                                                <th>#</th>
                                                <th>CRF</th>
                                                <th>Records</th>
                                                <th>Action</th>
                                            </tfoot>
                                        </table>
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
        <?php } elseif ($_GET['id'] == 8) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Medications</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Medications</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <?php
                                        $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                                        $visits_status = $override->firstRow1('visit', 'status', 'id', 'client_id', $_GET['cid'], 'visit_code', 'EV')[0]['status'];

                                        // $patient = $override->get('clients', 'id', $_GET['cid'])[0];
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

                                        $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;

                                        ?>

                                        <div class="col-sm-6">
                                            <ol class="breadcrumb float-sm-right">
                                                <li class="breadcrumb-item">
                                                    <a href='index1.php' class="btn btn-default">Back</a>
                                                    </a>
                                                </li>
                                            </ol>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>Form</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $x = 1;
                                                foreach ($override->get('medications', 'status', 1) as $value) {

                                                    $batch_sum = $override->getSumD2('batch', 'amount', 'status', 1, 'medication_id', $value['id'])[0]['SUM(amount)'];
                                                    $forms = $override->getNewsAsc0('forms', 'status', 1, 'id', $value['forms'])[0];
                                                    if ($batch_sum) {
                                                        $batch_sum = $batch_sum;
                                                    } elseif ($visit['status'] == 1) {
                                                        $batch_sum = 0;
                                                    }

                                                ?>
                                                    <tr>
                                                        <td><?= $value['name'] ?></td>
                                                        <td> <?= $batch_sum ?></td>
                                                        <td> <?= $forms['name'] ?></td>
                                                        <td>
                                                            <?php if ($value['expire_date'] > date('Y-m-d')) { ?>
                                                                <a href="#editVisit<?= $value['id'] ?>" role="button" class="btn btn-success" data-toggle="modal">Valid</a>
                                                            <?php } elseif ($visit['status'] == 0) { ?>
                                                                <a href="#editVisit<?= $value['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Expired</a>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <a href="add.php?id=5&medication_id=<?= $value['id'] ?>&forms=<?= $value['forms'] ?>&use_group=<?= $value['use_group'] ?>&maintainance=<?= $value['maintainance'] ?>&btn=Update" role="button" class="btn btn-info">Update</a>
                                                            <a href="info.php?id=9&medication_id=<?= $value['id'] ?>&forms=<?= $value['forms'] ?>&use_group=<?= $value['use_group'] ?>&maintainance=<?= $value['maintainance'] ?>&btn=Update" role="button" class="btn btn-success">View</a>
                                                            <a href="#delete_medication<?= $value['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="delete_medication<?= $value['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Delete Medication</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p class="text-muted text-center">Are you sure yoy want to delete this medication ?</p>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id" value="<?= $value['id'] ?>">
                                                                        <input type="hidden" name="name" value="<?= $value['name'] ?>">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                                        <input type="submit" name="delete_medication" class="btn btn-danger" value="Yes, Delete">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->
                                                <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>Form</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
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
        <?php } elseif ($_GET['id'] == 9) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Medications</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Medications</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <?php
                                        $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                                        $visits_status = $override->firstRow1('visit', 'status', 'id', 'client_id', $_GET['cid'], 'visit_code', 'EV')[0]['status'];

                                        // $patient = $override->get('clients', 'id', $_GET['cid'])[0];
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

                                        $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;

                                        ?>

                                        <div class="col-sm-6">
                                            <ol class="breadcrumb float-sm-right">
                                                <li class="breadcrumb-item"><a href="index1.php">
                                                        <a href='info.php?id=8' class="btn btn-default">Back</a>
                                                    </a>
                                                </li>
                                            </ol>
                                        </div>
                                    </div>

                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Batch / Serial No.</th>
                                                    <th>Amount</th>
                                                    <th>Expire Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $x = 1;
                                                foreach ($override->getNews('batch', 'status', 1, 'medication_id', $_GET['medication_id']) as $value) {

                                                    $batch_sum = $override->getSumD2('batch', 'amount', 'status', 1, 'medication_id', $value['id'])[0]['SUM(amount)'];
                                                    $forms = $override->get('forms', 'status', 1, 'id', $value['forms'])[0];
                                                    if ($batch_sum) {
                                                        $batch_sum = $batch_sum;
                                                    } elseif ($visit['status'] == 1) {
                                                        $batch_sum = 0;
                                                    }

                                                ?>
                                                    <tr>
                                                        <td><?= $value['name'] ?></td>
                                                        <td><?= $value['serial_name'] ?></td>
                                                        <td><?= $value['amount'] ?></td>
                                                        <td><?= $value['expire_date'] ?></td>
                                                        <td>
                                                            <?php if ($value['expire_date'] > date('Y-m-d')) { ?>
                                                                <a href="#editVisit<?= $visit['id'] ?>" role="button" class="btn btn-success" data-toggle="modal">Valid</a>
                                                            <?php } elseif ($visit['status'] == 0) { ?>
                                                                <a href="#editVisit<?= $visit['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Expired</a>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <a href="#editVisit<?= $visit['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Update</a>
                                                            <a href="info.php?id=9&generic_id=<?= $value['id'] ?>" role="button" class="btn btn-success">View</a>
                                                        </td>

                                                    </tr>

                                                    <div class="modal fade" id="editVisit<?= $visit['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Default Modal</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <?php $screening = $override->get('screening', 'patient_id', $client['id'])[0];
                                                                    ?>
                                                                    <div class="modal-body">
                                                                        <div class="row">

                                                                            <div class="row">
                                                                                <div class="col-sm-6">
                                                                                    <div class="row-form clearfix">
                                                                                        <!-- select -->
                                                                                        <div class="form-group">
                                                                                            <label>Visit Date</label>
                                                                                            <input value="<?php if ($visit['status'] != 0) {
                                                                                                                echo $visit['visit_date'];
                                                                                                            } ?>" class="validate[required,custom[date]]" type="text" name="visit_date" id="visit_date" />
                                                                                            <span>Example: 2010-12-01</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- <div class="col-sm-3">
                                                                                    <div class="row-form clearfix"> -->
                                                                                <!-- select -->
                                                                                <!-- <div class="form-group">
                                                                                            <label>Visit Name</label>
                                                                                            <select name="visit_name" style="width: 100%;" required>
                                                                                                <option value="">Select</option>
                                                                                                <?php foreach ($override->getData('schedule') as $study) { ?>
                                                                                                    <option value="<?= $study['name'] ?>"><?= $study['name'] ?></option>
                                                                                                <?php } ?>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div> -->

                                                                                <div class="col-sm-6">
                                                                                    <div class="row-form clearfix">
                                                                                        <!-- select -->
                                                                                        <div class="form-group">
                                                                                            <label>Notes / Remarks /Comments</label>
                                                                                            <textarea name="reasons" rows="4"><?php if ($visit['status'] != 0) {
                                                                                                                                    echo $visit['reasons'];
                                                                                                                                } ?></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="dr"><span></span></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id" value="<?= $visit['id'] ?>">
                                                                        <input type="hidden" name="vc" value="<?= $visit['visit_code'] ?>">
                                                                        <input type="hidden" name="visit_name" value="<?= $visit['visit_name'] ?>">
                                                                        <input type="hidden" name="cid" value="<?= $visit['client_id'] ?>">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                        <input type="submit" name="edit_visit" class="btn btn-primary" value="Save changes">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->
                                                <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Batch / Serial No.</th>
                                                    <th>Amount</th>
                                                    <th>Expire Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
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
        <?php } elseif ($_GET['id'] == 10) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Medications Batch</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Medications Batch</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <?php
                                        $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                                        $visits_status = $override->firstRow1('visit', 'status', 'id', 'client_id', $_GET['cid'], 'visit_code', 'EV')[0]['status'];

                                        // $patient = $override->get('clients', 'id', $_GET['cid'])[0];
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

                                        $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;

                                        ?>

                                        <div class="col-sm-6">
                                            <ol class="breadcrumb float-sm-right">
                                                <li class="breadcrumb-item"><a href="index1.php">
                                                        <a href='info.php?id=8' class="btn btn-default">Back</a>
                                                    </a>
                                                </li>
                                            </ol>
                                        </div>
                                    </div>

                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Batch / Serial No.</th>
                                                    <th>Amount</th>
                                                    <th>Forms</th>
                                                    <th>Expire Date</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th>Price</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $x = 1;
                                                foreach ($override->getAsc('batch', 'status', 1) as $value) {
                                                    $medication = $override->getNews('medications', 'status', 1, 'id', $value['medication_id'])['0'];
                                                    $batch_sum = $override->getSumD2('batch', 'amount', 'status', 1, 'medication_id', $value['id'])[0]['SUM(amount)'];
                                                    $forms = $override->getNews('forms', 'status', 1, 'id', $medication['forms'])[0];
                                                    if ($batch_sum) {
                                                        $batch_sum = $batch_sum;
                                                    } elseif ($visit['status'] == 1) {
                                                        $batch_sum = 0;
                                                    }

                                                ?>
                                                    <tr>
                                                        <td><?= $medication['name'] ?></td>
                                                        <td><?= $value['serial_name'] ?></td>
                                                        <td><?= $value['amount'] ?></td>
                                                        <td><?= $forms['name'] ?></td>
                                                        <td><?= $value['expire_date'] ?></td>
                                                        <td>
                                                            <?php if ($value['expire_date'] > date('Y-m-d')) { ?>
                                                                <a href="#editVisit<?= $visit['id'] ?>" role="button" class="btn btn-success" data-toggle="modal">Valid</a>
                                                            <?php } elseif ($visit['status'] == 0) { ?>
                                                                <a href="#editVisit<?= $visit['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Expired</a>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?= $value['remarks'] ?></td>
                                                        <td><?= $value['price'] ?></td>
                                                        <td>
                                                            <a href="add.php?id=6&batch_id=<?= $value['id'] ?>&medication_id=<?= $medication['id'] ?>&btn=Update" role="button" class="btn btn-info">Update</a>
                                                            <a href="#delete_batch<?= $value['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="delete_batch<?= $value['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Delete Medication Batch</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p class="text-muted text-center">Are you sure yoy want to delete this medication batch ?</p>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id" value="<?= $value['id'] ?>">
                                                                        <input type="hidden" name="name" value="<?= $value['serial_name'] ?>">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                                        <input type="submit" name="delete_batch" class="btn btn-danger" value="Yes, Delete">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->
                                                <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Batch / Serial No.</th>
                                                    <th>Amount</th>
                                                    <th>Forms</th>
                                                    <th>Expire Date</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th>Price</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
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
        <?php } elseif ($_GET['id'] == 11) { ?>
        <?php } elseif ($_GET['id'] == 12) { ?>
        <?php } elseif ($_GET['id'] == 13) { ?>
        <?php } elseif ($_GET['id'] == 14) { ?>
        <?php } elseif ($_GET['id'] == 15) { ?>


        <?php  } ?>
    </div>
    <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
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
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <!-- <script src="dist/js/demo.js"></script> -->
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>

    <script>
        <?php if ($user->data()->pswd == 0) { ?>
            $(window).on('load', function() {
                $("#change_password_n").modal({
                    backdrop: 'static',
                    keyboard: false
                }, 'show');
            });
        <?php } ?>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        $(document).ready(function() {
            $("#myInput11").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#inventory_report1 tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        $(document).ready(function() {
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#medication_list tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        function checkQuestionValue1(currentQuestion, elementToHide) {
            var currentQuestionInput = document.getElementById(currentQuestion);
            var elementToHide = document.getElementById(elementToHide);

            var questionValue = currentQuestionInput.value;

            if (questionValue === "1") {
                elementToHide.classList.remove("hidden");
            } else {
                elementToHide.classList.add("hidden");
            }
        }

        function checkQuestionValue96(currentQuestion, elementToHide) {
            var currentQuestionInput = document.getElementById(currentQuestion);
            var elementToHide = document.getElementById(elementToHide);

            var questionValue = currentQuestionInput.value;

            if (questionValue === "96") {
                elementToHide.classList.remove("hidden");
            } else {
                elementToHide.classList.add("hidden");
            }
        }

        function checkQuestionValue45(currentQuestion, elementToHide1, elementToHide2) {
            var currentQuestionInput = document.getElementById(currentQuestion);
            var elementToHide1 = document.getElementById(elementToHide1);
            var elementToHide2 = document.getElementById(elementToHide2);

            var questionValue = currentQuestionInput.value;

            if (questionValue === "4") {
                elementToHide1.classList.remove("hidden");
            } else if (questionValue === "5") {
                elementToHide2.classList.remove("hidden");

            } else {
                elementToHide1.classList.add("hidden");
                elementToHide2.classList.add("hidden");

            }
        }

        function toggleQuestionVisibility(currentQuestionId, nextQuestionId) {
            var currentQuestion = document.getElementById(currentQuestionId);
            var nextQuestion = document.getElementById(nextQuestionId);

            // Check if the current question has a value
            if (currentQuestion.value) {
                nextQuestion.classList.remove("hidden"); // Show the next question
            } else {
                nextQuestion.classList.add("hidden"); // Hide the next question
            }
        }


        function autocomplete(inp, arr) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value;
                /*close any already open lists of autocompleted values*/
                closeAllLists();
                if (!val) {
                    return false;
                }
                currentFocus = -1;
                /*create a DIV element that will contain the items (values):*/
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                /*append the DIV element as a child of the autocomplete container:*/
                this.parentNode.appendChild(a);
                /*for each item in the array...*/
                for (i = 0; i < arr.length; i++) {
                    /*check if the item starts with the same letters as the text field value:*/
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        /*make the matching letters bold:*/
                        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                        b.innerHTML += arr[i].substr(val.length);
                        /*insert a input field that will hold the current array item's value:*/
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        /*execute a function when someone clicks on the item value (DIV element):*/
                        b.addEventListener("click", function(e) {
                            /*insert the value for the autocomplete text field:*/
                            inp.value = this.getElementsByTagName("input")[0].value;
                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }
            });
            /*execute a function presses a key on the keyboard:*/
            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
                    currentFocus++;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 38) { //up
                    /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
                    currentFocus--;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 13) {
                    /*If the ENTER key is pressed, prevent the form from being submitted,*/
                    e.preventDefault();
                    if (currentFocus > -1) {
                        /*and simulate a click on the "active" item:*/
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                /*a function to classify an item as "active":*/
                if (!x) return false;
                /*start by removing the "active" class on all items:*/
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                /*add class "autocomplete-active":*/
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                /*a function to remove the "active" class from all autocomplete items:*/
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            /*execute a function when someone clicks in the document:*/
            document.addEventListener("click", function(e) {
                closeAllLists(e.target);
            });
        }

        /*An array containing all the country names in the world:*/
        // var countries = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Anguilla", "Antigua & Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia & Herzegovina", "Botswana", "Brazil", "British Virgin Islands", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central Arfrican Republic", "Chad", "Chile", "China", "Colombia", "Congo", "Cook Islands", "Costa Rica", "Cote D Ivoire", "Croatia", "Cuba", "Curacao", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands", "Faroe Islands", "Fiji", "Finland", "France", "French Polynesia", "French West Indies", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauro", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russia", "Rwanda", "Saint Pierre & Miquelon", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "St Kitts & Nevis", "St Lucia", "St Vincent", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor L'Este", "Togo", "Tonga", "Trinidad & Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks & Caicos", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Virgin Islands (US)", "Yemen", "Zambia", "Zimbabwe"];
        // var getUid = $(this).val();
        fetch('fetch_medications.php')
            .then(response => response.json())
            .then(data => {
                // Process the data received from the PHP script
                // console.log(data);
                autocomplete(document.getElementById("medication_name"), data);
            })
            .catch(error => {
                // Handle any errors that occurred during the fetch request
                console.error('Error:', error);
            });

        fetch('fetching_brand.php')
            .then(response => response.json())
            .then(data => {
                // Process the data received from the PHP script
                // console.log(data);
                autocomplete(document.getElementById("brand_id2"), data);
            })
            .catch(error => {
                // Handle any errors that occurred during the fetch request
                console.error('Error:', error);
            });


        fetch('fetching_batch.php')
            .then(response => response.json())
            .then(data => {
                // Process the data received from the PHP script
                // console.log(data);
                autocomplete(document.getElementById("batch_no"), data);
            })
            .catch(error => {
                // Handle any errors that occurred during the fetch request
                console.error('Error:', error);
            });

        fetch('fetching_manufacturer.php')
            .then(response => response.json())
            .then(data => {
                // Process the data received from the PHP script
                // console.log(data);
                autocomplete(document.getElementById("manufacturer"), data);
            })
            .catch(error => {
                // Handle any errors that occurred during the fetch request
                console.error('Error:', error);
            });

        /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
        // autocomplete(document.getElementById("myInput"), countries);
    </script>
</body>

</html>