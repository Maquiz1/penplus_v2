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
// echo json_encode($all_generic)


// $result = $override->getNews7Month();

// $data = array();
// while ($row = $result) {
//     $data[$row['month']] = $row['total_value'];
// }


$chartData = [];
$labels = [];
$values = [];

$output = array();
$result = $override->getNews7Month();
foreach ($result as $name) {
    $labels[] = $name['created_on'];
    $value = $override->getNews7Month2();
    $sevenDaysData[$day] = $value;
    $values[] = $name[''];

}

$chartData['labels'] = $labels;
$chartData['values'] = $values;
$chartData['total_count'] = $total_count;

// Convert PHP array to JSON
$chartDataJSON = json_encode($chartData);

// Output JSON data
echo $chartDataJSON;
?>


<script>
    var labels = [];
    var values = [];

    <?php
    // foreach ($data as $month => $value) {
    //     echo "labels.push('$month');";
    //     echo "values.push($value);";
    // }
    ?>
</script>