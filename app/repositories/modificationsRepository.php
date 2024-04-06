<?php
namespace App\Repositories;

use PDO;
use App\Models\Modification;

class ModificationsRepository extends Repository
{
    public function getModifications(): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM Modification');
        $stmt->execute();
        $modifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($modification) {
            return new Modification(
                $modification['modificationId'],
                $modification['modificationName'],
                $modification['modificationImagePath'],
                $modification['modificationDescription'],
                $modification['modificationEstimatedPrice']
            );
        }, $modifications);
    }

    public function getModificationImagePath(int $modificationId): string
    {
        $stmt = $this->connection->prepare('SELECT modificationImagePath FROM Modification WHERE modificationId = :modificationId');
        $stmt->bindParam(':modificationId', $modificationId);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function deleteModification(int $modificationId): void
    {
        $stmt = $this->connection->prepare('DELETE FROM Modification WHERE modificationId = :modificationId');
        $stmt->bindParam(':modificationId', $modificationId);
        $stmt->execute();
    }

    public function addModification($name, $imagePath, $description, $estimatedPrice): void
    {
        $stmt = $this->connection->prepare('INSERT INTO Modification (modificationName, modificationImagePath, modificationDescription, modificationEstimatedPrice) VALUES (:name, :imagePath, :description, :estimatedPrice)');
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':imagePath', $imagePath);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':estimatedPrice', $estimatedPrice);
        $stmt->execute();
    }

    public function updateModification(int $modificationId, $name, $description, $estimatedPrice): void
    {
        $stmt = $this->connection->prepare('UPDATE Modification SET modificationName = :name, modificationDescription = :description, modificationEstimatedPrice = :estimatedPrice WHERE modificationId = :modificationId');
        $stmt->bindParam(':modificationId', $modificationId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':estimatedPrice', $estimatedPrice);
        $stmt->execute();
    }

    public function updateModificationImagePath($imagePath, $modificationId): void
    {
        $stmt = $this->connection->prepare('UPDATE Modification SET modificationImagePath = :imagePath WHERE modificationId = :modificationId');
        $stmt->bindParam(':imagePath', $imagePath);
        $stmt->bindParam(':modificationId', $modificationId);
        $stmt->execute();
    }

}