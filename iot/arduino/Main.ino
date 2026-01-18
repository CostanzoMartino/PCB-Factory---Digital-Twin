#include <WiFiNINA.h>         // WiFi library for communication
#include "WiFiConnection.h"   // Custom WiFi connection library (SSID + Password)
#include <Adafruit_Sensor.h>  // Adafruit sensor library
#include <DHT.h>              // DHT sensor library
#include <ArduinoJson.h>
#include <ArduinoHttpClient.h>

const char serverName[] = "yourServerAddress"; // Server address
int port = 80; // Your http port

WiFiClient wifi;
HttpClient client = HttpClient(wifi, serverName, port);

const int AVOIDANCE_PIN = 8;  // Digital pin connected to the Avoidance sensor
const int DHT_PIN = 12;       // Digital pin connected to the DHT sensor
const int CARBON_PIN_AO = 20; // Analog pin connected to the Carbon Monoxide sensor (MQ9)
const int CARBON_PIN_DO = 21; // Digital pin connected to the Carbon Monoxide sensor (MQ9)
const int BLUE_LED_PIN = 2;   // Digital pin connected to the RGB Blue LED
const int GREEN_LED_PIN = 3;  // Digital pin connected to the RGB Green LED
const int RED_LED_PIN = 4;    // Digital pin connected to the RGB Red LED
const int BUZZER_PIN = 14;    // Digital pin connected to the Buzzer

#define DHT_TYPE DHT11 // DHT 11 sensor type
DHT dht(DHT_PIN, DHT_TYPE); // DHT sensor initialization

/* Declare sensors readings */
int avoidanceState = HIGH;          // State of the Avoidance sensor (HIGH = No obstacle detected | LOW = Obstacle detected)
float DHTData[2] = { 0.00, 0.00 };  // Store temperature and humidity reading
float carbonLevel = 0.00;           // Store Carbon Monoxide sensor reading
bool isBlueLedOn = false;           // Track blue LED state
bool isGreenLedOn = false;          // Track green LED state
bool isRedLedOn = false;            // Track red LED state
int cameraId = 1;                   // Store the Id of the room in which sensors are located
bool isFirstLoop = true;            // Allow to initialize timers only the first time loop() starts

/* Sensors reading intervals */
unsigned int lastDHTReadTime = 0;              // Last time the DHT sensor was read
unsigned int lastCarbonReadTime = 0;           // Last time the Carbon Monoxide sensor was read
const unsigned int DHTReadInterval = 5000;     // Interval for DHT readings in milliseconds
const unsigned int carbonReadInterval = 5000;  // Interval for Carbon Monoxide readings in milliseconds

/* Triggers and timer to send data to db */
unsigned int avoidanceLastTrigger = 0;         // Last time the Avoidance sensor was read
unsigned int DHTLastTrigger = 0;               // Last time DHT sensor was triggered
unsigned int carbonLastTrigger = 0;            // Last time Carbon Monoxide sensor was triggered
const unsigned int avoidanceTimerToDB = 3000;  // Interval for Avoidance readings in milliseconds
const unsigned int DHTTimerToDB = 20000;       // Interval for DHT sensor to send data to db
const unsigned int carbonTimerToDB = 20000;    // Interval for Carbon Monoxide sensor to send data to db

unsigned int DHTReadCount = 0;                 // Count number of readings of DHT sensor
unsigned int carbonReadCount = 0;              // Count number of readings of Carbon Monoxide sensor
const unsigned int DHTReadTimes = DHTTimerToDB / DHTReadInterval; // Max number of readings
const unsigned int carbonReadTimes = carbonTimerToDB / carbonReadInterval; // Max number of readings


void setup() {
  Serial.begin(9600);

  connectToWiFi();

  pinMode(AVOIDANCE_PIN, INPUT);
  pinMode(CARBON_PIN_AO, INPUT);
  pinMode(CARBON_PIN_DO, INPUT);
  pinMode(BLUE_LED_PIN, OUTPUT);
  pinMode(GREEN_LED_PIN, OUTPUT);
  pinMode(RED_LED_PIN, OUTPUT);
  pinMode(BUZZER_PIN, OUTPUT);

  dht.begin();  // Initialize DHT sensor

  delay(2000);  // Allow sensors to stabilize
}

void loop() {
  unsigned long currentMillis = millis();  // Get the current time
  if (isFirstLoop) {
    avoidanceLastTrigger = currentMillis;
    DHTLastTrigger = currentMillis;
    carbonLastTrigger = currentMillis;
    isFirstLoop = false;
  }

  if (currentMillis - avoidanceLastTrigger >= avoidanceTimerToDB) {
    avoidanceState = readAvoidanceSensor();

    // Build the POST request
    String postData = "sensorId=1&movimento=" + String(avoidanceState);
    // Send data to database
    sendData(postData);

    avoidanceLastTrigger = currentMillis;  // Update the last read time
  }
  if (currentMillis - DHTLastTrigger >= DHTTimerToDB && DHTReadCount == DHTReadTimes) {
    float avgTemp = DHTData[0] / DHTReadTimes;
    float avgHum = DHTData[1] / DHTReadTimes;

    // Build the POST request
    String postData = "sensorId=2&temperatura=" + String(avgTemp) +
                      "&sensorId2=3&umidita=" + String(avgHum);
    // Send data to database
    sendData(postData);

    //Serial.println("Somma letture temperatura: " + String(DHTData[0]) + "°C");
    //Serial.println("Somma letture umidità: " + String(DHTData[1]) + "%");
    Serial.println("Temperatura media in " + String(DHTTimerToDB/1000) + " secondi: " + String(avgTemp) + " °C");
    Serial.println("Umidità media in " + String(DHTTimerToDB/1000) + " secondi: " + String(avgHum) + " %");

    DHTData[0] = 0; // Reset array to store new temperature values
    DHTData[1] = 0; // Reset array to store new humidity values
    DHTReadCount = 0;
    DHTLastTrigger = currentMillis; // Update the last read time
  } else {
    readDhtSensor(currentMillis, DHTData);
  }
  if (currentMillis - carbonLastTrigger >= carbonTimerToDB && carbonReadCount == carbonReadTimes) {
    float avgCarbonLevel = carbonLevel / carbonReadTimes;
    String avgCarbonLevelStr = String(avgCarbonLevel, 2);

    // Build the POST request
    String postData = "sensorId=4&monossido_di_carbonio=" + avgCarbonLevelStr;
    // Send data to database
    sendData(postData);

    //Serial.println("\nSomma letture livello di carbonio: " + String(carbonLevel) + " ppm");
    Serial.println("Livello medio di Monossido di Carbonio in " + String(carbonTimerToDB/1000) + " secondi: " + avgCarbonLevelStr + " ppm");
    Serial.println("\n--------------------------------------------------------\n");

    carbonLevel = 0; // Reset variable to store new Carbon Monoxide levels
    carbonReadCount = 0;
    carbonLastTrigger = currentMillis; // Update the last read time
  } else {
    carbonLevel += readCarbonSensor(currentMillis);
  }
}

int readAvoidanceSensor() {
  int count = 0;
  const int readings = 10;
  for (int i = 0; i < readings; i++) {
    if (digitalRead(AVOIDANCE_PIN) == LOW) {
      // Obstacle detected
      count++;
    }
    delay(50);
  }
  // Check if at least half readings detected an obstacle
  if (count >= readings / 2) {
    //Serial.println("Avoidance Sensor - Persona rilevata");
    digitalWrite(BLUE_LED_PIN, HIGH);
    delay(1000);
    digitalWrite(BLUE_LED_PIN, LOW);
    return 1;
  } else {
    //Serial.println("Avoidance Sensor - Persona non rilevata");
    return 0;
  }
}

void readDhtSensor(unsigned int currentMillis, float* data) {
  // Read the DHT sensor every "DHTReadInterval" seconds
  if (currentMillis - lastDHTReadTime >= DHTReadInterval) {
    float temperature = dht.readTemperature();
    float humidity = dht.readHumidity();

    // Check if any reads failed
    if (isnan(temperature) || isnan(humidity)) {
      Serial.println("Lettura dal sensore DHT fallita");
    } else {
      //Serial.println("Temperatura istantanea: " + String(temperature) + "°C");
      //Serial.println("Umidità istantanea: " + String(humidity) + "%");

      // Activate buzzer if temperature is too high
      if (temperature >= 35 || humidity >= 70) {
        tone(BUZZER_PIN, 500);
      } else {
        noTone(BUZZER_PIN);
      }

      data[0] += temperature;
      data[1] += humidity;
      DHTReadCount++; // Increment the counter for each successful reading
    }
    lastDHTReadTime = currentMillis;
  }
}

float readCarbonSensor(unsigned int currentMillis) {
  // Read the Carbon Monoxide sensor every "carbonReadInterval" seconds
  if (currentMillis - lastCarbonReadTime >= carbonReadInterval) {
    lastCarbonReadTime = currentMillis;
    int dataRead = analogRead(CARBON_PIN_AO);
    int threshold = digitalRead(CARBON_PIN_DO);
    float ppm = mapFloat(dataRead, 0, 1023, 0, 4.5);

    lastCarbonReadTime = currentMillis;
    carbonReadCount++;

    // Check Carbon Monoxide level and update LEDs accordingly
    if (threshold == LOW) {
      Serial.println("Concentrazione alta di Monossido di Carbonio nell'aria! | " + String(ppm) + " ppm");
      if (!isRedLedOn) {
        turnRedLedOn();
        isRedLedOn = true;
        isGreenLedOn = false;
        isBlueLedOn = false;
      }
    } else {
      Serial.println("Concentrazione di Monossido di Carbonio nell'aria nella norma | " + String(ppm) + " ppm");
      if (!isGreenLedOn) {
        turnGreenLedOn();
        isRedLedOn = false;
        isGreenLedOn = true;
        isBlueLedOn = false;
      }
    }
    //Serial.println("AnalogRead: " + String(dataRead));
    //Serial.println("ppm: " + String(ppm));
    return ppm;
  }
}

float mapFloat(float x, float in_min, float in_max, float out_min, float out_max) {
  return (x - in_min) * (out_max - out_min) / (in_max - in_min) + out_min;
}

void sendData(String postData) {
  //Serial.println("Inviando i dati al database...");
  
  // Creazione del JSON
  JsonDocument doc;
  doc["cameraId"] = cameraId;

  // Analisi della stringa postData per estrarre le chiavi e i valori
  int separatorPos;
  while (postData.length() > 0) {
    separatorPos = postData.indexOf('&');
    String pair = (separatorPos == -1) ? postData : postData.substring(0, separatorPos);
    int equalPos = pair.indexOf('=');
    String key = pair.substring(0, equalPos);
    String value = pair.substring(equalPos + 1);
    doc[key] = value;
    postData = (separatorPos == -1) ? "" : postData.substring(separatorPos + 1);
  }

  // Converti il documento JSON in una stringa
  String jsonString;
  serializeJson(doc, jsonString);

  String contentType = "application/json";

  client.post("/YOUR-PATH/gateway.php?nomeServizio=insertMisurazione", contentType, jsonString);

  
  // Read the status code and body of the response
  int statusCode = client.responseStatusCode();
  //Serial.print("Status code: ");
  //Serial.println(statusCode);
  String response = client.responseBody();
  //Serial.print("Response: ");
  //Serial.println(response);
}

void turnBlueLedOn() {
  digitalWrite(BLUE_LED_PIN, HIGH);
  digitalWrite(GREEN_LED_PIN, LOW);
  digitalWrite(RED_LED_PIN, LOW);
}

void turnGreenLedOn() {
  digitalWrite(BLUE_LED_PIN, LOW);
  digitalWrite(GREEN_LED_PIN, HIGH);
  digitalWrite(RED_LED_PIN, LOW);
}

void turnRedLedOn() {
  digitalWrite(BLUE_LED_PIN, LOW);
  digitalWrite(GREEN_LED_PIN, LOW);
  digitalWrite(RED_LED_PIN, HIGH);
}