<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('db_connect.php');

header('Content-Type: application/json');

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
    exit();
}

// ✅ Validate Budget ID
if (empty($_GET['budget_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing budget ID!"]);
    exit();
}

$budget_id = intval($_GET['budget_id']);

// ✅ Fetch Transactions (Expenses) for the Budget
$sql = "SELECT id, amount, description, created_at FROM expenses WHERE budget_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $budget_id);
$stmt->execute();
$result = $stmt->get_result();

$expenses = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($expenses);

$stmt->close();
$conn->close();
?>
