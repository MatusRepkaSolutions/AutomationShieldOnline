<?php
include '../connect.php'; // Include the database connection

// Debug log function
function logDebug($message) {
    file_put_contents(__DIR__ . '/debug_log.txt', date('Y-m-d H:i:s') . ' - ' . $message . "\n", FILE_APPEND);
}

logDebug("Script started");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logDebug("Received POST request");

    if (!isset($_POST['experiment']) || !isset($_POST['id'])) {
        logDebug("Missing required parameters");
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $experiment = $_POST['experiment'];
    $id = intval($_POST['id']);
    logDebug("Experiment: $experiment, ID: $id");

    // Fetch MAC address
    $sqlFetchMac = "SELECT mac FROM arduino_online WHERE id = ?";
    $stmtFetchMac = $db->prepare($sqlFetchMac);
    if ($stmtFetchMac) {
        $stmtFetchMac->bind_param('i', $id);
        $stmtFetchMac->execute();
        $resultFetchMac = $stmtFetchMac->get_result();

        if ($resultFetchMac->num_rows > 0) {
            $row = $resultFetchMac->fetch_assoc();
            $macAddress = $row['mac'];
            logDebug("Fetched MAC Address: $macAddress");
        } else {
            logDebug("No MAC address found for ID: $id");
            echo json_encode(['success' => false, 'message' => 'No MAC address found']);
            exit;
        }
    } else {
        logDebug("Failed to prepare statement for fetching MAC: " . $db->error);
        echo json_encode(['success' => false, 'message' => 'SQL Error', 'error' => $db->error]);
        exit;
    }

    if ($experiment === 'PID') {
        logDebug("Processing PID experiment");

        $Kp = isset($_POST['Kp']) ? floatval($_POST['Kp']) : null;
        $Ti = isset($_POST['Ti']) ? floatval($_POST['Ti']) : null;
        $Td = isset($_POST['Td']) ? floatval($_POST['Td']) : null;
        $timer2 = isset($_POST['timer2']) ? intval($_POST['timer2']) : null;
        $status = 'START';
        $reference = isset($_POST['Reference']) ? $_POST['Reference'] : null;

        logDebug("PID Values: Kp=$Kp, Ti=$Ti, Td=$Td, Timer2=$timer2, Reference=$reference");

        $referenceValues = array_fill(0, 6, 0);
        if ($reference) {
            $reference = preg_replace('/\s*,\s*/', ',', $reference);
            $values = explode(',', $reference);
            foreach ($values as $index => $value) {
                if ($index < 6) {
                    $referenceValues[$index] = floatval($value);
                }
            }
        }

        if ($id && $timer2 > 0) {
            if (!empty($macAddress)) {
                $truncateSQL = "TRUNCATE TABLE `$macAddress`";
                logDebug("Truncating table: $macAddress");
                
                if ($db->query($truncateSQL)) {
                    logDebug("Table truncated successfully");
                } else {
                    logDebug("Failed to truncate table: " . $db->error);
                }
            }

            $sql = "UPDATE MagnetoShield SET Experiment = ?, START = ?, Kp = ?, Ti = ?, Td = ?, r1 = ?, r2 = ?, r3 = ?, r4 = ?, r5 = ?, r6 = ?, T = ? WHERE arduino_online_id = ?";
            $stmt = $db->prepare($sql);

            if ($stmt) {
                logDebug("Prepared update query for MagnetoShield");
                $stmt->bind_param(
                    'ssddddddddddi',
                    $experiment, $status, $Kp, $Ti, $Td,
                    $referenceValues[0], $referenceValues[1], $referenceValues[2],
                    $referenceValues[3], $referenceValues[4], $referenceValues[5],
                    $timer2, $id
                );

                if ($stmt->execute()) {
                    logDebug("PID data updated successfully");
                    echo json_encode(['success' => true, 'message' => 'PID data successfully updated']);
                } else {
                    logDebug("Database update failed: " . $stmt->error);
                    echo json_encode(['success' => false, 'message' => 'Database update failed', 'error' => $stmt->error]);
                }
            } else {
                logDebug("Failed to prepare update query: " . $db->error);
                echo json_encode(['success' => false, 'message' => 'Failed to prepare query', 'error' => $db->error]);
            }
        } else {
            logDebug("Invalid input for PID experiment");
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
        }
    } elseif ($experiment === 'openloop') {
        logDebug("Processing OpenLoop experiment");
        $timer = isset($_POST['timer']) ? intval($_POST['timer']) : null;
        $status = 'START';

        if (!empty($macAddress)) {
            $truncateSQL = "TRUNCATE TABLE `$macAddress`";
            logDebug("Truncating table: $macAddress");
            if ($db->query($truncateSQL)) {
                logDebug("Table truncated successfully");
            } else {
                logDebug("Failed to truncate table: " . $db->error);
            }
        }

        if ($id && $timer > 0) {
            $sql = "UPDATE MagnetoShield SET Experiment = ?, START = ?, TIMER = ? WHERE arduino_online_id = ?";
            $stmt = $db->prepare($sql);

            if ($stmt) {
                logDebug("Prepared update query for OpenLoop");
                $stmt->bind_param('ssii', $experiment, $status, $timer, $id);

                if ($stmt->execute()) {
                    logDebug("OpenLoop data updated successfully");
                    echo json_encode(['success' => true, 'message' => 'OpenLoop data successfully updated']);
                } else {
                    logDebug("Database update failed: " . $stmt->error);
                    echo json_encode(['success' => false, 'message' => 'Database update failed', 'error' => $stmt->error]);
                }
            } else {
                logDebug("Failed to prepare update query: " . $db->error);
                echo json_encode(['success' => false, 'message' => 'Failed to prepare query', 'error' => $db->error]);
            }
        }
    } elseif ($experiment === 'identification') {
        logDebug("Processing identification experiment");
        $timer = isset($_POST['timer']) ? intval($_POST['timer']) : null;
        $status = 'START';
        $reference = isset($_POST['Reference']) ? $_POST['Reference'] : null;

        if (!empty($macAddress)) {
            $truncateSQL = "TRUNCATE TABLE `$macAddress`";
            logDebug("Truncating table: $macAddress");
            if ($db->query($truncateSQL)) {
                logDebug("Table truncated successfully");
            } else {
                logDebug("Failed to truncate table: " . $db->error);
            }
        }

        if ($id && $timer > 0) {
            $sql = "UPDATE MagnetoShield SET Experiment = ?, START = ?, TIMER = ?, r1 = ?, r2 = ?, r3 = ?, r4 = ?, r5 = ?, r6 = ? WHERE arduino_online_id = ?";
            $stmt = $db->prepare($sql);

            $referenceValues = array_fill(0, 6, 0);
            if ($reference) {
                $reference = preg_replace('/\s*,\s*/', ',', $reference);
                $values = explode(',', $reference);
                foreach ($values as $index => $value) {
                    if ($index < 6) {
                        $referenceValues[$index] = floatval($value);
                    }
                }
            }
            logDebug("Identification Values: Ti=$experiment, status=$status, Timer=$timer Reference=$reference");
            if ($stmt) {
                logDebug("Prepared update query for identification");
                $stmt->bind_param('ssdddddddi', $experiment, $status, $timer, $referenceValues[0], $referenceValues[1], $referenceValues[2],
                $referenceValues[3], $referenceValues[4], $referenceValues[5], $id);

                if ($stmt->execute()) {
                    logDebug("identification data updated successfully");
                    echo json_encode(['success' => true, 'message' => 'identification data successfully updated']);
                } else {
                    logDebug("Database update failed: " . $stmt->error);
                    echo json_encode(['success' => false, 'message' => 'Database update failed', 'error' => $stmt->error]);
                }
            } else {
                logDebug("Failed to prepare update query: " . $db->error);
                echo json_encode(['success' => false, 'message' => 'Failed to prepare query', 'error' => $db->error]);
            }
        }
    }elseif ($experiment === 'LQI') {
        logDebug("Processing LQI experiment");

        $timer = isset($_POST['timer2']) ? intval($_POST['timer2']) : null;
        $status = 'START';
        $reference = isset($_POST['Reference']) ? $_POST['Reference'] : null;

        logDebug("LQI Values: Timer2=$timer, Reference=$reference");

        $referenceValues = array_fill(0, 6, 0);
        if ($reference) {
            $reference = preg_replace('/\s*,\s*/', ',', $reference);
            $values = explode(',', $reference);
            foreach ($values as $index => $value) {
                if ($index < 6) {
                    $referenceValues[$index] = floatval($value);
                }
            }
        }

        if ($id && $timer > 0) {
            if (!empty($macAddress)) {
                $truncateSQL = "TRUNCATE TABLE `$macAddress`";
                logDebug("Truncating table: $macAddress");
                
                if ($db->query($truncateSQL)) {
                    logDebug("Table truncated successfully");
                } else {
                    logDebug("Failed to truncate table: " . $db->error);
                }
            }

            $sql = "UPDATE MagnetoShield SET Experiment = ?, START = ?, TIMER = ?, r1 = ?, r2 = ?, r3 = ?, r4 = ?, r5 = ?, r6 = ?  WHERE arduino_online_id = ?";
            $stmt = $db->prepare($sql);

            if ($stmt) {
                logDebug("Prepared update query for MagnetoShield");
                $stmt->bind_param(
                    'ssdddddddi',
                    $experiment, $status, $timer,
                    $referenceValues[0], $referenceValues[1], $referenceValues[2],
                    $referenceValues[3], $referenceValues[4], $referenceValues[5],
                    $id
                );

                if ($stmt->execute()) {
                    logDebug("LQI data updated successfully");
                    echo json_encode(['success' => true, 'message' => 'LQI data successfully updated']);
                } else {
                    logDebug("Database update failed: " . $stmt->error);
                    echo json_encode(['success' => false, 'message' => 'Database update failed', 'error' => $stmt->error]);
                }
            } else {
                logDebug("Failed to prepare update query: " . $db->error);
                echo json_encode(['success' => false, 'message' => 'Failed to prepare query', 'error' => $db->error]);
            }
        } else {
            logDebug("Invalid input for LQI experiment");
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
        }
    }elseif ($experiment === 'LQIman') {
        logDebug("Processing LQI manual experiment");

        $timer = isset($_POST['timer2']) ? intval($_POST['timer2']) : null;
        $status = 'START';

        logDebug("LQI Values: Timer2=$timer");

        if ($id && $timer > 0) {
            if (!empty($macAddress)) {
                $truncateSQL = "TRUNCATE TABLE `$macAddress`";
                logDebug("Truncating table: $macAddress");
                
                if ($db->query($truncateSQL)) {
                    logDebug("Table truncated successfully");
                } else {
                    logDebug("Failed to truncate table: " . $db->error);
                }
            }

            $sql = "UPDATE MagnetoShield SET Experiment = ?, START = ?, TIMER = ?  WHERE arduino_online_id = ?";
            $stmt = $db->prepare($sql);

            if ($stmt) {
                logDebug("Prepared update query for MagnetoShield");
                $stmt->bind_param(
                    'ssdi',
                    $experiment, $status, $timer, $id
                );

                if ($stmt->execute()) {
                    logDebug("LQI data updated successfully");
                    echo json_encode(['success' => true, 'message' => 'LQI manual data successfully updated']);
                } else {
                    logDebug("Database update failed: " . $stmt->error);
                    echo json_encode(['success' => false, 'message' => 'Database update failed', 'error' => $stmt->error]);
                }
            } else {
                logDebug("Failed to prepare update query: " . $db->error);
                echo json_encode(['success' => false, 'message' => 'Failed to prepare query', 'error' => $db->error]);
            }
        } else {
            logDebug("Invalid input for LQI manual experiment");
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
        }
    }elseif ($experiment === 'EMPC') {
        logDebug("Processing EMPC experiment");

        $timer = isset($_POST['timer2']) ? intval($_POST['timer2']) : null;
        $status = 'START';
        $reference = isset($_POST['Reference']) ? $_POST['Reference'] : null;

        logDebug("EMPC Values: Timer2=$timer, Reference=$reference");

        $referenceValues = array_fill(0, 6, 0);
        if ($reference) {
            $reference = preg_replace('/\s*,\s*/', ',', $reference);
            $values = explode(',', $reference);
            foreach ($values as $index => $value) {
                if ($index < 6) {
                    $referenceValues[$index] = floatval($value);
                }
            }
        }

        if ($id && $timer > 0) {
            if (!empty($macAddress)) {
                $truncateSQL = "TRUNCATE TABLE `$macAddress`";
                logDebug("Truncating table: $macAddress");
                
                if ($db->query($truncateSQL)) {
                    logDebug("Table truncated successfully");
                } else {
                    logDebug("Failed to truncate table: " . $db->error);
                }
            }

            $sql = "UPDATE MagnetoShield SET Experiment = ?, START = ?, TIMER = ?, r1 = ?, r2 = ?, r3 = ?, r4 = ?, r5 = ?, r6 = ?  WHERE arduino_online_id = ?";
            $stmt = $db->prepare($sql);

            if ($stmt) {
                logDebug("Prepared update query for MagnetoShield");
                $stmt->bind_param(
                    'ssdddddddi',
                    $experiment, $status, $timer,
                    $referenceValues[0], $referenceValues[1], $referenceValues[2],
                    $referenceValues[3], $referenceValues[4], $referenceValues[5],
                    $id
                );

                if ($stmt->execute()) {
                    logDebug("EMPC data updated successfully");
                    echo json_encode(['success' => true, 'message' => 'EMPC data successfully updated']);
                } else {
                    logDebug("Database update failed: " . $stmt->error);
                    echo json_encode(['success' => false, 'message' => 'Database update failed', 'error' => $stmt->error]);
                }
            } else {
                logDebug("Failed to prepare update query: " . $db->error);
                echo json_encode(['success' => false, 'message' => 'Failed to prepare query', 'error' => $db->error]);
            }
        } else {
            logDebug("Invalid input for EMPC experiment");
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
        }
    }elseif ($experiment === 'EMPCman') {
        logDebug("Processing EMPC manual experiment");

        $timer = isset($_POST['timer2']) ? intval($_POST['timer2']) : null;
        $status = 'START';

        logDebug("EMPC Values: Timer2=$timer");


        if ($id && $timer > 0) {
            if (!empty($macAddress)) {
                $truncateSQL = "TRUNCATE TABLE `$macAddress`";
                logDebug("Truncating table: $macAddress");
                
                if ($db->query($truncateSQL)) {
                    logDebug("Table truncated successfully");
                } else {
                    logDebug("Failed to truncate table: " . $db->error);
                }
            }

            $sql = "UPDATE MagnetoShield SET Experiment = ?, START = ?, TIMER = ?  WHERE arduino_online_id = ?";
            $stmt = $db->prepare($sql);

            if ($stmt) {
                logDebug("Prepared update query for MagnetoShield");
                $stmt->bind_param(
                    'ssdi',
                    $experiment, $status, $timer, $id
                );

                if ($stmt->execute()) {
                    logDebug("EMPC data updated successfully");
                    echo json_encode(['success' => true, 'message' => 'EMPC data successfully updated']);
                } else {
                    logDebug("Database update failed: " . $stmt->error);
                    echo json_encode(['success' => false, 'message' => 'Database update failed', 'error' => $stmt->error]);
                }
            } else {
                logDebug("Failed to prepare update query: " . $db->error);
                echo json_encode(['success' => false, 'message' => 'Failed to prepare query', 'error' => $db->error]);
            }
        } else {
            logDebug("Invalid input for EMPC experiment");
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
        }
    }
}




