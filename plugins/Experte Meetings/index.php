<!DOCTYPE html>
<html>
<head>
    <title>Meeting Generator</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 600px;
            text-align: center;
            margin-top: 80px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        h1 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 2.2em;
        }

        .meeting-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .generated-link {
            background: #f7fafc;
            padding: 15px;
            border-radius: 10px;
            font-size: 1.1em;
            color: #4a5568;
            word-break: break-all;
            margin: 20px 0;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            font-weight: 600;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .generate-btn {
            background: #4c1d95;
            color: white;
        }

        .share-btn {
            background: #2563eb;
            color: white;
        }

        .copy-btn {
            background: #059669;
            color: white;
        }

        .success-message {
            color: #059669;
            font-size: 0.9em;
            margin-top: 10px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .pulse {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <tg onclick="history.back();" style="position: fixed; top: 20px; left: 20px; cursor: pointer; z-index: 9999;">
    <img src="./assets/back-black.png" style="height: 40px; width: 40px;">
    </tg>
    <div class="container">
        <h1>Meeting Generator</h1>
        <div class="meeting-card">
            <div id="meetingLink" class="generated-link">
                <a href="#" id="meetingAnchor" target="_blank" style="text-decoration: none; color: #4a5568;">Your meeting link will appear here</a>
            </div>
            <div class="button-group">
                <button onclick="generateMeeting()" class="btn generate-btn">Generate Meeting</button>
                <button onclick="openMeeting()" class="btn share-btn">Open Link</button>
                <button onclick="copyLink()" class="btn copy-btn">Copy</button>
            </div>
            <div id="successMessage" class="success-message">Link copied successfully!</div>
        </div>
    </div>

    <script>
        function generateMeeting() {
            const randomId = Math.random().toString(36).substring(7);
            const baseUrl = 'https://www.experte.com/online-meeting';
            const meetingUrl = `${baseUrl}?join=${randomId}`;
            const anchor = document.getElementById('meetingAnchor');
            anchor.href = meetingUrl;
            anchor.textContent = meetingUrl;
            
            // Check if window is inside app.js sidebar
            if (window.parent !== window) {
                window.parent.postMessage(meetingUrl, '*');
            } else {
                document.getElementById('meetingLink').classList.add('pulse');
                setTimeout(() => {
                    document.getElementById('meetingLink').classList.remove('pulse');
                }, 1000);
            }
        }


        function openMeeting() {
            const meetingUrl = document.getElementById('meetingLink').textContent.trim(); // Trim whitespace
            console.log(meetingUrl);
            if (!meetingUrl.startsWith('https')) {
                // Open the meeting link in a new tab
                alert('Please click Generate Meeting first to generate a meeting link.');
            }else{window.open(meetingUrl, '_blank');}
        }


        function copyLink() {
            const meetingUrl = document.getElementById('meetingLink').textContent;
            if (meetingUrl !== 'Your meeting link will appear here') {
                navigator.clipboard.writeText(meetingUrl);
                const successMessage = document.getElementById('successMessage');
                successMessage.style.opacity = '1';
                setTimeout(() => {
                    successMessage.style.opacity = '0';
                }, 2000);
            }
        }
    </script>
</body>
</html>
