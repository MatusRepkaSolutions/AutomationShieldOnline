document.addEventListener('DOMContentLoaded', function () {

    // Share buttons
    const shareArduino = document.getElementById("share-arduino");
    const shareLibrary = document.getElementById("share-library");
    const shareOnline = document.getElementById("share-online");
    const shareOffline = document.getElementById("share-offline");
    const sharePassword = document.getElementById("share-password");
    const shareDebug = document.getElementById("share-debug");
    const shareMPC = document.getElementById("share-mpc");
    const shareGUI = document.getElementById("share-gui");
    const shareData = document.getElementById("share-data");
    const shareSVK = document.getElementById("share-SVK");
    const shareENG = document.getElementById("share-ENG");

    // Radio buttons
    const arduino = document.getElementById("how-to-arduino");
    const library = document.getElementById("how-to-library");
    const AutomationShield = document.getElementById("how-to-AutomationShield");
    const video = document.getElementById("how-to-video");

    // Radio buttons 2
    const online = document.getElementById("how-to-online");
    const offline = document.getElementById("how-to-offline");
    const login = document.getElementById("how-to-login");
    const MPC = document.getElementById("how-to-MPC");
    const GUI = document.getElementById("how-to-GUI");
    const data = document.getElementById("how-to-data");
    const debug = document.getElementById("how-to-debug");

    // Radio buttons 3
    const SVKvid = document.getElementById("how-to-SVKvid");
    const ENGvid = document.getElementById("how-to-ENGvid");

    // Tutorial wrappers
    const arduinoGuide = document.getElementById("how-to-arduino-wrapper");
    const libraryGuide = document.getElementById("how-to-library-wrapper");
    const AutomationShieldGuide = document.getElementById("how-to-AutomationShield-wrapper");
    const videoGuide = document.getElementById("how-to-video-wrapper");

    // Tutorial wrappers 2
    const onlinewrap = document.getElementById("how-to-online-wrapper");
    const offlinewrap = document.getElementById("how-to-offline-wrapper");
    const heslowrap = document.getElementById("how-to-heslo-wrapper");
    const MPCwrap = document.getElementById("how-to-MPC-wrapper");
    const GUIwrap = document.getElementById("how-to-GUI-wrapper");
    const datawrap = document.getElementById("how-to-data-wrapper");
    const debugwrap = document.getElementById("how-to-debug-wrapper");

    // Tutorial wrappers 3
    const engwrap = document.getElementById("how-to-ENGvid-wrapper");
    const svkwrap = document.getElementById("how-to-SVKvid-wrapper");

 
    // --- HANDLE URL PARAMETERS ---
    const urlParams = new URLSearchParams(window.location.search);
    const mainTab = urlParams.get('tab');
    const subTab = urlParams.get('sub');

    // Function to update visibility based on selected radio button
    function displayFunction(selectedRadio) {
        // Hide all guides
        arduinoGuide.setAttribute("show", "false");
        libraryGuide.setAttribute("show", "false");
        AutomationShieldGuide.setAttribute("show", "false");
        videoGuide.setAttribute("show", "false");
        stopVideo(engwrap);
        stopVideo(svkwrap);

        // Show the selected guide
        if (selectedRadio === arduino) arduinoGuide.setAttribute("show", "true");
        if (selectedRadio === library) libraryGuide.setAttribute("show", "true");
        if (selectedRadio === AutomationShield ){ 
            AutomationShieldGuide.setAttribute("show", "true");
            if (!subTab) { // ⬅️ pridaj túto podmienku
                online.checked = true;
                displayFunction2(online);
            }
        }
        if (selectedRadio === video) videoGuide.setAttribute("show", "true");
    }

    // Function to update visibility based on selected radio button
    function displayFunction2(selectedRadio2) {
        // Hide all guides
        onlinewrap.setAttribute("show", "false");
        offlinewrap.setAttribute("show", "false");
        heslowrap.setAttribute("show", "false");
        MPCwrap.setAttribute("show", "false");
        GUIwrap.setAttribute("show", "false");
        datawrap.setAttribute("show", "false");
        debugwrap.setAttribute("show", "false");
        stopVideo(engwrap);
        stopVideo(svkwrap);

        // Show the selected guide
        if (selectedRadio2 === online) onlinewrap.setAttribute("show", "true");
        if (selectedRadio2 === offline) offlinewrap.setAttribute("show", "true");
        if (selectedRadio2 === login) heslowrap.setAttribute("show", "true");
        if (selectedRadio2 === MPC) MPCwrap.setAttribute("show", "true");
        if (selectedRadio2 === GUI) GUIwrap.setAttribute("show", "true");
        if (selectedRadio2 === data) datawrap.setAttribute("show", "true");
        if (selectedRadio2 === debug) debugwrap.setAttribute("show", "true");
    }

    // Function to update visibility based on selected radio 3 button
    function displayFunction3(selectedRadio3) {
        // Hide all guides
        engwrap.setAttribute("show", "false");
        svkwrap.setAttribute("show", "false");
        stopVideo(engwrap);
        stopVideo(svkwrap);

        // Show the selected guide
        if (selectedRadio3 === SVKvid) svkwrap.setAttribute("show", "true");
        if (selectedRadio3 === ENGvid) engwrap.setAttribute("show", "true");
    }

    // Share link function

    function shareLinkFunction(shareButton){

        const message = shareButton.querySelector(".share-copied");
        message.setAttribute("show","true");

        let url = '';
        if(shareButton === shareArduino){
            url = `https://mrsolutions.sk/automationshield/how-to.php?tab=arduino`;
        }
        if(shareButton === shareLibrary){
            url = `https://mrsolutions.sk/automationshield/how-to.php?tab=library`;
        }
        if(shareButton === shareOnline){
            url = `https://mrsolutions.sk/automationshield/how-to.php?tab=AutomationShield&sub=online`;
        }
        if(shareButton === shareOffline){
            url = `https://mrsolutions.sk/automationshield/how-to.php?tab=AutomationShield&sub=offline`;
        }
        if(shareButton === sharePassword){
            url = `https://mrsolutions.sk/automationshield/how-to.php?tab=AutomationShield&sub=login`;
        }
        if(shareButton === shareDebug){
            url = `https://mrsolutions.sk/automationshield/how-to.php?tab=AutomationShield&sub=debug`;
        }
        if(shareButton === shareMPC){
            url = `https://mrsolutions.sk/automationshield/how-to.php?tab=AutomationShield&sub=MPC`;
        }
        if(shareButton === shareGUI){
            url = `https://mrsolutions.sk/automationshield/how-to.php?tab=AutomationShield&sub=GUI`;
        }
        if(shareButton === shareData){
            url = `https://mrsolutions.sk/automationshield/how-to.php?tab=AutomationShield&sub=data`;
        }
        if(shareButton === shareSVK){
            url = `https://mrsolutions.sk/automationshield/how-to.php?tab=video&sub=SVKvid`;
        }
        if(shareButton === shareENG){
            url = `https://mrsolutions.sk/automationshield/how-to.php?tab=video&sub=ENGvid`;
        }

        // Copy to clipboard
        navigator.clipboard.writeText(url);

        setTimeout(() => {
            message.style.transition = "opacity 0.5s ease";
            message.style.opacity = "0";
            
            // Optional: hide element after fading out
            setTimeout(() => {
                message.setAttribute("show", "false");
                message.style.opacity = ""; // reset for future uses
                message.style.transition = ""; // reset transition
            }, 500); // match fade duration
        }, 1000);
    }

    // Attach event listeners
    [arduino, library, AutomationShield , video].forEach(radio => {
        if (radio) radio.addEventListener('change', function () { displayFunction(this); });
    });
    // Attach event listeners 2
    [online, offline, login, MPC, GUI, data, debug].forEach(radio => {
        if (radio) radio.addEventListener('change', function () { displayFunction2(this); });
    });

    // Attach event listeners 3
    [SVKvid, ENGvid].forEach(radio => {
        if (radio) radio.addEventListener('change', function () { displayFunction3(this); });
    });

    // Attach event listeners for share buttons
    [shareArduino, shareLibrary, shareOnline, shareOffline, sharePassword, shareDebug, shareMPC, shareGUI, shareData, shareSVK, shareENG].forEach(button => {
        button.addEventListener('click', function () {
            shareLinkFunction(this);
        });
    });

    if (mainTab) {
        const mainRadio = document.getElementById(`how-to-${mainTab}`);
        if (mainRadio) {
            mainRadio.checked = true;
            displayFunction(mainRadio);
        }
    }

    if (subTab) {
        const subRadio = document.getElementById(`how-to-${subTab}`);

        if (subTab === "ENGvid" || subTab === "SVKvid") {
            if (subRadio) {
                subRadio.checked = true;
                displayFunction3(subRadio);
            }
        } else {
            if (subRadio) {
                subRadio.checked = true;
                displayFunction2(subRadio);
            }
        }
    }


    function stopVideo(wrapper) {
        const iframe = wrapper.querySelector('iframe');
        if (iframe) {
            const src = iframe.src;
            iframe.src = src; // Resetting the src stops the video
        }
    }

    // Run displayFunction once on page load to set initial state
    displayFunction(document.querySelector('input[name="how-to-tab"]:checked'));
});



