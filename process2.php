<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
header('Content-Type: application/json');

$output = array();
$all_generic = $override->getCount1('clients','status',1, 'site_id', $user->data()->site_id);

print_r($all_generic);
// echo json_encode($all_generic);
