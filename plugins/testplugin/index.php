<!DOCTYPE html>
<html>
<head>
    <title>Test Plugin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .container {
            margin-top: 20px;
        }
        .input-group {
            margin: 20px 0;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4338ca;
        }
        .debug-info {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 12px;
        }
        .debug-info h3 {
            margin-top: 0;
            font-family: Arial, sans-serif;
        }
        .debug-item {
            margin: 5px 0;
        }
        .form-data-section {
            background: #e8f4fd;
            border: 1px solid #2196f3;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .form-data-section h3 {
            margin-top: 0;
            color: #1976d2;
        }
        .form-data-item {
            margin: 8px 0;
            padding: 5px;
            background: white;
            border-radius: 3px;
            font-family: monospace;
            font-size: 12px;
        }
        .form-data-key {
            font-weight: bold;
            color: #1976d2;
        }
        .form-data-value {
            color: #333;
            word-break: break-all;
        }
        .back-button {
            cursor: pointer;
            display: inline-block;
        }
    </style>
</head>
<body>
    <tg onclick="history.back();" class="back-button">
        <img src="./assets/back-black.png" style="height: 40px; width: 40px;">
    </tg>

    <div class="container">
        <h2>Test Plugin - Field Debugging</h2>
        
        <div class="debug-info">
            <h3>📋 Debug Information</h3>
            <div class="debug-item"><strong>Source:</strong> <span id="debugSource">-</span></div>
            <div class="debug-item"><strong>Field (from URL):</strong> <span id="debugField">-</span></div>
            <div class="debug-item"><strong>Field has spaces:</strong> <span id="debugFieldSpaces">-</span></div>
            <div class="debug-item"><strong>Field has underscores:</strong> <span id="debugFieldUnderscores">-</span></div>
            <div class="debug-item"><strong>Full URL:</strong> <span id="debugUrl" style="word-break: break-all;">-</span></div>
        </div>
        
        <div class="form-data-section" id="formDataSection" style="display: none;">
            <h3>📊 Received Form Data</h3>
            <div class="debug-item"><strong>Data Source:</strong> <span id="formDataSource">-</span></div>
            <div class="debug-item"><strong>Target Field:</strong> <span id="formDataField">-</span></div>
            <div class="debug-item"><strong>Data Count:</strong> <span id="formDataCount">-</span></div>
            <div class="debug-item"><strong>Types Count:</strong> <span id="formTypesCount">-</span></div>
            <div id="formDataItems"></div>
        </div>
        
        <div class="input-group">
            <label for="messageInput"><strong>Enter test value to send:</strong></label>
            <input type="text" id="messageInput" placeholder="Type something and click Send...">
        </div>
        <button onclick="sendMessageToParent()">Send to Parent Window</button>
    </div>

     <script>
        console.log('=== TEST PLUGIN LOADED ===');
        console.log('Full URL:', window.location.href);
        
        const urlParams = new URLSearchParams(window.location.search);

        // Get 'source' and 'field' parameters
        const source = urlParams.get('source');
        const field = urlParams.get('field');
        
        console.log('URL Parameters:');
        console.log('  - source:', source);
        console.log('  - field (raw):', field);
        console.log('  - field type:', typeof field);
        console.log('  - field length:', field ? field.length : 0);
        console.log('  - field has spaces:', field ? field.includes(' ') : 'N/A');
        console.log('  - field has underscores:', field ? field.includes('_') : 'N/A');
        
        // Display debug info in UI
        document.getElementById('debugSource').textContent = source || 'N/A';
        document.getElementById('debugField').textContent = field || 'N/A';
        document.getElementById('debugFieldSpaces').textContent = field ? (field.includes(' ') ? 'YES ⚠️' : 'NO ✓') : 'N/A';
        document.getElementById('debugFieldUnderscores').textContent = field ? (field.includes('_') ? 'YES ✓' : 'NO ⚠️') : 'N/A';
        document.getElementById('debugUrl').textContent = window.location.href;
        
        let pluginDto = source == "addModal" ? localStorage.getItem("addDto") : localStorage.getItem("editDto");
        console.log('pluginDto:', pluginDto);
        
        // Listen for messages from parent window (including form data)
        window.addEventListener('message', function(event) {
            console.log('=== MESSAGE RECEIVED IN TEST PLUGIN ===');
            console.log('Message data:', event.data);
            console.log('Message origin:', event.origin);
            console.log('Message type:', typeof event.data);
            
            if (event.data && typeof event.data === 'object' && event.data.type === 'form-data') {
                console.log('=== FORM DATA RECEIVED ===');
                console.log('Form data details:');
                console.log('  - Type:', event.data.type);
                console.log('  - Source:', event.data.source);
                console.log('  - Field Name:', event.data.fieldName);
                console.log('  - Form Data:', event.data.formData);
                console.log('  - Field Types:', event.data.fieldTypes);
                console.log('  - Form Data Keys:', Object.keys(event.data.formData || {}));
                console.log('  - Field Types Keys:', Object.keys(event.data.fieldTypes || {}));
                console.log('  - Form Data Count:', Object.keys(event.data.formData || {}).length);
                console.log('  - Field Types Count:', Object.keys(event.data.fieldTypes || {}).length);
                
                // Display form data in UI
                displayFormData(event.data);
            }
        });
        
        function displayFormData(data) {
            const section = document.getElementById('formDataSection');
            const sourceSpan = document.getElementById('formDataSource');
            const fieldSpan = document.getElementById('formDataField');
            const countSpan = document.getElementById('formDataCount');
            const typesCountSpan = document.getElementById('formTypesCount');
            const itemsDiv = document.getElementById('formDataItems');
            
            // Show the section
            section.style.display = 'block';
            
            // Update header info
            sourceSpan.textContent = data.source || 'N/A';
            fieldSpan.textContent = data.fieldName || 'N/A';
            countSpan.textContent = data.formData ? Object.keys(data.formData).length : 0;
            typesCountSpan.textContent = data.fieldTypes ? Object.keys(data.fieldTypes).length : 0;
            
            // Clear previous items
            itemsDiv.innerHTML = '';
            
            // Display each form field with its type
            if (data.formData && typeof data.formData === 'object') {
                Object.keys(data.formData).forEach(key => {
                    const value = data.formData[key];
                    const fieldType = data.fieldTypes ? data.fieldTypes[key] : 'unknown';
                    
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'form-data-item';
                    
                    const valueStr = (value === null || value === undefined) ? 'null/undefined' : 
                                    (typeof value === 'object' ? JSON.stringify(value) : String(value));
                    
                    itemDiv.innerHTML = `
                        <span class="form-data-key">${key}</span> 
                        <span style="color: #666; font-size: 11px;">(${fieldType})</span>: 
                        <span class="form-data-value">${valueStr}</span>
                    `;
                    
                    itemsDiv.appendChild(itemDiv);
                });
            } else {
                itemsDiv.innerHTML = '<div class="form-data-item">No form data received</div>';
            }
        }
        
        function sendMessageToParent() {
            const message = document.getElementById('messageInput').value;
            if (!message) {
                alert('Please enter a value first!');
                return;
            }
            console.log('=== SENDING MESSAGE TO PARENT ===');
            console.log('Message value:', message);
            console.log('Target field (from URL):', field);
            console.log('Source modal:', source);
            console.log('Sending to parent window...');
            window.parent.postMessage(message, '*');
            console.log('✓ Message sent!');
        }
    </script>
</body>
</html>
