<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $this->userModel->getUserByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                $this->redirect('/');
            } else {
                $this->view('auth/login', ['error' => 'Invalid credentials']);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}