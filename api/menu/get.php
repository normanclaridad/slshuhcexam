<?php

require ('../../models/Menu.php'); 
require ('../../inc/Helpers.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['code' => 4, 'message' => 'You are not authorized to access this page']);
    return;
}

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$menu = new Menu();

//Helpers
$helpers = new Helpers();

$btnAction  = isset($_POST['action']) ? $_POST['action'] : '';

$params = $columns = $totalRecords = $data = [];
 
$params = $_REQUEST;
$urlFormer = '';

if(in_array($btnAction, ['excel', 'print'])) {
    $_SESSION['SESS_GEN_TOKEN'] = rand(10000, 10000000);

    $urlFormer = 'token='. $_SESSION['SESS_GEN_TOKEN'];
}

$columns = ['name', 'url', 'status', 'created_at'];

$whereCondition = $sqlTot = $sqlRec = '';

if( !empty($params['search']['value']) ) {
    $whereCondition .= " AND ";
    $whereCondition .= " ( name LIKE '%". $params['search']['value'] ."%' )";

    $urlFormer .= '&search_value=' . $params['search']['value'];
}

$sortBy = 'id DESC';

if(isset($params['order'])) {
    $sortBy = $columns[$params['order'][0]['column']]."   ". $params['order'][0]['dir'];
    $urlFormer .= '&sort_by=' . $columns[$params['order'][0]['column']];
    $urlFormer .= '&sort_type=' . $params['order'][0]['dir'];
}

$start  = $params['start'];
$length = $params['length'];

//Get total
$totalRecords = $menu->getTotal($whereCondition, $sortBy);

//Get all tsag
$results = $menu->getJoinWhere($whereCondition, $sortBy, $start, $length);
$data = [];
foreach($results AS $row) {
    $encryptedId = $helpers->encryptDecrypt($row['id']);
    $menuUrl = $protocol . $_SERVER['HTTP_HOST'] . '/views/user-roles/farmers.php?id=' . $encryptedId;
    $action = '<a class="" href="/views/settings/menu/sub-menu/?id='.$encryptedId.'"><i class="fa fa-plus-square-o"></i></a>';
    $action .= '&nbsp; <a class="btn-edit" data-id="'. $row['id'] .'" data-name="'. $row['name'] .'" data-url="'. $row['url']  .'" data-icon="'. $row['icon'] .'" data-sort="'. $row['sort'] .'" data-status="'. $row['is_active'] .'" data-active-keyword="'. $row['active_keyword'] .'">
                    <i class="fa fa-pencil-square-o"></i>
                </a>';
    $action .= '&nbsp; <a class="btn-delete" data-id="'. $row['id'] .'" data-name="'. $row['name'] .'"><i class="fa fa-trash-o"></i></a>';
    
    $status = '';
    if($row['is_active'] == 'Y') {
        $status = '<i class="fa fa-check"></i>';
    }
    if($row['is_active'] == 'N') {
        $status = '<i class="fa fa-times"></i>';
    }

    $data[] = [
        $row['name'],
        $row['url'],
        $status,
        date('M d, Y h:i a', strtotime($row['created_at'])),
        $action,
    ];
}

$baseUrl = $protocol . $_SERVER['HTTP_HOST'] . '/views/app-settings/'.$btnAction.'.php?' . $urlFormer;

$json_data = [
    "draw"            => intval( $params['draw'] ),   
    "recordsTotal"    => intval( $totalRecords ),  
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data,
    'url'             => $baseUrl
];

echo json_encode($json_data);