//Automation Shield
#include "AeroShield.h"
#include <Sampling.h>
// Include header file for EMPC controller
#include "ectrl_unoR4.h"   
#include <empcSequential.h>   
//Official Arduino Libraries
#include <WiFiS3.h>
#include <HttpClient.h>  
           

//##################################################
//########### Login and Wifi credentials ###########
//############ User should edit this  ##############

const char* ssid = "your_wifi_name";                                      // WiFi network name
const char* password = "your_wifi_password";                              // WiFi network password
const char* arduinoName = "Test_arduino";                                 // Name assigned to this Arduino device
const char* shield_password = "pass";                                     // Password for the shield authentication

//#################################################
//######## Don't edit under this line #############
//#################################################

//############ Server connetion  ##################
const char* serverAddress = "mrsolutions.sk";                             // Server domain address
int serverPort = 80;                                                      // Server communication port
WiFiClient wifiClient;                                                    // WiFi client instance for handling connections
HttpClient client = HttpClient(wifiClient, serverAddress, serverPort);    // HTTP client for making requests

//########### Connection variables #################
const char* arduinoModel = "AeroShield";                                  // Automation Shield model
bool connected = false;                                                   // Succesfull connection flag
int experiment = 0;                                                       // Experiment mode flag
int status_code = 0;                                                      // Stores the HTTP response status code
String macStr;                                                            // MAC address var

//########### Get MAC address #####################
String getMacAddress() {
  byte mac[6];                                                            // Array to hold MAC address bytes
  WiFi.macAddress(mac);                                                   // Retrieve the MAC address
  String macAddress = "";                                                 // Initialize MAC address string
  for (int i = 0; i < 6; i++) {                                           // Loop through MAC address bytes
    if (i > 0) {                                                          // Add a separator for readability
      macAddress += ":";                                                  
    }
    macAddress += String(mac[i], HEX);                                    // Convert byte to hexadecimal format
  }
  macAddress.toUpperCase();                                               // Convert to uppercase for standard format
  return macAddress;                                                      // Return formatted MAC address
}

//########### Experiment variables ################

//PID
float Kp = 1.0;                                                           // PID Kp constant
float Ti = 0.550;                                                         // PID Ti constant
float Td = 0.25;                                                          // PID Td constant

//LQI
BLA::Matrix<3, 1> X = {0, 0, 0};                                          //--Estimated LQI state vector
BLA::Matrix<1, 3> K = {-0.0376, 0.3523, 0.1337};                          //--LQ gain with integrator, see MATLAB example
BLA::Matrix<3, 1> Xr = {0, 0, 0};                                         //--Initial LQI state reference
float pi = 3.1415926;                                                      // pi
int r_deg = 0;                                                            // For r output in degrees
int y_deg = 0;                                                            // For y output in degrees

//EMPC
float X_empc[3]  = {0.0, 0.0, 0.0};                                       // Estimated EMPC initial state vector
float Xr_empc[3] = {0.0, 0.0, 0.0};                                       // Initial EMPC reference state vector
static float u_opt[MPT_RANGE];                                            // predicted inputs 
extern struct mpc_ctl ctl;                                                // Object representing presets for MPC controller

//general
float r = 0.0;                                                            // Reference (Wanted pendulum angle)
float y = 0.0;                                                            // Output (Current pendulum angle)
float yprev = 0;                                                          // Previous state of y
float y_safety;                                                           // Output (Current pendulum angle)
float u = 0.0;                                                            // Input (motor power)
int timer = 0;                                                            // Experiment length
int T = 0;                                                                // Section length 
float R[6];                                                               // Reference 
int Ri = 0;                                                               // Size of R (Reference)
int i = 0;                                                                // Section counter
unsigned long Ts = 5;                                                     // Sampling period in milliseconds
unsigned long k = 0;                                                      // Sample index
bool nextStep = false;                                                    // Flag for step function
bool realTimeViolation = false;                                           // Flag for real-time sampling violation
bool safetyStop = false;                                                  // Flag if the pendulum angle is too big


//##############################################################################################################################
//################################################ Setup - only run once #######################################################
void setup() {
  Serial.begin(250000);                                                   // Initialize serial communication
  WiFi.begin(ssid, password);                                             // Begin connecting to the WiFi

  AeroShield.begin();                                                     // Initialise AeroShield board
  AeroShield.calibrate();                                                 // Calibrate AeroShield board + store the 0° value of the pendulum
  Sampling.period(Ts * 1000);                                             // Set sampling period in milliseconds
  PIDAbs.setTs(Sampling.samplingPeriod);                                  // Set sampling period for PID
  Sampling.interrupt(stepEnable);                                         // Set interrupt function

  while (WiFi.status() != WL_CONNECTED) {                                 // Confirm connection (loop until connected to the WiFi)
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");                                    // Print confirmation of established connection

  macStr = getMacAddress();                                               // Get MAX address
  Serial.println("Arduino Name: " + String(arduinoName));                 // Print name asigned by user
  Serial.println("Arduino Model: " + String(arduinoModel));               // Print AeroShield model
  Serial.println("MAC Address: " + macStr);                               // Print MAC address

  Serial.println("Connecting to the server, please wait.");               // Print notice about connecting to the server
  while(status_code<=0){
    sendArduinoData();                                                    // Loop until succesfully connected and registered on the server
  }
  
  passwordCheck();                                                        // Loop until recieving the same password user asigned above
}

//##############################################################################################################################
//############################################### Loop - loops to infinity #####################################################
void loop() {
  experimentCall();                                                       // Loop until recieving a new experiment
  experimentBegin();                                                      // Ask for experiment data, proceed with the experiment
  sendExperimentData();                                                   // Tell server the experiment has ended
}

//##############################################################################################################################
//#################################################### sendArduinoData #########################################################
void sendArduinoData() {
  // Prepare a post string with shield's data (case, name, model, MAC) - this request registers the shield on the server / to the the database
  String postData = "case=setup&name=" + String(arduinoName) + "&model=" + String(arduinoModel) + "&mac=" + macStr;

  client.beginRequest();                                                                             // Begin html POST request
  client.post("/automationshield/other/arduino_online.php");                                         // Server path
  client.sendHeader("Content-Type", "application/x-www-form-urlencoded");                            // Send content type header (form data)
  client.sendHeader("Content-Length", postData.length());                                            // Specify length of POST data
  client.beginBody();                                                                                // Start sending the request body
  client.print(postData);                                                                            // Print the whole request
  client.endRequest();                                                                               // End POST request

  int statusCode = client.responseStatusCode();                                                      // POST request success flag
  String response = client.responseBody();                                                           // Get the response from the server

  Serial.print("Status code: ");                                                                     // Debug output           
  Serial.println(statusCode);                                                                        // Print status flag
  status_code = statusCode;                                                                          // Asign status flag to status code

  if(status_code == 200){                                                                            // If the request is succesfull
    Serial.println("Arduino succesfully connected to the server.");
    Serial.print("Server response: ");
    Serial.println(response);
  }else{                                                                                             // Request not succesfull, loop sendArduinoData() until success
    Serial.println("Arduino failed to establish a connection to the server.");                       // Debug output
    Serial.println("This is usually the case if the WiFi connection is unreliable.");                // Debug output
    Serial.println("If the connection fails 3 or more times, another WiFi is required.");            // In case of a bad WiFi connection try another WiFi (happens with unreliable WiFi)
    Serial.print("Server response: ");                                                               // Debug output
    Serial.println(response);                                                                        // Status code can determine what is wrong with the connection
    Serial.println("Trying again...");
  }
  
}

//##############################################################################################################################
//###################################################### passwordCheck #########################################################
void passwordCheck() {
  while (!connected) {
    /*  Requesting password from the server.
        Onece the user inserts the password in the login page,
        this function will retrieve the code from the database
        and compare it with the password user asigned at the top of this code. */
    
    // Prepare a post string with shield's data (case, name, model, MAC) - this request retrieves the password from the database
    String postData = "case=password_check&name=" + String(arduinoName) + "&model=" + String(arduinoModel) + "&mac=" + macStr;

    client.beginRequest();                                                                            // Begin html POST request
    client.post("/automationshield/other/arduino_online.php");                                        // Server path
    client.sendHeader("Content-Type", "application/x-www-form-urlencoded");                            // Send content type header (form data)
    client.sendHeader("Content-Length", postData.length());                                            // Specify length of POST data
    client.beginBody();                                                                                // Start sending the request body
    client.print(postData);                                                                           // Print the whole request
    client.endRequest();                                                                              // End POST request

    int statusCode = client.responseStatusCode();                                                     // POST request success flag
    String response = client.responseBody();                                                          // Get the response from the server

    Serial.println("Arduino Name: " + String(arduinoName));                                           // Print name asigned by user
    Serial.println("Arduino Model: " + String(arduinoModel));                                         // Print AeroShield model
    Serial.println("MAC Address: " + macStr);                                                         // Print MAC address
    Serial.println("Reading password from the server");                                               // Debug output
    Serial.print("Status code: ");                                                                    // Debug output
    Serial.println(statusCode);                                                                       // Print status flag
    Serial.print("Server response: '");                                                               // Debug output
    Serial.print(response);                                                                           // Print response
    Serial.println("'");                                                                              // Debug output

    response.trim();                                                                                  // Trim the response to remove any extra spaces or newline characters

    if (response == shield_password) {                                                                // If the password matches
      Serial.println("Password matched");                                                             // Print confimarion
      
      // Prepare a post string with shield's data (name, model, MAC) - this request tells the server that the password matched
      postData = "case=updateStatus&name=" + String(arduinoName) + "&model=" + String(arduinoModel) + "&mac=" + macStr;
                                                
      client.beginRequest();                                                                          // Begin html POST request                                         
      client.post("/automationshield/other/arduino_online.php");                                      // Server path
      client.sendHeader("Content-Type", "application/x-www-form-urlencoded");                         // Send content type header (form data)
      client.sendHeader("Content-Length", postData.length());                                         // Specify length of POST data
      client.beginBody();                                                                             // Start sending the request body
      client.print(postData);                                                                         // Print the whole request
      client.endRequest();                                                                            // End POST request
      
      connected = true;                                                                               // Quit password check loop
    }
    else {
      Serial.println("Password mismatch, retrying...");                                               // If password didnt match, loop sendArduinoData() until match
    }
    
    delay(1000);                                                                                      // 1 second delay to prevent DDOS
  }
}

//##############################################################################################################################
//###################################################### experimentCall ########################################################
void experimentCall() {
  /*  Requesting if there is a new experiment asigned by the user.
      Onece the user inserts a new experiment on a model page,
      this function will retrieve the start flag from the server and let experimentBegin() do it's job. */

  while (experiment == 0) {                                                       // Loop until there is a new experiment
    // Prepare a post string with shield's data (case, name, model, MAC) - this request asks the server if there is a new experiment
    String postData = "case=experiment_start&name=" + String(arduinoName) + "&model=" + String(arduinoModel) + "&mac=" + macStr;

    client.beginRequest();                                                                            // Begin html POST request 
    client.post("/automationshield/other/arduino_online.php");                                        // Server path
    client.sendHeader("Content-Type", "application/x-www-form-urlencoded");                           // Send content type header (form data)
    client.sendHeader("Content-Length", postData.length());                                           // Specify length of POST data
    client.beginBody();                                                                               // Start sending the request body
    client.print(postData);                                                                           // Print the whole request
    client.endRequest();                                                                              // End POST request

    int statusCode = client.responseStatusCode();                                                     // POST request success flag
    String response = client.responseBody();                                                          // Get the response from the server

    Serial.print("Status code: ");                                                                    // Debug output
    Serial.println(statusCode);                                                                       // Print status flag
    Serial.print("Response: '");                                                                      // Debug output
    Serial.print(response);                                                                           // Print response
    Serial.println("'");

    if (response.startsWith("START")) {                                                               // If the response is START - user asigned new experiment
      experiment = 1;                                                                                 // Condition to quit experimentCall() loop
    }
  }
}

//##############################################################################################################################
//###################################################### experimentBegin #######################################################
void experimentBegin() {
  /*  Requesting the experiment data.
      The main parameter is the experiment type (PID, LQ, EMPC, OpenLoop, Identification),
      after which it can retrieve the correct data for the set experiment from the response.
      After receiving the data, this function also proceeds to do the experiment. */

  // Prepare a post string with shield's data (name, model, MAC) - this request asks the server what experiment type is signed (PID, LQ, EMPC, OpenLoop, Identification).
  String postData = "name=" + String(arduinoName) + "&model=" + String(arduinoModel) + "&mac=" + macStr;

  client.beginRequest();                                                                                // Begin html POST request 
  client.post("/automationshield/other/arduino_online_experiment_data.php");                            // Server path
  client.sendHeader("Content-Type", "application/x-www-form-urlencoded");                               // Send content type header (form data)
  client.sendHeader("Content-Length", postData.length());                                               // Specify length of POST data
  client.beginBody();                                                                                   // Start sending the request body
  client.print(postData);                                                                               // Print the whole request
  client.endRequest();                                                                                  // End POST request

  int statusCode = client.responseStatusCode();                                                         // POST request success flag
  String response = client.responseBody();                                                              // Get the response from the server

  Serial.print("Status code: ");                                                                        // Debug output
  Serial.println(statusCode);                                                                           // Print status flag
  Serial.print("Response: '");                                                                          // Debug output
  Serial.println(response);                                                                             // Print status response

  String experimentType = getParameter(response, "Experiment");                                         // Insert experiment response to a variable

  Serial.println("status: START");                                                                      // Print a start flag for python.exe
  Serial.println("model: " + String(arduinoModel));                                                     // Print a model for the python.exe
  Serial.println("experiment: " + experimentType);                                                      // Print an experiment fot the python.exe


  //############################# PID #############################
  if (experimentType == "PID") {                                                                        // If the received esperiment is PID
    Serial.println("PID Experiment Data:");                                                             // Debug output
    
    
    Kp = response.substring(response.indexOf("Kp=") + 3, response.indexOf("&Ti=")).toFloat();           // Get and asign Kp parameter from response
    Ti = response.substring(response.indexOf("Ti=") + 3, response.indexOf("&Td=")).toFloat();           // Get and asign Ti parameter from response
    Td = response.substring(response.indexOf("Td=") + 3, response.indexOf("&r1=")).toFloat();           // Get and asign Td parameter from response

    R[0] = response.substring(response.indexOf("r1=") + 3, response.indexOf("&r2=")).toFloat();         // Get and asign the first reference from response
    R[1] = response.substring(response.indexOf("r2=") + 3, response.indexOf("&r3=")).toFloat();         // Get and asign the second reference from response
    R[2] = response.substring(response.indexOf("r3=") + 3, response.indexOf("&r4=")).toFloat();         // Get and asign the third reference from response
    R[3] = response.substring(response.indexOf("r4=") + 3, response.indexOf("&r5=")).toFloat();         // Get and asign the forth reference from response
    R[4] = response.substring(response.indexOf("r5=") + 3, response.indexOf("&r6=")).toFloat();         // Get and asign the fifth reference from response
    R[5] = response.substring(response.indexOf("r6=") + 3, response.indexOf("&period=")).toFloat();      // Get and asign the sixt reference from response

    while(R[Ri]>0){                                                                                     // The user can send from one up to six references.
      Ri++;                                                                                             // This cycle asign only references grater then zero and stops at the first zero.
    }                                                                                                   // The Ri is then also used in the step() function
    
    T = response.substring(response.indexOf("period=") + 7).toInt();                                    // Get and asign the period for all references length from response
    timer = T * Ri;                                                                                     // Asign the experiment length 
    T = T * 200;                                                                                        // Covert experiment period time from s to ms
    
                                                                                                        
    Serial.print("Kp: ");                                                                               // Debug output of Kp
    Serial.println(Kp);                                                                                 // Print received Kp
    Serial.print("Ti: ");                                                                               // Debug output of Ti
    Serial.println(Ti);                                                                                 // Print received Ti
    Serial.print("Td: ");                                                                               // Debug output Td
    Serial.println(Td);                                                                                 // Print received Td
    
    Serial.println("Submited Array (R):");                                                              // Debug output of reference array
    for (int i = 0; i < Ri; i++) {                                                                      // Loop through reference array elements
        Serial.print("R[");                                                                             // Debug output index label
        Serial.print(i);                                                                                // Debug output index number
        Serial.print("]: ");                                                                            // Debug output Formatting 
        Serial.println(R[i]);                                                                           // Debug output actual reference value
    }
    
    Serial.print("Timer (s): ");                                                                        // Debug output expetiment time length in s
    Serial.println(timer);                                                                              // Print received experiment time
    Serial.print("Period time (s): ");                                                                  // Debug output expetiment time length
    Serial.println(T/200);                                                                              // Print received experiment time

    Serial.print("Starting PID experiment for ");                                                       // Debug output
    Serial.print(timer);                                                                                // Print received experiment time
    Serial.println(" seconds");                                                                         // Debug output

      AeroShield.calibrate();                                                                           // Calibrate AeroShield board + store the 0° value of the pendulum
      PIDAbs.setKp(Kp);                                                                                 // Set Proportional constant
      PIDAbs.setTi(Ti);                                                                                 // Set Integral constant
      PIDAbs.setTd(Td);                                                                                 // Set Derivative constant
      unsigned long startTime = millis();                                                               // Asign millis for comparision with the timer
      safetyStop = false;                                                                               // Ensure stafety stop flag is false
      
      //########################## PID control loop ###########################
      while ((millis() - startTime < (unsigned long)timer * 1000) && !safetyStop) {                     // Begin PID experiment while the millis time < set timer and stafety stop is false
        
        safetyAngle();                                                                                  // Call a safety function to check if the pendulum angle is too big
        if (i > Ri) {                                                                                   // If at end of trajectory
          analogWrite(5, 0);                                                                            // Turn off the motor
          safetyStop = true;
          //while (1);                                                                                  // Stop program execution
        } 
        if (nextStep) {                                                                                 // If ISR enables step flag
          stepPID();                                                                                    // Run step function
          nextStep = false;                                                                             // Disable step flag
        }


      }

      //################### reset all data after PID experiment ###################
      for (int clear = 0; clear < Ri ; clear++) {                                                       // R array reset loop
        R[clear] = 0;                                                                                   // Set each element to 0 (or any default value)
      }                                                                                      
      i=0;                                                                                              // Reset section counter
      k=0;                                                                                              // Reset sample index
      Ri=0;                                                                                             // Reset reference array size
      timer=0;                                                                                          // Reset section timer
      T=0;                                                                                              // Reset section period time
      experiment = 0;                                                                                   // Reset experiment flag
      u = 0;                                                                                            // Reset motor power input
      y_safety = 0;                                                                                     // Reset safety angle variable   

      AeroShield.actuatorWrite(0);                                                                      // Turn off motor
      Serial.println("status: STOP");                                                                   // Signal python.exe that the experiment ended

  //############################# openloop ########################################
  } else if (experimentType == "openloop") {
    Serial.println("Open loop Experiment Data:");                                                             // Debug output
    timer = response.substring(response.indexOf("timer=") + 6).toInt();                                 // Get and asign the experiment length from response
    Serial.print("Timer (s): ");                                                                        // Debug output expetiment time length in s
    Serial.println(timer);                                                                              // Print received experiment time
    Serial.print("Starting open loop experiment for ");                                                       // Debug output
    Serial.print(timer);                                                                                // Print received experiment time
    Serial.println(" seconds");                                                                         // Debug output
    AeroShield.calibrate();                                                                             // Calibrate AeroShield board + store the 0° value of the pendulum
    unsigned long startTime = millis();                                                                 // Asign millis for comparision with the timer
    safetyStop = false;                                                                                 // Ensure stafety stop flag is false                                                                           // Ensure stafety stop flag is false

    //########################## openloop control loop ###########################
    while ((millis() - startTime < (unsigned long)timer * 1000) && !safetyStop) { 
      safetyAngle();                                                                                    // Call a safety function to check if the pendulum angle is too big
      if (nextStep) {                                                                                   // If ISR enables step flag
          stepOPENLOOP();                                                                               // Run step function
          nextStep = false;                                                                             // Disable step flag
        }
      }

    //########################## restart of data after experiment #################
      timer=0;                                                                                          // Reset section timer
      u = 0;                                                                                            // Reset motor power input
      k = 0;                                                                                            // Reset sample index                                                                                           // 
      y_safety = 0;                                                                                     // Reset safety angle variable   

      AeroShield.actuatorWrite(0);                                                                      // Turn off motor
      Serial.println("status: STOP");                                                                   // Signal python.exe that the experiment ended

  //################## Identification ###############################
  } else if (experimentType == "identification") {
    Serial.println("Identification Experiment Data:");                                                  // Debug output
    
                                                                                                        // We use the same array as for reference to save memory
    R[0] = response.substring(response.indexOf("r1=") + 3, response.indexOf("&r2=")).toFloat();         // Get and asign the first reference from response
    R[1] = response.substring(response.indexOf("r2=") + 3, response.indexOf("&r3=")).toFloat();         // Get and asign the second reference from response
    R[2] = response.substring(response.indexOf("r3=") + 3, response.indexOf("&r4=")).toFloat();         // Get and asign the third reference from response
    R[3] = response.substring(response.indexOf("r4=") + 3, response.indexOf("&r5=")).toFloat();         // Get and asign the forth reference from response
    R[4] = response.substring(response.indexOf("r5=") + 3, response.indexOf("&r6=")).toFloat();         // Get and asign the fifth reference from response
    R[5] = response.substring(response.indexOf("r6=") + 3, response.indexOf("&timer=")).toFloat();      // Get and asign the sixt reference from response

    while(R[Ri]>0){                                                                                     // The user can send from one up to six references.
      Ri++;                                                                                             // This cycle asign only references grater then zero and stops at the first zero.
    }                                                                                                   // The Ri is then also used in the step() function
    
    T = response.substring(response.indexOf("timer=") + 6).toInt();                                     // Get and asign the time period 
    
    Serial.println("Submited Array (U):");                                                              // Debug output of reference array
    for (int i = 0; i < Ri; i++) {                                                                      // Loop through reference array elements
        Serial.print("U[");                                                                             // Debug output index label
        Serial.print(i);                                                                                // Debug output index number
        Serial.print("]: ");                                                                            // Debug output Formatting 
        Serial.println(R[i]);                                                                           // Debug output actual reference value
    }

    Serial.print("Period time (s): ");                                                                  // Debug output expetiment time length
    Serial.println(T);                                                                                  // Print received experiment time

    Serial.print("Starting identification experiment for ");                                            // Debug output
    Serial.print(T);                                                                                    // Print received experiment time
    Serial.println(" seconds");                                                                         // Debug output

    AeroShield.calibrate();                                                                             // Calibrate AeroShield board + store the 0° value of the pendulum
    unsigned long startTime = millis();                                                                 // Asign millis for comparision with the timer
    safetyStop = false;                                                                                 // Ensure stafety stop flag is false

    //#################### identification control loop ###############      
    while ((millis() - startTime < (unsigned long) Ri * T * 1000) && !safetyStop) {                 // Begin LQI experiment while the millis time < set timer and stafety stop is false
      safetyAngle();                                                                                    // Call a safety function to check if the pendulum angle is too big
      if(i > Ri){                                                                                       //--If trajectory ended
        AeroShield.actuatorWriteVolt(0.0);                                                              //--Stop the Motor
        safetyStop = true;                                                                              //--End of program execution
      }
      if (nextStep) {                                                                                   // If ISR enables step flag
          stepIdentification();                                                                         // Run step function
          nextStep = false;                                                                             // Disable step flag
        }
    }

    //####### reset all data after identification experiment #######
      for (int clear = 0; clear < Ri ; clear++) {                                                       // R array reset loop
        R[clear] = 0;                                                                                   // Set each element to 0 (or any default value)
      }                                                                                      
      i=0;                                                                                              // Reset section counter
      k=0;                                                                                              // Reset sample index
      Ri=0;                                                                                             // Reset reference array size
      T=0;                                                                                              // Reset section period time
      experiment = 0;                                                                                   // Reset experiment flag
      u = 0;                                                                                            // Reset motor power input
      y_safety = 0;                                                                                     // Reset safety angle variable   

      AeroShield.actuatorWrite(0);                                                                      // Turn off motor
      Serial.println("status: STOP");                                                                   // Signal python.exe that the experiment ended

  //################## LQI #########################
  } else if (experimentType == "LQI") {
    Serial.println("LQI Experiment Data:");                                                             // Debug output
    
    R[0] = response.substring(response.indexOf("r1=") + 3, response.indexOf("&r2=")).toFloat();         // Get and asign the first reference from response
    R[1] = response.substring(response.indexOf("r2=") + 3, response.indexOf("&r3=")).toFloat();         // Get and asign the second reference from response
    R[2] = response.substring(response.indexOf("r3=") + 3, response.indexOf("&r4=")).toFloat();         // Get and asign the third reference from response
    R[3] = response.substring(response.indexOf("r4=") + 3, response.indexOf("&r5=")).toFloat();         // Get and asign the forth reference from response
    R[4] = response.substring(response.indexOf("r5=") + 3, response.indexOf("&r6=")).toFloat();         // Get and asign the fifth reference from response
    R[5] = response.substring(response.indexOf("r6=") + 3, response.indexOf("&timer=")).toFloat();      // Get and asign the sixt reference from response

    while(R[Ri]>0){                                                                                     // The user can send from one up to six references.
      R[Ri]= R[Ri] * (pi/180.0f);                                                                       // Convertion degree to radian
      Ri++;                                                                                             // This cycle asign only references grater then zero and stops at the first zero.
    }                                                                                                   // The Ri is then also used in the step() function
    
    timer = response.substring(response.indexOf("timer=") + 6).toInt();                                 // Get and asign the experiment length from response
                                                                                                   
    Serial.println("Submited Array (R):");                                                              // Debug output of reference array
    for (int i = 0; i < Ri; i++) {                                                                      // Loop through reference array elements
        Serial.print("R[");                                                                             // Debug output index label
        Serial.print(i);                                                                                // Debug output index number
        Serial.print("]: ");                                                                            // Debug output Formatting 
        Serial.println(R[i]);                                                                           // Debug output actual reference value
    }

    Serial.print("Period time (s): ");                                                                  // Debug output expetiment time length
    Serial.println(timer);                                                                              // Print received experiment time

    Serial.print("Starting LQI experiment for ");                                                       // Debug output
    Serial.print(timer*Ri);                                                                             // Print received experiment time
    Serial.println(" seconds");                                                                         // Debug output

    AeroShield.calibrate();                                                                             // Calibrate AeroShield board + store the 0° value of the pendulum
    unsigned long startTime = millis();                                                                 // Asign millis for comparision with the timer
    safetyStop = false;                                                                                 // Ensure stafety stop flag is false

    //############################# LQI control loop #######################      
    while ((millis() - startTime < (unsigned long)timer * Ri * 1000) && !safetyStop) {              // Begin LQI experiment while the millis time < set timer and stafety stop is false
      safetyAngle();                                                                                    // Call a safety function to check if the pendulum angle is too big
      if(i > Ri){                                                                                       //--If trajectory ended
        AeroShield.actuatorWriteVolt(0.0);                                                              //--Stop the Motor
        safetyStop = true;                                                                              //--End of program execution
      }
      if (nextStep) {                                                                                   // If ISR enables step flag
          stepLQI();                                                                                    // Run step function
          nextStep = false;                                                                             // Disable step flag
        }
    }
    //##################### Reset all data after experiment ################
      i=0;                                                                                              // Reset section counter
      k=0;                                                                                              // Reset sample index
      Ri=0;                                                                                             // Reset reference array size
      timer=0;                                                                                          // Reset section timer
      experiment = 0;                                                                                   // Reset experiment flag
      u = 0;                                                                                            // Reset motor power input
      y_safety = 0;                                                                                     // Reset safety angle variable
      X(0,0) = 0;                                                                                       // Reset the first estimated state vector 
      X(1,0) = 0;                                                                                       // Reset the second estimated state vector 
      X(2,0) = 0;                                                                                       // Reset the third estimated state vector 
      Xr(0,0) = 0;                                                                                      // Reset the first initial state reference 
      Xr(1,0) = 0;                                                                                      // Reset the second initial state reference 
      Xr(2,0) = 0;                                                                                      // Reset the third initial state reference    

      AeroShield.actuatorWrite(0);                                                                      // Turn off motor
      Serial.println("status: STOP");                                                                   // Signal python.exe that the experiment ended

  //############################### LQI manual ####################################
  }else if (experimentType == "LQIman") {
    Serial.println("LQI manual Experiment Data:");                                                      // Debug output
    
    timer = response.substring(response.indexOf("timer=") + 6).toInt();                                 // Get and asign the experiment length from response

    Serial.print("Period time (s): ");                                                                  // Debug output expetiment time length
    Serial.println(timer);                                                                              // Print received experiment time

    Serial.print("Starting LQI experiment for ");                                                       // Debug output
    Serial.print(timer);                                                                                // Print received experiment time
    Serial.println(" seconds");                                                                         // Debug output

    AeroShield.calibrate();                                                                             // Calibrate AeroShield board + store the 0° value of the pendulum
    unsigned long startTime = millis();                                                                 // Asign millis for comparision with the timer
    safetyStop = false;                                                                                 // Ensure stafety stop flag is false

    //############################# LQI control loop #######################      
    while ((millis() - startTime < (unsigned long)timer * 1000) && !safetyStop) {              // Begin LQI experiment while the millis time < set timer and stafety stop is false
      safetyAngle();                                                                                    // Call a safety function to check if the pendulum angle is too big
      if (nextStep) {                                                                                   // If ISR enables step flag
          stepLQIman();                                                                                 // Run step function
          nextStep = false;                                                                             // Disable step flag
        }
    }
    //##################### Reset all data after experiment ################
      i=0;                                                                                              // Reset section counter
      k=0;                                                                                              // Reset sample index
      timer=0;                                                                                          // Reset section timer
      experiment = 0;                                                                                   // Reset experiment flag
      u = 0;                                                                                            // Reset motor power input
      y_safety = 0;                                                                                     // Reset safety angle variable
      X(0,0) = 0;                                                                                       // Reset the first estimated state vector 
      X(1,0) = 0;                                                                                       // Reset the second estimated state vector 
      X(2,0) = 0;                                                                                       // Reset the third estimated state vector 
      Xr(0,0) = 0;                                                                                      // Reset the first initial state reference 
      Xr(1,0) = 0;                                                                                      // Reset the second initial state reference 
      Xr(2,0) = 0;                                                                                      // Reset the third initial state reference    

      AeroShield.actuatorWrite(0);                                                                      // Turn off motor
      Serial.println("status: STOP");                                                                   // Signal python.exe that the experiment ended
  
  //############################### EMPC ####################################
  }else if (experimentType == "EMPC") {
    Serial.println("PID Experiment Data:");                                                             // Debug output
    
    R[0] = response.substring(response.indexOf("r1=") + 3, response.indexOf("&r2=")).toFloat();         // Get and asign the first reference from response
    R[1] = response.substring(response.indexOf("r2=") + 3, response.indexOf("&r3=")).toFloat();         // Get and asign the second reference from response
    R[2] = response.substring(response.indexOf("r3=") + 3, response.indexOf("&r4=")).toFloat();         // Get and asign the third reference from response
    R[3] = response.substring(response.indexOf("r4=") + 3, response.indexOf("&r5=")).toFloat();         // Get and asign the forth reference from response
    R[4] = response.substring(response.indexOf("r5=") + 3, response.indexOf("&r6=")).toFloat();         // Get and asign the fifth reference from response
    R[5] = response.substring(response.indexOf("r6=") + 3, response.indexOf("&X1=")).toFloat();         // Get and asign the sixt reference from response

    while(R[Ri]>0){                                                                                     // The user can send from one up to six references.
      R[Ri]= R[Ri] * (pi/180.0f);                                                                       // Convertion degree to radian
      Ri++;                                                                                             // This cycle asign only references grater then zero and stops at the first zero.
    }                                                                                                   // The Ri is then also used in the step() function
    
    timer = response.substring(response.indexOf("timer=") + 6).toInt();                                 // Get and asign the experiment length from response
                                                                                                   
    Serial.println("Submited Array (R):");                                                              // Debug output of reference array
    for (int i = 0; i < Ri; i++) {                                                                      // Loop through reference array elements
        Serial.print("R[");                                                                             // Debug output index label
        Serial.print(i);                                                                                // Debug output index number
        Serial.print("]: ");                                                                            // Debug output Formatting 
        Serial.println(R[i]);                                                                           // Debug output actual reference value
    }

    Serial.print("Period time (s): ");                                                                  // Debug output expetiment time length
    Serial.println(timer);                                                                              // Print received experiment time

    Serial.print("Starting EMPC experiment for ");                                                      // Debug output
    Serial.print(timer*Ri);                                                                             // Print received experiment time
    Serial.println(" seconds");                                                                         // Debug output

    AeroShield.calibrate();                                                                             // Calibrate AeroShield board + store the 0° value of the pendulum
    unsigned long startTime = millis();                                                                 // Asign millis for comparision with the timer
    safetyStop = false;                                                                                 // Ensure stafety stop flag is false
    
    //######################### EMPC control loop ###########################
    while ((millis() - startTime < (unsigned long)timer * Ri * 1000) && !safetyStop) {              // Begin LQI experiment while the millis time < set timer and stafety stop is false
      safetyAngle();                                                                                    // Call a safety function to check if the pendulum angle is too big
      if(i > Ri){                                                                                       //--If trajectory ended
        AeroShield.actuatorWriteVolt(0.0);                                                              //--Stop the Motor
        safetyStop = true;                                                                              //--End of program execution
      }
      if (nextStep) {                                                                                   // If ISR enables step flag
          stepEMPC();                                                                                   // Run step function
          nextStep = false;                                                                             // Disable step flag
        }
    }

    //######################## Reset all data after experiment ###############
      i=0;                                                                                              // Reset section counter
      k=0;                                                                                              // Reset sample index
      Ri=0;                                                                                             // Reset reference array size
      timer=0;                                                                                          // Reset section timer
      experiment = 0;                                                                                   // Reset experiment flag
      u = 0;                                                                                            // Reset motor power input
      y_safety = 0;                                                                                     // Reset safety angle variable   

      AeroShield.actuatorWrite(0);                                                                      // Turn off motor
      Serial.println("status: STOP");                                                                   // Signal python.exe that the experiment ended

  //################## EMPC manual #########################
  }else if (experimentType == "EMPCman") {
    Serial.println("PID Experiment Data:");                                                             // Debug output

    timer = response.substring(response.indexOf("timer=") + 6).toInt();                                 // Get and asign the experiment length from response
                                                                                                   

    Serial.print("Period time (s): ");                                                                  // Debug output expetiment time length
    Serial.println(timer);                                                                              // Print received experiment time

    Serial.print("Starting EMPC manual experiment for ");                                               // Debug output
    Serial.print(timer);                                                                                // Print received experiment time
    Serial.println(" seconds");                                                                         // Debug output

    AeroShield.calibrate();                                                                             // Calibrate AeroShield board + store the 0° value of the pendulum
    unsigned long startTime = millis();                                                                 // Asign millis for comparision with the timer
    safetyStop = false;                                                                                 // Ensure stafety stop flag is false
    
    //######################### EMPC control loop ###########################
    while ((millis() - startTime < (unsigned long)timer * 1000) && !safetyStop) {                       // Begin LQI experiment while the millis time < set timer and stafety stop is false
      safetyAngle();                                                                                    // Call a safety function to check if the pendulum angle is too big
      if (nextStep) {                                                                                   // If ISR enables step flag
          stepEMPCman();                                                                                // Run step function
          nextStep = false;                                                                             // Disable step flag
        }
    }

    //######################## Reset all data after experiment ###############
      timer=0;                                                                                          // Reset section timer
      experiment = 0;                                                                                   // Reset experiment flag
      u = 0;                                                                                            // Reset motor power input
      y_safety = 0;                                                                                     // Reset safety angle variable   

      AeroShield.actuatorWrite(0);                                                                      // Turn off motor
      Serial.println("status: STOP");                                                                   // Signal python.exe that the experiment ended

  //################## Unknown experiment #########################
  } else {
    Serial.println("Unknown experiment type.");
  }
}

//##############################################################################################################################
//################################## Extracts parameter value from a URL query string ##########################################
String getParameter(String data, String parameter) {                                                    // Function to extract a parameter's value from a query string
  int startIndex = data.indexOf(parameter + "=");                                                       // Find the start position of the parameter
  if (startIndex == -1) return "";                                                                      // Return empty string if parameter not found
  int endIndex = data.indexOf("&", startIndex);                                                         // Find the end of the parameter value
  if (endIndex == -1) endIndex = data.length();                                                         // If no '&' found, assume end of string
  return data.substring(startIndex + parameter.length() + 1, endIndex);                                 // Extract and return parameter value
}

//##############################################################################################################################
//###################################################### experimentBegin #######################################################
void sendExperimentData() {
   /* Reset of the experiment on the server.
      This function just tells the server about succesfull end of an experiment */

  // Prepare a post string with shield's data (case, name, model, MAC)
  String postData = "case=experiment_done&name=" + String(arduinoName) + "&model=" + String(arduinoModel) + "&mac=" + macStr;

  client.beginRequest();                                                                                // Begin html POST request
  client.post("/automationshield/other/arduino_online.php");                                            // Server path
  client.sendHeader("Content-Type", "application/x-www-form-urlencoded");                               // Send content type header (form data)
  client.sendHeader("Content-Length", postData.length());                                               // Specify length of POST data
  client.beginBody();                                                                                   // Start sending the request body
  client.print(postData);                                                                               // Print the whole request
  client.endRequest();                                                                                  // End POST request

  int statusCode = client.responseStatusCode();                                                         // POST request success flag
  String response = client.responseBody();                                                              // Get the response from the server

  Serial.print("Experiment done response status code: ");                                               // Debug output           
  Serial.println(statusCode);                                                                           // Print status flag
  Serial.print("Response: ");                                                                           // Debug output
  Serial.println(response);                                                                             // Print response


  experiment = 0;                                                                                       // Ensure that the experiment is 0
}

void stepEnable() {                                                                                     // ISR
/**if (nextStep == true) {                                                                                 // If previous sample still running
    realTimeViolation = true;                                                                           // Real-time has been violated
    Serial.println("Real-time samples violated.");                                                      // Print error message
    analogWrite(5, 0);                                                                                  // Turn off the motor
    safetyStop = true;                                                                                  // Stop control loop
  }**/ 
  nextStep = true;                                                                                      // Enable step flag
}

//##############################################################################################################################
//###################################################### stepPID ###############################################################
void stepPID() {                                                                                      // Define step function
  if (k % (T * i) == 0) {                                                                             // If at the end of section
    r = R[i];                                                                                         // Progress in trajectory
    i++;                                                                                              // Increment section counter
  }
  k++;                                                                                                // Increment index
  y = AeroShield.sensorRead();                                                                        // Read pendulum angle in %
  u = PIDAbs.compute(r - y, 0, 100, 0, 100);                                                          // PID
  AeroShield.actuatorWrite(u);                                                                        // Actuate

  Serial.print("r: ");                                                                                // Debug output of r (important for python.exe)
  Serial.print(r);                                                                                    // Print r (important for python.exe)
  Serial.print(" y: ");                                                                               // Debug output of y (important for python.exe)
  Serial.print(y);                                                                                    // Print y (important for python.exe)
  Serial.print(" u: ");                                                                               // Debug output of u (important for python.exe)
  Serial.println(u);                                                                                  // Print u (important for python.exe)
}

//##############################################################################################################################
//###################################################### stepLQI ###############################################################
void stepLQI() {                                                                                      // Define step function
  if (k % (timer*200*i) == 0){                                                                        //--Moving through trajectory values            
        Xr(1) = R[i];                                                                                 //r = R[i];
        r_deg = R[i] * (180/pi) ;                                                                     // for output in degrees
        i++;                                                                                          //--Change input value after defined amount of samples
      }
      k++;                                                                                            //--Increment

      u = -(K*(X-Xr))(0);
      u = constrain(u,0,3.7);

      y = AeroShield.sensorReadRadian();                                                              // Angle in radians

      AeroShield.actuatorWriteVolt(u);                                                                //--Actuation
      X(1) = y;
      X(2) = (y-yprev)/(Ts/1000.0);

      X(0) = X(0) + (Xr(1) - X(1));                                                                   // Integration part
      yprev=y;
      y_deg = y * (180/pi) ;                                                                          // for output in degrees
      Serial.print("r: ");                                                                            // Debug output of r (important for python.exe)
      Serial.print(r_deg);                                                                            // Print r (important for python.exe)
      Serial.print(" y: ");                                                                           // Debug output of y (important for python.exe)
      Serial.print(y_deg);                                                                            // Print y (important for python.exe)
      Serial.print(" u: ");                                                                           // Debug output of u (important for python.exe)
      Serial.println(X(1),3);                                                                         // Print u (important for python.exe)
}
//##############################################################################################################################
//###################################################### stepLQI manual ########################################################
void stepLQIman() {                                                                                   // Define step function
      
      r =  AutomationShield.mapFloat(AeroShield.referenceRead(),0,100,0,M_PI_2);                      //--Sensing Pot reference
      Xr(1) = r;
      r_deg = r * (180/pi) ;                                                                          // for output in degrees
      u = -(K*(X-Xr))(0);
      u = constrain(u,0,3.7);

      y = AeroShield.sensorReadRadian();                                                              // Angle in radians

      AeroShield.actuatorWriteVolt(u);                                                                //--Actuation
      X(1) = y;
      X(2) = (y-yprev)/(Ts/1000.0);

      X(0) = X(0) + (Xr(1) - X(1));                                                                   // Integration part
      yprev=y;
      y_deg = y * (180/pi) ;                                                                          // for output in degrees
      Serial.print("r_deg: ");                                                                        // Debug output of r (important for python.exe)
      Serial.print(r);                                                                                // Print r (important for python.exe)
      Serial.print(" y: ");                                                                           // Debug output of y (important for python.exe)
      Serial.print(y_deg);                                                                            // Print y (important for python.exe)
      Serial.print(" u: ");                                                                           // Debug output of u (important for python.exe)
      Serial.println(X(1),3);                                                                         // Print u (important for python.exe)
}
//##############################################################################################################################
//###################################################### EMPC ##################################################################
void stepEMPC() {                                                                                     // Define step function
  if (k % (timer*200*i) == 0){                                                                        //--Moving through trajectory values            
        Xr_empc[1] = R[i];                                                                            //r = R[i];
        r_deg = R[i] * (180/pi) ;                                                                     // for output in degrees
        i++;                                                                                          //--Change input value after defined amount of samples
      }
      k++;                                                                                            //--Increment
 
      y = AeroShield.sensorReadRadian();                                                              // Angle in radians

      X_empc[1] = y;                                                                                  // Arm angle
      X_empc[2] = (y-yprev)/(float(Ts)/1000.0);                                                       // Angular speed of the arm

      X_empc[0] = X_empc[0] + (Xr_empc[1] - X_empc[1]);                                                                   // Integral state 
      yprev=y;

      empcSequential(X_empc, u_opt);                                                                  // solve Explicit MPC problem 
      u = u_opt[0];                                                                                   // Save system input into input variable
      AeroShield.actuatorWriteVolt(u);                                                                // Actuation

      y_deg = y * (180/pi) ;                                                                          // for output in degrees
      Serial.print("r: ");                                                                            // Debug output of r (important for python.exe)
      Serial.print(r_deg);                                                                            // Print r (important for python.exe)
      Serial.print(" y: ");                                                                           // Debug output of y (important for python.exe)
      Serial.print(y_deg);                                                                            // Print y (important for python.exe)
      Serial.print(" u: ");                                                                           // Debug output of u (important for python.exe)
      Serial.println(u);                                                                              // Print u (important for python.exe)
}
//##############################################################################################################################
//###################################################### EMPC manual ###########################################################
void stepEMPCman() {                                                                                  // Define step function
      r =  AutomationShield.mapFloat(AeroShield.referenceRead(),0,100,0,M_PI_2);                      //--Sensing Pot reference
      Xr_empc[1] =  r;                  
      r_deg = r * (180/pi) ;                                                                          // for output in degrees
 
      y = AeroShield.sensorReadRadian();                                                              // Angle in radians

      X_empc[1] = y;                                                                                  // Arm angle
      X_empc[2] = (y-yprev)/(float(Ts)/1000.0);                                                       // Angular speed of the arm

      X_empc[0] = X_empc[0] + (Xr_empc[1] - X_empc[1]);                                                                   // Integral state 
      yprev=y;

      empcSequential(X_empc, u_opt);                                                                  // solve Explicit MPC problem 
      u = u_opt[0];                                                                                   // Save system input into input variable
      AeroShield.actuatorWriteVolt(u);                                                                // Actuation

      y_deg = y * (180/pi) ;                                                                          // for output in degrees
      Serial.print("r: ");                                                                            // Debug output of r (important for python.exe)
      Serial.print(r_deg);                                                                            // Print r (important for python.exe)
      Serial.print(" y: ");                                                                           // Debug output of y (important for python.exe)
      Serial.print(y_deg);                                                                            // Print y (important for python.exe)
      Serial.print(" u: ");                                                                           // Debug output of u (important for python.exe)
      Serial.println(u);                                                                              // Print u (important for python.exe)
}

//##############################################################################################################################
//################################################### identification ###########################################################
void stepIdentification() {                                                                           // Define step function
  if (k % (T*200*i) == 0){                                                                            //--Moving through trajectory values            
        u = R[i];                                                                                     // Progress in trajectory
        i++;                                                                                          //--Change input value after defined amount of samples
      }

      y = AeroShield.sensorRead();                                                                    // Read pendulum angle in %
      AeroShield.actuatorWrite(u);                                                                    // Actuate

      Serial.print(" y: ");                                                                           // Debug output of y (important for python.exe)
      Serial.print(y);                                                                                // Print y (important for python.exe)
      Serial.print(" u: ");                                                                           // Debug output of u (important for python.exe)
      Serial.println(u);                                                                              // Print u (important for python.exe)

      k++;                                                                                            // Increment index   
}
//##############################################################################################################################
//################################################### OPEN LOOP ################################################################
void stepOPENLOOP() {                                                                                 // Define step function
      y = AeroShield.sensorReadDegree();                                                              //  mapping the pendulum angle
      u = AeroShield.referenceRead();                                                                 //  Function for mapping the potentiometer input
      AeroShield.actuatorWrite(u);                                                                    //  Actuate

      Serial.print(" y: ");                                                                           // Debug output of y (important for python.exe)
      Serial.print(y);                                                                                // Print y (important for python.exe)
      Serial.print(" u: ");                                                                           // Debug output of u (important for python.exe)
      Serial.println(u);                                                                              // Print u (important for python.exe)   
}

//##############################################################################################################################
//###################################################### safetyAngle ###########################################################
void safetyAngle() {                                                                                  // Define safety function
  y_safety = AeroShield.sensorRead();                                                                 // Read pendulum angle in %
  if(y_safety>100){                                                                                   // Condition if the angle of pendulum is too big
    safetyStop = true;                                                                                // Stop any experiment
    Serial.print("Safety stop trigered. Pendulum angle too big!");                                    // Notice user why the experiment stopped
    y_safety = 0;                                                                                     // Reset safety angle variable                                      
  }
}
