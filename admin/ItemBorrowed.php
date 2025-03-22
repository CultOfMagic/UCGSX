    <?php 
    include 'db_connection.php';
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>UCGS Inventory | Item Borrowed</title>
        <link rel="stylesheet" href="../css/ItemBorrowed.css">
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
        <h2>Item Borrowed</h2>
        
        <div class="filter-container">
            <input type="text" id="search-box" placeholder="Search...">
            <label for="start-date">Date Range:</label>
            <input type="date" id="start-date">
            <label for="end-date">To:</label>
            <input type="date" id="end-date">
        </div>

        <table class="item-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Item Name</th>
                    <th>Item Type</th>
                    <th>Date Needed</th>
                    <th>Return Date</th>
                    <th>Quantity</th>
                    <th>Purpose</th>
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Request Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="item-table-body">
        <!-- Sample Data (To be removed in backend implementation) -->
        <tr>
            <td>David Tan</td>
            <td>Projector</td>
            <td>Electronics</td>
            <td>2025-03-20</td>
            <td>2025-03-25</td>
            <td>1</td>
            <td>Presentation</td>
            <td>Handle with care</td>
            <td>Pending</td>
            <td>2025-03-18</td>
            <td>
                <button class="approve-btn">Approve</button>
                <button class="reject-btn">Reject</button>
            </td>
        </tr>
        <tr>
            <td>John Michael</td>
            <td>Laptop</td>
            <td>Electronics</td>
            <td>2025-03-18</td>
            <td>2025-03-23</td>
            <td>1</td>
            <td>Office Work</td>
            <td>Requires charging</td>
            <td>Pending</td>
            <td>2025-03-16</td>
            <td>
                <button class="approve-btn">Approve</button>
                <button class="reject-btn">Reject</button>
            </td>
        </tr>
        <tr>
            <td>Jamaica Mabazza</td>
            <td>Tablet</td>
            <td>Gadgets</td>
            <td>2025-03-21</td>
            <td>2025-03-27</td>
            <td>1</td>
            <td>Research</td>
            <td>Screen protector applied</td>
            <td>Pending</td>
            <td>2025-03-19</td>
            <td>
                <button class="approve-btn">Approve</button>
                <button class="reject-btn">Reject</button>
            </td>
        </tr>
        <tr>
            <td>Jay Neri Gasilao</td>
            <td>Microphone</td>
            <td>Audio Equipment</td>
            <td>2025-03-22</td>
            <td>2025-03-29</td>
            <td>1</td>
            <td>Event Hosting</td>
            <td>Check battery level</td>
            <td>Pending</td>
            <td>2025-03-20</td>
            <td>
                <button class="approve-btn">Approve</button>
                <button class="reject-btn">Reject</button>
            </td>
        </tr>
        <tr>
            <td>Crislyn Solero</td>
            <td>Camera</td>
            <td>Photography</td>
            <td>2025-03-19</td>
            <td>2025-03-24</td>
            <td>1</td>
            <td>Documentation</td>
            <td>Lens checked</td>
            <td>Pending</td>
            <td>2025-03-17</td>
            <td>
                <button class="approve-btn">Approve</button>
                <button class="reject-btn">Reject</button>
            </td>
        </tr>
        <tr>
            <td>Kate Solero</td>
            <td>Speaker</td>
            <td>Audio Equipment</td>
            <td>2025-03-23</td>
            <td>2025-03-28</td>
            <td>1</td>
            <td>Presentation</td>
            <td>Volume tested</td>
            <td>Pending</td>
            <td>2025-03-21</td>
            <td>
                <button class="approve-btn">Approve</button>
                <button class="reject-btn">Reject</button>
            </td>
        </tr>
        <tr>
            <td>Aezelle May Montes</td>
            <td>Whiteboard</td>
            <td>Office Supplies</td>
            <td>2025-03-17</td>
            <td>2025-03-22</td>
            <td>1</td>
            <td>Meeting</td>
            <td>Cleaned before use</td>
            <td>Pending</td>
            <td>2025-03-15</td>
            <td>
                <button class="approve-btn">Approve</button>
                <button class="reject-btn">Reject</button>
            </td>
        </tr>
        <tr>
            <td>Neri Añonuevo</td>
            <td>Projector Screen</td>
            <td>Presentation</td>
            <td>2025-03-24</td>
            <td>2025-03-30</td>
            <td>1</td>
            <td>Training Session</td>
            <td>Folded properly</td>
            <td>Pending</td>
            <td>2025-03-22</td>
            <td>
                <button class="approve-btn">Approve</button>
                <button class="reject-btn">Reject</button>
            </td>
        </tr>
        <tr>
            <td>Aaron Morales</td>
            <td>Extension Cord</td>
            <td>Electrical</td>
            <td>2025-03-15</td>
            <td>2025-03-20</td>
            <td>1</td>
            <td>Event Setup</td>
            <td>Cable management applied</td>
            <td>Pending</td>
            <td>2025-03-13</td>
            <td>
                <button class="approve-btn">Approve</button>
                <button class="reject-btn">Reject</button>
            </td>
        </tr>
    </tbody>

        </table>
        <div class="pagination">
        <button onclick="prevPage()" id="prev-btn" style = "font-family:'Akrobat', sans-serif;">Previous</button>
        <span id="page-number" style = "font-family:'Akrobat', sans-serif;">Page 1</span>
        <button onclick="nextPage()" id="next-btn" style = "font-family:'Akrobat', sans-serif;">Next</button>
    </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="close"></span>
            <h3>Reject Request</h3>
            <textarea id="rejectionReason" rows="4" placeholder="Enter reason..."></textarea>
            
            <!-- Error message display (add this) -->
            <p id="error-message" style="color: red; font-size: 14px; margin-top: 5px;"></p>

            <div class="modal-buttons">
                <button id="confirmReject" class="confirm-btn">Confirm</button>
                <button id="cancelReject" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>
        <script src="../js/Itmborrowed.js"></script>
    </body>
    </html>
