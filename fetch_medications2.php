<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

header('Content-Type: application/json');

$output = array();
$batches = $override->get('medications', 'status', 1);
?>
<option value="">Select Locations</option>
<?php foreach ($batches as $batch) {
?>
    <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
<?php }
