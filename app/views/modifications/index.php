<?php
include __DIR__ . '/../../public/components/general/topBar.php';

use App\Services\ModificationsService;

$modificationsService = new ModificationsService();
$modifications = $modificationsService->getModifications();
?>


<head>
    <link rel="stylesheet" type="text/css" href="/CSS Files/modifications.css">
</head>

<body>
    <div class="image-container">
        <img class="image" src="/images/elements/Modifications-image-page.jpg" alt="Modification Image">
    </div>

    <!-- Modifications Grid -->
    <div class="container mt-5">

        <h1 class="header-image">Modifications</h1>

        <div class="row">
            <?php foreach ($modifications as $modification): ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="modification-item bg-dark text-white p-3">
                        <img src="<?= $modification->imagePath ?>" class="img-fluid"
                            alt="<?= htmlspecialchars($modification->name) ?>">
                        <h5 class="mt-3">
                            <?= htmlspecialchars($modification->name) ?>
                        </h5>
                        <p class="description">Description:
                            <?= htmlspecialchars($modification->description) ?>
                        </p>
                        <p class="estimated-price">Estimated Price:
                            <?= htmlspecialchars($modification->estimatedPrice) ?> $
                        </p>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
</body>



<?php
include __DIR__ . '/../../public/components/general/footer.php';
?>