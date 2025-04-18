<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// Database connection
$conn= new mysqli();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the car ID and category 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category = isset($_GET['category']) ? $_GET['category'] : 'suv';

// Delete the car from the selected table
$sql = "DELETE FROM $category WHERE id=$id";

if ($conn->query($sql)) {
    echo "Car deleted successfully!";
    header("Location: dashboard.php?category=$category");
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
