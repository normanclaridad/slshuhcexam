<?php
require ('../../models/Module_access.php');
require ('../../inc/Helpers.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['code' => 4, 'message' => 'You are not authorized to access this page']);
    return;
}

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$moduleAccess = new Module_access();

//Helpers
$helpers = new Helpers();

// $tsag       = isset($_POST['tsag']) ? $_POST['tsag'] : ''; 

$id  = isset($_POST['id']) ? $_POST['id'] : '';

$resModuleAccess = $moduleAccess->getAccess($id);

$data = [];

foreach($resModuleAccess as $row) {
    $data[] = [
        'name' => $row['name'],
        'code' => $row['code'],
        'user_id' => $row['user_id'],
        'id' => $row['id'],
        'insert' => !empty($row['insert']) ? $row['insert'] :  0,
        'view' => !empty($row['view']) ? $row['view'] :  0,
        'delete' => !empty($row['delete']) ? $row['delete'] :  0,
        'update' => !empty($row['update']) ? $row['update'] :  0,
        'export' => !empty($row['export']) ? $row['export'] :  0
    ];
}

echo json_encode($data);