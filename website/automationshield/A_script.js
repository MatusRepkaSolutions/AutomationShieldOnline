document.addEventListener('DOMContentLoaded', function () {
    // =============================
    // Global Variables
    // =============================
    let translations = {}; // Stores translations from JSON
    let currentLanguage = 'svk'; // Default language
    let isNightMode = false; // Default mode
    const cookieBottomButton = document.getElementById('cookie-footbar-button'); //show cookie policy 


    // =============================
    // reset cookies button
    // =============================

if (cookieBottomButton) {
    cookieBottomButton.addEventListener('click', function () {
        // Clear cookies
        setCookie('cookiesAccepted', '', -1);
        setCookie('language', '', -1);
        setCookie('nightMode', '', -1);
        // Reinsert cookie banner
        insertCookieBanner();
    });
}




    // =============================
    // Initialization
    // =============================
    function initialize() {
        loadTranslations(); // Load translations first

        if (isNewUser()) {
            insertCookieBanner(); // Directly insert cookie banner instead of fetching it
        }
        attachEventListeners(); // Attach event listeners to UI elements
        loadPreferences(); // Ensure language selection and flag updates always work
    }
    
    function setVh() {
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
    }

    window.addEventListener('resize', setVh);
    window.addEventListener('orientationchange', setVh);
    setVh();

    function isNewUser() {
        return !getCookie('cookiesAccepted');
    }

    function insertCookieBanner() {
        // Directly insert the cookie banner into the page
        let cookieHTML = `
        <div id="cookie-backdrop" class="cookie-backdrop">
          <div class="cookie-wrapper">
            <div class="cookie-header"> Cookies </div>
            <div class="cookie-text" data-txt="txtCookie"></div>
            <div class="cookie-text2" data-txt="txtCookie2"></div>
            <div class="cookie-button-line">
              <button id="cookie-accept-essential" class="cookie-accept-essential" data-txt="txtCookieAcceptEssential"></button>
              <button id="cookie-accept-all" class="cookie-accept-all" data-txt="txtCookieAccept"></button>
            </div>
          </div>
        </div>`;

        document.body.insertAdjacentHTML('beforeend', cookieHTML);
        applyTranslations(); // Apply translations after inserting HTML
        attachCookieEventListeners();
    }

    function attachCookieEventListeners() {
        document.getElementById('cookie-accept-essential').addEventListener('click', function () {
            acceptCookies('essential');
        });

        document.getElementById('cookie-accept-all').addEventListener('click', function () {
            acceptCookies('all');
        });
    }

    function acceptCookies(type) {
        setCookie('cookiesAccepted', type, 30);
        document.getElementById('cookie-backdrop').style.display = 'none';

        if (type === 'all') {
            setCookie('language', currentLanguage, 30);
            setCookie('nightMode', isNightMode, 30);
        }
    }

    // =============================
    // Translation Functions
    // =============================
    function applyTranslations() {
        document.querySelectorAll('[data-txt]').forEach(element => {
            let key = element.getAttribute('data-txt');
            if (translations[key] && translations[key][currentLanguage]) {
                let radio = element.querySelector("input");
                let newText = document.createTextNode(translations[key][currentLanguage]);

                element.innerHTML = "";
                if (radio) element.appendChild(radio);
                element.appendChild(newText);
            }
        });
    }

    function loadTranslations() {
        $.getJSON('/automationshield/A_translations.json', function(data) {
            translations = data;
            applyTranslations();
        }).fail(function() {
            console.error("Failed to load translations JSON.");
        });
    }

    // =============================
    // Language Handling Functions
    // =============================
    function setLanguage(language) {
        currentLanguage = language;
        applyTranslations();
        updateFlag();
        if (getCookie('cookiesAccepted') === 'all') {
            setCookie('language', currentLanguage, 30);
        }
        // Hide the dropdown menu after selection
        document.querySelector('.topbar-language-menu').style.display = 'none';
    }

    function updateFlag() {
        const flagMap = {
            'svk': 'SlovakFlag.png',
            'eng': 'GreatBritainFlag.png',
            'ukr': 'UkrainianFlag.png'
        };
        let flagUrl = flagMap[currentLanguage] || 'SlovakFlag.png';
        document.getElementById('language-switch').style.backgroundImage = `url(https://mrsolutions.sk/automationshield/flags/${flagUrl})`;
    }

    function toggleLanguageMenu() {
        const menu = document.querySelector('.topbar-language-menu');
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }
    // =============================
    // Theme Mode Functions
    // =============================
    function toggleMode() {
        isNightMode = !isNightMode;
        toggleStylesheets();
        document.getElementById('light-mode-switch').querySelector('input[type="checkbox"]').checked = !isNightMode;
        setCookie('nightMode', isNightMode, 30);
    }

    function toggleStylesheets() {
    document.documentElement.style.opacity = "1"; // Hide page during transition

    // First, enable both stylesheets for a brief moment
    setTimeout(() => {
        // Enable both stylesheets
        document.getElementById('day-css').disabled = false;
        document.getElementById('night-css').disabled = false;
        document.getElementById('page-day-css').disabled = false;
        document.getElementById('page-night-css').disabled = false;

        // Short delay to allow both styles to apply (middle step)
        setTimeout(() => {
            // Now switch to the correct theme by disabling the other
            document.getElementById('day-css').disabled = isNightMode;
            document.getElementById('night-css').disabled = !isNightMode;
            document.getElementById('page-day-css').disabled = isNightMode;
            document.getElementById('page-night-css').disabled = !isNightMode;

            document.documentElement.style.opacity = "1"; // Show page after styles are applied
        }, 30); // Delay between middle step and final switch (adjust as needed)
    }, 10); // Initial delay before applying styles
}

    // =============================
    // Load User Preferences
    // =============================
    function loadPreferences() {
        if (getCookie('cookiesAccepted') === 'all') {
            let languageCookie = getCookie('language');
            if (languageCookie) currentLanguage = languageCookie;
            updateFlag();

            let nightModeCookie = getCookie('nightMode');
            isNightMode = (nightModeCookie === 'true');
        } else {
            currentLanguage = 'svk';
            isNightMode = false;
        }

        applyMode();
    }

    function applyMode() {
        document.getElementById('day-css').disabled = isNightMode;
        document.getElementById('night-css').disabled = !isNightMode;
        document.getElementById('page-day-css').disabled = isNightMode;
        document.getElementById('page-night-css').disabled = !isNightMode;

        if (document.getElementById('workbench-day-css') && document.getElementById('workbench-night-css')) {
            document.getElementById('workbench-day-css').disabled = isNightMode;
            document.getElementById('workbench-night-css').disabled = !isNightMode;
        }
        document.getElementById('light-mode-switch').querySelector('input[type="checkbox"]').checked = !isNightMode;
    }

    // =============================
    // Cookie Management
    // =============================
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        let nameEQ = name + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
        }
        return null;
    }

    // =============================
    // Event Listeners
    // =============================
    function attachEventListeners() {
        document.getElementById('language-switch').addEventListener('click', toggleLanguageMenu);
        document.querySelectorAll('.topbar-language-menu-line').forEach(flag => {
            flag.addEventListener('click', function() {
                const language = this.id.split('-')[0];
                setLanguage(language);
            });
        });

        document.getElementById('light-mode-switch').querySelector('input[type="checkbox"]').addEventListener('change', toggleMode);
    }


    // =============================
    // Start Script
    // =============================
    initialize();
});



