<?php
session_start();
include 'db_connection.php';

// Check user authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/signin.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - UCGS Inventory</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Dashboard Overview -->
        <div class="dashboard-overview">
            <div class="stats-card">
                <h3>Pending Requests</h3>
                <p><?= getPendingRequestsCount($pdo, $user_id) ?></p>
            </div>
            <div class="stats-card">
                <h3>Borrowed Items</h3>
                <p><?= getBorrowedItemsCount($pdo, $user_id) ?></p>
            </div>
            <div class="stats-card">
                <h3>Recent Transactions</h3>
                <p><?= getRecentTransactionsCount($pdo, $user_id) ?></p>
            </div>
        </div>

        <!-- Dynamic Content Section -->
        <div id="content-container">
            <!-- Default Content -->
            <div class="content-section active" id="dashboard">
                <h2>Welcome, <?= htmlspecialchars($user['first_name']) ?></h2>
                <!-- Add dashboard widgets here -->
            </div>

            <!-- Request Forms -->
            <div class="content-section" id="new-request">
                <?php include 'forms/new_request.php'; ?>
            </div>

            <div class="content-section" id="borrowed-items">
                <?php include 'tables/borrowed_items.php'; ?>
            </div>

            <div class="content-section" id="transaction-history">
                <?php include 'tables/transactions.php'; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="../js/dashboard.js"></script>
</body>
</html>