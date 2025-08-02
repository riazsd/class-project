<?php
session_start();

class Auth {
    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
    }

    public static function logout() {
        session_destroy();
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }

    public static function requireRole($role) {
        self::requireLogin();
        if ($_SESSION['role'] !== $role) {
            header('Location: dashboard.php');
            exit();
        }
    }

    public static function getUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'first_name' => $_SESSION['first_name'],
                'last_name' => $_SESSION['last_name'],
                'email' => $_SESSION['email']
            ];
        }
        return null;
    }

    public static function redirectBasedOnRole() {
        if (self::isLoggedIn()) {
            if ($_SESSION['role'] === 'student') {
                header('Location: student_dashboard.php');
            } else {
                header('Location: teacher_dashboard.php');
            }
            exit();
        }
    }
}
?>
