const WebSocket = require('ws');

const wss = new WebSocket.Server({ port: 8080 });

console.log("✅ WebSocket Server started on ws://192.168.8.128:8080");

wss.on('connection', ws => {
    console.log("🔗 New WebSocket connection established");

    ws.on('message', message => {
        console.log(`📩 Received: ${message}`);
        ws.send(`🔄 Echo: ${message}`); // Echo message back for testing
    });

    ws.on('close', () => {
        console.log("❌ WebSocket connection closed");
    });
});

