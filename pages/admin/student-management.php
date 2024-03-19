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

$studentCount = GetStudentCount();
?>
<section class="user-management" id="student-management">
    <div class="table-header">
        <div class="table-perpage">
            Show
            <select id="user-management-perpage">
                <option value="5" selected>5</option>
                <option value="10" <?php if ($studentCount <= 5) echo "hidden"; ?>>10</option>
                <option value="15" <?php if ($studentCount <= 10) echo "hidden"; ?>>15</option>
                <option value="20" <?php if ($studentCount <= 15) echo "hidden"; ?>>20</option>
                <option value="25" <?php if ($studentCount <= 20) echo "hidden"; ?>>25</option>
                <option value="50" <?php if ($studentCount <= 25) echo "hidden"; ?>>50</option>
                <option value="100" <?php if ($studentCount <= 50) echo "hidden"; ?>>100</option>
            </select>
            Entries
        </div>
        <div class="table-btns">
            <!-- <button>Create New</button> -->
        </div>
    </div>
    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>ID</th>
                    <th>State</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody style="--shown-rows:<?php echo min($studentCount, 5); ?>" class="autoload">
                <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <span>Showing <i id="pagination-showing"><?php echo min($studentCount, 1); ?></i> to <i id="pagination-perpage"><?php echo min($studentCount, 5); ?></i> of <i id="pagination-total"><?php echo $studentCount; ?></i> entries</span>
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
                <?php for ($i=2; $i <= ceil($studentCount / 5); $i++) { ?>
                    <li class="<?php if ($i <= 5) echo "inactive"; else echo "hidden aria-hidden='true'"; ?>"  data-id="<?php echo $i; ?>">
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

<dialog id="dialog-edit-user" aria-modal="true">
    <form method="dialog">
        <fieldset>
            <span class="error-msg"></span>
            <input type="hidden" name="userID" id="form-userID">
            <div class="form-input">
                <label for="form-firstname">Firstname<i aria-hidden="true">*</i></label>
                <input type="text" name="firstname" id="form-firstname" required>
            </div>
            <div class="form-input">
                <label for="form-lastname">Lastname<i aria-hidden="true">*</i></label>
                <input type="text" name="lastname" id="form-lastname" required>
            </div>
            <div class="form-input">
                <label for="form-studentID">Student ID<i aria-hidden="true">*</i></label>
                <input type="text" name="studentID" id="form-studentID" required>
            </div>
            <div class="form-input">
                <label for="form-email">Email<i aria-hidden="true">*</i></label>
                <input type="email" name="email" id="form-email" required>
            </div>
            <div class="form-input">
                <label for="form-password">Password</label>
                <input type="password" name="password" id="form-password">
            </div>
            <div class="form-input">
                <label>State<i aria-hidden="true">*</i></label>
                <div class="input-switch" style="--count:3;">
                    <div>
                        <div class="input-switch-option">
                            <input type="radio" name="state" id="form-state-1" value="inactive" required>
                            <label for="form-state-1">Inactive</label>
                        </div>
                        <div class="input-switch-option">
                            <input type="radio" name="state" id="form-state-2" value="pending" required>
                            <label for="form-state-2">Pending</label>
                        </div>
                        <div class="input-switch-option">
                            <input type="radio" name="state" id="form-state-3" value="active" required>
                            <label for="form-state-3">Active</label>
                        </div>
                        <span class="input-switch-slider"></span>
                    </div>
                </div>
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