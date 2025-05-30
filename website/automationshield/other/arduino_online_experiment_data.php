<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connect.php'; // Include the database connection

// Check if required POST parameters are set
if (!isset($_POST['model']) || !isset($_POST['name']) || !isset($_POST['mac'])) {
    die("Missing model, name, or mac address.");
}

$model = $_POST['model'];
$name = $_POST['name'];
$mac = $_POST['mac'];

// Step 1: Fetch the arduino_online_id and model from the arduino_online table
$sql = "SELECT id, model FROM arduino_online WHERE name = ? AND mac = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('ss', $name, $mac);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $arduino_online_id = $row['id'];
    $model = $row['model'];
} else {
    die("No matching Arduino found.");
}

// Step 2: Handle logic based on the model
switch ($model) {
    case 'AeroShield':
        // Fetch the Experiment type from AeroShield using arduino_online_id
        $sql = "SELECT Experiment FROM AeroShield WHERE arduino_online_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $arduino_online_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $Experiment = $row['Experiment'];
        } else {
            die(json_encode(['error' => 'No matching entry found in AeroShield.']));
        }

        // Step 3: Handle logic based on the Experiment type
        switch ($Experiment) {
            case 'PID':
                // Fetch PID-related data including the timer
                $sql = "SELECT Kp, Ti, Td, r1, r2, r3, r4, r5, r6, T FROM AeroShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $Kp = $row['Kp'] ?? 'empty';
                    $Ti = $row['Ti'] ?? 'empty';
                    $Td = $row['Td'] ?? 'empty';
                    $r1 = $row['r1'] ?? 'empty';
                    $r2 = $row['r2'] ?? 'empty';
                    $r3 = $row['r3'] ?? 'empty';
                    $r4 = $row['r4'] ?? 'empty';
                    $r5 = $row['r5'] ?? 'empty';
                    $r6 = $row['r6'] ?? 'empty';
                    $timer2 = $row['T'] ?? 'empty';


                    // Send experiment data back
                    echo "Experiment=$Experiment&Kp=$Kp&Ti=$Ti&Td=$Td&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&period=$timer2";
                    flush();
                } else {
                    die(json_encode(['error' => 'No data found for PID experiment in AeroShield.']));
                }
                break;

            case 'openloop':
                // Fetch openloop-related data including the timer
                $sql = "SELECT TIMER FROM AeroShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for OpenLoop experiment in AeroShield.']));
                }
                break;
                
                case 'identification':
                // Fetch openloop-related data including the timer
                $sql = "SELECT r1, r2, r3, r4, r5, r6, TIMER FROM AeroShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $r1 = $row['r1'] ?? 'empty';
                  $r2 = $row['r2'] ?? 'empty';
                  $r3 = $row['r3'] ?? 'empty';
                  $r4 = $row['r4'] ?? 'empty';
                  $r5 = $row['r5'] ?? 'empty';
                  $r6 = $row['r6'] ?? 'empty';
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for Identification experiment in AeroShield.']));
                }
                break;
                
                case 'LQI':
                // Fetch openloop-related data including the timer
                $sql = "SELECT r1, r2, r3, r4, r5, r6, TIMER FROM AeroShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $r1 = $row['r1'] ?? 'empty';
                  $r2 = $row['r2'] ?? 'empty';
                  $r3 = $row['r3'] ?? 'empty';
                  $r4 = $row['r4'] ?? 'empty';
                  $r5 = $row['r5'] ?? 'empty';
                  $r6 = $row['r6'] ?? 'empty';
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for LQI experiment in AeroShield.']));
                }
                break;

                case 'LQIman':
                    // Fetch openloop-related data including the timer
                    $sql = "SELECT TIMER FROM AeroShield WHERE arduino_online_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('i', $arduino_online_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
    
                    if ($result->num_rows > 0) {
                      $row = $result->fetch_assoc(); // Fetch the result row
                      $timer = $row['TIMER'] ?? 'empty';
    
                      // Send experiment data back
                      echo "Experiment=$Experiment&timer=$timer";
                      flush();
                      
                    } else {
                        die(json_encode(['error' => 'No data found for LQI man experiment in AeroShield.']));
                    }
                    break;
                
                case 'EMPC':
                // Fetch openloop-related data including the timer
                $sql = "SELECT r1, r2, r3, r4, r5, r6, TIMER FROM AeroShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $r1 = $row['r1'] ?? 'empty';
                  $r2 = $row['r2'] ?? 'empty';
                  $r3 = $row['r3'] ?? 'empty';
                  $r4 = $row['r4'] ?? 'empty';
                  $r5 = $row['r5'] ?? 'empty';
                  $r6 = $row['r6'] ?? 'empty';
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for MPC experiment in AeroShield.']));
                }
                break;

                case 'EMPCman':
                    // Fetch openloop-related data including the timer
                    $sql = "SELECT TIMER FROM AeroShield WHERE arduino_online_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('i', $arduino_online_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc(); // Fetch the result row
                    $timer = $row['TIMER'] ?? 'empty';

                    // Send experiment data back
                    echo "Experiment=$Experiment&timer=$timer";
                    flush();
                    
                    } else {
                        die(json_encode(['error' => 'No data found for MPC manual experiment in AeroShield.']));
                    }
                    break;

                default:
                    die(json_encode(['error' => 'Invalid experiment type.']));
            }
            break;
            
    case 'FloatShield':
        // Fetch the Experiment type from AeroShield using arduino_online_id
        $sql = "SELECT Experiment FROM FloatShield WHERE arduino_online_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $arduino_online_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $Experiment = $row['Experiment'];
        } else {
            die(json_encode(['error' => 'No matching entry found in FloatShield.']));
        }

        // Step 3: Handle logic based on the Experiment type
        switch ($Experiment) {
            case 'PID':
                // Fetch PID-related data including the timer
                $sql = "SELECT Kp, Ti, Td, r1, r2, r3, r4, r5, r6, T FROM FloatShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $Kp = $row['Kp'] ?? 'empty';
                    $Ti = $row['Ti'] ?? 'empty';
                    $Td = $row['Td'] ?? 'empty';
                    $r1 = $row['r1'] ?? 'empty';
                    $r2 = $row['r2'] ?? 'empty';
                    $r3 = $row['r3'] ?? 'empty';
                    $r4 = $row['r4'] ?? 'empty';
                    $r5 = $row['r5'] ?? 'empty';
                    $r6 = $row['r6'] ?? 'empty';
                    $timer2 = $row['T'] ?? 'empty';


                    // Send experiment data back
                    echo "Experiment=$Experiment&Kp=$Kp&Ti=$Ti&Td=$Td&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&period=$timer2";
                    flush();
                } else {
                    die(json_encode(['error' => 'No data found for PID experiment in FloatShield.']));
                }
                break;

            case 'openloop':
                // Fetch openloop-related data including the timer
                $sql = "SELECT TIMER FROM FloatShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for OpenLoop experiment in FloatShield.']));
                }
                break;
                
                case 'identification':
                // Fetch openloop-related data including the timer
                $sql = "SELECT r1, r2, r3, r4, r5, r6, TIMER FROM FloatShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $r1 = $row['r1'] ?? 'empty';
                  $r2 = $row['r2'] ?? 'empty';
                  $r3 = $row['r3'] ?? 'empty';
                  $r4 = $row['r4'] ?? 'empty';
                  $r5 = $row['r5'] ?? 'empty';
                  $r6 = $row['r6'] ?? 'empty';
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for Identification experiment in FloatShield.']));
                }
                break;
                
                case 'LQI':
                // Fetch openloop-related data including the timer
                $sql = "SELECT r1, r2, r3, r4, r5, r6, TIMER FROM FloatShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $r1 = $row['r1'] ?? 'empty';
                  $r2 = $row['r2'] ?? 'empty';
                  $r3 = $row['r3'] ?? 'empty';
                  $r4 = $row['r4'] ?? 'empty';
                  $r5 = $row['r5'] ?? 'empty';
                  $r6 = $row['r6'] ?? 'empty';
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for LQI experiment in FloatShield.']));
                }
                break;

                case 'LQIman':
                    // Fetch openloop-related data including the timer
                    $sql = "SELECT TIMER FROM FloatShield WHERE arduino_online_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('i', $arduino_online_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
    
                    if ($result->num_rows > 0) {
                      $row = $result->fetch_assoc(); // Fetch the result row
                      $timer = $row['TIMER'] ?? 'empty';
    
                      // Send experiment data back
                      echo "Experiment=$Experiment&timer=$timer";
                      flush();
                      
                    } else {
                        die(json_encode(['error' => 'No data found for LQI man experiment in FloatShield.']));
                    }
                    break;
                
                case 'EMPC':
                // Fetch openloop-related data including the timer
                $sql = "SELECT r1, r2, r3, r4, r5, r6, TIMER FROM FloatShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $r1 = $row['r1'] ?? 'empty';
                  $r2 = $row['r2'] ?? 'empty';
                  $r3 = $row['r3'] ?? 'empty';
                  $r4 = $row['r4'] ?? 'empty';
                  $r5 = $row['r5'] ?? 'empty';
                  $r6 = $row['r6'] ?? 'empty';
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for MPC experiment in FloatShield.']));
                }
                break;

                case 'EMPCman':
                    // Fetch openloop-related data including the timer
                    $sql = "SELECT TIMER FROM FloatShield WHERE arduino_online_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('i', $arduino_online_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc(); // Fetch the result row
                    $timer = $row['TIMER'] ?? 'empty';

                    // Send experiment data back
                    echo "Experiment=$Experiment&timer=$timer";
                    flush();
                    
                    } else {
                        die(json_encode(['error' => 'No data found for MPC manual experiment in FloatShield.']));
                    }
                    break;

                default:
                    die(json_encode(['error' => 'Invalid experiment type.']));
            }
            break;            

    case 'MagnetoShield':
        // Fetch the Experiment type from AeroShield using arduino_online_id
        $sql = "SELECT Experiment FROM MagnetoShield WHERE arduino_online_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $arduino_online_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $Experiment = $row['Experiment'];
        } else {
            die(json_encode(['error' => 'No matching entry found in MagnetoShield.']));
        }

        // Step 3: Handle logic based on the Experiment type
        switch ($Experiment) {
            case 'PID':
                // Fetch PID-related data including the timer
                $sql = "SELECT Kp, Ti, Td, r1, r2, r3, r4, r5, r6, T FROM MagnetoShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $Kp = $row['Kp'] ?? 'empty';
                    $Ti = $row['Ti'] ?? 'empty';
                    $Td = $row['Td'] ?? 'empty';
                    $r1 = $row['r1'] ?? 'empty';
                    $r2 = $row['r2'] ?? 'empty';
                    $r3 = $row['r3'] ?? 'empty';
                    $r4 = $row['r4'] ?? 'empty';
                    $r5 = $row['r5'] ?? 'empty';
                    $r6 = $row['r6'] ?? 'empty';
                    $timer2 = $row['T'] ?? 'empty';


                    // Send experiment data back
                    echo "Experiment=$Experiment&Kp=$Kp&Ti=$Ti&Td=$Td&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&period=$timer2";
                    flush();
                } else {
                    die(json_encode(['error' => 'No data found for PID experiment in MagnetoShield.']));
                }
                break;
                
                case 'identification':
                // Fetch openloop-related data including the timer
                $sql = "SELECT r1, r2, r3, r4, r5, r6, TIMER FROM MagnetoShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $r1 = $row['r1'] ?? 'empty';
                  $r2 = $row['r2'] ?? 'empty';
                  $r3 = $row['r3'] ?? 'empty';
                  $r4 = $row['r4'] ?? 'empty';
                  $r5 = $row['r5'] ?? 'empty';
                  $r6 = $row['r6'] ?? 'empty';
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for Identification experiment in MagnetoShield.']));
                }
                break;
                
                case 'LQI':
                // Fetch openloop-related data including the timer
                $sql = "SELECT r1, r2, r3, r4, r5, r6, TIMER FROM MagnetoShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $r1 = $row['r1'] ?? 'empty';
                  $r2 = $row['r2'] ?? 'empty';
                  $r3 = $row['r3'] ?? 'empty';
                  $r4 = $row['r4'] ?? 'empty';
                  $r5 = $row['r5'] ?? 'empty';
                  $r6 = $row['r6'] ?? 'empty';
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for LQI experiment in MagnetoShield.']));
                }
                break;

                case 'LQIman':
                    // Fetch openloop-related data including the timer
                    $sql = "SELECT TIMER FROM MagnetoShield WHERE arduino_online_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('i', $arduino_online_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
    
                    if ($result->num_rows > 0) {
                      $row = $result->fetch_assoc(); // Fetch the result row
                      $timer = $row['TIMER'] ?? 'empty';
    
                      // Send experiment data back
                      echo "Experiment=$Experiment&timer=$timer";
                      flush();
                      
                    } else {
                        die(json_encode(['error' => 'No data found for LQI man experiment in MagnetoShield.']));
                    }
                    break;
                
                case 'EMPC':
                // Fetch openloop-related data including the timer
                $sql = "SELECT r1, r2, r3, r4, r5, r6, TIMER FROM MagnetoShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $r1 = $row['r1'] ?? 'empty';
                  $r2 = $row['r2'] ?? 'empty';
                  $r3 = $row['r3'] ?? 'empty';
                  $r4 = $row['r4'] ?? 'empty';
                  $r5 = $row['r5'] ?? 'empty';
                  $r6 = $row['r6'] ?? 'empty';
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for MPC experiment in MagnetoShield.']));
                }
                break;

                case 'EMPCman':
                    // Fetch openloop-related data including the timer
                    $sql = "SELECT TIMER FROM MagnetoShield WHERE arduino_online_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('i', $arduino_online_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc(); // Fetch the result row
                    $timer = $row['TIMER'] ?? 'empty';

                    // Send experiment data back
                    echo "Experiment=$Experiment&timer=$timer";
                    flush();
                    
                    } else {
                        die(json_encode(['error' => 'No data found for MPC manual experiment in MagnetoShield.']));
                    }
                    break;

                default:
                    die(json_encode(['error' => 'Invalid experiment type.']));
            }
            break;

    case 'FurutaShield':
        // Fetch the Experiment type from AeroShield using arduino_online_id
        $sql = "SELECT Experiment FROM FurutaShield WHERE arduino_online_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $arduino_online_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $Experiment = $row['Experiment'];
        } else {
            die(json_encode(['error' => 'No matching entry found in MagnetoShield.']));
        }

        // Step 3: Handle logic based on the Experiment type
        switch ($Experiment) {
                
                case 'LQI':
                // Fetch openloop-related data including the timer
                $sql = "SELECT r1, r2, r3, r4, r5, r6, TIMER FROM FurutaShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $r1 = $row['r1'] ?? 'empty';
                  $r2 = $row['r2'] ?? 'empty';
                  $r3 = $row['r3'] ?? 'empty';
                  $r4 = $row['r4'] ?? 'empty';
                  $r5 = $row['r5'] ?? 'empty';
                  $r6 = $row['r6'] ?? 'empty';
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for LQI experiment in MagnetoShield.']));
                }
                break;
                
                case 'EMPC':
                // Fetch openloop-related data including the timer
                $sql = "SELECT r1, r2, r3, r4, r5, r6, Ksu, Kq, Kdq, Ke, TIMER FROM FurutaShield WHERE arduino_online_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $arduino_online_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc(); // Fetch the result row
                  $r1 = $row['r1'] ?? 'empty';
                  $r2 = $row['r2'] ?? 'empty';
                  $r3 = $row['r3'] ?? 'empty';
                  $r4 = $row['r4'] ?? 'empty';
                  $r5 = $row['r5'] ?? 'empty';
                  $r6 = $row['r6'] ?? 'empty';
                  $Ksu = $row['Ksu'] ?? 'empty';
                  $Kq = $row['Kq'] ?? 'empty';
                  $Kdq = $row['Kdq'] ?? 'empty';
                  $Ke = $row['Ke'] ?? 'empty';
                  $timer = $row['TIMER'] ?? 'empty';

                  // Send experiment data back
                  echo "Experiment=$Experiment&r1=$r1&r2=$r2&r3=$r3&r4=$r4&r5=$r5&r6=$r6&Ksu=$Ksu&Kq=$Kq&Kdq=$Kdq&Ke=$Ke&timer=$timer";
                  flush();
                  
                } else {
                    die(json_encode(['error' => 'No data found for MPC experiment in MagnetoShield.']));
                }
                break;

                default:
                    die(json_encode(['error' => 'Invalid experiment type.']));
            }
            break;

    default:
        die(json_encode(['error' => "Invalid model: $model"]));

}
// Close the statement and the database connection
$stmt->close();
$db->close();
?>


