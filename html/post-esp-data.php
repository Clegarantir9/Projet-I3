<?php

$hName = "10.199.132.11";//host name

// REPLACE with your Database name
$dbname = "grp5pabdb";
// REPLACE with Database user
$username = "grp5pab";
// REPLACE with Database user password
$password = "wKtQ898v";


$api_key_value = "tPmAT5Ab3j7F9";//cle de securité


$api_key= $io= "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key1 = test_input($_GET["api_key"]);
   // echo $api_key1;
    if($api_key1 == $api_key_value) {
        $io = test_input($_GET["io"]);
        
        // Create connection
        $conn = new mysqli($hName, $username, $password, "$dbname");
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        
        $sql = "INSERT INTO data_esp (io)
        VALUES ('" . $io . "')";
        
        if ($conn->query($sql) === TRUE) {
            if($io == 1 ){
            echo "Nouvelle entrée crée";
           }else if($io == -1){
            echo "Nouvelle sortie crée";

           }else{
            echo "Nouvelle".$io. "crée";

           }
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