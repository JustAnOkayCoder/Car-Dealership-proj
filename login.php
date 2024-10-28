<?php

session_start();

$conn = new mysqli('localhost', 'root', '', 'dbname');

if ($conn->connect_error) {
    die("Connection Failed" . $conn->connect_error);
}

//see if the form got submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $check_user_query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($check_user_query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
        }
        else{
            echo "Invalid password";
        }
    }
    else{
        echo "User not found";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
    </head>
    <body>
        <h2>Login to your account</h2>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>

            <input type="submit" value="Login">
</form>
    <a href="create_user.php">Create an account</a>
    </body>
</html>