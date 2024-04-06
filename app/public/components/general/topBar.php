<?php
session_start();

// code for creating the moddal box
use App\Utilities\SessionManager;
use App\Utilities\Modal;

$sessionManager = new SessionManager();
$error = $sessionManager->getError();
if ($error) {
    $errorModal = new Modal('errorModal', htmlspecialchars($error));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>World Of Guns</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify@2/dist/purify.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="/CSS Files/jsCustomeAlert.css">
    <link rel="stylesheet" type="text/css" href="/CSS Files/topBar.css">
    <link rel="icon" type="image/x-icon" href="/images/elements/logo.webp">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/CSS Files/modal.css">
    <script src="javascript/errorHandlerClass.js"></script>
</head>

<body>
    <!-- Error Modal -->
    <?php if (isset($errorModal)): ?>
        <?php $errorModal->render(); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {
                    keyboard: false
                });
                errorModal.show();
                document.querySelectorAll('.btn-success, .close').forEach(function (element) {
                    element.addEventListener('click', function () {
                        errorModal.hide();
                    });
                });
            });
        </script>
    <?php endif; ?>

    <main>
        <header class="header">
            <div class="logoItems">
                <a href="/" class="logoLink">
                    <img class="logo" src="/images/elements/logo.webp" alt="Logo">
                </a>
                <p class="logoTitle">The World Of Guns</p>
            </div>

            <nav class="navigation">
                <a href="/" class="nav-link">Home</a>
                <a href="/guns" class="nav-link">Guns</a>
                <a href="/modifications" class="nav-link">Modifications</a>

                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
                    <a href="/admin" class="nav-link">Admin</a>
                <?php elseif (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <a href="/favourite" class="nav-link">Favourite</a>
                <?php else: ?>
                    <a href="/login" class="nav-link">Favourite</a>
                <?php endif; ?>

                <a href="/login">
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <a href="/profile">
                            <img class="AccountIcon" src="<?php echo $_SESSION['avatar_path']; ?>" alt="User Avatar">
                        </a>
                        <script>console.log('Logged in: Redirecting to profile.');</script>
                    <?php else: ?>
                        <a href="/login">
                            <img class="AccountIcon" src="/images/elements/profile-user.png" alt="Default Profile">
                        </a>
                        <script>console.log('Not logged in: Redirecting to login.');</script>
                    <?php endif; ?>
                </a>
            </nav>
        </header>

        <audio id="clickSound" src="/sounds/elements/shoot_cursror_sound.mp3"></audio>
    </main>

    <script>
        // function to play sound
        function playClickSound() {
            var sound = document.getElementById("clickSound");
            sound.play();
        }
        // event listener for any click on the document
        document.addEventListener('click', playClickSound);
    </script>

</body>