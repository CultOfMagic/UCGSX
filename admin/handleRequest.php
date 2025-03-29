<?php
include 'db_connection.php';
session_start();

// Verify admin session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: ../login/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $requestId = intval($_POST['request_id'] ?? 0);
    $reason = $_POST['reason'] ?? null;

    if ($action === 'approve') {
        // Approve the request
        $query = "UPDATE new_item_requests SET status = 'Approved' WHERE request_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $requestId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Request approved successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to approve the request.']);
        }
        $stmt->close();
    } elseif ($action === 'reject') {
        // Reject the request with a reason
        if (empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'Rejection reason is required.']);
            exit();
        }

        $query = "UPDATE new_item_requests SET status = 'Rejected', notes = ? WHERE request_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $reason, $requestId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Request rejected successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reject the request.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
