// Function to show loader and fetch data
function showLoaderAndFetchData() {
    // Set attributes to show loader background and loader, and hide data
    $('.arduinos-data').attr('IsLoading', 'True'); // Hide data container
    $('.loader-background').attr('IsLoading', 'True'); // Show loader background

    setTimeout(() => {
        fetchData(); // Fetch data after a delay
    }, 2000); // Adjust delay as needed
}

// Function to fetch and display data
function fetchData() {
    $.ajax({
        url: 'index_fetch_arduinos.php', // URL to the PHP file
        method: 'GET',
        success: function(data) {
            // Remove existing Arduino lines, but keep the "No Arduinos Available" message element
            $('#arduinos-data').find('.Arduinos-line').remove();
            
            // Append fetched data (Arduino lines)
            $('#arduinos-data').append(data);

            // Check if data contains any Arduino lines
            if ($('#arduinos-data').find('.Arduinos-line').length === 0) {
                $('.Arduinos-line-no-data').attr('data-IsAvailable', 'false'); // Show message
            } else {
                $('.Arduinos-line-no-data').attr('data-IsAvailable', 'true'); // Hide message
            }

            // Hide loader and show data container
            $('.arduinos-data').attr('IsLoading', 'False'); // Show data
            $('.loader-background').attr('IsLoading', 'False'); // Hide loader background

            applyTranslations(); // Apply translations after loading content
        },
        error: function() {
            $('#arduinos-data').html('<div class="Arduinos-line">Error fetching data</div>');

            // Hide loader in case of error
            $('.arduinos-data').attr('IsLoading', 'False'); // Show data
            $('.loader-background').attr('IsLoading', 'False'); // Hide loader background
        }
    });
}

// Document ready function for fetching data and setting interval
$(document).ready(function() {
    // Initial fetch
    fetchData();

    // Set up automatic refresh every 15 seconds
    refreshInterval = setInterval(fetchData, 15000);

    // Refresh button click event
    $('#refresh-button').click(function() {
        clearInterval(refreshInterval); // Clear the existing interval
        showLoaderAndFetchData(); // Show loader and fetch new data
        refreshInterval = setInterval(fetchData, 15000); // Reset the interval
    });
});

