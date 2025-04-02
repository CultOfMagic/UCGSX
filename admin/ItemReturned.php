<?php
include '../config/db_connection.php';
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

// Fetch returned items data
$query = "SELECT r.return_id, r.borrow_id, r.return_date, i.item_name 
FROM return_requests r 
JOIN items i ON r.borrow_id = i.item_id;";
$result = $conn->query($query);

if (!$result) {
    die("Error executing query: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCGS Inventory | Dashboard</title>
    <link rel="stylesheet" href="../css/ItmReturned.css">
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

    <div class="main-content">
        <!-- Main content goes here -->
        <div class="table-container">
            <h2>Item Returned</h2>
            <div class="filter-container">
                <input type="text" id="search-input" placeholder="Search..." oninput="searchTable()">

                <label for="start-date">Date Range:</label>
                <input type="date" id="start-date" onchange="filterByDate()">

                <label for="end-date">To:</label>
                <input type="date" id="end-date" onchange="filterByDate()">
            </div>

            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Item Name</th>
                        <th>Return Date</th>
                        <th>Quantity</th>
                        <th>Condition</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Return Request Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="item-table-body">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['return_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['item_condition']); ?></td>
                            <td><?php echo htmlspecialchars($row['notes']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                            <td>
                                <button class="approve-btn">Approve</button>
                                <button class="reject-btn">Reject</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="pagination">
                <button onclick="prevPage()" id="prev-btn" style="font-family:'Akrobat', sans-serif;">Previous</button>
                <span id="page-number" style="font-family:'Akrobat', sans-serif;">Page 1</span>
                <button onclick="nextPage()" id="next-btn" style="font-family:'Akrobat', sans-serif;">Next</button>
            </div>
        </div>

        <!-- Rejection Modal -->
        <div id="rejectModal" class="modal">
            <div class="modal-content">
                <span class="close"></span>
                <h3>Reject Request</h3>
                <textarea id="rejectionReason" rows="4" placeholder="Enter reason..."></textarea>

                <!-- Error message display (add this) -->
                <p id="error-message" style="color: red; font-size: 14px; margin-top: 5px;"></p>

                <div class="modal-buttons">
                    <button id="confirmReject" class="confirm-btn">Confirm</button>
                    <button id="cancelReject" class="cancel-btn">Cancel</button>
                </div>
            </div>
        </div>

        <script src="../js/ItReturned.js"></script>
</body>

</html>