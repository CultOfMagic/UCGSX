<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "ucgs"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";

// No need to close the connection right here if you're still using it later in the script
?>
