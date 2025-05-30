
SerialMonitor Application

This is a Python-based GUI application for monitoring and collecting serial data from an Arduino device. It displays real-time data, stores it in a `.mat` file for MATLAB, and sends data batches to a remote server.

🚀 Build Instructions

This repository does not include a pre-built version of the application (e.g., no `.exe` or compiled app). You are expected to build the application yourself.

To run the application:

1. Make sure you have Python 3 installed.
2. Install the required dependencies:
   pip install pyserial requests scipy
3. Run the app using:
   python SerialMonitor.py

❗ Why no pre-built app?

To keep the repository clean and avoid uploading unnecessary binaries, the compiled version is not included. The code is ready to be built or packaged (e.g., with `pyinstaller`) if needed.
