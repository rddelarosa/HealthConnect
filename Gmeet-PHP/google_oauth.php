<?php
session_start();

// Your Google OAuth credentials
define('CLIENT_ID', '840263782158-e64doljueubq615caj40ctpa4rle1orn.apps.googleusercontent.com');
define('CLIENT_SECRET', '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');
define('REDIRECT_URI', 'http://localhost/HealthConnect/Gmeet-PHP/google_oauth.php'); // Adjust as necessary

// Google API OAuth URL
$authUrl = 'https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=' . CLIENT_ID . '&redirect_uri=' . REDIRECT_URI . '&scope=https://www.googleapis.com/auth/calendar&access_type=offline';

if (!isset($_GET['code'])) {
    // Redirect to Google OAuth login page if code is not set
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

    $token = json_decode($tokenResponse, true);
    
    // Check if we received a valid token
    if (isset($token['access_token'])) {
        $_SESSION['access_token'] = $token['access_token'];

        // Proceed to create an event on Google Calendar (optional: send email link)
        createGoogleMeetEvent();

    } else {
        // Handle invalid token error
        echo 'Error obtaining access token';
        exit();
    }
}

function createGoogleMeetEvent() {
    // Get the access token from the session
    $accessToken = $_SESSION['access_token'];

    // Prepare event data
    $eventData = [
        'summary' => 'Test Appointment', // You can replace with dynamic data
        'location' => 'Your Clinic', // Replace with dynamic location
        'description' => 'Test Appointment Description', // Replace with description
        'start' => [
            'dateTime' => '2024-11-21T10:00:00-07:00', // Start time (ISO format)
            'timeZone' => 'America/Los_Angeles'
        ],
        'end' => [
            'dateTime' => '2024-11-21T10:30:00-07:00', // End time (ISO format)
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

    // Set up HTTP headers including the authorization token
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ];

    // Prepare the context for making the API call
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($eventData) // Convert event data to JSON format
        ]
    ];

    // Send the request to Google Calendar API to create the event
    $context = stream_context_create($options);
    $response = file_get_contents('https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1', false, $context);

    // Check if the event creation was successful
    if ($response === FALSE) {
        echo 'Error: Failed to create the appointment';
    } else {
        // Output the event details (optional)
        $event = json_decode($response, true);
        echo 'Appointment created successfully! Google Meet link: ' . $event['hangoutLink'];
    }
}
?>
