$(document).ready(function() {
    // Handle login form submission
    $('#login-form').on('submit', function(event) {
        // Show the loader when form is submitted
        const loader = $('.arduino-login-loader');
        const loginInfo = $('#login-arduino-informations');

        // Change the visibility to show loader and hide form
        loginInfo.attr('data-login-wait', 'true');
        loader.attr('data-login-wait', 'true');
        
        // Let the form be submitted normally after showing the loader
    });
});

