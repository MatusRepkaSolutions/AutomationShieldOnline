document.addEventListener('DOMContentLoaded', function () {
    // **********************************************************************************
    // ************************** ELEMENT SELECTION *************************************
    
    // Control panel menu
    const selectElement = document.querySelector('.side-panel-select select');
    
    // Graph canvases
    const ctx1 = document.getElementById('experimentGraph').getContext('2d');
    const ctx2 = document.getElementById('experimentGraph2').getContext('2d');
    const ctx3 = document.getElementById('experimentGraph3').getContext('2d');
    const ctx4 = document.getElementById('experimentGraph4').getContext('2d');
    const ctx5 = document.getElementById('experimentGraph5').getContext('2d');
    
    
    // Legend containers
    const stateLegendContainer = document.getElementById('stateLegend');
    const stateLegendContainer2 = document.getElementById('stateLegend2');
    const stateLegendContainer3 = document.getElementById('stateLegend3');
    const stateLegendContainer4 = document.getElementById('stateLegend4');
    const actionLegendContainer = document.getElementById('actionLegend');
   
    // Graph settings elements
    const updateActionGraphBtn = document.getElementById('updateActionGraph');
    const updateStateGraphBtn = document.getElementById('updateStateGraph');
    const updateStateGraphBtn2 = document.getElementById('updateStateGraph2');
    const updateStateGraphBtn3 = document.getElementById('updateStateGraph3');
    const updateStateGraphBtn4 = document.getElementById('updateStateGraph4');

    // Graph size checkboxes
    const stateGraphCheckbox = document.getElementById("state-graph-size");
    const stateGraphCheckbox2 = document.getElementById("state-graph-size2");
    const stateGraphCheckbox3 = document.getElementById("state-graph-size3");
    const stateGraphCheckbox4 = document.getElementById("state-graph-size4");
    const actionGraphCheckbox = document.getElementById("action-graph-size");
    const stateGraphControlsCheckbox = document.getElementById("state-graph-controls-button");
    const stateGraphControlsCheckbox2 = document.getElementById("state-graph-controls-button2");
    const stateGraphControlsCheckbox3 = document.getElementById("state-graph-controls-button3");
    const stateGraphControlsCheckbox4 = document.getElementById("state-graph-controls-button4");
    const actionGraphControlsCheckbox = document.getElementById("action-graph-controls-button");
    const stateGraphContainer = document.getElementById("stateGraph");
    const stateGraphContainer2 = document.getElementById("stateGraph2");
    const stateGraphContainer3 = document.getElementById("stateGraph3");
    const stateGraphContainer4 = document.getElementById("stateGraph4");
    const actionGraphContainer = document.getElementById("actionGraph");
    const stateGraphScroll = document.getElementById("stateGraphScroll");
    const stateGraphScroll2 = document.getElementById("stateGraphScroll2");
    const stateGraphScroll3 = document.getElementById("stateGraphScroll3");
    const stateGraphScroll4 = document.getElementById("stateGraphScroll4");
    const actionGraphscroll = document.getElementById("actionGraphScroll");
    const stateGraphControlsContainer = document.getElementById("state-graph-controls-container");
    const stateGraphControlsContainer2 = document.getElementById("state-graph-controls-container2");
    const stateGraphControlsContainer3 = document.getElementById("state-graph-controls-container3");
    const stateGraphControlsContainer4 = document.getElementById("state-graph-controls-container4");
    const actionGraphControlsContainer = document.getElementById("action-graph-controls-container");
    
    // Reset zoom
    const resetActionZoom = document.getElementById("resetActionZoom");
    const resetStateZoom = document.getElementById("resetStateZoom");
    const resetStateZoom2 = document.getElementById("resetStateZoom2");
    const resetStateZoom3 = document.getElementById("resetStateZoom3");
    const resetStateZoom4 = document.getElementById("resetStateZoom4");

    // Reset graph
    const resetActionGraphbtn = document.getElementById("resetActionGraph");
    const resetStateGraphbtn = document.getElementById("resetStateGraph");
    const resetStateGraphbtn2 = document.getElementById("resetStateGraph2");
    const resetStateGraphbtn3 = document.getElementById("resetStateGraph3");
    const resetStateGraphbtn4 = document.getElementById("resetStateGraph4");

    // AJAX logic for forms submission
    const LQIForm = document.getElementById('LQI-experiment-form');
    const EMPCForm = document.getElementById('EMPC-experiment-form');
    
    // Get the ID from the hidden input field with ID "hiddenID"
    const idInput = document.getElementById('hiddenID');
    const id = idInput ? idInput.value : null;
    console.log("Fetched ID:", id); // Debugging: Check if ID is correctly fetched

    // *************************************************************************************
    // ***************************** SIDE BAR **********************************************

   // Get all experiment forms
   const forms = {
    'EMPC': document.getElementById('EMPC-experiment-form'),
    'LQI': document.getElementById('LQI-experiment-form')
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

        fetch('FurutaShield_update.php', {
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
    
    if (LQIForm) LQIForm.addEventListener('submit', function (e) { submitFunction(e, this); });
    if (EMPCForm) EMPCForm.addEventListener('submit', function (e) { submitFunction(e, this); });
   
  // *************************************************************************************
  // ***************************** DISCONNECT CHECK **************************************
    let shieldWrapper = document.getElementById("Shield-disconnected-wrapper");
    
    function checkShieldStatus() {
        if (!id) return;
    
        fetch(`FurutaShield_DataCheck.php?id=${encodeURIComponent(id)}`)
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
                title: { display: true, text: 'Angle (°)' },
                ticks: {
                    beginAtZero: false, // Let it adapt but with a limit
                },
                afterDataLimits: (scale) => {
                    const minY = scale.min;
                    const maxY = scale.max;
                    const range = maxY - minY;
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
const experimentGraph3 = new Chart(ctx3, {
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
                delay(ctx3) {
                    if (ctx3.type !== 'data' || ctx3.xStarted) {
                        return 0;
                    }
                    ctx3.xStarted = true;
                    return 0;
                }
            },
            y: {
                type: 'number',
                easing: 'linear',
                duration: 0,
                from: previousY,
                delay(ctx3) {
                    if (ctx3.type !== 'data' || ctx3.yStarted) {
                        return 0;
                    }
                    ctx3.xStarted = true;
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
                title: { display: true, text: 'ω [°/s] ' },
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
                title: { display: true, text: 'Motor power (A)' },
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
const experimentGraph4 = new Chart(ctx4, {
    type: 'line',
    data: {
        labels: [], // X-axis (time)
        datasets: [
            { 
                label: 'y', 
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
                delay(ctx4) {
                    if (ctx4.type !== 'data' || ctx4.xStarted) {
                        return 0;
                    }
                    ctx4.xStarted = true;
                    return 0;
                }
            },
            y: {
                type: 'number',
                easing: 'linear',
                duration: 0,
                from: previousU, // Use previousU instead of previousY
                delay(ctx4) {
                    if (ctx4.type !== 'data' || ctx4.yStarted) {
                        return 0;
                    }
                    ctx4.xStarted = true;
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
                title: { display: true, text: 'Angle (°)' },
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
const experimentGraph5 = new Chart(ctx5, {
    type: 'line',
    data: {
        labels: [], // X-axis (time)
        datasets: [
            { 
                label: 'y', 
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
                delay(ctx5) {
                    if (ctx5.type !== 'data' || ctx5.xStarted) {
                        return 0;
                    }
                    ctx4.xStarted = true;
                    return 0;
                }
            },
            y: {
                type: 'number',
                easing: 'linear',
                duration: 0,
                from: previousU, // Use previousU instead of previousY
                delay(ctx5) {
                    if (ctx5.type !== 'data' || ctx5.yStarted) {
                        return 0;
                    }
                    ctx5.xStarted = true;
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
                title: { display: true, text: 'ω [°/s]' },
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
let detailViewEnabled3 = false; // action graph detail
let detailViewEnabled4 = false; // action graph detail
let detailViewEnabled5 = false; // action graph detail

// Helper function to round numbers to the nearest 0.01
function roundToStep(value, step = 0.01) {
    return (Math.round(value / step) * step).toFixed(2);
}

// Function to fetch data from the server and update the graph
let previousDataLength = 0;                 // Store the last known data length
let fetchInterval = 2000;                   // Default interval
let intervalId;                             // Store the interval ID
let zeroDataCount = 0; // Track consecutive zero fetches
let experimentStarted = false; // Track if experiment has started

function startExperiment() {
    experimentStarted = true; // Mark experiment as started
    zeroDataCount = 0; // Reset counter on start
    console.log("Experiment started, monitoring data...");
}

// Attach event listeners to form submissions
if (LQIForm) LQIForm.addEventListener('submit', function (e) { submitFunction(e, this); startExperiment(); });
if (EMPCForm) EMPCForm.addEventListener('submit', function (e) { submitFunction(e, this); startExperiment(); });

async function fetchDataAndUpdateGraph() {
    try {
        const response = await fetch(`FurutaShield_fetch.php?id=${encodeURIComponent(id)}`);
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
        fetchInterval = (currentDataLength !== previousDataLength) ? 250 : 2000;
        previousDataLength = currentDataLength;
        resetIntervals(); // Restart the interval with the new time

        const interval = 10; // 5ms per row
        const hasR = dataFromPHP.length > 0 && dataFromPHP[0].r !== null;

        // Keep only the last 1000 points if detail mode is on
        const limitedData1 = detailViewEnabled1 ? dataFromPHP.slice(-1000) : dataFromPHP;
        const limitedData2 = detailViewEnabled2 ? dataFromPHP.slice(-1000) : dataFromPHP;
        const limitedData3 = detailViewEnabled3 ? dataFromPHP.slice(-1000) : dataFromPHP;
        const limitedData4 = detailViewEnabled4 ? dataFromPHP.slice(-1000) : dataFromPHP;
        const limitedData5 = detailViewEnabled5 ? dataFromPHP.slice(-1000) : dataFromPHP;

        // Update graph data
        experimentGraph.data.labels = limitedData1.map((_, index) => ((index * interval) / 1000).toFixed(2));
        experimentGraph.data.datasets[0].data = limitedData1.map(row => row.y);

        if (hasR) {
            experimentGraph.data.datasets[1].data = limitedData1.map(row => row.r);
        }

        experimentGraph2.data.labels = limitedData2.map((_, index) => ((index * interval) / 1000).toFixed(2));
        experimentGraph2.data.datasets[0].data = limitedData2.map(row => row.u);

        experimentGraph3.data.labels = limitedData3.map((_, index) => ((index * interval) / 1000).toFixed(2));
        experimentGraph3.data.datasets[0].data = limitedData3.map(row => row.y2);

        experimentGraph4.data.labels = limitedData4.map((_, index) => ((index * interval) / 1000).toFixed(2));
        experimentGraph4.data.datasets[0].data = limitedData4.map(row => row.y3);

        experimentGraph5.data.labels = limitedData5.map((_, index) => ((index * interval) / 1000).toFixed(2));
        experimentGraph5.data.datasets[0].data = limitedData5.map(row => row.y4);

        // Refresh both graphs
        experimentGraph.update();
        experimentGraph2.update();
        experimentGraph3.update();
        experimentGraph4.update();
        experimentGraph5.update();
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

function generateStateLegend2() {
    stateLegendContainer2.innerHTML = '';
    experimentGraph3.data.datasets.forEach((dataset, index) => {
        const legendItem = document.createElement('div');
        legendItem.innerHTML = `<span style="display: inline-block; width: 12px; height: 12px; background-color: ${dataset.borderColor}; margin-right: 5px; cursor: pointer;"></span> ${dataset.label}`;
        legendItem.addEventListener('click', () => {
            dataset.hidden = !dataset.hidden;
            experimentGraph3.update();
        });
        stateLegendContainer2.appendChild(legendItem);
    });
}

function generateStateLegend3() {
    stateLegendContainer3.innerHTML = '';
    experimentGraph4.data.datasets.forEach((dataset, index) => {
        const legendItem = document.createElement('div');
        legendItem.innerHTML = `<span style="display: inline-block; width: 12px; height: 12px; background-color: ${dataset.borderColor}; margin-right: 5px; cursor: pointer;"></span> ${dataset.label}`;
        legendItem.addEventListener('click', () => {
            dataset.hidden = !dataset.hidden;
            experimentGraph4.update();
        });
        stateLegendContainer3.appendChild(legendItem);
    });
}

function generateStateLegend4() {
    stateLegendContainer4.innerHTML = '';
    experimentGraph5.data.datasets.forEach((dataset, index) => {
        const legendItem = document.createElement('div');
        legendItem.innerHTML = `<span style="display: inline-block; width: 12px; height: 12px; background-color: ${dataset.borderColor}; margin-right: 5px; cursor: pointer;"></span> ${dataset.label}`;
        legendItem.addEventListener('click', () => {
            dataset.hidden = !dataset.hidden;
            experimentGraph5.update();
        });
        stateLegendContainer4.appendChild(legendItem);
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
    } else if (graphType === "action") {
        detailViewEnabled3 = checkbox.checked;
    }
    fetchDataAndUpdateGraph(); // Ensure both graphs refresh properly
}
// Function to toggle the detail attribute and scroll right
function toggleSize(checkbox, graphContainer, scrollContainer, controlsContainer) {
    if (checkbox.checked) {
        graphContainer.setAttribute("big", "false");
        scrollContainer.setAttribute("big", "false");
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
    stateGraphCheckbox2.addEventListener("change", function () {
        toggleDetail("state", stateGraphCheckbox2);
    });
    stateGraphCheckbox3.addEventListener("change", function () {
        toggleDetail("state", stateGraphCheckbox3);
    });
    stateGraphCheckbox4.addEventListener("change", function () {
        toggleDetail("state", stateGraphCheckbox4);
    });
    actionGraphCheckbox.addEventListener("change", function () {
        toggleDetail("action", actionGraphCheckbox);
    });
    
    if (stateGraphControlsCheckbox && stateGraphContainer) {
        stateGraphControlsCheckbox.addEventListener("change", function () {
            toggleSize(stateGraphControlsCheckbox, stateGraphContainer, stateGraphScroll,stateGraphControlsContainer);
        });
    }

    if (stateGraphControlsCheckbox2 && stateGraphContainer2) {
        stateGraphControlsCheckbox2.addEventListener("change", function () {
            toggleSize(stateGraphControlsCheckbox2, stateGraphContainer2, stateGraphScroll2,stateGraphControlsContainer2);
        });
    }

    if (stateGraphControlsCheckbox3 && stateGraphContainer3) {
        stateGraphControlsCheckbox3.addEventListener("change", function () {
            toggleSize(stateGraphControlsCheckbox3, stateGraphContainer3, stateGraphScroll3,stateGraphControlsContainer3);
        });
    }

    if (stateGraphControlsCheckbox4 && stateGraphContainer4) {
        stateGraphControlsCheckbox4.addEventListener("change", function () {
            toggleSize(stateGraphControlsCheckbox4, stateGraphContainer4, stateGraphScroll4,stateGraphControlsContainer4);
        });
    }

    if (actionGraphControlsCheckbox && actionGraphContainer) {
        actionGraphControlsCheckbox.addEventListener("change", function () {
            toggleSize(actionGraphControlsCheckbox, actionGraphContainer, actionGraphscroll,actionGraphControlsContainer);
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

// Update state graph2
function updateStateGraph2() {
     if (experimentGraph3.data.datasets.length > 0) {
        const dataset = experimentGraph3.data.datasets[0];

        // Get values from inputs
        const newColor = document.getElementById('datasetYColor2').value;
        const newLineType = document.getElementById('datasetYLineType2').value;
        const newLineWidth = parseInt(document.getElementById('datasetYLineWidth2').value, 10) || 2;

        const graphColor = document.getElementById('datasetstatecolor2').value;
        const axesColor = document.getElementById('datasetstateaxes2').value;
        const gridColor = document.getElementById('datasetstategrid2').value;

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
        if (experimentGraph3.data.datasets.length > 1) {
            const dataset2 = experimentGraph.data.datasets[1];
            const newColor2 = document.getElementById('datasetYColor3').value;
            const newLineType2 = document.getElementById('datasetYLineType3').value;
            const newLineWidth2 = parseInt(document.getElementById('datasetYLineWidth3').value, 10) || 2;

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
        experimentGraph3.options.scales.x.title.color = axesColor;
        experimentGraph3.options.scales.y.title.color = axesColor;
        experimentGraph3.options.scales.x.ticks.color = axesColor;
        experimentGraph3.options.scales.y.ticks.color = axesColor;

        // Apply grid color
        experimentGraph3.options.scales.x.grid.color = gridColor;
        experimentGraph3.options.scales.y.grid.color = gridColor;

        // Apply background color
        document.getElementById('stateGraph2').style.backgroundColor = graphColor;

        // Refresh the chart
        experimentGraph3.update();
        generateStateLegend2();
    } else {
        console.error('Dataset not found in experimentGraph.');
    }
}

// Update state graph3
function updateStateGraph3() {
     if (experimentGraph4.data.datasets.length > 0) {
        const dataset = experimentGraph4.data.datasets[0];

        // Get values from inputs
        const newColor = document.getElementById('datasetYColor3').value;
        const newLineType = document.getElementById('datasetYLineType3').value;
        const newLineWidth = parseInt(document.getElementById('datasetYLineWidth3').value, 10) || 2;

        const graphColor = document.getElementById('datasetstatecolor3').value;
        const axesColor = document.getElementById('datasetstateaxes3').value;
        const gridColor = document.getElementById('datasetstategrid3').value;

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
        if (experimentGraph3.data.datasets.length > 1) {
            const dataset2 = experimentGraph.data.datasets[1];
            const newColor2 = document.getElementById('datasetYColor4').value;
            const newLineType2 = document.getElementById('datasetYLineType4').value;
            const newLineWidth2 = parseInt(document.getElementById('datasetYLineWidth4').value, 10) || 2;

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
        experimentGraph4.options.scales.x.title.color = axesColor;
        experimentGraph4.options.scales.y.title.color = axesColor;
        experimentGraph4.options.scales.x.ticks.color = axesColor;
        experimentGraph4.options.scales.y.ticks.color = axesColor;

        // Apply grid color
        experimentGraph4.options.scales.x.grid.color = gridColor;
        experimentGraph4.options.scales.y.grid.color = gridColor;

        // Apply background color
        document.getElementById('stateGraph3').style.backgroundColor = graphColor;

        // Refresh the chart
        experimentGraph4.update();
        generateStateLegend3();
    } else {
        console.error('Dataset not found in experimentGraph.');
    }
}

// Update state graph2
function updateStateGraph4() {
     if (experimentGraph5.data.datasets.length > 0) {
        const dataset = experimentGraph5.data.datasets[0];

        // Get values from inputs
        const newColor = document.getElementById('datasetYColor4').value;
        const newLineType = document.getElementById('datasetYLineType4').value;
        const newLineWidth = parseInt(document.getElementById('datasetYLineWidth4').value, 10) || 2;

        const graphColor = document.getElementById('datasetstatecolor4').value;
        const axesColor = document.getElementById('datasetstateaxes4').value;
        const gridColor = document.getElementById('datasetstategrid4').value;

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
        if (experimentGraph5.data.datasets.length > 1) {
            const dataset2 = experimentGraph.data.datasets[1];
            const newColor2 = document.getElementById('datasetYColor5').value;
            const newLineType2 = document.getElementById('datasetYLineType5').value;
            const newLineWidth2 = parseInt(document.getElementById('datasetYLineWidth5').value, 10) || 2;

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
        experimentGraph5.options.scales.x.title.color = axesColor;
        experimentGraph5.options.scales.y.title.color = axesColor;
        experimentGraph5.options.scales.x.ticks.color = axesColor;
        experimentGraph5.options.scales.y.ticks.color = axesColor;

        // Apply grid color
        experimentGraph5.options.scales.x.grid.color = gridColor;
        experimentGraph5.options.scales.y.grid.color = gridColor;

        // Apply background color
        document.getElementById('stateGraph4').style.backgroundColor = graphColor;

        // Refresh the chart
        experimentGraph5.update();
        generateStateLegend4();
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
function resetStateGraphZoom2() {
    experimentGraph3.resetZoom();
}
function resetStateGraphZoom3() {
    experimentGraph4.resetZoom();
}
function resetStateGraphZoom4() {
    experimentGraph5.resetZoom();
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
function resetStateGraph2() {
    if (experimentGraph3) {
        const dataset = experimentGraph3.data.datasets[0];
        
        // Reset to default settings
        dataset.borderColor = 'blue';
        dataset.borderWidth = 2;
        dataset.borderDash = [];
        dataset.pointRadius = 0;
        
        experimentGraph3.options.scales.x.title.color = 'black';
        experimentGraph3.options.scales.y.title.color = 'black';
        experimentGraph3.options.scales.x.ticks.color = 'black';
        experimentGraph3.options.scales.y.ticks.color = 'black';
        experimentGraph3.options.scales.x.grid.color = 'gray';
        experimentGraph3.options.scales.y.grid.color = 'gray';
        
        document.getElementById('stateGraph2').style.backgroundColor = 'white';
        experimentGraph3.update();
    }
}

function resetStateGraph3() {
    if (experimentGraph4) {
        const dataset = experimentGraph4.data.datasets[0];
        
        // Reset to default settings
        dataset.borderColor = 'blue';
        dataset.borderWidth = 2;
        dataset.borderDash = [];
        dataset.pointRadius = 0;
        
        experimentGraph4.options.scales.x.title.color = 'black';
        experimentGraph4.options.scales.y.title.color = 'black';
        experimentGraph4.options.scales.x.ticks.color = 'black';
        experimentGraph4.options.scales.y.ticks.color = 'black';
        experimentGraph4.options.scales.x.grid.color = 'gray';
        experimentGraph4.options.scales.y.grid.color = 'gray';
        
        document.getElementById('stateGraph3').style.backgroundColor = 'white';
        experimentGraph4.update();
    }
}

function resetStateGraph4() {
    if (experimentGraph5) {
        const dataset = experimentGraph5.data.datasets[0];
        
        // Reset to default settings
        dataset.borderColor = 'blue';
        dataset.borderWidth = 2;
        dataset.borderDash = [];
        dataset.pointRadius = 0;
        
        experimentGraph5.options.scales.x.title.color = 'black';
        experimentGraph5.options.scales.y.title.color = 'black';
        experimentGraph5.options.scales.x.ticks.color = 'black';
        experimentGraph5.options.scales.y.ticks.color = 'black';
        experimentGraph5.options.scales.x.grid.color = 'gray';
        experimentGraph5.options.scales.y.grid.color = 'gray';
        
        document.getElementById('stateGraph4').style.backgroundColor = 'white';
        experimentGraph5.update();
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
    generateStateLegend2();
    generateStateLegend3();
    generateStateLegend4();
    generateActionLegend();
    
// ****************PERIODICAL CALLS**********************

    // generate graphs every 2 seconds
    intervalId = setInterval(fetchDataAndUpdateGraph, fetchInterval);

    //update legends every 2 seconds
    setInterval(() => { generateStateLegend(); generateStateLegend2(); generateStateLegend3(); generateStateLegend4(); generateActionLegend(); }, 2000);
    
    // Run disconnect check every 5 seconds
    setInterval(checkShieldStatus, 5000);

// ************** Enforce whole numbers on time ************
    
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
    // State Graph2 Update
    updateStateGraphBtn2.addEventListener('click', updateStateGraph2);
    // State Graph3 Update
    updateStateGraphBtn3.addEventListener('click', updateStateGraph3);
    // State Graph4 Update
    updateStateGraphBtn4.addEventListener('click', updateStateGraph4);
    // Action Graph Update
    updateActionGraphBtn.addEventListener('click', updateActionGraph);
    // State Graph Reset Zoom
    resetStateZoom.addEventListener('click', resetStateGraphZoom);
    // State Graph2 Reset Zoom
    resetStateZoom2.addEventListener('click', resetStateGraphZoom2);
    // State Graph3 Reset Zoom
    resetStateZoom3.addEventListener('click', resetStateGraphZoom3);
    // State Graph4 Reset Zoom
    resetStateZoom4.addEventListener('click', resetStateGraphZoom4);
    // Action Graph Reset Zoom
    resetActionZoom.addEventListener('click', resetActionGraphZoom);
    // State Graph Reset 
    resetStateGraphbtn.addEventListener('click', resetStateGraph);
    // State Graph2 Reset 
    resetStateGraphbtn2.addEventListener('click', resetStateGraph2);
    // State Graph3 Reset 
    resetStateGraphbtn3.addEventListener('click', resetStateGraph3);
    // State Graph4 Reset 
    resetStateGraphbtn4.addEventListener('click', resetStateGraph4);
    // Action Graph Reset 
    resetActionGraphbtn.addEventListener('click', resetActionGraph);
});











