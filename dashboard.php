<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'dbname');
if ($conn->connect_error) {
    die("Connection failed" .$conn->connect_error);
}

//Since we have so many catagories i figure the best way to do it is this
$category = isset($_GET['category']) ? $_GET['category'] : 'suv';//we can change this to whatever later

//this should allow us to see whatever we choose
$sql ="SELECT * FROM $category";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
            }

            table, th, td {
                border: 1px solid black;
            }

            th, td {
                padding: 15px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }
        </style>
    </head>
    <body>
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <h2>Inventory</h2>
        <p> Here you will be able to view, add, and update your business.</p>
        <!-- This is our inventory stuff  -->

        <form method="GET" action="dashboard.php">
            <label for="category">Select Category:</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="suv" <?php if ($category == 'suv') echo 'selected'; ?>>SUV</option>
                <!-- We gotta add the rest -->

        </form>

        <!-- Add Car Button -->
    <p><a href="add_car.php?category=<?php echo $category; ?>">Add New Car to <?php echo ucfirst($category); ?></a></p>

<!-- Display Car Inventory -->
<table>
    <tr>
        <th>Make</th>
        <th>Model</th>
        <th>Year</th>
        <th>Price</th>
        <th>Mileage</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['make'] . "</td>
                    <td>" . $row['model'] . "</td>
                    <td>" . $row['year'] . "</td>
                    <td>$" . number_format($row['price'], 2) . "</td>
                    <td>" . number_format($row['mileage']) . " miles</td>
                    <td>" . $row['status'] . "</td>
                    <td>
                        <a href='edit_car.php?id=" . $row['id'] . "&category=$category'>Edit</a> |
                        <a href='delete_car.php?id=" . $row['id'] . "&category=$category' onclick='return confirm(\"Are you sure?\");'>Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No cars found in $category category.</td></tr>";
    }
    ?>
</table>
    </body>
</html>

<?php
$conn->close();
?>