<?php
session_start();

require ('../../models/Questions.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['code' => 4, 'message' => 'You are not authorized to access this page']);
    return;
}

$questions = new Questions();

$id         = isset($_POST['id']) ? $_POST['id'] : ''; 
$actionType = isset($_POST['action_type']) ? $_POST['action_type']: ''; 
$subjectId   = isset($_POST['subject_id']) ? $_POST['subject_id']: '';
$question       = isset($_POST['question']) ? $_POST['question']: '';
$status         = isset($_POST['status']) ? 'Y' : 'N';

$userId     = $_SESSION['SESS_ID'];

$dateTime   = date('Y-m-d H:i:s');
$data = [
    'subject_id'   => $subjectId,
    'question'  => $question,
    'is_active'     => $status,
    'updated_by'    => $userId,
    'updated_at'    => $dateTime
];

if($actionType == 'add') {
    $data = array_merge($data, ['created_at' => $dateTime, 'created_by' => $userId]);
}

if($actionType == 'add') {
    // $where = " AND name = '$name' AND course_no = '$courseNo'";
    // $validateDuplicate = $subjects->getWhere($where);

    // if(!empty($validateDuplicate)) {
    //     echo json_encode(['code' => 2, 'message' => "$name already exist in our database."]);
    //     return;
    // }
}

//Add data
if($actionType == 'add') {
    $resAction = $questions->insertData($data);
} else if($actionType == 'update') {
    $where = " id = $id";
    $resAction = $questions->updateData($data, $where);
} else if($actionType == 'delete') {
    $resAction = $questions->delete($id);
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