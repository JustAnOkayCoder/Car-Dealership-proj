<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// Database connection
$conn = new mysqli('73.214.12.104', 'billroot', 'mysql', 'dealership');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Default table (e.g., 'suv') if none selected
$table = isset($_GET['table']) ? $conn->real_escape_string($_GET['table']) : 'suv';

// Ensure table is valid
$valid_tables = ['suv', 'truck', 'van', 'sedans', 'hatch', 'electric', 'coupe', 'crossover', 'convertable'];
if (!in_array($table, $valid_tables)) {
    die("Invalid table selected.");
}

// Handle delete operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $delete_query = "DELETE FROM $table WHERE id$table = $id";
    if ($conn->query($delete_query)) {
        echo "Record deleted successfully.";
        header("Location: dashboard.php?table=$table");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Fetch data from the selected table
$sql = "SELECT * FROM $table";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Inventory Dashboard</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-buttons {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <h2>Car Inventory Dashboard</h2>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="main_menu.php">Back to Main Menu</a> |
        <a href="logout.php">Logout</a>
    </div>

    <!-- Dropdown to Select Table -->
    <form method="GET" action="dashboard.php">
        <label for="table">Select Table:</label>
        <select name="table" id="table" onchange="this.form.submit()">
            <?php foreach ($valid_tables as $valid_table): ?>
                <option value="<?php echo $valid_table; ?>" <?php if ($table == $valid_table) echo 'selected'; ?>>
                    <?php echo ucfirst($valid_table); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <p><a href="add_car.php?category=<?php echo $table; ?>">Add New Car to <?php echo ucfirst($table); ?></a></p>

    <!-- Display Table Data -->
    <table>
        <tr>
            <th>ID</th>
            <th>Make</th>
            <th>Model</th>
            <th>Year</th>
            <th>Drivetrain</th>
            <th>Transmission</th>
            <th>Mileage</th>
            <th>Engine</th>
            <th>Condition</th>
            <th>Color</th>
            <th>VIN</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id' . $table]; ?></td>
                    <td><?php echo $row['make']; ?></td>
                    <td><?php echo $row['model']; ?></td>
                    <td><?php echo $row['year']; ?></td>
                    <td><?php echo $row['drivetrain']; ?></td>
                    <td><?php echo $row['transmission']; ?></td>
                    <td><?php echo $row['mileage']; ?></td>
                    <td><?php echo $row['engine']; ?></td>
                    <td><?php echo $row['condition']; ?></td>
                    <td><?php echo $row['color']; ?></td>
                    <td><?php echo $row['vin']; ?></td>
                    <td>
                        <a href="edit_car.php?table=<?php echo $table; ?>&id=<?php echo $row['id' . $table]; ?>">Edit</a> |
                        <a href="dashboard.php?table=<?php echo $table; ?>&id=<?php echo $row['id' . $table]; ?>&action=delete" 
                           onclick="return confirm('Are you sure you want to delete this vehicle?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="12">No data found in <?php echo ucfirst($table); ?> table.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
