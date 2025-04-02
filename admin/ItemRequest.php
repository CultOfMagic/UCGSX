<?php
include '../config/db_connection.php';
session_start();

// Verify admin session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: ../login/login.php");
    exit();
}

// Fetch currently logged-in admin details
$currentAdminId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $currentAdminId);
$stmt->execute();
$result = $stmt->get_result();
$currentAdmin = $result->fetch_assoc();
$stmt->close();

// Pass the current admin details to the frontend
$accountName = $currentAdmin['username'] ?? 'User';
$accountEmail = $currentAdmin['email'] ?? '';
$accountRole = $_SESSION['role'];

// Fetch item requests data
$query = "SELECT r.request_id, u.username, r.item_name, r.item_category, r.request_date, r.quantity, r.status 
          FROM new_item_requests r
          JOIN users u ON r.user_id = u.user_id";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $requestId = intval($_POST['request_id'] ?? 0);

    if ($action === 'approve') {
        // Approve the request
        $query = "SELECT item_name, item_category, quantity, user_id FROM new_item_requests WHERE request_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($request) {
            // Add the item to the items table
            $insertItemQuery = "INSERT INTO items (item_name, item_category, quantity, status) VALUES (?, ?, ?, 'Available')";
            $insertItemStmt = $conn->prepare($insertItemQuery);
            $insertItemStmt->bind_param("ssi", $request['item_name'], $request['item_category'], $request['quantity']);
            $insertItemStmt->execute();

            // Retrieve the newly inserted item_id
            $itemId = $conn->insert_id;
            $insertItemStmt->close();

            // Update the request status to "Approved" and set the item_id
            $updateRequestQuery = "UPDATE new_item_requests SET status = 'Approved', item_id = ? WHERE request_id = ?";
            $updateRequestStmt = $conn->prepare($updateRequestQuery);
            $updateRequestStmt->bind_param("ii", $itemId, $requestId);
            $updateRequestStmt->execute();
            $updateRequestStmt->close();

            // Notify the user
            $notificationMessage = "Your request for '{$request['item_name']}' has been approved.";
            $insertNotificationQuery = "INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())";
            $insertNotificationStmt = $conn->prepare($insertNotificationQuery);
            if ($insertNotificationStmt) {
                $insertNotificationStmt->bind_param("is", $request['user_id'], $notificationMessage);
                $insertNotificationStmt->execute();
                $insertNotificationStmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to prepare notification query.']);
                exit;
            }

            echo json_encode(['success' => true, 'message' => 'Request approved and item added to inventory.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Request not found.']);
        }
    } elseif ($action === 'reject') {
        // Reject the request
        $reason = htmlspecialchars(trim($_POST['reason'] ?? ''));
        if (empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'Rejection reason is required.']);
            exit;
        }

        // Fetch user_id for notification
        $query = "SELECT user_id, item_name FROM new_item_requests WHERE request_id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            $request = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to fetch request details.']);
            exit;
        }

        if ($request) {
            // Update the request status to "Rejected" with the reason
            $updateRequestQuery = "UPDATE new_item_requests SET status = 'Rejected', notes = ? WHERE request_id = ?";
            $updateRequestStmt = $conn->prepare($updateRequestQuery);
            if ($updateRequestStmt) {
                $updateRequestStmt->bind_param("si", $reason, $requestId);
                $updateRequestStmt->execute();
                $updateRequestStmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update request status.']);
                exit;
            }

            // Notify the user
            $notificationMessage = "Your request for '{$request['item_name']}' has been rejected. Reason: $reason";
            $insertNotificationQuery = "INSERT INTO user_notifications (user_id, message, created_at) VALUES (?, ?, NOW())";
            $insertNotificationStmt = $conn->prepare($insertNotificationQuery);
            if ($insertNotificationStmt) {
                $insertNotificationStmt->bind_param("is", $request['user_id'], $notificationMessage);
                $insertNotificationStmt->execute();
                $insertNotificationStmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send rejection notification.']);
                exit;
            }

            echo json_encode(['success' => true, 'message' => 'Request rejected with reason provided.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Request not found.']);
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCGS Inventory | Item Request</title>
    <link rel="stylesheet" href="../css/Itmreqs.css">
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
    <span class="admin-text"><?php echo htmlspecialchars($accountName); ?> (<?php echo htmlspecialchars($accountRole); ?>)</span>
    <div class="user-dropdown" id="userDropdown">
        <a href="adminprofile.php"><img src="../assets/img/updateuser.png" alt="Profile Icon" class="dropdown-icon"> Profile</a>
        <a href="adminnotification.php"><img src="../assets/img/notificationbell.png" alt="Notification Icon" class="dropdown-icon"> Notification</a>
        <a href="../login/logout.php"><img src="../assets/img/logout.png" alt="Logout Icon" class="dropdown-icon"> Logout</a>
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
    <h2>Item Request List</h2>
    <table class="item-table">
    <div class="filter-container">
    <input type="text" id="search-box" placeholder="Search...">
    <label for="start-date">Date Range:</label>
    <input type="date" id="start-date">
    <label for="end-date">To:</label>
    <input type="date" id="end-date">
</div>

    <thead>
        <tr>
            <th>Username</th>
            <th>Requested Item</th>
            <th>Item Category</th>
            <th>Request Date</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr data-request-id="<?php echo $row['request_id']; ?>">
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
            <td><?php echo htmlspecialchars($row['item_category']); ?></td>
            <td><?php echo htmlspecialchars($row['request_date']); ?></td>
            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <div class="action-buttons">
                    <button class="approve-btn">Approve</button>
                    <button class="reject-btn">Reject</button>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
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
    <script src="../js/Itmreqs.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const approveButtons = document.querySelectorAll(".approve-btn");
    const rejectButtons = document.querySelectorAll(".reject-btn");
    const rejectModal = document.getElementById("rejectModal");
    const rejectionReason = document.getElementById("rejectionReason");
    const errorMessage = document.getElementById("error-message");
    const confirmReject = document.getElementById("confirmReject");
    const cancelReject = document.getElementById("cancelReject");
    let currentRequestId = null;

    approveButtons.forEach(button => {
        button.addEventListener("click", function () {
            const requestId = this.closest("tr").dataset.requestId;

            fetch("ItemRequest.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=approve&request_id=${requestId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });
    });

    rejectButtons.forEach(button => {
        button.addEventListener("click", function () {
            currentRequestId = this.closest("tr").dataset.requestId;
            rejectionReason.value = "";
            errorMessage.textContent = "";
            rejectModal.style.display = "block";
        });
    });

    confirmReject.addEventListener("click", function () {
        const reason = rejectionReason.value.trim();

        if (!reason) {
            errorMessage.textContent = "Rejection reason is required.";
            return;
        }

        fetch("ItemRequest.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `action=reject&request_id=${currentRequestId}&reason=${encodeURIComponent(reason)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        });

        rejectModal.style.display = "none";
    });

    cancelReject.addEventListener("click", function () {
        rejectModal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target === rejectModal) {
            rejectModal.style.display = "none";
        }
    });
});
</script>
</body>
</html>
