<?php
include '../connect.php'; // Use `connect.php` for the database connection

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$sqlFetchMac = "SELECT mac FROM arduino_online WHERE id = ?";
    $stmtFetchMac = $db->prepare($sqlFetchMac);

    if ($stmtFetchMac) {
        // Bind the ID to the query
        $stmtFetchMac->bind_param('i', $id);
        $stmtFetchMac->execute();
        $resultFetchMac = $stmtFetchMac->get_result();

        // Check if a result was found
        if ($resultFetchMac->num_rows > 0) {
            $row = $resultFetchMac->fetch_assoc();
            $macAddress = $row['mac'];
            }
          }
// Fetch data from the table
$query = "SELECT r, y, u FROM `$macAddress`";
$result = mysqli_query($db, $query);

if (!$result) {
    die(json_encode(['error' => 'Query failed: ' . mysqli_error($db)]));
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($data);
