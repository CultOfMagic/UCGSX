<?php
include 'db_connection.php';

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

    $query = "SELECT * FROM users WHERE username LIKE ? LIMIT ? OFFSET ?";
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



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCGS Inventory | Dashboard</title>
    <link rel="stylesheet" href="../css/UsersManagements.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
<header class="header">
        <div class="header-content">
            <div class="left-side">
                <img src="../assets/img/Logo.png" alt="Logo" class="logo">
                <span class="website-name">UCGS Inventory</span>
            </div>
            <div class="right-side">
            <div class="user">
    <img src="../assets/img/users.png" alt="User" class="icon" id="userIcon">
    <span class="admin-text">Admin</span>
    <div class="user-dropdown" id="userDropdown">
        <a href="adminprofile.php"><img src="../assets/img/updateuser.png" alt="Profile Icon" class="dropdown-icon"> Profile</a>
        <a href="adminnotification.php"><img src="../assets/img/notificationbell.png" alt="Notification Icon" class="dropdown-icon"> Notification</a>
        <a href="#"><img src="../assets/img/logout.png" alt="Logout Icon" class="dropdown-icon"> Logout</a>
    </div>
</div>
            </div>
        </div>
    </header>

    <aside class="sidebar">
        <ul>
            <li><a href="AdminDashboard.php"><img src="../assets/img/dashboards.png" alt="Dashboard Icon" class="sidebar-icon"> Dashboard</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-btn">
                    <img src="../assets/img/list-items.png" alt="Items Icon" class="sidebar-icon">
                    <span class="text">Items</span> 
                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="dropdown-content">
                    <li><a href="ItemRecords.php"> Item Records</a></li>
                    <li><a href="InventorySummary.php"> Inventory Summary</a></li>   
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-btn">
                    <img src="../assets/img/request-for-proposal.png" alt="Request Icon" class="sidebar-icon">
                    <span class="text">Request Record</span>
                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="dropdown-content">
                    <li><a href="ItemRequest.php"> Item Request by User</a></li>
                    <li><a href="ItemBorrowed.php"> Item Borrowed</a></li>
                    <li><a href="ItemReturned.php"> Item Returned</a></li>
                </ul>
            </li>
            <li><a href="Reports.php"><img src="../assets/img/reports.png" alt="Reports Icon" class="sidebar-icon"> Reports</a></li>
            <li><a href="UserManagement.php"><img src="../assets/img/user-management.png" alt="User Management Icon" class="sidebar-icon"> User Management</a></li>
        </ul>
    </aside>

<div class="main-content">
        <h2>User Management</h2>

        <div class="filter-container">
    <div class="filter-inputs">
        <input type="text" id="search-box" placeholder="Search...">
        <label for="start-date">Date Range:</label>
        <input type="date" id="start-date">
        <label for="end-date">To:</label>
        <input type="date" id="end-date">
    </div>
    <button id="create-account-btn">Create Account</button>
</div>


<table class="user-table">
    <thead>
        <tr>
            <th>Username / Account Name</th>
            <th>Email</th>
            <th>Password</th>
            <th>Role</th>
            <th>Date Creation</th>
            <th>Ministry</th>
        </tr>
    </thead>
    <tbody id="user-table-body">

    </tbody>
</table>

<div class="pagination">
    <button onclick="prevPage()" id="prev-btn" style="font-family:'Akrobat', sans-serif;">Previous</button>
    <span id="page-number" style="font-family:'Akrobat', sans-serif;">Page 1</span>
    <button onclick="nextPage()" id="next-btn" style="font-family:'Akrobat', sans-serif;">Next</button>
</div>

<div id="create-account-modal" class="modal">
    <div class="modal-content">
        <h2>Create Account</h2>
        <form id="account-form">
            <label for="username">Username / Account Name</label>
            <input type="text" id="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" required>

            <label for="ministry">Ministry</label>
            <select id="ministry">
                <option value="UCM">UCM</option>
                <option value="CWA">CWA</option>
                <option value="CHOIR">CHOIR</option>
                <option value="PWT">PWT</option>
                <option value="CYF">CYF</option>
            </select>

            <label for="role">Role</label>
            <select id="role">
                <option value="User">User</option>
                <option value="Administrator">Administrator</option>
            </select>
            <button type="submit">Submit</button>
            <button type="button" id="cancel-btn">Cancel</button>
        </form>
    </div>
</div>


    <script src="../js/UsersManagements.js"></script>
</body>
</html>
