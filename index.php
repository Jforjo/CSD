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
        $routerPage = '/dashboard.php';
        include_once(__DIR__ . '/pages/admin/index.php');
        break;
    case 'admin/dashboard':
        if (isset($_POST['jsfetch'])) {
            include_once(__DIR__ . '/pages/admin/dashboard.php');
        } else {
            $routerPage = '/dashboard.php';
            include_once(__DIR__ . '/pages/admin/index.php');
        }
        break;
    case 'admin/lecturer-management':
        if (isset($_POST['jsfetch'])) {
            include_once(__DIR__ . '/pages/admin/lecturer-management.php');
        } else {
            $routerPage = '/lecturer-management.php';
            include_once(__DIR__ . '/pages/admin/index.php');
        }
        break;
    case 'admin/student-management':
        if (isset($_POST['jsfetch'])) {
            include_once(__DIR__ . '/pages/admin/student-management.php');
        } else {
            $routerPage = '/student-management.php';
            include_once(__DIR__ . '/pages/admin/index.php');
        }
        break;
    case 'admin/subject-management':
        if (isset($_POST['jsfetch'])) {
            include_once(__DIR__ . '/pages/admin/subject-management.php');
        } else {
            $routerPage = '/subject-management.php';
            include_once(__DIR__ . '/pages/admin/index.php');
        }
        break;
    case 'admin/quiz-management':
        if (isset($_POST['jsfetch'])) {
            include_once(__DIR__ . '/pages/admin/quiz-management.php');
        } else {
            $routerPage = '/quiz-management.php';
            include_once(__DIR__ . '/pages/admin/index.php');
        }
        break;

    default:
        include_once(__DIR__ . '/pages/404.php');
        break;
}

?>