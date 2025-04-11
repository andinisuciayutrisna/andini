<?php
session_start();
require 'functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location:index.php');
    exit;
}

if (isset($_POST["login"])) {
    $username = strtolower(trim($_POST["username"]));
    $password = $_POST["password"];


    // Query untuk mencari username
    $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");

    // Cek apakah username ditemukan
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $row["password"])) {
            $_SESSION["login"] = true;
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            header("Location: index.php");
            exit;
        }
    }

    $error = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,
    initial-scale=1.0">
    <title>
        Login From in HTML and CSS | Codehal
    </title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

    <?php if (isset($error)) : ?>
        <p style="color: red; font-style: italic;">username / password salah</p>
    <?php endif; ?>

    <div class="wrapper">
        <form action="" method="POST">
            <h1>Login</h1>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="Password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <div class="remember-forgot">
                <label><input type="checkbox"> Remember me</label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" name="login" class="btn">Login</button>

            <div class="register-link">
                <p>Don't have an account? <a href="registrasi.php">Register</a></p>
            </div>
        </form>
    </div>
</body>

</html>