LibreSpeed (Custom Fork)

This is a custom fork of the LibreSpeed speed test project, enhanced with WebSocket-based packet loss measurement, tailored for browser-based testing in NAT environments like ISP-level CGNAT setups.

✨ Custom Features Added

✅ Packet Loss Detection via WebSockets

✅ Ping/Jitter Calculations

✅ Automatic Packet Loss Logging to MySQL

✅ Integrated into Existing LibreSpeed UI and Telemetry

💡 Use Case

This fork is designed for ISPs needing to measure:

Download & Upload Speeds

Latency (Ping)

Jitter

Packet Loss

...directly in customer browsers without deploying any software agents.

📁 Directory Overview

speedtest_worker.js → WebSocket logic for packet loss + test execution.

speedtest.js → Main frontend logic (updated to show loss).

results/telemetry.php → Accepts packet loss via POST.

results/telemetry_db.php → Writes loss to packet_loss column.

index.html* → UI variants for testing.

🚀 Setup Instructions

Clone this repo

Ensure Dependencies

PHP with MySQL

Apache/Nginx

ws Node.js WebSocket server for packet loss

Configure WebSocket Server

Database Setup
Ensure the speedtest_users table includes this column:

Access Web UI
Visit http://your-server-ip/index.html to run the test and store results.

📊 Output Example

⚡ Contributors

@clifforddsouza — Packet loss integration

✉ Feedback / Issues

Open an issue or fork it further.

❤️ This project extends LibreSpeed for real-world, ISP-grade network quality monitoring.

