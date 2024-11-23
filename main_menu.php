<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Menu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <h2>Main Menu</h2>
        <ul>
            <li><a href="dashboard.php">Car Management</a></li>
			<li><a href="customer_dashboard.php">Customer Management</a></li>
            <li><a href="personnel_dashboard.php">Personnel Management</a></li>
        </ul>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
