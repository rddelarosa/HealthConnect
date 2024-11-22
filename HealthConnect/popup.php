<?php
// Define the correct password
$correctPassword = "mySecret123";
$passwordError = ""; // Variable to store error message (if any)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if password is submitted
    if (!empty($_POST['password'])) {
        $enteredPassword = $_POST['password'];

        // Check if the entered password is correct
        if ($enteredPassword !== $correctPassword) {
            $passwordError = "The password you entered does not match. Please try again.";
        } else {
            // Password is correct, you can proceed with the logic
            // For now, we will just show a success message
            echo "<script>alert('Password is correct!');</script>";
            // You can redirect or proceed to another page here
        }
    } else {
        $passwordError = "Password field is required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Popup Example</title>
    <style>
        /* Basic reset */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* The overlay background */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* 50% opacity */
            display: none;
            justify-content: center;
            align-items: flex-start;
            z-index: 999;
        }

        /* The popup window */
        .popup {
            background: white;
            padding: 20px;
            margin-top: 10%;
            border-radius: 5px;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }

        .popup button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup button:hover {
            background-color: #0056b3;
        }

        /* Form styling */
        input[type="password"] {
            padding: 10px;
            margin: 10px 0;
            width: 80%;
            max-width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .error-message {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>

<!-- The overlay (for popup) -->
<div class="overlay" id="popupOverlay" <?php echo ($passwordError) ? 'style="display: flex;"' : ''; ?>>
    <div class="popup">
        <h2>Password Incorrect</h2>
        <p class="error-message"><?php echo $passwordError; ?></p>
        <button onclick="closePopup()">Close Popup</button>
    </div>
</div>

<!-- Your page content -->
<div>
    <h1>Enter Password</h1>
    <form action="popup.php" method="POST" onsubmit="checkPassword(event)">
        <input type="password" id="passwordInput" name="password" placeholder="Enter password" required>
        <button type="submit">Submit</button>
    </form>
</div>

<script>
// Function to close the popup
function closePopup() {
    const popupOverlay = document.getElementById('popupOverlay');
    popupOverlay.style.display = 'none'; // Hide the popup
}

// If there is an error, show the popup
<?php if ($passwordError): ?>
    const popupOverlay = document.getElementById('popupOverlay');
    popupOverlay.style.display = 'flex';
<?php endif; ?>
</script>

</body>
</html>
