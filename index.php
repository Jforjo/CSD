<?

switch ($_GET['url']) {
    case '':
        include_once(__DIR__ . '/pages/index.php');
        break;
    case 'home':
        include_once(__DIR__ . '/pages/home.php');
        break;
    case 'login':
        include_once(__DIR__ . '/pages/login.php');
        break;
    case 'admin':
        include_once(__DIR__ . '/pages/admin-dashboard.php');
        break;
    default:
        include_once(__DIR__ . '/pages/404.php');
        break;
}

?>