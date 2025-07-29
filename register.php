<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db_connect.php'); // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if POST variables exist
    if (!isset($_POST['username'], $_POST['email'], $_POST['password'])) {
        die("Error: Missing required fields.");
    }

    // Sanitize and validate inputs
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        die("<script>alert('All fields are required!'); window.location.href='index.html';</script>");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("<script>alert('Invalid email format!'); window.location.href='index.html';</script>");
    }

    // Check if the username already exists
    $checkUser = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($checkUser === false) {
        die("Database query error: " . $conn->error);
    }

    if ($checkUser->num_rows > 0) {
        die("<script>alert('Username already exists!'); window.location.href='../index.html';</script>");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert into the database
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registration successful! Please login now.'); window.location.href='../index.html';</script>";
    } else {
        die("Database insertion error: " . $conn->error);
    }
}

$conn->close();
?>
