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
    <link id="page-day-css" rel="stylesheet" href="how-to-light.css" type="text/css">
    <link id="page-night-css" rel="stylesheet" href="how-to-dark.css" type="text/css" disabled>
   
    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="A_script.js"></script> <!-- Global Script -->
    <script src="how-to.js"></script> <!-- Page-Specific Script -->
</head>

<body>

<?php
include 'A_topbar.php'; // Include the connection file
?>

<!--********************************** HOW TO BODY *******************************-->
<div class="how-to-body">
  <!--********************************** HEADER *******************************-->
  <div class="how-to-page-header" data-txt="txtGuide"></div>
  <!--********************************** MAIN DIV *******************************-->
  <div class="how-to-wrapper">
    <!--********************************** TAB BUTTONS *******************************-->
    <div class="how-to-tab-button-wrapper" >
      <label class="how-to-tab-button" data-txt="txtHowToArduino" >
        <input type="radio" name="how-to-tab" value="arduino" id="how-to-arduino" checked hidden>
        1. Arduino IDE
      </label>
      <div class="how-to-tab-button-divider"></div>
      <label class="how-to-tab-button" data-txt="txtHowToLibrary">
        <input type="radio" name="how-to-tab" value="library" id="how-to-library" hidden>
        2. Knižnica AutomationShield
      </label>
      <div class="how-to-tab-button-divider"></div>
      <label class="how-to-tab-button" data-txt="txtHowToAutomationShield">
        <input type="radio" name="how-to-tab" value="online" id="how-to-AutomationShield" hidden>
        3. AutomationShield 
      </label>
      <div class="how-to-tab-button-divider"></div>
      <label class="how-to-tab-button" data-txt="txtHowToVideo">
        <input type="radio" name="how-to-tab" value="video" id="how-to-video" hidden>
        Kompletné video
      </label>
    </div>

<!--********************************** ARDUINO IDE GUIDE *******************************-->
    <div class="how-to-content-wrapper" id="how-to-arduino-wrapper" show="true">

      <?php include 'how-to-modules/arduino.php' ?>

    </div>

  <!--********************************** LIBRARY GUIDE *******************************-->
    <div class="how-to-content-wrapper" id="how-to-library-wrapper" show="false">

      <?php include 'how-to-modules/library.php' ?>
      
    </div>
  <!--********************************** AutomationShield GUIDE *******************************-->
    <div class="how-to-content-wrapper-online" id="how-to-AutomationShield-wrapper" show="true">

      <?php include 'how-to-modules/automationshield.php' ?>

    </div>
  <!--********************************** VIDEO *******************************-->
    <div class="how-to-content-wrapper-online" id="how-to-video-wrapper" show="false">
      
      <?php include 'how-to-modules/video.php' ?>

    </div>
  



  <!--********************************** END OF MAIN DIV *******************************-->
  </div>
<!--********************************** END OF HOW TO BODY *******************************-->
</div>
<?php
include 'A_footbar.php'; // Include the connection file
?>

</body>
</html>



