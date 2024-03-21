<?php define('APP_RAN', 1);

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
    case 'signup':
        include_once(__DIR__ . '/pages/signup.php');
        break;
    case 'logout':
        include_once(__DIR__ . '/pages/logout.php');
        break;

    case 'dashboard':
        include_once(__DIR__ . '/pages/student/index.php');
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
    case 'admin/lecturers':
        if (isset($_POST['jsfetch'])) {
            include_once(__DIR__ . '/pages/admin/lecturer-management.php');
        } else {
            $routerPage = '/lecturer-management.php';
            include_once(__DIR__ . '/pages/admin/index.php');
        }
        break;
    case 'admin/students':
        if (isset($_POST['jsfetch'])) {
            include_once(__DIR__ . '/pages/admin/student-management.php');
        } else {
            $routerPage = '/student-management.php';
            include_once(__DIR__ . '/pages/admin/index.php');
        }
        break;
    case 'admin/subjects':
        if (isset($_POST['jsfetch'])) {
            include_once(__DIR__ . '/pages/admin/subject-management.php');
        } else {
            $routerPage = '/subject-management.php';
            include_once(__DIR__ . '/pages/admin/index.php');
        }
        break;
    case 'admin/quizzes':
        if (isset($_POST['jsfetch'])) {
            include_once(__DIR__ . '/pages/admin/quiz-management.php');
        } else {
            $routerPage = '/quiz-management.php';
            include_once(__DIR__ . '/pages/admin/index.php');
        }
        break;
    case 'admin/questions':
        if (isset($_POST['jsfetch'])) {
            include_once(__DIR__ . '/pages/admin/question-management.php');
        } else {
            $routerPage = '/question-management.php';
            include_once(__DIR__ . '/pages/admin/index.php');
        }
        break;

    default:
        include_once(__DIR__ . '/pages/404.php');
        break;
}

?>