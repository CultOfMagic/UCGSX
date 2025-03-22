<?php 
include 'db_connection.php';

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
    <h2>Item Records</h2>
    <!-- Search and Filter Form -->
<div class="search-form">
    <input type="text" id="search-input" placeholder="Search...">
    <p style = "font-family: 'Akrobat', sans-serif;">Date Range:</p>
    <input type="date" id="start-date">
    <p style = "font-family: 'Akrobat', sans-serif;">To</p>
    <input type="date" id="end-date">
    <button class="search-btn" onclick="searchTable()">Search</button>
    <button class="reset-btn" onclick="resetSearch()">Reset</button>
</div>


    <!-- Item Records Table -->
    <form id="item-form">
        <table class="item-table">
            <thead>
                <tr>
                    <th>Select</th>
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="item-table-body">
                <tr>
                    <td><input type="checkbox" class="select-item"></td>
                    <td>001</td>
                    <td>Sample Item 1</td>
                    <td>Example description</td>
                    <td>10</td>
                    <td>pcs</td>
                    <td>Available</td>
                    <td>2025-03-18</td>
                    <td>Model X</td>
                    <td>Electronics</td>
                    <td>Warehouse A</td>
                    <td>
                        <button type="button" class="update-btn" onclick="updateRow(this)">Update</button>
                        <button type="button" class="delete-btn" onclick="openDeleteModal(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="select-item"></td>
                    <td>002</td>
                    <td>Sample Item 2</td>
                    <td>Another example</td>
                    <td>5</td>
                    <td>kg</td>
                    <td>Out of Stock</td>
                    <td>2025-02-15</td>
                    <td>Model Y</td>
                    <td>Appliances</td>
                    <td>Warehouse B</td>
                    <td>
                        <button type="button" class="update-btn" onclick="updateRow(this)">Update</button>
                        <button type="button" class="delete-btn" onclick="openDeleteModal(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="select-item"></td>
                    <td>003</td>
                    <td>Sample Item 2</td>
                    <td>Another example</td>
                    <td>5</td>
                    <td>kg</td>
                    <td>Out of Stock</td>
                    <td>2025-02-15</td>
                    <td>Model Y</td>
                    <td>Appliances</td>
                    <td>Warehouse B</td>
                    <td>
                        <button type="button" class="update-btn" onclick="updateRow(this)">Update</button>
                        <button type="button" class="delete-btn" onclick="openDeleteModal(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="select-item"></td>
                    <td>004</td>
                    <td>Sample Item 2</td>
                    <td>Another example</td>
                    <td>5</td>
                    <td>kg</td>
                    <td>Out of Stock</td>
                    <td>2025-02-15</td>
                    <td>Model Y</td>
                    <td>Appliances</td>
                    <td>Warehouse B</td>
                    <td>
                        <button type="button" class="update-btn" onclick="updateRow(this)">Update</button>
                        <button type="button" class="delete-btn" onclick="openDeleteModal(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="select-item"></td>
                    <td>005</td>
                    <td>Sample Item 2</td>
                    <td>Another example</td>
                    <td>5</td>
                    <td>kg</td>
                    <td>Out of Stock</td>
                    <td>2025-02-15</td>
                    <td>Model Y</td>
                    <td>Appliances</td>
                    <td>Warehouse B</td>
                    <td>
                        <button type="button" class="update-btn" onclick="updateRow(this)">Update</button>
                        <button type="button" class="delete-btn" onclick="openDeleteModal(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="select-item"></td>
                    <td>006</td>
                    <td>Sample Item 2</td>
                    <td>Another example</td>
                    <td>5</td>
                    <td>kg</td>
                    <td>Out of Stock</td>
                    <td>2025-02-15</td>
                    <td>Model Y</td>
                    <td>Appliances</td>
                    <td>Warehouse B</td>
                    <td>
                        <button type="button" class="update-btn" onclick="updateRow(this)">Update</button>
                        <button type="button" class="delete-btn" onclick="openDeleteModal(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="select-item"></td>
                    <td>007</td>
                    <td>Sample Item 2</td>
                    <td>Another example</td>
                    <td>5</td>
                    <td>kg</td>
                    <td>Out of Stock</td>
                    <td>2025-02-15</td>
                    <td>Model Y</td>
                    <td>Appliances</td>
                    <td>Warehouse B</td>
                    <td>
                        <button type="button" class="update-btn" onclick="updateRow(this)">Update</button>
                        <button type="button" class="delete-btn" onclick="openDeleteModal(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="select-item"></td>
                    <td>008</td>
                    <td>Sample Item 2</td>
                    <td>Another example</td>
                    <td>5</td>
                    <td>kg</td>
                    <td>Out of Stock</td>
                    <td>2025-02-15</td>
                    <td>Model Y</td>
                    <td>Appliances</td>
                    <td>Warehouse B</td>
                    <td>
                        <button type="button" class="update-btn" onclick="updateRow(this)">Update</button>
                        <button type="button" class="delete-btn" onclick="openDeleteModal(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="select-item"></td>
                    <td>009</td>
                    <td>Sample Item 2</td>
                    <td>Another example</td>
                    <td>5</td>
                    <td>kg</td>
                    <td>Out of Stock</td>
                    <td>2025-02-15</td>
                    <td>Model Y</td>
                    <td>Appliances</td>
                    <td>Warehouse B</td>
                    <td>
                        <button type="button" class="update-btn" onclick="updateRow(this)">Update</button>
                        <button type="button" class="delete-btn" onclick="openDeleteModal(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="select-item"></td>
                    <td>010</td>
                    <td>Sample Item 2</td>
                    <td>Another example</td>
                    <td>5</td>
                    <td>kg</td>
                    <td>Out of Stock</td>
                    <td>2025-02-15</td>
                    <td>Model Y</td>
                    <td>Appliances</td>
                    <td>Warehouse B</td>
                    <td>
                        <button type="button" class="update-btn" onclick="updateRow(this)">Update</button>
                        <button type="button" class="delete-btn" onclick="openDeleteModal(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="select-item"></td>
                    <td>011</td>
                    <td>Sample Item 2</td>
                    <td>Another example</td>
                    <td>5</td>
                    <td>kg</td>
                    <td>Out of Stock</td>
                    <td>2025-02-15</td>
                    <td>Model Y</td>
                    <td>Appliances</td>
                    <td>Warehouse B</td>
                    <td>
                        <button type="button" class="update-btn" onclick="updateRow(this)"style = "font-family:'Akrobat', sans-serif;">Update</button>
                        <button type="button" class="delete-btn" onclick="openDeleteModal(this)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <div class="pagination">
    <button onclick="prevPage()" id="prev-btn" style = "font-family:'Akrobat', sans-serif;">Previous</button>
    <span id="page-number" style = "font-family:'Akrobat', sans-serif;">Page 1</span>
    <button onclick="nextPage()" id="next-btn" style = "font-family:'Akrobat', sans-serif;">Next</button>
</div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to delete this item?</p>
        <div class="modal-buttons">
            <button id="confirmDelete" class="delete-btn">Yes</button>
            <button id="cancelDelete" class="cancel-btn">Cancel</button>
        </div>
    </div>
</div>



    <script src="../js/records.js"></script>
</body>
</html>
