<?php
session_start();

require ('../../models/Users.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['code' => 4, 'message' => 'You are not authorized to access this page']);
    return;
}

$users = new Users();

$id = isset($_POST['id']) ? $_POST['id'] : ''; 
$actionType = isset($_POST['action_type']) ? $_POST['action_type']: ''; 
$firstName = isset($_POST['first_name']) ? $_POST['first_name']: '';
$lastName = isset($_POST['last_name']) ? $_POST['last_name']: '';
$userName = isset($_POST['username']) ? $_POST['username']: '';
$password = isset($_POST['password']) ? $_POST['password']: '';
$confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password']: '';
$userRoleId = isset($_POST['user_role_id']) ? $_POST['user_role_id']: '';
$status = isset($_POST['status']) ? 'Y': 'N';
$userId = $_SESSION['SESS_ID'];
$dateTime = date('Y-m-d H:i:s');

if(in_array($actionType, ['add', 'reset'])) {
    if($password != $confirmPassword) {
        echo json_encode(['code' => 3, 'message' => "Password and confirm password is not match!"]);
        return;
    }
}

$data = [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'user_role_id' => $userRoleId,
    'username' => $userName,    
    'status' => $status,
    'updated_by' => $userId,
    'updated_at' => $dateTime
];

if($actionType == 'add') {
    $data = array_merge($data, [
            'password' => hash('sha512', $password), 
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
} else if($actionType == 'reset') {
    $data =[
        'password' => hash('sha512', $password), 
        'updated_by' => $userId,
        'updated_at' => $dateTime
    ];
}

if(in_array($actionType, ['add', 'update'])) {
    
    $where = "AND username = '$userName'";
    
    if($actionType == 'update') {
        $where .= " AND id != $id";
    }

    $checkUser = $users->getWhere($where);

    if(!empty($checkUser)) {
        echo json_encode(['code' => 2, 'message' => "Username $userName already exist in our database."]);
        return;
    }
}

//Add data
if($actionType == 'add') {
    $resUser = $users->insertData($data);
} else {
    $where = " id = $id";
    $resUser = $users->updateData($data, $where);
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