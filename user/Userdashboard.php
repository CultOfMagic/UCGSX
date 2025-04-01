<?php
// Use a secure session configuration
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
session_regenerate_id(true);

// Redirect unauthorized access
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../login/login.php");
    exit();
}

include 'db_connection.php';

// Verify database connection
if (!$conn || $conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    header("Location: ../error.php");
    exit();
}

// Sanitize and validate user ID
$userId = filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT);

// Fetch user data securely
function getCurrentUser($conn) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
        header("Location: ../login/login.php");
        exit();
    }

    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, email, role, ministry, status FROM users WHERE user_id = ?");
    if (!$stmt) {
        error_log("Database query error: " . $conn->error);
        header("Location: ../error.php");
        exit();
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user || $user['status'] !== 'Active') {
        session_destroy();
        header("Location: ../login/login.php?error=account_inactive");
        exit();
    }

    return $user;
}

$currentUser = getCurrentUser($conn);
$accountName = htmlspecialchars($currentUser['username']);
$email = htmlspecialchars($currentUser['email']);
$role = htmlspecialchars($currentUser['role']);
$ministry = htmlspecialchars($currentUser['ministry']);
$status = htmlspecialchars($currentUser['status']);

// Updated functions using mysqli consistently
function getPendingRequestsCount($mysqli, $userId) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM user_requests WHERE user_id = ? AND status = 'Pending'");
    if (!$stmt) {
        error_log("Query preparation failed: " . $mysqli->error);
        return 0;
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getBorrowedItemsCount($mysqli, $userId) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM borrowed_items WHERE user_id = ? AND status = 'Borrowed'");
    if (!$stmt) {
        error_log("Query preparation failed: " . $mysqli->error);
        return 0;
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getRecentTransactionsCount($mysqli, $userId) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = ?");
    if (!$stmt) {
        error_log("Query preparation failed: " . $mysqli->error);
        return 0;
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

// Add a function to get total items count
function getTotalItemsCount($mysqli) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM items");
    if (!$stmt) {
        error_log("Query preparation failed: " . $mysqli->error);
        return 0;
    }
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

// Add a function to fetch user-specific notifications
function getUserNotifications($mysqli, $userId) {
    $stmt = $mysqli->prepare("SELECT notification_id, message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    if (!$stmt) {
        error_log("Query preparation failed: " . $mysqli->error);
        return [];
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    $stmt->close();
    return $notifications;
}

// Fetch additional data for the dashboard
$totalItems = getTotalItemsCount($conn);
$notifications = getUserNotifications($conn, $userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - UCGS Inventory</title>
    <link rel="stylesheet" href="../css/userdb.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                    <span class="user-text"><?php echo htmlspecialchars($accountName); ?></span> <!-- Display logged-in user's username -->
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
    <div class="main-content">
        <!-- Dashboard Overview -->
        <div class="dashboard-overview">
            <div class="stats-card">
                <h3>Pending Requests</h3>
                <p><?= htmlspecialchars(getPendingRequestsCount($conn, $userId)) ?></p>
            </div>
            <div class="stats-card">
                <h3>Borrowed Items</h3>
                <p><?= htmlspecialchars(getBorrowedItemsCount($conn, $userId)) ?></p>
            </div>
            <div class="stats-card">
                <h3>Recent Transactions</h3>
                <p><?= htmlspecialchars(getRecentTransactionsCount($conn, $userId)) ?></p>
            </div>
            <div class="stats-card">
                <h3>Total Items</h3>
                <p><?= htmlspecialchars($totalItems) ?></p>
            </div>
        </div>

        <!-- Notifications Section -->
        <div class="notifications-section">
            <h2>Recent Notifications</h2>
            <ul class="notifications-list">
                <?php foreach ($notifications as $notification): ?>
                    <li>
                        <p><?= htmlspecialchars($notification['message']) ?></p>
                        <small><?= htmlspecialchars($notification['created_at']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Tables Section -->
        <div class="tables-section">
            <h2>Pending Requests</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Item Name</th>
                        <th>Request Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT request_id, item_name, request_date, status FROM user_requests WHERE user_id = ? AND status = 'Pending'");
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['request_id']) . "</td>
                                <td>" . htmlspecialchars($row['item_name']) . "</td>
                                <td>" . htmlspecialchars($row['request_date']) . "</td>
                                <td>" . htmlspecialchars($row['status']) . "</td>
                              </tr>";
                    }
                    $stmt->close();
                    ?>
                </tbody>
            </table>

            <h2>Borrowed Items</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT item_id, item_name, borrow_date, actual_return_date FROM borrowed_items WHERE user_id = ? AND status = 'Borrowed'");
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['item_id']) . "</td>
                                <td>" . htmlspecialchars($row['item_name']) . "</td>
                                <td>" . htmlspecialchars($row['borrow_date']) . "</td>
                                <td>" . htmlspecialchars($row['return_date']) . "</td>
                              </tr>";
                    }
                    $stmt->close();
                    ?>
                </tbody>
            </table>

            <h2>Recent Transactions</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Details</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT transaction_id, details, created_at FROM transactions WHERE user_id = ?");
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['transaction_id']) . "</td>
                                <td>" . htmlspecialchars($row['details']) . "</td>
                                <td>" . htmlspecialchars($row['created_at']) . "</td>
                              </tr>";
                    }
                    $stmt->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../js/usersd.js"></script>
    <script>
        // Sidebar dropdown functionality
        document.querySelectorAll('.dropdown-btn').forEach(button => {
            button.addEventListener('click', () => {
                const dropdownContent = button.nextElementSibling;
                dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
            });
        });

        // Profile dropdown functionality
        const userIcon = document.getElementById('userIcon');
        const userDropdown = document.getElementById('userDropdown');
        userIcon.addEventListener('click', () => {
            userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
        });

        // Close dropdown if clicked outside
        document.addEventListener('click', (event) => {
            if (!userIcon.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.style.display = 'none';
            }
        });
    </script>
</body>
</html>

<?php
// Close database connection (moved to the end of the script)
$conn->close();
?>