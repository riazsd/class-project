<?php
require_once 'config/database.php';

class Course {
    private $conn;
    private $courses_table = "courses";
    private $registrations_table = "course_registrations";
    private $users_table = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllCourses() {
        $query = "SELECT c.*, u.first_name, u.last_name 
                  FROM " . $this->courses_table . " c
                  LEFT JOIN " . $this->users_table . " u ON c.teacher_id = u.id
                  ORDER BY c.course_code";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableCoursesForStudent($student_id) {
        $query = "SELECT c.*, u.first_name, u.last_name,
                         cr.status as registration_status
                  FROM " . $this->courses_table . " c
                  LEFT JOIN " . $this->users_table . " u ON c.teacher_id = u.id
                  LEFT JOIN " . $this->registrations_table . " cr ON c.id = cr.course_id AND cr.student_id = ?
                  ORDER BY c.course_code";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $student_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registerForCourse($student_id, $course_id) {
        $query = "INSERT INTO " . $this->registrations_table . " 
                  (student_id, course_id, status) 
                  VALUES (?, ?, 'pending')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $student_id);
        $stmt->bindParam(2, $course_id);

        return $stmt->execute();
    }

    public function getStudentRegistrations($student_id) {
        $query = "SELECT c.*, cr.status, cr.registered_at, cr.updated_at,
                         u.first_name as teacher_first_name, u.last_name as teacher_last_name
                  FROM " . $this->registrations_table . " cr
                  JOIN " . $this->courses_table . " c ON cr.course_id = c.id
                  LEFT JOIN " . $this->users_table . " u ON c.teacher_id = u.id
                  WHERE cr.student_id = ?
                  ORDER BY cr.registered_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $student_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeacherCourseRegistrations($teacher_id) {
        $query = "SELECT cr.*, c.course_code, c.course_name,
                         u.first_name as student_first_name, u.last_name as student_last_name, u.email as student_email
                  FROM " . $this->registrations_table . " cr
                  JOIN " . $this->courses_table . " c ON cr.course_id = c.id
                  JOIN " . $this->users_table . " u ON cr.student_id = u.id
                  WHERE c.teacher_id = ?
                  ORDER BY cr.registered_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $teacher_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRegistrationStatus($registration_id, $status) {
        $query = "UPDATE " . $this->registrations_table . " 
                  SET status = ?, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $status);
        $stmt->bindParam(2, $registration_id);

        return $stmt->execute();
    }

    public function removeRegistration($registration_id) {
        $query = "DELETE FROM " . $this->registrations_table . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $registration_id);

        return $stmt->execute();
    }

    public function isStudentRegistered($student_id, $course_id) {
        $query = "SELECT id FROM " . $this->registrations_table . " 
                  WHERE student_id = ? AND course_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $student_id);
        $stmt->bindParam(2, $course_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>
