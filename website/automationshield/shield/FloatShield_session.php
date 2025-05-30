<?php
session_start(); // Start the session
include '../connect.php'; // Include the database connection

// Check if $id is provided
if (isset($id) && !empty($id)) {
    // Fetch MAC address from arduino_online table
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
            $tableName = $db->real_escape_string($macAddress); // Escape the table name

            // Store the MAC address in the session
            $_SESSION['mac_address'] = $macAddress; // Use consistent session key
            error_log("MAC Address fetched and stored in session: $macAddress");

            // Create the table if it doesn't already exist
            $sqlCreateTable = "
                CREATE TABLE IF NOT EXISTS `$tableName` (
                    r FLOAT,
                    y FLOAT,
                    u FLOAT
                );
            ";

            if ($db->query($sqlCreateTable) === TRUE) {
                error_log("Table `$tableName` created or already exists.");
                $_SESSION['table_created'] = true; // Optionally store a flag for table creation
            } else {
                error_log("Error creating table `$tableName`: " . $db->error);
                echo "Error creating table: " . htmlspecialchars($db->error) . "<br>";
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

