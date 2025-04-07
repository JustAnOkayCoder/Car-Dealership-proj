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

// Default table (e.g., 'suv') if none selected
$table = isset($_GET['table']) ? $conn->real_escape_string($_GET['table']) : 'suv';

// Ensure table is valid
$valid_tables = ['suv', 'truck', 'van', 'sedans', 'hatch', 'electric', 'coupe', 'crossover', 'convertable'];
if (!in_array($table, $valid_tables)) {
    die("Invalid table selected.");
}

// Search and filter inputs
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_field = isset($_GET['filter_field']) ? $conn->real_escape_string($_GET['filter_field']) : '';
$filter_value = isset($_GET['filter_value']) ? $conn->real_escape_string($_GET['filter_value']) : '';
$mileage_filter = isset($_GET['mileage_filter']) ? $_GET['mileage_filter'] : '';
$mileage_amount = isset($_GET['mileage_amount']) ? (int)$_GET['mileage_amount'] : 0;
$color_filter = isset($_GET['color_filter']) ? $conn->real_escape_string($_GET['color_filter']) : '';

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

// Build SQL query with search, mileage, and color filters
$sql = "SELECT * FROM $table WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND (make LIKE '%$search%' OR model LIKE '%$search%' OR year LIKE '%$search%' OR vin LIKE '%$search%')";
}
if (!empty($filter_field) && !empty($filter_value)) {
    $sql .= " AND $filter_field = '$filter_value'";
}
if (!empty($mileage_filter) && $mileage_amount > 0) {
    if ($mileage_filter == 'over') {
        $sql .= " AND mileage > $mileage_amount";
    } elseif ($mileage_filter == 'under') {
        $sql .= " AND mileage < $mileage_amount";
    }
}
if (!empty($color_filter)) {
    $sql .= " AND color = '$color_filter'";
}

// Execute query
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
        .action-buttons, .search-filter {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Car Inventory Dashboard</h1>

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

    <!-- Search and Filter Form -->
    <div class="search-filter">
        <form method="GET" action="dashboard.php">
            <input type="hidden" name="table" value="<?php echo $table; ?>">
            <label for="search">Search:</label>
            <input type="text" name="search" id="search" value="<?php echo $search; ?>">

            <label for="filter_field">Filter By:</label>
            <select name="filter_field" id="filter_field">
                <option value="">Select Field</option>
                <option value="make" <?php if ($filter_field == 'make') echo 'selected'; ?>>Make</option>
                <option value="model" <?php if ($filter_field == 'model') echo 'selected'; ?>>Model</option>
                <option value="year" <?php if ($filter_field == 'year') echo 'selected'; ?>>Year</option>
                <option value="drivetrain" <?php if ($filter_field == 'drivetrain') echo 'selected'; ?>>Drivetrain</option>
            </select>

            <label for="filter_value">Value:</label>
            <input type="text" name="filter_value" id="filter_value" value="<?php echo $filter_value; ?>">

            <label for="mileage_filter">Mileage:</label>
            <select name="mileage_filter" id="mileage_filter">
                <option value="">Select</option>
                <option value="over" <?php if ($mileage_filter == 'over') echo 'selected'; ?>>Over</option>
                <option value="under" <?php if ($mileage_filter == 'under') echo 'selected'; ?>>Under</option>
            </select>

            <label for="mileage_amount">Amount:</label>
            <input type="number" name="mileage_amount" id="mileage_amount" value="<?php echo $mileage_amount; ?>">

            <label for="color_filter">Color:</label>
            <input type="text" name="color_filter" id="color_filter" value="<?php echo $color_filter; ?>">

            <button type="submit">Apply</button>
			<a href="dashboard.php?table=<?php echo $table; ?>">
            <button type="button">Reset</button>
        </a>

        </form>
    </div>

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
