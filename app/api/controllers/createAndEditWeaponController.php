<?php
namespace App\Api\Controllers;

use App\Services\GunsService;
use App\Utilities\ErrorHandlerMethod;

class CreateAndEditWeaponController
{
    private $gunsService;

    public function __construct()
    {
        $this->gunsService = new GunsService();
    }

    public function editGun()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $gunId = (int) filter_var($_POST['gunId'], FILTER_SANITIZE_NUMBER_INT);
                $gunName = htmlspecialchars($_POST['gunName']) ?? '';
                $gunDescription = htmlspecialchars($_POST['gunDescription']) ?? '';
                $countryOfOrigin = htmlspecialchars($_POST['countryOfOrigin']) ?? '';
                $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT) ?? null;
                $estimatedPrice = filter_var($_POST['estimatedPrice'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? null;
                $typeOfGun = $_POST['typeOfGun'] ?? null;
                $showInGunsPage = filter_var($_POST['showInGunsPage'], FILTER_VALIDATE_BOOLEAN);
                $gunImagePath = null;
                $gunSoundPath = null;
                $gunImagePathCurrent = $this->gunsService->getImagePathByGunId($gunId);
                $gunSoundPathCurrent = $this->gunsService->getSoundPathByGunId($gunId);

                if (isset($_FILES['gunImage']) && $_FILES['gunImage']['error'] == UPLOAD_ERR_OK) {
                    $gunImagePath = $this->uploadFile('gunImage', 'images/guns');
                    $this->deleteFile($gunImagePathCurrent);
                } else {
                    // If no new image is uploaded, keep the current image path
                    $gunImagePath = $gunImagePathCurrent;
                }

                if (isset($_FILES['gunSound']) && $_FILES['gunSound']['error'] == UPLOAD_ERR_OK) {
                    $gunSoundPath = $this->uploadFile('gunSound', 'sounds/weapons_sounds');
                    $this->deleteFile($gunSoundPathCurrent);
                } else {
                    $gunSoundPath = $gunSoundPathCurrent;
                }

                if ($gunImagePath && $gunSoundPath) {
                    $this->gunsService->updateGun(
                        $gunId,
                        $gunName,
                        $gunDescription,
                        $countryOfOrigin,
                        $year,
                        $estimatedPrice,
                        $typeOfGun,
                        $gunImagePath,
                        $gunSoundPath,
                        $showInGunsPage
                    );

                    echo json_encode(['success' => true, 'message' => 'Gun updated successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error updating files.']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Only POST method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }
    public function addGun()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $userId = filter_var($_POST['userId'], FILTER_SANITIZE_NUMBER_INT) ?? null;
                $gunName = htmlspecialchars($_POST['gunName']) ?? '';
                $gunDescription = htmlspecialchars($_POST['gunDescription']) ?? '';
                $countryOfOrigin = htmlspecialchars($_POST['countryOfOrigin']) ?? '';
                $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT) ?? null;
                $estimatedPrice = filter_var($_POST['estimatedPrice'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? null;
                $typeOfGun = $_POST['typeOfGun'] ?? null;
                $showInGunsPage = filter_var($_POST['showInGunsPage'], FILTER_VALIDATE_BOOLEAN);

                // process image and sound upload
                $gunImagePath = $this->uploadFile('gunImage', 'images/guns');
                $gunSoundPath = $this->uploadFile('gunSound', 'sounds/weapons_sounds');

                if ($gunImagePath && $gunSoundPath) {
                    $this->gunsService->addGun(
                        $userId,
                        $gunName,
                        $gunDescription,
                        $countryOfOrigin,
                        $year,
                        $estimatedPrice,
                        $typeOfGun,
                        $gunImagePath,
                        $gunSoundPath,
                        $showInGunsPage
                    );

                    echo json_encode(['success' => true, 'message' => 'Gun added successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error uploading files.']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Only POST method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    private function uploadFile($fileKey, $subDir)
    {
        if (isset($_FILES[$fileKey])) {
            $file = $_FILES[$fileKey];
            $projectRoot = realpath(__DIR__ . '/../../..');
            $uploadsDir = $projectRoot . '/app/public/' . $subDir;
            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0777, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'audio/mpeg', 'audio/mp3', 'audio/wav'];

            if ($file['error'] === UPLOAD_ERR_OK && in_array($file['type'], $allowedTypes)) {
                $uniqueSuffix = time() . '-' . rand(); // Ensuring unique filename
                $newFileName = $uniqueSuffix . '-' . basename($file['name']);
                $destination = $uploadsDir . '/' . $newFileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    return "/$subDir/$newFileName";
                } else {
                    return null; // Indicate failure
                }
            }
        }
        return null; // Indicate failure
    }

    private function deleteFile($filePath)
    {
        $projectRoot = realpath(__DIR__ . '/../../..');
        $fullPath = $projectRoot . '/app/public' . $filePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }






}