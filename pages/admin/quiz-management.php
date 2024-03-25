<?php
defined('APP_RAN') or header("Location: /404");
session_start();
// Checks if the user is logged in
if (!isset($_SESSION['userID'])) die(json_encode(array(
        "type" => "refresh"
)));
require_once(__DIR__ . '/../../php/dbfuncs.php');
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
    die(json_encode(array(
        "type" => "refresh"
    )));
}

$quizCount = GetQuizCount();
?>
<section class="user-management" id="quiz-management">
    <div class="table-header">
        <div class="table-perpage">
            Show
            <select id="user-management-perpage">
                <option value="5" selected>5</option>
                <option value="10" <?php if ($quizCount <= 5) echo "hidden"; ?>>10</option>
                <option value="15" <?php if ($quizCount <= 10) echo "hidden"; ?>>15</option>
                <option value="20" <?php if ($quizCount <= 15) echo "hidden"; ?>>20</option>
                <option value="25" <?php if ($quizCount <= 20) echo "hidden"; ?>>25</option>
                <option value="50" <?php if ($quizCount <= 25) echo "hidden"; ?>>50</option>
                <option value="100" <?php if ($quizCount <= 50) echo "hidden"; ?>>100</option>
            </select>
            Entries
        </div>
        <div class="table-btns">
            <button class="create">Create New</button>
        </div>
    </div>
    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Subject</th>
                    <th>Questions</th>
                    <th>Available</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody class="autoload">
                <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <span>Showing <i id="pagination-showing"><?php echo min($quizCount, 1); ?></i> to <i id="pagination-perpage"><?php echo min($quizCount, 5); ?></i> of <i id="pagination-total"><?php echo $quizCount; ?></i> entries</span>
        <nav>
            <div class="arrow">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                    <g stroke-linecap="square" stroke-miterlimit="10" fill="none" stroke="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper">
                        <polyline points="19,22 13,16 19,10 " transform="translate(0, 0)"></polyline>
                    </g>
                </svg>
            </div>
            <ul id="pagination-menu">
                <li class="active" data-id="1">
                    <span>1</span>
                </li>
                <?php for ($i=2; $i <= ceil($quizCount / 5); $i++) { ?>
                    <li class="<?php if ($i <= 5) echo "inactive"; else echo "hidden"; ?>"  data-id="<?php echo $i; ?>">
                        <span><?php echo $i; ?></span>
                    </li>
                <?php } ?>
            </ul>
            <div class="arrow">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                    <g stroke-linecap="square" stroke-miterlimit="10" fill="none" stroke="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper">
                        <polyline points="13,10 19,16 13,22 " transform="translate(0, 0)"></polyline>
                    </g>
                </svg>
            </div>
        </nav>
    </div>
</section>
<?php
$subjects = GetAllSubjects();
$subjectCount = count($subjects);
?>
<dialog id="dialog-edit-quiz" aria-modal="true">
    <form method="dialog">
        <fieldset>
            <span class="error-msg"></span>
            <input type="hidden" name="quizID" id="form-quizID">
            <div class="form-input">
                <label for="form-title">Title<i aria-hidden="true">*</i></label>
                <input type="text" name="title" id="form-title" required>
            </div>
            <div class="form-input">
                <label for="form-subject">Subject<i aria-hidden="true">*</i></label>
                <select name="subject" id="form-subject" <?php if ($subjectCount == 0) echo "disabled"; ?>>
                    <?php if ($subjectCount == 0) { ?>
                        <option selected>None Available</option>
                    <?php } else { ?>
                        <option disabled>Select an option</option>
                        <?php foreach ($subjects as $subject) { ?>
                            <option value="<?php echo $subject['subjectID']; ?>"><?php echo $subject['name']; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
            <div class="form-input">
                <label for="form-available">Available</label>
                <input type="datetime-local" name="available" id="form-available" step="1">
            </div>
        </fieldset>
        <hr>
        <footer>
            <menu>
                <button autofocus type="reset" onclick="this.closest('dialog').close('cancel');">Cancel</button>
                <button type="submit">Edit</button>
            </menu>
        </footer>
    </form>
</dialog>
<?php
$students = GetAllStudentsData();
$studentCount = count($students);
?>
<dialog id="dialog-assign-quiz" aria-modal="true">
    <form method="dialog">
        <fieldset>
            <span class="error-msg"></span>
            <input type="hidden" name="quizID" id="form-assignQuizID">
            <div class="form-input">
                <label for="form-questionCount">Number of Questions<i aria-hidden="true">*</i></label>
                <input type="number" name="questionCount" id="form-questionCount" required>
            </div>
            <div class="form-input">
                <label for="form-student">Students<i aria-hidden="true">*</i></label>
                <select name="students[]" id="form-assignStudent" <?php if ($studentCount == 0) echo "disabled"; ?> multiple size="8">
                    <?php if ($studentCount == 0) { ?>
                        <option selected>None Available</option>
                    <?php } else { ?>
                        <option disabled>Select an option</option>
                        <?php foreach ($students as $student) { ?>
                            <option value="<?php echo $student['studentID']; ?>"><?php echo $student['studentID'] . " - " . $student['firstname'] . " " . $student['lastname']; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </fieldset>
        <hr>
        <footer>
            <menu>
                <button autofocus type="reset" onclick="this.closest('dialog').close('cancel');">Cancel</button>
                <button type="submit">Submit</button>
            </menu>
        </footer>
    </form>
</dialog>