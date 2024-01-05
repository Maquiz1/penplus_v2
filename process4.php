<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
header('Content-Type: application/json');

// $output = array();
// $all_generic = $override->getCount1('clients','status',1, 'site_id', $user->data()->site_id);

// print_r($all_generic);
// echo json_encode($all_generic);



// Simulated data for the last 7 days (replace this with your actual data retrieval logic)
// For demonstration purposes, assuming random data
$today = time();
$sevenDaysData = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days", $today)); // Generate date for the last 7 days
    $value = rand(10, 100); // Replace this with your actual data retrieval logic for each day
    $sevenDaysData[$day] = $value;
}

// Prepare the data for the last 7 days in an array
$chartData = [];
$labels = [];
$values = [];

// Loop through the last 7 days' data
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days", $today));
    $labels[] = $day;
    $values[] = isset($sevenDaysData[$day]) ? $sevenDaysData[$day] : 0;
}

$chartData['labels'] = $labels;
$chartData['values'] = $values;

// Convert PHP array to JSON
$chartDataJSON = json_encode($chartData);

// Output JSON data
echo $chartDataJSON;
?>
