<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
header('Content-Type: application/json');

$output = array();
$all_generic = $override->getcolumns('clients', 'id', 'clinic_date','firstname', 'age');
foreach ($all_generic as $name) {
    $output[] = $name;
}
echo json_encode($output);








