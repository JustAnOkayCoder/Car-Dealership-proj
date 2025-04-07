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

// just loads a default table
$table = isset($_GET['table']) ? $conn->real_escape_string($_GET['table']) : 'manager';

// does the table exist
$valid_tables = ['manager', 'employee'];
if (!in_array($table, $valid_tables)) {
    die("Invalid table selected.");
}

// This is the search and filter items
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_field = isset($_GET['filter_field']) ? $conn->real_escape_string($_GET['filter_field']) : '';
$filter_value = isset($_GET['filter_value']) ? $conn->real_escape_string($_GET['filter_value']) : '';
$hire_date_filter = isset($_GET['hire_date_filter']) ? $_GET['hire_date_filter'] : '';
$hire_date = isset($_GET['hire_date']) ? $_GET['hire_date'] : '';

// this is the delete section
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $delete_query = "DELETE FROM $table WHERE id$table = $id";
    if ($conn->query($delete_query)) {
        echo "Record deleted successfully.";
        header("Location: personnel_dashboard.php?table=$table");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// the query for the search filters
$sql = "SELECT * FROM $table WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND (fname LIKE '%$search%' OR lname LIKE '%$search%' OR position LIKE '%$search%' OR hiredate LIKE '%$search%')";
}
if (!empty($filter_field) && !empty($filter_value)) {
    $sql .= " AND $filter_field = '$filter_value'";
}
if (!empty($hire_date_filter) && !empty($hire_date)) {
    if ($hire_date_filter == 'before') {
        $sql .= " AND hiredate < '$hire_date'";
    } elseif ($hire_date_filter == 'after') {
        $sql .= " AND hiredate > '$hire_date'";
    }
}

// does the query
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnel Management Dashboard</title>
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
    <h1>Personnel Management Dashboard</h1>

    <!-- logout buttons -->
    <div class="action-buttons">
        <a href="main_menu.php">Back to Main Menu</a> | 
        <a href="logout.php">Logout</a>
    </div>

    <!-- drop down for the tables -->
    <form method="GET" action="personnel_dashboard.php">
        <label for="table">Select Table:</label>
        <select name="table" id="table" onchange="this.form.submit()">
            <?php foreach ($valid_tables as $valid_table): ?>
                <option value="<?php echo $valid_table; ?>" <?php if ($table == $valid_table) echo 'selected'; ?>>
                    <?php echo ucfirst($valid_table); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- search and filter on the page -->
    <div class="search-filter">
        <form method="GET" action="personnel_dashboard.php">
            <input type="hidden" name="table" value="<?php echo $table; ?>">
            
            <label for="search">Search:</label>
            <input type="text" name="search" id="search" value="<?php echo $search; ?>">

            <label for="filter_field">Filter By:</label>
            <select name="filter_field" id="filter_field">
                <option value="">Select Field</option>
                <option value="fname" <?php if ($filter_field == 'fname') echo 'selected'; ?>>First Name</option>
                <option value="lname" <?php if ($filter_field == 'lname') echo 'selected'; ?>>Last Name</option>
                <option value="position" <?php if ($filter_field == 'position') echo 'selected'; ?>>Position</option>
            </select>

            <label for="filter_value">Value:</label>
            <input type="text" name="filter_value" id="filter_value" value="<?php echo $filter_value; ?>">

            <label for="hire_date_filter">Hire Date:</label>
            <select name="hire_date_filter" id="hire_date_filter">
                <option value="">Select</option>
                <option value="before" <?php if ($hire_date_filter == 'before') echo 'selected'; ?>>Before</option>
                <option value="after" <?php if ($hire_date_filter == 'after') echo 'selected'; ?>>After</option>
            </select>

            <label for="hire_date">Date:</label>
            <input type="date" name="hire_date" id="hire_date" value="<?php echo $hire_date; ?>">

            <button type="submit">Apply</button>
            <a href="personnel_dashboard.php?table=<?php echo $table; ?>">
                <button type="button">Reset</button>
            </a>
        </form>
    </div>

    <p><a href="add_personnel.php?table=<?php echo $table; ?>">Add New Record to <?php echo ucfirst($table); ?></a></p>

    <!-- shows the table -->
    <table>
        <tr>
            <?php
            
            if ($result->num_rows > 0) {
                $columns = array_keys($result->fetch_assoc());
                foreach ($columns as $column) {
                    echo "<th>" . ucfirst($column) . "</th>";
                }
                echo "<th>Actions</th>";
                $result->data_seek(0); 
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
                        <a href="edit_personnel.php?table=<?php echo $table; ?>&id=<?php echo $row['id' . $table]; ?>">Edit</a> |
                        <a href="personnel_dashboard.php?table=<?php echo $table; ?>&id=<?php echo $row['id' . $table]; ?>&action=delete" 
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
