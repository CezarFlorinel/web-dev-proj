<?php
include __DIR__ . '/../../public/components/general/topBar.php';

use App\Services\GunsService;

$gunsService = new GunsService();

?>

<head>
    <link rel="stylesheet" type="text/css" href="/CSS Files/guns.css">
</head> <!-- could have made some componets or merge it with the gun page, check it out for web dev 2  -->

<body>
    <div class="image-container">
        <img class="image" src="/images/elements/favourite.png" alt="Modification Image">
    </div>

    <div class="container mt-4">
        <?php include __DIR__ . '/../../public/components/general/searchAndFilter.php'; ?>

        <div class="container mt-4">
            <h1 class="mb-4">Guns Collection</h1>

            <?php include __DIR__ . '/../../public/components/gunsDisplayPage/addGunButton.php'; ?>

            <div class="row">
                <h2 class="mb-4">Guns Made By You</h2>
                <div class="guns_store_user col-12">
                    <!-- !!!!! Guns will be displayed here with javascript !!!!!! -->
                </div>
                <h2 class="mb-4">Favourite Guns</h2>
                <div class="guns_store col-12">
                    <!-- !!!!! Guns will be displayed here with javascript !!!!!! -->
                </div>
            </div>

        </div>
    </div>
    <?php include __DIR__ . '/../../public/components/gunsDisplayPage/addGunButton.php'; ?>

    <div id="phpData" style="display: none;"
        data-user-is-logged-in="<?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin']; ?>"
        data-int-array-of-favourite-guns="<?php echo htmlspecialchars(json_encode(isset($_SESSION['user_id']) ? $gunsService->getIntArrayFavouriteGunsByUserId((int) $_SESSION['user_id']) : [])); ?>"
        data-user-logged-in-id="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>">
    </div>

    <script src="javascript/favourite.js"></script>

</body>



<?php
include __DIR__ . '/../../public/components/general/footer.php';
?>