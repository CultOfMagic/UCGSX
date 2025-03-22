<?php
session_start();

include 'db_connection.php';
// Initialize variables
$errors = [];
$formData = [
    'item-name' => '',
    'item-category' => '',
    'quantity' => '',
    'date-needed' => '',
    'purpose' => '',
    'notes' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token';
    }

    // Sanitize and validate inputs
    $formData = [
        'item-name' => filter_input(INPUT_POST, 'item-name', FILTER_SANITIZE_STRING),
        'item-category' => filter_input(INPUT_POST, 'item-category', FILTER_SANITIZE_STRING),
        'quantity' => filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT),
        'date-needed' => filter_input(INPUT_POST, 'date-needed', FILTER_SANITIZE_STRING),
        'purpose' => filter_input(INPUT_POST, 'purpose', FILTER_SANITIZE_STRING),
        'notes' => filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING)
    ];

    // Validation
    if (empty($formData['item-name'])) $errors[] = 'Item name is required';
    if (!in_array($formData['item-category'], ['Electronics', 'Stationery', 'Furniture'])) $errors[] = 'Invalid category';
    if (!$formData['quantity'] || $formData['quantity'] < 1) $errors[] = 'Invalid quantity';
    if (empty($formData['date-needed'])) $errors[] = 'Date needed is required';
    if (empty($formData['purpose'])) $errors[] = 'Purpose is required';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO requests (user_id, item_name, category, quantity, date_needed, purpose, notes) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $formData['item-name'],
                $formData['item-category'],
                $formData['quantity'],
                $formData['date-needed'],
                $formData['purpose'],
                $formData['notes']
            ]);
            
            $_SESSION['success'] = 'Request submitted successfully!';
            header('Location: '.$_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            $errors[] = 'Database error: '.$e->getMessage();
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
                    <span class="user-text"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="/userprofile.php"><img src="../assets/img/updateuser.png" alt="Profile" class="dropdown-icon"> Profile</a>
                        <a href="/logout.php"><img src="../assets/img/logout.png" alt="Logout" class="dropdown-icon"> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <aside class="sidebar">
        <ul>
            <li><a href="Userdashboard.php"><img src="../assets/img/dashboards.png" alt="Dashboard Icon" class="sidebar-icon"> Dashboard</a></li>
            <li><a href="ItemRecords.php"><img src="../assets/img/list-items.png" alt="Items Icon" class="sidebar-icon"> Item Records</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-btn">
                    <img src="../assets/img/request-for-proposal.png" alt="Request Icon" class="sidebar-icon">
                    <span class="text">Request Record</span>
                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="dropdown-content">
                    <li><a href="ItemRequests.php">New Item Request</a></li>
                    <li><a href="ItemBorrow.php">Borrow Item Request</a></li>
                    <li><a href="ItemReturned.php">Return Item Request</a></li>
                </ul>
            </li>
            <li><a href="UserTransaction.php"><img src="../assets/img/time-management.png" alt="Reports Icon" class="sidebar-icon">Transaction Records</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div id="new-request" class="tab-content active">
            <h1>New Item Request</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif (isset($_SESSION['success'])): ?>
                <div class="alert success">
                    <p><?= htmlspecialchars($_SESSION['success']) ?></p>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form id="requestForm" class="request-form" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="item-name">Item Name:</label>
                        <input type="text" id="item-name" name="item-name" required
                               value="<?= htmlspecialchars($formData['item-name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="item-category">Category:</label>
                        <select id="item-category" name="item-category" required>
                            <option value="">Select Category</option>
                            <?php foreach (['Electronics', 'Stationery', 'Furniture',  'Consumables',] as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" 
                                    <?= $formData['item-category'] === $cat ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" min="1" required
                               value="<?= htmlspecialchars($formData['quantity']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="date-needed">Date Needed:</label>
                        <input type="date" id="date-needed" name="date-needed" required
                               value="<?= htmlspecialchars($formData['date-needed']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="purpose">Purpose:</label>
                    <input id="purpose" name="purpose" rows="3" required><?= htmlspecialchars($formData['purpose']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Additional Notes:</label>
                    <input id="notes" name="notes" rows="2"><?= htmlspecialchars($formData['notes']) ?></textarea>
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