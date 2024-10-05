<?php
session_start();

require ('../../models/Menu.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['code' => 4, 'message' => 'You are not authorized to access this page']);
    return;
}

$menu = new Menu();

$id         = isset($_POST['id']) ? $_POST['id'] : ''; 
$actionType = isset($_POST['action_type']) ? $_POST['action_type']: ''; 
$name       = isset($_POST['name']) ? $_POST['name']: '';
$icon       = isset($_POST['icon']) ? $_POST['icon']: '';
$sort       = isset($_POST['sort']) ? $_POST['sort']: '';
$activekeyword  = isset($_POST['sort']) ? $_POST['active_keyword']: '';
$url            = isset($_POST['url']) ? $_POST['url']: '';
$status         = isset($_POST['status']) ? 'Y' : 'N';

$userId     = $_SESSION['SESS_ID'];

$dateTime   = date('Y-m-d H:i:s');
$data = [
    'name'  => $name,
    'url'   => $url,
    'icon'  => $icon,
    'sort'  => $sort,
    'active_keyword'=> $activekeyword,
    'is_active'     => $status,
    'updated_by'    => $userId,
    'updated_at'    => $dateTime
];

if($actionType == 'add') {
    $data = array_merge($data, ['created_at' => $dateTime, 'created_by' => $userId]);
}

if($actionType == 'add') {
    $where = " AND name = '$name' ";
    $checkMenu = $menu->getWhere($where);

    if(!empty($checkMenu)) {
        echo json_encode(['code' => 2, 'message' => "$name already exist in our database."]);
        return;
    }
}

//Add data
if($actionType == 'add') {
    $resMenu = $menu->insertData($data);
} else if($actionType == 'update') {
    $where = " id = $id";
    $resMenu = $menu->updateData($data, $where);
} else if($actionType == 'delete') {
    $resMenu = $menu->delete($id);
}

if(!$resMenu) {
    echo json_encode(['code' => 1, 'message' => 'Internal error. Please contact administrator.']);
    return;
}

$actionMessage = 'added';
if($actionType == 'update') {
    $actionMessage = 'updated';
} else if($actionType == 'delete') {
    $actionMessage = 'deleted';
}

echo json_encode(['code' => 0, 'message' => 'Record has been successully ' . $actionMessage]);
return;