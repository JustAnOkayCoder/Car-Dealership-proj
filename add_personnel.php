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

// Validate table
$table = isset($_GET['table']) ? $conn->real_escape_string($_GET['table']) : '';
$valid_tables = ['customer', 'returncustomer', 'manager', 'employee'];
if (!in_array($table, $valid_tables)) {
    die("Invalid table selected.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $columns = array_keys($_POST);
    $values = array_map(function ($value) use ($conn) {
        return "'" . $conn->real_escape_string($value) . "'";
    }, $_POST);

    $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
    if ($conn->query($sql)) {
        header("Location: personnel_dashboard.php?table=$table");
        exit();
    } else {
        echo "Error adding record: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Record</title>
</head>
<body>
    <h1>Add New Record to <?php echo ucfirst($table); ?></h1>
    <form method="POST">
        <!-- Dynamically Generate Input Fields -->
        <?php
        // Dynamically generate input fields based on table structure
        $result = $conn->query("DESCRIBE $table");
        if (!$result) {
            die("Error fetching table structure: " . $conn->error);
        }
        while ($row = $result->fetch_assoc()) {
            echo '<label for="' . $row['Field'] . '">' . ucfirst($row['Field']) . ':</label>';
            echo '<input type="text" name="' . $row['Field'] . '" id="' . $row['Field'] . '" required><br>';
        }
        ?>
        <button type="submit">Add Record</button>
    </form>
    <a href="personnel_dashboard.php?table=<?php echo $table; ?>">Back to Dashboard</a>
</body>
</html>
<?php
$conn->close();
?>
