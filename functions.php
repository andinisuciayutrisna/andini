<?php
require 'database.php';

function registrasi($data)
{
    global $conn;

    // Get data from the form
    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $password2 = mysqli_real_escape_string($conn, $data["password2"]);
    $email = filter_var($data["email"], FILTER_SANITIZE_EMAIL); //tambahan

    // Check if username already exists
    $result = mysqli_query($conn, "SELECT username FROM user WHERE username = '$username'");
    if (mysqli_fetch_assoc($result)) {
        echo "<script>
            alert('Username sudah terdaftar!');
        </script>";
        return false;
    }

    // Check if password and confirmation match
    if ($password != $password2) {
        echo "<script>
            alert('Konfirmasi password tidak sesuai!');
        </script>";
        return false;
    }

    // Hash the password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    mysqli_query($conn, "INSERT INTO user (username, password, email) VALUES('$username', '$password', '$email')");

    // Check if the query was successful
    return mysqli_affected_rows($conn);
}
