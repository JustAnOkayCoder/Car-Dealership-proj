<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// Database connection
$conn = new mysqli("73.214.12.104", "billroot", "mysql", "dealership");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate table and ID
$table = isset($_GET['table']) ? $conn->real_escape_string($_GET['table']) : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$valid_tables = ['customer', 'returncustomer', 'manager', 'employee'];
if (!in_array($table, $valid_tables) || $id <= 0) {
    die("Invalid table or ID.");
}

// Fetch record
$sql = "SELECT * FROM $table WHERE id$table = $id";
$result = $conn->query($sql);
if ($result->num_rows != 1) {
    die("Record not found.");
}
$row = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_parts = [];
    foreach ($_POST as $column => $value) {
        $update_parts[] = "$column='" . $conn->real_escape_string($value) . "'";
    }

    $update_query = "UPDATE $table SET " . implode(', ', $update_parts) . " WHERE id$table = $id";
    if ($conn->query($update_query)) {
        header("Location: personnel_dashboard.php?table=$table");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record</title>
</head>
<body>
    <h1>Edit Record in <?php echo ucfirst($table); ?></h1>
    <form method="POST">
        <!-- Dynamically Generate Input Fields -->
        <?php foreach ($row as $column => $value): ?>
            <label for="<?php echo $column; ?>"><?php echo ucfirst($column); ?>:</label>
            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" value="<?php echo htmlspecialchars($value); ?>" required><br>
        <?php endforeach; ?>
        <button type="submit">Save Changes</button>
    </form>
    <a href="personnel_dashboard.php?table=<?php echo $table; ?>">Back to Dashboard</a>
</body>
</html>
