<?php
session_start();
include 'db_connection.php';

// Check user authentication

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCGS Inventory | Browse Items</title>
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
                    <span class="user-text"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="/profile.php"><img src="/assets/img/updateuser.png" alt="Profile" class="dropdown-icon"> Profile</a>
                        <a href="/logout.php"><img src="/assets/img/logout.png" alt="Logout" class="dropdown-icon"> Logout</a>
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

    <div class="main-content">
        <h2>Available Items</h2>
        <div class="search-form">
            <input type="text" id="search-input" placeholder="Search items...">
            <select id="category-filter">
                <option value="">All Categories</option>
                <option value="Electronics">Electronics</option>
                <option value="Appliances">Appliances</option>
                <!-- Add more categories as needed -->
            </select>
            <button class="search-btn" onclick="searchItems()">Search</button>
            <button class="reset-btn" onclick="resetSearch()">Reset</button>
        </div>

        <table class="item-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Available Quantity</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="item-table-body">
                <?php
                try {
                    $stmt = $pdo->prepare("
                        SELECT item_id, item_name, description, quantity, category, location 
                        FROM items 
                        WHERE quantity > 0 
                        ORDER BY item_name
                    ");
                    $stmt->execute();
                    
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                            <td>{$row['item_name']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['quantity']}</td>
                            <td>{$row['category']}</td>
                            <td>{$row['location']}</td>
                            <td>
                                <button class='request-btn' 
                                        onclick='showRequestForm({$row['item_id']})'>
                                    Request Item
                                </button>
                            </td>
                        </tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='6'>Error loading items</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="pagination">
            <button onclick="prevPage()" id="prev-btn">Previous</button>
            <span id="page-number">Page 1</span>
            <button onclick="nextPage()" id="next-btn">Next</button>
        </div>
    </div>

    <!-- Request Item Modal -->
    <div id="requestModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Request Item</h3>
            <form id="requestForm">
                <input type="hidden" id="requestItemId">
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" min="1" required>
                </div>
                <div class="form-group">
                    <label for="needDate">Date Needed:</label>
                    <input type="date" id="needDate" required>
                </div>
                <div class="form-group">
                    <label for="purpose">Purpose:</label>
                    <textarea id="purpose" rows="3" required></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="submit-btn">Submit Request</button>
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/user_records.js"></script>
</body>
</html>