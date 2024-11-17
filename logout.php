<?php
session_start();
session_destroy(); 
header("Location: index.html"); // Takes us to the main login page
exit();
?>
