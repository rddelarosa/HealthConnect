<?php
require 'db_connection.php';

header('Content-Type: application/json');

$hospitalId = $_GET['hospital_id'] ?? '';

if (empty($hospitalId)) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT doctor_id AS id, name FROM doctors WHERE hospital_id = ?");
$stmt->bind_param("s", $hospitalId);
$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

echo json_encode($doctors);
?>
