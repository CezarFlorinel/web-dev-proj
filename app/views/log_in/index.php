<?php
include __DIR__ . '/../../public/components/general/topBar.php';
?>


<head>

    <link rel="stylesheet" type="text/css" href="/CSS Files/logIn.css">
</head>

<body class="logInBody">
    <h1 class="logInHeader text-center">Log In</h1>
    <form class="logInForm" action="/login/logIn" method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input class="form-control" id="username" name="username" type="text"
                value="<?= $_POST['username'] ?? '' ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" id="password" name="password" type="password">
        </div>
        <button type="submit" class="btn btn-primary">Log In</button>
        <p class="account-creation-p">Don't have an account? <a href="/createAccount">Sign Up</a></p>
    </form>

</body>




<?php
include __DIR__ . '/../../public/components/general/footer.php';
?>