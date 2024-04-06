<?php
namespace App\Controllers;

use App\Services\UserService;
use App\Utilities\SessionManager;
use App\Utilities\ErrorHandlerMethod;

class ProfileController
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
        require __DIR__ . '/../views/profile/index.php';
    }

    public function modifyAccount()
    {
        try {
            session_start();
            ErrorHandlerMethod::serverIsNotPostMethodCheck($this->sessionManager, '/profile', $_SERVER['REQUEST_METHOD']);

            $userID = $_SESSION['user_id'];

            $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $currentPassword = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $newPassword = filter_var($_POST['password2'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $newPassword2 = filter_var($_POST['password3'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);


            // Check if current password matches
            if ($this->userService->returnPasswordById($userID) !== hash('sha256', $currentPassword)) {
                $this->sessionManager->setError("Current password is incorrect.");

            } else {
                // Attempt to update password only if new password fields are filled
                if (!empty($newPassword) || !empty($newPassword2)) {
                    $this->updatePassword($userID, $newPassword, $newPassword2);
                }

                $this->updateUsername($userID, $username);
                $this->updateEmail($userID, $email);
                $avatarID = isset($_POST['avatar']) ? $_POST['avatar'] : 0;
                $this->updateAvatar($userID, $avatarID);
            }
            header('Location: /profile');
            exit();
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorController($e, $this->sessionManager, '/profile');
        }
    }

    private function updatePassword($userID, $newPassword, $newPassword2)
    {
        //check if new password matches
        if (strlen($newPassword) !== 0) {

            if ($newPassword !== $newPassword2) {
                $this->sessionManager->setError("Passwords do not match.");
            } else {
                $hashedPassword = hash('sha256', $newPassword);
                $this->userService->updatePassword($userID, $hashedPassword);
            }
        }
    }

    private function updateUsername($userID, $username)
    {
        $currentUsername = $this->userService->returnUsernameById($userID);

        if ($username !== $currentUsername) {
            //check if username is empty
            if (strlen($username) == 0) {
                $this->sessionManager->setError("Username cannot be empty.");
            } else if ($this->userService->checkUsernameExists($username)) {
                $this->sessionManager->setError("Username already exists.");
            }

            $this->userService->updateUsername($userID, $username);
            $_SESSION['username'] = $username;
        }
    }

    private function updateEmail($userID, $email)
    {
        $currentEmail = $this->userService->returnEmailById($userID);

        if ($email !== $currentEmail) {
            //check if email is empty
            if (strlen($email) == 0) {
                $this->sessionManager->setError("Email cannot be empty.");
            } else if ($this->userService->checkEmailExists($email)) {
                $this->sessionManager->setError("Email already exists.");
            }

            $this->userService->updateEmail($userID, $email);
            $_SESSION['email'] = $email;
        }
    }

    private function updateAvatar($userID, $avatarID)
    {
        if ($avatarID !== 0) {
            $this->userService->updateAvatar($userID, $avatarID);
            $this->userService->setAvatarPathSession($avatarID);
        }
    }

    public function logout()
    {
        try {
            session_start();
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                $_SESSION = array();
                session_destroy();
                header('Location: /home');
                exit();
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorController($e, $this->sessionManager, '/profile');
        }
    }

}