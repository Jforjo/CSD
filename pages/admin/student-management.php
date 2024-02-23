<?php session_start();
// Checks if the user is logged in
if (!isset($_SESSION['userID'])) die(json_encode(array(
        "type" => "refresh"
)));
require_once(__DIR__ . '/../../php/dbfuncs.php');
// Checks if their session id is valid
if (!CheckUserIDExists($_SESSION['userID'])) {
    session_unset();
    session_destroy();
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

$students = GetAllStudentsData();
/*
userID
studentID
firstname
lastname
email
state
lastLogin
*/
$studentCount = count($students);
?>
<section class="user-management" id="student-management">
    <div class="table-header">
        <div class="table-perpage">
            Show
            <select id="user-management-perpage">
                <option value="5" selected>5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
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
                    <th>Status</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody style="--shown-rows:<?php echo min($studentCount, 5); ?>">
                <?php foreach($students as $student) { ?>
                <tr data-userid="<?php echo $student['userID']; ?>">
                    <td>
                        <div>
                            <span><?php echo ucwords($student['firstname'] . ' ' . $student['lastname']); ?></span>
                            <a href="mailto:<?php echo $student['email']; ?>"><?php echo $student['email']; ?></a>
                        </div>
                    </td>
                    <td><span><?php echo $student['studentID']; ?></span></td>
                    <td><span><?php echo ucfirst($student['state']); ?></span></td>
                    <td>
                        <div class="icons">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="table-edit-btn">
                                <g stroke-linecap="square" stroke-miterlimit="10" fill="none" stroke="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper">
                                    <line data-cap="butt" x1="20" y1="5" x2="27" y2="12" stroke-linecap="butt"></line>
                                    <line x1="10" y1="22" x2="23.5" y2="8.5"></line>
                                    <line data-cap="butt" x1="4" y1="21" x2="11" y2="28" stroke-linecap="butt"></line>
                                    <path d="M11,28,2,30l2-9L22.414,2.586a2,2,0,0,1,2.828,0l4.172,4.172a2,2,0,0,1,0,2.828Z"></path>
                                </g>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="table-delete-btn">
                                <g fill="currentColor" class="nc-icon-wrapper">
                                    <path data-color="color-2" d="M30,5h-8V1c0-.552-.448-1-1-1H11c-.552,0-1,.448-1,1V5H2c-.552,0-1,.448-1,1s.448,1,1,1H30c.552,0,1-.448,1-1s-.448-1-1-1ZM12,2h8v3H12V2Z"></path>
                                    <path d="M4,9V28c-.024,2.185,1.728,3.976,3.914,4,.029,0,.058,0,.086,0H24c2.185,.024,3.976-1.728,4-3.914,0-.029,0-.058,0-.086V9H4Zm7,16c0,.552-.447,1-1,1s-1-.448-1-1v-9c0-.552,.447-1,1-1s1,.448,1,1v9Zm6,0c0,.552-.447,1-1,1s-1-.448-1-1v-9c0-.552,.447-1,1-1s1,.448,1,1v9Zm6,0c0,.552-.447,1-1,1s-1-.448-1-1v-9c0-.552,.447-1,1-1s1,.448,1,1v9Z"></path>
                                </g>
                            </svg>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <span><?php echo "Showing 1 to 5 of " . $studentCount . " entries"; ?></span>
        <nav>
            <div class="arrow">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                    <g stroke-linecap="square" stroke-miterlimit="10" fill="none" stroke="currentColor" stroke-linejoin="miter" class="nc-icon-wrapper">
                        <polyline points="19,22 13,16 19,10 " transform="translate(0, 0)"></polyline>
                    </g>
                </svg>
            </div>
            <ul>
                <li class="active">
                    <span>1</span>
                </li>
                <li>
                    <span>2</span>
                </li>
                <li>
                    <span>3</span>
                </li>
                <li>
                    <span>4</span>
                </li>
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
            <div class="form-input">
                <label for="form-firstname">Firstname<i aria-hidden="true">*</i></label>
                <input type="text" name="firstname" id="form-firstname">
            </div>
            <div class="form-input">
                <label for="form-lastname">Lastname<i aria-hidden="true">*</i></label>
                <input type="text" name="lastname" id="form-lastname">
            </div>
            <div class="form-input">
                <label for="form-stdentID">Student ID<i aria-hidden="true">*</i></label>
                <input type="text" name="stdentID" id="form-stdentID">
            </div>
            <div class="form-input">
                <label for="form-email">Email<i aria-hidden="true">*</i></label>
                <input type="email" name="email" id="form-email">
            </div>
            <div class="form-input">
                <label for="form-password">Password</label>
                <input type="password" name="password" id="form-password">
            </div>
            <div class="form-input">
                <label for="form-email">Status<i aria-hidden="true">*</i></label>
                <div class="input-switch" style="--count:3;">
                    <div>
                        <div class="input-switch-option">
                            <input type="radio" name="status" id="form-status-1" value="inactive">
                            <label for="form-status-1">Inactive</label>
                        </div>
                        <div class="input-switch-option">
                            <input type="radio" name="status" id="form-status-2" value="pending">
                            <label for="form-status-2">Pending</label>
                        </div>
                        <div class="input-switch-option">
                            <input type="radio" name="status" id="form-status-3" value="active">
                            <label for="form-status-3">Active</label>
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
                <button type="submit">Create</button>
            </menu>
        </footer>
    </form>
</dialog>