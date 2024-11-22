<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost"; // Change this as needed
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "healthconnect"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $communication = $_POST['communication'];
    $emergency_contact_name = $_POST['emergency_contact_name'];
    $relationship = $_POST['relationship'];
    $emergency_contact_phone = $_POST['emergency_contact_phone'];
    $username = $_POST['username']; // Get username from the form
    $password = $_POST['password']; // Get password from the form

    // Check if confirm password is set and match
    if (isset($_POST['confirm-password'])) {
        $confirm_password = $_POST['confirm-password']; // Get confirm password from the form
        if ($password !== $confirm_password) {
            echo "Passwords do not match.";
            exit;
        }
    } else {
        echo "Confirm password field is missing.";
        exit;
    }

    // Check if email or username already exists in database
    $email_check = "SELECT * FROM users WHERE email = ?";
    $stmt_email = $conn->prepare($email_check);
    $stmt_email->bind_param('s', $email);
    $stmt_email->execute();
    $result_email = $stmt_email->get_result();

    if ($result_email->num_rows > 0) {
        echo "This email is already registered. Please use a different email.";
    } else {
        // Username check
        $username_check = "SELECT * FROM users WHERE username = ?";
        $stmt_username = $conn->prepare($username_check);
        $stmt_username->bind_param('s', $username);
        $stmt_username->execute();
        $result_username = $stmt_username->get_result();

        if ($result_username->num_rows > 0) {
            echo "This username is already taken. Please choose a different one.";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the user data into the database
            $sql = "INSERT INTO users (first_name, last_name, birthday, gender, email, phone_number, communication, emergency_contact_name, relationship, emergency_contact_phone, username, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssssssssss', $first_name, $last_name, $birthday, $gender, $email, $phone_number, $communication, $emergency_contact_name, $relationship, $emergency_contact_phone, $username, $hashed_password);

            if ($stmt->execute()) {
                // Insert medical history
                if (!empty($_POST['condition']) && !empty($_POST['condition-details'])) {
                    $conditions = $_POST['condition'];
                    $condition_details = $_POST['condition-details'];

                    foreach ($conditions as $index => $condition) {
                        $condition_detail = $condition_details[$index];
                        $sql_medical = "INSERT INTO medical_history (username, medical_history, medical_details) 
                                        VALUES (?, ?, ?)";
                        $stmt_medical = $conn->prepare($sql_medical);
                        $stmt_medical->bind_param('sss', $username, $condition, $condition_detail);
                        $stmt_medical->execute();
                    }
                }

                // Insert allergies
                if (!empty($_POST['allergy']) && !empty($_POST['allergy-details'])) {
                    $allergies = $_POST['allergy'];
                    $allergy_details = $_POST['allergy-details'];

                    foreach ($allergies as $index => $allergy) {
                        $allergy_detail = $allergy_details[$index];
                        $sql_allergy = "INSERT INTO allergies (username, allergy, allergy_details) 
                                        VALUES (?, ?, ?)";
                        $stmt_allergy = $conn->prepare($sql_allergy);
                        $stmt_allergy->bind_param('sss', $username, $allergy, $allergy_detail);
                        $stmt_allergy->execute();
                    }
                }

                // Insert medications
                if (!empty($_POST['medication']) && !empty($_POST['medication-details'])) {
                    $medications = $_POST['medication'];
                    $medication_details = $_POST['medication-details'];

                    foreach ($medications as $index => $medication) {
                        $medication_detail = $medication_details[$index];
                        $sql_medication = "INSERT INTO current_medications (username, medication, medication_details) 
                                        VALUES (?, ?, ?)";
                        $stmt_medication = $conn->prepare($sql_medication);
                        $stmt_medication->bind_param('sss', $username, $medication, $medication_detail);
                        $stmt_medication->execute();
                    }
                }

                echo "Account created and medical details added successfully!";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}
?>
