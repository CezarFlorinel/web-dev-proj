<?php
include __DIR__ . '/../../public/components/general/topBar.php';
use App\Models\Enumerations\TypeOfGuns;
use App\Services\GunsService;

$typesOfGuns = TypeOfGuns::cases();

$userId = null;
$isAdmin = false;
$checkIsGunPresent = false;
$gun = null;
$gunsService = new GunsService();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
} else {
    $userId = $_SESSION['user_id'];
    if (isset($_SESSION['admin'])) {
        $isAdmin = $_SESSION['admin'];
    }
}

$checkIsGunPresent = false;

if (isset($_GET['gunId'])) {
    $gun = $gunsService->getGunById($_GET['gunId']);
    $checkIsGunPresent = true;
}
?>



<head>
    <title>Edit/Create Guns</title>
    <link rel="stylesheet" type="text/css" href="/CSS Files/createWeapons.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Edit/Create Gun</h1>
        <form id="createGunForm" enctype="multipart/form-data" data-type-admin-bool="<?php echo $isAdmin ?>"
            data-type-userid="<?php echo htmlspecialchars($userId) ?>"
            data-type-gunpresent="<?php echo $checkIsGunPresent ?>" data-type-gunid="<?php if ($checkIsGunPresent) {
                   echo $gun->gunId;  // hmm, should this be htmlspecialchars()? the id is made in the database as integer, so it should be safe to use it as is
               } ?>">
            <div class="form-group">
                <label for="gunName">Name</label>
                <input type="text" class="form-control" id="gunName" name="gunName"
                    value="<?php echo $checkIsGunPresent ? htmlspecialchars($gun->gunName, ENT_QUOTES, 'UTF-8') : ''; ?>"
                    required>
            </div>
            <div class="form-group">
                <label for="gunDescription">Description</label>
                <textarea class="form-control" id="gunDescription" rows="3" maxlength="999"
                    required><?php echo $checkIsGunPresent ? htmlspecialchars($gun->description, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="gunYear">Year</label>
                <input type="number" class="form-control" id="gunYear" step="1"
                    value="<?php echo $checkIsGunPresent ? htmlspecialchars($gun->year) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="estimatedPrice">Estimated Price</label>
                <input type="number" class="form-control" id="estimatedPrice"
                    value="<?php echo $checkIsGunPresent ? htmlspecialchars($gun->estimatedPrice) : ''; ?>" step="0.01"
                    required>
            </div>
            <div class="form-group">
                <label for="gunCountry">Country of Origin</label>
                <input type="text" class="form-control" id="gunCountry"
                    value="<?php echo $checkIsGunPresent ? htmlspecialchars($gun->countryOfOrigin, ENT_QUOTES, 'UTF-8') : ''; ?>"
                    required>
            </div>
            <div class="form-group">
                <label for="gunType">Type</label>
                <select class="form-control" id="gunType" name="gunType" required>
                    <?php foreach ($typesOfGuns as $type): ?>
                        <option value="<?php echo htmlspecialchars($type->value, ENT_QUOTES, 'UTF-8'); ?>" <?php if ($checkIsGunPresent && $gun->typeOfGun->value === $type->value)
                                 echo 'selected'; ?>>
                            <?php echo htmlspecialchars($type->value, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <?php if ($checkIsGunPresent && $gun->imagePath): ?>
                    <img src="<?php echo htmlspecialchars($gun->imagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="Gun Image"
                        style="max-width: 200px; max-height: 200px;">
                <?php endif; ?>
                <label for="gunImage">Gun Image</label>
                <input type="file" class="form-control-file" id="gunImage" <?php if (!$checkIsGunPresent)
                    echo 'required'; ?>>
            </div>
            <div class="form-group">
                <?php if ($checkIsGunPresent && $gun->soundPath): ?>
                    <button class="btn-1" type="button" onclick="playSound()">Play Sound</button>
                    <audio id="gunSoundAudio"
                        src="<?php echo htmlspecialchars($gun->soundPath, ENT_QUOTES, 'UTF-8'); ?>"></audio>
                <?php endif; ?>
                <label for="gunSound">Gun Sound</label>
                <input type="file" class="form-control-file" id="gunSound" <?php if (!$checkIsGunPresent)
                    echo 'required'; ?>>
            </div>
            <div class="btn-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <script src="javascript/createAndEditWeapons.js"></script>

</body>



<?php
include __DIR__ . '/../../public/components/general/footer.php';
?>