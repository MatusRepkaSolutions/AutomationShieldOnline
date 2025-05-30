<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../connect.php'; // Include your database connection

function log_error($message) {
    $logFile = __DIR__ . '/arduino_log.txt';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    log_error("Incoming data: " . json_encode($data));

    $mac = $data['mac'] ?? null;
    $model = $data['model'] ?? null;
    $experiment = $data['experiment'] ?? null;
    $rows = $data['data'] ?? [];

    if (empty($mac)) {
        $message = 'MAC address is missing';
        log_error($message);
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }

    // Construct the table name from the MAC address
    $tableName = $db->real_escape_string($mac);
    $errors = [];

    if (strtolower($model) === 'furutashield') {
        $sql = "INSERT INTO `$tableName` (r, y, y2, y3, u) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        if ($stmt === false) {
            $message = 'Prepare failed: ' . $db->error;
            log_error($message);
            echo json_encode(['success' => false, 'message' => $message]);
            exit;
        }

        foreach ($rows as $row) {
            $r = $row['r'];
            $y = $row['y'];
            $y2 = $row['y2'];
            $y3 = $row['y3'];
            $u = $row['u'];

            $stmt->bind_param('ddddd', $r, $y, $y2, $y3, $u);
            if (!$stmt->execute()) {
                $errMsg = "Failed to insert row: " . json_encode($row) . " - Error: " . $stmt->error;
                $errors[] = $errMsg;
                log_error($errMsg);
            }
        }
    } else {
        $sql = "INSERT INTO `$tableName` (r, y, u) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        if ($stmt === false) {
            $message = 'Prepare failed: ' . $db->error;
            log_error($message);
            echo json_encode(['success' => false, 'message' => $message]);
            exit;
        }

        foreach ($rows as $row) {
            $r = $row['r'];
            $y = $row['y'];
            $u = $row['u'];

            $stmt->bind_param('ddd', $r, $y, $u);
            if (!$stmt->execute()) {
                $errMsg = "Failed to insert row: " . json_encode($row) . " - Error: " . $stmt->error;
                $errors[] = $errMsg;
                log_error($errMsg);
            }
        }
    }

    if (empty($errors)) {
        log_error("Batch insert successful for table `$tableName`");
        echo json_encode(['success' => true, 'message' => 'Batch inserted successfully']);
    } else {
        log_error("Some rows failed for table `$tableName`");
        echo json_encode(['success' => false, 'message' => 'Some rows failed to insert', 'errors' => $errors]);
    }
}
?>

