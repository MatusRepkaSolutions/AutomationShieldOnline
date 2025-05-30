<?php
// Check if 'id' is provided via POST request
$id = isset($_POST['id']) ? intval($_POST['id']) : null;

include 'FurutaShield_session.php'; // Include the session management file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- meta -->
    <!-- meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../img/favicon.ico">
    <title>AutomationShield: FurutaShield</title>
    
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
    <script src="FurutaShield.js"></script> <!-- Page-Specific Script -->
    
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
</head>
<body>

<?php
include '../A_topbar.php'; //topbar
?>

<!-- FurutaShield -->
<div class="automation-shield-workbench-wrapper">
<div class="automation-shield-workbench">
  <!-- SIDE PANEL -->
  <div class="automation-shield-workbench-side-panel">
    <!-- SIDE PANEL HEADER -->
    <div class="side-panel-header"> FurutaShield </div>
    <!-- SIDE PANEL DIVIDER -->
    <div class="side-panel-divider"></div>
    
    <!-- SIDE PANEL SELECT EXPERIMENT -->
    <div class="side-panel-select">
      <select>
        <option value="EMPC"data-txt="txtEMPC">EMPC</option>
        <option value="LQI"data-txt="txtLQI"></option>
      </select>
      
    </div>
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
                (-0.0376, 0.3523, 0.1337)
              </div>
            </div>
        </div> 
        
        <div class="side-panel-form-line-wrapper-column">
          <div class="side-panel-form-line-input-text-column tooltip">
              <span data-txt="txtReferenceAngle">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipReferenceAngle">&nbsp;</span>
          </div>
          <input id="Reference" name="Reference" type="text" class="side-panel-form-line-input-column" placeholder="15,8,30,50,60 min 1 max 6" required>
        </div>
        
        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text-long tooltip">
              <span  data-txt="txtPerLength">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipPerLength">&nbsp;</span>
           </div>
           <input id="TimeRange2" name="timer2" type="number" step="1" min="1" class="side-panel-form-line-input-short" required>
        </div>

        <div class="side-panel-WHAT-IS">
          <a class="side-panel-WHAT-IS-txt" href="https://mrsolutions.sk/automationshield/file/Zaklady_Prediktivneho_Riadenia.pdf#page=36" target="_blank" data-txt="txtWhatIsLQI"></a>
        </div>
        
        <div class="side-panel-form-img-wrapper">
          <a href="https://github.com/gergelytakacs/AutomationShield/wiki/FurutaShield" target="_blank" class="side-panel-form-img-href">
            <img class="side-panel-form-img" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/FurutaShield.png">
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
            3
            </div>
          </div>
          <div class="side-panel-form-info-line">
            <div class="side-panel-form-info-var tooltip">
              <span class="tooltiptext2" data-txt="txttooltipMPCQ">&nbsp;</span>
              <span data-txt="txtQ">&nbsp;&nbsp;&nbsp;&nbsp;</span>
            </div>
            <div class="side-panel-form-info-data">
            diag(5, 1, 100)
            </div>
          </div>
          <div class="side-panel-form-info-line">
            <div class="side-panel-form-info-var tooltip">
              <span class="tooltiptext2" data-txt="txttooltipMPCR">&nbsp;</span>
              <span data-txt="txtR">&nbsp;</span>
            </div>
            <div class="side-panel-form-info-data">
              2000
            </div>
          </div>
        </div>  

        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text tooltip">
             <span data-txt="txtKsu">&nbsp;</span> 
             <span class="tooltiptext" data-txt="txttooltipKsu">&nbsp;</span>
           </div>
           <input id="Ksu" name="Ksu" type="number" step=".01" class="side-panel-form-line-input" value="10.5" required>
        </div>

        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text tooltip">
             <span data-txt="txtKq">&nbsp;</span> 
             <span class="tooltiptext" data-txt="txttooltipKq">&nbsp;</span>
           </div>
           <input id="Kq" name="Kq" type="number" step=".01" class="side-panel-form-line-input" value="8" required>
        </div>

        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text tooltip">
             <span data-txt="txtKdq">&nbsp;</span> 
             <span class="tooltiptext" data-txt="txttooltipKdq">&nbsp;</span>
           </div>
           <input id="Kdq" name="Kdq" type="number" step=".01" class="side-panel-form-line-input" value="9" required>
        </div>

        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text tooltip">
             <span data-txt="txtKe">&nbsp;</span> 
             <span class="tooltiptext" data-txt="txttooltipKe">&nbsp;</span>
           </div>
           <input id="Ke" name="Ke" type="number" step=".01" class="side-panel-form-line-input" value="6" required>
        </div>

        <div class="side-panel-form-line-wrapper-column">
          <div class="side-panel-form-line-input-text-column tooltip">
              <span  data-txt="txtReferenceAngle">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipReferenceAngle">&nbsp;</span>
          </div>
          <input id="Reference" name="Reference" type="text" class="side-panel-form-line-input-column" placeholder="15,8,30,50,60 min 1 max 6" required>
        </div>
        
        <div class="side-panel-form-line-wrapper">
           <div class="side-panel-form-line-input-text-long tooltip">
              <span  data-txt="txtPerLength">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipPerLength">&nbsp;</span>
           </div>
           <input id="TimeRange2" name="timer2" type="number" step="1" min="1" class="side-panel-form-line-input-short" required>
        </div>

        <div class="side-panel-WHAT-IS">
          <a class="side-panel-WHAT-IS-txt" href="https://mrsolutions.sk/automationshield/file/Zaklady_Prediktivneho_Riadenia.pdf#page=36" target="_blank" data-txt="txtWhatIsMPC"></a>
        </div>
        
        <div class="side-panel-form-img-wrapper">
          <a href="https://github.com/gergelytakacs/AutomationShield/wiki/FurutaShield" target="_blank" class="side-panel-form-img-href">
            <img class="side-panel-form-img" alt="automationshield" src="https://mrsolutions.sk/automationshield/img/FurutaShield.png">
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
      <div class="workbench-graph-container-wrapper" id="stateGraphScroll" furuta="true" big="FALSE">
      
        <!-- Graph header -->
        <div class="workbench-graph-header" furuta="true">
          <div class="workbench-graph-header-txt" data-txt="txtStateGraph1"></div>
          
          <div class="workbench-graph-header-hide-button-wrapper">
            <div class="workbench-graph-header-hide-button-text tooltip">
              <span  data-txt="txtControls">&nbsp;</span> 
              <span class="tooltiptext3" data-txt="txttooltipControls">&nbsp;</span>
            </div>
            <div class="workbench-graph-header-hide-button">
              <label class="workbench-graph-header-hide-button-switch">
                <input type="checkbox" id="state-graph-controls-button" checked>
                <span class="workbench-graph-header-hide-button-slider"></span>
              </label>
            </div>
          </div>
          
        </div>
        
        <!-- Graph  -->
        <div class="workbench-graph-container" id="stateGraph" furuta="true" big="FALSE">
          <canvas id="experimentGraph" class="experimentGraph" width="8000px"></canvas>
        </div>
      </div>  
      
        <!-- Graph controls -->
        <div class="workbench-graph-controls" id="state-graph-controls-container" furuta="true" hide="true">
        
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
      <div class="workbench-header-divider" furuta="true" ></div>

      <!-- ************************************** STATE GRAPH 2 ************************************* -->
      <div class="workbench-graph-container-wrapper" id="stateGraphScroll2" furuta="true" big="FALSE">
      
        <!-- Graph header -->
        <div class="workbench-graph-header" furuta="true">
          <div class="workbench-graph-header-txt" data-txt="txtStateGraph2"></div>
          
          <div class="workbench-graph-header-hide-button-wrapper">
            <div class="workbench-graph-header-hide-button-text tooltip">
              <span  data-txt="txtControls">&nbsp;</span> 
              <span class="tooltiptext3" data-txt="txttooltipControls">&nbsp;</span>
            </div>
            <div class="workbench-graph-header-hide-button">
              <label class="workbench-graph-header-hide-button-switch">
                <input type="checkbox" id="state-graph-controls-button2" checked>
                <span class="workbench-graph-header-hide-button-slider"></span>
              </label>
            </div>
          </div>
          
        </div>
        
        <!-- Graph  -->
        <div class="workbench-graph-container" id="stateGraph2" furuta="true" furuta="true" big="FALSE">
          <canvas id="experimentGraph3" class="experimentGraph" width="8000px"></canvas>
        </div>
      </div>  
      
        <!-- Graph controls -->
        <div class="workbench-graph-controls" id="state-graph-controls-container2" furuta="true" hide="true">
        
          <!-- Graph size button -->
          <div class="workbench-graph-controls-size-button-wrapper">
            <div class="workbench-graph-controls-size-button-text tooltip">
              <span  data-txt="txtDetail">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipDetail">&nbsp;</span>
            </div>
            <div class="workbench-graph-controls-size-button">
              <label class="workbench-graph-controls-size-button-switch">
                <input type="checkbox" id="state-graph-size2">
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
            <div id="stateLegend2" class="workbench-graph-controls-legend"></div>
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
                <input class="workbench-graph-axis-paramether-color" type="color" id="datasetYColor2" value="#0000ff">
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetYLineType2">
                  <option value="solid">Solid</option>  
                  <option value="dashed">Dashed</option> 
                  <option value="dotted">Dotted</option>
                  <option value="pointradius">Circles</option> 
                </select>
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetYLineWidth2">
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
              <input class="workbench-graph-color" type="color" id="datasetstatecolor2" value="#FFFFFF">
            </div>  
            
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Axes color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                <span  data-txt="txtAxes">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipAxes">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetstateaxes2" value="#666666">
            </div>  
             
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Grid color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                  <span  data-txt="txtGrid">&nbsp;</span> 
                  <span class="tooltiptext2" data-txt="txttooltipGrid">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetstategrid2" value="#e5e5e5">
            </div> 
          
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="1" id="updateStateGraph2" data-txt="txtSet"></button>
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="2" id="resetStateZoom2" data-txt="txtResetZoom"></button>
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="3" id="resetStateGraph2" data-txt="txtGraphDefault"></button>
        <!-- End of state controls -->
        </div>
        
      <!-- divider -->
      <div class="workbench-header-divider" furuta="true" ></div>

      <!-- ************************************** STATE GRAPH 3 ************************************* -->
      <div class="workbench-graph-container-wrapper" id="stateGraphScroll3" furuta="true" big="FALSE">
      
        <!-- Graph header -->
        <div class="workbench-graph-header" furuta="true">
          <div class="workbench-graph-header-txt" data-txt="txtStateGraph3"></div>
          
          <div class="workbench-graph-header-hide-button-wrapper">
            <div class="workbench-graph-header-hide-button-text tooltip">
              <span  data-txt="txtControls">&nbsp;</span> 
              <span class="tooltiptext3" data-txt="txttooltipControls">&nbsp;</span>
            </div>
            <div class="workbench-graph-header-hide-button">
              <label class="workbench-graph-header-hide-button-switch">
                <input type="checkbox" id="state-graph-controls-button3" checked>
                <span class="workbench-graph-header-hide-button-slider"></span>
              </label>
            </div>
          </div>
          
        </div>
        
        <!-- Graph  -->
        <div class="workbench-graph-container" id="stateGraph3" furuta="true" furuta="true" big="FALSE">
          <canvas id="experimentGraph4" class="experimentGraph" width="8000px"></canvas>
        </div>
      </div>  
      
        <!-- Graph controls -->
        <div class="workbench-graph-controls" id="state-graph-controls-container3" furuta="true" hide="true">
        
          <!-- Graph size button -->
          <div class="workbench-graph-controls-size-button-wrapper">
            <div class="workbench-graph-controls-size-button-text tooltip">
              <span  data-txt="txtDetail">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipDetail">&nbsp;</span>
            </div>
            <div class="workbench-graph-controls-size-button">
              <label class="workbench-graph-controls-size-button-switch">
                <input type="checkbox" id="state-graph-size3">
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
            <div id="stateLegend3" class="workbench-graph-controls-legend"></div>
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
                <input class="workbench-graph-axis-paramether-color" type="color" id="datasetYColor3" value="#0000ff">
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetYLineType3">
                  <option value="solid">Solid</option>  
                  <option value="dashed">Dashed</option> 
                  <option value="dotted">Dotted</option>
                  <option value="pointradius">Circles</option> 
                </select>
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetYLineWidth3">
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
              <input class="workbench-graph-color" type="color" id="datasetstatecolor3" value="#FFFFFF">
            </div>  
            
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Axes color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                <span  data-txt="txtAxes">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipAxes">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetstateaxes3" value="#666666">
            </div>  
             
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Grid color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                  <span  data-txt="txtGrid">&nbsp;</span> 
                  <span class="tooltiptext2" data-txt="txttooltipGrid">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetstategrid3" value="#e5e5e5">
            </div> 
          
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="1" id="updateStateGraph3" data-txt="txtSet"></button>
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="2" id="resetStateZoom3" data-txt="txtResetZoom"></button>
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="3" id="resetStateGraph3" data-txt="txtGraphDefault"></button>
        <!-- End of state controls -->
        </div>
        
      <!-- divider -->
      <div class="workbench-header-divider" furuta="true" ></div>

      <!-- ************************************** STATE GRAPH 4 ************************************* -->
      <div class="workbench-graph-container-wrapper" id="stateGraphScroll4" furuta="true" big="FALSE">
      
        <!-- Graph header -->
        <div class="workbench-graph-header" furuta="true">
          <div class="workbench-graph-header-txt" data-txt="txtStateGraph4"></div>
          
          <div class="workbench-graph-header-hide-button-wrapper">
            <div class="workbench-graph-header-hide-button-text tooltip">
              <span  data-txt="txtControls">&nbsp;</span> 
              <span class="tooltiptext3" data-txt="txttooltipControls">&nbsp;</span>
            </div>
            <div class="workbench-graph-header-hide-button">
              <label class="workbench-graph-header-hide-button-switch">
                <input type="checkbox" id="state-graph-controls-button4" checked>
                <span class="workbench-graph-header-hide-button-slider"></span>
              </label>
            </div>
          </div>
          
        </div>
        
        <!-- Graph  -->
        <div class="workbench-graph-container" id="stateGraph4" furuta="true" furuta="true" big="FALSE">
          <canvas id="experimentGraph5" class="experimentGraph" width="8000px"></canvas>
        </div>
      </div>  
      
        <!-- Graph controls -->
        <div class="workbench-graph-controls" id="state-graph-controls-container4" furuta="true" hide="true">
        
          <!-- Graph size button -->
          <div class="workbench-graph-controls-size-button-wrapper">
            <div class="workbench-graph-controls-size-button-text tooltip">
              <span  data-txt="txtDetail">&nbsp;</span> 
              <span class="tooltiptext2" data-txt="txttooltipDetail">&nbsp;</span>
            </div>
            <div class="workbench-graph-controls-size-button">
              <label class="workbench-graph-controls-size-button-switch">
                <input type="checkbox" id="state-graph-size4">
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
            <div id="stateLegend4" class="workbench-graph-controls-legend"></div>
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
                <input class="workbench-graph-axis-paramether-color" type="color" id="datasetYColor4" value="#0000ff">
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetYLineType4">
                  <option value="solid">Solid</option>  
                  <option value="dashed">Dashed</option> 
                  <option value="dotted">Dotted</option>
                  <option value="pointradius">Circles</option> 
                </select>
                <select class="workbench-graph-axis-paramether-dropdown" id="datasetYLineWidth4">
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
              <input class="workbench-graph-color" type="color" id="datasetstatecolor4" value="#FFFFFF">
            </div>  
            
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Axes color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                <span  data-txt="txtAxes">&nbsp;</span> 
                <span class="tooltiptext2" data-txt="txttooltipAxes">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetstateaxes4" value="#666666">
            </div>  
             
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <!-- Grid color -->
            <div class="workbench-graph-color-setting-wrapper">
              <div class="workbench-graph-color-text tooltip">
                  <span  data-txt="txtGrid">&nbsp;</span> 
                  <span class="tooltiptext2" data-txt="txttooltipGrid">&nbsp;</span>
              </div>
              <input class="workbench-graph-color" type="color" id="datasetstategrid4" value="#e5e5e5">
            </div> 
          
            <!-- Divider -->
            <div class="workbench-graph-controls-divider"></div>
            
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="1" id="updateStateGraph4" data-txt="txtSet"></button>
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="2" id="resetStateZoom4" data-txt="txtResetZoom"></button>
            <button class="workbench-graph-axis-paramether-update-button" grid-pos="3" id="resetStateGraph4" data-txt="txtGraphDefault"></button>
        <!-- End of state controls -->
        </div>
        
      <!-- divider -->
      <div class="workbench-header-divider" furuta="true" ></div>
      
      <!-- ************************************** ACTION GRAPH ************************************* -->
      <div class="workbench-graph-container-wrapper" id="actionGraphScroll" furuta="true" big="FALSE">
      
        <!-- Action Graph header -->
        <div class="workbench-graph-header" furuta="true">
          <div class="workbench-graph-header-txt" data-txt="txtActionGraph"></div>
       
          <div class="workbench-graph-header-hide-button-wrapper">
            <div class="workbench-graph-header-hide-button-text tooltip">
              <span  data-txt="txtControls">&nbsp;</span> 
              <span class="tooltiptext3" data-txt="txttooltipControls">&nbsp;</span>
            </div>
            <div class="workbench-graph-header-hide-button">
              <label class="workbench-graph-header-hide-button-switch">
                <input type="checkbox" id="action-graph-controls-button" checked>
                <span class="workbench-graph-header-hide-button-slider"></span>
              </label>
            </div>
          </div>
       
        </div>
        
        <!-- Action Graph  -->
        <div class="workbench-graph-container" id="actionGraph" furuta="true" big="FALSE">  
          <canvas id="experimentGraph2" class="experimentGraph" width="8000px"></canvas>
        </div>
      </div>
        
        <!-- Action Graph controls -->
        <div class="workbench-graph-controls" id="action-graph-controls-container" furuta="true" hide="true">
        
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
      <div class="workbench-header-divider" furuta="true" ></div>
    
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




