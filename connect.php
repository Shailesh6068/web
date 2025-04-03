<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', 'Spdj6068@', 'user_db');

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed!']));
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data securely
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if the user exists
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    
    // Execute query and fetch result
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the entered password with the hashed password
        if (password_verify($password, $user['password'])) {
            echo json_encode([
                'status' => 'success',
                'username' => $user['username'],
                'message' => 'Login successful!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid password. Please try again!'
            ]);
        }
    } else {
        // No user found
        echo json_encode([
            'status' => 'error',
            'message' => 'User not found!'
        ]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
