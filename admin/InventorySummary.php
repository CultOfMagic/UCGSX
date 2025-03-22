<?php 
include 'db_connection.php';
// this is comment

// Fetch inventory data
$query = "SELECT * FROM inventory"; // Replace 'inventory' with your actual table name
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCGS Inventory | Inventory Summary</title>
    <link rel="stylesheet" href="../css/Isummary.css">
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
    <span class="admin-text">Admin</span>
    <div class="user-dropdown" id="userDropdown">
        <a href="adminprofile.php"><img src="../assets/img/updateuser.png" alt="Profile Icon" class="dropdown-icon"> Profile</a>
        <a href="adminnotification.php"><img src="../assets/img/notificationbell.png" alt="Notification Icon" class="dropdown-icon"> Notification</a>
        <a href="#"><img src="../assets/img/logout.png" alt="Logout Icon" class="dropdown-icon"> Logout</a>
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
    <h2 style="font-family:'Akrobat', sans-serif;">Inventory Summary</h2>
    <input type="text" id="searchBar" placeholder="Search items...">
    <button onclick="searchTable()">Search</button>
    <button onclick="resetSearch()">Reset</button>

    <table class="inventory-table">
        <thead>
            <tr>
                <th>Item No</th>
                <th>Last Updated</th>
                <th>Model No</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Item Category</th>
                <th>Item Location</th>
                <th>Expiration</th>
                <th>Brand</th>
                <th>Supplier</th>
                <th>Price Per Item</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Status</th>
                <th>Reorder Point</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['item_no']; ?></td>
                        <td><?php echo $row['last_updated']; ?></td>
                        <td><?php echo $row['model_no']; ?></td>
                        <td><?php echo $row['item_name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['item_category']; ?></td>
                        <td><?php echo $row['item_location']; ?></td>
                        <td><?php echo $row['expiration']; ?></td>
                        <td><?php echo $row['brand']; ?></td>
                        <td><?php echo $row['supplier']; ?></td>
                        <td><?php echo $row['price_per_item']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['unit']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['reorder_point']; ?></td>
                        <td>
                            <button onclick="editItem(<?php echo $row['item_no']; ?>)">Edit</button>
                            <button onclick="deleteItem(<?php echo $row['item_no']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="16">No items found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="pagination">
        <button onclick="prevPage()" id="prev-btn" style="font-family:'Akrobat', sans-serif;">Previous</button>
        <span id="page-number" style="font-family:'Akrobat', sans-serif;">Page 1</span>
        <button onclick="nextPage()" id="next-btn" style="font-family:'Akrobat', sans-serif;">Next</button>
    </div>
</div>

<!-- For delete modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to delete this item?</p>
        <div class="modal-buttons">
            <button id="confirmDelete" class="delete-btn">Yes</button>
            <button id="cancelDelete" class="cancel-btn">Cancel</button>
        </div>
    </div>
</div>

<script src="../js/summary.js"></script>
</body>
</html>
