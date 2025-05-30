<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'connect.php'; // Include the database connection

if (!isset($_POST['case']) || !isset($_POST['name']) || !isset($_POST['mac'])) {
    die("Missing case, name, or mac address");
}

$case = $_POST['case'];
$name = $_POST['name'];
$mac = $_POST['mac'];
$current_datetime = date('Y-m-d H:i:s'); // Full datetime

switch ($case) {
    case 'setup':  // Case 1: Send Arduino data and return password
    if (!isset($_POST['model'])) {
        die("Missing model");
    }

    $model = $_POST['model']; // The model will be the same as the table name (e.g., AeroShield)
    $password = 'empty';
    $status = 'disconnected';
    $START = 'STOP';

    // Check if the entry already exists in the database
    $sql = "SELECT id, model FROM arduino_online WHERE name = ? AND mac = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ss', $name, $mac);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Entry exists, so update
        $row = $result->fetch_assoc();
        $arduino_online_id = $row['id'];
        $previous_model = $row['model']; // Fetch previous model (e.g., AeroShield)

        // Delete the entry in the previous model's table
        $sql_delete_old_model = "DELETE FROM $previous_model WHERE arduino_online_id = ?";
        $stmt_delete_old_model = $db->prepare($sql_delete_old_model);
        $stmt_delete_old_model->bind_param('i', $arduino_online_id);
        $stmt_delete_old_model->execute();

        // Update the arduino_online entry with the new model data
        $sql_update = "UPDATE arduino_online 
                       SET datetime = ?, password = ?, status = ?, model = ? 
                       WHERE name = ? AND mac = ?";
        $stmt_update = $db->prepare($sql_update);
        $stmt_update->bind_param('ssssss', $current_datetime, $password, $status, $model, $name, $mac);
        $stmt_update->execute();

        // Insert into the new model's table (e.g., AeroShield) only for the START column
        $sql_model_insert = "INSERT INTO $model (arduino_online_id, START) 
                             VALUES (?, ?)";
        $stmt_model_insert = $db->prepare($sql_model_insert);
        $stmt_model_insert->bind_param('is', $arduino_online_id, $START);
        $stmt_model_insert->execute();

        echo "Database updated in arduino_online and new model $model";
    } else {
        // No entry exists, so insert a new entry in arduino_online
        $sql_insert = "INSERT INTO arduino_online (name, model, mac, datetime, password, status) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $db->prepare($sql_insert);
        $stmt_insert->bind_param('ssssss', $name, $model, $mac, $current_datetime, $password, $status);
        $stmt_insert->execute();
        $arduino_online_id = $stmt_insert->insert_id; // Get the last inserted ID

        // Insert into the corresponding model table (e.g., AeroShield) only for the START column
        $sql_model_insert = "INSERT INTO $model (arduino_online_id, START) 
                             VALUES (?, ?)";
        $stmt_model_insert = $db->prepare($sql_model_insert);
        $stmt_model_insert->bind_param('is', $arduino_online_id, $START);
        $stmt_model_insert->execute();

        echo "New entry created in arduino_online and $model";
    }
    break;


    case 'password_check':  // Case for checking password
            // Retrieve stored password from the database
            $sql = "SELECT password FROM arduino_online WHERE name = ? AND mac = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ss', $name, $mac);
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Update time in arduino_online
            $sql_update = "UPDATE arduino_online SET datetime = ? WHERE name = ? AND mac = ?";
            $stmt_update = $db->prepare($sql_update);
            $stmt_update->bind_param('sss', $current_datetime, $name, $mac);
            $stmt_update->execute();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $storedPassword = $row['password'];

                // Return the stored password to Arduino
                echo $storedPassword;
            } else {
                echo "No matching Arduino found";
            }
            break;

    case 'updateStatus':  // Case for updating the status to "connected"
            // Update status to 'connected' in arduino_online
            $status = 'connected';
            $sql_update = "UPDATE arduino_online SET status = ?, datetime = ? WHERE name = ? AND mac = ?";
            $stmt_update = $db->prepare($sql_update);
            $stmt_update->bind_param('ssss', $status, $current_datetime, $name, $mac);
            $stmt_update->execute();

            echo "Status updated to $status";
            break;
        
    case 'experiment_start':  // Case for start check
        // First fetch the model and id
        $sql = "SELECT id, model FROM arduino_online WHERE name = ? AND mac = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ss', $name, $mac);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $arduino_online_id = $row['id'];
            $model = $row['model']; // Get the model (e.g., AeroShield)

            $sql_update = "UPDATE arduino_online SET datetime = ? WHERE id = ? ";
            $stmt_update = $db->prepare($sql_update);
            $stmt_update->bind_param('si', $current_datetime, $arduino_online_id);
            $stmt_update->execute();

            // Fetch the START value from the model-specific table
            $sql_model_check = "SELECT START FROM $model WHERE arduino_online_id = ?";
            $stmt_model_check = $db->prepare($sql_model_check);
            $stmt_model_check->bind_param('i', $arduino_online_id);
            $stmt_model_check->execute();
            $result_model = $stmt_model_check->get_result();

            if ($result_model->num_rows > 0) {
                $row_model = $result_model->fetch_assoc();
                $storedSTART = $row_model['START'];

                if ($storedSTART == 'START') {
                    echo "START";
                } else {
                    echo "STOP";
                }
            }
        } else {
            echo "No matching Arduino found";
        }
        break;
        
    case 'experiment_done':  // Case for stopping the experiment
        // First fetch the model and id
        $sql = "SELECT id, model FROM arduino_online WHERE name = ? AND mac = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ss', $name, $mac);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $arduino_online_id = $row['id'];
            $model = $row['model']; // Get the model (e.g., AeroShield)

            $sql_update_time = "UPDATE arduino_online SET datetime = ? WHERE id = ? ";
            $stmt_update_time = $db->prepare($sql_update_time);
            $stmt_update_time->bind_param('si', $current_datetime, $arduino_online_id);
            $stmt_update_time->execute();

            // Update the START column to 'STOP' in the model-specific table
            $sql_update = "UPDATE $model SET START = 'STOP' WHERE arduino_online_id = ?";
            $stmt_update = $db->prepare($sql_update);
            $stmt_update->bind_param('i', $arduino_online_id);
            $stmt_update->execute();

            if ($stmt_update->affected_rows > 0) {
                echo "Experiment stopped successfully.";
            } else {
                echo "Failed to stop experiment.";
            }
        } else {
            echo "No matching Arduino found";
        }
        break;


    default:
        echo "Invalid case";
        break;
}
?>

