<?php
include 'db_connection.php';
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

$accountName = $currentAdmin['username'] ?? 'User';
$accountEmail = $currentAdmin['email'] ?? '';
$accountRole = $_SESSION['role'];

// Check if the 'reports' table exists
$tableCheckQuery = "SHOW TABLES LIKE 'reports'";
$tableCheckResult = $conn->query($tableCheckQuery);

if (!$tableCheckResult || $tableCheckResult->num_rows === 0) {
    echo "<p style='color: red; text-align: center;'>Error: The 'reports' table does not exist in the database.</p>";
    exit();
}

// Fetch reports data
$query = "SELECT * FROM reports";
$result = $conn->query($query);

if (!$result) {
    echo "<p style='color: red; text-align: center;'>Error fetching reports: " . htmlspecialchars($conn->error) . "</p>";
    exit();
}

// Handle report download requests
if (isset($_GET['download'])) {
    $downloadType = $_GET['download'];
    if (in_array($downloadType, ['pdf', 'xlsx'])) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if (empty($data)) {
            echo "<p style='color: red; text-align: center;'>No data available for download.</p>";
            exit();
        }

        if ($downloadType === 'pdf') {
            if (!file_exists('../libs/tcpdf/tcpdf.php')) {
                echo "<p style='color: red; text-align: center;'>Error: TCPDF library is missing. Please ensure the file '../libs/tcpdf/tcpdf.php' exists.</p>";
                exit();
            }
            require_once '../libs/tcpdf/tcpdf.php';
            $pdf = new TCPDF();
            $pdf->AddPage();
            $html = '<h1>Reports</h1><table border="1" cellpadding="5">';
            $html .= '<tr><th>Item No</th><th>Item Name</th><th>Quantity</th><th>Status</th></tr>';
            foreach ($data as $row) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($row['item_no']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['item_name']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['status']) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
            $pdf->writeHTML($html);
            $pdf->Output('reports.pdf', 'D');
        } elseif ($downloadType === 'xlsx') {
            require_once '../libs/phpspreadsheet/vendor/autoload.php';
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Item No')
                  ->setCellValue('B1', 'Item Name')
                  ->setCellValue('C1', 'Quantity')
                  ->setCellValue('D1', 'Status');
            $rowIndex = 2;
            foreach ($data as $row) {
                $sheet->setCellValue("A$rowIndex", $row['item_no'])
                      ->setCellValue("B$rowIndex", $row['item_name'])
                      ->setCellValue("C$rowIndex", $row['quantity'])
                      ->setCellValue("D$rowIndex", $row['status']);
                $rowIndex++;
            }
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="reports.xlsx"');
            $writer->save('php://output');
        }
        exit();
    } else {
        echo "<p style='color: red; text-align: center;'>Invalid download type.</p>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCGS Inventory | Dashboard</title>
    <link rel="stylesheet" href="../css/Report.css">
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
        <h2>Reports</h2>
        <div class="download-options" style="text-align: right;">
            <a href="?download=pdf" class="download-pdf">
                <img src="../assets/FileIcon/pdf.png" alt="PDF Icon" width="20"> PDF
            </a>
            <a href="?download=xlsx" class="download-xlsx">
                <img src="../assets/FileIcon/xlsx.png" alt="XLSX Icon" width="20"> XLSX
            </a>
        </div>
        <table class="report-table">
    <thead>
        <tr>
            <th>Select</th>
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
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><?php echo htmlspecialchars($row['item_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['last_updated']); ?></td>
                    <td><?php echo htmlspecialchars($row['model_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_category']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_location']); ?></td>
                    <td><?php echo htmlspecialchars($row['expiration']); ?></td>
                    <td><?php echo htmlspecialchars($row['brand']); ?></td>
                    <td><?php echo htmlspecialchars($row['supplier']); ?></td>
                    <td><?php echo htmlspecialchars($row['price_per_item']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['unit']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['reorder_point']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr class="no-data">
                <td colspan="16" style="text-align:center; padding: 10px;">No data available</td>
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

    <script src="../js/report.js"></script>
</body>
</html>