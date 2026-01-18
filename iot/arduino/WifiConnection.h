#ifndef WIFI_CONNECTION_H
#define WIFI_CONNECTION_H
 
#include <WiFiNINA.h>
 
char ssid[] = "SSID";  // Replace with your network SSID
char password[] = "password";  // Replace with your network password
 
 
void connectToWiFi() {
  int status = WL_IDLE_STATUS;  // the WiFi radio's status
 
  // Check for the WiFi module:
  if (WiFi.status() == WL_NO_MODULE) {
    Serial.println("Communication with WiFi module failed!");
    while (true);  // don't continue
  }
 
  // Attempt to connect to WiFi network:
  while (status != WL_CONNECTED) {
    Serial.print("Attempting to connect to SSID: ");
    Serial.println(ssid);
    status = WiFi.begin(ssid, password);
 
    // Wait 10 seconds for connection:
    delay(5000);
  }
 
  Serial.println("--- Connected to WiFi network ---\n");
  //Serial.print("IP Address: ");
  //Serial.println(WiFi.localIP());
}
 
#endif