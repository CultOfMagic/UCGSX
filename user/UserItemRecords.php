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
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user;
}

$currentUser = getCurrentUser($conn);
$accountName = htmlspecialchars($currentUser['username'] ?? 'User');
$accountEmail = htmlspecialchars($currentUser['email'] ?? '');

// Pagination logic
$itemsPerPage = 10; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure the page is at least 1

// Count total items
$totalItemsQuery = "SELECT COUNT(*) as total FROM items WHERE deleted_at IS NULL";
$totalItemsResult = $conn->query($totalItemsQuery);
if (!$totalItemsResult) {
    die("Database error: " . $conn->error);
}
$totalItemsRow = $totalItemsResult->fetch_assoc();
$totalItems = $totalItemsRow['total'] ?? 0;

// Calculate total pages
$totalPages = ceil($totalItems / $itemsPerPage);

// Fetch items for the current page
$offset = ($page - 1) * $itemsPerPage;
$query = "SELECT * FROM items WHERE deleted_at IS NULL LIMIT ?, ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param("ii", $offset, $itemsPerPage);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Database error: " . $stmt->error);
}
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = array_map('htmlspecialchars', $row);
}
$stmt->close();

// Pass items data to JavaScript
echo "<script>const itemsData = " . json_encode($items) . ";</script>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCGS Inventory | Item Records</title>
    <link rel="stylesheet" href="../css/records.css">
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
    <h2>Item Records</h2>
    <!-- Search and Filter Form -->
<div class="search-form">
    <input type="text" id="search-input" placeholder="Search...">
    <p style = "font-family: 'Akrobat', sans-serif;">Date Range:</p>
    <input type="date" id="start-date">
    <p style = "font-family: 'Akrobat', sans-serif;">To</p>
    <input type="date" id="end-date">
    <button class="reset-btn" onclick="resetSearch()">Reset</button>
</div>


    <!-- Item Records Table -->
    <form id="item-form">
        <table class="item-table">
            <thead>
                <tr>
                    <th>Item No</th>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Model No</th>
                    <th>Item Category</th>
                    <th>Item Location</th>
                </tr>
            </thead>
            <tbody id="item-table-body">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['item_no']) ?></td>
                            <td><?= htmlspecialchars($row['item_name']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                            <td><?= htmlspecialchars($row['unit']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['last_updated']) ?></td>
                            <td><?= htmlspecialchars($row['model_no']) ?></td>
                            <td><?= htmlspecialchars($row['item_category']) ?></td>
                            <td><?= htmlspecialchars($row['item_location']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
    <div class="pagination">
    <button onclick="prevPage()" id="prev-btn" style = "font-family:'Akrobat', sans-serif;">Previous</button>
    <span id="page-number" style = "font-family:'Akrobat', sans-serif;">Page 1</span>
    <button onclick="nextPage()" id="next-btn" style = "font-family:'Akrobat', sans-serif;">Next</button>
</div>
</div>

    <script src="../js/userecords.js"></script>
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