<form class="how-to-tab-button-wrapper-online" >
        <label class="how-to-tab-button-online" data-txt="txtHowToOnline" >
          <input type="radio" name="how-to-tab" value="arduino" id="how-to-online" checked hidden>
          Online
        </label>
        <div class="how-to-tab-button-divider"></div>
        <label class="how-to-tab-button-online" data-txt="txtHowToOffline">
          <input type="radio" name="how-to-tab" value="video" id="how-to-offline" hidden>
          Offline
        </label>
        <div class="how-to-tab-button-divider"></div>
        <label class="how-to-tab-button-online" data-txt="txtHowToPassword">
          <input type="radio" name="how-to-tab" value="library" id="how-to-login" hidden>
          Heslo
        </label>
        <div class="how-to-tab-button-divider"></div>
        <label class="how-to-tab-button-online" data-txt="txtHowToDebug">
          <input type="radio" name="how-to-tab" value="debug" id="how-to-debug" hidden>
          Debug
        </label>
        <div class="how-to-tab-button-divider"></div>
        <label class="how-to-tab-button-online" data-txt="txtHowToMPC">
          <input type="radio" name="how-to-tab" value="online" id="how-to-MPC" hidden>
          MPC
        </label>
        <div class="how-to-tab-button-divider"></div>
        <label class="how-to-tab-button-online" data-txt="txtHowToGUI">
          <input type="radio" name="how-to-tab" value="video" id="how-to-GUI" hidden>
          GUI
        </label>
        <div class="how-to-tab-button-divider"></div>
        <label class="how-to-tab-button-online" data-txt="txtHowToData">
          <input type="radio" name="how-to-tab" value="video" id="how-to-data" hidden>
          Data
        </label>
      </form>
      <!--********************************** ONLINE *******************************-->
      <div class="how-to-content-wrapper" id="how-to-online-wrapper" show="true">
        
        <?php include 'online.php' ?>

      </div>
      <!--********************************** OFFLINE *******************************-->
      <div class="how-to-content-wrapper" id="how-to-offline-wrapper" show="false">
        
        <?php include 'offline.php' ?>

      </div>
      <!--********************************** HESLO *******************************-->
      <div class="how-to-content-wrapper" id="how-to-heslo-wrapper" show="false">
        
        <?php include 'password.php' ?>

      </div>

      <!--********************************** MPC *******************************-->
      <div class="how-to-content-wrapper" id="how-to-MPC-wrapper" show="false">
       
        <?php include 'mpc.php' ?>
          
      </div>

      <!--********************************** GUI *******************************-->
      <div class="how-to-content-wrapper" id="how-to-GUI-wrapper" show="false">
        
        <?php include 'gui.php' ?>

      </div>
      <!--********************************** DATA *******************************-->
      <div class="how-to-content-wrapper" id="how-to-data-wrapper" show="false">
        
        <?php include 'data.php' ?>

      </div>
      <!--********************************** Debug *******************************-->
      <div class="how-to-content-wrapper" id="how-to-debug-wrapper" show="false">
         
        <?php include 'debug.php' ?>

      </div>