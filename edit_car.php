<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'used_car_lot');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the car ID and category from the URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category = isset($_GET['category']) ? $_GET['category'] : 'suv';

// Fetch car details from the selected category table
$sql = "SELECT * FROM $category WHERE id=$id";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $car = $result->fetch_assoc();
} else {
    echo "Car not found!";
    exit();
}

// Check if form was submitted for updating the car
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $make = $conn->real_escape_string($_POST['make']);
    $model = $conn->real_escape_string($_POST['model']);
    $year = (int)$_POST['year'];
    $price = (float)$_POST['price'];
    $mileage = (int)$_POST['mileage'];
    $status = $conn->real_escape_string($_POST['status']);

    // Update car details in the database
    $update_sql = "UPDATE $category 
                   SET make='$make', model='$model', year=$year, price=$price, mileage=$mileage, status='$status' 
                   WHERE id=$id";

    if ($conn->query($update_sql)) {
        echo "Car updated successfully!";
        header("Location: dashboard.php?category=$category");
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
    <title>Edit Car</title>
</head>
<body>
    <h2>Edit Car: <?php echo ucfirst($category); ?></h2>
    <form action="edit_car.php?id=<?php echo $id; ?>&category=<?php echo $category; ?>" method="POST">
        <label for="make">Make:</label>
        <input type="text" name="make" id="make" value="<?php echo $car['make']; ?>" required><br>

        <label for="model">Model:</label>
        <input type="text" name="model" id="model" value="<?php echo $car['model']; ?>" required><br>

        <label for="year">Year:</label>
        <input type="number" name="year" id="year" value="<?php echo $car['year']; ?>" required><br>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" id="price" value="<?php echo $car['price']; ?>" required><br>

        <label for="mileage">Mileage:</label>
        <input type="number" name="mileage" id="mileage" value="<?php echo $car['mileage']; ?>" required><br>

        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="Available" <?php if ($car['status'] == 'Available') echo 'selected'; ?>>Available</option>
            <option value="Sold" <?php if ($car['status'] == 'Sold') echo 'selected'; ?>>Sold</option>
        </select><br><br>

        <input type="submit" value="Update Car">
    </form>
    <a href="dashboard.php?category=<?php echo $category; ?>">Back to Dashboard</a>
</body>
</html>
