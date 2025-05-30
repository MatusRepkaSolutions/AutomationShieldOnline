<!DOCTYPE html>
<html lang="en">
<head>
    <!-- meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <title>AutomationShield</title>
    
    <!-- Global CSS + fonts -->
    <link id="day-css" rel="stylesheet" href="A_light-mode-style.css" type="text/css">
    <link id="night-css" rel="stylesheet" href="A_dark-mode-style.css" type="text/css" disabled>
    <link href='https://fonts.googleapis.com/css?family=Noto Sans' rel='stylesheet'>
    
    <!-- Page-Specific CSS -->
    <link id="page-day-css" rel="stylesheet" href="login-light.css" type="text/css">
    <link id="page-night-css" rel="stylesheet" href="login-dark.css" type="text/css" disabled>
   
    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="A_script.js"></script> <!-- Global Script -->
    <script src="login.js"></script> <!-- Page-Specific Script -->
</head>
<body>

<?php
include 'A_topbar.php'; // Include the connection file
?>

<!-- Arduino Login Form -->
<?php
include 'connect.php'; // Include the connection file

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define a variable for the ID (coming from POST or GET)
$id = null;
$status = '';
$model = '';

// Check if 'id' is provided via POST (form submission) or GET (initial page load)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
}

// Handle form submission to update the password and check the status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password']) && $id) {
    $password = $_POST['password'];

    // Update the password in the database
    $sql = "UPDATE arduino_online SET password = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('si', $password, $id);

    if ($stmt->execute()) {
        // Wait for 4 seconds to give the Arduino time to connect
        sleep(6);

        // Fetch the updated status from the database
        $sql = "SELECT status, model FROM arduino_online WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['status'] == 'connected') {
            // If Arduino is connected, redirect to the model page
            $status = $row['status'];
            $model = $row['model'];

            // Form for redirecting to model page if connected
            echo '<form id="model-redirect" method="POST" action="https://mrsolutions.sk/automationshield/shield/'.$model.'.php">';
            echo '<input type="hidden" name="id" value="'.htmlspecialchars($id).'">';
            echo '</form>';
            
        } else {
            // If not connected, show wrong password form again
            echo '<form id="index-redirect" method="POST" action="login.php">';
            echo '<input type="hidden" name="id" value="'.htmlspecialchars($id).'">';
            echo '<input type="hidden" name="wrong-password" value="1">'; // This tells the page that the password was wrong
            echo '</form>';
        }
    }
}
?>

<script>
    // Automatically submit the relevant form after checking the status
    if (document.getElementById('model-redirect')) {
        document.getElementById('model-redirect').submit(); // Submit the form to the model page
    } else if (document.getElementById('index-redirect')) {
        document.getElementById('index-redirect').submit(); // Submit the form to the index page
    }
</script>

<!-- Arduino Details Form (if ID is provided and status is not yet updated) -->
<?php if ($id && empty($status)): ?>
    <!-- Fetch and display Arduino data -->
    <?php
    // SQL query to select the entry by ID
    $sql = "SELECT * FROM arduino_online WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>

        <!-- Arduino Login Form -->
        <div class="login-arduino-wrapper" id="login-form-wrapper">
            <div id="login-arduino-informations" class="login-arduino-informations" data-login-wait="false">
              <!-- Login Title -->
              <div class="login-arduino-title" data-txt="txtLoginTitle"></div>

              <!-- Arduino Details -->
              <div class="login-arduino-line">
                  <div class="login-arduino-line-title" data-txt="txtName"></div>
                  <div class="login-arduino-line-value"><?php echo htmlspecialchars($row['name']); ?></div>
              </div>
              <div class="login-arduino-line">
                  <div class="login-arduino-line-title" data-txt="txtModel"></div>
                  <div class="login-arduino-line-value"><?php echo htmlspecialchars($row['model']); ?></div>
              </div>
              <div class="login-arduino-line">
                   <div class="login-arduino-line-title" data-txt="txtMAC"></div>
                  <div class="login-arduino-line-value"><?php echo htmlspecialchars($row['mac']); ?></div>
              </div>
              <div class="login-arduino-line">
                  <div class="login-arduino-line-title" data-txt="txtTime"></div>
                  <div class="login-arduino-line-value"><?php echo htmlspecialchars($row['datetime']); ?></div>
              </div>

              <!-- Password Form -->
              <div class="login-arduino-line-password-text">
                  <div class="login-arduino-line-password-title" data-txt="txtPassword"></div>
                  <a href="https://mrsolutions.sk/automationshield/how-to.php?tab=AutomationShield&sub=login" class="login-arduino-line-password-href" data-txt="txtPasswordWhat"></a>
              </div>
              <form id="login-form" method="POST" class="login-arduino-line-password">
                  <!-- Password input field -->
                  <input type="password" name="password" class="login-arduino-line-password-input" required>
                  
                  <?php 
                  // Display "Password doesn't match" message if 'wrong-password' is set
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['wrong-password']) && $_POST['wrong-password'] == '1') {
                      echo '<div class="login-wrong-password">Password doesn\'t match</div>';
                    }
                  ?>
                        
                  <!-- Hidden input field for the Arduino ID -->
                  <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                        
                  <!-- Submit button -->
                  <div class="login-arduino-line-button">
                      <button type="submit" class="login-arduino-submit-button" data-txt="txtConnect"></button>
                  </div>
              </form>
            </div>
            <div class="arduino-login-loader" data-login-wait="false">
              <div aria-label="Orange and tan hamster running in a metal wheel" role="img" class="wheel-and-hamster">
	            <div class="wheel"></div>
	            <div class="hamster">
		          <div class="hamster__body">
		          <div class="hamster__head">
			      	<div class="hamster__ear"></div>
				    <div class="hamster__eye"></div>
				    <div class="hamster__nose"></div>
			      </div>
			      <div class="hamster__limb hamster__limb--fr"></div>
			      <div class="hamster__limb hamster__limb--fl"></div>
			      <div class="hamster__limb hamster__limb--br"></div>
			      <div class="hamster__limb hamster__limb--bl"></div>
			      <div class="hamster__tail"></div>
		        </div>
	          </div>
	          <div class="spoke"></div>
            </div>
            </div>
        </div>

    <?php
    } else {
        echo "<p>No Arduino found with the provided ID.</p>";
    }
?>
<?php endif; ?>

<?php
include 'A_footbar.php'; // Include the connection file
?>

</body>
</html>
