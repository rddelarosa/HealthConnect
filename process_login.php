<?php
// Start session to handle user login state
session_start();

// Database configuration
$host = 'localhost'; // Your database host
$db = 'healthconnect'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password

// Create a database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve user inputs
    $identifier = $_POST['username']; // Input name 'username' used for email/username
    $password = $_POST['password']; // Input name 'password'

    // Prepare the SQL query
    $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $identifier, $identifier);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a matching user is found
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the entered password with the stored hashed password
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to the dashboard
            header("Location: dashboard.html");
            exit();
        } else {
        echo "No user found with the provided username or email.";
        }

    $stmt->close();
}
}
$conn->close();
?>
