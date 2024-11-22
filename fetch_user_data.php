<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthconnect";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
    exit;
}

$userIdentifier = $_SESSION['username']; // Can be either username or email

// Fetch user details
$sql = "SELECT id, first_name, last_name, birthday, gender, email, username, phone_number, emergency_contact_name, communication 
        FROM users 
        WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $userIdentifier, $userIdentifier); // Use 'ss' for two strings
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $stmt->close();

    // Return all data as JSON
    echo json_encode([
        'user' => [
            'id' => $user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'birthday' => $user['birthday'],
            'gender' => $user['gender'],
            'email' => $user['email'],
            'username' => $user['username'],
            'phone_number' => $user['phone_number'],
            'emergency_contact_name' => $user['emergency_contact_name'],
            'communication' => $user['communication'],
        ],
    ]);
} else {
    echo json_encode(['error' => 'User not found']);
}

$conn->close();
?>
