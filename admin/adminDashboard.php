<?php
session_start();
include 'db_connection.php';

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

try {
    // Fetch the total number of users
    $userCountQuery = "SELECT COUNT(*) FROM users";
    $userCountResult = $conn->query($userCountQuery);
    $userCount = $userCountResult->fetch_row()[0];
} catch (mysqli_sql_exception $e) {
    die("Error: Unable to fetch user count. Please ensure the 'users' table exists in the database.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCGS Inventory | Dashboard</title>
    <link rel="stylesheet" href="../css/admind.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <body>
    <h4 class="overview-title">OVERVIEW</h4>
    <div class="main-content">
        <div class="dashboard-overview">
        <div class="card gradient-yellow">
                <i class="fa-solid fa-user"></i>
                <h2>Users</h2>
                <p><?php echo htmlspecialchars($userCount); ?></p>
                <canvas id="chart1" class="chart-container"></canvas>
            </div>
            <div class="card gradient-orange">
                <i class="fa-solid fa-check-circle"></i>
                <h2>Approved Requests</h2>
                <p>300</p>
                <canvas id="chart2" class="chart-container"></canvas>
            </div>
            <div class="card gradient-green">
                <i class="fa-solid fa-clock"></i>
                <h2>Pending Requests</h2>
                <p>200</p>
                <canvas id="chart3" class="chart-container"></canvas>
            </div>
            <div class="card gradient-purple">
                <i class="fa-solid fa-list"></i>
                <h2>Total Items</h2>
                <p>500</p>
                <canvas id="chart4" class="chart-container"></canvas>
            </div>
        </div>
        <div class="main-content">
        <div class="table-container">
            <h2>Recent Items</h2>
            <table>
                <tr>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Model No.</th>
                    <th>Expiration</th>
                    <th>Brand</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <td>Printer Ink</td>
                    <td>Black Ink Cartridge</td>
                    <td>HP123</td>
                    <td>N/A</td>
                    <td>HP</td>
                    <td>20</td>
                    <td><button class="btn view" onclick="openModal('viewModal')">View</button></td>
                </tr>
            </table>
        </div>
        <div class="table-container">
            <h2>Pending Requests</h2>
            <table>
                <tr>
                    <th>Username</th>
                    <th>Requested Item Name</th>
                    <th>Item Category</th>
                    <th>Request Date</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <td>JohnDoe</td>
                    <td>Printer Ink</td>
                    <td>Office Supplies</td>
                    <td>2025-03-19</td>
                    <td>2</td>
                    <td>
                        <button class="btn approve" onclick="openModal('approveModal')">Approve</button>
                        <button class="btn reject" onclick="openModal('rejectModal')">Reject</button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('viewModal')">&times;</span>
            <h2>Item Details</h2>
            <p><strong>Item Name:</strong> <span id="modalItemName"></span></p>
            <p><strong>Description:</strong> <span id="modalDescription"></span></p>
            <p><strong>Model No.:</strong> <span id="modalModel"></span></p>
            <p><strong>Expiration:</strong> <span id="modalExpiration"></span></p>
            <p><strong>Brand:</strong> <span id="modalBrand"></span></p>
            <p><strong>Quantity:</strong> <span id="modalQuantity"></span></p>
        </div>
    </div>
    
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('approveModal')">&times;</span>
            <h2>Approve Request</h2>
            <p>Are you sure you want to approve this request?</p>
            <center><button class="btn approve">Confirm</button></center>
        </div>
    </div>
    
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('rejectModal')">&times;</span>
            <h2>Reject Request</h2>
            <p>Are you sure you want to reject this request?</p>
            <center><button class="btn reject">Confirm</button></center>
        </div>
    </div>

    <script src="../js/admind.js"></script>
</body>
</html>