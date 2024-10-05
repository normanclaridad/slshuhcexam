<?php
session_start();
 
require ('../../../models/Sub_menu.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['code' => 4, 'message' => 'You are not authorized to access this page']);
    return;
}

$sub_menu = new Sub_menu();

$id         = isset($_POST['id']) ? $_POST['id'] : ''; 
$actionType = isset($_POST['action_type']) ? $_POST['action_type']: ''; 
$menu_id    = isset($_POST['menu_id']) ? $_POST['menu_id']: ''; 
$name       = isset($_POST['name']) ? $_POST['name']: '';
$url        = isset($_POST['url']) ? $_POST['url']: '';
$icon       = isset($_POST['icon']) ? $_POST['icon']: '';
$sort       = isset($_POST['sort']) ? $_POST['sort']: '';
$activekeyword  = isset($_POST['sort']) ? $_POST['active_keyword']: '';
$status         = isset($_POST['status']) ? 'Y' : 'N';
$userId         = $_SESSION['SESS_ID'];

$dateTime   = date('Y-m-d H:i:s');
$data = [
    'menu_id'   => $menu_id,
    'name'      => $name,
    'url'       => $url,
    'icon'      => $icon,
    'sort'      => $sort,
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
    $checkSub_menu = $sub_menu->getWhere($where);

    if(!empty($checkSub_menu)) {
        echo json_encode(['code' => 2, 'message' => "$name already exist in our database."]);
        return;
    }
}

//Add data
if($actionType == 'add') {
    $resSub_menu = $sub_menu->insertData($data);
} else if($actionType == 'update') {
    $where = " id = $id";
    $resSub_menu = $sub_menu->updateData($data, $where);
} else if($actionType == 'delete') {
    $resSub_menu = $sub_menu->delete($id);
}


if(!$resSub_menu) {
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