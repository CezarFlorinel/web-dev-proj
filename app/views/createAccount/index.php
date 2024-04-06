<?php
include __DIR__ . '/../../public/components/general/topBar.php';
?>



<head>
    <title>Create Account</title>
    <link rel="stylesheet" type="text/css" href="/CSS Files/createAccount.css">
</head>

<body class="bodyCreateAccount">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Create Account</h1>

        <form action="/createAccount/createAccount" method="POST" class="border p-4 shadow rounded">
            <h3 class="h3-avatarSelection">Choose Your Avatar:</h3>
            <div class="avatar-selection text-center mb-4">

                <!-- Avatar 1 -->
                <label for="avatar1">
                    <input type="radio" name="avatar" id="avatar1" value="1" required hidden>
                    <img src="../images/account avatars/1.webp" alt="Avatar 1" class="avatar-img">
                </label>
                <!-- Avatar 2 -->
                <label for="avatar2">
                    <input type="radio" name="avatar" id="avatar2" value="2" required hidden>
                    <img src="../images/account avatars/2.webp" alt="Avatar 2" class="avatar-img">
                </label>
                <!-- Avatar 3 -->
                <label for="avatar3">
                    <input type="radio" name="avatar" id="avatar3" value="3" required hidden>
                    <img src="../images/account avatars/3.jpg" alt="Avatar 3" class="avatar-img">
                </label>
                <!-- Avatar 4 -->
                <label for="avatar4">
                    <input type="radio" name="avatar" id="avatar4" value="4" required hidden>
                    <img src="../images/account avatars/4.webp" alt="Avatar 4" class="avatar-img
                        ">
                </label>
                <!-- Avatar 5 -->
                <label for="avatar5">
                    <input type="radio" name="avatar" id="avatar5" value="5" required hidden>
                    <img src="../images/account avatars/5.jpg" alt="Avatar 5" class="avatar-img">
                </label>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm-password">Confirm Password:</label>
                <input type="password" class="form-control" id="confirm-password" name="confirmPassword" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Create Account</button>
        </form>
    </div>
</body>



<?php
include __DIR__ . '/../../public/components/general/footer.php';
?>