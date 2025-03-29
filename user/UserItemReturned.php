<?php
session_start();
require_once 'db_connection.php'; // Include database connection

function getCurrentUser($db) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login/login.php");
        exit();
    }

    $userId = $_SESSION['user_id'];
    $stmt = $db->prepare("SELECT name FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return $user ?: null; // Ensure null is returned if no user is found
}

$currentUser = getCurrentUser($db);
if (!$currentUser) {
    header("Location: ../login/login.php");
    exit();
}

$accountName = htmlspecialchars($currentUser['name'] ?? 'User');
$user_id = $_SESSION['user_id']; // Initialize $user_id from session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
    $item_category = filter_input(INPUT_POST, 'item_category', FILTER_SANITIZE_STRING);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $return_date = filter_input(INPUT_POST, 'return_date', FILTER_SANITIZE_STRING);
    $condition = filter_input(INPUT_POST, 'condition', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);

    if ($item_id && $item_category && $quantity && $return_date && $condition) {
        try {
            // Prepare SQL query to insert return request
            $stmt = $db->prepare("INSERT INTO item_returns (user_id, item_id, category, quantity, return_date, item_condition, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");

            if ($stmt->execute([$user_id, $item_id, $item_category, $quantity, $return_date, $condition, $notes])) {
                $success_message = "Return request submitted successfully.";
            } else {
                $error_message = "Failed to submit return request. Please try again.";
            }
        } catch (PDOException $e) {
            $error_message = "An error occurred: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UCGS Inventory Management System - Return Item Request">
    <title>UCGS Inventory | Return Item Request</title>
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
                <span class="website-name">UCGS Inventory | Return Item Request</span>
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
            <h1>Return Item Request</h1>
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php elseif (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form id="returnForm" class="request-form" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="item-id">Select Item:</label>
                        <select id="item-id" name="item_id" required>
                            <option value="" disabled selected>Select an Item</option>
                            <!-- Items will be dynamically populated based on category -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="item-category">Item Category:</label>
                        <select id="item-category" name="item_category" required>
                            <option value="" disabled selected>Select a Category</option>
                            <option value="electronics">Electronics</option>
                            <option value="furniture">Furniture</option>
                            <option value="stationery">Stationery</option>
                            <option value="consumables">Consumables</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="quantity">Quantity to Return:</label>
                        <input type="number" id="quantity" name="quantity" 
                               min="1" required placeholder="Enter quantity">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="return-date">Return Date:</label>
                        <input type="date" id="return-date" name="return_date" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="condition">Condition of Item:</label>
                    <textarea id="condition" name="condition" rows="3" required placeholder="Describe the condition of the item"></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Additional Notes:</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Enter any additional notes (optional)"></textarea>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="submit-btn">Submit Return</button>
                    <button type="reset" class="reset-btn">Clear Form</button>
                </div>
            </form>
        </div>
    </main>

    <script src="../js/usereqs.js"></script>
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
