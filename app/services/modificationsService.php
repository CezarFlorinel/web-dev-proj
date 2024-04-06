<?php

namespace App\Services;

use App\Models\Modification;
use App\Repositories\ModificationsRepository;

class ModificationsService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new ModificationsRepository();
    }

    public function getModifications(): array
    {
        return $this->repository->getModifications();
    }

    public function deleteModification(int $modificationId): void
    {
        $this->repository->deleteModification($modificationId);
    }

    public function addModification($name, $imagePath, $description, $estimatedPrice): void
    {
        $this->repository->addModification($name, $imagePath, $description, $estimatedPrice);
    }

    public function updateModification(int $modificationId, $name, $description, $estimatedPrice): void
    {
        $this->repository->updateModification($modificationId, $name, $description, $estimatedPrice);
    }

    public function updateModificationImagePath($imagePath, $modificationId): void
    {
        $this->repository->updateModificationImagePath($imagePath, $modificationId);
    }

    public function getModificationImagePath(int $modificationId): string
    {
        return $this->repository->getModificationImagePath($modificationId);
    }



}