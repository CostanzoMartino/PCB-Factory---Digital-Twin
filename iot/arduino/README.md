# Arduino IoT Environmental Monitoring Firmware

This firmware runs on an **Arduino Nano 33 IoT** and collects environmental data inside a PCB manufacturing area.

It reads multiple sensors, controls actuators, and sends JSON data to a backend API using HTTP.

---

## Features

- Temperature & Humidity monitoring (DHT11)  
- Carbon Monoxide detection (MQ-9)  
- Operator presence detection (Avoidance sensor)  
- RGB LED and Buzzer alerts  
- WiFi connectivity  
- JSON data transmission to REST API  
- Configurable sampling and transmission intervals  

---

## Hardware Requirements

- Arduino Nano 33 IoT  
- DHT11 sensor  
- MQ-9 Gas Sensor  
- Avoidance / IR Sensor  
- RGB LED  
- Passive Buzzer  

---

## Configuration

Before uploading the firmware, configure the following:


const char serverName[] = "<YOUR_SERVER_IP_OR_DOMAIN>";
int port = 80;

client.post("/<YOUR-PATH>/gateway.php?nomeServizio=insertMisurazione", contentType, jsonString);

Also configure your WiFi credentials in:
WiFiConnection.h

