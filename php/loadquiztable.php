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

$quizzes = GetLimitedQuizzesData($limit, $offset);

$quizCount = count($quizzes);
?>

<?php if ($quizCount == 0) { ?>
    <h3 class="n-a">N/A</h3>
<?php } else { ?>
    <?php foreach($quizzes as $quiz) { ?>
    <tr data-quizid="<?php echo $quiz['quizID']; ?>">
        <td><span><?php echo $quiz['title']; ?></span></td>
        <td><span><?php echo $quiz['subject']; ?></span></td>
        <td>
            <a href="/admin/questions?quiz=<?php echo $quiz['quizID']; ?>" title="View all questions of quiz: '<?php echo $quiz['title']; ?>'">
                <?php echo $quiz['questions']; ?>
            </a>
        </td>
        <td><span><?php echo $quiz['available']; ?></span></td>
        <td>
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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="table-assignquiz-btn" title="Assign to students" aria-label="Assign to students">
                    <g fill="currentColor" class="nc-icon-wrapper">
                        <path d="M59.856,43.065,48.468,38.51a1,1,0,0,1-.622-.8l-.378-3.025A12,12,0,0,0,54,24V14.725a7.009,7.009,0,0,0-6.368-6.972L46.341,5.172a2.157,2.157,0,0,0-2.617-1.053L36.316,6.764a9.478,9.478,0,0,0-4.191,3.2A14.946,14.946,0,0,1,37,21v1a23.2,23.2,0,0,0,3.43,11.941A4,4,0,0,1,38.262,39.8a21.294,21.294,0,0,1-2.067.575l4.776,1.91A7.962,7.962,0,0,1,46,49.708V57a3.939,3.939,0,0,1-.142,1H62a1,1,0,0,0,1-1V47.708A4.975,4.975,0,0,0,59.856,43.065Z" fill="currentColor" data-color="color-2"></path>
                        <path d="M42,58H2a1,1,0,0,1-1-1V49.708a4.975,4.975,0,0,1,3.144-4.643L15.533,40.51a1,1,0,0,0,.621-.806l.279-2.226a18.784,18.784,0,0,1-9.749-.53,1,1,0,0,1-.541-1.463A26.2,26.2,0,0,0,10,22V21a12,12,0,0,1,24,0v1a26.2,26.2,0,0,0,3.857,13.485,1,1,0,0,1-.541,1.463,18.774,18.774,0,0,1-9.749.53l.279,2.227a1,1,0,0,0,.622.8l11.388,4.555A4.975,4.975,0,0,1,43,49.708V57A1,1,0,0,1,42,58Z" fill="currentColor"></path>
                    </g>
                </svg>
            </div>
        </td>
    </tr>
    <?php } ?>
<?php } ?>