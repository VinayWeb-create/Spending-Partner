<?php
session_start();
include('db_connect.php');
header('Content-Type: application/json');

// ✅ Check user login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$userId = $_SESSION['user_id'];

// ✅ Fetch all budgets for the user
$sql = "SELECT id, name, amount, remaining_amount, time_period FROM budgets WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $budgets = [];
    while ($row = $result->fetch_assoc()) {
        $budgets[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $budgets]);
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]);
}

$conn->close();
?>
