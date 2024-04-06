<?php
include __DIR__ . '/../../public/components/general/topBar.php';

use App\Services\GunsService;
use App\Services\UserService;
use App\Services\ModificationsService;
use App\Services\QandAService;

$userService = new UserService();
$gunsService = new GunsService();
$modificationsService = new ModificationsService();
$qAndAService = new QandAService();
$usersUnconverted = $userService->returnAllUsers();
$users = $userService->convertUsers($usersUnconverted);

if (!isset($_SESSION['loggedin']) || $_SESSION['admin'] !== true) {
    header('Location: /');
    exit();
}

?>

<head>
    <link rel="stylesheet" type="text/css" href="/CSS Files/admin.css">
</head>

<body>
    <div class="container mt-4">
        <div class="adminContainer shadow-sm p-3 mb-5 bg-white rounded">
            <div class="adminContent">
                <h1 class="adminTitle">Welcome to the admin panel</h1>
                <h2 class="adminSubtitle">Here you can manage the users, modifications, and Q&A</h2>

                <!-- navigation of the page -->
                <div class="navigation mt-3">
                    <h3 class="jump-to-title">Jump to:</h3>
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link text-primary" href="#usersSection">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-primary" href="#qnaSection">Question and Answer</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-primary" href="#modificationsSection">Modifications</a>
                        </li>
                    </ul>
                </div>

            </div>
            <div class="adminContent">
                <h1 id="usersSection" class="adminSubtitle">Users</h1>
                <div class="adminList">
                    <ul class="list-group">
                        <?php foreach ($users as $user): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($user->getUsername()) ?>
                                <button data-type-user-id="<?php echo htmlspecialchars($user->getUserId()) ?>"
                                    class="delete-user-btn btn btn-danger btn-sm">Delete</button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <br>

                <h1 id="qnaSection" class="adminSubtitle">Question and Answer</h1>
                <div class="bg-light shadow rounded p-3">
                    <div id="containerForQandAstorage">
                        <!-- QandA cards will be added here -->
                    </div>

                    <div id="addForm" class="d-none p-3 bg-secondary border rounded">
                        <h2 class="h5 mb-2">Add New Question and Answer:</h2>
                        <div>
                            <label for="newQuestion">Question:</label>
                            <input type="text" id="newQuestion" class="form-control mt-1 mb-2">
                            <label for="newAnswer">Answer:</label>
                            <textarea id="newAnswer" class="form-control mt-1 mb-2"></textarea>
                            <button id="submitNewInfo"
                                class="btn btn-primary py-2 px-4 rounded hover:bg-opacity-75 transition">Submit</button>
                        </div>
                    </div>

                    <div class="p-3">
                        <button id="addQandABtn"
                            class="add-QandA-btn btn btn-success py-2 px-4 rounded hover:bg-opacity-75 transition">Add
                            +</button>
                    </div>
                </div>

                <h1 id="modificationsSection" class="adminSubtitle">Modifications:</h1>
                <div class="bg-light shadow rounded p-3">
                    <div id="containerForModifications">
                        <!-- Modification cards will be added here -->
                    </div>

                    <h2> Create new modification</h2>

                    <div id="newModificationCard"
                        class="modification-card p-3 border-bottom d-flex justify-content-between">
                        <div>
                            <p>Name:</p>
                            <input type="text" class="form-control nameInput mb-2" placeholder="Enter name" required>
                            <p>Description:</p>
                            <textarea class="form-control descriptionInput mb-2" placeholder="Enter description"
                                required></textarea>
                            <p>Estimated Price:</p>
                            <input type="number" step="0.01" class="form-control estimatedPriceInput mb-2"
                                placeholder="Enter price" required>
                            <p>Image:</p>
                            <input type="file" class="form-control imageInput mb-2" required>
                        </div>
                        <div>
                            <button
                                class="save-modification-btn btn btn-success py-2 px-4 rounded hover:bg-opacity-75 transition"
                                onclick="saveNewModification()">Save</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="javascript/admin.js"></script>


</body>




<?php
include __DIR__ . '/../../public/components/general/footer.php';
?>