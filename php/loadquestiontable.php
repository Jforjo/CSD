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
    <tr data-questionid="<?php echo $question['questionID']; ?>">
        <td colspan="12" align="center"><span><?php echo $question['question']; ?></span></td>
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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="table-linkquiz-btn" title="Link to a quiz" aria-label="Link to a quiz">
                    <g fill="currentColor" class="nc-icon-wrapper">
                        <rect x="1" y="1" width="12" height="12" rx="1" ry="1" fill="currentColor"></rect>
                        <rect x="19" y="19" width="12" height="12" rx="1" ry="1" fill="currentColor"></rect>
                        <rect x="1" y="19" width="12" height="12" rx="1" ry="1" fill="currentColor"></rect>
                        <path data-color="color-2" d="M22.293,11.707c.188,.188,.442,.293,.707,.293,.011,0,.022,0,.033,0,.276-.009,.537-.133,.72-.341L30.753,3.659c.363-.416,.321-1.047-.095-1.411-.415-.364-1.046-.322-1.411,.094l-6.296,7.196-2.244-2.244c-.391-.391-1.023-.391-1.414,0s-.391,1.023,0,1.414l3,3Z" fill="currentColor"></path>
                    </g>
                </svg>
            </div>
        </td>
    </tr>
    <tr>
        <?php
        $totalAnswers = 0;
        if ($question['answerOne'] != null) $totalAnswers++;
        if ($question['answerTwo'] != null) $totalAnswers++;
        if ($question['answerThree'] != null) $totalAnswers++;
        if ($question['answerFour'] != null) $totalAnswers++;
        ?>

        <?php if ($question['answerOne'] != null) { ?>
            <td colspan="<?php echo 12 / $totalAnswers; ?>" align="center" class="<?php if ($question['correctAnswer'] == "1") echo "correct"; ?>"><span><?php echo $question['answerOne']; ?></span></td>
        <?php } ?>
        <?php if ($question['answerTwo'] != null) { ?>
            <td colspan="<?php echo 12 / $totalAnswers; ?>" align="center" class="<?php if ($question['correctAnswer'] == "2") echo "correct"; ?>"><span><?php echo $question['answerTwo']; ?></span></td>
        <?php } ?>
        <?php if ($question['answerThree'] != null) { ?>
            <td colspan="<?php echo 12 / $totalAnswers; ?>" align="center" class="<?php if ($question['correctAnswer'] == "3") echo "correct"; ?>"><span><?php echo $question['answerThree']; ?></span></td>
        <?php } ?>
        <?php if ($question['answerFour'] != null) { ?>
            <td colspan="<?php echo 12 / $totalAnswers; ?>" align="center" class="<?php if ($question['correctAnswer'] == "4") echo "correct"; ?>"><span><?php echo $question['answerFour']; ?></span></td>
        <?php } ?>
    </tr>
    <?php } ?>
<?php } ?>