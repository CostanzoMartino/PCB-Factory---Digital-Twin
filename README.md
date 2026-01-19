# PCB-Factory---Digital-Twin

# Digital Twin System for PCB Manufacturing

## Overview  
This project implements a **Digital Twin** of an industrial **Printed Circuit Board (PCB)** manufacturing process.  
The system integrates a **FlexSim 3D simulation** with a real-time **IoT infrastructure** to monitor environmental conditions and optimize production workflows.

A **microservices-based, event-driven architecture** is used to collect, process, and expose sensor data through REST APIs and a web dashboard.

---

## Key Features  
- Real-time IoT environmental monitoring  
- Event-driven microservices architecture  
- REST API Gateway for data management  
- FlexSim 3D industrial simulation  
- Web dashboard with analytics  
- Process optimization through simulation  

---

## System Architecture  

The platform is structured into four layers:

1. **IoT Layer**  
   Arduino Nano 33 IoT with DHT11, MQ-9, and Avoidance sensors for data acquisition and actuator control.

2. **Backend Layer**  
   PHP-based microservices with an API Gateway and MySQL database for data ingestion and retrieval.

3. **Application Layer**  
   FlexSim simulation environment and web dashboard for visualization and analysis.

4. **Data Layer**  
   Centralized relational database storing time-series measurements and metadata.

---

## Technologies Used  

- **Backend:** PHP, REST APIs  
- **Frontend:** HTML, CSS  
- **Simulation:** FlexSim  
- **IoT:** Arduino Nano 33 IoT  
- **Database:** MySQL  
- **Communication:** JSON, HTTP  
- **Architecture:** Microservices, Event-Driven  

---

## Author
Costanzo Martino + [LinkedIn ](https://www.linkedin.com/in/costanzomartino/)

