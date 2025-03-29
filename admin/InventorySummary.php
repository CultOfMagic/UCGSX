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
$stmt = $conn->prepare("SELECT username, email, role FROM users WHERE user_id = ?");
$stmt->bind_param("i", $currentAdminId);
$stmt->execute();
$result = $stmt->get_result();
$currentAdmin = $result->fetch_assoc();
$stmt->close();

// Pass the current admin details to the frontend
$accountName = $currentAdmin['username'] ?? 'User';
$accountEmail = $currentAdmin['email'] ?? '';
$accountRole = $currentAdmin['role'] ?? '';

// Pagination logic
$limit = 10; // Number of rows per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total number of rows
$totalQuery = "SELECT COUNT(*) AS total FROM items";
$totalResult = $conn->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch limited rows for the current page
$query = "SELECT * FROM items LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCGS Inventory | Inventory Summary</title>
    <link rel="stylesheet" href="../css/Isummary.css">
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
        <a href="../adminprofile.php"><img src="../assets/img/updateuser.png" alt="Profile Icon" class="dropdown-icon"> Profile</a>
        <a href="../adminnotification.php"><img src="../assets/img/notificationbell.png" alt="Notification Icon" class="dropdown-icon"> Notification</a>
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

<div class="main-content">
    <h2 style="font-family:'Akrobat', sans-serif;">Inventory Summary</h2>
    <input type="text" id="searchBar" placeholder="Search items...">
    <button onclick="searchTable()">Search</button>
    <button onclick="resetSearch()">Reset</button>

    <table class="inventory-table">
        <thead>
            <tr>
                <th>Item ID</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Location</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['item_id']; ?></td>
                        <td><?php echo $row['item_name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td><?php echo $row['location']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['unit']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No items found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" id="prev-btn" style="font-family:'Akrobat', sans-serif;">Previous</a>
        <?php endif; ?>
        <span id="page-number" style="font-family:'Akrobat', sans-serif;">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>" id="next-btn" style="font-family:'Akrobat', sans-serif;">Next</a>
        <?php endif; ?>
    </div>
</div>

<script src="../js/summary.js"></script>
</body>
</html>
