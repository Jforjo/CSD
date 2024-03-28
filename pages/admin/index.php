<?php session_start();
// Checks if the user is logged in
if (!isset($_SESSION['userID'])) header("Location: /");
require_once(__DIR__ . '/../../php/dbfuncs.php');
// Checks if their session id is valid
if (!CheckUserIDExists($_SESSION['userID'])) {
    DestroySession();
    header("Location: /");
}
// Checks if their account is active
if (GetUserState($_SESSION['userID']) !== "active") {
    DestroySession();
    die(json_encode(array(
        "type" => "refresh"
    )));
}
// Checks if they have the correct permissions
$role = GetUserRole($_SESSION['userID']);
if (!($role === "lecturer" || $role === "admin")) {
    header("Location: /");
}

// Bruteforce...
$pageData = array(
    "title" => "Admin | Dashboard",
    "header-title" => "Dashboard",
    "pos" => "0"
);
if ($role === 'admin') {
    if ($routerPage === '/lecturer-management.php') $pageData = array(
        "title" => "Admin | Lecturer Management",
        "header-title" => "Lecturer Management",
        "pos" => "1"
    );
    else if ($routerPage === '/student-management.php') $pageData = array(
        "title" => "Admin | Student Management",
        "header-title" => "Student Management",
        "pos" => "2"
    );
    else if ($routerPage === '/quiz-management.php') $pageData = array(
        "title" => "Admin | Quiz Management",
        "header-title" => "Quiz Management",
        "pos" => "3"
    );
    else if ($routerPage === '/question-management.php') $pageData = array(
        "title" => "Admin | Question Management",
        "header-title" => "Question Management",
        "pos" => "4"
    );
    else if ($routerPage === '/subject-management.php') $pageData = array(
        "title" => "Admin | Subject Management",
        "header-title" => "Subject Management",
        "pos" => "5"
    );
} else {
    if ($routerPage === '/student-management.php') $pageData = array(
        "title" => "Admin | Student Management",
        "header-title" => "Student Management",
        "pos" => "1"
    );
    else if ($routerPage === '/quiz-management.php') $pageData = array(
        "title" => "Admin | Quiz Management",
        "header-title" => "Quiz Management",
        "pos" => "2"
    );
    else if ($routerPage === '/question-management.php') $pageData = array(
        "title" => "Admin | Question Management",
        "header-title" => "Question Management",
        "pos" => "3"
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageData['title']; ?></title>
    <script defer src="/assets/js/script.js"></script>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav>
        <i class="glow"></i>
        <div class="top">
            <a>
                <span>Qu</span><i>?</i><span>z</span>
            </a>
        </div>
        <ul>
            <i class="indicator" style="--pos:<?php echo $pageData['pos']; ?>;"></i>
            <li <?php if ($routerPage == '/dashboard.php') echo 'class="active"'; ?>>
                <a data-link="admin/dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="<?php if ($routerPage == '/dashboard.php') echo 'nc-int-icon-state-b'; ?>">
                        <g stroke-linecap="square" transform="translate(0.5 0.5)" stroke-miterlimit="10" fill="none" stroke="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper">
                            <g class="nc-int-icon js-nc-int-icon">
                                <g class="nc-int-icon-a">
                                    <rect x="6" y="2" width="22" height="34"></rect>
                                    <rect x="6" y="44" width="22" height="18"></rect>
                                    <rect x="36" y="2" width="22" height="18"></rect>
                                    <rect x="36" y="28" width="22" height="34"></rect>
                                </g>
                                <g class="nc-int-icon-b">
                                    <path fill="currentColor" d="M27,36H6c-0.552,0-1-0.448-1-1V2c0-0.552,0.448-1,1-1h21c0.552,0,1,0.448,1,1v33C28,35.552,27.552,36,27,36z " stroke="none"></path>
                                    <path d="M27,63H6c-0.552,0-1-0.448-1-1V45c0-0.552,0.448-1,1-1h21c0.552,0,1,0.448,1,1v17 C28,62.552,27.552,63,27,63z" stroke="none" fill="currentColor"></path>
                                    <path d="M58,20H37c-0.552,0-1-0.448-1-1V2c0-0.552,0.448-1,1-1h21c0.552,0,1,0.448,1,1v17 C59,19.552,58.552,20,58,20z" stroke="none" fill="currentColor"></path>
                                    <path fill="currentColor" d="M58,63H37c-0.552,0-1-0.448-1-1V29c0-0.552,0.448-1,1-1h21c0.552,0,1,0.448,1,1v33 C59,62.552,58.552,63,58,63z" stroke="none"></path>
                                </g>
                            </g>
                            <style fill="currentColor" stroke="none">.nc-int-icon{position:relative;}.nc-int-icon-b{position: absolute;top: calc(50% - 0.5em);left: calc(50% - 0.5em);opacity: 0;}.nc-int-icon-state-b .nc-int-icon-a{opacity: 0;}.nc-int-icon-state-b .nc-int-icon-b{opacity: 1;}</style>
                        </g>
                    </svg>
                    <span>
                        Dashboard
                    </span>
                </a>
            </li>
            <?php if ($role === "admin") { ?>
            <li <?php if ($routerPage == '/lecturer-management.php') echo 'class="active"'; ?>>
                <a data-link="admin/lecturers">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="<?php if ($routerPage == '/lecturer-management.php') echo 'nc-int-icon-state-b'; ?>">
                        <g stroke-linecap="square" transform="translate(0.5 0.5)" fill="none" stroke="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper" stroke-miterlimit="10">
                            <g class="nc-int-icon js-nc-int-icon">
                                <g class="nc-int-icon-a">
                                    <circle cx="14" cy="8" r="6"></circle>
                                    <polyline points="26 8 59 8 59 46 28 46"></polyline>
                                    <line x1="45" y1="46" x2="55" y2="62" data-cap="butt" stroke-linecap="butt"></line>
                                    <path d="M40.44,19H11a6,6,0,0,0-6,6V58.831A3.115,3.115,0,0,0,7.84,62,3,3,0,0,0,11,59V42h5V58.831A3.115,3.115,0,0,0,18.84,62,3,3,0,0,0,22,59V25l18.412-.877a2.666,2.666,0,0,0,2.579-2.342A2.56,2.56,0,0,0,40.44,19Z"></path>
                                    <line x1="35" y1="8" x2="35" y2="3"></line>
                                </g>
                                <g class="nc-int-icon-b">
                                    <path d="M48.082,49H44.543l7.685,12.3a1.5,1.5,0,0,0,2.544-1.59Z" stroke="none" fill="currentColor"></path>
                                    <circle cx="12" cy="8" r="6" fill="currentColor" stroke="none"></circle>
                                    <path d="M58,7H36V3a1,1,0,0,0-2,0V7H19.931a7.876,7.876,0,0,1-2.657,7H39.268a4.672,4.672,0,0,1,4.7,4.018,4.552,4.552,0,0,1-4.318,5.091L22,23.911V47H58a2,2,0,0,0,2-2V9A2,2,0,0,0,58,7Z" stroke="none" fill="currentColor"></path>
                                    <path d="M39.443,16H10a6,6,0,0,0-6,6V58.935A3.065,3.065,0,0,0,7.065,62h0a3.065,3.065,0,0,0,3.062-2.932L11,39h2l.873,20.068A3.065,3.065,0,0,0,16.935,62h0A3.065,3.065,0,0,0,20,58.935V22l19.559-.889A2.556,2.556,0,0,0,42,18.557h0A2.557,2.557,0,0,0,39.443,16Z" fill="currentColor" stroke="none"></path>
                                </g>
                            </g>
                            <style fill="currentColor" stroke="none">.nc-int-icon{position:relative;}.nc-int-icon-b{position: absolute;top: calc(50% - 0.5em);left: calc(50% - 0.5em);opacity: 0;}.nc-int-icon-a,.nc-int-icon-b{transform-origin:center center;}.nc-int-icon-scale .nc-int-icon-a,.nc-int-icon-scale .nc-int-icon-b{transition: opacity 0s calc(var(--animation-duration)/2), transform var(--animation-duration);}.nc-int-icon-scale .nc-int-icon-b{transform: scale(0.8);}.nc-int-icon-state-b .nc-int-icon-a{opacity: 0;}.nc-int-icon-state-b .nc-int-icon-b{opacity: 1;}.nc-int-icon-scale.nc-int-icon-state-b .nc-int-icon-a{transform: scale(0.8);}.nc-int-icon-scale.nc-int-icon-state-b .nc-int-icon-b{transform: scale(1);}</style>
                        </g>
                    </svg>
                    <span>
                        Lecturers
                    </span>
                </a>
            </li>
            <?php } ?>
            <li <?php if ($routerPage == '/student-management.php') echo 'class="active"'; ?>>
                <a data-link="admin/students">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="<?php if ($routerPage == '/student-management.php') echo 'nc-int-icon-state-b'; ?>">
                        <g stroke-linecap="square" transform="translate(0.5 0.5)" fill="none" stroke="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper" stroke-miterlimit="10">
                            <g class="nc-int-icon js-nc-int-icon">
                                <g class="nc-int-icon-a">
                                    <path d="M48,57H62V47.708a4,4,0,0,0-2.514-3.714L48.1,39.438a2,2,0,0,1-1.241-1.609l-.468-3.744A11,11,0,0,0,53,24V14.725a6,6,0,0,0-6-6L45.447,5.618a1.159,1.159,0,0,0-1.387-.557L36.654,7.705A7.6,7.6,0,0,0,34.413,9"></path>
                                    <path d="M39.486,45.994,28.1,41.438a2,2,0,0,1-1.241-1.609l-.462-3.693A17.529,17.529,0,0,0,37,36a27.345,27.345,0,0,1-4-14V21a11,11,0,0,0-22,0v1A27.345,27.345,0,0,1,7,36a17.529,17.529,0,0,0,10.608.136l-.462,3.693a2,2,0,0,1-1.241,1.609L4.514,45.994A4,4,0,0,0,2,49.708V57H42V49.708A4,4,0,0,0,39.486,45.994Z"></path>
                                </g>
                                <g class="nc-int-icon-b">
                                    <path d="M59.856,43.065,48.468,38.51a1,1,0,0,1-.622-.8l-.378-3.025A12,12,0,0,0,54,24V14.725a7.009,7.009,0,0,0-6.368-6.972L46.341,5.172a2.157,2.157,0,0,0-2.617-1.053L36.316,6.764a9.478,9.478,0,0,0-4.191,3.2A14.946,14.946,0,0,1,37,21v1a23.2,23.2,0,0,0,3.43,11.941A4,4,0,0,1,38.262,39.8a21.294,21.294,0,0,1-2.067.575l4.776,1.91A7.962,7.962,0,0,1,46,49.708V57a3.939,3.939,0,0,1-.142,1H62a1,1,0,0,0,1-1V47.708A4.975,4.975,0,0,0,59.856,43.065Z" stroke="none" fill="currentColor"></path>
                                    <path d="M42,58H2a1,1,0,0,1-1-1V49.708a4.975,4.975,0,0,1,3.144-4.643L15.533,40.51a1,1,0,0,0,.621-.806l.279-2.226a18.784,18.784,0,0,1-9.749-.53,1,1,0,0,1-.541-1.463A26.2,26.2,0,0,0,10,22V21a12,12,0,0,1,24,0v1a26.2,26.2,0,0,0,3.857,13.485,1,1,0,0,1-.541,1.463,18.774,18.774,0,0,1-9.749.53l.279,2.227a1,1,0,0,0,.622.8l11.388,4.555A4.975,4.975,0,0,1,43,49.708V57A1,1,0,0,1,42,58Z" fill="currentColor" stroke="none"></path>
                                </g>
                            </g>
                            <style fill="currentColor" stroke="none">.nc-int-icon{position:relative;}.nc-int-icon-b{position: absolute;top: calc(50% - 0.5em);left: calc(50% - 0.5em);opacity: 0;}.nc-int-icon-state-b .nc-int-icon-a{opacity: 0;}.nc-int-icon-state-b .nc-int-icon-b{opacity: 1;}</style>
                        </g>
                    </svg>
                    <span>
                        Students
                    </span>
                </a>
            </li>
            <li <?php if ($routerPage == '/quiz-management.php') echo 'class="active"'; ?>>
                <a data-link="admin/quizzes">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="<?php if ($routerPage == '/quiz-management.php') echo 'nc-int-icon-state-b'; ?>">
                        <g stroke-linecap="square" transform="translate(0.5 0.5)" fill="none" stroke="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper" stroke-miterlimit="10">
                            <g class="nc-int-icon js-nc-int-icon">
                                <g class="nc-int-icon-a">
                                    <rect x="5" y="5" width="21" height="21"></rect>
                                    <rect x="38" y="38" width="21" height="21"></rect>
                                    <rect x="5" y="38" width="21" height="21"></rect>
                                    <polyline points="38 15 46 23 61 5"></polyline>
                                </g>
                                <g class="nc-int-icon-b">
                                    <rect x="4" y="4" width="23" height="23" rx="2" ry="2" fill="currentColor" stroke="none"></rect>
                                    <rect x="37" y="37" width="23" height="23" rx="2" ry="2" fill="currentColor" stroke="none"></rect>
                                    <rect x="4" y="37" width="23" height="23" rx="2" ry="2" fill="currentColor" stroke="none"></rect>
                                    <path d="M60.62,4.215c-.434-.342-1.062-.268-1.405,.165l-14.303,18.117-7.205-7.205c-.391-.391-1.023-.391-1.414,0s-.391,1.023,0,1.414l8,8c.188,.188,.442,.293,.707,.293,.02,0,.039,0,.059-.002,.285-.017,.55-.154,.727-.378L60.785,5.62c.342-.434,.268-1.062-.165-1.404Z" stroke="none" fill="currentColor"></path>
                                </g>
                            </g>
                            <style fill="currentColor" stroke="none">.nc-int-icon{position:relative;}.nc-int-icon-b{position: absolute;top: calc(50% - 0.5em);left: calc(50% - 0.5em);opacity: 0;}.nc-int-icon-state-b .nc-int-icon-a{opacity: 0;}.nc-int-icon-state-b .nc-int-icon-b{opacity: 1;}</style>
                        </g>
                    </svg>
                    <span>
                        Quizzes
                    </span>
                </a>
            </li>
            <li <?php if ($routerPage == '/question-management.php') echo 'class="active"'; ?>>
                <a data-link="admin/questions">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="<?php if ($routerPage == '/question-management.php') echo 'nc-int-icon-state-b'; ?>">
                        <g stroke-linecap="square" stroke-miterlimit="10" fill="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper">
                            <g class="nc-int-icon js-nc-int-icon">
                                <g class="nc-int-icon-a">
                                    <rect x="5" y="5" width="54" height="54" fill="none" stroke="currentColor"></rect>
                                    <circle data-color="color-2" cx="32" cy="47" r="1" fill="none" stroke="currentColor"></circle>
                                    <path data-color="color-2" d="M25,16.551c5.481-2.425,12.575-2.136,15,1.787s.748,8.483-3.4,12S32,36.232,32,39" fill="none" stroke="currentColor"></path>
                                </g>
                                <g class="nc-int-icon-b">
                                    <path d="M54,4H10c-3.314,0-6,2.686-6,6V54c0,3.314,2.686,6,6,6H54c3.314,0,6-2.686,6-6V10c0-3.314-2.686-6-6-6ZM30,51c-1.657,0-3-1.343-3-3s1.343-3,3-3,3,1.343,3,3-1.343,3-3,3Zm12.039-28.968c.066,2.126-.62,4.206-1.939,5.875-1.262,1.449-2.642,2.791-4.126,4.013-1.304,1.014-2.438,2.229-3.359,3.6-.531,1.125-.818,2.345-.847,3.581-.012,.534-.459,.953-.993,.953h-1.262c-.559,0-1-.459-1.003-1.018-.01-1.73,.312-3.449,.95-5.063,.839-1.553,1.986-2.919,3.371-4.013,3.878-3.279,5.208-4.722,5.208-7.875,0-3.718-2.627-5.359-6.39-5.359-2.515,.016-4.991,.589-7.251,1.672-.522,.25-1.145,.042-1.376-.488l-.677-1.55c-.213-.488-.012-1.07,.468-1.299,2.835-1.354,5.939-2.06,9.088-2.061,3.2,0,5.691,.788,7.472,2.365,1.836,1.715,2.814,4.159,2.667,6.667Z" fill="currentColor"></path>
                                </g>
                            </g>
                            <style fill="currentColor">.nc-int-icon{position:relative;}.nc-int-icon-b{position: absolute;top: calc(50% - 0.5em);left: calc(50% - 0.5em);opacity: 0;}.nc-int-icon-state-b .nc-int-icon-a{opacity: 0;}.nc-int-icon-state-b .nc-int-icon-b{opacity: 1;}</style>
                        </g>
                    </svg>
                    <span>
                        Questions
                    </span>
                </a>
            </li>
            <?php if ($role === "admin") { ?>
            <li <?php if ($routerPage == '/subject-management.php') echo 'class="active"'; ?>>
                <a data-link="admin/subjects">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="<?php if ($routerPage == '/subject-management.php') echo 'nc-int-icon-state-b'; ?>">
                        <g stroke-linecap="square" stroke-miterlimit="10" fill="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper">
                            <g class="nc-int-icon js-nc-int-icon">
                                <g class="nc-int-icon-a">
                                    <path data-cap="butt" fill="none" stroke="currentColor" d="M12,22v20c0,4.971,8.954,9,20,9 c11.046,0,20-4.029,20-9V22" stroke-linecap="butt"></path>
                                    <polyline data-cap="butt" data-color="color-2" fill="none" stroke="currentColor" points="44,54 49,61 39,61 44,54 44,26 " stroke-linecap="butt"></polyline>
                                    <polygon fill="none" stroke="currentColor" points="32,32 4,18 32,4 60,18 "></polygon>
                                </g>
                                <g class="nc-int-icon-b">
                                    <path fill="currentColor" d="M62.423,17.094l-30-14c-0.268-0.125-0.578-0.125-0.846,0l-30,14C1.225,17.258,1,17.611,1,18 s0.225,0.742,0.577,0.906l30,14C31.711,32.969,31.855,33,32,33s0.289-0.031,0.423-0.094L43,27.97V53.68l-4.814,6.739 c-0.217,0.305-0.247,0.705-0.075,1.039C38.282,61.791,38.625,62,39,62h10c0.375,0,0.718-0.209,0.889-0.542 c0.171-0.334,0.142-0.734-0.075-1.039L45,53.68V27.037l17.423-8.131C62.775,18.742,63,18.389,63,18S62.775,17.258,62.423,17.094z"></path>
                                    <path data-color="color-2" fill="currentColor" d="M41,31.111l-7.731,3.608C32.875,34.902,32.436,35,32,35s-0.875-0.098-1.27-0.281L11,25.511 V42c0,5.607,9.224,10,21,10c3.244,0,6.283-0.343,9-0.944V31.111z"></path>
                                    <path data-color="color-2" fill="currentColor" d="M47,28.311v20.748c3.726-1.796,6-4.277,6-7.059V25.51L47,28.311z"></path>
                                </g>
                            </g>
                            <style fill="currentColor">.nc-int-icon{position:relative;}.nc-int-icon-b{position: absolute;top: calc(50% - 0.5em);left: calc(50% - 0.5em);opacity: 0;}.nc-int-icon-state-b .nc-int-icon-a{opacity: 0;}.nc-int-icon-state-b .nc-int-icon-b{opacity: 1;}</style>
                        </g>
                    </svg>
                    <span>
                        Subjects
                    </span>
                </a>
            </li>
            <?php } ?>
        </ul>
    </nav>
    <header>
        <div class="title">
            <!-- <span id="breadcrumbs">
                <a data-link="">admin</a>
                <i>/</i>
                <a data-link="">dashboard</a>
            </span> -->
            <h1 id="page-title"><?php echo $pageData['header-title']; ?></h1>
        </div>
        <div class="search">
            <!-- <div class="search-box">
                <search>
                    <form role="search">
                        <input type="search" name="q" id="search-input" placeholder="Search...">
                    </form>
                </search>
            </div> -->
            <button id="logout">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                    <g stroke-linecap="square" stroke-miterlimit="10" fill="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper">
                        <line data-cap="butt" data-color="color-2" x1="20" y1="32" x2="56" y2="32" fill="none" stroke="currentColor" stroke-linecap="butt"></line>
                        <polyline data-color="color-2" points="44 44 56 32 44 20" fill="none" stroke="currentColor"></polyline>
                        <path d="M50,12V8a5,5,0,0,0-5-5H15a5,5,0,0,0-5,5V56a5,5,0,0,0,5,5H45a5,5,0,0,0,5-5V52" fill="none" stroke="currentColor"></path>
                    </g>
                </svg>
            </button>
        </div>
    </header>
    <main>
        <?php if(isset($routerPage)) {
            $_POST['jsfetch'] = true;
            include(__dir__ . $routerPage);
        } ?>
    </main>
    <dialog id="popup" aria-modal="true" aria-labelledby="popup-title" aria-describedby="popup-msg">
        <form method="dialog">
            <header>
                <h3 id="popup-title"></h3>
                <button onclick="this.closest('dialog').close('close');"><i></i></button>
            </header>
            <article>
                <p id="popup-msg"></p>
            </article>
        </form>
    </dialog>
</body>
</html>