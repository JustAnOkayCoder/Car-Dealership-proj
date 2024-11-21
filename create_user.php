<?php

$conn= new mysqli('73.214.12.104', 'billroot', 'mysql', 'dealership');

if ($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

//does our form work
if($_SERVER['REQUEST_METHOD'] =='POST'){
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    //see if the user name already exist
    $check_user_query ="SELECT * FROM login WHERE username='$username'";
    $result = $conn->query($check_user_query);

    if ($result->num_rows > 0){
        echo "This Username already exist";
    }
    else{
        //make a new user
        $insert_query = "INSERT INTO login (username, password) VALUES ('$username', '$password')";

        if ($conn->query($insert_query)) {
            echo "User created successfully";
            header("Location: index.html");
        }
            else{
                echo "Error: " . $conn->error;
            }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create User</title>
    </head>
    <body>
        <h2>Create a New Account</h2>
        <form action="create_user.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>

            <input type="submit" value="Create Account">
</form>   
    <a href="index.html">Back to Login</a>
    </body>
</html>