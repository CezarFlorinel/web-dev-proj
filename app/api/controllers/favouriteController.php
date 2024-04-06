<?php
namespace App\Api\Controllers;

use App\Services\GunsService;
use App\Utilities\ErrorHandlerMethod;

class FavouriteController // should have fused this with guns controller
{
    private $gunsService;

    public function __construct()
    {
        $this->gunsService = new GunsService();
    }

    public function displayGunsBasedOnSearchTerm()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $searchTerm = htmlspecialchars($_GET['searchTerm']) ?? '';
                $isGunPage = false;
                $guns = $this->gunsService->searchGunsByNameInGunsPage($searchTerm, $isGunPage);

                header('Content-Type: application/json');
                echo json_encode($guns);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Only GET method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function displayGunsBasedOnType()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $type = htmlspecialchars($_GET['type']) ?? '';
                $isGunPage = false;
                $guns = $this->gunsService->filterGunsByTypeInGunsPage($type, $isGunPage);

                header('Content-Type: application/json');
                echo json_encode($guns);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Only GET method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function displayGuns()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $guns = $this->gunsService->getGuns();

                header('Content-Type: application/json');
                echo json_encode($guns);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Only GET method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function removeGunFromFavourites()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                if (isset($input['userId'], $input['gunId'])) {
                    $userId = filter_var($input['userId'], FILTER_VALIDATE_INT);
                    $gunId = filter_var($input['gunId'], FILTER_VALIDATE_INT);

                    $this->gunsService->removeGunFromFavourites($userId, $gunId);
                    echo json_encode(['success' => true, 'message' => 'Successfully removed from favourites']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Missing user ID or gun ID']);
                }

            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Only POST method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function deleteGun()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                $input = json_decode(file_get_contents('php://input'), true);
                if (isset($input['gunId'])) {
                    $gunId = filter_var($input['gunId'], FILTER_VALIDATE_INT);

                    // Fetch gun details (presumably to get file paths)
                    $gun = $this->gunsService->getGunById($gunId);
                    if (!$gun) {
                        http_response_code(404);
                        echo json_encode(['success' => false, 'error' => 'Gun not found']);
                        return;
                    }

                    // Attempt to delete image and sound files
                    $projectRoot = realpath(__DIR__ . '/../../..');
                    $fileErrors = [];
                    foreach ([$gun->imagePath, $gun->soundPath] as $filePath) {
                        if ($filePath && file_exists($projectRoot . '/app/public' . $filePath)) {
                            if (!unlink($projectRoot . '/app/public' . $filePath)) {
                                $fileErrors[] = 'Failed to delete ' . $filePath;
                            }
                        }
                    }

                    // If there were file deletion errors, don't delete gun from DB and return an error
                    if (!empty($fileErrors)) {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'errors' => $fileErrors]);
                        return;
                    }

                    // Delete gun from database
                    $this->gunsService->deleteGun($gunId);
                    echo json_encode(['success' => true, 'message' => 'Successfully deleted gun']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Missing gun ID']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Only DELETE method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }




}