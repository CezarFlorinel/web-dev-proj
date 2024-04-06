<?php
include __DIR__ . '/../../public/components/general/topBar.php';

use App\Services\QandAService;

$qAndAService = new QandAService();
$questionsAndAnswers = $qAndAService->getQandAs();

?>



<head>
    <link rel="stylesheet" type="text/css" href="/CSS Files/home.css">
</head>

<body class="homeBody">

    <div class="homeImage-container" id="carousel">
        <img class="homeImage" src="/images/elements/homeImage.png" alt="Home Image">
    </div>

    <h1 class="header-image">The World Of Guns</h1>

    <div class="p-first-block">
        <img class="gun-img" src="/images/elements/g2.webp">
        <p>
            Welcome to the World of Guns! Here you can find all the information you need about guns and modifications.
            You can also create an account and save your favourite guns and modifications.
            If you are a gun enthusiast, this is the right place for you!
        </p>
    </div>

    <div class="p-second-block">
        <img class="gun-img" src="/images/elements/g1.jpg">
        <p>
            The history of firearms is a fascinating journey that traces back to the 10th century, with the invention of
            gunpowder in China.
            This remarkable discovery led to the development of the first firearms, which were simple, hand-held
            cannons. Over the centuries, firearms evolved dramatically from the rudimentary matchlock muskets of the
            15th century to the precision-engineered flintlocks of the 18th century. The 19th century witnessed a
            revolutionary leap with the introduction of cartridge-based ammunition, paving the way for the modern era of
            firearms. This era saw rapid advancements, including the development of repeating rifles, automatic pistols,
            and eventually, the sophisticated assault rifles of the 20th century. Each epoch in the history of firearms
            not only reflects technological innovation but also the changing dynamics of warfare, hunting, and sport
            shooting, marking an indelible impact on society and culture across the globe.
        </p>
    </div>

    <h2>Practical Information</h2>
    <div class="QandA-info-container">
        <?php foreach ($questionsAndAnswers as $qAndA): ?>
            <div class="QandA-info-item">
                <img class="QandA-info-sign toggle-sign" src="images/elements/+ sign.png" alt="Toggle Answer"
                    data-toggle="closed">
                <p class="QandA-info-text">
                    <?php echo htmlspecialchars($qAndA->question); ?>
                </p>
            </div>
            <div class="QandA-info-answer" style="display:none;">
                <?php echo htmlspecialchars($qAndA->answer); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="javascript/home.js"></script>



</body>





<?php
include __DIR__ . '/../../public/components/general/footer.php';
?>