<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location index.html");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'dbname');
if ($conn->connect_error) {
    die("connection failed to db". $conn->connect_error);
}

$category = isset($_GET['category']) ? $_GET['category'] : suv;


//this is just a check to see if we submitted it its nothing important testing only
// we need to add to this 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $make = $conn->real_escape_string($_POST['make']);
    $model = $conn->real_escape_string($_POST['model']);
    $year = (int)$_POST['year'];
    $price = (float)$_POST['price'];
    $mileage = (int)$_POST['mileage'];
    $status = $conn->real_escape_string($_POST['status']);


$sql = "INSERT INTO $category (make, model, year, price, mileage, status) 
            VALUES ('$make', '$model', $year, $price, $mileage, '$status')";

    if ($conn->query($sql)) {
        echo "Car added successfully to $category category!";
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
        <title>Add New Car</title>
    </head>
    <body>
        <h2>Add a New Car to <?php echo ucfirst($category); ?></h2>
        <form action="add_car.php?category=<?php echo $category; ?>"method="POST">
            <label for="make">Make</label>
            <input type="text" name="make" id="make" required><br>

            <label for="model">Model</label>
            <input type="text" name="model" id="model" required><br>   
            
            <label for="year">Year</label>
            <input type="number" name="year" id="year" required><br>

            <label for="price">Price</label>
            <input type="number" step="0.01" name="price" id="price" required><br>

            <label for="mileage">Mileage</label>
            <input type="number" name="mileage" id="mileage" required><br>

            <label for="status">Status</label>
            <select name="status" id="status">
                <option value ="Available">Available</option>
                <option value ="Sold">Sold</option>
            </select><br><br>


            <input type="submit" value="Add Car">
</form>
<a href="dashboard.php?category=<?php echo $category; ?>">Back to Dashboard</a>
    </body>
</html>