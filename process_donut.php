<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
// header('Content-Type: application/json');

// $output = array();
// $all_generic = $override->getCount1('clients','status',1, 'site_id', $user->data()->site_id);

// print_r($all_generic);
// echo json_encode($all_generic);

// $total = $override->getCount1('clients','status',1, 'site_id', $user->data()->site_id);
// $total_count = $override->getCount('clients', 'status', 1);


// Simulated data for the last 7 months (replace this with your actual data retrieval logic)
// For demonstration purposes, assuming random data
$today = time();
$sevenMonthsData = [];
for ($i = 6; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months", $today)); // Generate month for the last 7 months
    $value = rand(100, 500); // Replace this with your actual data retrieval logic for each month
    // $value = $override->getNews('clients', 'status', 1, 'clinic_date', $month)[0];
    $sevenMonthsData[$month] = $value;
}

// Prepare the data for the last 7 months in an array
$chartData = [];
$labels = [];
$values = [];

// Loop through the last 7 months' data
for ($i = 6; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months", $today));
    $monthName = date('F Y', strtotime("-$i months", $today)); // Format month for display
    $labels[] = $monthName;
    $values[] = isset($sevenMonthsData[$month]) ? $sevenMonthsData[$month] : 0;
}

$chartData['labels'] = $labels;
$chartData['values'] = $values;
$chartData['total_count'] = $total_count;

// Convert PHP array to JSON
$chartDataJSON = json_encode($chartData);

// Output JSON data
echo $chartDataJSON;
?>

