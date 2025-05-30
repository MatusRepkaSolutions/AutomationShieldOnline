//Automation Shield
#include <FurutaShield.h>
#include <SamplingServo.h>
//EMPC
#include "ectrl.h"
#include <empcSequential.h>
#include "furuta_ekf_model.h"
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
const char* arduinoModel = "FurutaShield";                               // Automation Shield model
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
float Kp = 2.3;                                                           // PID Kp constant
float Ti = 0.1;                                                           // PID Ti constant
float Td = 0.03;                                                          // PID Td constant


// LQ gain with integrator
BLA::Matrix<4, 1> X;
BLA::Matrix<4, 1> X1;
BLA::Matrix<2, 1> Y;
BLA::Matrix<2, 4> H = { 1, 0, 0, 0, 0, 0, 1, 0 };
BLA::Matrix<4, 4> Q_kalman = {
  0.0001, 0, 0, 0,
  0, 0.0001, 0, 0,
  0, 0, 0.00001, 0,
  0, 0, 0, 0.00001
};
BLA::Matrix<2, 2> R_kalman = { 1e-12, 0, 0, 1e-10 };
BLA::Matrix<1, 4> K = { -0.8840, -1.6925, -213.6926, -18.2625 };
//BLA::Matrix<1, 5> K = { -19.0138, 3.6472, 3.7027, -30.1155, -24.2531 }; neviem co je toto ci to je podla toho ktory FS pouzivame
BLA::Matrix<2, 1> prevOutput;

BLA::Matrix<4, 4> A = {
  1.0, 0.01, 0, 0,
  0, 1.0, 0, 0,
  0, 0, 1.0067, 0.01,
  0, 0, 1.338, 1.0027
};

BLA::Matrix<4, 1> B = { 0.0001, 0.01, -0.0001, -0.0136 };


float r_deg = 0;                                                          // For r output in degrees
float y_deg = 0;                                                          // For y output in degrees
float y2_deg = 0;                                                         // For y output in degrees
float y3_deg = 0;                                                         // For y output in degrees
float y4_deg = 0;                                                         // For y output in degrees
float unan = 0;

//EMPC
ekf_t ekf;
BLA::Matrix<5, 1> X_empc;
BLA::Matrix<4, 1> X1_empc;
BLA::Matrix<1, 1> Xr_empc;
BLA::Matrix<2, 1> Y_empc;
BLA::Matrix<2, 1> prevOutput_empc;
BLA::Matrix<2, 4> H_empc = { 1, 0, 0, 0, 0, 0, 1, 0 };

static float u_opt[MPT_RANGE];                                            // predicted inputs 
extern struct mpc_ctl ctl;                                                // Object representing presets for MPC controller

unsigned long prevMill;
float Ksu = 10.5;
float Kq = 8;
float Kdq = 9;
float Ke = 6;
//float eta = 1.05;

bool up = false;
bool enable = false;

float mp = 0.00034;
float g = 9.81;
float lp = 0.055;
float Ip = mp * pow(lp, 2) / 3;
float beta = PI;
float w0 = sqrt((mp * g * lp) / Ip);
float w1 = mp * g * lp / 2;
int wmax = 100;
float amax = (3 * PI / 4);  //Limit for arm angle
float E0 = 0.00183447;
float E;
float r = 0.0;

int h;
const int stop = 5000;
float angle;
unsigned long prevTime;

//general
float rprev = 0.0;                                                        // Previous reference (Wanted ball altitude)
double y[2] = { 0.0, 0.0 };                                               // Output vector
double u[1] = { 0.0 };                                                    // Input vector
float yprev = 0;                                                          // Previous state of y
float y_safety;                                                           // Output (Current ball altitude)
int timer = 0;                                                            // Experiment length
int T = 0;                                                                // Section length 
float Re[6];                                                              // Reference 
int Ri = 0;                                                               // Size of R (Reference)
int i = 0;                                                                // Section counter
unsigned long Ts = 10;                                                     // Sampling period in milliseconds
unsigned long k = 0;                                                      // Sample index
bool nextStep = false;                                                    // Flag for step function
bool realTimeViolation = false;                                           // Flag for real-time sampling violation
bool safetyStop = false;                                                  // Flag if the ball altitude is too big


//##############################################################################################################################
//################################################ Setup - only run once #######################################################
void setup() {
  FurutaShield.actuatorWrite(0);
  Serial.begin(250000);                                                   // Initialize serial communication
  WiFi.begin(ssid, password);                                             // Begin connecting to the WiFi

  delay(1000);
  Sampling.period(Ts * 1000.0);
  Sampling.interrupt(stepEnable);
  
  FurutaShield.begin();
  Wire.begin();
  delay(3000);

  ekf.x[0] = 0.0;   // theta0
  ekf.x[1] = 0.0;   // dtheta0
  ekf.x[2] = M_PI;  // theta1 (kyvadlo dole)
  ekf.x[3] = 0.0;   // dtheta1

  // Inicializácia matíc P, Q
  for (int i = 0; i < 4; i++) {
    for (int j = 0; j < 4; j++) {
      ekf.P[i][j] = (i == j) ? 0.01 : 0.0;
      ekf.Q[i][j] = (i == j) ? 1e-5 : 0.0;
    }
  }

  // Inicializácia matice R
  for (int i = 0; i < 2; i++) {
    for (int j = 0; j < 2; j++) {
      ekf.R[i][j] = (i == j) ? 1e-4 : 0.0;
    }
  }

  while (WiFi.status() != WL_CONNECTED) {                                 // Confirm connection (loop until connected to the WiFi)
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");                                    // Print confirmation of established connection

  macStr = getMacAddress();                                               // Get MAX address
  Serial.println("Arduino Name: " + String(arduinoName));                 // Print name asigned by user
  Serial.println("Arduino Model: " + String(arduinoModel));               // Print FurutaShield model
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
    client.sendHeader("Content-Type", "application/x-www-form-urlencoded");                           // Send content type header (form data)
    client.sendHeader("Content-Length", postData.length());                                           // Specify length of POST data
    client.beginBody();                                                                               // Start sending the request body
    client.print(postData);                                                                           // Print the whole request
    client.endRequest();                                                                              // End POST request

    int statusCode = client.responseStatusCode();                                                     // POST request success flag
    String response = client.responseBody();                                                          // Get the response from the server

    Serial.println("Arduino Name: " + String(arduinoName));                                           // Print name asigned by user
    Serial.println("Arduino Model: " + String(arduinoModel));                                         // Print FurutaShield model
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

  //################## LQI #########################
  if (experimentType == "LQI") {
    Serial.println("LQI Experiment Data:");                                                             // Debug output
    
    Re[0] = response.substring(response.indexOf("r1=") + 3, response.indexOf("&r2=")).toFloat();         // Get and asign the first reference from response
    Re[1] = response.substring(response.indexOf("r2=") + 3, response.indexOf("&r3=")).toFloat();         // Get and asign the second reference from response
    Re[2] = response.substring(response.indexOf("r3=") + 3, response.indexOf("&r4=")).toFloat();         // Get and asign the third reference from response
    Re[3] = response.substring(response.indexOf("r4=") + 3, response.indexOf("&r5=")).toFloat();         // Get and asign the forth reference from response
    Re[4] = response.substring(response.indexOf("r5=") + 3, response.indexOf("&r6=")).toFloat();         // Get and asign the fifth reference from response
    Re[5] = response.substring(response.indexOf("r6=") + 3, response.indexOf("&timer=")).toFloat();      // Get and asign the sixt reference from response

    while(Re[Ri]>0){                                                                                     // The user can send from one up to six references.
      if(Re[Ri]>170){                                                                                    // Check if Re is not out of bounds (too big)
        Re[Ri]=170;                                                                                      // Set Re to the max posssible value
      }
      if(Re[Ri]<-170){                                                                                   // Check if Re is not out of bounds (too small)
        Re[Ri]=-170;                                                                                     // Set Re to the min possible val
      }
      Re[Ri] = Re[Ri] * PI / 180.0;                                                                      // Convert degrees to radians
      Ri++;                                                                                              // This cycle asign only references grater then zero and stops at the first zero.
    }                                                                                                    // The Ri is then also used in the step() function
    
    T = response.substring(response.indexOf("timer=") + 6).toInt();                                     // Get and asign the experiment length from response
    timer = T * Ri;                                                                                     // Asign the experiment length 
    T = T * 100;                                                                                        // Covert experiment period time from s to itterations
                                                                                                   
    Serial.println("Submited Array (R):");                                                              // Debug output of reference array
    for (int i = 0; i < Ri; i++) {                                                                      // Loop through reference array elements
        Serial.print("Re[");                                                                             // Debug output index label
        Serial.print(i);                                                                                // Debug output index number
        Serial.print("]: ");                                                                            // Debug output Formatting 
        Serial.println(Re[i]);                                                                           // Debug output actual reference value
    }

    Serial.print("Period time (s): ");                                                                  // Debug output expetiment time length
    Serial.println(timer);                                                                              // Print received experiment time

    Serial.print("Starting LQI experiment for ");                                                       // Debug output
    Serial.print(timer*Ri);                                                                             // Print received experiment time
    Serial.println(" seconds");                                                                         // Debug output

    unsigned long startTime = millis();                                                                 // Asign millis for comparision with the timer
    safetyStop = false;                                                                                 // Ensure stafety stop flag is false

    //############################# LQI control loop #######################      
    while ((millis() - startTime < (unsigned long)timer * 1000) && !safetyStop) {                  // Begin LQI experiment while the millis time < set timer and stafety stop is false
      safetyAngle();                                                                                    // Call a safety function to check if the ball altitude is too big
      if(i > Ri){                                                                                       //--If trajectory ended
        FurutaShield.actuatorWrite(0.0);                                                                 //--Stop the Motor
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
      u[1] = {0.0};                                                                                        // Reset motor power input
      y_safety = 0;                                                                                     // Reset safety angle variable
      X = {0, 0, 0, 0};
      X1 = {0, 0, 0, 0};
      Y = {0, 0};
      prevOutput = {0, 0};
      H = {1, 0, 0, 0, 0, 0, 1, 0}; 

      FurutaShield.actuatorWrite(0);                                                                     // Turn off motor
      Serial.println("status: STOP");                                                                   // Signal python.exe that the experiment ended

 
  //############################### EMPC ####################################
  }else if (experimentType == "EMPC") {
    Serial.println("PID Experiment Data:");                                                             // Debug output
    
    Re[0] = response.substring(response.indexOf("r1=") + 3, response.indexOf("&r2=")).toFloat();         // Get and asign the first reference from response
    Re[1] = response.substring(response.indexOf("r2=") + 3, response.indexOf("&r3=")).toFloat();         // Get and asign the second reference from response
    Re[2] = response.substring(response.indexOf("r3=") + 3, response.indexOf("&r4=")).toFloat();         // Get and asign the third reference from response
    Re[3] = response.substring(response.indexOf("r4=") + 3, response.indexOf("&r5=")).toFloat();         // Get and asign the forth reference from response
    Re[4] = response.substring(response.indexOf("r5=") + 3, response.indexOf("&r6=")).toFloat();         // Get and asign the fifth reference from response
    Re[5] = response.substring(response.indexOf("r6=") + 3, response.indexOf("&Ksu=")).toFloat();         // Get and asign the sixt reference from response
    Ksu = response.substring(response.indexOf("Ksu=") + 4, response.indexOf("&Kq=")).toFloat();         // Get and asign the second reference from response
    Kq = response.substring(response.indexOf("Kq=") + 3, response.indexOf("&Kdq=")).toFloat();         // Get and asign the third reference from response
    Kdq = response.substring(response.indexOf("Kdq=") + 4, response.indexOf("&Ke=")).toFloat();         // Get and asign the forth reference from response
    Ke = response.substring(response.indexOf("Ke=") + 3, response.indexOf("&timer=")).toFloat();         // Get and asign the fifth reference from response

    while(Re[Ri]>0){                                                                                     // The user can send from one up to six references.
      if(Re[Ri]>170){                                                                                    // Check if Re is not out of bounds (too big)
        Re[Ri]=170;                                                                                      // Set Re to the max posssible value
      }
      if(Re[Ri]<-170){                                                                                   // Check if Re is not out of bounds (too small)
        Re[Ri]=-170;                                                                                     // Set Re to the min possible val
      }
      Re[Ri] = Re[Ri] * PI / 180.0;                                                                      // Convert degrees to radians
      Ri++;                                                                                              // This cycle asign only references grater then zero and stops at the first zero.
    }                                                                                                    // The Ri is then also used in the step() function
    
    T = response.substring(response.indexOf("timer=") + 6).toInt();                                     // Get and asign the experiment length from response
    timer = T * Ri;                                                                                     // Asign the experiment length 
    T = T * 100;                                                                                        // Covert experiment period time from s to itterations
                                                                                                   
    Serial.println("Submited Array (R):");                                                              // Debug output of reference array
    for (int i = 0; i < Ri; i++) {                                                                      // Loop through reference array elements
        Serial.print("Re[");                                                                             // Debug output index label
        Serial.print(i);                                                                                // Debug output index number
        Serial.print("]: ");                                                                            // Debug output Formatting 
        Serial.println(Re[i]);                                                                           // Debug output actual reference value
    }

    Serial.println("----- Parsed Values -----");
    Serial.print("Ksu: ");
    Serial.println(Ksu); 

    Serial.print("Kq: ");
    Serial.println(Kq);

    Serial.print("Kdq: ");
    Serial.println(Kdq);

    Serial.print("Ke: ");
    Serial.println(Ke);

    Serial.print("Period time (s): ");                                                                  // Debug output expetiment time length
    Serial.println(timer);                                                                              // Print received experiment time

    Serial.print("Starting EMPC experiment for ");                                                      // Debug output
    Serial.print(timer*Ri);                                                                             // Print received experiment time
    Serial.println(" seconds");                                                                         // Debug output

    unsigned long startTime = millis();                                                                 // Asign millis for comparision with the timer
    safetyStop = false;                                                                                 // Ensure stafety stop flag is false
    
    //######################### EMPC control loop ###########################
    while ((millis() - startTime < (unsigned long)timer * 1000) && !safetyStop) {                  // Begin LQI experiment while the millis time < set timer and stafety stop is false
      safetyAngle();                                                                                    // Call a safety function to check if the ball altitude is too big
      if(i > Ri){                                                                                       //--If trajectory ended
        FurutaShield.actuatorWrite(0.0);                                                                 //--Stop the Motor
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
      u[1] = {0.0};                                                                                        // Reset motor power input
      y_safety = 0;                                                                                     // Reset safety angle variable   
      X_empc = {0, 0, 0, 0, 0};
      Y_empc = {0, 0};
      prevOutput_empc = {0, 0};
      H_empc = {1, 0, 0, 0, 0, 0, 1, 0}; 

      FurutaShield.actuatorWrite(5000);                                                                 // Turn off motor
      Serial.println("status: STOP");                                                                   // Signal python.exe that the experiment ended

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
/**if (nextStep == true) {                                                                              // If previous sample still running
    realTimeViolation = true;                                                                           // Real-time has been violated
    Serial.println("Real-time samples violated.");                                                      // Print error message
    analogWrite(5, 0);                                                                                  // Turn off the motor
    safetyStop = true;                                                                                  // Stop control loop
  }**/ 
  nextStep = true;                                                                                      // Enable step flag
}
//##############################################################################################################################
//###################################################### stepLQI ###############################################################
void stepLQI() {                                                                                      // Define step function                                                                                       //--Increment
  if (k % (T * i) == 0) {                                                                             // If at the end of section
    r = Re[i];                                                                                        // Progress in trajectory
    i++;                                                                                              // Increment section counter
  }
  k++;                                                                                                // Increment index 

      Y = FurutaShield.sensorRead();
      Y(1) = FurutaShield.wrapToPi(Y(1));
      if (-0.5 <= Y(1) && Y(1) <= 0.5) {
        if (!up)

        up = true;
    //    u = -float((K * (X))(0, 0));

        if (abs(X(0)) >= PI) {
          safetyStop = true; 
        }
      } else {
        up = false;

    //    u = FurutaShield.swingUp(Ksu, Kq, Kdq, Ke, eta, X1, wmax, amax, mp, lp);
      }

      //u = 0;
      //X1 = ekf(u, Y, H, Q_kalman, R_kalman);
      X = FurutaShield.estimate(Y);
      //u = sat(u, 50, -50);

      X(2) = FurutaShield.wrapToPi(X(2));
      //X1(2) = FurutaShield.wrapToPi(X1(2));

      FurutaShield.actuatorWrite(u[0]);                                                               // Actuation

      r_deg = r * 180.0 / PI;
      y_deg = X(0) * 180.0 / PI;
      y2_deg = X(1) * 180.0 / PI;
      y3_deg = X(2) * 180.0 / PI;

      Serial.print("r: ");                                                                            // Debug output of r (important for python.exe)
      Serial.print(r_deg);                                                                            // Print r (important for python.exe)
      Serial.print(" y: ");                                                                           // Debug output of y (important for python.exe)
      Serial.print(y_deg);                                                                             // Print y (important for python.exe)
      Serial.print(" y2: ");                                                                          // Debug output of y2 (important for python.exe)
      Serial.print(y2_deg);                                                                             // Print y (important for python.exe)
      Serial.print(" y3: ");                                                                          // Debug output of y3 (important for python.exe)
      Serial.print(y3_deg);                                                                             // Print y (important for python.exe)
      Serial.print(" u: ");                                                                           // Debug output of u (important for python.exe)
      Serial.println(u[0]);                                                                              // Print u (important for python.exe)
}
//##############################################################################################################################
//###################################################### EMPC ##################################################################
void stepEMPC() {                                                                                     // Define step function
  if (k % (T * i) == 0) {                                                                             // If at the end of section
    Xr_empc = Re[i];                                                                                  // Progress in trajectory
    r = Re[i];
    i++;                                                                                              // Increment section counter
  }
  k++;                                                                                                // Increment index
  
      Y_empc = FurutaShield.sensorRead();                                                             // Angle in radians
      y[0] = Y_empc(0);
      y[1] = Y_empc(1);
      Y_empc(1) = FurutaShield.wrapToPi(Y_empc(1));

      if (-0.3 <= Y_empc(1) && Y_empc(1) <= 0.3) {
        up = true;
        float X_array[6];
        for (int i = 0; i < 6; i++) {
          X_array[i] = X_empc(i);
          if (i == 0)
            X_array[0] = X_array[0] - r;
        }
        //Now call empcSequential with X_array
        empcSequential(X_array, u_opt);
        u[0] = u_opt[0];                                                                                 // Save system input into input variable
      }else {
        up = false;

       // u[0] = FurutaShield.swingUp(Ksu, Kq, Kdq, Ke, X1_empc, wmax, amax, mp, lp);
      }

      model(&ekf, ekf.x, u);                                                                          // predikcia + výpočet Jacobiánov  !!!!!!!!!!!!!!!!
      ekf_step(&ekf, y);                                                                              // vykoná korekciu

      double theta0_est = ekf.x[0];
      double theta0_dot_est = ekf.x[1];
      double theta1_est = FurutaShield.wrapToPi(ekf.x[2]);
      double theta1_dot_est = ekf.x[3];

      X1_empc = FurutaShield.estimate(Y_empc);
      X1_empc(2) = FurutaShield.wrapToPi(X1_empc(2));
      
      X_empc(0) += (Xr_empc(0) - Y_empc(0));
      X_empc(1) = X1_empc(0);
      X_empc(2) = X1_empc(1);
      X_empc(3) = X1_empc(2);
      X_empc(4) = X1_empc(3);

      FurutaShield.actuatorWrite(u[0]);                                                               // Actuation

      r_deg = r * 180.0 / PI;
      y_deg = X_empc(1) * 180.0 / PI;
      y2_deg = X_empc(2) * 180.0 / PI;
      y3_deg = X_empc(3) * 180.0 / PI;
      y4_deg = X_empc(4) * 180.0 / PI;
      if(isnan(u[0])){
        unan = 0;
      }else{
        unan = u[0];
      }

      Serial.print("r: ");                                                                            // Debug output of r (important for python.exe)
      Serial.print(r_deg);                                                                            // Print r (important for python.exe)
      Serial.print(" y: ");                                                                           // Debug output of y (important for python.exe) uhol ramena
      Serial.print(y_deg);                                                                             // Print y (important for python.exe)
      Serial.print(" y2: ");                                                                          // Debug output of y2 (important for python.exe) rychlost ramena
      Serial.print(y2_deg);                                                                             // Print y (important for python.exe)
      Serial.print(" y3: ");                                                                          // Debug output of y3 (important for python.exe) uhol kyvadla
      Serial.print(y3_deg);                                                                             // Print y (important for python.exe)
      Serial.print(" y4: ");                                                                          // Debug output of y3 (important for python.exe) rychlost kyvadla
      Serial.print(y4_deg);                                                                             // Print y (important for python.exe)
      Serial.print(" u: ");                                                                           // Debug output of u (important for python.exe)
      Serial.println(unan);                                                                              // Print u (important for python.exe)
}
//##############################################################################################################################
//###################################################### safetyAngle ###########################################################
void safetyAngle() {                                                                                  // Define safety function
  if(y_deg>10000000){                                                                                 // Safety condition
    safetyStop = true;                                                                                // Stop any experiment
    Serial.print("Oppa, sumting wong!");                                                              // Notice user why the experiment stopped
    y_safety = 0;                                                                                     // Reset safety angle variable                                      
  }
}
