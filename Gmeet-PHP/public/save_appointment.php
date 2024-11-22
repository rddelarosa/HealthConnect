<?php
require 'db_connection.php'; // Include DB connection

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? null;
    $diagnostic_lab = $_POST['diagnostic_lab'] ?? null;
    $hospital_id = $_POST['hospital_id'] ?? null;
    $doctor_id = $_POST['doctor_id'] ?? null;
    $appointment_schedule = $_POST['appointment_schedule'] ?? null;
    $chief_complaint = $_POST['chief_complaint'] ?? null;

    if ($username && $diagnostic_lab && $hospital_id && $doctor_id && $appointment_schedule && $chief_complaint) {
        $stmt = $conn->prepare("INSERT INTO appointments (username, diagnostic_lab, hospital_id, doctor_id, appointment_schedule, chief_complaint, status) VALUES (?, ?, ?, ?, ?, ?, 'created')");
        $stmt->bind_param("ssisss", $username, $diagnostic_lab, $hospital_id, $doctor_id, $appointment_schedule, $chief_complaint);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Appointment booked successfully.';
            if ($diagnostic_lab === 'teleconsult') {
                $response['gmeet_link'] = 'https://meet.google.com/example'; // Replace with actual logic for generating a Google Meet link
            }
        } else {
            $response['message'] = 'Failed to book appointment.';
        }
    } else {
        $response['message'] = 'All fields are required.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
