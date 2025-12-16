<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Calculator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .calculator-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-top: 16px;
        }
        .calc-btn {
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .calc-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .number-btn {
            background: #f3f4f6;
            color: #1f2937;
        }
        .number-btn:hover {
            background: #e5e7eb;
        }
        .operator-btn {
            background: #3b82f6;
            color: white;
        }
        .operator-btn:hover {
            background: #2563eb;
        }
        .equals-btn {
            background: #10b981;
            color: white;
            grid-column: span 2;
        }
        .equals-btn:hover {
            background: #059669;
        }
        .clear-btn {
            background: #ef4444;
            color: white;
        }
        .clear-btn:hover {
            background: #dc2626;
        }
        .tab-active {
            background: #3b82f6;
            color: white;
        }
        .tab-inactive {
            background: #f3f4f6;
            color: #6b7280;
        }
        .result-display {
            background: #1f2937;
            color: white;
            padding: 20px;
            border-radius: 8px;
            font-size: 24px;
            font-family: 'Courier New', monospace;
            text-align: right;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            word-break: break-all;
        }
        .unit-select {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .conversion-result {
            background: #ecfdf5;
            border: 1px solid #10b981;
            border-radius: 6px;
            padding: 12px;
            margin-top: 16px;
        }
        .percentage-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 16px;
        }
        .percentage-input {
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
        }
        .percentage-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 4px;
            display: block;
        }
        .back-button {
            cursor: pointer;
            display: inline-block;
            transition: transform 0.2s;
        }
        .back-button:hover {
            transform: scale(1.1);
        }
        .form-data-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin: 16px 0;
            font-size: 12px;
        }
        .form-data-item {
            margin: 4px 0;
            padding: 4px;
            background: white;
            border-radius: 4px;
        }
        .form-data-key {
            font-weight: bold;
            color: #3b82f6;
        }
        .form-data-value {
            color: #374151;
            word-break: break-all;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">

    <!-- Back Button -->
    <div class="fixed top-4 left-4 z-50">
        <button onclick="history.back()" class="back-button bg-white p-2 rounded-full shadow-lg hover:shadow-xl">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-700">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <div class="min-h-screen flex flex-col items-center justify-start pt-8 px-4">
        <div class="flex items-center justify-center mb-8">
            <img src="./Calculator.png" alt="Calculator Logo" class="h-12 w-auto mr-3" />
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Advanced Calculator</h1>
                <p class="text-gray-600 text-sm mt-1">Numbers, Percentages & Unit Conversions</p>
            </div>
        </div>

        <div class="max-w-2xl w-full bg-white rounded-xl shadow-lg p-6">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-6">
                <button id="tab-numbers" class="tab-active px-6 py-3 rounded-t-lg font-medium" onclick="switchTab('numbers')">
                    <i class="fas fa-calculator mr-2"></i>Numbers
                </button>
                <button id="tab-percentage" class="tab-inactive px-6 py-3 rounded-t-lg font-medium" onclick="switchTab('percentage')">
                    <i class="fas fa-percentage mr-2"></i>Percentage
                </button>
                <button id="tab-units" class="tab-inactive px-6 py-3 rounded-t-lg font-medium" onclick="switchTab('units')">
                    <i class="fas fa-exchange-alt mr-2"></i>Units
                </button>
            </div>

            <!-- Numbers Calculator -->
            <div id="numbers-calc" class="tab-content">
                <div class="result-display" id="numbers-display">0</div>
                <div class="calculator-grid">
                    <button class="calc-btn clear-btn" onclick="clearCalculator()">C</button>
                    <button class="calc-btn operator-btn" onclick="appendToDisplay('(')">(</button>
                    <button class="calc-btn operator-btn" onclick="appendToDisplay(')')">)</button>
                    <button class="calc-btn operator-btn" onclick="appendToDisplay('/')">÷</button>

                    <button class="calc-btn number-btn" onclick="appendToDisplay('7')">7</button>
                    <button class="calc-btn number-btn" onclick="appendToDisplay('8')">8</button>
                    <button class="calc-btn number-btn" onclick="appendToDisplay('9')">9</button>
                    <button class="calc-btn operator-btn" onclick="appendToDisplay('*')">×</button>

                    <button class="calc-btn number-btn" onclick="appendToDisplay('4')">4</button>
                    <button class="calc-btn number-btn" onclick="appendToDisplay('5')">5</button>
                    <button class="calc-btn number-btn" onclick="appendToDisplay('6')">6</button>
                    <button class="calc-btn operator-btn" onclick="appendToDisplay('-')">-</button>

                    <button class="calc-btn number-btn" onclick="appendToDisplay('1')">1</button>
                    <button class="calc-btn number-btn" onclick="appendToDisplay('2')">2</button>
                    <button class="calc-btn number-btn" onclick="appendToDisplay('3')">3</button>
                    <button class="calc-btn operator-btn" onclick="appendToDisplay('+')">+</button>

                    <button class="calc-btn number-btn" onclick="appendToDisplay('0')">0</button>
                    <button class="calc-btn number-btn" onclick="appendToDisplay('.')">.</button>
                    <button class="calc-btn equals-btn" onclick="calculateResult()">=</button>
                </div>
                <div class="mt-4">
                    <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300" onclick="safeSendToParent('numbers')">
                        Send Result to Form
                    </button>
                </div>
            </div>

            <!-- Percentage Calculator -->
            <div id="percentage-calc" class="tab-content hidden">
                <div class="percentage-grid">
                    <div>
                        <label class="percentage-label">Value</label>
                        <input type="number" id="percentage-value" class="percentage-input" placeholder="Enter value">
                    </div>
                    <div>
                        <label class="percentage-label">Percentage (%)</label>
                        <input type="number" id="percentage-rate" class="percentage-input" placeholder="Enter %">
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <button class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-300" onclick="calculatePercentage('of')">
                        Calculate X% of Value
                    </button>
                    <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300" onclick="calculatePercentage('is')">
                        What % is Value of Total
                    </button>
                    <button class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition duration-300" onclick="calculatePercentage('increase')">
                        Increase by %
                    </button>
                    <button class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-300" onclick="calculatePercentage('decrease')">
                        Decrease by %
                    </button>
                </div>

                <div id="percentage-result" class="conversion-result hidden">
                    <div>
                        <div class="font-semibold text-green-800">Result: <span id="percentage-result-value"></span></div>
                        <div class="text-sm text-green-700 mt-1" id="percentage-result-explanation"></div>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300" onclick="safeSendToParent('percentage')">
                        Send Result to Form
                    </button>
                </div>
            </div>

            <!-- Unit Converter -->
            <div id="units-calc" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="percentage-label">From Unit</label>
                        <select id="from-unit" class="unit-select w-full">
                            <optgroup label="Length">
                                <option value="mm">Millimeter (mm)</option>
                                <option value="cm">Centimeter (cm)</option>
                                <option value="m">Meter (m)</option>
                                <option value="km">Kilometer (km)</option>
                                <option value="in">Inch (in)</option>
                                <option value="ft">Foot (ft)</option>
                                <option value="yd">Yard (yd)</option>
                                <option value="mi">Mile (mi)</option>
                            </optgroup>
                            <optgroup label="Weight">
                                <option value="mg">Milligram (mg)</option>
                                <option value="g">Gram (g)</option>
                                <option value="kg">Kilogram (kg)</option>
                                <option value="oz">Ounce (oz)</option>
                                <option value="lb">Pound (lb)</option>
                                <option value="ton">Ton (ton)</option>
                            </optgroup>
                            <optgroup label="Temperature">
                                <option value="c">Celsius (°C)</option>
                                <option value="f">Fahrenheit (°F)</option>
                                <option value="k">Kelvin (K)</option>
                            </optgroup>
                            <optgroup label="Volume">
                                <option value="ml">Milliliter (ml)</option>
                                <option value="l">Liter (l)</option>
                                <option value="gal">Gallon (gal)</option>
                                <option value="qt">Quart (qt)</option>
                                <option value="pt">Pint (pt)</option>
                                <option value="cup">Cup (cup)</option>
                                <option value="fl-oz">Fluid Ounce (fl oz)</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="percentage-label">Value</label>
                        <input type="number" id="unit-value" class="percentage-input w-full" placeholder="Enter value">
                    </div>
                    <div>
                        <label class="percentage-label">To Unit</label>
                        <select id="to-unit" class="unit-select w-full">
                            <optgroup label="Length">
                                <option value="mm">Millimeter (mm)</option>
                                <option value="cm">Centimeter (cm)</option>
                                <option value="m">Meter (m)</option>
                                <option value="km">Kilometer (km)</option>
                                <option value="in">Inch (in)</option>
                                <option value="ft">Foot (ft)</option>
                                <option value="yd">Yard (yd)</option>
                                <option value="mi">Mile (mi)</option>
                            </optgroup>
                            <optgroup label="Weight">
                                <option value="mg">Milligram (mg)</option>
                                <option value="g">Gram (g)</option>
                                <option value="kg">Kilogram (kg)</option>
                                <option value="oz">Ounce (oz)</option>
                                <option value="lb">Pound (lb)</option>
                                <option value="ton">Ton (ton)</option>
                            </optgroup>
                            <optgroup label="Temperature">
                                <option value="c">Celsius (°C)</option>
                                <option value="f">Fahrenheit (°F)</option>
                                <option value="k">Kelvin (K)</option>
                            </optgroup>
                            <optgroup label="Volume">
                                <option value="ml">Milliliter (ml)</option>
                                <option value="l">Liter (l)</option>
                                <option value="gal">Gallon (gal)</option>
                                <option value="qt">Quart (qt)</option>
                                <option value="pt">Pint (pt)</option>
                                <option value="cup">Cup (cup)</option>
                                <option value="fl-oz">Fluid Ounce (fl oz)</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <button class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-300 mb-4" onclick="convertUnits()">
                    Convert Units
                </button>

                <div id="conversion-result" class="conversion-result hidden">
                    <div>
                        <div class="font-semibold text-green-800">Result: <span id="conversion-result-value"></span></div>
                        <div class="text-sm text-green-700 mt-1" id="conversion-result-explanation"></div>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300" onclick="safeSendToParent('units')">
                        Send Result to Form
                    </button>
                </div>
            </div>

            <!-- Form Data Display -->
            <div id="formDataSection" class="form-data-section hidden">
                <h4 class="font-semibold text-gray-800 mb-2">📊 Received Form Context</h4>
                <div id="formDataItems"></div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentTab = 'numbers';
        let receivedFormData = null;
        let receivedFieldTypes = null;

        // URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const source = urlParams.get('source');
        const field = urlParams.get('field');

        console.log('=== CALCULATOR PLUGIN LOADED ===');
        console.log('Source:', source, 'Field:', field);

        // Tab switching
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tabs
            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                tab.classList.remove('tab-active');
                tab.classList.add('tab-inactive');
            });

            // Show selected tab
            document.getElementById(tabName + '-calc').classList.remove('hidden');
            document.getElementById('tab-' + tabName).classList.remove('tab-inactive');
            document.getElementById('tab-' + tabName).classList.add('tab-active');

            currentTab = tabName;
        }

        // Numbers Calculator Functions
        function appendToDisplay(value) {
            const display = document.getElementById('numbers-display');
            if (display.textContent === '0' && !isNaN(value)) {
                display.textContent = value;
            } else {
                display.textContent += value;
            }
        }

        function clearCalculator() {
            document.getElementById('numbers-display').textContent = '0';
        }

        function calculateResult() {
            const display = document.getElementById('numbers-display');
            try {
                let expression = display.textContent;
                // Replace × and ÷ with * and /
                expression = expression.replace(/×/g, '*').replace(/÷/g, '/');
                const result = eval(expression);
                display.textContent = result;
            } catch (error) {
                display.textContent = 'Error';
                setTimeout(() => display.textContent = '0', 1500);
            }
        }

        // Percentage Calculator Functions
        function calculatePercentage(type) {
            const value = parseFloat(document.getElementById('percentage-value').value);
            const rate = parseFloat(document.getElementById('percentage-rate').value);

            if (isNaN(value) || isNaN(rate)) {
                alert('Please enter valid numbers');
                return;
            }

            let result, explanation;

            switch (type) {
                case 'of':
                    result = (value * rate) / 100;
                    explanation = `${rate}% of ${value} = ${result}`;
                    break;
                case 'is':
                    result = (value / rate) * 100;
                    explanation = `${value} is ${result.toFixed(2)}% of ${rate}`;
                    break;
                case 'increase':
                    result = value + (value * rate / 100);
                    explanation = `${value} increased by ${rate}% = ${result}`;
                    break;
                case 'decrease':
                    result = value - (value * rate / 100);
                    explanation = `${value} decreased by ${rate}% = ${result}`;
                    break;
            }

            document.getElementById('percentage-result-value').textContent = result;
            document.getElementById('percentage-result-explanation').textContent = explanation;
            document.getElementById('percentage-result').classList.remove('hidden');
        }

        // Unit Conversion Functions
        function convertUnits() {
            const value = parseFloat(document.getElementById('unit-value').value);
            const fromUnit = document.getElementById('from-unit').value;
            const toUnit = document.getElementById('to-unit').value;

            if (isNaN(value)) {
                alert('Please enter a valid number');
                return;
            }

            if (fromUnit === toUnit) {
                document.getElementById('conversion-result-value').textContent = value;
                document.getElementById('conversion-result-explanation').textContent = 'Same units - no conversion needed';
                document.getElementById('conversion-result').classList.remove('hidden');
                return;
            }

            const result = convertValue(value, fromUnit, toUnit);
            const fromLabel = document.querySelector(`option[value="${fromUnit}"]`).textContent;
            const toLabel = document.querySelector(`option[value="${toUnit}"]`).textContent;

            document.getElementById('conversion-result-value').textContent = result;
            document.getElementById('conversion-result-explanation').textContent = `${value} ${fromLabel} = ${result} ${toLabel}`;
            document.getElementById('conversion-result').classList.remove('hidden');
        }

        function convertValue(value, from, to) {
            // Convert to base unit first, then to target unit
            const baseValue = toBaseUnit(value, from);
            return fromBaseUnit(baseValue, to);
        }

        function toBaseUnit(value, unit) {
            const conversions = {
                // Length (base: meters)
                'mm': value / 1000,
                'cm': value / 100,
                'm': value,
                'km': value * 1000,
                'in': value * 0.0254,
                'ft': value * 0.3048,
                'yd': value * 0.9144,
                'mi': value * 1609.344,

                // Weight (base: grams)
                'mg': value / 1000,
                'g': value,
                'kg': value * 1000,
                'oz': value * 28.3495,
                'lb': value * 453.592,
                'ton': value * 907184.74,

                // Temperature (base: celsius)
                'c': value,
                'f': (value - 32) * 5/9,
                'k': value - 273.15,

                // Volume (base: liters)
                'ml': value / 1000,
                'l': value,
                'gal': value * 3.78541,
                'qt': value * 0.946353,
                'pt': value * 0.473176,
                'cup': value * 0.236588,
                'fl-oz': value * 0.0295735
            };
            return conversions[unit] || value;
        }

        function fromBaseUnit(value, unit) {
            const conversions = {
                // Length (from meters)
                'mm': value * 1000,
                'cm': value * 100,
                'm': value,
                'km': value / 1000,
                'in': value / 0.0254,
                'ft': value / 0.3048,
                'yd': value / 0.9144,
                'mi': value / 1609.344,

                // Weight (from grams)
                'mg': value * 1000,
                'g': value,
                'kg': value / 1000,
                'oz': value / 28.3495,
                'lb': value / 453.592,
                'ton': value / 907184.74,

                // Temperature (from celsius)
                'c': value,
                'f': value * 9/5 + 32,
                'k': value + 273.15,

                // Volume (from liters)
                'ml': value * 1000,
                'l': value,
                'gal': value / 3.78541,
                'qt': value / 0.946353,
                'pt': value / 0.473176,
                'cup': value / 0.236588,
                'fl-oz': value / 0.0295735
            };
            return conversions[unit] || value;
        }

        // Safe wrapper for sendToParent to prevent undefined errors
        function safeSendToParent(calculatorType) {
            if (typeof sendToParent === 'function') {
                sendToParent(calculatorType);
            } else {
                console.error('sendToParent function not yet loaded');
                alert('Plugin is still loading. Please wait a moment and try again.');
            }
        }

        // Send result to parent
        function sendToParent(calculatorType) {
            let result;

            switch (calculatorType) {
                case 'numbers':
                    result = document.getElementById('numbers-display').textContent;
                    if (result === '0' || result === 'Error') {
                        alert('Please calculate a valid result first');
                        return;
                    }
                    break;

                case 'percentage':
                    const percentageResult = document.getElementById('percentage-result-value');
                    if (!percentageResult.textContent) {
                        alert('Please calculate a percentage first');
                        return;
                    }
                    result = percentageResult.textContent;
                    break;

                case 'units':
                    const conversionResult = document.getElementById('conversion-result-value');
                    if (!conversionResult.textContent) {
                        alert('Please perform a conversion first');
                        return;
                    }
                    result = conversionResult.textContent;
                    break;
            }

            console.log('=== SENDING RESULT TO PARENT ===');
            console.log('Calculator Type:', calculatorType);
            console.log('Result:', result);
            console.log('Target Field:', field);
            console.log('Source Modal:', source);

            // Send to parent window (same as Blob Uploader)
            if (window.parent !== window) {
                window.parent.postMessage(result, '*');
                console.log('✓ Result sent to parent!');
            } else {
                console.error('Cannot send to parent: window.parent is same as window');
            }
        }

        // Listen for messages from parent window
        window.addEventListener('message', function(event) {
            console.log('=== MESSAGE RECEIVED IN CALCULATOR ===');
            console.log('Message data:', event.data);
            console.log('Message origin:', event.origin);

            if (event.data && typeof event.data === 'object' && event.data.type === 'form-data') {
                console.log('=== FORM DATA RECEIVED ===');
                console.log('Form data details:');
                console.log('  - Type:', event.data.type);
                console.log('  - Source:', event.data.source);
                console.log('  - Field Name:', event.data.fieldName);
                console.log('  - Form Data:', event.data.formData);
                console.log('  - Field Types:', event.data.fieldTypes);

                receivedFormData = event.data.formData;
                receivedFieldTypes = event.data.fieldTypes;

                displayFormData(event.data);
            }
        });

        function displayFormData(data) {
            const section = document.getElementById('formDataSection');
            const itemsDiv = document.getElementById('formDataItems');

            // Show the section
            section.classList.remove('hidden');

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

        // Initialize with numbers tab
        switchTab('numbers');
    </script>

</body>
</html>
