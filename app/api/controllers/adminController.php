<?php
namespace App\Api\Controllers;

use App\Services\GunsService;
use App\Services\UserService;
use App\Services\ModificationsService;
use App\Services\QandAService;
use App\Utilities\ErrorHandlerMethod;

class AdminController
{
    private $gunsService;
    private $usersService;
    private $modificationsService;
    private $qAndAService;

    public function __construct()
    {
        $this->gunsService = new GunsService();
        $this->usersService = new UserService();
        $this->modificationsService = new ModificationsService();
        $this->qAndAService = new QandAService();
    }

    public function deleteUser()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                $input = json_decode(file_get_contents('php://input'), true);
                if (isset($input['userId'])) {
                    $fileErrors = [];
                    $userId = filter_var($input['userId'], FILTER_SANITIZE_NUMBER_INT);


                    $allGunsOwnedByUser = $this->gunsService->getIDsOfGunsOwnedByUser($userId);

                    $projectRoot = realpath(__DIR__ . '/../../..');
                    foreach ($allGunsOwnedByUser as $gunId) {
                        $imagePath = $this->gunsService->getImagePathByGunId($gunId);
                        $soundPath = $this->gunsService->getSoundPathByGunId($gunId);

                        foreach ([$imagePath, $soundPath] as $filePath) {
                            if ($filePath && file_exists($projectRoot . '/app/public' . $filePath)) {
                                if (!unlink($projectRoot . '/app/public' . $filePath)) {
                                    $fileErrors[] = 'Failed to delete ' . $filePath;
                                }
                            }
                        }
                    }

                    if (!empty($fileErrors)) {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'errors' => $fileErrors]);
                        return;
                    }

                    $this->usersService->deleteUser($userId);

                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'User deleted']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'User id is required']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Only POST method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function getQandAs()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $QandAs = $this->qAndAService->getQandAs();
                echo json_encode($QandAs);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Only GET method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function addQandA()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                if (isset($input['question'], $input['answer'])) {
                    $question = htmlspecialchars($input['question']);
                    $answer = htmlspecialchars($input['answer']);

                    $this->qAndAService->addQandA($question, $answer);

                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'QandA added']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Question and answer are required']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Only POST method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function editQandA()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                if (isset($input['id'], $input['question'], $input['answer'])) {
                    $id = filter_var($input['id'], FILTER_SANITIZE_NUMBER_INT);
                    $question = htmlspecialchars($input['question']);
                    $answer = htmlspecialchars($input['answer']);

                    $this->qAndAService->editQandA($id, $question, $answer);

                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'QandA edited']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Id, question and answer are required']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Only POST method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function deleteQandA()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                $input = json_decode(file_get_contents('php://input'), true);
                if (isset($input['id'])) {
                    $id = filter_var($input['id'], FILTER_SANITIZE_NUMBER_INT);
                    $this->qAndAService->deleteQandA($id);

                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'QandA deleted successfully']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Id is required']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Only DELETE method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function getModifications()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $modifications = $this->modificationsService->getModifications();
                echo json_encode($modifications);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Only GET method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function deleteModification()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                $input = json_decode(file_get_contents('php://input'), true);
                if (isset($input['modificationId'])) {
                    $modificationId = filter_var($input['modificationId'], FILTER_SANITIZE_NUMBER_INT);

                    $imagePath = $this->modificationsService->getModificationImagePath($modificationId);
                    if ($imagePath) {
                        $this->deleteFile($imagePath);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Failed to delete image']);
                        return;
                    }

                    $this->modificationsService->deleteModification($modificationId);

                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'Modification deleted']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Modification id is required']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Only DELETE method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function addModification()
    {
        try {

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['name'], $_POST['description'], $_POST['estimatedPrice'])) {

                    $name = htmlspecialchars($_POST['name']);
                    $description = htmlspecialchars($_POST['description']);
                    $estimatedPrice = filter_var($_POST['estimatedPrice'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                    $modificationImagePath = null;
                    $modificationImagePath = $this->uploadFile('image', 'images/modifications');

                    if (!$modificationImagePath) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                        return;
                    }

                    $this->modificationsService->addModification($name, $modificationImagePath, $description, $estimatedPrice);

                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'Modification added']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Name, image, description and estimated price are required']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Only POST method is supported']);
            }
        } catch (\Exception $e) {
            ErrorHandlerMethod::handleErrorApiController($e);
        }
    }

    public function editModification()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['modificationId'], $_POST['name'], $_POST['description'], $_POST['estimatedPrice'])) {
                    $modificationId = filter_var($_POST['modificationId'], FILTER_SANITIZE_NUMBER_INT);
                    $name = htmlspecialchars($_POST['name']);
                    $description = htmlspecialchars($_POST['description']);
                    $estimatedPrice = filter_var($_POST['estimatedPrice'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                    $modificationImagePath = null;

                    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                        $modificationImagePath = $this->uploadFile('image', 'images/modifications');
                        if ($modificationImagePath) {
                            $currentImagePath = $this->modificationsService->getModificationImagePath($modificationId);
                            $this->modificationsService->updateModificationImagePath($modificationImagePath, $modificationId);
                            $this->deleteFile($currentImagePath);
                        }
                    }

                    $this->modificationsService->updateModification($modificationId, $name, $description, $estimatedPrice);

                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'Modification edited']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Modification id, name, description and estimated price are required']);
                }
            } else {
                http_response_code(400);
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

            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

            if ($file['error'] === UPLOAD_ERR_OK && in_array($file['type'], $allowedTypes)) {
                $uniqueSuffix = time() . '-' . rand();
                $newFileName = $uniqueSuffix . '-' . basename($file['name']);
                $destination = $uploadsDir . '/' . $newFileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    return "/$subDir/$newFileName";
                } else {
                    return null; // Indicate failure
                }
            }
        }
        return null; // Indicate no file or failure
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