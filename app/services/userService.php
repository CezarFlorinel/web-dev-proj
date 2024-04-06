<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function convertUsers($users): array  // for some reason, the UserRepository returns an array of stdClass objects
    {
        return array_map(function ($stdUser) {
            $user = new User();

            // Manually assign properties from stdClass to User
            $user->setUserId($stdUser->userId ?? 0);
            $user->setUsername($stdUser->username ?? '');
            $user->setEmail($stdUser->email ?? '');
            $user->setPassword($stdUser->password ?? '');
            $user->setAvatarId($stdUser->avatarId ?? 0);
            $user->setIsAdmin($stdUser->admin ?? false);

            return $user;
        }, $users);
    }

    public function setAvatarPathSession($avatarID)
    {
        switch ($avatarID) {
            case 1:
                $_SESSION['avatar_path'] = 'images/account avatars/1.webp';
                break;
            case 2:
                $_SESSION['avatar_path'] = 'images/account avatars/2.webp';
                break;
            case 3:
                $_SESSION['avatar_path'] = 'images/account avatars/3.jpg';
                break;
            case 4:
                $_SESSION['avatar_path'] = 'images/account avatars/4.webp';
                break;
            case 5:
                $_SESSION['avatar_path'] = 'images/account avatars/5.jpg';
                break;
            default:
                $_SESSION['avatar_path'] = 'images/profile-user.png';
                break;
        }
    }
    public function updateAvatar($userId, $newAvatarId)
    {
        $this->userRepository->updateAvatar($userId, $newAvatarId);
    }

    public function updateEmail($userId, $newEmail)
    {
        $this->userRepository->updateEmail($userId, $newEmail);
    }

    public function returnEmailById($userId): string
    {
        return $this->userRepository->returnEmailById($userId);
    }

    public function updateUsername($userId, $newUsername)
    {
        $this->userRepository->updateUsername($userId, $newUsername);
    }

    public function returnUsernameById($userId): string
    {
        return $this->userRepository->returnUsernameById($userId);
    }

    public function updatePassword($userId, $newPassword)
    {
        $this->userRepository->updatePassword($userId, $newPassword);
    }

    public function returnPasswordById($userId): string
    {
        return $this->userRepository->returnPasswordById($userId);
    }

    public function returnPasswordByUsername($username): string
    {
        return $this->userRepository->returnPasswordByUsername($username);
    }

    public function returnUserById($userId): User
    {
        return $this->userRepository->returnUserById($userId);
    }

    public function returnUserByUsername($username): User
    {
        return $this->userRepository->returnUserByUsername($username);
    }

    function checkUsernameExists($enteredUsername): bool
    {
        return $this->userRepository->checkUsernameExists($enteredUsername);
    }

    public function checkEmailExists($email): bool
    {
        return $this->userRepository->checkEmailExists($email);
    }

    public function createNewUser(User $user): ?User
    {
        return $this->userRepository->createNewUser($user);
    }

    public function returnAllUsers(): array
    {
        return $this->userRepository->returnAllUsers();
    }

    public function deleteUser($userId)
    {
        $this->userRepository->deleteUser($userId);
    }
}