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
$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ? AND role = 'User'");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$currentUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Define accountName for displaying the username
$accountName = $currentUser['username'] ?? '';

// If user details are not found, redirect to login
if (!$currentUser) {
    header("Location: ../login/login.php");
    exit();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize messages
$errorMessage = '';
$successMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Validate CSRF token
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
        $errorMessage = 'Invalid CSRF token.';
    } else {
        // Sanitize and validate input fields
        $itemName = htmlspecialchars(trim($_POST['item-name']));
        $itemCategory = htmlspecialchars(trim($_POST['item-category']));
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
        $itemUnit = htmlspecialchars(trim($_POST['item-unit']));
        $purpose = htmlspecialchars(trim($_POST['purpose']));
        $notes = isset($_POST['notes']) ? htmlspecialchars(trim($_POST['notes'])) : null;

        if (empty($itemName) || empty($itemCategory) || $quantity <= 0 || empty($itemUnit) || empty($purpose)) {
            $errorMessage = 'All fields are required. Please fill out the form completely.';
        } else {
            // Fetch item_id from the items table
            $itemQuery = "SELECT item_id FROM items WHERE item_name = ? AND item_category = ?";
            $itemStmt = $conn->prepare($itemQuery);

            if (!$itemStmt) {
                $errorMessage = 'Database error: Unable to prepare statement.';
            } else {
                $itemStmt->bind_param("ss", $itemName, $itemCategory);
                $itemStmt->execute();
                $itemResult = $itemStmt->get_result();
                $item = $itemResult->fetch_assoc();
                $itemStmt->close();

                if (!$item) {
                    $errorMessage = 'The specified item does not exist in the inventory.';
                } else {
                    $itemId = $item['item_id'];

                    // Insert the new item request with status "Pending"
                    $insertQuery = "INSERT INTO new_item_requests (user_id, item_id, item_name, item_category, quantity, purpose, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
                    $insertStmt = $conn->prepare($insertQuery);

                    if (!$insertStmt) {
                        $errorMessage = 'Database error: Unable to prepare statement.';
                    } else {
                        $insertStmt->bind_param("iississ", $currentUserId, $itemId, $itemName, $itemCategory, $quantity, $purpose, $notes);
                        $insertStmt->execute();

                        if ($insertStmt->affected_rows > 0) {
                            $successMessage = 'Your request has been submitted successfully and is pending admin approval.';
                        } else {
                            $errorMessage = 'Failed to submit your request. Please try again.';
                        }
                        $insertStmt->close();
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UCGS Inventory Management System - New Item Request">
    <title>UCGS Inventory | New Item Request</title>
    <link rel="stylesheet" href="../css/UserRequests.css">
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
                    <li><a href="UserItemReturned.php">Return Item Request</a></ul>
            </li>
            <li><a href="UserTransaction.php"><img src="../assets/img/time-management.png" alt="Reports Icon" class="sidebar-icon">Transaction Records</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div id="new-request" class="tab-content active">
            <h1>New Item Request</h1>
            
            <?php if (!empty($errorMessage)): ?>
                <div class="alert error">
                    <p><?php echo htmlspecialchars($errorMessage); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($successMessage)): ?>
                <div class="alert success">
                    <p><?php echo htmlspecialchars($successMessage); ?></p>
                </div>
            <?php endif; ?>

            <form id="requestForm" class="request-form" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="item-name">Item Name:</label>
                        <input type="text" id="item-name" name="item-name" required>
                    </div>
                    <div class="form-group">
                        <label for="item-category">Category:</label>
                        <select id="item-category" name="item-category" required>
                            <option value="">Select Category</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Stationery">Stationery</option>
                            <option value="Furniture">Furniture</option>
                            <option value="Consumables">Consumables</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="item-unit">Item Unit:</label>
                        <select id="item-unit" name="item-unit" required>
                            <option value="">Select Unit</option>
                            <option value="Piece">Pcs</option>
                            <option value="Box">Bx</option>
                            <option value="Pair">Pr</option>
                            <option value="Bundle">Bdl</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="purpose">Purpose:</label>
                    <textarea id="purpose" name="purpose" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Additional Notes:</label>
                    <textarea id="notes" name="notes" rows="2"></textarea>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="submit-btn">Submit Request</button>
                    <button type="reset" class="reset-btn">Clear Form</button>
                </div>
            </form>
        </div>
    </main>

    <script src="../js/usereqs.js"></script>
</body>
</html>