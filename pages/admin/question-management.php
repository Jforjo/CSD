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

if (isset($_GET['quiz'])) $quizID = $_GET['quiz'];
else $quizID = "";

$questionCount = GetQuestionCount($quizID);
?>
<section class="user-management" id="question-management">
    <div class="table-header">
        <div class="table-perpage">
            Show
            <select id="user-management-perpage">
                <option value="5" selected>5</option>
                <option value="10" <?php if ($questionCount <= 5) echo "hidden"; ?>>10</option>
                <option value="15" <?php if ($questionCount <= 10) echo "hidden"; ?>>15</option>
                <option value="20" <?php if ($questionCount <= 15) echo "hidden"; ?>>20</option>
                <option value="25" <?php if ($questionCount <= 20) echo "hidden"; ?>>25</option>
                <option value="50" <?php if ($questionCount <= 25) echo "hidden"; ?>>50</option>
                <option value="100" <?php if ($questionCount <= 50) echo "hidden"; ?>>100</option>
            </select>
            Entries
        </div>
        <div class="table-btns">
            <button class="create">Create New</button>
        </div>
    </div>
    <div class="table">
        <table>
            <!-- <thead>
                <tr>
                    <th>Question</th>
                </tr>
            </thead> -->
            <tbody class="autoload">
                <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <span>Showing <i id="pagination-showing"><?php echo min($questionCount, 1); ?></i> to <i id="pagination-perpage"><?php echo min($questionCount, 5); ?></i> of <i id="pagination-total"><?php echo $questionCount; ?></i> entries</span>
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
                <?php for ($i=2; $i <= ceil($questionCount / 5); $i++) { ?>
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
<dialog id="dialog-edit-question" aria-modal="true">
    <form method="dialog">
        <fieldset>
            <span class="error-msg"></span>
            <input type="hidden" name="questionID" id="form-questionID">
            <div class="form-input">
                <label for="form-question">Question<i aria-hidden="true">*</i></label>
                <textarea type="text" name="question" id="form-question" required></textarea>
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
                <label>Correct Annswer<i aria-hidden="true">*</i></label>
                <div class="input-switch" style="--count:4;">
                    <div>
                        <div class="input-switch-option">
                            <input type="radio" name="correctAnswer" id="form-correct-1" value="1" required>
                            <label for="form-correct-1">1</label>
                        </div>
                        <div class="input-switch-option">
                            <input type="radio" name="correctAnswer" id="form-correct-2" value="2" required>
                            <label for="form-correct-2">2</label>
                        </div>
                        <div class="input-switch-option">
                            <input type="radio" name="correctAnswer" id="form-correct-3" value="3" required>
                            <label for="form-correct-3">3</label>
                        </div>
                        <div class="input-switch-option">
                            <input type="radio" name="correctAnswer" id="form-correct-4" value="4" required>
                            <label for="form-correct-4">4</label>
                        </div>
                        <span class="input-switch-slider"></span>
                    </div>
                </div>
            </div>
            <div class="form-input">
                <label for="form-answerOne">Answer One<i aria-hidden="true">*</i></label>
                <textarea name="answerOne" id="form-answerOne" required></textarea>
            </div>
            <div class="form-input">
                <label for="form-answerTwo">Answer Two<i aria-hidden="true">*</i></label>
                <textarea name="answerTwo" id="form-answerTwo" required></textarea>
            </div>
            <div class="form-input">
                <label for="form-answerThree">Answer Three</label>
                <textarea name="answerThree" id="form-answerThree"></textarea>
            </div>
            <div class="form-input">
                <label for="form-answerFour">Answer Four</label>
                <textarea name="answerFour" id="form-answerFour"></textarea>
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
$quizzes = GetAllQuizTitles();
$quizCount = count($quizzes);
?>
<dialog id="dialog-link-question" aria-modal="true">
    <form method="dialog">
        <fieldset>
            <span class="error-msg"></span>
            <input type="hidden" name="questionID" id="form-linkQuestionID">
            <div class="form-input">
                <label for="form-linkQuestion">Question</label>
                <input type="text" type="text" name="question" id="form-linkQuestion" required disabled>
            </div>
            <div class="form-input">
                <label for="form-linkQuiz">Quiz<i aria-hidden="true">*</i></label>
                <select name="quizID" id="form-linkQuiz" <?php if ($quizCount == 0) echo "disabled"; ?>>
                    <?php if ($quizCount == 0) { ?>
                        <option selected>None Available</option>
                    <?php } else { ?>
                        <option disabled selected>Select an option</option>
                        <?php foreach ($quizzes as $quiz) { ?>
                            <option value="<?php echo $quiz['quizID']; ?>"><?php echo $quiz['title']; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </fieldset>
        <hr>
        <footer>
            <menu>
                <button autofocus type="reset" onclick="this.closest('dialog').close('cancel');">Cancel</button>
                <button type="submit">Link</button>
            </menu>
        </footer>
    </form>
</dialog>