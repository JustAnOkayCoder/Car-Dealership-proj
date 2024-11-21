<?php
session_start();

// Database connection
$conn = new mysqli('73.214.12.104', 'billroot', 'mysql', 'dealership');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Query to check if the user exists
    $check_user_query = "SELECT * FROM login WHERE username='$username'";
    $result = $conn->query($check_user_query);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username']; // Store username in session
            header("Location: main_menu.php"); // Redirect to main menu
            exit();
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        $error_message = "User not found. Please check your username.";
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Login to Your Account</h2>
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>

            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="create_user.php">Sign up here</a></p>
    </div>
</body>
</html>
