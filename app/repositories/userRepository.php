<?php
namespace App\Repositories;

use PDO;
use App\Models\User;

class UserRepository extends Repository    //functions used for creating, checking and getting user data 
{
    public function updateAvatar($userId, $newAvatarId)
    {
        $stmt = $this->connection->prepare("UPDATE Users SET avatarId = ? WHERE userId = ?");
        $stmt->execute([$newAvatarId, $userId]);
    }
    public function updateEmail($userId, $newEmail)
    {
        $stmt = $this->connection->prepare("UPDATE Users SET email = ? WHERE userId = ?");
        $stmt->execute([$newEmail, $userId]);
    }

    public function returnUsernameById($userId): ?string
    {
        $stmt = $this->connection->prepare("SELECT username FROM Users WHERE userId = ?");
        $stmt->execute([$userId]);
        $username = $stmt->fetchColumn();
        return ($username !== false) ? $username : null;
    }

    public function returnEmailById($userId): ?string
    {
        $stmt = $this->connection->prepare("SELECT email FROM Users WHERE userId = ?");
        $stmt->execute([$userId]);
        $email = $stmt->fetchColumn();
        return ($email !== false) ? $email : null;
    }

    public function updateUsername($userId, $newUsername)
    {
        $stmt = $this->connection->prepare("UPDATE Users SET username = ? WHERE userId = ?");
        $stmt->execute([$newUsername, $userId]);
    }

    public function updatePassword($userId, $newPassword)
    {
        $stmt = $this->connection->prepare("UPDATE Users SET password = ? WHERE userId = ?");
        $stmt->execute([$newPassword, $userId]);
    }

    public function returnPasswordById($userId): ?string
    {
        $stmt = $this->connection->prepare("SELECT password FROM Users WHERE userId = ?");
        $stmt->execute([$userId]);
        $hashedPassword = $stmt->fetchColumn();
        return ($hashedPassword !== false) ? $hashedPassword : null;
    }

    public function returnUserById($userId): ?User
    {
        $stmt = $this->connection->prepare("SELECT * FROM Users WHERE userId = ?");
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Models\User');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_CLASS);
        return ($user !== false) ? $user : null;
    }

    public function returnUserByUsername($username): ?User
    {
        $stmt = $this->connection->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Models\User');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_CLASS);
        return ($user !== false) ? $user : null;
    }

    public function returnAllUsers(): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM Users WHERE admin = 0");
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Models\User');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    public function returnPasswordByUsername($username): ?string
    {
        $stmt = $this->connection->prepare("SELECT password FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        $hashedPassword = $stmt->fetchColumn();
        return ($hashedPassword !== false) ? $hashedPassword : null;
    }

    public function deleteUser($userId)
    {
        $stmt = $this->connection->prepare("DELETE FROM Users WHERE userId = ?");
        $stmt->execute([$userId]);
    }

    // used for creating a new user
    function checkUsernameExists($enteredUsername): bool
    {
        $stmt = $this->connection->prepare("SELECT COUNT(*) as user_count FROM Users WHERE username = ?");
        $stmt->execute([$enteredUsername]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['user_count'] > 0);
    }
    public function checkEmailExists($enteredEmail): bool
    {
        $stmt = $this->connection->prepare("SELECT COUNT(*) as user_count FROM Users WHERE email = ?");
        $stmt->execute([$enteredEmail]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['user_count'] > 0);
    }

    public function createNewUser(User $user): ?User
    {
        $isAdmin = 0; // default value for admin

        $stmt = $this->connection->prepare("INSERT INTO Users (username, password, email, avatarId, admin) VALUES (?, ?, ?, ?, ?)");

        // bind parameters, used in the sql code above
        $username = $user->getUsername();
        $password = $user->getPassword();
        $email = $user->getEmail();
        $avatarId = $user->getAvatarId();
        $stmt->execute([$username, $password, $email, $avatarId, $isAdmin]);

        // if the row count is greater than 0, then the user was created
        if ($stmt->rowCount() > 0) {
            $lastInsertId = $this->connection->lastInsertId(); // returns the last inserted id, used to auto increment the user id
            return $this->returnUserById($lastInsertId);
        }

        return null;
    }
}

