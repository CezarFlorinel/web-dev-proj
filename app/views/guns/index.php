<?php
include __DIR__ . '/../../public/components/general/topBar.php';
use App\Services\GunsService;

$gunsService = new GunsService();

?>

<head>
    <link rel="stylesheet" type="text/css" href="/CSS Files/guns.css">
</head>

<body>
    <div class="image-container">
        <img class="image" src="/images/elements/guns.jpg" alt="Modification Image">
    </div>

    <div class="container mt-4">
        <?php include __DIR__ . '/../../public/components/general/searchAndFilter.php'; ?>

        <div class="container mt-4">
            <h1 class="mb-4">Guns Collection</h1>
            <div class="createButtonForAdminWithJs"><!-- button gets created here --> </div>
            <div class="row">
                <div class="guns_store col-12">
                    <!-- !!!!! Guns will be displayed here with javascript !!!!!! -->
                </div>
            </div>
        </div>
    </div>

    <div class="createButtonForAdminWithJs"><!-- button gets created here --> </div>

    <!-- data for javascript (i should change if time) -->
    <div id="phpData" style="display: none;"
        data-user-is-admin="<?php echo isset($_SESSION['admin']) && $_SESSION['admin']; ?>"
        data-user-is-logged-in="<?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin']; ?>"
        data-int-array-of-favourite-guns="<?php echo htmlspecialchars(json_encode(isset($_SESSION['user_id']) ? $gunsService->getIntArrayFavouriteGunsByUserId((int) $_SESSION['user_id']) : [])); ?>"
        data-user-logged-in-id="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>">
    </div>

    <script src="javascript/guns.js"></script>

</body>



<?php
include __DIR__ . '/../../public/components/general/footer.php';
?>