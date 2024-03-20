<?php require_once('connection.php');

/**
 * Destroys all session data.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 */
function DestroySession() {
    // This saves copy and pasting it
    //  although, it does mean this entire file needs to load...
    session_start();
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
}
/**
 * Checks if the a user with the ID already exists.
 * 
 * @param string $userID The user ID to check exists.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE if the user ID exists or FALSE if it doesn't. Also, FALSE is returned on failure.
 */
function CheckUserIDExists(string $userID): bool {
    $sql = "SELECT CheckUserIDExists(:userID) AS 'exists';";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data['exists'];
}
/**
 * Checks if the a student with the ID already exists.
 * 
 * @param string $studentID The student ID to check exists.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE if the student ID exists or FALSE if it doesn't. Also, FALSE is returned on failure.
 */
function CheckStudentIDExists(string $studentID): bool {
    $sql = "SELECT CheckStudentIDExists(:studentID) AS 'exists';";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data['exists'];
}
/**
 * Checks if the a quiz with the ID already exists.
 * 
 * @param string $quizID The quiz ID to check exists.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE if the quiz ID exists or FALSE if it doesn't. Also, FALSE is returned on failure.
 */
function CheckQuizIDExists(string $quizID): bool {
    $sql = "SELECT CheckQuizIDExists(:quizID) AS 'exists';";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":quizID", $quizID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data['exists'];
}
/**
 * Checks if the a subject with the ID already exists.
 * 
 * @param string $subjectID The subject ID to check exists.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE if the subject ID exists or FALSE if it doesn't. Also, FALSE is returned on failure.
 */
function CheckSubjectIDExists(string $subjectID): bool {
    $sql = "SELECT CheckSubjectIDExists(:subjectID) AS 'exists';";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":subjectID", $subjectID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data['exists'];
}
/**
 * Checks if the a user with the give ID has been assigned a student number.
 * 
 * @param string $userID The user's ID.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE if the user has been assigned a student number or FALSE if it doesn't. Also, FALSE is returned on failure.
 */
function CheckUserIsStudent(string $userID): bool {
    $sql = "SELECT CheckUserIsStudent(:userID) AS 'exists';";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data['exists'];
}
/**
 * Fetches the user's role.
 * 
 * @param string $userID The user's ID.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed The user's role as a string or FALSE on failure.
 */
function GetUserRole(string $userID): mixed {
    $sql = "CALL GetUserRole(:userID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data['role'];
}
/**
 * Fetches the user's state.
 * 
 * @param string $userID The user's ID.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed The user's state as a string or FALSE on failure.
 */
function GetUserState(string $userID): mixed {
    $sql = "CALL GetUserState(:userID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data['state'];
}
/**
 * Fetches the amount of students.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed The amount of students as an int or FALSE on failure.
 */
function GetStudentCount(): mixed {
    $sql = "CALL GetStudentCount();";
    $conn = newConn();
    $data = $conn->query($sql)->fetch();
    $conn = null;
    return $data['count'];
}
/**
 * Fetches the amount of lecturers.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed The amount of lecturers as an int or FALSE on failure.
 */
function GetLecturerCount(): mixed {
    $sql = "CALL GetLecturerCount();";
    $conn = newConn();
    $data = $conn->query($sql)->fetch();
    $conn = null;
    return $data['count'];
}
/**
 * Fetches the amount of students with the 'active' state.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed The amount of active students as an int or FALSE on failure.
 */
function GetActiveStudentCount(): mixed {
    $sql = "CALL GetActiveStudentCount();";
    $conn = newConn();
    $data = $conn->query($sql)->fetch();
    $conn = null;
    return $data['count'];
}
/**
 * Fetches the amount of users with the 'pending' state.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed The amount of pending users as an int or FALSE on failure.
 */
function GetPendingUserCount(): mixed {
    $sql = "CALL GetPendingUserCount();";
    $conn = newConn();
    $data = $conn->query($sql)->fetch();
    $conn = null;
    return $data['count'];
}
/**
 * Fetches the amount of students with the 'inactive' state.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed The amount of inactive students as an int or FALSE on failure.
 */
function GetInactiveStudentCount(): mixed {
    $sql = "CALL GetInactiveStudentCount();";
    $conn = newConn();
    $data = $conn->query($sql)->fetch();
    $conn = null;
    return $data['count'];
}
/**
 * Fetches the amount of quizzes.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed The amount of quizzes as an int or FALSE on failure.
 */
function GetQuizCount(): mixed {
    $sql = "CALL GetQuizCount();";
    $conn = newConn();
    $data = $conn->query($sql)->fetch();
    $conn = null;
    return $data['count'];
}
/**
 * Fetches the amount of questions.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed The amount of questions as an int or FALSE on failure.
 */
function GetQuestionCount(): mixed {
    $sql = "CALL GetQuestionCount();";
    $conn = newConn();
    $data = $conn->query($sql)->fetch();
    $conn = null;
    return $data['count'];
}
/**
 * Fetches a specified range of student data.
 * 
 * @param int $limit [optional] The max amount of rows to return.
 * @param int $offset [optional] The row offset.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed Array of mixed data of students or FALSE on failure.
 */
function GetLimitedStudentsData(int|null $limit = 5, int|null $offset = 0): mixed {
    $sql = "CALL GetLimitedStudentsData(:limit, :offset);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll();
    $conn = null;
    return $data;
}
/**
 * Fetches a specified range of lecturer data.
 * 
 * @param int $limit [optional] The max amount of rows to return.
 * @param int $offset [optional] The row offset.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed Array of mixed data of lecturers or FALSE on failure.
 */
function GetLimitedLecturersData(int|null $limit = 5, int|null $offset = 0): mixed {
    $sql = "CALL GetLimitedLecturersData(:limit, :offset);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll();
    $conn = null;
    return $data;
}
/**
 * Fetches a specified range of quiz data.
 * 
 * @param int $limit [optional] The max amount of rows to return.
 * @param int $offset [optional] The row offset.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed Array of mixed data of quizzes or FALSE on failure.
 */
function GetLimitedQuizzesData(int|null $limit = 5, int|null $offset = 0): mixed {
    $sql = "CALL GetLimitedQuizzesData(:limit, :offset);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll();
    $conn = null;
    return $data;
}
/**
 * Fetches a specified range of question data.
 * 
 * @param int $limit [optional] The max amount of rows to return.
 * @param int $offset [optional] The row offset.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed Array of mixed data of questions or FALSE on failure.
 */
function GetLimitedQuestionsData(int|null $limit = 5, int|null $offset = 0): mixed {
    $sql = "CALL GetLimitedQuestionsData(:limit, :offset);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll();
    $conn = null;
    return $data;
}
/**
 * Fetches all student data.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed Array of arrays of mixed data of students or FALSE on failure.
 */
function GetAllStudentsData(): mixed {
    $sql = "CALL GetAllStudentsData();";
    $conn = newConn();
    $data = $conn->query($sql)->fetchAll();
    $conn = null;
    return $data;
}
/**
 * Fetches all subject data.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed Array of arrays of mixed data of subjects or FALSE on failure.
 */
function GetAllSubjects(): mixed {
    $sql = "CALL GetAllSubjects();";
    $conn = newConn();
    $data = $conn->query($sql)->fetchAll();
    $conn = null;
    return $data;
}
/**
 * Fetches all data of a student with the given ID.
 * 
 * @param string $studentID The student's ID.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed Array of mixed data of the student or FALSE on failure.
 */
function GetStudentData(string $studentID): mixed {
    $sql = "CALL GetStudentData(:studentID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data;
}
/**
 * Fetches all data of a lecturer with the given ID.
 * 
 * @param string $lecturerID The lecturer's ID.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed Array of mixed data of the lecturer or FALSE on failure.
 */
function GetLecturerData(string $lecturerID): mixed {
    $sql = "CALL GetLecturerData(:lecturerID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":lecturerID", $lecturerID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data;
}
/**
 * Fetches all data of a quiz with the given ID.
 * 
 * @param string $quizID The quiz's ID.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed Array of mixed data of the quiz or FALSE on failure.
 */
function GetQuizData(string $quizID): mixed {
    $sql = "CALL GetQuizData(:quizID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":quizID", $quizID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data;
}
/**
 * Fetches data of the most recently created student.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return mixed Array of mixed data of the student or FALSE on failure.
 */
function GetRecentPendingStudentData(): mixed {
    $sql = "CALL GetRecentPendingStudentData();";
    $conn = newConn();
    $data = $conn->query($sql)->fetch();
    $conn = null;
    return $data;
}
/**
 * Create q quiz based on the given parameters.
 * 
 * @param string $title The title of the quiz.
 * @param string $subjectID The ID of the subject.
 * @param string $available The timestamp of when the quiz will become available.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function CreateQuiz($subjectID, $title, $available): bool {
    $sql = "CALL CreateQuiz(:quizID, :subjectID, :title, :available);";
    do {
        $quizID = bin2hex(random_bytes(16));
    } while (CheckQuizIDExists($quizID));
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":quizID", $quizID, PDO::PARAM_STR);
    $stmt->bindValue(":subjectID", $subjectID, PDO::PARAM_STR);
    $stmt->bindValue(":title", $title, PDO::PARAM_STR);
    if ($available == null || $available == '') {
        $stmt->bindValue(":available", null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(":available", $available, PDO::PARAM_STR);
    }
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
/**
 * Edit the data of the user with the given ID.
 * 
 * @param string $userID The user's ID.
 * @param string $firstname The user's new firstname.
 * @param string $lastname The user's new lastname.
 * @param string $email The user's new email.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function EditUser(string $userID, string $firstname, string $lastname, string $email): bool {
    $sql = "CALL EditUser(:userID, :firstname, :lastname, :email);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->bindValue(":firstname", $firstname, PDO::PARAM_STR);
    $stmt->bindValue(":lastname", $lastname, PDO::PARAM_STR);
    $stmt->bindValue(":email", $email, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
/**
 * Edit the state of the user with the given ID.
 * 
 * @param string $userID The user's ID.
 * @param string $state The user's new state.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function EditUserState(string $userID, string $state): bool {
    $sql = "CALL EditUserState(:userID, :state);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->bindValue(":state", $state, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
/**
 * Edit the role of the user with the given ID.
 * 
 * @param string $userID The user's ID.
 * @param string $role The user's new role.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function EditUserRole(string $userID, string $role): bool {
    $sql = "CALL EditUserRole(:userID, :role);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->bindValue(":role", $role, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
/**
 * Edit the password of the user with the given ID.
 * 
 * @param string $userID The user's ID.
 * @param string $password The user's new password.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function EditUserPassword(string $userID, string $password): bool {
    $sql = "CALL EditUserPassword(:userID, :password);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
/**
 * Edit the data of the user with the given ID.
 * 
 * @param string $userID The user's ID.
 * @param string $firstname The user's new firstname.
 * @param string $lastname The user's new lastname.
 * @param string $email The user's new email.
 * @param string $state The user's new state.
 * @param string|null $password The user's new password.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function EditUserData(string $userID, string $firstname, string $lastname, string $email, string $state, string|null $password): bool {
    $success = true;
    if (!EditUser($userID, $firstname, $lastname, $email)) $success = false;
    if (!EditUserState($userID, $state)) $success = false;
    if ($password != null) {
        if (!EditUserPassword($userID, $password)) $success = false;
    }
    return $success;
}
/**
 * Edit the student number of the user with the given ID.
 * 
 * @param string $userID The user's ID.
 * @param string $studentID The user's new student number.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function EditStudentID(string $userID, string $studentID): bool {
    $sql = "CALL EditStudentID(:userID, :studentID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
/**
 * Edit the data of the student with the given ID.
 * 
 * @param string $userID The user's ID.
 * @param string $firstname The user's new firstname.
 * @param string $lastname The user's new lastname.
 * @param string $studentID The user's new student number.
 * @param string $email The user's new email.
 * @param string $state The user's new state.
 * @param string|null $password The user's new password.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function EditStudent(string $userID, string $firstname, string $lastname, string $studentID, string $email, string $state, string|null $password): bool {
    return EditUserData($userID, $firstname, $lastname, $email, $state, $password)
        && EditStudentID($userID, $studentID);
}
/**
 * Edit the data of the quiz with the given ID.
 * 
 * @param string $quizID The quiz's ID.
 * @param string $title The quiz's new title.
 * @param string $subjectID The quiz's new subjectID.
 * @param string $available The quiz's new available timestamp.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function EditQuiz(string $quizID, string $title, string $subjectID, string $available): bool {
    $sql = "CALL EditQuiz(:quizID, :title, :subjectID, :available);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":quizID", $quizID, PDO::PARAM_STR);
    $stmt->bindValue(":title", $title, PDO::PARAM_STR);
    $stmt->bindValue(":subjectID", $subjectID, PDO::PARAM_STR);
    $stmt->bindValue(":available", $available, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
/**
 * Deletes the user with the given ID.
 * 
 * @param string $userID The user's ID.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function DeleteUser(string $userID): bool {
    $sql = "CALL DeleteUser(:userID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success && !CheckUserIDExists($userID);
}
/**
 * Deletes the student entry with the given userID.
 * 
 * @param string $userID The user's ID.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function DeleteStudent(string $userID): bool {
    $sql = "CALL DeleteStudent(:userID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success && !CheckUserIsStudent($userID);
}
/**
 * Deletes the quiz with the given ID.
 * 
 * @param string $quizID The quiz's ID.
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function DeleteQuiz(string $quizID): bool {
    $sql = "CALL DeleteQuiz(:quizID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":quizID", $quizID, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success && !CheckQuizIDExists($quizID);
}
/**
 * Assign a user and student ID.
 * 
 * @param string $userID The user's ID.
 * @param string $studentID The user's student ID
 * 
 * @author Jforjo <https://github.com/Jforjo>
 * @return bool TRUE on success or FALSE on failure.
 */
function CreateStudent(string $userID, string $studentID): bool {
    $sql = "CALL CreateStudent(:userID, :studentID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success && CheckUserIsStudent($userID);
}

?>