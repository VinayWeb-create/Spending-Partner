<?php
session_start();  // ✅ Ensure session starts

// ✅ Include database connection
include('db_connect.php');
header('Content-Type: application/json');

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
    exit();
}

$userId = $_SESSION['user_id'];  // ✅ Get user ID from session

// ✅ Read JSON input
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid data format."]);
    exit();
}

// ✅ Extract and sanitize data
$budgetName = mysqli_real_escape_string($conn, $data['name'] ?? '');
$budgetAmount = floatval($data['amount'] ?? 0);
$timePeriod = mysqli_real_escape_string($conn, $data['time_period'] ?? '');

if (empty($budgetName) || $budgetAmount <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid budget details."]);
    exit();
}

// ✅ Insert into database
$remainingAmount = $budgetAmount;

$sql = "INSERT INTO budgets (user_id, name, amount, remaining_amount, time_period) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("isdss", $userId, $budgetName, $budgetAmount, $remainingAmount, $timePeriod);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Budget saved successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error saving budget: " . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "SQL preparation error: " . $conn->error]);
}

$conn->close();
?>
