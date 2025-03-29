<?php
include 'db_connection.php';
session_start();

// Verify admin session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: ../login/login.php");
    exit();
}

// Fetch currently logged-in admin details
$currentAdminId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $currentAdminId);
$stmt->execute();
$result = $stmt->get_result();
$currentAdmin = $result->fetch_assoc();
$stmt->close();

// Pass the current admin details to the frontend
$accountName = $currentAdmin['username'] ?? 'User';
$accountEmail = $currentAdmin['email'] ?? '';

$userId = $_SESSION['user_id'];
$query = "SELECT username, email FROM users WHERE user_id = ? AND role = 'Administrator'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($username, $email);
if ($stmt->fetch()) {
    $admin = ['username' => $username, 'email' => $email];
} else {
    header("Location: ../login/login.php");
    exit();
}
$stmt->close();

$loggedInUser = getLoggedInUser($conn);
if (!$loggedInUser || $loggedInUser['role'] !== 'Administrator') {
    header("Location: ../login/login.php");
    exit();
}

$accountName = $loggedInUser['username'];
$accountRole = $loggedInUser['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCGS Inventory | Profile</title>
    <link rel="stylesheet" href="../css/adminprof.css">
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
                <span class="admin-text"><?php echo htmlspecialchars($accountName); ?> (<?php echo htmlspecialchars($accountRole); ?>)</span>
                <div class="user-dropdown" id="userDropdown">
                    <a href="adminprofile.php"><img src="../assets/img/updateuser.png" alt="Profile Icon" class="dropdown-icon"> Profile</a>
                    <a href="adminnotification.php"><img src="../assets/img/notificationbell.png" alt="Notification Icon" class="dropdown-icon"> Notification</a>
                    <a href="../login/logout.php"><img src="../assets/img/logout.png" alt="Logout Icon" class="dropdown-icon"> Logout</a>
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
<h2 class="profile-title">Admin Profile</h2>
<form class="profile-form" method="POST" action="update_admin_profile.php">
    <div class="form-row">
        <div class="form-group">
            <label>Username / Account Name</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($accountName); ?>" required>
        </div>
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="text" name="email" value="<?php echo htmlspecialchars($accountEmail); ?>" required>
    </div>
    <div class="form-group">
        <h3>Change Password</h3>
        <label>New Password (leave blank to keep current)</label>
        <input type="password" name="password">
        
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password">
    </div>
    <button type="submit" class="btn save-btn">Save Changes</button>
</form>
<script src="../js/adminprof.js"></script>
</body>
</html>