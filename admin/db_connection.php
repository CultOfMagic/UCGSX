<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ucgs";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getLoggedInUser($conn) {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT user_id, username, role FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>
