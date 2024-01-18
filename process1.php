<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
header('Content-Type: application/json');


if ($_GET['content'] == 'all') {
    if ($_GET['getUid']) {
        $output = array();
        $query = $override->getMonthCount();
        foreach ($query as $data) {
            $output[] = $data;
        }
        // // Convert PHP array to JSON
        $chartDataJSON = json_encode($output);

        // Output JSON data
        echo $chartDataJSON;
    }
} elseif ($_GET['content'] == 'site') {
    if ($_GET['getUid']) {
        $output = array();
        $query = $override->getMonthCountSite();
        foreach ($query as $data) {
            $site = $row['site_id'];
            $month = $row['monthname'];
            $countData = $row['count_data'];

            $output[$site][] = [
                'monthname' => $month,
                'count_data' => $countData,
            ];

        }
        // // Convert PHP array to JSON
        $chartDataJSON = json_encode($output);

        // Output JSON data
        echo $chartDataJSON;
    }
} elseif ($_GET['content'] == 'diseases') {
    if ($_GET['getUid']) {
        $output = array();
        $query = $override->getMonthCount();
        foreach ($query as $data) {
            $output[] = $data;
        }
        // // Convert PHP array to JSON
        $chartDataJSON = json_encode($output);

        // Output JSON data
        echo $chartDataJSON;
    }
}

// $query = $override->getMonthCountSite();
// print_r($query);
