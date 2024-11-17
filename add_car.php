<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// Database connection
$conn = new mysqli("73.214.12.104", "billroot", "mysql", "dealership");

if ($conn->connect_error) {
    die("Connection failed to DB: " . $conn->connect_error);
}

// Validate and sanitize the selected category
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

// List of valid tables
$valid_tables = ['suv', 'truck', 'van', 'sedans', 'hatch', 'electric', 'coupe', 'crossover', 'convertable'];

// Check if the selected table is valid
if (!in_array($category, $valid_tables)) {
    die("Invalid table selected.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form inputs
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

    // SQL query to insert the new car
    $sql = "INSERT INTO $category (make, model, year, drivetrain, transmission, mileage, engine, `condition`, color, vin) 
            VALUES ('$make', '$model', $year, '$drivetrain', '$transmission', $mileage, '$engine', '$condition', '$color', '$vin')";

    if ($conn->query($sql)) {
        echo "Car added successfully to $category category!";
        header("Location: dashboard.php?table=$category");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Car</title>
</head>
<body>
    <h2>Add a New Car to <?php echo ucfirst($category); ?></h2>
    <form action="add_car.php?category=<?php echo $category; ?>" method="POST">
        <label for="make">Make:</label>
        <input type="text" name="make" id="make" required><br>

        <label for="model">Model:</label>
        <input type="text" name="model" id="model" required><br>   

        <label for="year">Year:</label>
        <input type="number" name="year" id="year" required><br>

        <label for="drivetrain">Drivetrain:</label>
        <input type="text" name="drivetrain" id="drivetrain" required><br>

        <label for="transmission">Transmission:</label>
        <input type="text" name="transmission" id="transmission" required><br>

        <label for="mileage">Mileage:</label>
        <input type="number" name="mileage" id="mileage" required><br>

        <label for="engine">Engine:</label>
        <input type="text" name="engine" id="engine" required><br>

        <label for="condition">Condition:</label>
        <input type="text" name="condition" id="condition" required><br>

        <label for="color">Color:</label>
        <input type="text" name="color" id="color" required><br>

        <label for="vin">VIN:</label>
        <input type="text" name="vin" id="vin" required><br><br>

        <input type="submit" value="Add Car">
    </form>
    <a href="dashboard.php?table=<?php echo $category; ?>">Back to Dashboard</a>
</body>
</html>
