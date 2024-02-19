<?php

$url = '';
if (isset($_GET['url'])) $url = $_GET['url'];

switch ($url) {
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
        include_once(__DIR__ . '/pages/admin/index.php');
        break;
    case 'admin/dashboard':
        include_once(__DIR__ . '/pages/admin/dashboard.php');
        break;
    case 'admin/lecturer-management':
        include_once(__DIR__ . '/pages/admin/lecturer-management.php');
        break;
    case 'admin/student-management':
        include_once(__DIR__ . '/pages/admin/student-management.php');
        break;
    case 'admin/subject-management':
        include_once(__DIR__ . '/pages/admin/subject-management.php');
        break;
    case 'admin/quiz-management':
        include_once(__DIR__ . '/pages/admin/quiz-management.php');
        break;

    default:
        include_once(__DIR__ . '/pages/404.php');
        break;
}

?>