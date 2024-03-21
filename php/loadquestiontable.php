<?php session_start();
require_once('dbfuncs.php');
// Checks if they are logged in
// Header will not work as what it would link to
//  would be sent to the JavaScript instead of actually
//  redirecting to the desired link.
if (!isset($_SESSION['userID'])) {
    DestroySession();
    die(json_encode(array(
        "type" => "refresh"
    )));
}
// Checks if their session id is valid
if (!CheckUserIDExists($_SESSION['userID'])) {
    DestroySession();
    die(json_encode(array(
        "type" => "refresh"
    )));
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
if (!($role == "lecturer" || $role == "admin")) {
    // They shouldn't have been able to access this file without
    //  these permissions, so log them out just incase.
    DestroySession();
    die(json_encode(array(
        "type" => "refresh"
    )));
}

$limit = $_POST['limit'];
if (!isset($_POST['limit']) || $_POST['limit'] < 5) $limit = 5;
$offset = $_POST['offset'];
if (!isset($_POST['offset']) || $_POST['offset'] < 0) $offset = 0;

if (isset($_GET['quiz'])) $quizID = $_GET['quiz'];
else $quizID = "";

$questions = GetLimitedQuestionsData($limit, $offset, $quizID);

$questionCount = count($questions);
?>

<?php if ($questionCount == 0) { ?>
    <h3 class="n-a">N/A</h3>
<?php } else { ?>
    <?php foreach($questions as $question) { ?>
    <tr data-quizid="<?php echo $question['quizID']; ?>">
        <td colspan="12"><span><?php echo $question['question']; ?></span></td>
        <td rowspan="2">
            <div class="icons">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="table-edit-btn" title="Edit" aria-label="Edit" aria-haspopup="dialog">
                    <g stroke-linecap="square" stroke-miterlimit="10" fill="none" stroke="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper">
                        <line data-cap="butt" x1="20" y1="5" x2="27" y2="12" stroke-linecap="butt"></line>
                        <line x1="10" y1="22" x2="23.5" y2="8.5"></line>
                        <line data-cap="butt" x1="4" y1="21" x2="11" y2="28" stroke-linecap="butt"></line>
                        <path d="M11,28,2,30l2-9L22.414,2.586a2,2,0,0,1,2.828,0l4.172,4.172a2,2,0,0,1,0,2.828Z"></path>
                    </g>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="table-delete-btn" title="Delete" aria-label="Delete">
                    <g fill="currentColor" class="nc-icon-wrapper">
                        <path data-color="color-2" d="M30,5h-8V1c0-.552-.448-1-1-1H11c-.552,0-1,.448-1,1V5H2c-.552,0-1,.448-1,1s.448,1,1,1H30c.552,0,1-.448,1-1s-.448-1-1-1ZM12,2h8v3H12V2Z"></path>
                        <path d="M4,9V28c-.024,2.185,1.728,3.976,3.914,4,.029,0,.058,0,.086,0H24c2.185,.024,3.976-1.728,4-3.914,0-.029,0-.058,0-.086V9H4Zm7,16c0,.552-.447,1-1,1s-1-.448-1-1v-9c0-.552,.447-1,1-1s1,.448,1,1v9Zm6,0c0,.552-.447,1-1,1s-1-.448-1-1v-9c0-.552,.447-1,1-1s1,.448,1,1v9Zm6,0c0,.552-.447,1-1,1s-1-.448-1-1v-9c0-.552,.447-1,1-1s1,.448,1,1v9Z"></path>
                    </g>
                </svg>
            </div>
        </td>
    </tr>
    <tr>
        <?php if ($question['answer1'] != null) { ?>
            <td colspan="100%"><span><?php echo $question['answer1']; ?></span></td>
        <?php } ?>
        <?php if ($question['answer2'] != null) { ?>
            <td colspan="100%"><span><?php echo $question['answer2']; ?></span></td>
        <?php } ?>
        <?php if ($question['answer3'] != null) { ?>
            <td colspan="100%"><span><?php echo $question['answer3']; ?></span></td>
        <?php } ?>
        <?php if ($question['answer4'] != null) { ?>
            <td colspan="100%"><span><?php echo $question['answer4']; ?></span></td>
        <?php } ?>
    </tr>
    <?php } ?>
<?php } ?>