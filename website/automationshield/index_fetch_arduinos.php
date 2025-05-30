<?php
include 'connect.php'; // Include the connection file

// Get current time minus 15 seconds
$timeLimit = date('Y-m-d H:i:s', strtotime('-15 seconds'));

// SQL query to select all entries not older than 15 seconds
$sql = "SELECT id, name, model, mac, datetime FROM arduino_online WHERE datetime >= '$timeLimit' AND status='disconnected'";
$result = $db->query($sql); // Use $db for the database connection

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
            echo "<div class='Arduinos-line'>";
            echo "<div class='Arduinos-line-text'>" . htmlspecialchars($row["name"]) . "</div>";
            echo "<div class='Arduino-line-divider'></div>";
            echo "<div class='Arduinos-line-text'>" . htmlspecialchars($row["model"]) . "</div>";
            echo "<div class='Arduino-line-divider'></div>";
            echo "<div class='Arduinos-line-text'>" . htmlspecialchars($row["mac"]) . "</div>";  // Display MAC address
            echo "<div class='Arduino-line-divider'></div>";
            echo "<div class='Arduinos-line-text'>" . htmlspecialchars($row["datetime"]) . "</div>";
            echo "<div class='Arduino-line-divider'></div>";
            // Form to redirect to login.php with ID in POST
            echo "<form action='login.php' method='POST' class='Arduino-line-connect-form'>";
            echo "<input type='hidden' name='id' value='" . htmlspecialchars($row["id"]) . "'>";
            echo "<button type='submit' class='Arduino-line-connect' data-txt='txtConnect'>Connect</button>";
            echo "</form>";
            echo "</div>";
    }
}
?>

