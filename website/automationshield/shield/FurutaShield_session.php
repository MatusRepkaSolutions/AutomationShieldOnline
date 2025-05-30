<?php
session_start(); // Start the session
include '../connect.php'; // Include the database connection

// Required columns for the table
$requiredColumns = ['r', 'y', 'y2', 'y3', 'y4', 'u'];
// Check if $id is provided
if (isset($id) && !empty($id)) {
    // Fetch MAC address from arduino_online table
    $sqlFetchMac = "SELECT mac FROM arduino_online WHERE id = ?";
    $stmtFetchMac = $db->prepare($sqlFetchMac);

    if ($stmtFetchMac) {
        $stmtFetchMac->bind_param('i', $id);
        $stmtFetchMac->execute();
        $resultFetchMac = $stmtFetchMac->get_result();

        if ($resultFetchMac->num_rows > 0) {
            $row = $resultFetchMac->fetch_assoc();
            $macAddress = $row['mac'];
            $tableName = $db->real_escape_string($macAddress);

            $_SESSION['mac_address'] = $macAddress;
            error_log("MAC Address fetched and stored in session: $macAddress");

            $needsRecreate = false;

            // Check if table exists
            $sqlCheckTable = "SHOW TABLES LIKE '$tableName'";
            $resultCheckTable = $db->query($sqlCheckTable);

            if ($resultCheckTable && $resultCheckTable->num_rows > 0) {
                // Table exists - check its columns
                $sqlDescribe = "DESCRIBE `$tableName`";
                $resultDescribe = $db->query($sqlDescribe);

                $existingColumns = [];
                while ($col = $resultDescribe->fetch_assoc()) {
                    $existingColumns[] = $col['Field'];
                }

                // Compare existing columns with required ones
                sort($existingColumns);
                sort($requiredColumns);
                if ($existingColumns !== $requiredColumns) {
                    $needsRecreate = true;
                    error_log("Table `$tableName` structure mismatch. It will be dropped and recreated.");
                }
            } else {
                $needsRecreate = true; // Table doesn't exist
            }

            // Drop table if needed
            if ($needsRecreate) {
                $db->query("DROP TABLE IF EXISTS `$tableName`");
                $sqlCreateTable = "
                    CREATE TABLE `$tableName` (
                        r FLOAT,
                        y FLOAT,
                        y2 FLOAT,
                        y3 FLOAT,
                        y4 FLOAT,
                        u FLOAT
                    );
                ";
                if ($db->query($sqlCreateTable) === TRUE) {
                    error_log("Table `$tableName` created successfully.");
                    $_SESSION['table_created'] = true;
                } else {
                    error_log("Error creating table `$tableName`: " . $db->error);
                    echo "Error creating table: " . htmlspecialchars($db->error) . "<br>";
                }
            } else {
                error_log("Table `$tableName` already exists and matches the required structure.");
            }

        } else {
            error_log("No MAC address found for ID: $id");
            echo "MAC address not found for the given ID.<br>";
        }
    } else {
        error_log("Failed to prepare statement to fetch MAC address: " . $db->error);
        echo "Failed to fetch MAC address.<br>";
    }
} else {
    error_log("No ID provided in the request.");
    echo "No ID provided.<br>";
}
?>

