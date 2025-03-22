<?php
include 'db_connection.php'; // Ensure the correct path to your db_connection.php

// Create User (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? ''; // IMPORTANT: Hash passwords before storing
    $role = $_POST['role'] ?? '';
    $ministry = $_POST['ministry'] ?? '';
    $created_at = date('Y-m-d H:i:s');

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $query = "INSERT INTO users (username, email, password, role, ministry, created_at) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $username, $email, $hashedPassword, $role, $ministry, $created_at);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User created successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to create user"]);
    }
    exit();
}

// Fetch Users (GET request)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'read') {
    $search = $_GET['search'] ?? '';
    $page = $_GET['page'] ?? 1;
    $itemsPerPage = 10;
    $offset = ($page - 1) * $itemsPerPage;

    $query = "SELECT * FROM users WHERE deleted_at IS NULL AND username LIKE ? LIMIT ? OFFSET ?";
    $searchParam = "%" . $search . "%";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $searchParam, $itemsPerPage, $offset);

    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
    exit();
}

// Update User (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $user_id = $_POST['user_id'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? '';
    $ministry = $_POST['ministry'] ?? '';

    $query = "UPDATE users SET username = ?, email = ?, role = ?, ministry = ?, updated_at = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $username, $email, $role, $ministry, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update user"]);
    }
    exit();
}

// Soft Delete User (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $user_id = $_POST['user_id'] ?? '';

    $query = "UPDATE users SET deleted_at = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete user"]);
    }
    exit();
}

// Close database connection
$conn->close();
?>