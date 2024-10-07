<?php
session_start();

require ('../../models/Subjects.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['code' => 4, 'message' => 'You are not authorized to access this page']);
    return;
}

$subjects = new Subjects();

$id         = isset($_POST['id']) ? $_POST['id'] : ''; 
$actionType = isset($_POST['action_type']) ? $_POST['action_type']: ''; 
$courseNo   = isset($_POST['course_no']) ? $_POST['course_no']: '';
$name       = isset($_POST['name']) ? $_POST['name']: '';
$description= isset($_POST['description']) ? $_POST['description']: '';
$status         = isset($_POST['status']) ? 'Y' : 'N';

$userId     = $_SESSION['SESS_ID'];

$dateTime   = date('Y-m-d H:i:s');
$data = [
    'course_no'   => $courseNo,
    'name'  => $name,
    'description'  => $description,
    'is_active'     => $status,
    'updated_by'    => $userId,
    'updated_at'    => $dateTime
];

if($actionType == 'add') {
    $data = array_merge($data, ['created_at' => $dateTime, 'created_by' => $userId]);
}

if($actionType == 'add') {
    $where = " AND name = '$name' AND course_no = '$courseNo'";
    $validateDuplicate = $subjects->getWhere($where);

    if(!empty($validateDuplicate)) {
        echo json_encode(['code' => 2, 'message' => "$name already exist in our database."]);
        return;
    }
}

//Add data
if($actionType == 'add') {
    $resAction = $subjects->insertData($data);
} else if($actionType == 'update') {
    $where = " id = $id";
    $resAction = $subjects->updateData($data, $where);
} else if($actionType == 'delete') {
    $resAction = $subjects->delete($id);
}

if(!$resAction) {
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