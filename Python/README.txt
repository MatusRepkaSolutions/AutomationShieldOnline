
SerialMonitor Application

This is a Python-based GUI application for monitoring and collecting serial data from an Arduino device. It displays real-time data, stores it in a `.mat` file for MATLAB, and sends data batches to a remote server.

🚀 Build Instructions

This repository does not include a pre-built version of the application (e.g., no `.exe` or compiled app). You are expected to build the application yourself.

To run the application:

1. Make sure you have Python 3 installed.
2. Install the required dependencies:
   pip install -r requirements.txt
3. Run the app using:
   python SerialMonitor.py

macOS Apple Silicon build:

1. Install Python and Tkinter (Homebrew example):
   brew install python python-tk@3.14
2. Create and activate a virtual environment, then install dependencies:
   python3 -m venv .venv
   source .venv/bin/activate
   pip install -r requirements.txt
3. From this Python directory, build the native arm64 app:
   pyinstaller --clean --noconfirm SerialMonitor.spec
4. The application will be available at:
   dist/SerialMonitor.app

📦 Pre-built macOS app

The Apple Silicon DMG is available at:
website/automationshield/file/SerialMonitor-macOS-Apple-Silicon.dmg

It supports M1, M2, M3, M4, and newer Apple Silicon Macs. The app is
distributed without an Apple Developer ID signature or notarization.
