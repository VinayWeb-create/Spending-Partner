<?php
session_start();
include('db_connect.php'); // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // ✅ Fetch both `user_id` and `username`
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // ✅ Verify password
        if (password_verify($password, $row['password'])) {
            // ✅ Store both `user_id` and `username` in session
            $_SESSION['user_id'] = $row['id'];   // Store `user_id`
            $_SESSION['username'] = $row['username']; 

            // ✅ Store `user_id` in `localStorage` using JavaScript
            echo "
            <script>
                localStorage.setItem('user_id', '{$row['id']}');
                localStorage.setItem('username', '{$row['username']}');
                window.location.href = 'newhomepagenav.html';  // Redirect after login
            </script>";
            exit();
        } else {
            echo "<script>alert('Invalid username or password!'); window.location.href='index.html';</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.location.href='index.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
