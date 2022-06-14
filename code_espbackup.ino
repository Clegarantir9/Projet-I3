
  #include <WiFi.h>
  #include <HTTPClient.h>


#include <Wire.h>

// Replace with your network credentials
const char* ssid     = "Livebox-44F4";
const char* password = "FAjqqds94jYPG7yLdb";
//const char* ssid     = "Honor";
//const char* password = "Esieepaul1234";

// REPLACE with your Domain name and URL path or IP address with path
const char* serverName = "http://surveillance-environnementale.unilasalle.fr/post-esp-data.php";
String apiKeyValue = "tPmAT5Ab3j7F9";


#include "Adafruit_VL53L0X.h"

// address we will assign if dual sensor is present
#define LOX1_ADDRESS 0x30
#define LOX2_ADDRESS 0x31
int sensor1,sensor2;


// set the pins to shutdown
#define SHT_LOX1 2
#define SHT_LOX2 4

// objects for the vl53l0x
Adafruit_VL53L0X lox1 = Adafruit_VL53L0X();
Adafruit_VL53L0X lox2 = Adafruit_VL53L0X();

// this holds the measurement
VL53L0X_RangingMeasurementData_t measure1;
VL53L0X_RangingMeasurementData_t measure2;


void setID() {
  // all reset
  digitalWrite(SHT_LOX1, LOW);    
  digitalWrite(SHT_LOX2, LOW);
  delay(10);
  // all unreset
  digitalWrite(SHT_LOX1, HIGH);
  digitalWrite(SHT_LOX2, HIGH);
  delay(10);

  // activating LOX1 and reseting LOX2
  digitalWrite(SHT_LOX1, HIGH);
  digitalWrite(SHT_LOX2, LOW);

  // initing LOX1
  if(!lox1.begin(LOX1_ADDRESS)) {
    Serial.println(F("Failed to boot first VL53L0X"));
    while(1);
  }
  delay(10);

  // activating LOX2
  digitalWrite(SHT_LOX2, HIGH);
  delay(10);

  //initing LOX2
  if(!lox2.begin(LOX2_ADDRESS)) {
    Serial.println(F("Failed to boot second VL53L0X"));
    while(1);
  }
}

int read_dual_sensors(int io) {
  int out= 0;

  lox1.rangingTest(&measure1, false); // pass in 'true' to get debug data printout!
  lox2.rangingTest(&measure2, false); // pass in 'true' to get debug data printout!

    sensor1 = measure1.RangeMilliMeter;
   
    if(sensor1 < 800){  
      Serial.print("1: ");
      Serial.print(sensor1);
      Serial.print("mm"); 

      io=1;

    }else{ 
      out = out + 1;
    }
 sensor2 = measure2.RangeMilliMeter;
    
    if(sensor2 < 800){ 
      Serial.print("2: ");
      Serial.print(sensor2);
      Serial.print("mm"); 

      io=-1;

    }else{ 
      out = out + 1;
    } 
if (out == 2){
   Serial.println(".");
   return io = 0;
}else{
  Serial.println();
}
  return io;
}

void sendbdd(int mem){


if(WiFi.status()== WL_CONNECTED){
    WiFiClient client;
    HTTPClient http;

  
    http.begin(client, "http://surveillance-environnementale.unilasalle.fr/post-esp-data.php?api_key=" + apiKeyValue + "&io=" + mem);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
     int httpResponseCode = http.POST("");

      String response = http.getString();
      
      
    if (httpResponseCode>0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
      Serial.println(response);
    }
    else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
    // Free resources
    http.end();
  }
  else {
    Serial.println("WiFi Disconnected");
  }
}

void setup() {
  Serial.begin(115200);
  while (! Serial) { delay(1); }

  pinMode(SHT_LOX1, OUTPUT);
  pinMode(SHT_LOX2, OUTPUT);

  Serial.println("Shutdown pins inited...");

  digitalWrite(SHT_LOX1, LOW);
  digitalWrite(SHT_LOX2, LOW);

  Serial.println("Both in reset mode...(pins are low)");
  
  
  Serial.println("Starting...");
  setID();
  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
 
}

void loop() {
    int io=0,mem;  

    io = read_dual_sensors(io);
    mem = io;
    while(io != 0){
        io = read_dual_sensors(io);
        
    }
    if(mem == -1 || mem == 1){
        sendbdd(mem);    
    }
  
}
