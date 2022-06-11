<?php

$hName = "10.199.132.11";//host name

// REPLACE with your Database name
$dbname = "grp5pabdb";
// REPLACE with Database user
$username = "grp5pab";
// REPLACE with Database user password
$password = "wKtQ898v";


$api_key_value = "tPmAT5Ab3j7F9";//cle de securité


$api_key= $airquality = $son = $temp = $humidite = $gaz1 = $gaz2 = $gaz3 = $gaz4 = $lumi = $uv = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key1 = test_input($_GET["api_key"]);
   // echo $api_key1;
    if($api_key1 == $api_key_value) {
        $airquality = test_input($_GET["airquality"]);
        $son = test_input($_GET["son"]);
        $temp = test_input($_GET["temp"]);
        $humidite = test_input($_GET["humidite"]);
        $gaz1 = test_input($_GET["gaz1"]);
        $gaz2 = test_input($_GET["gaz2"]);
        $gaz3 = test_input($_GET["gaz3"]);
        $gaz4 = test_input($_GET["gaz4"]);
        $lumi = test_input($_GET["lumi"]);
        $uv = test_input($_GET["uv"]);
        
        
        // Create connection
        $conn = new mysqli($hName, $username, $password, "$dbname");
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        
        $sql = "INSERT INTO data_arduino (airquality, son, temp, humidite, gaz1,gaz2,gaz3,gaz4,lumi,uv ) VALUES ('" . $airquality . "','" . $son . "','" . $temp . "','" . $humidite . "','" . $gaz1 . "','" . $gaz2 . "','" . $gaz3 . "','" . $gaz4 . "','" . $lumi . "','" . $uv . "');";
        
        if ($conn->query($sql) === TRUE) {
            
            echo "Nouvelle entrée crée";
        } 
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    
        $conn->close();
    }
    else {
        echo "Wrong API Key provided.";
    }

}
else {
    echo "No data posted with HTTP POST.";
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}