import serial
import requests
import tkinter as tk
from tkinter import ttk
from serial.tools import list_ports
import scipy.io
import threading


class SerialMonitorApp:
    def __init__(self, root):
        self.root = root
        self.root.title("AutomationShield Serial Monitor")
        self.root.configure(bg="#57AB59")
        self.root.protocol("WM_DELETE_WINDOW", self.on_close)

        # UI Elements
        self.com_label = tk.Label(root, text="Select serial port:", bg="#57AB59", fg="white")
        self.com_label.pack(pady=5)

        self.com_menu = ttk.Combobox(root, state="readonly", width=45)
        self.com_menu.pack(pady=5)

        self.refresh_button = tk.Button(
            root,
            text="Refresh ports",
            command=self.refresh_ports,
            bg="#F48220",
            fg="black",
            activeforeground="black",
        )
        self.refresh_button.pack(pady=5)

        self.start_button = tk.Button(
            root,
            text="Start",
            command=self.start_experiment,
            bg="#F48220",
            fg="black",
            activeforeground="black",
            disabledforeground="#666666",
            width=20,
            height=2,
        )
        self.start_button.pack(pady=5)

        self.text_area = tk.Text(root, height=20, width=80, bg="white", fg="black", borderwidth=2, relief="solid")
        self.text_area.pack(pady=5)

        self.stop_button = tk.Button(
            root,
            text="Stop",
            command=self.stop_experiment,
            state=tk.DISABLED,
            bg="#F48220",
            fg="black",
            activeforeground="black",
            disabledforeground="#666666",
            width=20,
            height=2,
        )
        self.stop_button.pack(pady=5)

        # Variables
        self.experiment_active = False
        self.mac_address = ""
        self.model = ""
        self.data_batch = []
        self.y_data = []
        self.r_data = []
        self.u_data = []
        self.time_data = []
        self.batch_size = 50
        self.ser = None
        self.iteration = 0
        self.refresh_ports()

    def log(self, message):
        """Update Tk widgets only from Tk's main thread."""
        self.root.after(0, self._append_log, message)

    def _append_log(self, message):
        self.text_area.insert(tk.END, message)
        self.text_area.see(tk.END)

    def refresh_ports(self):
        ports = [port.device for port in list_ports.comports()]
        self.com_menu["values"] = ports
        if ports:
            if self.com_menu.get() not in ports:
                self.com_menu.current(0)
        else:
            self.com_menu.set("")
            self.log("No serial ports detected. Connect the Arduino and refresh.\n")

    def start_experiment(self):
        port = self.com_menu.get()
        baudrate = 250000
        if not port:
            self.log("Select a serial port first.\n")
            return
        try:
            self.ser = serial.Serial(port, baudrate, timeout=0.1)
            self.log(f"Connected to {port} at {baudrate} baud\n")
            self.start_button.config(state=tk.DISABLED)
            self.stop_button.config(state=tk.NORMAL)
            self.refresh_button.config(state=tk.DISABLED)
            self.y_data.clear()
            self.r_data.clear()
            self.u_data.clear()
            self.time_data.clear()
            self.data_batch.clear()
            self.iteration = 0
            self.read_serial()
        except serial.SerialException as e:
            self.log(f"Error connecting to {port}: {e}\n")

    def stop_experiment(self):
        self.experiment_active = False
        if self.ser:
            self.ser.close()
        self.start_button.config(state=tk.NORMAL)
        self.stop_button.config(state=tk.DISABLED)
        self.refresh_button.config(state=tk.NORMAL)
        self.log("Experiment stopped\n")
        self.send_data()
        self.save_matlab_file()

    def save_matlab_file(self):
        mat_filename = "experiment_data.mat"
        scipy.io.savemat(mat_filename, {
            'y': self.y_data,
            'u': self.u_data,
            'r': self.r_data,
            'time': self.time_data
        })
        self.log(f"MATLAB file saved: {mat_filename}\n")

    def send_data(self):
        url = 'https://mrsolutions.sk/automationshield/other/arduino_insert.php'
        if self.data_batch:
            payload = {'mac': self.mac_address, 'model': self.model, 'data': self.data_batch}
            try:
                response = requests.post(url, json=payload, timeout=15)
                self.log(f"Batch sent: {len(self.data_batch)} rows, Response: {response.status_code}\n")
                if response.status_code == 200:
                    self.data_batch.clear()
            except Exception as e:
                self.log(f"Error sending batch: {e}\n")

    def read_serial(self):
        def process_serial():
            while self.ser and self.ser.is_open:
                try:
                    line = self.ser.readline().decode('utf-8').strip()
                    if line:
                        self.log(line + "\n")

                        if "MAC Address:" in line:
                            self.mac_address = line.split(": ")[1]
                        elif "Arduino Model:" in line:
                            self.model = line.split(": ")[1]
                        elif "status: START" in line:
                            self.experiment_active = True
                            self.y_data.clear()
                            self.r_data.clear()
                            self.u_data.clear()
                            self.time_data.clear()
                            self.iteration = 0
                        elif "status: STOP" in line:
                            self.experiment_active = False
                            self.send_data()
                            self.save_matlab_file()
                            self.text_area.insert(tk.END, "Experiment ended\n")
                        elif self.experiment_active:
                            parts = line.split(" ")
                            data = {"r": None, "y": None, "u": None}

                            # Support for y2 to y5
                            for suffix in range(2, 6):
                                data[f"y{suffix}"] = None

                            for i in range(0, len(parts), 2):
                                key = parts[i].strip(':')
                                if key in data and (i + 1) < len(parts):
                                    try:
                                        data[key] = float(parts[i + 1])
                                    except ValueError:
                                        data[key] = None

                            if data["y"] is not None and data["u"] is not None:
                                if data["r"] is None:
                                    data["r"] = None
                                self.y_data.append(data["y"])
                                self.u_data.append(data["u"])
                                self.r_data.append(data["r"])
                                self.time_data.append(self.iteration * 0.005)
                                self.iteration += 1
                                self.data_batch.append(data)

                            if len(self.data_batch) >= self.batch_size:
                                self.send_data()
                except Exception as e:
                    # Closing the port interrupts a blocking read on macOS.
                    # That is an expected shutdown condition, not a read error.
                    if self.ser and self.ser.is_open:
                        self.log(f"Error: {e}\n")

        threading.Thread(target=process_serial, daemon=True).start()

    def on_close(self):
        if self.ser and self.ser.is_open:
            self.ser.close()
        self.root.destroy()


if __name__ == "__main__":
    root = tk.Tk()
    app = SerialMonitorApp(root)
    root.mainloop()
