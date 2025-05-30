<?php
$servername = "sql720.your-server.de";
$username = "mrsolu_1";
$password = "xjPB7XU2TK899KQX";
$dbname = "mrepka";

// Create connection
$db = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}?>