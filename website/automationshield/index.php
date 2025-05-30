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
    <link id="page-day-css" rel="stylesheet" href="index-light.css" type="text/css">
    <link id="page-night-css" rel="stylesheet" href="index-dark.css" type="text/css" disabled>
   
    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="A_script.js"></script> <!-- Global Script -->
    <script src="index.js"></script> <!-- Page-Specific Script -->
</head>

<body>

<?php
include 'A_topbar.php'; // Include the connection file
?>

<!-- Table of available arduinos -->
<div class="Arduinos">
  <div class="Arduinos-header">
    <!-- Name -->
    <div class="Arduinos-header-text" data-txt="txtName"></div>
    <div class="Arduino-header-divider"></div>
    <!-- Model -->
    <div class="Arduinos-header-text" data-txt="txtModel"></div>
    <div class="Arduino-header-divider"></div>
    <!-- MAC -->
    <div class="Arduinos-header-text" data-txt="txtMAC"></div>
    <div class="Arduino-header-divider"></div>
    <!-- Time -->
    <div class="Arduinos-header-text" data-txt="txtTime"></div>
    <div class="Arduino-header-divider"></div>
    <!-- Refresh -->
    <button class="Arduino-header-refresh" data-txt="txtRefresh" id="refresh-button"></button>
  </div>
  
  <!-- TABLE CONTENT-->
  <div class="loader-background" IsLoading="false">
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
  
  <!-- Dynamic Content -->
  <div id="arduinos-data" class="arduinos-data" IsLoading="false">
    <!-- Loader -->
    
    <!-- Data will be loaded here by index.js -->
    <div class="Arduinos-line-no-data" data-IsAvailable="true">
      <div class="Arduinos-line-text" style="width: 100%;" data-txt="txtAvailable"></div>
    </div>
  </div>
</div>

<?php
include 'A_footbar.php'; // Include the connection file
?>

</body>
</html>

