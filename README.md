# AutomationShield Online

**AutomationShield Online** is the result of a diploma thesis at the Slovak University of Technology in Bratislava. It presents a modular system for controlling didactic **AutomationShield** devices remotely through a modern **web interface**, using the **Arduino UNO R4 WiFi** microcontroller.

## 📘 Project Overview

This project extends the [AutomationShield initiative](https://github.com/gergelytakacs/AutomationShield/wiki), which provides open-source, low-cost educational hardware for experimenting with control systems like PID, LQ, and MPC on various physical models.

The goal of this thesis was to lower the barrier of entry for students and educators by enabling control and visualization of AutomationShield experiments directly from a browser—without the need to recompile and upload Arduino code for every change.

The entire system is currently deployed and running at:

🔗 [https://mrsolutions.sk/automationshield/index.php](https://mrsolutions.sk/automationshield/index.php)

This live version includes full functionality along with complete tutorials, multilingual interface, model selection, real-time data visualization, and more.

✅ **Free to use** – available for educational, testing, or demonstration purposes without restrictions.

## 🎯 Main Features

- Full-stack system for web-based control of Arduino-connected AutomationShield devices
- Modular, scalable architecture using Arduino UNO R4 WiFi and PHP backend
- GUI for selecting control types and entering experiment parameters
- Data logging and visualization through responsive graphs
- Language switching (multilingual support)
- Light/Dark mode and mobile-friendly design
- Integration of multiple AutomationShield models (e.g. AeroShield, FloatShield, MagnetoShield, FurutaShield)
- Built-in user tutorials and documentation

## ✅ Supported Features by Device

| **Method**           | **AeroShield** | **FloatShield** | **MagnetoShield** | **FurutaShield** |
|----------------------|----------------|------------------|-------------------|------------------|
| **PID**              | ✅              | ✅                | ✅                 | ❌               |
| **MPC**              | EMPC           | MPC+Kal          | EMPC              | EMPC             |
| **MPC Manual**       | ✅              | ✅                | ✅                 | ❌               |
| **LQ**               | LQI            | LQ+Kal           | LQ                | LQR              |
| **LQ Manual**        | ✅              | ✅                | ✅                 | ❌               |
| **Identification**   | ✅              | ❌                | ❌                 | ❌               |
| **Closed-loop ID**   | ❌              | ✅                | ✅                 | ❌               |
| **Open-loop**        | ✅              | ✅                | ❌                 | ❌               |


## 🧠 Educational Impact

This system is intended to support interactive learning in automation and control engineering. It can be used in both university and secondary school environments to help students learn the fundamentals of control theory through hands-on experiments—without requiring deep programming knowledge.

## 📎 Related Projects

- [AutomationShield Wiki](https://github.com/gergelytakacs/AutomationShield/wiki) – official documentation and background
- [AutomationShield GitHub](https://github.com/gergelytakacs/AutomationShield) – main codebase and hardware library

## 📄 Thesis Info

**Title:** Modular System for Controlling AutomationShield Devices via a Web Interface  
**Author:** Matúš Repka  
**Institution:** Slovak University of Technology in Bratislava  
**Year:** 2025
📄 [Read full thesis (PDF, Slovak)](https://github.com/MatusRepkaSolutions/AutomationShieldOnline/blob/main/Publications/Slovak/AutomationShieldOnline.pdf)

## 🛠 Technologies Used

- Arduino UNO R4 WiFi
- C++ (Arduino)
- PHP (backend)
- MySQL (database)
- JavaScript, HTML/CSS (frontend)
