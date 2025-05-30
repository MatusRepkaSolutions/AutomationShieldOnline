document.addEventListener('DOMContentLoaded', function () {
    // **********************************************************************************
    // ************************** ELEMENT SELECTION *************************************
    
    // Control panel menu
    const selectElement = document.querySelector('.side-panel-select select');
    
    // Graph canvases
    const ctx1 = document.getElementById('experimentGraph').getContext('2d');
    const ctx2 = document.getElementById('experimentGraph2').getContext('2d');
    
    // Legend containers
    const stateLegendContainer = document.getElementById('stateLegend');
    const actionLegendContainer = document.getElementById('actionLegend');
   
    // Graph settings elements
    const colorPicker = document.getElementById('datasetUColor');
    const lineTypeSelect = document.getElementById('datasetULineType');
    const lineWidthSelect = document.getElementById('datasetULineWidth'); 
    const updateActionGraphBtn = document.getElementById('updateActionGraph');
    const updateStateGraphBtn = document.getElementById('updateStateGraph');

    // Graph size checkboxes
    const stateGraphCheckbox = document.getElementById("state-graph-size");
    const actionGraphCheckbox = document.getElementById("action-graph-size");
    const stateGraphControlsCheckbox = document.getElementById("state-graph-controls-button");
    const actionGraphControlsCheckbox = document.getElementById("action-graph-controls-button");
    const stateGraphContainer = document.getElementById("stateGraph");
    const actionGraphContainer = document.getElementById("actionGraph");
    const stateGraphScroll = document.getElementById("stateGraphScroll");
    const actionGraphscroll = document.getElementById("actionGraphScroll");
    const stateGraphControlsContainer = document.getElementById("state-graph-controls-container");
    const actionGraphControlsContainer = document.getElementById("action-graph-controls-container");
    
    // Reset zoom
    const resetActionZoom = document.getElementById("resetActionZoom");
    const resetStateZoom = document.getElementById("resetStateZoom");

    // Reset graph
    const resetActionGraphbtn = document.getElementById("resetActionGraph");
    const resetStateGraphbtn = document.getElementById("resetStateGraph");

    // AJAX logic for forms submission
    const pidForm = document.getElementById('PID-experiment-form');
    const openloopForm = document.getElementById('OPENLOOP-experiment-form');
    const LQIForm = document.getElementById('LQI-experiment-form');
    const EMPCForm = document.getElementById('EMPC-experiment-form');
    const LQImanForm = document.getElementById('LQIman-experiment-form');
    const EMPCmanForm = document.getElementById('EMPCman-experiment-form');
    const identificationForm = document.getElementById('IDENTIFICATION-experiment-form');
    
    // Get the ID from the hidden input field with ID "hiddenID"
    const idInput = document.getElementById('hiddenID');
    const id = idInput ? idInput.value : null;
    console.log("Fetched ID:", id); // Debugging: Check if ID is correctly fetched

    // *************************************************************************************
    // ***************************** SIDE BAR **********************************************

   // Get all experiment forms
   const forms = {
    'PID': document.getElementById('PID-experiment-form'),
    'EMPC': document.getElementById('EMPC-experiment-form'),
    'Identification': document.getElementById('IDENTIFICATION-experiment-form'),
    'LQI': document.getElementById('LQI-experiment-form'),
    'LQIman': document.getElementById('LQIman-experiment-form'),
    'EMPCman': document.getElementById('EMPCman-experiment-form'),
    'OpenLoop': document.getElementById('OPENLOOP-experiment-form')
};

    // *************************************************************************************
    // ***************************** Display only chosen form ******************************
function hideAllForms() {
    for (let form in forms) {
        if (forms[form]) {
            forms[form].style.display = 'none';  // Hide all forms
        }
    }
}

// Event listener for select change
selectElement.addEventListener('change', function () {
    // Hide all forms
    hideAllForms();
    
    // Show the selected form with flex display
    const selectedForm = selectElement.value;
    if (forms[selectedForm]) {
        forms[selectedForm].style.display = 'flex';  // Set display to flex
    }
});
    // *************************************************************************************
    // ***************************** Submit experiment *************************************
     function submitFunction(event, form) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(form);

        // Append ID from the hidden field if not already in the form
        if (!formData.has('id') && id) {
            formData.append('id', id);
        }

        fetch('MagnetoShield_update.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
              document.querySelectorAll("#AlertDataSuccess").forEach((el) => {
                el.setAttribute("show", "true");
                setTimeout(() => {
                el.setAttribute("show", "false");
                }, 5000);
              });
          } else {
              document.querySelectorAll("#SomethingWrong").forEach((el) => {
                el.setAttribute("show", "true");
                setTimeout(() => {
                el.setAttribute("show", "false");
                }, 5000);
              });
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // *************************************************************************************
    // ***************************** Attach Event Listeners ********************************
    
    if (pidForm) pidForm.addEventListener('submit', function (e) { submitFunction(e, this); });
    if (openloopForm) openloopForm.addEventListener('submit', function (e) { submitFunction(e, this); });
    if (LQIForm) LQIForm.addEventListener('submit', function (e) { submitFunction(e, this); });
    if (EMPCForm) EMPCForm.addEventListener('submit', function (e) { submitFunction(e, this); });
    if (LQImanForm) LQImanForm.addEventListener('submit', function (e) { submitFunction(e, this); });
    if (EMPCmanForm) EMPCmanForm.addEventListener('submit', function (e) { submitFunction(e, this); });
    if (identificationForm) identificationForm.addEventListener('submit', function (e) { submitFunction(e, this); });
   
  // *************************************************************************************
  // ***************************** DISCONNECT CHECK **************************************
    let shieldWrapper = document.getElementById("Shield-disconnected-wrapper");
    
    function checkShieldStatus() {
        if (!id) return;
    
        fetch(`MagnetoShield_DataCheck.php?id=${encodeURIComponent(id)}`)
            .then(response => response.json())
            .then(data => {
                if (data.disconnected) {
                    console.log("Shield is disconnected");
                    shieldWrapper.setAttribute("disconnected", "true");
                } else {
                    console.log("Shield is connected");
                    shieldWrapper.setAttribute("disconnected", "false");
                }
            })
            .catch(error => console.error("Error checking shield status:", error));
    }
    
// ********************************************************************************************
// ***************************** GRAPHS SETTINGS **********************************************

    
// Chart.js graph
let lastUpdateTime = performance.now(); // Track last update time

const previousY = (ctx) => {
    if (ctx.index === 0) {
        return ctx.chart.scales.y.getPixelForValue(100); // Default start for y
    }
    const meta = ctx.chart.getDatasetMeta(ctx.datasetIndex);
    if (!meta || !meta.data || !meta.data[ctx.index - 1]) {
        return ctx.chart.scales.y.getPixelForValue(100);
    }
    return meta.data[ctx.index - 1].getProps(['y'], true).y;
};

const previousU = (ctx) => {
    if (ctx.index === 0) {
        return ctx.chart.scales.y.getPixelForValue(0); // Default start for u
    }
    const meta = ctx.chart.getDatasetMeta(ctx.datasetIndex);
    if (!meta || !meta.data || !meta.data[ctx.index - 1]) {
        return ctx.chart.scales.y.getPixelForValue(0);
    }
    return meta.data[ctx.index - 1].getProps(['y'], true).y; // Use 'y' since 'u' is stored in the same structure
};

const experimentGraph = new Chart(ctx1, {
    type: 'line',
    data: {
        labels: [], // X-axis (time)
        datasets: [
            { 
                label: 'y', 
                data: [], 
                borderColor: 'blue', 
                fill: false, 
                pointRadius: 0 // No circles for y
            },
            { 
                label: 'r', 
                data: [], 
                borderColor: 'red', 
                fill: false, 
                borderDash: [10, 5], // Dashed line for r
                pointRadius: 0 // No circles for r
            }
        ]
    },
    options: {
        animation: {
            x: {
                type: 'number',
                easing: 'linear',
                duration: 0,
                from: NaN, // Skip initial point
                delay(ctx) {
                    if (ctx.type !== 'data' || ctx.xStarted) {
                        return 0;
                    }
                    ctx.xStarted = true;
                    return 0;
                }
            },
            y: {
                type: 'number',
                easing: 'linear',
                duration: 0,
                from: previousY,
                delay(ctx) {
                    if (ctx.type !== 'data' || ctx.yStarted) {
                        return 0;
                    }
                    ctx.xStarted = true;
                    return 0;
                }
            }
        },
        responsive: true,
        maintainAspectRatio: false, // Prevent fixed aspect ratio
        scales: {
            x: {
                title: { display: true, text: 'Time (s)' }, // X-axis in seconds
                ticks: {
                    maxRotation: 0, // Prevent tick label rotation
                    autoSkip: true,
                    maxTicksLimit: 20
                }
            },
            y: {
                title: { display: true, text: 'Altitude (%)' },
                ticks: {
                    beginAtZero: false, // Let it adapt but with a limit
                },
                afterDataLimits: (scale) => {
                    const minY = scale.min;
                    const maxY = scale.max;
                    const range = maxY - minY;

                    // Ensure a minimum spread of 5 units
                    if (range < 5) {
                        const center = (minY + maxY) / 2;
                        scale.min = center - 2.5;
                        scale.max = center + 2.5;
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false,
                labels: {
                    color: 'rgb(255, 99, 132)'
                }
            },
            tooltip: {
                enabled: true
            },
            zoom: {
                zoom: {
                    wheel: {
                        enabled: true // Enable zooming with mouse scroll
                    },
                    pinch: {
                        enabled: true // Enable zooming with touch gestures
                    },
                    mode: 'xy' // Zoom in both X and Y directions
                }
            }
        },
        layout: {
            padding: {
                top: 10,
                bottom: 10,
                left: 0,
                right: 0
            }
        }
    }
});
const experimentGraph2 = new Chart(ctx2, {
    type: 'line',
    data: {
        labels: [], // X-axis (time)
        datasets: [
            { 
                label: 'u', 
                data: [], 
                borderColor: 'purple', 
                fill: false, 
                pointRadius: 0 
            }
        ]
    },
    options: {
        animation: {
            x: {
                type: 'number',
                easing: 'linear',
                duration: 0,
                from: NaN, // Skip initial point
                delay(ctx) {
                    if (ctx.type !== 'data' || ctx.xStarted) {
                        return 0;
                    }
                    ctx.xStarted = true;
                    return 0;
                }
            },
            y: {
                type: 'number',
                easing: 'linear',
                duration: 0,
                from: previousU, // Use previousU instead of previousY
                delay(ctx) {
                    if (ctx.type !== 'data' || ctx.yStarted) {
                        return 0;
                    }
                    ctx.xStarted = true;
                    return 0;
                }
            }
        },
        responsive: true,
        maintainAspectRatio: false, // Prevent fixed aspect ratio
        scales: {
            x: {
                title: { display: true, text: 'Time (s)' },
                ticks: {
                    maxRotation: 0,
                    autoSkip: true,
                    maxTicksLimit: 20
                }
            },
            y: {
                title: { display: true, text: 'Motor power (V)' },
                ticks: {
                    beginAtZero: false
                },
                afterDataLimits: (scale) => {
                    const minY = scale.min;
                    const maxY = scale.max;
                    const range = maxY - minY;

                    if (range < 5) {
                        const center = (minY + maxY) / 2;
                        scale.min = center - 2;
                        scale.max = center + 1;
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false,
                labels: {
                    color: 'rgb(255, 99, 132)'
                }
            },
            tooltip: {
                enabled: true
            },
            zoom: {
                zoom: {
                    wheel: {
                        enabled: true
                    },
                    pinch: {
                        enabled: true
                    },
                    mode: 'xy'
                }
            }
        },
        layout: {
            padding: {
                top: 10,
                bottom: 10,
                left: 0,
                right: 0
            }
        }
    }
});


// *********************************************************************************************
// ***************************** FETCH GRAPH DATA **********************************************

let detailViewEnabled1 = false; // state graph detail
let detailViewEnabled2 = false; // action graph detail

// Helper function to round numbers to the nearest 0.01
function roundToStep(value, step = 0.01) {
    return (Math.round(value / step) * step).toFixed(2);
}

// Function to fetch data from the server and update the graph
let previousDataLength = 0;                 // Store the last known data length
let fetchInterval = 1000;                   // Default interval
let intervalId;                             // Store the interval ID
let zeroDataCount = 0; // Track consecutive zero fetches
let experimentStarted = false; // Track if experiment has started

function startExperiment() {
    experimentStarted = true; // Mark experiment as started
    zeroDataCount = 0; // Reset counter on start
    console.log("Experiment started, monitoring data...");
}

// Attach event listeners to form submissions
if (pidForm) pidForm.addEventListener('submit', function (e) { submitFunction(e, this); startExperiment(); });
if (openloopForm) openloopForm.addEventListener('submit', function (e) { submitFunction(e, this); startExperiment(); });
if (LQIForm) LQIForm.addEventListener('submit', function (e) { submitFunction(e, this); startExperiment(); });
if (EMPCForm) EMPCForm.addEventListener('submit', function (e) { submitFunction(e, this); startExperiment(); });
if (LQImanForm) LQImanForm.addEventListener('submit', function (e) { submitFunction(e, this); startExperiment(); });
if (EMPCmanForm) EMPCmanForm.addEventListener('submit', function (e) { submitFunction(e, this); startExperiment(); });
if (identificationForm) identificationForm.addEventListener('submit', function (e) { submitFunction(e, this); startExperiment(); });

async function fetchDataAndUpdateGraph() {
    try {
        const response = await fetch(`MagnetoShield_fetch.php?id=${encodeURIComponent(id)}`);
        const dataFromPHP = await response.json();

        console.log('Response from PHP:', dataFromPHP); // Debugging

        if (dataFromPHP.error) {
            console.error(dataFromPHP.error);
            return;
        }

        const currentDataLength = dataFromPHP.length;

        // Only check for zero data if experiment has started
        if (experimentStarted) {
            if (currentDataLength === 0) {
                zeroDataCount++;
            } else {
                zeroDataCount = 0; // Reset counter if data is received
            }

            // Show warning if zero data is received 5 times in a row
            document.querySelectorAll("#GraphNoData").forEach((el) => {
                el.setAttribute("show", zeroDataCount >= 6 ? "true" : "false");
            });
        }

        // Adjust fetch interval dynamically
        fetchInterval = (currentDataLength !== previousDataLength) ? 500 : 2000;
        previousDataLength = currentDataLength;
        resetIntervals(); // Restart the interval with the new time

        const interval = 5; // 5ms per row
        const hasR = dataFromPHP.length > 0 && dataFromPHP[0].r !== null;

        // Keep only the last 1000 points if detail mode is on
        const limitedData1 = detailViewEnabled1 ? dataFromPHP.slice(-1000) : dataFromPHP;
        const limitedData2 = detailViewEnabled2 ? dataFromPHP.slice(-1000) : dataFromPHP;

        // Update graph data
        experimentGraph.data.labels = limitedData1.map((_, index) => ((index * interval) / 1000).toFixed(2));
        experimentGraph.data.datasets[0].data = limitedData1.map(row => row.y);

        if (hasR) {
            experimentGraph.data.datasets[1].data = limitedData1.map(row => row.r);
        }

        experimentGraph2.data.labels = limitedData2.map((_, index) => ((index * interval) / 1000).toFixed(2));
        experimentGraph2.data.datasets[0].data = limitedData2.map(row => row.u);

        // Refresh both graphs
        experimentGraph.update();
        experimentGraph2.update();
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}



// ************************************************************************************************
// ***************************** GRAPH CUSTOMIZATION **********************************************
// ************************************************************************************************
// ************************************************************************************************

// *********************************************************************************************
// ***************************** GENERATE LEGENDS **********************************************

function generateStateLegend() {
    stateLegendContainer.innerHTML = '';
    experimentGraph.data.datasets.forEach((dataset, index) => {
        const legendItem = document.createElement('div');
        legendItem.innerHTML = `<span style="display: inline-block; width: 12px; height: 12px; background-color: ${dataset.borderColor}; margin-right: 5px; cursor: pointer;"></span> ${dataset.label}`;
        legendItem.addEventListener('click', () => {
            dataset.hidden = !dataset.hidden;
            experimentGraph.update();
        });
        stateLegendContainer.appendChild(legendItem);
    });
}

function generateActionLegend() {
    actionLegendContainer.innerHTML = '';
    experimentGraph2.data.datasets.forEach((dataset, index) => {
        const legendItem = document.createElement('div');
        legendItem.innerHTML = `<span style="display: inline-block; width: 12px; height: 12px; background-color: ${dataset.borderColor}; margin-right: 5px; cursor: pointer;"></span> ${dataset.label}`;
        legendItem.addEventListener('click', () => {
            dataset.hidden = !dataset.hidden;
            experimentGraph2.update();
        });
        actionLegendContainer.appendChild(legendItem);
    });
}

// Function to toggle the detail attribute and scroll right
function toggleDetail(graphType, checkbox) {
    if (graphType === "state") {
        detailViewEnabled1 = checkbox.checked;
    } else if (graphType === "action") {
        detailViewEnabled2 = checkbox.checked;
    }
    fetchDataAndUpdateGraph(); // Ensure both graphs refresh properly
}
// Function to toggle the detail attribute and scroll right
function toggleSize(checkbox, graphContainer, scrollContainer, controlsContainer) {
    if (checkbox.checked) {
        graphContainer.setAttribute("big", "true");
        scrollContainer.setAttribute("big", "true");
        controlsContainer.setAttribute("hide", "true");
    } else {
        graphContainer.setAttribute("big", "false");
        scrollContainer.setAttribute("big", "false");
        controlsContainer.setAttribute("hide", "false");
    }
}

    // Attach event listeners to checkboxes
    stateGraphCheckbox.addEventListener("change", function () {
        toggleDetail("state", stateGraphCheckbox);
    });
    actionGraphCheckbox.addEventListener("change", function () {
        toggleDetail("action", actionGraphCheckbox);
    });
    
    if (stateGraphControlsCheckbox && stateGraphContainer) {
        stateGraphControlsCheckbox.addEventListener("change", function () {
            toggleSize(stateGraphControlsCheckbox, stateGraphContainer, stateGraphScroll,stateGraphControlsContainer);
        });
    }

    if (actionGraphControlsCheckbox && actionGraphContainer) {
        actionGraphControlsCheckbox.addEventListener("change", function () {
            toggleSize(actionGraphControlsCheckbox, actionGraphContainer, actionGraphScroll,actionGraphControlsContainer);
        });
    }


// *************************************************************************************
// ***************************** UPDATE GRAPHS **********************************************
// Update state graph
function updateStateGraph() {
     if (experimentGraph.data.datasets.length > 0) {
        const dataset = experimentGraph.data.datasets[0];

        // Get values from inputs
        const newColor = document.getElementById('datasetYColor').value;
        const newLineType = document.getElementById('datasetYLineType').value;
        const newLineWidth = parseInt(document.getElementById('datasetYLineWidth').value, 10) || 2;

        const graphColor = document.getElementById('datasetstatecolor').value;
        const axesColor = document.getElementById('datasetstateaxes').value;
        const gridColor = document.getElementById('datasetstategrid').value;

        // Apply new properties to dataset
        dataset.borderColor = newColor;
        dataset.borderWidth = newLineWidth;

        // Apply line type styles
        switch (newLineType) {
            case "dashed":
                dataset.borderDash = [10, 5];
                dataset.pointRadius = 0;
                break;
            case "dotted":
                dataset.borderDash = [2, 5];
                dataset.pointRadius = 0;
                break;
            case "pointradius":
                dataset.borderDash = [];
                dataset.pointRadius = 2;
                break;
            default:
                dataset.borderDash = [];
                dataset.pointRadius = 0;
                break;
        }

        // Check if dataset2 exists before modifying it
        if (experimentGraph.data.datasets.length > 1) {
            const dataset2 = experimentGraph.data.datasets[1];
            const newColor2 = document.getElementById('datasetRColor').value;
            const newLineType2 = document.getElementById('datasetRLineType').value;
            const newLineWidth2 = parseInt(document.getElementById('datasetRLineWidth').value, 10) || 2;

            dataset2.borderColor = newColor2;
            dataset2.borderWidth = newLineWidth2;

            // Apply line type styles to dataset2
            switch (newLineType2) {
                case "solid":
                    dataset2.borderDash = [];
                    dataset2.pointRadius = 0;
                    break;
                case "dotted":
                    dataset2.borderDash = [2, 5];
                    dataset2.pointRadius = 0;
                    break;
                case "pointradius":
                    dataset2.borderDash = [];
                    dataset2.pointRadius = 2;
                    break;
                default:
                    dataset2.borderDash = [10, 5];
                    dataset2.pointRadius = 0;
                    break;
            }
        }

        // Apply axes color
        experimentGraph.options.scales.x.title.color = axesColor;
        experimentGraph.options.scales.y.title.color = axesColor;
        experimentGraph.options.scales.x.ticks.color = axesColor;
        experimentGraph.options.scales.y.ticks.color = axesColor;

        // Apply grid color
        experimentGraph.options.scales.x.grid.color = gridColor;
        experimentGraph.options.scales.y.grid.color = gridColor;

        // Apply background color
        document.getElementById('stateGraph').style.backgroundColor = graphColor;

        // Refresh the chart
        experimentGraph.update();
        generateStateLegend();
    } else {
        console.error('Dataset not found in experimentGraph.');
    }
}

// Update action graph
function updateActionGraph() {
    if (experimentGraph2.data.datasets.length > 0) {
        const dataset = experimentGraph2.data.datasets[0];

        // Get values from inputs
        const newColor = document.getElementById('datasetUColor').value;
        const newLineType = document.getElementById('datasetULineType').value;
        const newLineWidth = parseInt(document.getElementById('datasetULineWidth').value, 10) || 2;

        const graphColor = document.getElementById('datasetactioncolor').value;
        const axesColor = document.getElementById('datasetactionaxes').value;
        const gridColor = document.getElementById('datasetactiongrid').value;

        // Apply new properties to the dataset
        dataset.borderColor = newColor;
        dataset.borderWidth = newLineWidth;

        // Apply line type styles
        switch (newLineType) {
            case "dashed":
                dataset.borderDash = [10, 5];
                dataset.pointRadius = 0;
                break;
            case "dotted":
                dataset.borderDash = [2, 5];
                dataset.pointRadius = 0;
                break;
            case "solid":
                dataset.borderDash = [];
                dataset.pointRadius = 0;
                break;
            default:
                dataset.borderDash = [];
                dataset.pointRadius = 2;
                break;
        }

        
        // Apply axes color
        experimentGraph2.options.scales.x.title.color = axesColor;
        experimentGraph2.options.scales.y.title.color = axesColor;
        experimentGraph2.options.scales.x.ticks.color = axesColor;
        experimentGraph2.options.scales.y.ticks.color = axesColor;
        
        // Apply grid color
        experimentGraph2.options.scales.x.grid.color = gridColor;
        experimentGraph2.options.scales.y.grid.color = gridColor;
        
        // Apply naclground color
        document.getElementById('actionGraph').style.backgroundColor = graphColor;

        // Refresh the chart
        experimentGraph2.update();
        generateActionLegend();
    } else {
        console.error('Dataset not found in experimentGraph2.');
    }
}
// ********************** ZOOM RESET ***************************
function resetStateGraphZoom() {
    experimentGraph.resetZoom();
}
function resetActionGraphZoom() {
    experimentGraph2.resetZoom();
}
// ********************** ZOOM GRAPHS ***************************
function resetStateGraph() {
    if (experimentGraph) {
        const dataset = experimentGraph.data.datasets[0];
        
        // Reset to default settings
        dataset.borderColor = 'blue';
        dataset.borderWidth = 2;
        dataset.borderDash = [];
        dataset.pointRadius = 0;
        
        if (experimentGraph.data.datasets.length > 1) {
            const dataset2 = experimentGraph.data.datasets[1];
            dataset2.borderColor = 'red';
            dataset2.borderWidth = 2;
            dataset2.borderDash = [10, 5];
            dataset2.pointRadius = 0;
        }
        
        experimentGraph.options.scales.x.title.color = 'black';
        experimentGraph.options.scales.y.title.color = 'black';
        experimentGraph.options.scales.x.ticks.color = 'black';
        experimentGraph.options.scales.y.ticks.color = 'black';
        experimentGraph.options.scales.x.grid.color = 'gray';
        experimentGraph.options.scales.y.grid.color = 'gray';
        
        document.getElementById('stateGraph').style.backgroundColor = 'white';
        experimentGraph.update();
    }
}

function resetActionGraph() {
    if (experimentGraph2) {
        const dataset = experimentGraph2.data.datasets[0];
        
        // Reset to default settings
        dataset.borderColor = 'purple';
        dataset.borderWidth = 2;
        dataset.borderDash = [];
        dataset.pointRadius = 0;
        
        experimentGraph2.options.scales.x.title.color = 'black';
        experimentGraph2.options.scales.y.title.color = 'black';
        experimentGraph2.options.scales.x.ticks.color = 'black';
        experimentGraph2.options.scales.y.ticks.color = 'black';
        experimentGraph2.options.scales.x.grid.color = 'gray';
        experimentGraph2.options.scales.y.grid.color = 'gray';
        
        document.getElementById('actionGraph').style.backgroundColor = 'white';
        experimentGraph2.update();
    }
}

// ********************** FETCH INTERVAL ***************************
// Function to restart the intervals with new timing
function resetIntervals() {
    clearInterval(intervalId);
    intervalId = setInterval(fetchDataAndUpdateGraph, fetchInterval);
}

// *************************************************************************************
// ***************************** FUNCTION CALLS AND LISTENERS**********************************************
// ****************ON LOAD CALLS LISTENERS**********************

    // Initially hide all forms and display the first selected one with flex display
    hideAllForms();
    if (forms[selectElement.value]) {
        forms[selectElement.value].style.display = 'flex';  // Set display to flex
    }
    
    // generate graphs and legends on page load
    fetchDataAndUpdateGraph();
    
    //generate legends
    generateStateLegend();
    generateActionLegend();
    
// ****************PERIODICAL CALLS**********************

    // generate graphs every 2 seconds
    intervalId = setInterval(fetchDataAndUpdateGraph, fetchInterval);

    //update legends every 2 seconds
    setInterval(() => { generateStateLegend(); generateActionLegend(); }, 2000);
    
    // Run disconnect check every 5 seconds
    setInterval(checkShieldStatus, 5000);
    
// ******************** ENFORCE WHOLE NUMBER ON TIME ***************

function enforceWholeNumbers(inputId) {
  const input = document.getElementById(inputId);
  if (input) {
    input.addEventListener('input', () => {
      // Remove any non-digit characters
      input.value = input.value.replace(/[^0-9]/g, '');
    });

    // Remove the step attribute to avoid decimal entry
    input.removeAttribute('step');
  }
}

enforceWholeNumbers('TimeRange');
enforceWholeNumbers('TimeRange2');

// ****************EVENT LISTENERS**********************
   
    // State Graph Update
    updateStateGraphBtn.addEventListener('click', updateStateGraph);
    // Action Graph Update
    updateActionGraphBtn.addEventListener('click', updateActionGraph);
    // State Graph Reset Zoom
    resetStateZoom.addEventListener('click', resetStateGraphZoom);
    // Action Graph Reset Zoom
    resetActionZoom.addEventListener('click', resetActionGraphZoom);
    // State Graph Reset 
    resetStateGraphbtn.addEventListener('click', resetStateGraph);
    // Action Graph Reset 
    resetActionGraphbtn.addEventListener('click', resetActionGraph);
});









