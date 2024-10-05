<?php
require ($_SERVER['DOCUMENT_ROOT'] . '/models/Menu.php');
require ($_SERVER['DOCUMENT_ROOT'] . '/models/Sub_menu.php');

$uri = $_SERVER['REQUEST_URI'];

$menu       = new Menu();
$sub_menu   = new Sub_menu();

$resMenu = $menu->getWhere('', 'sort ASC');
$menu_list = [];
foreach($resMenu AS $row) {
    $resSubMenu = $sub_menu->getWhere(" AND menu_id = " . $row['id'], "name ASC");
    $menu_list[] = [
        'id'    => $row['id'],
        'name'  => $row['name'],
        'url'   => $row['url'],
        'icon'  => $row['icon'],
        'active_keyword'  => $row['active_keyword'],
        'sub_menu' => $resSubMenu
    ];
}
?>
<!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">                
            <?php foreach($menu_list AS $row): 
                    $checkmenu = $helpers->checkactivemenu($uri, $row['active_keyword']);
                    $active = ($checkmenu) ? 'active' : '';
                    $areaexpanded = ($checkmenu) ? 'aria-expanded="true"' : '';
                    $navCon = ($checkmenu) ? 'aria-expanded="true"' : '';
                    $navcontentshow = ($checkmenu) ? 'show' : '';

                    $baseUrl = BASE_URL;
                    if($row['active_keyword'] != 'home') {
                        $baseUrl = BASE_URL . $row['url'];
                    }
                    
                    if($row['active_keyword'] == 'home' && $uri == '/') {
                        $active = '';
                    }
                ?>
                <?php if(empty($row['sub_menu'])): ?>
                <li class="nav-item <?php echo $active ?>" data-active-keyword="<?php echo $row['active_keyword'] . '|' . $uri ?>">
                    <a class="nav-link" href="<?php echo $baseUrl ?>">
                        <span class="menu-title"><?php echo $row['name'] ?></span>
                        <i class="<?php echo $row['icon'] ?>"></i>
                    </a>
                </li>
                <?php else : ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active ?>" data-bs-toggle="collapse" href="#<?php echo $row['active_keyword'] ?>" aria-expanded="false" aria-controls="<?php echo $row['active_keyword'] ?>">
                        <span class="menu-title"><?php echo $row['name'] ?></span>
                        <i class="menu-arrow"></i>
                        <i class="<?php echo $row['icon'] ?>"></i>
                    </a>
                    <div class="collapse" id="<?php echo $row['active_keyword'] ?>">
                        <ul class="nav flex-column sub-menu">
                            <?php foreach($row['sub_menu'] AS $rows): 
                                $checkmenu = $helpers->checkactivemenu($uri, $rows['active_keyword']); 
                                $active = ($checkmenu) ? 'active' : '';
                            ?>
                            <li class="nav-item">
                                <a class="<?php echo $rows['icon'] ?> <?php echo $active ?>" href="<?php echo BASE_URL . '/' . $rows['url'] ?>">
                                    <?php echo $rows['name'] ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>
        <!-- partial -->