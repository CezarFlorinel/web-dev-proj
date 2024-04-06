<?php
include __DIR__ . '/../../public/components/general/topBar.php';
?>


<head>
    <link rel="stylesheet" type="text/css" href="/CSS Files/profile.css">
</head>

<body>
    <div class="container profile">
        <h1 class="profile-header">Profile</h1>
        <h2 class="profile-subheader">Current Avatar Icon:</h2>
        <div class="profile-picture text-center">
            <?php if (isset ($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <img class="AccountIcon" src="<?php echo $_SESSION['avatar_path']; ?>" alt="User Avatar">
            <?php else: ?>
                <img class="AccountIcon" src="/images/elements/profile-user.png" alt="Logo">
            <?php endif; ?>
        </div>

        <form action="/profile/modifyAccount" method="POST" class="border p-4 shadow rounded">
            <h2 class="profile-subheader">Choose New Icon:</h2>
            <div class="avatar-selection">
                <!-- Avatar 1 -->
                <label for="avatar1">
                    <input type="radio" name="avatar" id="avatar1" value="1">
                    <img src="../images/account avatars/1.webp" alt="Avatar 1" class="avatar-img">
                </label>
                <!-- Avatar 2 -->
                <label for="avatar2">
                    <input type="radio" name="avatar" id="avatar2" value="2">
                    <img src="../images/account avatars/2.webp" alt="Avatar 2" class="avatar-img">
                </label>
                <!-- Avatar 3 -->
                <label for="avatar3">
                    <input type="radio" name="avatar" id="avatar3" value="3">
                    <img src="../images/account avatars/3.jpg" alt="Avatar 3" class="avatar-img">
                </label>
                <!-- Avatar 4 -->
                <label for="avatar4">
                    <input type="radio" name="avatar" id="avatar4" value="4">
                    <img src="../images/account avatars/4.webp" alt="Avatar 4" class="avatar-img
                        ">
                </label>
                <!-- Avatar 5 -->
                <label for="avatar5">
                    <input type="radio" name="avatar" id="avatar5" value="5">
                    <img src="../images/account avatars/5.jpg" alt="Avatar 5" class="avatar-img">
                </label>
            </div>

            <div class="profile-info">

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username"
                        value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                        value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required class="form-control">
                </div>

                <h2 class="profile-subheader2">Change your password:</h2>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="password2">Enter New Password:</label>
                    <input type="password" id="password2" name="password2" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password3">Re-enter Password:</label>
                    <input type="password" id="password3" name="password3" class="form-control">
                </div>
                <input type="submit" value="Update Profile">

            </div>
        </form>

        <form action="/profile/logout" method="POST">
            <button type="submit" class="btn btn-danger btn-animate">LOG OUT</button>
        </form>

        <p class="text-paragraph">In order to make any changes to your account, the password field is mandatory to be
            completed! </p>
    </div>

</body>




<?php
include __DIR__ . '/../../public/components/general/footer.php';
?>