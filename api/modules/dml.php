<?php
session_start();

require ('../../models/Modules.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['code' => 4, 'message' => 'You are not authorized to access this page']);
    return;
}

$modules = new Modules();

$id = isset($_POST['id']) ? $_POST['id'] : ''; 
$actionType = isset($_POST['action_type']) ? $_POST['action_type']: ''; 
$name = isset($_POST['name']) ? $_POST['name']: '';
$code = isset($_POST['module_code']) ? $_POST['module_code']: '';
$status = isset($_POST['status']) ? 'Y': 'N';
$userId = $_SESSION['SESS_ID'];
$dateTime = date('Y-m-d H:i:s');

$data = [
    'name' => $name,
    'code' => $code,
    'status' => $status,
    'updated_by' => $userId,
    'updated_at' => $dateTime
];

if($actionType == 'add') {
    $data = array_merge($data, [
            'created_at' => $dateTime, 
            'created_by' => $userId
        ]
    ); 
} else if($actionType == 'delete') {
    $data = [
        'status' => 'D',        
        'updated_by' => $userId,
        'updated_at' => $dateTime
    ];
}

if(in_array($actionType, ['add', 'update'])) {    
    $where = "AND name = '". $name ."'";
    if($actionType == 'update') {
        $where .= " AND id != $id";
    }

    $checkModule = $modules->getWhere($where);

    if(!empty($checkModule)) {
        echo json_encode(['code' => 2, 'message' => "Module $name already exist in our database."]);
        return;
    }
}

//Add data
if($actionType == 'add') {
    $resUser = $modules->insertData($data);
} else {
    $where = " id = $id";
    $resUser = $modules->updateData($data, $where);
}

if(!$resUser) {
    echo json_encode(['code' => 1, 'message' => 'Internal error. Please contact administrator.']);
    return;
}

$actionMessage = 'added';
if($actionType == 'update') {
    $actionMessage = 'updated';
} else if($actionType == 'delete') {
    $actionMessage = 'deleted';
} else if($actionType == 'reset') {
    $actionMessage = 'reseted';
}

echo json_encode(['code' => 0, 'message' => 'Record has been successully ' . $actionMessage]);
return;