<?php
// Check if 'id' is provided via POST request
$id = isset($_POST['id']) ? intval($_POST['id']) : null;

include 'MagnetoShield_session.php'; // Include the session management file
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <!-- meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../img/favicon.ico">
    <title>AutomationShield: MagnetoShield</title>
    
    <!-- Global CSS + fonts -->
    <link id="day-css" rel="stylesheet" href="../A_light-mode-style.css" type="text/css">
    <link id="night-css" rel="stylesheet" href="../A_dark-mode-style.css" type="text/css" disabled>
    <link href='https://fonts.googleapis.com/css?family=Noto Sans' rel='stylesheet'>
    
    <!-- AutomationShield Workbench (page specific) -->
    <link id="page-day-css" rel="stylesheet" href="A_automation-shield-workbench-light.css" type="text/css">
    <link id="page-night-css" rel="stylesheet" href="A_automation-shield-workbench-dark.css" type="text/css" disabled>
    
    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../A_script.js"></script> <!-- Global Script -->
    <script src="MagnetoShield.js"></script> <!-- Page-Specific Script -->
    
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
</head>
<body>

<?php
include '../A_topbar.php'; //topbar
?>

<!-- MagnetoShield -->
<div class="automation-shield-workbench-wrapper">
<div class="automation-shield-workbench">
  <!-- SIDE PANEL -->
  <div class="automation-shield-workbench-side-panel">
    <!-- SIDE PANEL HEADER -->
    <div class="side-panel-header"> MagnetoShield </div>
    <!-- SIDE PANEL DIVIDER -->
    <div class="side-panel-divider"></div>
    
    <!-- SIDE PANEL SELECT EXPERIMENT -->
    <div class="side-panel-select">
      <select>
        <option value="PID"data-txt="PID">PID</option>
        <option value="EMPC"data-txt="txtEMPC">EMPC</option>
        <option value="EMPCman"data-txt="txtEMPCman">EMPC</option>
        <option value="LQI"data-txt="txtLQI"></option>
        <option value="LQIman"data-txt="txtLQIman"></option>
        <option value="Identification"data-txt="txtIdentification"></option>
      </select>
      
    </div>
    <!-- ***************************************************************************************-->
    <!-- ************************************SIDE PANEL FORM PID********************************-->
      
      <form id="PID-experiment-form" class="side-panel-form">
        <input type="hidden" id="hiddenID" name="id" value="<?php echo htmlspecialchars($id); ?>">
        <input type="hidden" id="experiment" name="experiment" value="PID">
        
        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text tooltip">
             <span data-txt="txtKp">&nbsp;</span> 
             <span class="tooltiptext" data-txt="txttooltipKp">&nbsp;</span>
           </div>
           <input id="Kp" name="Kp" type="number" step=".01" value="2.3" class="side-panel-form-line-input" required>
        </div>

        <div class="side-panel-form-line-wrapper">
          <div class="side-panel-form-line-input-text tooltip">
             <span data-txt="txtTi">&nbsp;</span> 
             <span class="tooltiptext" data-txt="txttooltipTi">&nbsp;</span>
          </div>
          <input id="Ti" name="Ti" type="number" step=".01" value="0.1" class="side-panel-form-line-input" required>
        </div>

        <div class="side-panel-form-line-wrapper">
          <div class="side-panel-form-line-input-text tooltip">
              <span data-txt="txtTd">&nbsp;</span> 
              <span class="tooltiptext" data-txt="txttooltipTd">&nbsp;</span>
          </div>
          <input id="Td" name="Td" type="number" step=".01" value="0.03" class="side-panel-form-line-input" required>
        </div>

        <div class="side-panel-form-line-wrapper-column">
          <div class="side-panel-form-line-input-text-column tooltip">
              <span data-txt="txtReferenceValFloat">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipReferenceFloat">&nbsp;</span>
          </div>
          <input id="Reference" name="Reference" type="text" class="side-panel-form-line-input-column" placeholder="15,8,30,50,60 min 1 max 6" required>
        </div>
        
        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text-long tooltip" >
              <span data-txt="txtPerLength">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipPerLengthFloat">&nbsp;</span>
           </div>
           <input id="TimeRange2" name="timer2" type="number" step="1" class="side-panel-form-line-input-short" required>
        </div>

        <div class="side-panel-WHAT-IS">
          <a class="side-panel-WHAT-IS-txt" href="https://mrsolutions.sk/automationshield/file/Zaklady_Prediktivneho_Riadenia.pdf#page=36" target="_blank" data-txt="txtWhatIsPID"></a>
        </div>
        
        <div class="side-panel-form-img-wrapper">
          <a href="https://github.com/gergelytakacs/AutomationShield/wiki/MagnetoShield" target="_blank" class="side-panel-form-img-href">
            <img class="side-panel-form-img" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/MagnetoShieldGithub.png">
          </a>
        </div>
  
        <!-- Submit button -->
        <div class="form-submit-button-line">
          <button type="submit" class="form-submit-button" data-txt="txtStart"></button>
          <div class="tooltipicon tooltip" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <img src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <span class="tooltiptext4" data-txt="txttooltipSTART">&nbsp;</span>
          </div>
          <div id="AlertDataSuccess" class="start-alert" data-txt="txtAlertDataSuccess" show="FALSE"></div>
          <div id="SomethingWrong" class="start-alert" data-txt="txtAlertSomethingWrong" show="FALSE"></div>
          <div id="GraphNoData" class="start-alert" data-txt="txtGraphNoData" show="FALSE"></div>
        </div>
      </form>
      
      <!-- *******************************************************************************************-->
      <!-- **************************************SIDE PANEL FORM IDENTIFICATION***********************-->
      
      <form id="IDENTIFICATION-experiment-form" class="side-panel-form">
      <input type="hidden" id="experiment" name="experiment" value="identification">
      
        <input type="hidden" id="hiddenID" name="id" value="<?php echo htmlspecialchars($id); ?>">
        
        <div class="side-panel-form-line-wrapper-column">
          <div class="side-panel-form-line-input-text-column tooltip">
            <span data-txt="txtReferenceValFloat">&nbsp;</span> 
            <span class="tooltiptext2" data-txt="txttooltipReferenceFloat">&nbsp;</span>
          </div>
          <input id="Reference" name="Reference" type="text" class="side-panel-form-line-input-column" placeholder="5.4,10.5,... min 1 max 6" required>
        </div>
        
        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text-long tooltip">
              <span data-txt="txtPerLength">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipPerLengthFloat">&nbsp;</span>
           </div>
           <input id="TimeRange" name="timer" type="number" step="1" class="side-panel-form-line-input-short" required>
        </div>

        <div class="side-panel-WHAT-IS">
          <a class="side-panel-WHAT-IS-txt" href="https://mrsolutions.sk/automationshield/file/Zaklady_Prediktivneho_Riadenia.pdf#page=36" target="_blank" data-txt="txtWhatIsIdentification"></a>
        </div>
        
        <div class="side-panel-form-img-wrapper">
          <a href="https://github.com/gergelytakacs/AutomationShield/wiki/MagnetoShield" target="_blank" class="side-panel-form-img-href">
            <img class="side-panel-form-img" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/MagnetoShieldGithub.png">
          </a>
        </div>
        
        <!-- Submit button -->
        <div class="form-submit-button-line">
          <button type="submit" class="form-submit-button" data-txt="txtStart"></button>
          <div class="tooltipicon tooltip" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <img src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <span class="tooltiptext4" data-txt="txttooltipSTART">&nbsp;</span>
          </div>
          <div id="AlertDataSuccess" class="start-alert" data-txt="txtAlertDataSuccess" show="FALSE"></div>
          <div id="SomethingWrong" class="start-alert" data-txt="txtAlertSomethingWrong" show="FALSE"></div>
          <div id="GraphNoData" class="start-alert" data-txt="txtGraphNoData" show="FALSE"></div>
        </div>
        
      </form>
      
      <!-- **********************************************************************************-->
      <!-- **********************************SIDE PANEL FORM LQI*****************************-->
      
      <form id="LQI-experiment-form" class="side-panel-form">
      <input type="hidden" id="experiment" name="experiment" value="LQI">
      
        <input type="hidden" id="hiddenID" name="id" value="<?php echo htmlspecialchars($id); ?>">

        <div class="side-panel-form-info">
          <div class="side-panel-form-info-line">
            <div class="side-panel-form-info-var lq tooltip">
              <span class="tooltiptext2" data-txt="txttooltipLQIK">&nbsp;</span>
              <span data-txt="txtK">&nbsp;</span>
              </div>
              <div class="side-panel-form-info-data">
                (18.046, -3972.5, -93.018, 33.947)
              </div>
            </div>
        </div> 
        
        <div class="side-panel-form-line-wrapper-column">
          <div class="side-panel-form-line-input-text-column tooltip">
              <span data-txt="txtReferenceValFloat">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipReferenceFloat">&nbsp;</span>
          </div>
          <input id="Reference" name="Reference" type="text" class="side-panel-form-line-input-column" placeholder="15,8,30,50,60 min 1 max 6" required>
        </div>
        
        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text-long tooltip">
              <span  data-txt="txtPerLength">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipPerLengthFloat">&nbsp;</span>
           </div>
           <input id="TimeRange2" name="timer2" type="number" step="1" class="side-panel-form-line-input-short" required>
        </div>

        <div class="side-panel-WHAT-IS">
          <a class="side-panel-WHAT-IS-txt" href="https://mrsolutions.sk/automationshield/file/Zaklady_Prediktivneho_Riadenia.pdf#page=36" target="_blank" data-txt="txtWhatIsLQI"></a>
        </div>
        
        <div class="side-panel-form-img-wrapper">
          <a href="https://github.com/gergelytakacs/AutomationShield/wiki/MagnetoShield" target="_blank" class="side-panel-form-img-href">
            <img class="side-panel-form-img" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/MagnetoShieldGithub.png">
          </a>
        </div>
  
        <!-- Submit button -->
        <div class="form-submit-button-line">
          <button type="submit" class="form-submit-button" data-txt="txtStart"></button>
          <div class="tooltipicon tooltip" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <img src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <span class="tooltiptext4" data-txt="txttooltipSTART">&nbsp;</span>
          </div>
          <div id="AlertDataSuccess" class="start-alert" data-txt="txtAlertDataSuccess" show="FALSE"></div>
          <div id="SomethingWrong" class="start-alert" data-txt="txtAlertSomethingWrong" show="FALSE"></div>
          <div id="GraphNoData" class="start-alert" data-txt="txtGraphNoData" show="FALSE"></div>
        </div>
      </form>
      
      <!-- **********************************************************************************-->
      <!-- **********************************SIDE PANEL FORM LQI MANUAL**********************-->
      
      <form id="LQIman-experiment-form" class="side-panel-form">
      <input type="hidden" id="experiment" name="experiment" value="LQIman">
      
        <input type="hidden" id="hiddenID" name="id" value="<?php echo htmlspecialchars($id); ?>">
        
        <div class="side-panel-form-info">
          <div class="side-panel-form-info-line">
            <div class="side-panel-form-info-var lq tooltip">
              <span class="tooltiptext2" data-txt="txttooltipLQIK">&nbsp;</span>
              <span data-txt="txtK">&nbsp;</span>
              </div>
              <div class="side-panel-form-info-data">
                (18.046, -3972.5, -93.018, 33.947)
              </div>
            </div>
        </div> 

        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text-long tooltip">
              <span  data-txt="txtExpLength">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipExpLength">&nbsp;</span>
           </div>
           <input id="TimeRange2" name="timer2" type="number" step="1" class="side-panel-form-line-input-short" required>
        </div>

        <div class="side-panel-form-manual-tip-header tooltip"">
          <span data-txt="txtWhatsIsManual">&nbsp;</span>
          <div class="side-panel-form-manual-tip">
            <div class="side-panel-form-manual-tip-txt" data-txt="txtOpenLoopTipFloat">&nbsp;</div>
            <img class="side-panel-form-manual-tip-img" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/potentiometer.png">
          </div>
        </div>

        <div class="side-panel-WHAT-IS">
          <a class="side-panel-WHAT-IS-txt" href="https://mrsolutions.sk/automationshield/file/Zaklady_Prediktivneho_Riadenia.pdf#page=36" target="_blank" data-txt="txtWhatIsLQI"></a>
        </div>
        
        <div class="side-panel-form-img-wrapper">
          <a href="https://github.com/gergelytakacs/AutomationShield/wiki/MagnetoShield" target="_blank" class="side-panel-form-img-href">
            <img class="side-panel-form-img" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/MagnetoShieldGithub.png">
          </a>
        </div>
  
        <!-- Submit button -->
        <div class="form-submit-button-line">
          <button type="submit" class="form-submit-button" data-txt="txtStart"></button>
          <div class="tooltipicon tooltip" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <img src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <span class="tooltiptext4" data-txt="txttooltipSTART">&nbsp;</span>
          </div>
          <div id="AlertDataSuccess" class="start-alert" data-txt="txtAlertDataSuccess" show="FALSE"></div>
          <div id="SomethingWrong" class="start-alert" data-txt="txtAlertSomethingWrong" show="FALSE"></div>
          <div id="GraphNoData" class="start-alert" data-txt="txtGraphNoData" show="FALSE"></div>
        </div>
      </form>
      
      <!-- **********************************************************************************-->
      <!-- **********************************SIDE PANEL FORM EMPC****************************-->
      
      <form id="EMPC-experiment-form" class="side-panel-form">
      <input type="hidden" id="experiment" name="experiment" value="EMPC">
      
        <input type="hidden" id="hiddenID" name="id" value="<?php echo htmlspecialchars($id); ?>">
        
        <div class="side-panel-form-info">
          <div class="side-panel-form-info-line">
            <div class="side-panel-form-info-var tooltip">
              <span class="tooltiptext2" data-txt="txttooltipMPCnp">&nbsp;</span>
              <span data-txt="txtnp">&nbsp;</span>
            </div>
            <div class="side-panel-form-info-data">
            2
            </div>
          </div>
          <div class="side-panel-form-info-line">
            <div class="side-panel-form-info-var tooltip">
              <span class="tooltiptext2" data-txt="txttooltipMPCQ">&nbsp;</span>
              <span data-txt="txtQ">&nbsp;&nbsp;&nbsp;&nbsp;</span>
            </div>
            <div class="side-panel-form-info-data">
            diag(50 100 100 10)
            </div>
          </div>
          <div class="side-panel-form-info-line">
            <div class="side-panel-form-info-var tooltip">
              <span class="tooltiptext2" data-txt="txttooltipMPCR">&nbsp;</span>
              <span data-txt="txtR">&nbsp;</span>
            </div>
            <div class="side-panel-form-info-data">
              0.1
            </div>
          </div>
        </div>  

        <div class="side-panel-form-line-wrapper-column">
          <div class="side-panel-form-line-input-text-column tooltip">
              <span  data-txt="txtReferenceValFloat">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipReferenceFloat">&nbsp;</span>
          </div>
          <input id="Reference" name="Reference" type="text" class="side-panel-form-line-input-column" placeholder="15,8,30,50,60 min 1 max 6" required>
        </div>
        
        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text-long tooltip">
              <span  data-txt="txtPerLength">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipPerLengthFloat">&nbsp;</span>
           </div>
           <input id="TimeRange2" name="timer2" type="number" step="1" class="side-panel-form-line-input-short" required>
        </div>

        <div class="side-panel-WHAT-IS">
          <a class="side-panel-WHAT-IS-txt" href="https://mrsolutions.sk/automationshield/file/Zaklady_Prediktivneho_Riadenia.pdf#page=36" target="_blank" data-txt="txtWhatIsMPC"></a>
        </div>
        
        <div class="side-panel-form-img-wrapper">
          <a href="https://github.com/gergelytakacs/AutomationShield/wiki/MagnetoShield" target="_blank" class="side-panel-form-img-href">
            <img class="side-panel-form-img" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/MagnetoShieldGithub.png">
          </a>
        </div>
  
        <!-- Submit button -->
        <div class="form-submit-button-line">
          <button type="submit" class="form-submit-button" data-txt="txtStart"></button>
          <div class="tooltipicon tooltip" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <img src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <span class="tooltiptext4" data-txt="txttooltipSTART">&nbsp;</span>
          </div>
          <div id="AlertDataSuccess" class="start-alert" data-txt="txtAlertDataSuccess" show="FALSE"></div>
          <div id="SomethingWrong" class="start-alert" data-txt="txtAlertSomethingWrong" show="FALSE"></div>
          <div id="GraphNoData" class="start-alert" data-txt="txtGraphNoData" show="FALSE"></div>
        </div>
      </form>
   <!-- **********************************************************************************-->
      <!-- **********************************SIDE PANEL FORM EMPC MANUAL******************-->
      
      <form id="EMPCman-experiment-form" class="side-panel-form">
      <input type="hidden" id="experiment" name="experiment" value="EMPCman">
      
        <input type="hidden" id="hiddenID" name="id" value="<?php echo htmlspecialchars($id); ?>">
        
        <div class="side-panel-form-info">
          <div class="side-panel-form-info-line">
            <div class="side-panel-form-info-var tooltip">
              <span class="tooltiptext2" data-txt="txttooltipMPCnp">&nbsp;</span>
              <span data-txt="txtnp">&nbsp;</span>
            </div>
            <div class="side-panel-form-info-data">
            2
            </div>
          </div>
          <div class="side-panel-form-info-line">
            <div class="side-panel-form-info-var tooltip">
              <span class="tooltiptext2" data-txt="txttooltipMPCQ">&nbsp;</span>
              <span data-txt="txtQ">&nbsp;&nbsp;&nbsp;</span>
            </div>
            <div class="side-panel-form-info-data">
            diag(50 100 100 10)
            </div>
          </div>
          <div class="side-panel-form-info-line">
            <div class="side-panel-form-info-var tooltip">
              <span class="tooltiptext2" data-txt="txttooltipMPCR">&nbsp;</span>
              <span data-txt="txtR">&nbsp;</span>
            </div>
            <div class="side-panel-form-info-data">
              0.1
            </div>
          </div>
        </div> 

        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text-long tooltip">
              <span  data-txt="txtExpLength">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipExpLength">&nbsp;</span>
           </div>
           <input id="TimeRange2" name="timer2" type="number" step="1" class="side-panel-form-line-input-short" required>
        </div>
        
        <div class="side-panel-form-manual-tip-header tooltip"">
          <span data-txt="txtWhatsIsManual">&nbsp;</span>
          <div class="side-panel-form-manual-tip">
            <div class="side-panel-form-manual-tip-txt" data-txt="txtOpenLoopTipFloat">&nbsp;</div>
            <img class="side-panel-form-manual-tip-img" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/potentiometer.png">
          </div>
        </div>

        <div class="side-panel-WHAT-IS">
          <a class="side-panel-WHAT-IS-txt" href="https://mrsolutions.sk/automationshield/file/Zaklady_Prediktivneho_Riadenia.pdf#page=36" target="_blank" data-txt="txtWhatIsMPC"></a>
        </div>
        
        <div class="side-panel-form-img-wrapper">
          <a href="https://github.com/gergelytakacs/AutomationShield/wiki/MagnetoShield" target="_blank" class="side-panel-form-img-href">
            <img class="side-panel-form-img" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/MagnetoShieldGithub.png">
          </a>
        </div>
  
        <!-- Submit button -->
        <div class="form-submit-button-line">
          <button type="submit" class="form-submit-button" data-txt="txtStart"></button>
          <div class="tooltipicon tooltip" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <img src="https://mrsolutions.sk/automationshield/img/tooltip_questionmark.png">
            <span class="tooltiptext4" data-txt="txttooltipSTART">&nbsp;</span>
          </div>
          <div id="AlertDataSuccess" class="start-alert" data-txt="txtAlertDataSuccess" show="FALSE"></div>
          <div id="SomethingWrong" class="start-alert" data-txt="txtAlertSomethingWrong" show="FALSE"></div>
          <div id="GraphNoData" class="start-alert" data-txt="txtGraphNoData" show="FALSE"></div>
        </div>
      </form>
  </div>
  <!-- ****************************************************************************************-->
  <!-- ************************************** MAIN PANEL ************************************* -->
  
  <div class="automation-shield-workbench-main-panel-wrapper">
    <div class="automation-shield-workbench-main-panel">
        
      <!-- ************************************** STATE GRAPH ************************************* -->
      <div class="workbench-graph-container-wrapper" id="stateGraphScroll" big="FALSE">
      
        <!-- Graph header -->
        <div class="workbench-graph-header">
          <div class="workbench-graph-header-txt" data-txt="txtStateGraph"></div>
          
          <div class="workbench-graph-header-hide-button-wrapper">
            <div class="workbench-graph-header-hide-button-text tooltip">
              <span  data-txt="txtControls">&nbsp;</span> 
              <span class="tooltiptext3" data-txt="txttooltipControls">&nbsp;</span>
            </div>
            <div class="workbench-graph-header-hide-button">
              <label class="workbench-graph-header-hide-button-switch">
                <input type="checkbox" id="state-graph-controls-button">
                <span class="workbench-graph-header-hide-button-slider"></span>
              </label>
            </div>
          </div>
          
        </div>
        
        <!-- Graph  -->
        <div class="workbench-graph-container" id="stateGraph" big="FALSE">
          <canvas id="experimentGraph" class="experimentGraph" width="8000px"></canvas>
        </div>
      </div>  
      
        <!-- Graph controls -->
        <div class="workbench-graph-controls" id="state-graph-controls-container" hide="FALSE">
        
          <!-- Graph size button -->
          <div class="workbench-graph-controls-size-button-wrapper">
            <div class="workbench-graph-controls-size-button-text tooltip">
              <span  data-txt="txtDetail">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipDetail">&nbsp;</span>
            </div>
            <div class="workbench-graph-controls-size-button">
              <label class="workbench-graph-controls-size-button-switch">
                <input type="checkbox" id="state-graph-size">
                <span class="workbench-graph-controls-size-button-slider"></span>
              </label>
            </div>
          </div>
          
          <!-- Divider -->
          <div class="workbench-graph-controls-divider"></div>
           
          <!-- Legend -->  
          <div class="workbench-graph-legend-wrapper">
            <div class="workbench-graph-legend-text tooltip">
                <span  data-txt="txtLegend">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipLegend">&nbsp;</span>
            </div>
            <div id="stateLegend" class="workbench-graph-controls-legend"></div>
          </div>
          
          <!-- Divider -->
          <div class="workbench-graph-controls-divider"></div>
          
          <!-- Graph config -->
            <!-- y config -->
            <div class="workbench-graph-axis-paramether-wrapper">
              <div class="workbench-graph-axis-paramether-text tooltip">
                <span  data-txt="txtsetY">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipsetY">&nbsp;</span>
              </div>
              <div class="workbench-graph-axis-paramether-wrapper-inside">
                <input class="workbench-graph-axis-paramether-color" type="color" id="datasetYColor" value="#0000ff">
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetYLineType">
                  <option value="solid">Solid</option>  
                  <option value="dashed">Dashed</option> 
                  <option value="dotted">Dotted</option>
                  <option value="pointradius">Circles</option> 
                </select>
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetYLineWidth">
                  <option value="1">1 px</option>
                  <option value="2">2 px</option>
                  <option value="3">3 px</option>
                  <option value="4">4 px</option>
                  <option value="5">5 px</option>
                  <option value="6">6 px</option>
                  <option value="7">7 px</option>
                </select>
                
              </div>
            </div>
            
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
          
            <!-- r config -->
            <div class="workbench-graph-axis-paramether-wrapper">
              <div class="workbench-graph-axis-paramether-text tooltip">
                <span  data-txt="txtsetR">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipsetR">&nbsp;</span>
              </div>
              <div class="workbench-graph-axis-paramether-wrapper-inside">
                <input class="workbench-graph-axis-paramether-color" type="color" id="datasetRColor" value="#FF0000">
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetRLineType">
                  <option value="dashed">Dashed</option>  
                  <option value="solid">Solid</option> 
                  <option value="dotted">Dotted</option>
                  <option value="pointradius">Circles</option> 
                </select>
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetRLineWidth">
                  <option value="1">1 px</option>
                  <option value="2">2 px</option>
                  <option value="3">3 px</option>
                  <option value="4">4 px</option>
                  <option value="5">5 px</option>
                  <option value="6">6 px</option>
                  <option value="7">7 px</option>
                </select>
                
              </div>
            </div>
            
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Graph color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                <span  data-txt="txtGraph">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipGraph">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetstatecolor" value="#FFFFFF">
            </div>  
            
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Axes color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                <span  data-txt="txtAxes">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipAxes">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetstateaxes" value="#666666">
            </div>  
             
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Grid color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                  <span  data-txt="txtGrid">&nbsp;</span> 
                  <span class="tooltiptext2" data-txt="txttooltipGrid">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetstategrid" value="#e5e5e5">
            </div> 
          
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="1" id="updateStateGraph" data-txt="txtSet"></button>
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="2" id="resetStateZoom" data-txt="txtResetZoom"></button>
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="3" id="resetStateGraph" data-txt="txtGraphDefault"></button>
        <!-- End of state controls -->
        </div>
        
      <!-- divider -->
      <div class="workbench-header-divider"></div>
      
      <!-- ************************************** ACTION GRAPH ************************************* -->
      <div class="workbench-graph-container-wrapper" id="actionGraphScroll" big="FALSE">
      
        <!-- Action Graph header -->
        <div class="workbench-graph-header">
          <div class="workbench-graph-header-txt" data-txt="txtActionGraph"></div>
       
          <div class="workbench-graph-header-hide-button-wrapper">
            <div class="workbench-graph-header-hide-button-text tooltip">
              <span  data-txt="txtControls">&nbsp;</span> 
              <span class="tooltiptext3" data-txt="txttooltipControls">&nbsp;</span>
            </div>
            <div class="workbench-graph-header-hide-button">
              <label class="workbench-graph-header-hide-button-switch">
                <input type="checkbox" id="action-graph-controls-button">
                <span class="workbench-graph-header-hide-button-slider"></span>
              </label>
            </div>
          </div>
       
        </div>
        
        <!-- Action Graph  -->
        <div class="workbench-graph-container" id="actionGraph" big="FALSE">  
          <canvas id="experimentGraph2" class="experimentGraph" width="8000px"></canvas>
        </div>
      </div>
        
        <!-- Action Graph controls -->
        <div class="workbench-graph-controls" id="action-graph-controls-container" hide="FALSE">
        
          <!-- Graph size button -->
          <div class="workbench-graph-controls-size-button-wrapper">
            <div class="workbench-graph-controls-size-button-text tooltip">
                <span  data-txt="txtDetail">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipDetail">&nbsp;</span>
            </div>
            <div class="workbench-graph-controls-size-button">
              <label class="workbench-graph-controls-size-button-switch" >
                <input type="checkbox" id="action-graph-size">
                <span class="workbench-graph-controls-size-button-slider"></span>
              </label>
            </div>
          </div>
          
          <!-- Divider -->
          <div class="workbench-graph-controls-divider"></div>
          
         <!-- Legend -->  
          <div class="workbench-graph-legend-wrapper">
            <div class="workbench-graph-legend-text tooltip">
                <span  data-txt="txtLegend">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipLegend">&nbsp;</span>
            </div>
            <div id="actionLegend" class="workbench-graph-controls-legend"></div>
          </div>
          
          <!-- Divider -->
          <div class="workbench-graph-controls-divider"></div>
          
          <!-- Graph config -->
            <!-- u config -->
            <div class="workbench-graph-axis-paramether-wrapper">
              <div class="workbench-graph-axis-paramether-text tooltip">
                <span  data-txt="txtsetU">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipsetU">&nbsp;</span>
              </div>
              <div class="workbench-graph-axis-paramether-wrapper-inside">
                <input class="workbench-graph-axis-paramether-color" type="color" id="datasetUColor" value="#800080">
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetULineType">
                  <option value="pointradius">Circles</option>  
                  <option value="dashed">Dashed</option> 
                  <option value="dotted">Dotted</option>
                  <option value="solid">Solid</option> 
                </select>
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetULineWidth">
                  <option value="1">1 px</option>
                  <option value="2">2 px</option>
                  <option value="3">3 px</option>
                  <option value="4">4 px</option>
                  <option value="5">5 px</option>
                  <option value="6">6 px</option>
                  <option value="7">7 px</option>
                </select>
                
              </div>
            </div>
            
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Graph color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                <span  data-txt="txtGraph">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipGraph">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetactioncolor" value="#FFFFFF">
            </div>  
            
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Axes color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                  <span  data-txt="txtAxes">&nbsp;</span> 
                  <span class="tooltiptext2" data-txt="txttooltipAxes">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetactionaxes" value="#666666">
            </div>  
             
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Grid color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                  <span  data-txt="txtGrid">&nbsp;</span> 
                  <span class="tooltiptext2" data-txt="txttooltipGrid">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetactiongrid" value="#e5e5e5">
            </div> 
          
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <button class="workbench-graph-axis-paramether-update-button" id="updateActionGraph" data-txt="txtSet"></button>
            <button class="workbench-graph-axis-paramether-update-button" id="resetActionZoom" data-txt="txtResetZoom"></button>
            <button class="workbench-graph-axis-paramether-update-button" id="resetActionGraph" data-txt="txtGraphDefault"></button>
            
        <!-- End of action controls -->
        </div>
      
      <!-- divider -->
      <div class="workbench-header-divider"></div>
    
      <!-- Display ID and MAC -->
      <div style="margin: 500px auto auto 20px;">
        <?php if ($id): ?>
          Received arduino_online ID: <?php echo htmlspecialchars($id); ?><br> 
          MAC Address: <?php echo htmlspecialchars($macAddress); ?>
        <?php else: ?>
          No ID received
        <?php endif; ?>
      </div>
      
    </div><!-- End of automation main-panel --> 
  </div> <!-- End of automation main-panel-wrapper -->
  
  <!-- If Shield disconnected --> 
<div id="Shield-disconnected-wrapper" class="Shield-disconnected-wrapper" disconnected="false">
  <div class="Shield-disconnected-text" data-txt="txtShieldDisconnected"></div>
  <a href="https://mrsolutions.sk/automationshield/" class="Shield-disconnected-href">
    <button class="Shield-disconnected-button" data-txt="txtReturnHome"></button>
  </a>
</div>
  
</div><!-- End of automation shield workbench -->
</div><!-- End of automation shield workbench wrapper-->
<?php
include '../A_footbar.php'; // Include the connection file
?>
</body>
</html>





