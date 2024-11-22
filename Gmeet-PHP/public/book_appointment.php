<?php
session_start();

// Your Google OAuth credentials
define('CLIENT_ID', '840263782158-e64doljueubq615caj40ctpa4rle1orn.apps.googleusercontent.com');
define('CLIENT_SECRET', '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');
define('REDIRECT_URI', 'http://localhost/HealthConnect/Gmeet-PHP/google_oauth.php'); // Adjust as necessary

// Check if access token is already stored
if (!isset($_SESSION['access_token'])) {
    // OAuth process to get the access token and refresh token
    if (!isset($_GET['code'])) {
        // If the code isn't provided, redirect to Google OAuth login page
        $authUrl = 'https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=' . CLIENT_ID . '&redirect_uri=' . REDIRECT_URI . '&scope=https://www.googleapis.com/auth/calendar&access_type=offline';
        header('Location: ' . $authUrl);
        exit();
    } else {
        // Exchange the authorization code for an access token
        $code = $_GET['code'];

        // Prepare data for the token request
        $tokenData = [
            'code' => $code,
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'redirect_uri' => REDIRECT_URI,
            'grant_type' => 'authorization_code'
        ];

        // Send the token request
        $tokenResponse = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($tokenData),
            ]
        ]));

        // Decode token response
        $token = json_decode($tokenResponse, true);

        // Check if we received a valid token
        if (isset($token['access_token']) && isset($token['refresh_token'])) {
            // Store the access token and refresh token in session
            $_SESSION['access_token'] = $token['access_token'];
            $_SESSION['refresh_token'] = $token['refresh_token'];

            // Redirect to the next page for appointment booking
            header('Location: book_appointment.php');
            exit();
        } else {
            // Handle invalid token error
            echo 'Error obtaining access token: ' . $tokenResponse;
            exit();
        }
    }
} else {
    // Access token already stored, use it for further requests
    createGoogleMeetEvent();
}

function createGoogleMeetEvent() {
    $accessToken = $_SESSION['access_token'];

    // Prepare event data for Google Meet
    $eventData = [
        'summary' => 'Patient Appointment', // Replace dynamically
        'location' => 'Your Clinic', 
        'description' => 'Appointment Description', 
        'start' => [
            'dateTime' => '2024-11-22T10:00:00-07:00', // Replace dynamically
            'timeZone' => 'America/Los_Angeles'
        ],
        'end' => [
            'dateTime' => '2024-11-22T10:30:00-07:00', 
            'timeZone' => 'America/Los_Angeles'
        ],
        'conferenceData' => [
            'createRequest' => [
                'requestId' => uniqid(),
                'conferenceSolutionKey' => [
                    'type' => 'hangoutsMeet'
                ]
            ]
        ]
    ];

    // Set HTTP headers with authorization
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($eventData)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents('https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1', false, $context);

    if ($response === FALSE) {
        echo json_encode(['success' => false, 'message' => 'Failed to create Google Meet link']);
    } else {
        $event = json_decode($response, true);
        $gmeetLink = $event['hangoutLink'] ?? '';

        // Redirect to save_appointment.php with Google Meet link and other form data
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'gmeet_link' => $gmeetLink
        ]);
    }
}
?>
