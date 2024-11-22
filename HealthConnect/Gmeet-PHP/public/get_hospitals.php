<?php
require 'db_connection.php';

header('Content-Type: application/json');

$diagnosticLab = $_GET['diagnostic_lab'] ?? '';

if (empty($diagnosticLab)) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT hospital_id AS id, name FROM hospitals WHERE type = ?");
$stmt->bind_param("s", $diagnosticLab);
$stmt->execute();
$result = $stmt->get_result();

$hospitals = [];
while ($row = $result->fetch_assoc()) {
    $hospitals[] = $row;
}

echo json_encode($hospitals);
?>
