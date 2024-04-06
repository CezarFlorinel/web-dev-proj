<?php
namespace App\Repositories;

use PDO;
use App\Models\Gun;
use App\Models\Enumerations\TypeOfGuns;

class GunRepository extends Repository
{

    // -------------------------- get methods --------------------------

    public function getIDsOfGunsOwnedByUser(int $userId): array
    {
        $stmt = $this->connection->prepare('SELECT gunId FROM Guns WHERE userId = :userId');
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    public function getGuns(): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM Guns');
        $stmt->execute();
        $guns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($gun) {
            $typeOfGun = TypeOfGuns::tryFrom($gun['type']) ?? throw new \InvalidArgumentException("Invalid gun type");

            $gunData = new Gun(
                $gun['gunId'],
                $gun['userId'],
                $gun['gunName'],
                $gun['gunDescription'],
                $gun['countryOfOrigin'],
                $gun['gunEstimatedPrice'],
                $typeOfGun,
                $gun['gunImagePath'],
                $gun['soundPath'],
                $gun['showInGunsPage'],
                $gun['year'] ?? 0
            );


            return [
                'gunId' => $gunData->gunId,
                'userId' => $gunData->userId,
                'gunName' => $gunData->gunName,
                'description' => $gunData->description,
                'countryOfOrigin' => $gunData->countryOfOrigin,
                'year' => $gunData->year,
                'estimatedPrice' => $gunData->estimatedPrice,
                'gunType' => $gunData->typeOfGun->value,
                'imagePath' => $gunData->imagePath,
                'soundPath' => $gunData->soundPath,
                'showInGunsPage' => $gunData->showInGunsPage
            ];

        }, $guns);
    }

    public function getGunsToDisplayInGunsPage(): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM Guns WHERE showInGunsPage = 1');
        $stmt->execute();
        $guns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($gunData) {
            // Assuming TypeOfGuns is an enum and 'from' throws an exception if the value is not valid.
            $typeOfGun = TypeOfGuns::tryFrom($gunData['type']) ?? throw new \InvalidArgumentException("Invalid gun type");

            $gun = new Gun(
                $gunData['gunId'],
                $gunData['userId'],
                $gunData['gunName'],
                $gunData['gunDescription'],
                $gunData['countryOfOrigin'],
                $gunData['gunEstimatedPrice'],
                $typeOfGun, // Now correctly an instance of TypeOfGuns
                $gunData['gunImagePath'],
                $gunData['soundPath'],
                $gunData['showInGunsPage'],
                $gunData['year'] ?? 0
            );

            // Now convert Gun object into an array for JSON response, including converting the enum to a string
            return [
                'gunId' => $gun->gunId,
                'userId' => $gun->userId,
                'gunName' => $gun->gunName,
                'description' => $gun->description,
                'countryOfOrigin' => $gun->countryOfOrigin,
                'year' => $gun->year,
                'estimatedPrice' => $gun->estimatedPrice,
                'gunType' => $gun->typeOfGun->value, // Convert enum to string for JSON
                'imagePath' => $gun->imagePath,
                'soundPath' => $gun->soundPath,
                'showInGunsPage' => $gun->showInGunsPage
            ];


        }, $guns);
    }

    public function getGunById(int $gunId): Gun
    {
        $stmt = $this->connection->prepare('SELECT * FROM Guns WHERE gunId = :gunId'); // Corrected table name to 'Guns'
        $stmt->bindParam(':gunId', $gunId);
        $stmt->execute();
        $gun = $stmt->fetch(PDO::FETCH_ASSOC);

        // Handle the possibility of the gun not being found
        if (!$gun) {
            throw new \Exception("Gun not found with ID $gunId");
        }

        // Convert 'type' from the database to 'TypeOfGuns' enum
        $typeOfGun = TypeOfGuns::tryFrom($gun['type']) ?? throw new \InvalidArgumentException("Invalid gun type");

        return new Gun(
            $gun['gunId'],
            $gun['userId'],
            $gun['gunName'],
            $gun['gunDescription'],
            $gun['countryOfOrigin'],
            $gun['gunEstimatedPrice'],
            $typeOfGun, // Corrected type conversion
            $gun['gunImagePath'],
            $gun['soundPath'],
            $gun['showInGunsPage'],
            $gun['year']
        );
    }

    public function getIntArrayFavouriteGunsByUserId(int $userId): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM Favourite WHERE userId = :userId');
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $favourites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($favourite) {
            return $favourite['gunId'];
        }, $favourites);
    }

    public function getImagePathByGunId(int $gunId): string
    {
        $stmt = $this->connection->prepare('SELECT gunImagePath FROM Guns WHERE gunId = :gunId');
        $stmt->bindParam(':gunId', $gunId);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getSoundPathByGunId(int $gunId): string
    {
        $stmt = $this->connection->prepare('SELECT soundPath FROM Guns WHERE gunId = :gunId');
        $stmt->bindParam(':gunId', $gunId);
        $stmt->execute();
        return $stmt->fetchColumn();
    }


    // -------------------------- delete methods --------------------------
    public function removeGunFromFavourites(int $userId, int $gunId): void
    {
        $stmt = $this->connection->prepare('DELETE FROM Favourite WHERE userId = :userId AND gunId = :gunId');
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':gunId', $gunId);
        $stmt->execute();
    }

    public function removeGunFromFavouritesByGunId(int $gunId): void
    {
        $stmt = $this->connection->prepare('DELETE FROM Favourite WHERE gunId = :gunId');
        $stmt->bindParam(':gunId', $gunId);
        $stmt->execute();
    }

    public function deleteGun(int $gunId): void
    {
        $this->removeGunFromFavouritesByGunId($gunId);

        $stmt = $this->connection->prepare('DELETE FROM Guns WHERE gunId = :gunId');
        $stmt->bindParam(':gunId', $gunId);
        $stmt->execute();
    }


    // -------------------------- search and filter methods --------------------------
    public function filterGunsByTypeInGunsPage($type, $isGunPage): array
    {

        if ($isGunPage) {
            $stmt = $this->connection->prepare('SELECT * FROM Guns WHERE type = :type AND showInGunsPage = 1');

        } else {
            $stmt = $this->connection->prepare('SELECT * FROM Guns WHERE type = :type');
        }
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        $guns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($gunData) {
            // Convert database data into Gun object here
            $typeOfGun = TypeOfGuns::tryFrom($gunData['type']) ?? throw new \InvalidArgumentException("Invalid gun type");
            $gun = new Gun(
                $gunData['gunId'],
                $gunData['userId'],
                $gunData['gunName'],
                $gunData['gunDescription'],
                $gunData['countryOfOrigin'],
                $gunData['gunEstimatedPrice'],
                $typeOfGun, // Now correctly an instance of TypeOfGuns
                $gunData['gunImagePath'],
                $gunData['soundPath'],
                $gunData['showInGunsPage'],
                $gunData['year'] ?? 0
            );

            // Now convert Gun object into an array for JSON response, including converting the enum to a string
            return [
                'gunId' => $gun->gunId,
                'userId' => $gun->userId,
                'gunName' => $gun->gunName,
                'description' => $gun->description,
                'countryOfOrigin' => $gun->countryOfOrigin,
                'year' => $gun->year,
                'estimatedPrice' => $gun->estimatedPrice,
                'gunType' => $gun->typeOfGun->value, // Convert enum to string for JSON
                'imagePath' => $gun->imagePath,
                'soundPath' => $gun->soundPath,
                'showInGunsPage' => $gun->showInGunsPage
            ];
        }, $guns);
    }

    public function searchGunsByNameInGunsPage($searchTerm, $isGunPage): array
    {
        $searchTerm = "%" . $searchTerm . "%";

        if ($isGunPage) {
            $stmt = $this->connection->prepare('SELECT * FROM Guns WHERE gunName LIKE :searchTerm AND showInGunsPage = 1');
        } else {
            $stmt = $this->connection->prepare('SELECT * FROM Guns WHERE gunName LIKE :searchTerm');
        }
        $stmt->bindParam(':searchTerm', $searchTerm);
        $stmt->execute();
        $guns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($gunData) {
            // Convert database data into Gun object here
            $typeOfGun = TypeOfGuns::tryFrom($gunData['type']) ?? throw new \InvalidArgumentException("Invalid gun type");
            $gun = new Gun(
                $gunData['gunId'],
                $gunData['userId'],
                $gunData['gunName'],
                $gunData['gunDescription'],
                $gunData['countryOfOrigin'],
                $gunData['gunEstimatedPrice'],
                $typeOfGun, // Now correctly an instance of TypeOfGuns
                $gunData['gunImagePath'],
                $gunData['soundPath'],
                $gunData['showInGunsPage'],
                $gunData['year'] ?? 0
            );

            // Now convert Gun object into an array for JSON response, including converting the enum to a string
            return [
                'gunId' => $gun->gunId,
                'userId' => $gun->userId,
                'gunName' => $gun->gunName,
                'description' => $gun->description,
                'countryOfOrigin' => $gun->countryOfOrigin,
                'year' => $gun->year,
                'estimatedPrice' => $gun->estimatedPrice,
                'gunType' => $gun->typeOfGun->value, // Convert enum to string for JSON
                'imagePath' => $gun->imagePath,
                'soundPath' => $gun->soundPath,
                'showInGunsPage' => $gun->showInGunsPage
            ];
        }, $guns);
    }


    // -------------------------- add methods --------------------------
    public function addGun(int $userId, string $gunName, string $gunDescription, string $countryOfOrigin, ?int $year, float $gunEstimatedPrice, string $typeOfGun, string $gunImagePath, string $soundPath, bool $showInGunsPage): void
    {
        $query = 'INSERT INTO Guns (userId, gunName, gunDescription, countryOfOrigin, year, gunEstimatedPrice, type, gunImagePath, soundPath, showInGunsPage) VALUES (:userId, :gunName, :gunDescription, :countryOfOrigin, :year, :gunEstimatedPrice, :type, :gunImagePath, :soundPath, :showInGunsPage)';

        $stmt = $this->connection->prepare($query);

        // Bind parameters
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':gunName', $gunName);
        $stmt->bindParam(':gunDescription', $gunDescription);
        $stmt->bindParam(':countryOfOrigin', $countryOfOrigin);
        $stmt->bindParam(':gunEstimatedPrice', $gunEstimatedPrice);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':type', $typeOfGun);
        $stmt->bindParam(':gunImagePath', $gunImagePath);
        $stmt->bindParam(':soundPath', $soundPath);
        $stmt->bindParam(':showInGunsPage', $showInGunsPage, PDO::PARAM_BOOL);

        $stmt->execute();
    }

    public function addGunToFavourites(int $userId, int $gunId): void
    {
        $stmt = $this->connection->prepare('INSERT INTO Favourite (userId, gunId) VALUES (:userId, :gunId)');
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':gunId', $gunId);
        $stmt->execute();
    }


    // -------------------------- update methods --------------------------

    public function updateGun(int $gunId, string $gunName, string $gunDescription, string $countryOfOrigin, ?int $year, float $gunEstimatedPrice, string $type, string $gunImagePath, string $soundPath, bool $showInGunsPage): void
    {
        $query = 'UPDATE Guns SET 
                gunName = :gunName, 
                gunDescription = :gunDescription, 
                countryOfOrigin = :countryOfOrigin, 
                year = :year,
                gunEstimatedPrice = :gunEstimatedPrice, 
                type = :type, 
                gunImagePath = :gunImagePath, 
                soundPath = :soundPath, 
                showInGunsPage = :showInGunsPage 
              WHERE gunId = :gunId';

        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':gunId', $gunId);
        $stmt->bindParam(':gunName', $gunName);
        $stmt->bindParam(':gunDescription', $gunDescription);
        $stmt->bindParam(':countryOfOrigin', $countryOfOrigin);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':gunEstimatedPrice', $gunEstimatedPrice);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':gunImagePath', $gunImagePath);
        $stmt->bindParam(':soundPath', $soundPath);
        $stmt->bindParam(':showInGunsPage', $showInGunsPage, PDO::PARAM_BOOL);

        $stmt->execute();
    }










}