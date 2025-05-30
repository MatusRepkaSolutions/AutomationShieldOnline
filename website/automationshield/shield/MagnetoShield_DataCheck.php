<?php
include '../connect.php'; // Database connection

header('Content-Type: application/json');

// Ensure `id` is provided
if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Missing ID parameter."]);
    exit;
}

$arduino_id = intval($_GET['id']); // Convert to integer
$current_time = time(); // Get current Unix timestamp

// Step 1: Check `arduino_online` table for last update time and status
$sql = "SELECT datetime, status FROM arduino_online WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $arduino_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "No data found for ID: $arduino_id"]);
    exit;
}

$row = $result->fetch_assoc();
$last_update_time = strtotime($row['datetime']);
$time_difference = $current_time - $last_update_time;
$arduino_status = $row['status']; // Fetch 'status' field

// Step 2: Check if Arduino is already disconnected
if ($arduino_status === "disconnected") {
    echo json_encode(["disconnected" => true]);
    exit;
}

// Step 3: Check `FloatShield` table for `START` status
$sql_status = "SELECT START FROM MagnetoShield WHERE arduino_online_id = ?";
$stmt_status = $db->prepare($sql_status);
$stmt_status->bind_param('i', $arduino_id);
$stmt_status->execute();
$result_status = $stmt_status->get_result();

if ($result_status->num_rows === 0) {
    echo json_encode(["error" => "No experiment status found for ID: $arduino_id"]);
    exit;
}

$row_status = $result_status->fetch_assoc();
$status = $row_status['START'];

// Step 4: Apply the 15s timeout + START condition
$isDisconnected = ($time_difference > 15 && $status === 'STOP');

// Return JSON response
echo json_encode(["disconnected" => $isDisconnected]);
?>

