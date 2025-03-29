<?php
session_start();
include 'db_connection.php';

function getCurrentUser($conn) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
        header("Location: ../login/login.php");
        exit();
    }

    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, email, role FROM users WHERE user_id = ?");
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
$accountRole = htmlspecialchars($currentUser['role'] ?? '');

try {
    // Fetch the total number of users (if needed for user role)
    $userCountQuery = "SELECT COUNT(*) FROM users";
    $userCountResult = $conn->query($userCountQuery);
    $userCount = $userCountResult->fetch_row()[0];
} catch (mysqli_sql_exception $e) {
    die("Error: Unable to fetch user count. Please ensure the 'users' table exists in the database.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedUsername = trim(htmlspecialchars($_POST['username']));
    $updatedEmail = trim(htmlspecialchars($_POST['email']));
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($updatedUsername) || empty($updatedEmail)) {
        echo "<script>alert('Username and Email are required.');</script>";
    } elseif (!filter_var($updatedEmail, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
    } else {
        // Update username and email
        try {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $conn->error);
            }
            $stmt->bind_param("ssi", $updatedUsername, $updatedEmail, $currentUserId);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            echo "<script>alert('Error updating profile: " . addslashes($e->getMessage()) . "');</script>";
        }

        // Update password if provided
        if (!empty($newPassword)) {
            if ($newPassword === $confirmPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                try {
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                    if (!$stmt) {
                        throw new Exception("Failed to prepare statement: " . $conn->error);
                    }
                    $stmt->bind_param("si", $hashedPassword, $currentUserId);
                    $stmt->execute();
                    $stmt->close();
                } catch (Exception $e) {
                    echo "<script>alert('Error updating password: " . addslashes($e->getMessage()) . "');</script>";
                }
            } else {
                echo "<script>alert('Passwords do not match.');</script>";
            }
        }

        echo "<script>alert('Profile updated successfully.'); window.location.reload();</script>";
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCGS Inventory | Profile</title>
    <link rel="stylesheet" href="../css/userprof.css">
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
            <li><a href="ItemRecords.php"><img src="../assets/img/list-items.png" alt="Items Icon" class="sidebar-icon"> Item Records</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-btn">
                    <img src="../assets/img/request-for-proposal.png" alt="Request Icon" class="sidebar-icon">
                    <span class="text">Request Record</span>
                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="dropdown-content">
                    <li><a href="UserItemRequests.php">New Item Request</a></li>
                    <li><a href="UserItemBorrow.php">Borrow Item Request</a></li>
                    <li><a href="UserItemReturned.php">Return Item Request</a></li></ul>
            </li>
            <li><a href="UserTransaction.php"><img src="../assets/img/time-management.png" alt="Reports Icon" class="sidebar-icon">Transaction Records</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h2 class="profile-title">User Profile</h2>
            <form class="profile-form" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Username / Account Name</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($accountName); ?>" required> <!-- Pre-filled username -->
                </div>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($accountEmail); ?>" required> <!-- Pre-filled email -->
            </div>

            <div class="form-group">
                <h3>Change Password</h3>
                <label>New Password (leave blank to keep current)</label>
                <input type="password" name="password">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password">
            </div>

            <button type="submit" class="btn save-btn">Save Changes</button>
        </form>
    </div>

    <script src="../js/userprof.js"></script>
</body>
</html>