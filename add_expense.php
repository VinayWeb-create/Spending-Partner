<?php
// ✅ Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('db_connect.php');

header('Content-Type: application/json');

// ✅ Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
    exit();
}

// ✅ Validate incoming POST data
$budget_id = isset($_POST['budget_id']) ? intval($_POST['budget_id']) : 0;
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// ✅ Check for missing fields
if ($budget_id <= 0 || $amount <= 0 || empty($description)) {
    echo json_encode(["status" => "error", "message" => "Invalid expense details!"]);
    exit();
}

// ✅ Check if the budget exists and belongs to the logged-in user
$sql = "SELECT * FROM budgets WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $budget_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Budget not found or unauthorized."]);
    exit();
}

// ✅ Insert the expense using `created_at` for the date
$sql = "INSERT INTO expenses (budget_id, amount, description) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ids", $budget_id, $amount, $description);

    if ($stmt->execute()) {
        // ✅ Update the remaining budget
        $updateBudget = "UPDATE budgets SET remaining_amount = remaining_amount - ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateBudget);
        $updateStmt->bind_param("di", $amount, $budget_id);
        $updateStmt->execute();
        $updateStmt->close();

        echo json_encode(["status" => "success", "message" => "Expense added successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add expense: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "SQL Preparation Error: " . $conn->error]);
}

$conn->close();
?>
