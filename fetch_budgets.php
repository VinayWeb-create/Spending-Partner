<?php
session_start();
include('db_connect.php');

header('Content-Type: application/json');

// ✅ Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user ID

// ✅ Fetch budgets only for this user
$sql = "SELECT * FROM budgets WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $budgets = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($budgets); // Return budgets as JSON
    } else {
        echo json_encode(["status" => "error", "message" => "Database execution error: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "SQL Preparation Error: " . $conn->error]);
}



$conn->close();
?>
