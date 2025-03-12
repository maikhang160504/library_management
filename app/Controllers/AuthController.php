<?php

namespace App\Controllers;

use App\Models\Auth;

class AuthController 
{
    private $userModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new Auth();
    }

    public function index()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../../views/auth/login.php';
     
    }

    public function login()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $errors = ['usernameErr' => '', 'passwordErr' => '', 'loginErr' => ''];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Kiểm tra CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error_message'] = "Xác thực CSRF token thất bại!";
            header('Location: /login');
            exit();
        }

        $username = $_POST["username"] ?? '';
        $password = $_POST["password"] ?? '';

        if (empty($username)) {
            $errors['usernameErr'] = "Vui lòng nhập tên đăng nhập.";
        }
        if (empty($password)) {
            $errors['passwordErr'] = "Vui lòng nhập mật khẩu.";
        }

        if (empty($errors['usernameErr']) && empty($errors['passwordErr'])) {
            if ($this->userModel->validateUser($username, $password)) {
                $_SESSION['user'] = $username;
                unset($_SESSION['csrf_token']);
                header('Location: /');
                exit();
            } else {
                $errors['loginErr'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
            }
        }

        $_SESSION['errors'] = $errors;
        header('Location: /login');
        exit();
    }
}



    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: /home");
        exit();
    }

    private function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}
