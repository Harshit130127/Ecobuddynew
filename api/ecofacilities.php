<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/ecofacilitymodel.php';

header('Content-Type: application/json');

$model = new ecofacilitymodel();
echo json_encode($model->getFacilities());
?>