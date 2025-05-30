<?php
include '../connect.php'; // Use `connect.php` for the database connection

// Get ID from GET request
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Validate ID
if ($id === null || $id <= 0) {
    die(json_encode(['error' => 'Invalid or missing ID.']));
}

// Prepare SQL to fetch MAC address
$sqlFetchMac = "SELECT mac FROM arduino_online WHERE id = ?";
$stmtFetchMac = $db->prepare($sqlFetchMac);

if ($stmtFetchMac) {
    $stmtFetchMac->bind_param('i', $id);
    if (!$stmtFetchMac->execute()) {
        die(json_encode(['error' => 'MAC address fetch failed.']));
    }

    $resultFetchMac = $stmtFetchMac->get_result();
    if ($resultFetchMac->num_rows > 0) {
        $row = $resultFetchMac->fetch_assoc();
        $macAddress = $row['mac'];
    } else {
        die(json_encode(['error' => 'MAC address not found.']));
    }
} else {
    die(json_encode(['error' => 'Database query failed.']));
}

// Fetch data from the device-specific table
$query = "SELECT r, y, y2, y3, y4, u FROM `$macAddress`";
$result = mysqli_query($db, $query);
if (!$result) {
    die(json_encode(['error' => 'Query failed: ' . mysqli_error($db)]));
}

// Collect data
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Convert to JSON
$jsonResponse = json_encode($data);

// Save to json.txt
file_put_contents('json.txt', $jsonResponse);

// Set content type and output
header('Content-Type: application/json');
echo $jsonResponse;

