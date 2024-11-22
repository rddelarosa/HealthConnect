<?php
require 'db_connection.php';

header('Content-Type: application/json');

// Validate the input
$doctorId = $_GET['doctor_id'] ?? '';

if (empty($doctorId)) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT date, time FROM doctor_schedule WHERE doctor_id = ? AND available = true");
$stmt->bind_param("i", $doctorId);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[] = [
        'date' => $row['date'],
        'time' => $row['time']
    ];
}

echo json_encode($schedules);
?>
