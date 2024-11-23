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

// Default table (e.g., 'customer') if none selected
$table = isset($_GET['table']) ? $conn->real_escape_string($_GET['table']) : 'customer';

// Ensure table is valid
$valid_tables = ['customer', 'returncustomer'];
if (!in_array($table, $valid_tables)) {
    die("Invalid table selected.");
}

// Search and filter inputs
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_field = isset($_GET['filter_field']) ? $conn->real_escape_string($_GET['filter_field']) : '';
$filter_value = isset($_GET['filter_value']) ? $conn->real_escape_string($_GET['filter_value']) : '';

// Handle delete operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $delete_query = "DELETE FROM $table WHERE id$table = $id";
    if ($conn->query($delete_query)) {
        echo "Record deleted successfully.";
        header("Location: customer_dashboard.php?table=$table");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Build SQL query with search and filter
$sql = "SELECT * FROM $table WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND (fname LIKE '%$search%' OR lname LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}
if (!empty($filter_field) && !empty($filter_value)) {
    $sql .= " AND $filter_field = '$filter_value'";
}

// Execute query
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management Dashboard</title>
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
    <h1>Customer Management Dashboard</h1>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="main_menu.php">Back to Main Menu</a> | 
        <a href="logout.php">Logout</a>
    </div>

    <!-- Dropdown to Select Table -->
    <form method="GET" action="customer_dashboard.php">
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
        <form method="GET" action="customer_dashboard.php">
            <input type="hidden" name="table" value="<?php echo $table; ?>">
            <label for="search">Search:</label>
            <input type="text" name="search" id="search" value="<?php echo $search; ?>">

            <label for="filter_field">Filter By:</label>
            <select name="filter_field" id="filter_field">
                <option value="">Select Field</option>
                <option value="fname" <?php if ($filter_field == 'fname') echo 'selected'; ?>>First Name</option>
                <option value="lname" <?php if ($filter_field == 'lname') echo 'selected'; ?>>Last Name</option>
                <option value="email" <?php if ($filter_field == 'email') echo 'selected'; ?>>Email</option>
                <option value="phone" <?php if ($filter_field == 'phone') echo 'selected'; ?>>Phone</option>
            </select>

            <label for="filter_value">Value:</label>
            <input type="text" name="filter_value" id="filter_value" value="<?php echo $filter_value; ?>">

            <button type="submit">Apply</button>
			<a href="customer_dashboard.php?table=<?php echo $table; ?>">
            <button type="button">Reset</button>
        </a>

        </form>
    </div>

    <p><a href="add_customer.php?table=<?php echo $table; ?>">Add New Record to <?php echo ucfirst($table); ?></a></p>

    <!-- Display Table Data -->
    <table>
        <tr>
            <?php
            // Dynamically generate table headers
            if ($result->num_rows > 0) {
                $columns = array_keys($result->fetch_assoc());
                foreach ($columns as $column) {
                    echo "<th>" . ucfirst($column) . "</th>";
                }
                echo "<th>Actions</th>";
                $result->data_seek(0); // Reset pointer for data fetch
            }
            ?>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <?php foreach ($row as $key => $value): ?>
                        <td><?php echo htmlspecialchars($value); ?></td>
                    <?php endforeach; ?>
                    <td>
                        <a href="edit_customer.php?table=<?php echo $table; ?>&id=<?php echo $row['id' . $table]; ?>">Edit</a> |
                        <a href="customer_dashboard.php?table=<?php echo $table; ?>&id=<?php echo $row['id' . $table]; ?>&action=delete" 
                           onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="100%">No data found in <?php echo ucfirst($table); ?> table.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
