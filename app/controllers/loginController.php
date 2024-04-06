<?php
namespace App\Controllers;

use App\Services\UserService;
use App\Utilities\SessionManager;
use App\Utilities\ErrorHandlerMethod;

class LoginController
{
    private $userService;
    private $sessionManager;

    function __construct()
    {
        $this->userService = new UserService();
        $this->sessionManager = new SessionManager();
    }
    public function index()
    {
        require __DIR__ . '/../views/log_in/index.php';
    }

    public function logIn()
    {
        try {
            session_start();

            ErrorHandlerMethod::serverIsNotPostMethodCheck($this->sessionManager, '/login', $_SERVER['REQUEST_METHOD']);

            $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if ($this->userService->checkUsernameExists($username)) {
                $hashedPassword = $this->userService->returnPasswordByUsername($username);
                $enteredPassword = hash('sha256', $password);

                if ($enteredPassword === $hashedPassword) {
                    $user = $this->userService->returnUserByUsername($username);
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['user_id'] = $user->getUserId();
                    $_SESSION['avatar_id'] = $user->getAvatarId();
                    $_SESSION['email'] = $user->getEmail();
                    $_SESSION['admin'] = $user->getIsAdmin();

                    $this->userService->setAvatarPathSession($user->getAvatarId());

                    header('Location: /home');
                } else {
                    $this->sessionManager->setError("Incorrect password. Please try again.");
                    header('Location: /login');
                }
            } else {
                $this->sessionManager->setError("Username does not exist. Please check your username or sign up.");
                header('Location: /login');
            }
            exit();
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorController($e, $this->sessionManager, '/login');
        }
    }
}