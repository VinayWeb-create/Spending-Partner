
<?php
session_start();
include('db_connect.php');
header('Content-Type: application/json');

// ✅ Check session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
    exit();
}

$userId = $_SESSION['user_id'];

// ✅ Check if POST data is received
if (empty($_POST)) {
    echo json_encode(["status" => "error", "message" => "No input data received."]);
    exit();
}

// ✅ Extract and sanitize data from $_POST
$budgetId = $_POST['id'] ?? null;
$budgetName = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$budgetAmount = floatval($_POST['amount'] ?? 0);
$timePeriod = mysqli_real_escape_string($conn, $_POST['time_period'] ?? '');

if (!$budgetId || empty($budgetName) || $budgetAmount <= 0 || empty($timePeriod)) {
    echo json_encode(["status" => "error", "message" => "Invalid data format."]);
    exit();
}

// ✅ Update the budget
$sql = "UPDATE budgets SET name = ?, amount = ?, time_period = ? WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sdssi", $budgetName, $budgetAmount, $timePeriod, $budgetId, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Budget updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update budget."]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "SQL preparation error: " . $conn->error]);
}

$conn->close();
?>
