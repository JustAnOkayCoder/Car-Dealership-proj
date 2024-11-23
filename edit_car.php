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

//  table and id
$table = isset($_GET['table']) ? $conn->real_escape_string($_GET['table']) : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// the table exists
$valid_tables = ['suv', 'truck', 'van', 'sedans', 'hatch', 'electric', 'coupe', 'crossover', 'convertable'];
if (!in_array($table, $valid_tables) || $id <= 0) {
    die("Invalid table or ID.");
}

// get the existing record
$sql = "SELECT * FROM $table WHERE id$table = $id";
$result = $conn->query($sql);
if ($result->num_rows != 1) {
    die("Record not found.");
}
$row = $result->fetch_assoc();

// doesa the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $make = $conn->real_escape_string($_POST['make']);
    $model = $conn->real_escape_string($_POST['model']);
    $year = (int)$_POST['year'];
    $drivetrain = $conn->real_escape_string($_POST['drivetrain']);
    $transmission = $conn->real_escape_string($_POST['transmission']);
    $mileage = (int)$_POST['mileage'];
    $engine = $conn->real_escape_string($_POST['engine']);
    $condition = $conn->real_escape_string($_POST['condition']);
    $color = $conn->real_escape_string($_POST['color']);
    $vin = $conn->real_escape_string($_POST['vin']);

    // just updates the query
    $update_query = "UPDATE $table SET 
                        make='$make', 
                        model='$model', 
                        year=$year, 
                        drivetrain='$drivetrain', 
                        transmission='$transmission', 
                        mileage=$mileage, 
                        engine='$engine', 
                        `condition`='$condition', 
                        color='$color', 
                        vin='$vin' 
                    WHERE id$table = $id";

    if ($conn->query($update_query)) {
        header("Location: dashboard.php?table=$table");
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
    <title>Edit Car</title>
</head>
<body>
    <h1>Edit Car</h1>
    <form method="POST">
        <label for="make">Make:</label>
        <input type="text" name="make" id="make" value="<?php echo htmlspecialchars($row['make']); ?>" required><br>

        <label for="model">Model:</label>
        <input type="text" name="model" id="model" value="<?php echo htmlspecialchars($row['model']); ?>" required><br>

        <label for="year">Year:</label>
        <input type="number" name="year" id="year" value="<?php echo htmlspecialchars($row['year']); ?>" required><br>

        <label for="drivetrain">Drivetrain:</label>
        <input type="text" name="drivetrain" id="drivetrain" value="<?php echo htmlspecialchars($row['drivetrain']); ?>" required><br>

        <label for="transmission">Transmission:</label>
        <input type="text" name="transmission" id="transmission" value="<?php echo htmlspecialchars($row['transmission']); ?>" required><br>

        <label for="mileage">Mileage:</label>
        <input type="number" name="mileage" id="mileage" value="<?php echo htmlspecialchars($row['mileage']); ?>" required><br>

        <label for="engine">Engine:</label>
        <input type="text" name="engine" id="engine" value="<?php echo htmlspecialchars($row['engine']); ?>" required><br>

        <label for="condition">Condition:</label>
        <input type="text" name="condition" id="condition" value="<?php echo htmlspecialchars($row['condition']); ?>" required><br>

        <label for="color">Color:</label>
        <input type="text" name="color" id="color" value="<?php echo htmlspecialchars($row['color']); ?>" required><br>

        <label for="vin">VIN:</label>
        <input type="text" name="vin" id="vin" value="<?php echo htmlspecialchars($row['vin']); ?>" required><br>

        <button type="submit">Save Changes</button>
    </form>
    <a href="dashboard.php?table=<?php echo htmlspecialchars($table); ?>">Back to Dashboard</a>
</body>
</html>
