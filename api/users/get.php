<?php
require ('../../models/Users.php');
require ('../../inc/Helpers.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['code' => 4, 'message' => 'You are not authorized to access this page']);
    return;
}

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$users = new Users();

//Helpers
$helpers = new Helpers();

// $tsag       = isset($_POST['tsag']) ? $_POST['tsag'] : ''; 

$btnAction  = isset($_POST['action']) ? $_POST['action'] : '';

$params = $columns = $totalRecords = $data = [];
 
$params = $_REQUEST;
$urlFormer = '';

if(in_array($btnAction, ['excel', 'print'])) {
    $_SESSION['SESS_GEN_TOKEN'] = rand(10000, 10000000);

    $urlFormer = 'token='. $_SESSION['SESS_GEN_TOKEN'];
}

$columns = [
        'u.first_name',
        'u.last_name', 
        'u.username', 
        'ur.name', 
        'u.status', 
        'u.created_at' ];

$whereCondition = $sqlTot = $sqlRec = '';

if( !empty($params['search']['value']) ) {
    $whereCondition .= " AND ";
    $whereCondition .= " ( u.first_name LIKE '%". $params['search']['value'] ."%'";
    $whereCondition .= " OR u.last_name LIKE '%". $params['search']['value'] ."%'";
    $whereCondition .= " OR u.username LIKE '%". $params['search']['value'] ."%'";
    $whereCondition .= " OR ur.name LIKE '%". $params['search']['value'] ."%')";

    $urlFormer .= '&search_value=' . $params['search']['value'];
}
$sortBy = 'u.id DESC';

if(isset($params['order'])) {
    $sortBy = $columns[$params['order'][0]['column']]."   ". $params['order'][0]['dir'];
    $urlFormer .= '&sort_by=' . $columns[$params['order'][0]['column']];
    $urlFormer .= '&sort_type=' . $params['order'][0]['dir'];
}

$start  = $params['start'];
$length = $params['length'];

//Get total
$totalRecords = $users->getTotal($whereCondition, $sortBy);

//Get all tsag
$resUsers = $users->getJoinWhere($whereCondition, $sortBy, $start, $length);
$data = [];
foreach($resUsers AS $row) {
    $encryptedId = $helpers->encryptDecrypt($row['id']);
    $action = '<a class="btn-edit" data-id="'. $row['id'] .'" data-first-name="'. $row['first_name']  .'" data-last-name="'. $row['last_name']  .'" data-username="'. $row['username']  .'" data-status="'. $row['status']  .'" data-user-role-id="'. $row['user_role_id']  .'"><i class="fa fa-edit"></i></a>';
    $action .= '&nbsp; <a class="btn-delete" data-id="'. $row['id'] .'" data-first-name="'. $row['first_name']  .'" data-last-name="'. $row['last_name']  .'"><i class="fa fa-trash"></i></a>';
    $action .= '&nbsp; <a class="btn-reset" data-id="'. $row['id'] .'" data-first-name="'. $row['first_name']  .'" data-last-name="'. $row['last_name']  .'"><i class="fa fa-key"></i></a>';
    $action .= '&nbsp; <a class="btn-access" data-id="'. $row['id'] .'" data-first-name="'. $row['first_name']  .'" data-last-name="'. $row['last_name']  .'"><i class="fa fa-lock"></i></a>';
    $status = '<i class="fa fa-times"></i>';
    if ($row['status'] == 'Y'){
        $status = '<i class = "fa fa-check"></i>';
    }
    $data[] = [
        $row['first_name'],
        $row['last_name'],
        $row['username'],
        $row['user_role_name'],
        $status,
        date('M d, Y h:i a', strtotime($row['created_at'])),
        $action,
    ];
}

$json_data = [
    "draw"            => intval( $params['draw'] ),   
    "recordsTotal"    => intval( $totalRecords ),  
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data
];

echo json_encode($json_data);