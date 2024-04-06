<?php
namespace App\Controllers;

use App\Services\UserService;
use App\Models\User;
use App\Utilities\SessionManager;
use App\Utilities\ErrorHandlerMethod;

class CreateAccountController
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
        require __DIR__ . '/../views/createAccount/index.php';
    }

    public function createAccount()
    {
        try {
            session_start();
            ErrorHandlerMethod::serverIsNotPostMethodCheck($this->sessionManager, '/createAccount', $_SERVER['REQUEST_METHOD']);

            $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            if ($this->userService->checkUsernameExists($username) || $this->userService->checkEmailExists($email)) {
                $this->sessionManager->setError("Username already taken. Please try to use a new one.");
                header('Location: /createAccount');
                exit();
            } else {
                $user = new User();
                $user->setUsername($username);
                $hashedPassword = hash('sha256', $password);
                $user->setPassword($hashedPassword);
                $user->setEmail($email);
                $avatarId = isset($_POST['avatar']) ? intval($_POST['avatar']) : 1; // default becomes one if not set
                $user->setAvatarId($avatarId);

                if ($loggedInUser = $this->userService->createNewUser($user)) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user'] = $loggedInUser;
                    $_SESSION['username'] = $loggedInUser->getUsername();
                    $_SESSION['avatar_id'] = $loggedInUser->getAvatarId();
                    $_SESSION['email'] = $loggedInUser->getEmail();
                    $_SESSION['user_id'] = $loggedInUser->getUserId();
                    $this->userService->setAvatarPathSession($loggedInUser->getAvatarId());
                    header('Location: /home');
                } else {
                    $this->sessionManager->setError("Error occurred while creating the account. Please try again.");
                    header('Location: /createAccount');
                }
                exit();
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorController($e, $this->sessionManager, '/createAccount');
        }
    }


}