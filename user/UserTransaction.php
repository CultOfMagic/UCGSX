<?php
include 'db_connection.php';
session_start();

// Verify User session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../login/login.php");
    exit();
}

// Fetch logged-in user details
$currentUserId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$currentUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Define accountName for displaying the username
$accountName = $currentUser['username'] ?? 'User';

// Fetch transactions for the logged-in user
$transactionsQuery = "SELECT action, details, created_at, status FROM transactions WHERE user_id = ? ORDER BY created_at DESC";
$transactionsStmt = $conn->prepare($transactionsQuery);
$transactionsStmt->bind_param("i", $currentUserId);
$transactionsStmt->execute();
$transactionsResult = $transactionsStmt->get_result();
$transactionsStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UCGS Inventory Management System - User Transactions">
    <title>UCGS Inventory | User Transactions</title>
    <link rel="stylesheet" href="../css/UserTransactions.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>
<header class="header">
    <div class="header-content">
        <div class="left-side">
            <img src="../assets/img/Logo.png" alt="UCGS Inventory Logo" class="logo">
            <span class="website-name">UCGS Inventory</span>
        </div>
        <div class="right-side">
            <div class="user">
                <img src="../assets/img/users.png" alt="User profile" class="icon" id="userIcon">
                <span class="user-text"><?php echo htmlspecialchars($accountName); ?></span>
                <div class="user-dropdown" id="userDropdown">
                    <a href="userprofile.php"><img src="../assets/img/updateuser.png" alt="Profile" class="dropdown-icon"> Profile</a>
                    <a href="usernotification.php"><img src="../assets/img/notificationbell.png" alt="Notification Icon" class="dropdown-icon"> Notification</a>
                    <a href="../login/logout.php"><img src="../assets/img/logout.png" alt="Logout" class="dropdown-icon"> Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<aside class="sidebar">
    <ul>
        <li><a href="Userdashboard.php"><img src="../assets/img/dashboards.png" alt="Dashboard Icon" class="sidebar-icon"> Dashboard</a></li>
        <li><a href="UserItemRecords.php"><img src="../assets/img/list-items.png" alt="Items Icon" class="sidebar-icon"> Item Records</a></li>
        <li class="dropdown">
            <a href="#" class="dropdown-btn">
                <img src="../assets/img/request-for-proposal.png" alt="Request Icon" class="sidebar-icon">
                <span class="text">Request Record</span>
                <i class="fa-solid fa-chevron-down arrow-icon"></i>
            </a>
            <ul class="dropdown-content">
                <li><a href="UserItemRequests.php">New Item Request</a></li>
                <li><a href="UserItemBorrow.php">Borrow Item Request</a></li>
                <li><a href="UserItemReturned.php">Return Item Request</a></li>
            </ul>
        </li>
        <li><a href="UserTransaction.php"><img src="../assets/img/time-management.png" alt="Reports Icon" class="sidebar-icon">Transaction Records</a></li>
    </ul>
</aside>

<main class="main-content">
    <h1>User Transactions</h1>
    <table class="transaction-table">
        <thead>
            <tr>
                <th>Action</th>
                <th>Details</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($transaction = $transactionsResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaction['action']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['details']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<script src="../js/UserTransactions.js"></script>
</body>
</html>