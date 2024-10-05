<?php
session_start();

require ('../models/Users.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['code' => 4, 'message' => 'You are not authorized to access this page']);
    return;
}

$users = new Users();

$userName = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if(empty($userName) || empty($password))
{
    echo json_encode(['code' => 3, 'message' => 'Email and password are required.']);
    return;
}

$password = hash('sha512', $password);

$resCheckUser = $users->checkUser($userName, $password);

if(empty($resCheckUser)) 
{
    echo json_encode(['code' => 2, 'message' => 'Invalid username and password.']);
    return;
}

$_SESSION['SESS_AUTH_EXAM'] = TRUE;
$_SESSION['SESS_ID'] = $resCheckUser['id'];
$_SESSION['SESS_FIRST_NAME'] = $resCheckUser['first_name'];
$_SESSION['SESS_LAST_NAME'] = $resCheckUser['last_name'];
$_SESSION['SESS_USER_ROLE_NAME'] = $resCheckUser['user_role_name'];

 
echo json_encode(['code' => 0, 'message' => 'Successful.']);
return;