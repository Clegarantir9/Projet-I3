#include <Wire.h>
#include <math.h> 
#include "rgb_lcd.h"

#include "Arduino.h"

#include "DHT.h"
#include "Air_Quality_Sensor.h"
#include "SI114X.h"

#include <SPI.h>
#include <Ethernet.h>

#include <Multichannel_Gas_GMXXX.h>

//temp
#define DHTTYPE DHT22   // DHT 22  (AM2302)

#define DHTPIN 2    
DHT dht(DHTPIN, DHTTYPE);   // DHT22


#if defined(ARDUINO_ARCH_AVR)
    #define debug  Serial

#elif defined(ARDUINO_ARCH_SAMD) ||  defined(ARDUINO_ARCH_SAM)
    #define debug  SerialUSB
#else
    #define debug  Serial
#endif

#include "rgb_lcd.h"
//lcd
rgb_lcd lcd;

const int colorR = 200;
const int colorG = 200;
const int colorB = 200;

String scrollingMessage = "Projet I3   Groupe 5    FALLONI Paul  GAUTIER Benoit  LAURENT Arthur";

//airquality
AirQualitySensor airsensor(A2);

//sunlight

SI114X sunlight = SI114X();

//loudness
float loudness;
const int sampleWindow = 50;                              // Sample window width in mS (50 mS = 20Hz)
unsigned int sample;

//gas V2
#ifdef SOFTWAREWIRE
    #include <SoftwareWire.h>
    SoftwareWire myWire(3, 2);
    GAS_GMXXX<SoftwareWire> gas;
#else
    #include <Wire.h>
    GAS_GMXXX<TwoWire> gas;
#endif

static uint8_t recv_cmd[8] = {};

//eth
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };

EthernetClient client;

int    HTTP_PORT   = 80;
String HTTP_METHOD = "POST";
char   HOST_NAME[] = "surveillance-environnementale.unilasalle.fr";
String PATH_NAME   = "/post-arduino-data.php?";
String queryString = "";;
String apiKeyValue = "tPmAT5Ab3j7F9";


void setup() {
    lcd.begin(16, 2);
    
    
    Serial.begin(9600);
    while (!Serial);
    lcd.clear();     
    lcd.setCursor(0, 0);
    lcd.print("Waiting sensor to init...");
    Serial.println("Waiting sensor to init...");
    lcd.clear();
    delay(10000);
    lcd.clear();     
    lcd.setCursor(0, 0);
    lcd.print("10 sec restantes");
    Serial.println("10 sec restantes");
    delay(5000);
    lcd.clear();     
    lcd.setCursor(0, 0);
    lcd.print("5 sec restantes");
    Serial.println("5 sec restantes");
    delay(5000);
lcd.setRGB(colorR, colorG, colorB);
//temp
    Serial.println("DHT22 test!");
    Wire.begin();

    dht.begin();

//airquality
    if (airsensor.init()) {
        Serial.println("Air quality test!");
    } else {
        Serial.println("Sensor air ERROR!");
    }

//sunlight
    while (!sunlight.Begin()) {
        Serial.println("sunlight is not ready!");
        
    }
    Serial.println("sunlight is ready!");    



//loudness
    Serial.println("loudness is ready!"); 

//gaz V2

gas.begin(Wire, 0x08);
        Serial.println("gas is ready!");
//ethernet
  // initialize the Ethernet shield using DHCP:
  if (Ethernet.begin(mac) == 0) {
    Serial.println("Failed to obtaining an IP address using DHCP");
    
  }
    Serial.println("ethernet OK");

}

void scrollMessage(int row, String message, int delayTime, int totalColumns) {
  for (int i=0; i < totalColumns; i++) {
    message = " " + message;  
  } 
  message = message + " "; 
  for (int position = 0; position < message.length(); position++) {
    lcd.setCursor(0, row);
    lcd.print(message.substring(position, position + totalColumns));
    delay(delayTime);
  }
}

void sendbdd(String apiKeyValue,int quality,float dB,float tempe,float humi,int gaz1,int gaz2,int gaz3,int gaz4,int lumi,int uv ){
  // connect to web server on port 80:
  if(client.connect(HOST_NAME, HTTP_PORT)) {
    // if connected:
    Serial.println("Connected to server");
    //  make a HTTP request:send HTTP header
    PATH_NAME   = "/post-arduino-data.php?api_key=" + apiKeyValue +"&airquality="+quality+"&son="+ dB  +"&temp="+ tempe +"&humidite="+ humi + "&gaz1=" + gaz1 + "&gaz2=" + gaz2 + "&gaz3=" + gaz3 + "&gaz4=" + gaz4 + "&lumi=" + lumi + "&uv=" + uv + "";
    client.println(HTTP_METHOD + " " + PATH_NAME + " HTTP/1.1");
    client.println("Host: " + String(HOST_NAME));
    client.println("Connection: close");
    client.println(); // end HTTP header

    // send HTTP body
    client.println(queryString);

    while(client.connected()) {
      if(client.available()){
        // read an incoming byte from the server and print it to serial monitor:
        char c = client.read();
        Serial.print(c);
      }
    }

    // the server's disconnected, stop the client:
    client.stop();
    Serial.println();
    Serial.println("disconnected");
  } else {// if not connected:
    Serial.println("connection failed");
  }
}

void loop() {
  
    float temp_hum_val[2] = {0};
    // Reading temperature or humidity takes about 250 milliseconds!
    // Sensor readings may also be up to 2 seconds 'old' (its a very slow sensor)

    int quality = airsensor.slope();

    uint8_t len = 0;uint8_t addr = 0;uint8_t i;
    uint32_t gaz1 = 0;uint32_t gaz2 = 0;uint32_t gaz3 = 0;uint32_t gaz4 = 0;
    float tempe;float humi;int lumi;int uv;int ir;

Serial.println("<-------------------------------------------------->");

//temp
Serial.println("");
    if (!dht.readTempAndHumidity(temp_hum_val)) {
        Serial.print("Humidity: ");
        humi=temp_hum_val[0] ;
        Serial.print(humi);
        Serial.print(" %\t");
        Serial.print("Temperature: ");
        tempe= temp_hum_val[1];
        Serial.print(tempe);
        Serial.println(" *C");
    } else {
        Serial.println("Failed to get temprature and humidity value.");
    }
  lcd.clear(); 
  
  lcd.setCursor(0, 1);

  
  if(tempe<17){
     lcd.setRGB(100, 100, 255);
  }else if(tempe>30){
     lcd.setRGB(255, 50,0); 
  }else { 
     lcd.setRGB(150, 255,0);
  }
  lcd.print("Temperature ");lcd.print(tempe);lcd.print((char)223);lcd.print("C");
  //lcd.print("Temperature ");lcd.print(round(temp_hum_val[1]));lcd.print((char)223);lcd.print("C");
  scrollMessage(0, "Projet I3   Groupe 5", 150, 16);

  lcd.clear(); 
    if(humi<25 || humi>75){
     lcd.setRGB(255, 50,0); 
  }else{ 
    lcd.setRGB(150, 255,0);
  }
  lcd.setCursor(0, 1);
  lcd.print("Humidite ");lcd.print(humi);lcd.print((char)37);
   scrollMessage(0, "FALLONI Paul  GAUTIER Benoit  LAURENT Arthur", 150, 16);
  
  lcd.clear(); 
  delay(400);

//airquality
String air;
Serial.println("");
Serial.print("Sensor value: ");
    Serial.print(airsensor.getValue());
    Serial.print(" \t");
   lcd.setCursor(0, 1); 
    if (quality == 0) {
     air= "Air tres pollué!";
     lcd.setRGB(255, 0,0);
    } else if (quality == 1) {
      air= "Air tres pollué!";
      lcd.setRGB(255, 0,0);      
    } else if (quality == 2) {
      air= "Air peu pollue";
      lcd.setRGB(255, 50,0);    
    } else if (quality == 3) {
      air= "Air frais";
      lcd.setRGB(150, 255,0);    
    }
  lcd.setCursor(0, 1);
    
  Serial.println(air);
  lcd.setCursor(0, 1);
  lcd.print(air);
  scrollMessage(0, scrollingMessage, 150, 16);
  delay(400);
   lcd.clear(); 
  
//sunlight
Serial.println("");
    Serial.print("Vis: ");
    lumi = round(sunlight.ReadVisible());
    Serial.print(lumi);
    Serial.print(" \t");
    ir = sunlight.ReadIR();
    Serial.print("IR: "); Serial.print(ir);
    Serial.print(" \t");
    //the real UV value must be div 100 from the reg value , datasheet for more information.
    uv= round((float)sunlight.ReadUV() / 100);
    Serial.print("UV: ");  Serial.println(uv);

  lcd.clear(); 
    
     lcd.setRGB(200, 200, 200);

  lcd.setCursor(0, 1);
  
  lcd.print("Luminosite ");lcd.print(lumi);lcd.print("lux");
  scrollMessage(0, "Projet I3   Groupe 5", 150, 16);

  lcd.clear(); 
  
  lcd.setCursor(0, 1);
   if(uv<6){
     lcd.setRGB(200, 200, 200);
  }else {
     lcd.setRGB(255, 50,0); 
  }
  lcd.print("Luminosite ");lcd.print(uv);lcd.print(" UV");
   scrollMessage(0, "FALLONI Paul  GAUTIER Benoit  LAURENT Arthur", 150, 16);
  
  lcd.clear(); 
  delay(400);
//loudness
Serial.println("");

    unsigned long startMillis= millis();                   // Start of sample window
   float peakToPeak = 0;                                  // peak-to-peak level
 
   unsigned int signalMax = 0;                            //minimum value
   unsigned int signalMin = 1024;                         //maximum value
 
                                                          // collect data for 50 mS
   while (millis() - startMillis < sampleWindow)
   {
      sample = analogRead(0);                    //get reading from microphone
      if (sample < 1024)                                  // toss out spurious readings
      {
         if (sample > signalMax)
         {
            signalMax = sample;                           // save just the max levels
         }
         else if (sample < signalMin)
         {
            signalMin = sample;                           // save just the min levels
         }
      }
   }
 
   peakToPeak = signalMax - signalMin;                    // max - min = peak-peak amplitude
   float dB = map(peakToPeak,20,900,30,90);             //calibrate for deciBels

   lcd.setRGB(200, 200, 200);
   
 Serial.print(dB);Serial.println(" dB");
 lcd.setCursor(0, 1);
  lcd.print("Volume ");lcd.print(dB);lcd.print(" dB");
  scrollMessage(0, scrollingMessage, 150, 16);
  delay(400);
  lcd.clear(); 

//gaz V2
Serial.println("");   
    gaz2 = gas.measure_NO2(); Serial.print("NO2: "); Serial.print(gaz2); Serial.print("  =  ");
    Serial.print(gas.calcVol(gaz2)); Serial.println("V");

    gaz3 = gas.measure_C2H5OH(); Serial.print("C2H5OH: "); Serial.print(gaz3); Serial.print("  =  ");
    Serial.print(gas.calcVol(gaz3)); Serial.println("V");

    gaz4 = gas.measure_VOC(); Serial.print("VOC: "); Serial.print(gaz4); Serial.print("  =  ");
    Serial.print(gas.calcVol(gaz4)); Serial.println("V");
 
    gaz1 = gas.measure_CO(); Serial.print("CO: "); Serial.print(gaz1/10); Serial.print("  =  ");
    Serial.print(gas.calcVol(gaz1)); Serial.println("V");
    
   lcd.setCursor(0, 1);
   lcd.print("CO     ");lcd.print(gaz1);lcd.print(" ppm");
  scrollMessage(0, "Projet I3   Groupe 5", 150, 16);
  lcd.clear(); 
  
  lcd.setCursor(0, 1);
  lcd.print("NO2     ");lcd.print(gaz2);lcd.print(" ppm");
  scrollMessage(0, "FALLONI Paul  GAUTIER Benoit  LAURENT Arthur", 150, 16);
  lcd.clear(); 
  delay(400);

     
  lcd.setCursor(0, 1);
  lcd.print("C2H50H  ");lcd.print(gaz3);lcd.print(" ppm");
  scrollMessage(0, "Projet I3   Groupe 5", 150, 16);
  lcd.clear();
  
  lcd.setCursor(0, 1);
  lcd.print("VOC     ");lcd.print(gaz4);lcd.print(" ppm");
  scrollMessage(0, "FALLONI Paul  GAUTIER Benoit  LAURENT Arthur", 150, 16);
  lcd.clear(); 

    Serial.println("");
delay(50);

sendbdd(apiKeyValue,quality, dB,tempe,humi,gaz1,gaz2,gaz3,gaz4,lumi,uv );
}
