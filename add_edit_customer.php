<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// Database connection
$conn = new mysqli();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// table and action (add or edit)
$table = isset($_GET['table']) ? $conn->real_escape_string($_GET['table']) : '';
$action = isset($_GET['action']) ? $_GET['action'] : 'add';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// table is valid
$valid_tables = ['customer', 'returncustomer'];
if (!in_array($table, $valid_tables)) {
    die("Invalid table selected.");
}

// starts variables
$record = [];
if ($action === 'edit' && $id > 0) {
    
    $query = "SELECT * FROM $table WHERE id$table = $id";
    $result = $conn->query($query);
    if ($result && $result->num_rows === 1) {
        $record = $result->fetch_assoc();
    } else {
        die("Record not found.");
    }
}

// form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $columns = array_keys($_POST);
    $values = array_map(function ($value) use ($conn) {
        return "'" . $conn->real_escape_string($value) . "'";
    }, $_POST);

    if ($action === 'add') {
        // insert new record
        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
    } elseif ($action === 'edit') {
        // update existing record
        $updates = [];
        foreach ($_POST as $column => $value) {
            $updates[] = "$column = '" . $conn->real_escape_string($value) . "'";
        }
        $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE id$table = $id";
    }

    if ($conn->query($sql)) {
        header("Location: customer_dashboard.php?table=$table");
        exit();
    } else {
        echo "Error saving record: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($action); ?> Customer Record</title>
</head>
<body>
    <h1><?php echo ucfirst($action); ?> Record in <?php echo ucfirst($table); ?></h1>
    <form method="POST">
       
        <?php
        // get table structure
        $result = $conn->query("DESCRIBE $table");
        if (!$result) {
            die("Error fetching table structure: " . $conn->error);
        }
        while ($row = $result->fetch_assoc()) {
            $field = $row['Field'];
            $value = isset($record[$field]) ? htmlspecialchars($record[$field]) : '';
            $readonly = ($action === 'edit' && $field === "id$table") ? "readonly" : ""; // Make ID readonly for edit
            echo '<label for="' . $field . '">' . ucfirst($field) . ':</label>';
            echo '<input type="text" name="' . $field . '" id="' . $field . '" value="' . $value . '" ' . $readonly . ' required><br>';
        }
        ?>
        <button type="submit"><?php echo ucfirst($action); ?> Record</button>
    </form>
    <a href="customer_dashboard.php?table=<?php echo $table; ?>">Back to Dashboard</a>
</body>
</html>
<?php
$conn->close();
?>
