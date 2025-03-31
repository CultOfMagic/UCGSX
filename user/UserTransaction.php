<?php
include 'db_connection.php';
session_start();

// Verify User session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../login/login.php");
    exit();
}

function getCurrentUser($conn) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
        header("Location: ../login/login.php");
        exit();
    }

    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, email, user_id FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        // Handle case where user is not found in the database
        header("Location: ../login/login.php");
        exit();
    }

    return $user;
}

$currentUser = getCurrentUser($conn);
$accountName = htmlspecialchars($currentUser['username'] ?? 'User');
$currentUserId = $currentUser['user_id']; // Ensure this is properly set

// Check if the 'requests' table exists
$tableCheckQuery = "SHOW TABLES LIKE 'requests'";
$tableCheckResult = $conn->query($tableCheckQuery);
if ($tableCheckResult->num_rows === 0) {
    die("Error: The 'requests' table does not exist in the database.");
}

// Fetch requests for the logged-in user
$requestsQuery = "
    SELECT 
        borrow_requests.user_id, 
        borrow_requests.item_id, 
        borrow_requests.quantity, 
        borrow_requests.date_needed, 
        borrow_requests.return_date, 
        borrow_requests.purpose, 
        borrow_requests.notes, 
        borrow_requests.status, 
        items.item_name 
    FROM borrow_requests 
    JOIN items ON borrow_requests.item_id = items.item_id 
    WHERE borrow_requests.user_id = ?
";
$requestsStmt = $conn->prepare($requestsQuery);
$requestsStmt->bind_param("i", $currentUserId); // Use the correct user ID
$requestsStmt->execute();
$requestsResult = $requestsStmt->get_result();
$requestsStmt->close();

// Fetch new item requests for the logged-in user
$newRequestsQuery = "
    SELECT 
        new_item_requests.quantity, 
        new_item_requests.request_date AS date_requested, 
        new_item_requests.status, 
        new_item_requests.item_name 
    FROM new_item_requests 
    WHERE new_item_requests.user_id = ?
";
$newRequestsStmt = $conn->prepare($newRequestsQuery);
$newRequestsStmt->bind_param("i", $currentUserId);
$newRequestsStmt->execute();
$newRequestsResult = $newRequestsStmt->get_result();
$newRequestsStmt->close();

// Correct the SQL query to reference valid columns
$sql = "
    SELECT 
        borrow_requests.id AS borrow_id, -- Adjusted column name to 'id'
        borrow_requests.item_name,
        borrow_requests.quantity AS borrow_quantity,
        borrow_requests.status AS borrow_status,
        return_requests.id AS return_id,
        return_requests.return_date,
        return_requests.condition AS return_condition
    FROM 
        borrow_requests
    LEFT JOIN 
        return_requests ON borrow_requests.id = return_requests.borrow_request_id -- Adjusted join condition
    WHERE 
        borrow_requests.user_id = ?
";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Fetch returned item requests for the logged-in user
$returnedRequestsQuery = "
    SELECT 
        return_requests.quantity, 
        return_requests.date_returned, 
        return_requests.status, 
        items.item_name 
    FROM return_requests 
    JOIN borrow_requests ON return_requests.borrow_id = borrow_requests.id 
    JOIN items ON borrow_requests.item_id = items.item_id 
    WHERE borrow_requests.user_id = ?
";
$returnedRequestsStmt = $conn->prepare($returnedRequestsQuery);
$returnedRequestsStmt->bind_param("i", $currentUserId);
$returnedRequestsStmt->execute();
$returnedRequestsResult = $returnedRequestsStmt->get_result();
$returnedRequestsStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"="width=device-width, initial-scale=1.0">
    <meta name="description" content="UCGS Inventory Management System - User Transactions">
    <title>UCGS Inventory | User Transactions</title>
    <link rel="stylesheet" href="../css/UserTransact.css">
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
    <h1>User Requests</h1>
    <table class="transaction-table">
        <thead>
            <tr>
                <th>Request Type</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Date</th>
                <th>Additional Info</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($request = $requestsResult->fetch_assoc()): ?>
                <tr>
                    <td>Borrow</td>
                    <td><?php echo htmlspecialchars($request['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($request['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($request['date_needed']); ?></td>
                    <td>Return Date: <?php echo htmlspecialchars($request['return_date']); ?><br>Purpose: <?php echo htmlspecialchars($request['purpose']); ?><br>Notes: <?php echo htmlspecialchars($request['notes']); ?></td>
                    <td><?php echo htmlspecialchars($request['status']); ?></td>
                </tr>
            <?php endwhile; ?>

            <?php while ($newRequest = $newRequestsResult->fetch_assoc()): ?>
                <tr>
                    <td>New Item</td>
                    <td><?php echo htmlspecialchars($newRequest['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($newRequest['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($newRequest['date_requested']); ?></td>
                    <td>--</td>
                    <td><?php echo htmlspecialchars($newRequest['status']); ?></td>
                </tr>
            <?php endwhile; ?>

            <?php while ($returnedRequest = $returnedRequestsResult->fetch_assoc()): ?>
                <tr>
                    <td>Returned</td>
                    <td><?php echo htmlspecialchars($returnedRequest['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($returnedRequest['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($returnedRequest['date_returned']); ?></td>
                    <td>--</td>
                    <td><?php echo htmlspecialchars($returnedRequest['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<script src="../js/UserTransactions.js"></script>
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