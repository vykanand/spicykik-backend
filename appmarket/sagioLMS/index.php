<!DOCTYPE html>
<html>
<head>
    <title>Test Plugin</title>
</head>
<body>
    <tg onclick="history.back();" class="back-button">
        <img src="./assets/back-black.png" style="height: 40px; width: 40px;">
    </tg>

    <div class="container">
        <h2>Test Plugin</h2>
        <div class="input-group">
            <input type="text" id="messageInput" placeholder="Enter test value">
        </div>
        <button onclick="sendMessageToParent()">Send Value</button>
    </div>

     <script>
        function sendMessageToParent() {
            const message = document.getElementById('messageInput').value;
            console.log('Plugin sending message:', message);
            window.parent.postMessage(message, '*');
        }
    </script>
</body>
</html>
