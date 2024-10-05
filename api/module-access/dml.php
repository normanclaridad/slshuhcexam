<?php
session_start();

require ('../../models/Module_access.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['code' => 4, 'message' => 'You are not authorized to access this page']);
    return;
}

$moduleAccess = new Module_access();

$userId = $_SESSION['SESS_ID'];
$dateTime = date('Y-m-d H:i:s');

$access = isset($_POST['access']) ? $_POST['access'] : '';
$id = isset($_POST['userid']) ? $_POST['userid'] : ''; 

//Delete access
$moduleAccess->deleteAll(" user_id = $id");

foreach($access as $row) {
    // print_r($row);
    $data = [
        'module_id' => $row['id'],
        'user_id' => $id,
        'insert' => $row['insert'],
        'view' => $row['view'],
        'delete' => $row['delete'],
        'update' => $row['update'],
        'export' => $row['export'],
        'created_by' => $userId,
        'created_at' => $dateTime, 
        'updated_by' => $userId,
        'updated_at' => $dateTime
    ];

    $resAccess = $moduleAccess->insertData($data);
}



echo json_encode(['code' => 0, 'message' => 'Record has been successully saved.']);
return;