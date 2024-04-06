<?php

namespace App\Services;

use App\Repositories\GunRepository;

class GunsService
{
    private $repository;
    public function __construct()
    {
        $this->repository = new GunRepository();
    }

    public function getIDsOfGunsOwnedByUser(int $userId): array
    {
        return $this->repository->getIDsOfGunsOwnedByUser($userId);
    }

    public function getIntArrayFavouriteGunsByUserId(int $userId): array
    {
        return $this->repository->getIntArrayFavouriteGunsByUserId($userId);
    }

    public function addGunToFavourites(int $userId, int $gunId): void
    {
        $this->repository->addGunToFavourites($userId, $gunId);
    }

    public function removeGunFromFavourites(int $userId, int $gunId): void
    {
        $this->repository->removeGunFromFavourites($userId, $gunId);
    }

    public function searchGunsByNameInGunsPage($searchTerm, $isGunPage): array
    {
        return $this->repository->searchGunsByNameInGunsPage($searchTerm, $isGunPage);
    }

    public function getGunsToDisplayInGunsPage(): array
    {
        return $this->repository->getGunsToDisplayInGunsPage();
    }
    public function filterGunsByTypeInGunsPage($type, $isGunPage): array
    {
        return $this->repository->filterGunsByTypeInGunsPage($type, $isGunPage);
    }

    public function getGuns(): array
    {
        return $this->repository->getGuns();
    }
    public function getGunById(int $gunId)
    {
        return $this->repository->getGunById($gunId);
    }

    public function getImagePathByGunId(int $gunId): string
    {
        return $this->repository->getImagePathByGunId($gunId);
    }

    public function getSoundPathByGunId(int $gunId): string
    {
        return $this->repository->getSoundPathByGunId($gunId);
    }

    public function updateGun(int $gunId, string $gunName, string $gunDescription, string $countryOfOrigin, ?int $year, float $gunEstimatedPrice, string $type, string $gunImagePath, string $soundPath, bool $showInGunsPage): void
    {
        $this->repository->updateGun($gunId, $gunName, $gunDescription, $countryOfOrigin, $year, $gunEstimatedPrice, $type, $gunImagePath, $soundPath, $showInGunsPage);
    }

    public function deleteGun(int $gunId): void
    {
        $this->repository->deleteGun($gunId);
    }

    public function addGun(int $userId, string $gunName, string $gunDescription, string $countryOfOrigin, ?int $year, float $gunEstimatedPrice, string $typeOfGun, string $gunImagePath, string $soundPath, bool $showInGunsPage): void
    {
        $this->repository->addGun($userId, $gunName, $gunDescription, $countryOfOrigin, $year, $gunEstimatedPrice, $typeOfGun, $gunImagePath, $soundPath, $showInGunsPage);
    }


}